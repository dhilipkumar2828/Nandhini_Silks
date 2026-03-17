@extends('admin.layouts.admin')

@section('title', 'Create Order')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-8 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Create New Order</h2>
                <p class="text-xs text-slate-400 mt-1">Manual order entry</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                Cancel and Return
            </a>
        </div>

        <div class="bg-amber-50 border border-amber-100 p-4 rounded-xl mb-6 flex items-start gap-3">
            <i class="fas fa-info-circle text-amber-500 mt-1"></i>
            <p class="text-xs text-amber-700 leading-relaxed">
                Manual order creation is typically for back-office use. Customer notifications will be sent based on the payment and order status set here.
            </p>
        </div>

        <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Customer Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Customer Name</label>
                    <input type="text" name="customer_name" required value="{{ old('customer_name') }}"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Email Address</label>
                    <input type="email" name="customer_email" required value="{{ old('customer_email') }}"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>

                <!-- Phone -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Phone Number</label>
                    <input type="text" name="customer_phone" required value="{{ old('customer_phone') }}"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>

                <!-- Grand Total -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Grand Total (₹)</label>
                    <input type="number" step="0.01" name="grand_total" required value="{{ old('grand_total') }}"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-bold text-[#a91b43] focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>
            </div>

            <!-- Delivery Address -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Delivery Address</label>
                <textarea name="delivery_address" rows="3" required placeholder="Full shipping address..."
                          class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">{{ old('delivery_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Payment Method -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Payment Method</label>
                    <select name="payment_method" class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                        <option value="cod">Cash on Delivery (COD)</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="razorpay">Razorpay / Online</option>
                    </select>
                </div>

                <!-- Order Status -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Initial Order Status</label>
                    <select name="order_status" class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="dispatched">Dispatched</option>
                    </select>
                </div>
            </div>

            <!-- Admin Notes -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Admin Notes</label>
                <textarea name="admin_notes" rows="2" placeholder="Internal remarks..."
                          class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">{{ old('admin_notes') }}</textarea>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-4 rounded-xl text-sm font-bold shadow-lg shadow-[#a91b43]/20 hover:bg-[#940437] transition-all">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
