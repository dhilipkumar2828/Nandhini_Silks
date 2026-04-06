@extends('admin.layouts.admin')

@section('title', 'Order Details #' . $order->order_number)

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
                        {{ ucwords($order->order_status) }}
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
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold text-slate-800">Shiprocket Logistics</h2>
                <img src="https://www.shiprocket.in/wp-content/themes/shiprocket/assets/images/shiprocket-logo.svg" alt="Shiprocket" class="h-5">
            </div>
            
            @if($order->shiprocket_order_id)
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Shiprocket Order ID</label>
                            <span class="text-sm font-bold text-slate-800">{{ $order->shiprocket_order_id ?? '-' }}</span>
                        </div>
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Shipment ID</label>
                            <span class="text-sm font-bold text-slate-800">{{ $order->shiprocket_shipment_id ?? '-' }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">AWB Tracking Number</label>
                        @if($order->shiprocket_awb)
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-black text-blue-900 tracking-wider">{{ $order->shiprocket_awb }}</span>
                                <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[9px] font-black uppercase rounded">Assigned</span>
                            </div>
                        @else
                            <form action="{{ route('admin.orders.shiprocket.awb', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="flex items-center gap-1.5 text-[11px] bg-blue-50 text-blue-600 px-4 py-2 rounded-lg font-bold hover:bg-blue-100 hover:text-blue-700 transition-all border border-blue-100 uppercase tracking-wider">
                                    <i class="fas fa-plus-circle"></i> Generate AWB Now
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="pt-4 grid grid-cols-2 gap-3">
                        <a href="{{ route('admin.orders.shiprocket.label', $order->id) }}" target="_blank" class="flex items-center justify-center gap-2 bg-slate-900 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-black hover:shadow-lg transition-all active:scale-[0.98]">
                            <i class="fas fa-print text-xs"></i> Print Label
                        </a>
                        <form action="{{ route('admin.orders.shiprocket.pickup', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-emerald-700 hover:shadow-lg transition-all active:scale-[0.98]">
                                <i class="fas fa-calendar-check text-xs"></i> Call Pickup
                            </button>
                        </form>
                    </div>

                    @if($order->order_status == 'delivered')
                        <div class="pt-2">
                            <form action="{{ route('admin.orders.shiprocket.return', $order->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-rose-50 text-rose-600 border border-rose-100 py-2.5 rounded-xl text-[11px] font-bold hover:bg-rose-100 transition-all">
                                    <i class="fas fa-rotate-left"></i> Initiate Channel Return
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @else
                <div class="text-center py-6 px-4 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4">
                        <i class="fas fa-cloud-arrow-up text-slate-300 text-lg"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-500 mb-5 leading-relaxed tracking-wide uppercase">Requires Manual Synchronization</p>
                    <form action="{{ route('admin.orders.shiprocket.push', $order->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-[#a91b43] text-white py-3 rounded-xl text-xs font-black shadow-lg shadow-rose-100 hover:bg-[#940437] transition-all active:scale-[0.98] uppercase tracking-widest">
                            <i class="fas fa-bolt mr-2 text-xs"></i> Push to Shiprocket
                        </button>
                    </form>
                </div>
            @endif
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

        @if($order->return_status)
        <div class="card-glass p-6 rounded-2xl border-2 border-amber-100 shadow-lg shadow-amber-50/50">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                    <i class="fas fa-undo text-amber-600 text-lg"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Return Request</h2>
                    <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest">Customer Logic Flow</p>
                </div>
            </div>

            <div class="space-y-5">
                <div class="bg-amber-50/50 border border-amber-100 p-4 rounded-xl">
                    <label class="text-[10px] font-bold uppercase text-amber-600 block mb-2 opacity-70">Customer Reason</label>
                    <p class="text-sm font-bold text-slate-700 leading-relaxed italic">"{{ $order->return_reason }}"</p>
                </div>

                <form action="{{ route('orders.return.status', $order->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 block mb-2">Update Return State</label>
                        <select name="return_status" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 outline-none focus:border-amber-400 focus:ring-4 focus:ring-amber-50 transition-all cursor-pointer">
                            <option value="requested" {{ $order->return_status == 'requested' ? 'selected' : '' }}>Requested (Pending Review)</option>
                            <option value="approved" {{ $order->return_status == 'approved' ? 'selected' : '' }}>Approved (Create Shiprocket Pickup)</option>
                            <option value="rejected" {{ $order->return_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="picked" {{ $order->return_status == 'picked' ? 'selected' : '' }}>Picked Up</option>
                            <option value="received" {{ $order->return_status == 'received' ? 'selected' : '' }}>Received in Warehouse</option>
                            <option value="refunded" {{ $order->return_status == 'refunded' ? 'selected' : '' }}>Refunded to Customer</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-bold uppercase text-slate-400 block mb-2">Internal Notes</label>
                        <textarea name="admin_notes" rows="2" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-amber-400 transition-all resize-none">{{ $order->return_admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-amber-500 text-white text-center py-3 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-amber-600 transition-all shadow-lg">
                        Update Return State
                    </button>
                </form>

                @if($order->reverse_awb)
                <div class="pt-4 mt-4 border-t border-slate-100">
                    <label class="text-[10px] font-bold uppercase text-slate-400 block">Shiprocket AWB</label>
                    <span class="text-sm font-black text-slate-800 tracking-wider">{{ $order->reverse_awb }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
