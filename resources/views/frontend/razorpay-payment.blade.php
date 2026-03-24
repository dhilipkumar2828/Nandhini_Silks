@extends('frontend.layouts.app')

@section('title', 'Payment - Nandhini Silks')

@section('content')
<div class="container py-12">
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-indigo-600 px-6 py-8 text-white text-center">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-credit-card text-2xl"></i>
            </div>
            <h2 class="text-2xl font-bold">Secure Payment</h2>
            <p class="text-white/80 mt-1">Please do not refresh or close this page.</p>
        </div>
        
        <div class="p-8">
            <div class="flex justify-between items-center mb-6 pb-6 border-bottom border-gray-100">
                <span class="text-gray-500 font-medium">Order Number:</span>
                <span class="text-gray-900 font-bold tracking-wider">{{ $order->order_number }}</span>
            </div>

            <div class="flex justify-between items-center mb-10">
                <span class="text-gray-500 font-medium">Amount to Pay:</span>
                <span class="text-3xl font-extrabold text-indigo-600">₹{{ number_format($order->grand_total, 2) }}</span>
            </div>

            <button id="rzp-button1" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg hover:shadow-xl flex items-center justify-center gap-3">
                <span>Pay with Razorpay</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex items-center justify-center gap-4 border-t border-gray-100">
            <img src="https://razorpay.com/assets/razorpay-glyph.svg" class="h-4 opacity-50 gray-scale" alt="Razorpay">
            <span class="text-xs text-gray-400 font-medium">100% Encrypted & Secure Payments</span>
        </div>
    </div>
</div>

<form action="{{ route('razorpay.verify') }}" method="POST" id="razorpay-form">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    var options = {
        "key": "{{ config('services.razorpay.key') }}",
        "amount": "{{ $razorOrder['amount'] }}",
        "currency": "INR",
        "name": "Nandhini Silks",
        "description": "Order #{{ $order->order_number }}",
        "image": "https://nandhinisilks.com/logo.png",
        "order_id": "{{ $razorOrder['id'] }}",
        "handler": function (response){
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
            document.getElementById('razorpay_signature').value = response.razorpay_signature;
            document.getElementById('razorpay_form').submit();
        },
        "prefill": {
            "name": "{{ $order->customer_name }}",
            "email": "{{ $order->customer_email }}",
            "contact": "{{ $order->customer_phone }}"
        },
        "theme": {
            "color": "#4f46e5"
        }
    };
    var rzp1 = new Razorpay(options);
    document.getElementById('rzp-button1').onclick = function(e){
        rzp1.open();
        e.preventDefault();
    }
    
    // Auto open
    window.onload = function() {
        rzp1.open();
    };
</script>
@endsection
