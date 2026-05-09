<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\Setting;
use App\Mail\OrderStatusUpdate;
use App\Mail\ReturnStatusCustomerMail;
use App\Services\ShiprocketService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::query();

        // Apply Filters
        if ($request->filled('status') && $request->status != 'all') {
            if ($request->status == 'paid') {
                $query->where('payment_status', '=', 'paid');
            } elseif ($request->status == 'unpaid') {
                $query->where('payment_status', '=', 'pending');
            } elseif ($request->status == 'shipped') {
                $query->where('order_status', '=', 'shipped');
            } elseif ($request->status == 'out for delivery') {
                $query->where('order_status', '=', 'out for delivery');
            } else {
                $query->where('order_status', '=', $request->status);
            }
        }

        if ($request->filled('search')) {
            $term = trim($request->search);
            $query->where(function($q) use ($term) {
                $q->where('order_number', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_email', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $orders = $query->latest('created_at')->paginate($perPage)->withQueryString();

        $counts = [
            'all' => Order::count(),
            'order placed' => Order::where('order_status', 'order placed')->count(),
            'new' => Order::where('order_status', 'new')->count(),
            'processing' => Order::where('order_status', 'processing')->count(),
            'ready to ship' => Order::where('order_status', 'ready to ship')->count(),
            'shipped' => Order::where('order_status', 'shipped')->count(),
            'out for delivery' => Order::where('order_status', 'out for delivery')->count(),
            'delivered' => Order::where('order_status', 'delivered')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];
        
        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function create()
    {
        return view('admin.orders.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'sub_total' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'shipping' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'delivery_address' => 'required|string',
            'payment_method' => 'required|string',
            'payment_status' => 'required|string',
            'order_status' => 'required|string',
        ]);

        $data = $request->all();
        
        // Generate Order Number if not exists
        if (!$request->filled('order_number')) {
            $data['order_number'] = 'ORD-' . strtoupper(Str::random(8)) . '-' . time();
        }

        $order = Order::create($data);
        $order->syncStatus($order->order_status ?? 'order placed');

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order, ShiprocketService $shiprocket)
    {
        $order->load(['items.product', 'items.variant', 'user', 'coupon']);

        // Auto-sync with Shiprocket on page load if order is pushed but not delivered/cancelled
        if ($order->shiprocket_order_id && !in_array($order->order_status, ['delivered', 'cancelled'])) {
            try {
                $this->syncShiprocketStatusInternal($order, $shiprocket);
            } catch (\Exception $e) {
                Log::error("Auto-sync failed for Order #{$order->order_number}: " . $e->getMessage());
            }
        }

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order, ShiprocketService $shiprocket)
    {
        $request->validate([
            'order_status' => 'required|in:pending,order placed,new,processing,ready to ship,shipped,out for delivery,delivered,cancelled,returned,refunded',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partial',
            'tracking_number' => 'nullable|string|max:255',
            'courier_name' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;
        $oldTracking = $order->tracking_number;
        $newStatus = $request->order_status;

        $order->syncStatus(
            $newStatus,
            null, // Not a shiprocket-triggered sync
            $request->tracking_number
        );

        // Update other fields manually
        $order->update($request->only([
            'payment_status',
            'courier_name',
            'admin_notes'
        ]));

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Order updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }

    public function downloadInvoice(Order $order)
    {
        Log::info('Downloading Official Invoice for Order: ' . $order->order_number);
        $order->load(['items.product', 'items.variant']);
        $filename = 'invoice-' . ($order->order_number ?? $order->id) . '.pdf';

        $pdf = Pdf::loadView('admin.orders.invoice', compact('order'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

        return $pdf->download($filename);
    }

    public function pushToShiprocket(Request $request, Order $order, ShiprocketService $shiprocket)
    {
        $isCancelled = in_array(strtoupper($order->shiprocket_status ?? ''), ['CANCELED', 'CANCELLED']);
        if ($order->shiprocket_order_id && !$isCancelled) {
            return back()->with('error', 'Order already pushed to Shiprocket.');
        }
        $dimensions = [
            'length'  => $request->length ?? $order->package_length,
            'breadth' => $request->breadth ?? $order->package_breadth,
            'height'  => $request->height ?? $order->package_height,
            'weight'  => $request->weight ?? $order->package_weight,
        ];

        $result = $shiprocket->createOrder($order, $dimensions);

        if ($result['status']) {
            $order->syncStatus('new');
            return back()->with('success', 'Order pushed to Shiprocket successfully and status updated to New.');
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Unknown Error'));
    }

    /**
     * All-in-one: Push to Shiprocket + Auto-assign AWB + Schedule Pickup with chosen date.
     * Admin selects pickup date from modal → single form submit does everything.
     */
    public function pushWithPickup(Request $request, Order $order, ShiprocketService $shiprocket)
    {
        $request->validate([
            'pickup_date' => 'required|date|after_or_equal:today',
            'length'      => 'required|numeric|min:0.5',
            'breadth'     => 'required|numeric|min:0.5',
            'height'      => 'required|numeric|min:0.5',
            'weight'      => 'required|numeric|min:0.01',
        ]);

        $isCancelled = in_array(strtoupper($order->shiprocket_status ?? ''), ['CANCELED', 'CANCELLED']);
        if ($order->shiprocket_order_id && !$isCancelled) {
            return back()->with('error', 'Order already pushed to Shiprocket.');
        }

        // ── STEP 1: Create order in Shiprocket ─────────────────────────────
        $dimensions = $request->only(['length', 'breadth', 'height', 'weight']);
        $createResult = $shiprocket->createOrder($order, $dimensions);
        if (!$createResult['status']) {
            return back()->with('error', 'Shiprocket Push Failed: ' . ($createResult['message'] ?? 'Unknown Error'));
        }

        // Reload order to get fresh shiprocket_shipment_id
        $order->refresh();

        // ── STEP 2: Auto-assign AWB ─────────────────────────────────────────
        $awbResult = $shiprocket->assignCourierAndAWB($order);
        if (!$awbResult['status']) {
            // Pushed successfully but AWB failed — still mark as new
            $order->syncStatus('new');
            return back()->with('warning', 'Order pushed to Shiprocket ✓ but AWB assignment failed: ' . ($awbResult['message'] ?? 'Try assigning AWB manually.'));
        }

        // Reload again after AWB assigned
        $order->refresh();

        // ── STEP 3: Schedule Pickup with chosen date ────────────────────────
        $pickupDate = $request->pickup_date; // YYYY-MM-DD
        $pickupResult = $shiprocket->requestPickup($order->shiprocket_shipment_id, $pickupDate);

        // Save pickup scheduled date in DB
        $order->update(['pickup_scheduled_at' => $pickupDate]);

        // Re-calculate EDD based on the newly scheduled pickup date
        // Since surface takes time, but user requested "one or two days", we set it to +2 days from pickup
        $pickupRef = \Carbon\Carbon::parse($pickupDate);
        $currentEdd = $order->edd ? \Carbon\Carbon::parse($order->edd) : null;

        if (!$currentEdd || $currentEdd->lte($pickupRef)) {
            $order->update(['edd' => $pickupRef->copy()->addDays(2)->format('Y-m-d')]);
        }

        // Update order status + send emails
        $order->syncStatus('new');
        
        if (!$pickupResult['status']) {
            $msg = $pickupResult['message'] ?? 'Unknown Error';
            
            // If it's already in queue, treat as success for the user UX
            if (str_contains(strtolower($msg), 'already in pickup queue') || str_contains(strtolower($msg), 'already scheduled')) {
                return back()->with('success',
                    "✅ Order pushed to Shiprocket! AWB: {$awbResult['awb']} via {$awbResult['courier']}. " .
                    "Note: Pickup was already scheduled."
                );
            }

            return back()->with('warning',
                "✓ Pushed & AWB ({$awbResult['awb']}) assigned via {$awbResult['courier']}. " .
                "⚠ Pickup scheduling failed: " . $msg
            );
        }

        return back()->with('success',
            "✅ Order pushed to Shiprocket! AWB: {$awbResult['awb']} via {$awbResult['courier']}. " .
            "Pickup scheduled for {$pickupDate}."
        );
    }

    public function assignShiprocketAWB(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        $result = $shiprocket->assignCourierAndAWB($order);

        if ($result['status']) {
            return back()->with('success', 'AWB assigned successfully: ' . $result['awb'] . ' (via ' . ($result['courier'] ?? 'Partner') . ')');
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Unknown Error'));
    }

    public function generateShiprocketLabel(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        $result = $shiprocket->generateLabel($order->shiprocket_shipment_id);

        if ($result['status'] && isset($result['data']['label_url'])) {
            return redirect($result['data']['label_url']);
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Failed to generate label link.'));
    }

    public function generateShiprocketManifest(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        if (!$order->shiprocket_awb) {
            return back()->with('error', 'AWB must be assigned before generating manifest.');
        }

        $result = $shiprocket->generateManifest($order->shiprocket_shipment_id);

        if ($result['status']) {
            $manifestUrl = $result['manifest_url'] ?? null;
            if ($manifestUrl) {
                $order->update(['shiprocket_manifest_url' => $manifestUrl]);
                return redirect($manifestUrl);
            }
            // Manifest generated but URL not returned — save what we have
            return back()->with('success', 'Manifest generated successfully. Check Shiprocket dashboard to download.');
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Failed to generate manifest.'));
    }

    public function generateShiprocketInvoice(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_order_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        $result = $shiprocket->generateInvoice($order->shiprocket_order_id);

        if ($result['status']) {
            $invoiceUrl = $result['data']['invoice_url'] ?? null;
            if ($invoiceUrl) {
                $order->update(['shiprocket_invoice_url' => $invoiceUrl]);
                return redirect($invoiceUrl);
            }
            return back()->with('success', 'Invoice generated successfully.');
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Failed to generate invoice.'));
    }

    public function requestShiprocketPickup(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        $result = $shiprocket->requestPickup($order->shiprocket_shipment_id);

        if ($result['status']) {
            return back()->with('success', 'Pickup requested successfully.');
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Unknown Error'));
    }

    public function createShiprocketReturn(Order $order, ShiprocketService $shiprocket)
    {
        if ($order->order_status !== 'delivered') {
            return back()->with('error', 'Only delivered orders can be returned.');
        }

        if ($order->return_status && in_array($order->return_status, ['approved', 'picked', 'received', 'refunded'])) {
            return back()->with('error', 'Return already processed for this order.');
        }

        $result = $shiprocket->createReturnOrder($order);

        if ($result['status']) {
            $data = $result['data'] ?? [];
            $order->update([
                'return_status'                 => 'approved',
                'shiprocket_return_order_id'    => $data['order_id'] ?? null,
                'shiprocket_return_shipment_id' => $data['shipment_id'] ?? null,
                'reverse_awb'                   => $data['awb_code'] ?? null,
            ]);
            // Use syncStatus to properly set order_status = 'returned' + trigger email
            $order->syncStatus('returned');
            return back()->with('success', 'Return order created in Shiprocket. Shipment ID: ' . ($data['shipment_id'] ?? 'N/A'));
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Unknown Error'));
    }

    public function updateReturnStatus(Request $request, Order $order, ShiprocketService $shiprocket)
    {
        $request->validate([
            'return_status' => 'required|in:requested,approved,rejected,picked,received,refunded',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $order->return_status;
        $newStatus = $request->return_status;

        $order->update([
            'return_status'    => $newStatus,
            'return_admin_notes' => $request->admin_notes,
        ]);

        // If newly approved, trigger Shiprocket return order creation
        if ($newStatus === 'approved' && $oldStatus === 'requested') {
            $result = $shiprocket->createReturnOrder($order);

            if ($result['status'] && isset($result['data'])) {
                $order->update([
                    'shiprocket_return_order_id' => $result['data']['order_id'] ?? null,
                    'shiprocket_return_shipment_id' => $result['data']['shipment_id'] ?? null,
                    // If shiprocket returns AWB immediately in return creation
                    'reverse_awb' => $result['data']['awb_code'] ?? null,
                ]);
                
                return back()->with('success', 'Return request approved and sent to Shiprocket. Shipment ID: ' . ($result['data']['shipment_id'] ?? 'N/A'));
            } else {
                Log::error('Shiprocket Return Creation Failed: ' . ($result['message'] ?? 'Unknown error'));
                return back()->with('warning', 'Status updated, but failed to create return in Shiprocket: ' . ($result['message'] ?? 'Check logs'));
            }
        }

        // If refunded, update payment status too and trigger email
        if ($newStatus === 'refunded') {
            $order->update(['payment_status' => 'refunded']);
        }

        // If return is received, mark order as returned
        if ($newStatus === 'received') {
            $order->syncStatus('returned');
        }

        // Send Email to Customer
        try {
            Mail::to($order->customer_email)->send(new \App\Mail\ReturnStatusCustomerMail($order));
        } catch (\Exception $e) {
            Log::error('Return Status Update Email Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Return status updated to ' . strtoupper($newStatus));
    }

    public function syncShiprocketStatus(Order $order, ShiprocketService $shiprocket)
    {
        $result = $this->syncShiprocketStatusInternal($order, $shiprocket);

        if ($result['synced']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('info', $result['message']);
    }

    private function syncShiprocketStatusInternal(Order $order, ShiprocketService $shiprocket): array
    {
        if (!$order->shiprocket_shipment_id && !$order->shiprocket_order_id) {
            return ['synced' => false, 'message' => 'No Shiprocket IDs found for this order.'];
        }

        $synced = false;
        $statusMsg = "Tracking data not available yet.";

        // Attempt 1: Track via shipment ID
        if ($order->shiprocket_shipment_id) {
            $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);
            Log::info("Manual Sync Tracking Data for Order #{$order->order_number}:", $trackingData ?? []);

            $trackObj = $trackingData['tracking_data'] ?? null;
            if ($trackObj && ($trackObj['track_status'] ?? 0) == 1) {
                // Try to find the current status in various possible fields
                $shipmentTrack = $trackObj['shipment_track'][0] ?? null;
                $rawStatus = $shipmentTrack['current_status'] ?? $trackObj['shipment_status'] ?? null;
                $awb = $shipmentTrack['awb_code'] ?? $trackObj['awb'] ?? $order->shiprocket_awb;
                $edd = $shipmentTrack['edd'] ?? $shipmentTrack['expected_delivery_date'] ?? ($shipmentTrack['etd'] ?? null);

                if ($rawStatus || $awb) {
                    $upd = [];
                    if ($rawStatus) $upd['shiprocket_status'] = $rawStatus;
                    if ($awb) {
                        $upd['shiprocket_awb'] = $awb;
                        $upd['tracking_number'] = $awb;
                    }
                    if ($edd) $upd['edd'] = $edd;
                    
                    if (!empty($upd)) {
                        $order->update($upd);
                    }

                    $mockPayload = [
                        'shipment_id'    => $order->shiprocket_shipment_id,
                        'current_status' => $rawStatus ?? $order->shiprocket_status,
                        'awb'            => $awb ?? $order->shiprocket_awb,
                        'edd'            => $edd ?? $order->edd,
                    ];
                    
                    // Force 'ready to ship' if status is pickup related or AWB exists (AND NOT CANCELLED)
                    $normStatus = strtoupper($rawStatus ?? '');
                    $isCancelled = str_contains($normStatus, 'CANCEL');
                    
                    if (!$isCancelled && (str_contains($normStatus, 'PICK') || str_contains($normStatus, 'AWB') || $awb)) {
                        $order->update(['order_status' => 'ready to ship']);
                    }

                    $shiprocket->processWebhook($mockPayload);
                    $synced = true;
                    $statusMsg = "Status synced from Shiprocket: " . ($rawStatus ?? 'AWB Captured');
                    error_log("SYNC ATTEMPT 1 SUCCESS - Status: " . ($rawStatus ?? 'NULL') . " AWB: " . ($awb ?? 'NULL'));
                }
            }
        }

        // Attempt 2: Fallback to Order Details (Crucial for Cancelled orders where tracking might be disabled)
        if (!$synced && $order->shiprocket_order_id) {
            $orderData = $shiprocket->getOrderDetails($order->shiprocket_order_id);
            Log::info("Manual Sync Order Data Fallback for Order #{$order->order_number}:", $orderData ?? []);
            
            if ($orderData && isset($orderData['data'])) {
                $d = $orderData['data'];
                $awb = $d['shipments'][0]['awb'] ?? null;
                $rawStatus = $d['status'] ?? null;
                
                $upd = [];
                if ($rawStatus) $upd['shiprocket_status'] = $rawStatus;
                if ($awb) {
                    $upd['shiprocket_awb'] = $awb;
                    $upd['tracking_number'] = $awb;
                }
                
                if (!empty($upd)) {
                    $order->update($upd);
                }

                $mockPayload = [
                    'order_id'       => $order->shiprocket_order_id,
                    'shipment_id'    => $d['shipments'][0]['id'] ?? $order->shiprocket_shipment_id,
                    'current_status' => $rawStatus ?? $order->shiprocket_status,
                    'awb'            => $awb ?? $order->shiprocket_awb,
                ];
                error_log("SYNC ATTEMPT 2 SUCCESS - Status: " . ($rawStatus ?? 'NULL'));
                // Force 'ready to ship' if status is pickup related or AWB exists (AND NOT CANCELLED)
                $normStatus = strtoupper($rawStatus ?? '');
                $isCancelled = str_contains($normStatus, 'CANCEL');

                if (!$isCancelled && (str_contains($normStatus, 'PICK') || str_contains($normStatus, 'AWB') || $awb)) {
                    $order->update(['order_status' => 'ready to ship']);
                }

                $shiprocket->processWebhook($mockPayload);
                $synced = true;
                $statusMsg = "Status synced from Shiprocket Order Details: " . ($rawStatus ?? 'Details Fetched');
                error_log("SYNC ATTEMPT 2 SUCCESS - Status: " . ($rawStatus ?? 'NULL') . " AWB: " . ($awb ?? 'NULL'));
            }
        }

        return ['synced' => $synced, 'message' => $statusMsg];
    }

    public function saveDimensions(Request $request, Order $order)
    {
        Log::info('Saving dimensions for Order #' . $order->id, $request->all());
        
        try {
            $request->validate([
                'length'  => 'required|numeric|min:0.5',
                'breadth' => 'required|numeric|min:0.5',
                'height'  => 'required|numeric|min:0.5',
                'weight'  => 'required|numeric|min:0.01',
            ]);

            $order->update([
                'package_length'  => (float) $request->length,
                'package_breadth' => (float) $request->breadth,
                'package_height'  => (float) $request->height,
                'package_weight'  => (float) $request->weight,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Package dimensions saved successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to save dimensions: ' . $e->getMessage());
            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function syncPaymentStatus(Order $order)
    {
        if ($order->payment_method !== 'razorpay') {
            return back()->with('error', 'Only Razorpay orders can be synced.');
        }

        if ($order->payment_status === 'paid') {
            return back()->with('info', 'Order is already marked as paid.');
        }

        if (!$order->payment_id) {
            return back()->with('error', 'No Razorpay Order ID found for this order.');
        }

        $creds = $this->getRazorpayCredentials();
        if (!$creds['key'] || !$creds['secret']) {
             return back()->with('error', 'Razorpay credentials not found.');
        }

        try {
            $api = new \Razorpay\Api\Api($creds['key'], $creds['secret']);
            $payments = $api->order->fetch($order->payment_id)->payments();

            $isPaid = false;
            $hasFailed = false;
            foreach ($payments['items'] as $payment) {
                if ($payment['status'] === 'captured') {
                    $isPaid = true;
                    break;
                }
                if ($payment['status'] === 'failed') {
                    $hasFailed = true;
                }
            }

            if ($isPaid) {
                DB::beginTransaction();
                try {
                    $order->update(['payment_status' => 'paid', 'order_status' => 'order placed']);
                    $order->reduceStock();
                    DB::commit();
                    return back()->with('success', 'Payment verified successfully! Order updated.');
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', 'Payment found but failed to update order: ' . $e->getMessage());
                }
            } elseif ($hasFailed) {
                $order->update(['payment_status' => 'failed']);
                return back()->with('warning', 'Payment for this order has failed at Razorpay.');
            } else {
                return back()->with('warning', 'No captured or failed payment found for this Razorpay order yet.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Razorpay Sync Failed: ' . $e->getMessage());
        }
    }

    public function syncAllOnlinePayments(Request $request)
    {
        $selectedIds = $request->input('order_ids');
        
        $query = Order::where('payment_method', 'razorpay')
            ->where('payment_status', 'pending')
            ->whereNotNull('payment_id');

        if (!empty($selectedIds)) {
            $idsArray = explode(',', $selectedIds);
            $query->whereIn('id', $idsArray);
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            return back()->with('info', 'No pending Razorpay orders found to sync.');
        }

        $creds = $this->getRazorpayCredentials();
        if (!$creds['key'] || !$creds['secret']) {
            return back()->with('error', 'Razorpay credentials not found.');
        }

        $updatedCount = 0;
        $failedStatusCount = 0;
        $errorCount = 0;

        try {
            $api = new \Razorpay\Api\Api($creds['key'], $creds['secret']);
            
            foreach ($orders as $order) {
                try {
                    $payments = $api->order->fetch($order->payment_id)->payments();
                    $isPaid = false;
                    $hasFailed = false;
                    foreach ($payments['items'] as $payment) {
                        if ($payment['status'] === 'captured') {
                            $isPaid = true;
                            break;
                        }
                        if ($payment['status'] === 'failed') {
                            $hasFailed = true;
                        }
                    }

                    if ($isPaid) {
                        DB::beginTransaction();
                        try {
                            $order->update(['payment_status' => 'paid', 'order_status' => 'order placed']);
                            $order->reduceStock();
                            DB::commit();
                            $updatedCount++;
                        } catch (\Exception $e) {
                            DB::rollBack();
                            $errorCount++;
                        }
                    } elseif ($hasFailed) {
                        $order->update(['payment_status' => 'failed']);
                        $failedStatusCount++;
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                }
            }

            $msg = [];
            if ($updatedCount > 0) $msg[] = "{$updatedCount} orders marked as Paid";
            if ($failedStatusCount > 0) $msg[] = "{$failedStatusCount} orders marked as Failed";

            if (!empty($msg)) {
                return back()->with('success', "Sync complete: " . implode(', ', $msg) . ".");
            } else {
                return back()->with('info', 'Checked all pending orders, but no status changes were found.');
            }

        } catch (\Exception $e) {
            return back()->with('error', 'Razorpay Bulk Sync Failed: ' . $e->getMessage());
        }
    }

    private function getRazorpayCredentials(): array
    {
        $envPath = base_path('.env');
        $parsed  = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '' || str_starts_with($line, '#')) continue;
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $key = trim($k);
                    $val = trim($v);
                    if (str_contains($val, ' #')) $val = trim(explode(' #', $val)[0]);
                    if (str_starts_with($val, '"') && str_ends_with($val, '"')) $val = trim($val, '"');
                    elseif (str_starts_with($val, "'") && str_ends_with($val, "'")) $val = trim($val, "'");
                    $parsed[$key] = $val;
                }
            }
        }
        $key = $parsed['RAZORPAY_KEY'] ?? config('services.razorpay.key') ?? env('RAZORPAY_KEY');
        $secret = $parsed['RAZORPAY_SECRET'] ?? config('services.razorpay.secret') ?? env('RAZORPAY_SECRET');
        return ['key' => $key, 'secret' => $secret];
    }
}
