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
                            <th class="pb-3 font-bold text-right">Price</th>
                            <th class="pb-3 font-bold text-right">Qty</th>
                            <th class="pb-3 font-bold text-right">Tax</th>
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
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if(!empty($item->attributes) && is_array($item->attributes))
                                                @foreach($item->attributes as $attr)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 tracking-tighter">
                                                        {{ $attr['name'] }}: {{ $attr['value'] }}
                                                    </span>
                                                @endforeach
                                            @elseif($item->size || $item->color)
                                                @if($item->size) 
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 tracking-tighter">
                                                    Size: {{ $item->size }}
                                                </span>
                                                @endif
                                                @if($item->color) 
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-100 text-slate-600 border border-slate-200 tracking-tighter">
                                                    Color: {{ $item->color }}
                                                </span>
                                                @endif
                                            @endif
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 text-slate-600 text-right">₹{{ number_format($item->price, 2) }}</td>
                            <td class="py-4 text-slate-600 text-right">{{ $item->quantity }}</td>
                            <td class="py-4 text-slate-600 text-right">
                                <div>₹{{ number_format($item->tax_amount ?? 0, 2) }}</div>
                                <div class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">({{ $item->tax_rate ?? 0 }}%)</div>
                            </td>
                            <td class="py-4 text-right font-black text-slate-800">₹{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="text-slate-600">
                            <td colspan="4" class="pt-4 text-right pr-4 font-bold text-[10px] uppercase">Sub Total</td>
                            <td class="pt-4 text-right font-bold">₹{{ number_format($order->sub_total, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr class="text-rose-500">
                            <td colspan="4" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Discount</td>
                            <td class="py-1 text-right font-bold">-₹{{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="text-slate-600">
                            <td colspan="4" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Shipping</td>
                            <td class="py-1 text-right font-bold">₹{{ number_format($order->shipping, 2) }}</td>
                        </tr>
                        <tr class="text-slate-600">
                            <td colspan="4" class="py-1 text-right pr-4 font-bold text-[10px] uppercase">Tax (GST)</td>
                            <td class="py-1 text-right font-bold">₹{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr class="text-slate-800 text-lg">
                            <td colspan="4" class="pt-4 text-right pr-4 font-bold">Grand Total</td>
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
        <div class="card-glass p-0 rounded-2xl overflow-hidden shadow-xl border-slate-100/50">
            <div class="p-5 border-b border-slate-50 bg-slate-50/50 flex justify-between items-center">
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Workflow Engine</h2>
                <i class="fas fa-microchip text-rose-500 text-xs"></i>
            </div>
            <div class="p-6 space-y-6">
                <!-- Status Sync -->
                <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-tighter">Order Processing State</label>
                        <select name="order_status" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all cursor-pointer">
                            <option value="order placed" {{ $order->order_status == 'order placed' ? 'selected' : '' }}>Order Placed (Pending)</option>
                            <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="ready to ship" {{ $order->order_status == 'ready to ship' ? 'selected' : '' }}>Ready to Ship</option>
                            <option value="shipped" {{ $order->order_status == 'shipped' ? 'selected' : '' }}>Shipped (In Transit)</option>
                            <option value="out for delivery" {{ $order->order_status == 'out for delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered (Complete)</option>
                            <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-tighter">Financial Settlement</label>
                        <select name="payment_status" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all cursor-pointer">
                            <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending Payment</option>
                            <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid (Confirmed)</option>
                            <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Payment Failed</option>
                            <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-tighter">Internal Intelligence (Admin Notes)</label>
                        <textarea name="admin_notes" rows="3" placeholder="Add private notes about this order..." 
                                  class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 outline-none focus:border-indigo-500 transition-all resize-none shadow-inner">{{ $order->admin_notes }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-black hover:shadow-xl hover:shadow-slate-200 transition-all active:scale-[0.98]">
                        <i class="fas fa-save mr-2"></i> Deploy Status Updates
                    </button>
                </form>

                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="flex items-center justify-center gap-3 w-full border-2 border-slate-100 text-slate-600 py-3 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                        <i class="fas fa-file-invoice text-rose-500"></i> Download Official Invoice
                    </a>
                </div>
            </div>
        </div>

        <div class="card-glass p-6 rounded-2xl">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Shiprocket Logistics</h2>
                    @if($order->shiprocket_status)
                        <span class="text-[10px] font-black uppercase tracking-widest text-[#a91b43] bg-rose-50 px-2 py-0.5 rounded border border-rose-100">
                            Live: {{ $order->shiprocket_status }}
                        </span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    @if($order->shiprocket_shipment_id)
                        <form action="{{ route('admin.orders.shiprocket.sync', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="p-2 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 transition-all shadow-sm" title="Sync Live Status">
                                <i class="fas fa-sync-alt text-xs"></i>
                            </button>
                        </form>
                    @endif
                    <img src="https://www.shiprocket.in/wp-content/uploads/2023/01/shiprocket_logo.svg" alt="Shiprocket" class="h-6">
                </div>
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
                        @if($order->pickup_scheduled_at)
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Pickup Scheduled</label>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-black rounded-lg border border-emerald-100">
                                <i class="fas fa-calendar-check text-[10px]"></i>
                                {{ \Carbon\Carbon::parse($order->pickup_scheduled_at)->format('d M Y') }}
                            </span>
                        </div>
                        @endif
                        @if($order->edd)
                        <div>
                            <label class="text-[10px] font-bold uppercase text-slate-400 block mb-1">Est. Delivery</label>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-black rounded-lg border border-blue-100">
                                <i class="fas fa-truck text-[10px]"></i>
                                {{ \Carbon\Carbon::parse($order->edd)->format('d M Y') }}
                            </span>
                        </div>
                        @endif
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

                    @if($order->shiprocket_awb)
                    <div class="pt-4 grid grid-cols-2 gap-3">
                        {{-- Print Label --}}
                        <a href="{{ route('admin.orders.shiprocket.label', $order->id) }}" target="_blank"
                            class="flex items-center justify-center gap-2 bg-slate-900 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-black hover:shadow-lg transition-all active:scale-[0.98]">
                            <i class="fas fa-print text-xs"></i> Print Label
                        </a>

                        {{-- Call Pickup --}}
                        <form action="{{ route('admin.orders.shiprocket.pickup', $order->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-emerald-600 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-emerald-700 hover:shadow-lg transition-all active:scale-[0.98]">
                                <i class="fas fa-calendar-check text-xs"></i> Call Pickup
                            </button>
                        </form>

                        {{-- Generate Manifest --}}
                        @if($order->shiprocket_manifest_url)
                            <a href="{{ $order->shiprocket_manifest_url }}" target="_blank"
                                class="col-span-2 flex items-center justify-center gap-2 bg-violet-600 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-violet-700 hover:shadow-lg transition-all active:scale-[0.98]">
                                <i class="fas fa-file-arrow-down text-xs"></i> Download Manifest
                            </a>
                        @else
                            <form action="{{ route('admin.orders.shiprocket.manifest', $order->id) }}" method="POST" class="col-span-2">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center gap-2 bg-violet-600 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-violet-700 hover:shadow-lg transition-all active:scale-[0.98]">
                                    <i class="fas fa-clipboard-list text-xs"></i> Generate Manifest
                                </button>
                            </form>
                        @endif

                        {{-- Shiprocket Invoice --}}
                        <form action="{{ route('admin.orders.shiprocket.invoice', $order->id) }}" method="POST" class="col-span-2">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-indigo-600 text-white py-3 rounded-xl text-[11px] font-bold hover:bg-indigo-700 hover:shadow-lg transition-all active:scale-[0.98]">
                                <i class="fas fa-file-invoice text-xs"></i> Generate Shiprocket Invoice
                            </button>
                        </form>
                    </div>
                    @endif

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
                    <p class="text-xs font-bold text-slate-500 mb-5 leading-relaxed tracking-wide uppercase">Push & Schedule Pickup</p>
                    {{-- Trigger Modal --}}
                    <button type="button" onclick="document.getElementById('pickupModal').classList.remove('hidden')"
                        class="w-full bg-[#a91b43] text-white py-3 rounded-xl text-xs font-black shadow-lg shadow-rose-100 hover:bg-[#940437] transition-all active:scale-[0.98] uppercase tracking-widest">
                        <i class="fas fa-bolt mr-2 text-xs"></i> Push to Shiprocket
                    </button>
                </div>

                {{-- ── Pickup Date Modal ────────────────────────────────────────── --}}
                <div id="pickupModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm px-4">
                    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-fade-in">
                        {{-- Header --}}
                        <div class="bg-gradient-to-r from-[#a91b43] to-rose-500 px-6 py-5 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-white/20 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-truck text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-white uppercase tracking-widest">Push to Shiprocket</h3>
                                    <p class="text-[10px] text-rose-200 font-semibold">Order #{{ $order->order_number }}</p>
                                </div>
                            </div>
                            <button onclick="document.getElementById('pickupModal').classList.add('hidden')"
                                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-all">
                                <i class="fas fa-times text-white text-xs"></i>
                            </button>
                        </div>

                        {{-- Body --}}
                        <form action="{{ route('admin.orders.shiprocket.push-with-pickup', $order->id) }}" method="POST" class="p-6 space-y-5">
                            @csrf

                            {{-- What will happen info --}}
                            <div class="bg-slate-50 rounded-2xl p-4 space-y-2.5">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">What happens when you submit:</p>
                                <div class="flex items-center gap-3 text-xs font-semibold text-slate-600">
                                    <span class="w-6 h-6 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">1</span>
                                    Order will be created in Shiprocket
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold text-slate-600">
                                    <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">2</span>
                                    AWB will be auto-assigned (best courier selected)
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold text-slate-600">
                                    <span class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">3</span>
                                    Pickup will be scheduled on your chosen date
                                </div>
                                <div class="flex items-center gap-3 text-xs font-semibold text-slate-600">
                                    <span class="w-6 h-6 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-[10px] font-black flex-shrink-0">4</span>
                                    Email notification sent to Customer &amp; Admin
                                </div>
                            </div>

                            {{-- Date Picker --}}
                            <div>
                                <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-widest">
                                    <i class="fas fa-calendar-alt text-rose-400 mr-1"></i> Select Pickup Date
                                </label>
                                <input type="date" name="pickup_date" id="pickup_date"
                                    min="{{ date('Y-m-d') }}"
                                    value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                    class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-700 outline-none focus:border-rose-400 focus:ring-4 focus:ring-rose-400/10 transition-all cursor-pointer"
                                    required>
                                <p class="mt-1.5 text-[10px] text-slate-400 font-medium">* Only today or a future date can be selected</p>
                            </div>

                            {{-- Buttons --}}
                            <div class="flex gap-3 pt-1">
                                <button type="button" onclick="document.getElementById('pickupModal').classList.add('hidden')"
                                    class="flex-1 py-3 border-2 border-slate-100 text-slate-500 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-50 transition-all">
                                    Cancel
                                </button>
                                <button type="submit" id="pushSubmitBtn"
                                    class="flex-1 py-3 bg-[#a91b43] text-white rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-[#940437] shadow-lg shadow-rose-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                    <i class="fas fa-bolt text-xs"></i> Push & Schedule
                                </button>
                            </div>
                        </form>
                    </div>
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
        <div class="card-glass p-0 rounded-3xl border-2 border-amber-100/50 shadow-2xl shadow-amber-900/5 overflow-hidden">
            <div class="bg-gradient-to-r from-amber-50 to-white px-6 py-5 border-b border-amber-100 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-white rounded-2xl flex items-center justify-center shadow-sm border border-amber-200">
                        <i class="fas fa-reply-all text-amber-600"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Recovery Protocol</h2>
                        <p class="text-[9px] font-bold text-amber-600 uppercase tracking-tighter italic">Customer-Initiated Return</p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-[10px] font-black uppercase tracking-widest">
                        State: {{ strtoupper($order->return_status ?? 'NONE') }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Return Context -->
                <div class="bg-slate-50 border border-slate-100 p-5 rounded-2xl relative">
                    <i class="fas fa-quote-left absolute top-4 left-4 text-slate-200 text-2xl"></i>
                    <label class="text-[10px] font-black uppercase text-slate-400 block mb-3 relative z-10 pl-6">Statement of Reason</label>
                    <p class="text-sm font-bold text-slate-700 leading-relaxed italic pl-6">"{{ $order->return_reason }}"</p>
                </div>

                <!-- Action Logic -->
                <form action="{{ route('admin.orders.return.status', $order->id) }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-4">
                        <div class="group">
                            <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-tighter">Transition To</label>
                            <select name="return_status" class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-800 outline-none focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all cursor-pointer">
                                <option value="requested" {{ $order->return_status == 'requested' ? 'selected' : '' }}>Requested (Under Review)</option>
                                <option value="approved" {{ $order->return_status == 'approved' ? 'selected' : '' }}>Approve (Trigger Shiprocket Pickup)</option>
                                <option value="rejected" {{ $order->return_status == 'rejected' ? 'selected' : '' }}>Reject Request</option>
                                <option value="picked" {{ $order->return_status == 'picked' ? 'selected' : '' }}>Picked Up (Reverse AWB)</option>
                                <option value="received" {{ $order->return_status == 'received' ? 'selected' : '' }}>Received & Verified</option>
                                <option value="refunded" {{ $order->return_status == 'refunded' ? 'selected' : '' }}>Refunded (Complete)</option>
                            </select>
                            <p class="mt-2 text-[9px] text-slate-400 font-medium leading-tight">Note: Selecting 'Approve' will automatically attempt to create a return order in Shiprocket.</p>
                        </div>

                        <div>
                            <label class="text-[10px] font-black uppercase text-slate-400 block mb-2 tracking-tighter">Processing Intelligence</label>
                            <textarea name="admin_notes" rows="3" placeholder="Notes for this return workflow..." 
                                      class="w-full bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-semibold text-slate-600 outline-none focus:border-amber-500 transition-all resize-none shadow-inner">{{ $order->return_admin_notes }}</textarea>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-amber-500 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-amber-600 hover:shadow-xl hover:shadow-amber-100 transition-all active:scale-[0.98]">
                        <i class="fas fa-check-double mr-2"></i> Update Recovery State
                    </button>
                </form>

                @if($order->shiprocket_return_shipment_id || $order->reverse_awb)
                <div class="pt-6 border-t border-slate-100">
                    <div class="flex items-center gap-2 mb-4">
                        <img src="https://www.shiprocket.in/wp-content/uploads/2023/01/shiprocket_logo.svg" alt="Shiprocket" class="h-4 opacity-50">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none">Reverse Logistics ID</span>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        @if($order->reverse_awb)
                        <div class="px-4 py-2 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <div class="text-[9px] font-bold text-indigo-400 uppercase tracking-tighter">Reverse AWB</div>
                            <div class="text-xs font-black text-indigo-900 tracking-wider">{{ $order->reverse_awb }}</div>
                        </div>
                        @endif
                        @if($order->shiprocket_return_shipment_id)
                        <div class="px-4 py-2 bg-slate-50 border border-slate-100 rounded-xl">
                            <div class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Shipment ID</div>
                            <div class="text-xs font-black text-slate-800 tracking-wider">{{ $order->shiprocket_return_shipment_id }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
