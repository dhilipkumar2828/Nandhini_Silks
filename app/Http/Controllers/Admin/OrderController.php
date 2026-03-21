<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

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
            } elseif ($request->status == 'processing') {
                $query->where('order_status', '=', 'processing');
            } elseif ($request->status == 'dispatched') {
                $query->where('order_status', '=', 'dispatched');
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

        $orders = $query->latest('created_at')->paginate(15)->withQueryString();
        
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

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,dispatched,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded,partial',
            'tracking_number' => 'nullable|string|max:255',
            'courier_name' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        $order->update($request->only([
            'order_status',
            'payment_status',
            'tracking_number',
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
}
