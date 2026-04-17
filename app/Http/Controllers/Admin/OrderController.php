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

        return redirect()->route('admin.orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'coupon']);
        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order, ShiprocketService $shiprocket)
    {
        $request->validate([
            'order_status' => 'required|in:pending,order placed,processing,ready to ship,shipped,out for delivery,delivered,cancelled,returned,refunded',
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
        $order->load('items.product');
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

    public function pushToShiprocket(Order $order, ShiprocketService $shiprocket)
    {
        if ($order->shiprocket_order_id) {
            return back()->with('error', 'Order already pushed to Shiprocket.');
        }

        $result = $shiprocket->createOrder($order);

        if ($result['status']) {
            $order->syncStatus('ready to ship');
            return back()->with('success', 'Order pushed to Shiprocket successfully and status updated to Ready to Ship.');
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
        ]);

        if ($order->shiprocket_order_id) {
            return back()->with('error', 'Order already pushed to Shiprocket.');
        }

        // ── STEP 1: Create order in Shiprocket ─────────────────────────────
        $createResult = $shiprocket->createOrder($order);
        if (!$createResult['status']) {
            return back()->with('error', 'Shiprocket Push Failed: ' . ($createResult['message'] ?? 'Unknown Error'));
        }

        // Reload order to get fresh shiprocket_shipment_id
        $order->refresh();

        // ── STEP 2: Auto-assign AWB ─────────────────────────────────────────
        $awbResult = $shiprocket->assignCourierAndAWB($order);
        if (!$awbResult['status']) {
            // Pushed successfully but AWB failed — still mark ready to ship
            $order->syncStatus('ready to ship');
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
        $order->syncStatus('ready to ship');
        
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
        if (!$order->shiprocket_shipment_id && !$order->shiprocket_order_id) {
            return back()->with('error', 'No Shiprocket IDs found for this order.');
        }

        $synced = false;
        $statusMsg = "Tracking data not available yet.";

        // Attempt 1: Track via shipment ID
        if ($order->shiprocket_shipment_id) {
            $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);
            if (isset($trackingData['tracking_data']) && $trackingData['tracking_data']['track_status'] == 1) {
                $shipmentTrack = $trackingData['tracking_data']['shipment_track'][0] ?? null;
                if ($shipmentTrack) {
                    $mockPayload = [
                        'shipment_id'    => $order->shiprocket_shipment_id,
                        'current_status' => $shipmentTrack['current_status'],
                        'awb'            => $shipmentTrack['awb_code'] ?? $order->shiprocket_awb,
                        'edd'            => $shipmentTrack['edd'] ?? $shipmentTrack['expected_delivery_date'] ?? ($shipmentTrack['etd'] ?? null),
                    ];
                    $shiprocket->processWebhook($mockPayload);
                    $synced = true;
                    $statusMsg = "Status synced from Shiprocket: " . $shipmentTrack['current_status'];
                }
            }
        }

        // Attempt 2: Fallback to Order Details (Crucial for Cancelled orders where tracking might be disabled)
        if (!$synced && $order->shiprocket_order_id) {
            $orderData = $shiprocket->getOrderDetails($order->shiprocket_order_id);
            if ($orderData && isset($orderData['data'])) {
                $shiprocketStatus = $orderData['data']['status'] ?? null;
                if ($shiprocketStatus) {
                    $mockPayload = [
                        'order_id'       => $order->shiprocket_order_id,
                        'current_status' => $shiprocketStatus,
                        'edd'            => $orderData['data']['etd'] ?? null,
                    ];
                    $shiprocket->processWebhook($mockPayload);
                    $synced = true;
                    $statusMsg = "Status synced from Shiprocket Order Info: " . $shiprocketStatus;
                }
            }
        }

        if ($synced) {
            return back()->with('success', $statusMsg);
        }

        return back()->with('error', $statusMsg);
    }
}
