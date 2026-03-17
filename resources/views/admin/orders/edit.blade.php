@extends('admin.layouts.admin')

@section('title', 'Update Order #' . $order->id)

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-8 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Update Order Status</h2>
                <p class="text-xs text-slate-400 mt-1">Order #{{ $order->id }} for {{ $order->customer_name }}</p>
            </div>
            <a href="{{ route('admin.orders.show', $order->id) }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                <i class="fas fa-eye mr-1"></i> View Order
            </a>
        </div>

        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Order Status -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Order Status</label>
                    <select name="order_status" class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                        <option value="pending" {{ $order->order_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->order_status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="dispatched" {{ $order->order_status == 'dispatched' ? 'selected' : '' }}>Dispatched</option>
                        <option value="delivered" {{ $order->order_status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                        <option value="cancelled" {{ $order->order_status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Payment Status -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Payment Status</label>
                    <select name="payment_status" class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                        <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Courier Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Courier Name</label>
                    <input type="text" name="courier_name" value="{{ old('courier_name', $order->courier_name) }}" placeholder="e.g. Delhivery, Bluedart"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>

                <!-- Tracking Number -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Tracking Number</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="Enter tracking ID"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all font-mono">
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Admin Notes (Internal)</label>
                <textarea name="admin_notes" rows="4" placeholder="Add notes about this order..."
                          class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">{{ old('admin_notes', $order->admin_notes) }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-4 rounded-xl text-sm font-bold shadow-lg shadow-[#a91b43]/20 hover:bg-[#940437] transition-all">
                    Update Order
                </button>
                <a href="{{ route('admin.orders.index') }}" class="block text-center mt-4 text-xs font-bold text-slate-400 hover:text-slate-600">
                    Cancel Changes
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
