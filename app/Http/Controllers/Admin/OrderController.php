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
        
        return view('admin.orders.index', compact('orders'));
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
            'order_status' => 'required|in:pending,order placed,shipped,out for delivery,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partial',
            'tracking_number' => 'nullable|string|max:255',
            'courier_name' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $oldStatus = $order->order_status;
        $oldTracking = $order->tracking_number;
        $newStatus = $request->order_status;

        // If manually cancelling, tell Shiprocket to cancel too
        if ($newStatus == 'cancelled' && $oldStatus != 'cancelled') {
            if ($order->shiprocket_order_id) {
                $shiprocket->cancelOrder($order->shiprocket_order_id);
            }
        }

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
            return back()->with('success', 'Order pushed to Shiprocket successfully. Order ID: ' . $result['data']['order_id']);
        }

        return back()->with('error', 'Shiprocket Error: ' . ($result['message'] ?? 'Unknown Error'));
    }

    public function assignShiprocketAWB(Order $order, ShiprocketService $shiprocket)
    {
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'Order must be pushed to Shiprocket first.');
        }

        $result = $shiprocket->assignAWB($order->shiprocket_shipment_id);

        if ($result['status']) {
            $order->update([
                'shiprocket_awb' => $result['awb'],
                'tracking_number' => $result['awb'],
                'courier_name' => 'Shiprocket',
            ]);
            return back()->with('success', 'AWB assigned successfully: ' . $result['awb']);
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
        if ($order->order_status != 'delivered') {
            return back()->with('error', 'Only delivered orders can be returned.');
        }

        $result = $shiprocket->createReturnOrder($order);

        if ($result['status']) {
            $order->update(['order_status' => 'refunded']); // or returned
            return back()->with('success', 'Return order created in Shiprocket. Return ID: ' . $result['data']['shipment_id']);
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

        // If refunded, maybe update payment status too
        if ($newStatus === 'refunded') {
            $order->update(['payment_status' => 'refunded']);
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
        if (!$order->shiprocket_shipment_id) {
            return back()->with('error', 'No Shipment ID found for this order.');
        }

        $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);
        
        Log::info('Order sync manually triggered', ['order_id' => $order->id, 'shipment_id' => $order->shiprocket_shipment_id]);

        if (isset($trackingData['tracking_data']) && $trackingData['tracking_data']['track_status'] == 1) {
            $shipmentTrack = $trackingData['tracking_data']['shipment_track'][0] ?? null;
            if ($shipmentTrack) {
                // Mocking a webhook payload to use the existing status mapping logic in service
                $mockPayload = [
                    'shipment_id'    => $order->shiprocket_shipment_id,
                    'current_status' => $shipmentTrack['current_status'],
                    'awb'            => $shipmentTrack['awb_code'] ?? $order->shiprocket_awb,
                ];

                $shiprocket->processWebhook($mockPayload);
                
                return back()->with('success', 'Status synced from Shiprocket: ' . $shipmentTrack['current_status']);
            }
        }

        return back()->with('error', 'Shiprocket tracking data not available yet.');
    }
}
