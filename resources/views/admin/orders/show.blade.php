@extends('admin.layouts.admin')

@section('title', 'Order Details #' . $order->id)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Main Order Details -->
    <div class="lg:col-span-2 space-y-6">
        <div class="card-glass p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-slate-800">Order Items</h2>
                <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                    <i class="fas fa-arrow-left mr-1"></i> Back to List
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                            <th class="pb-3 font-bold">Product</th>
                            <th class="pb-3 font-bold">Price</th>
                            <th class="pb-3 font-bold">Qty</th>
                            <th class="pb-3 font-bold text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($order->items as $item)
                        <tr class="border-b border-slate-50">
                            <td class="py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $item->getImageUrl() }}" class="w-12 h-12 rounded-lg object-cover border border-slate-100 shadow-sm" alt="">
                                    <div>
                                        <div class="font-bold text-slate-800">{{ $item->product_name }}</div>
                                        @if($item->size || $item->color)
                                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">
                                            @if($item->size) Size: {{ $item->size }} @endif
                                            @if($item->color) {{ $item->size ? '|' : '' }} Color: {{ $item->color }} @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-slate-600">₹{{ number_format($item->price, 2) }}</td>
                            <td class="py-4 text-slate-600">{{ $item->quantity }}</td>
                            <td class="py-4 text-right font-bold text-slate-800">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-slate-600">
                            <td colspan="3" class="pt-4 text-right pr-4 font-bold text-[10px] uppercase">Sub Total</td>
                            <td class="pt-4 text-right font-bold">₹{{ number_format($order->sub_total, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr class="text-rose-500">
                            <td colspan="3" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Discount</td>
                            <td class="py-1 text-right font-bold">-₹{{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="text-slate-600">
                            <td colspan="3" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Shipping</td>
                            <td class="py-1 text-right font-bold">₹{{ number_format($order->shipping, 2) }}</td>
                        </tr>
                        <tr class="text-slate-600">
                            <td colspan="3" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Tax</td>
                            <td class="py-1 text-right font-bold">₹{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr class="text-slate-800 text-lg">
                            <td colspan="3" class="pt-4 text-right pr-4 font-bold">Grand Total</td>
                            <td class="pt-4 text-right font-bold text-[#a91b43]">₹{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="card-glass p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Delivery Address</h2>
            <div class="bg-slate-50 p-4 rounded-xl text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                {{ $order->delivery_address }}
            </div>
        </div>
    </div>

    <!-- Sidebar: Order Status & Actions -->
    <div class="space-y-6">
        <div class="card-glass p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Order Status</h2>
            <div class="space-y-4">
                <div>
                    <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Current Status</label>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-700 
                        @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-700
                        @elseif($order->order_status == 'dispatched') bg-blue-100 text-blue-700
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ $order->order_status }}
                    </span>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Payment Status</label>
                    <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                        @if($order->payment_status == 'paid') bg-emerald-100 text-emerald-700 
                        @elseif($order->payment_status == 'failed') bg-rose-100 text-rose-700
                        @else bg-amber-100 text-amber-700 @endif">
                        {{ $order->payment_status }} ({{ $order->payment_method }})
                    </span>
                </div>
                <hr class="border-slate-100">
                <a href="{{ route('admin.orders.edit', $order->id) }}" class="block w-full bg-[#a91b43] text-white text-center py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all">
                    Update Order Status
                </a>
                <a href="{{ route('admin.orders.invoice', $order->id) }}" class="block w-full bg-slate-800 text-white text-center py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-all">
                    <i class="fas fa-file-download mr-1.5"></i> Download Invoice
                </a>
            </div>
        </div>

        <div class="card-glass p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Tracking Info</h2>
            @if($order->tracking_number)
                <div class="mb-4">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Courier Name</label>
                    <div class="text-sm font-bold text-slate-800">{{ $order->courier_name }}</div>
                </div>
                <div>
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Tracking Number</label>
                    <div class="text-sm font-bold text-[#a91b43] tracking-wider">{{ $order->tracking_number }}</div>
                </div>
            @else
                <p class="text-sm text-slate-400 italic">No tracking information available.</p>
            @endif
        </div>

        <div class="card-glass p-6 rounded-2xl">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Admin Notes</h2>
            <div class="text-sm text-slate-600 bg-amber-50/50 p-3 rounded-lg border border-amber-100 min-h-[60px]">
                {{ $order->admin_notes ?? 'No internal notes added.' }}
            </div>
        </div>
    </div>
</div>
@endsection
