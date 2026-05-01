@extends('frontend.layouts.app')

@section('title', 'Secure Payment — Nandhini Silks')

@section('content')
<style>
    :root { --brand: #A91B43; --brand-dark: #8B1535; --brand-light: #fdf0f4; }

    .rzp-wrapper {
        min-height: 80vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fdf0f4 0%, #fff5f7 50%, #fff 100%);
        padding: 2rem 1rem;
    }

    .rzp-card {
        width: 100%;
        max-width: 480px;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(169,27,67,0.12), 0 4px 20px rgba(0,0,0,0.06);
        overflow: hidden;
        animation: slideUp 0.5s ease;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .rzp-header {
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        padding: 2.5rem 2rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .rzp-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.08);
    }

    .rzp-header::after {
        content: '';
        position: absolute;
        bottom: -30px; left: -30px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }

    .rzp-icon-circle {
        width: 72px; height: 72px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
        border: 2px solid rgba(255,255,255,0.3);
        position: relative; z-index: 1;
    }

    .rzp-header h1 {
        font-size: 1.5rem; font-weight: 700;
        color: #fff; margin: 0 0 0.25rem;
        position: relative; z-index: 1;
    }

    .rzp-header p {
        color: rgba(255,255,255,0.82);
        font-size: 0.875rem; margin: 0;
        position: relative; z-index: 1;
    }

    .rzp-body { padding: 2rem; }

    .rzp-info-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 0.875rem 1rem;
        border-radius: 12px;
        background: #fafafa;
        border: 1px solid #f0f0f0;
        margin-bottom: 0.75rem;
    }

    .rzp-info-row .label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #999;
    }

    .rzp-info-row .value {
        font-size: 0.9rem; font-weight: 700; color: #222;
    }

    .rzp-amount-box {
        background: var(--brand-light);
        border: 2px solid rgba(169,27,67,0.15);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        text-align: center;
        margin: 1.25rem 0;
    }

    .rzp-amount-box .amount-label {
        font-size: 0.75rem; font-weight: 700;
        text-transform: uppercase; letter-spacing: 1px;
        color: var(--brand); margin-bottom: 0.25rem;
    }

    .rzp-amount-box .amount-value {
        font-size: 2.5rem; font-weight: 900;
        color: var(--brand); line-height: 1;
    }

    .rzp-pay-btn {
        width: 100%;
        background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: 1rem 1.5rem;
        font-size: 1rem; font-weight: 700;
        cursor: pointer;
        display: flex; align-items: center; justify-content: center; gap: 0.75rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 24px rgba(169,27,67,0.35);
        margin-top: 1.5rem;
        letter-spacing: 0.3px;
    }

    .rzp-pay-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(169,27,67,0.45);
    }

    .rzp-pay-btn:active { transform: translateY(0); }

    .rzp-pay-btn .spinner {
        display: none;
        width: 20px; height: 20px;
        border: 2px solid rgba(255,255,255,0.4);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .rzp-footer {
        background: #fafafa;
        border-top: 1px solid #f0f0f0;
        padding: 1rem 2rem;
        display: flex; align-items: center; justify-content: center; gap: 1rem;
        flex-wrap: wrap;
    }

    .rzp-trust-badge {
        display: flex; align-items: center; gap: 0.35rem;
        font-size: 0.72rem; color: #888; font-weight: 600;
    }

    .rzp-trust-badge i { color: #22c55e; font-size: 0.8rem; }

    .rzp-warning {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-top: 1rem;
        display: flex; align-items: flex-start; gap: 0.5rem;
        font-size: 0.78rem; color: #92400e;
    }

    @media (min-width: 480px) {
        .rzp-header { padding: 3rem 2.5rem 2.5rem; }
        .rzp-body { padding: 2.5rem; }
    }

    /* Full-screen Loader Overlay */
    .payment-processing-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(255, 255, 255, 0.95);
        display: none; /* Hidden by default */
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999999; /* Ensure it is above everything, including Razorpay modal if it persists */
        backdrop-filter: blur(8px);
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }

    .loader-container {
        text-align: center;
    }

    .custom-loader {
        width: 60px;
        height: 60px;
        border: 5px solid var(--brand-light);
        border-top: 5px solid var(--brand);
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 1.5rem;
        box-shadow: 0 0 15px rgba(169, 27, 67, 0.1);
    }

    .processing-text {
        color: var(--brand);
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 0.5rem;
    }

    .processing-subtext {
        color: #666;
        font-size: 0.95rem;
    }
</style>

<div class="payment-processing-overlay" id="payment-overlay">
    <div class="loader-container">
        <div class="custom-loader"></div>
        <div class="processing-text">Processing Your Payment</div>
        <div class="processing-subtext">Please do not close or refresh this page.</div>
    </div>
</div>

<div class="rzp-wrapper">
    <div class="rzp-card">
        {{-- Header --}}
        <div class="rzp-header">
            <div class="rzp-icon-circle">
                <i class="fas fa-shield-alt" style="font-size:1.8rem; color:#fff;"></i>
            </div>
            <h1>Complete Your Payment</h1>
            <p>Secured by Razorpay — 256-bit SSL Encryption</p>
        </div>

        {{-- Body --}}
        <div class="rzp-body">

            {{-- Order Info --}}
            <div class="rzp-info-row">
                <span class="label">Order ID</span>
                <span class="value">{{ $order->order_number }}</span>
            </div>
            <div class="rzp-info-row">
                <span class="label">Customer</span>
                <span class="value">{{ $order->customer_name }}</span>
            </div>

            {{-- Amount --}}
            <div class="rzp-amount-box">
                <div class="amount-label">Total Amount to Pay</div>
                <div class="amount-value">₹{{ number_format($order->grand_total, 2) }}</div>
            </div>

            {{-- Do not close warning --}}
            <div class="rzp-warning">
                <i class="fas fa-exclamation-triangle" style="margin-top:1px; color:#d97706;"></i>
                <span>Please <strong>do not refresh or close</strong> this page until the payment is complete.</span>
            </div>

            {{-- Pay Button --}}
            <button id="rzp-button1" class="rzp-pay-btn" onclick="openRazorpay(this)">
                <i class="fas fa-lock"></i>
                <span>Pay Securely ₹{{ number_format($order->grand_total, 2) }}</span>
                <div class="spinner" id="btn-spinner"></div>
            </button>

            {{-- Manual Verification Fallback (Hidden by default) --}}
            <div id="manual-verify-box" style="display: none; margin-top: 1.5rem; text-align: center; animation: fadeIn 0.5s ease;">
                <p style="font-size: 0.85rem; color: #666; margin-bottom: 0.5rem;">Payment already done but stuck?</p>
                <button type="button" onclick="location.reload()" class="rzp-pay-btn" style="background: #666; box-shadow: none; padding: 0.75rem; font-size: 0.9rem;">
                    <i class="fas fa-sync-alt"></i> Refresh Page
                </button>
            </div>
        </div>

        {{-- Footer Trust Badges --}}
        <div class="rzp-footer">
            <div class="rzp-trust-badge">
                <i class="fas fa-check-circle"></i>
                <span>100% Secure</span>
            </div>
            <div class="rzp-trust-badge">
                <i class="fas fa-check-circle"></i>
                <span>PCI DSS Compliant</span>
            </div>
            <div class="rzp-trust-badge">
                <i class="fas fa-check-circle"></i>
                <span>UPI / Cards / NetBanking</span>
            </div>
        </div>
    </div>
</div>

{{-- Hidden form for backend verification --}}
<form action="{{ route('razorpay.verify') }}" method="POST" id="razorpay-form">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id"   id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature"  id="razorpay_signature">
</form>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    (function() {
        console.log("Razorpay Script Initialized");
        
        var options = {
            "key"         : "{{ config('services.razorpay.key') }}",
            "amount"      : "{{ $razorOrder['amount'] }}",
            "currency"    : "INR",
            "name"        : "Nandhini Silks",
            "description" : "Order #{{ $order->order_number }}",
            "order_id"    : "{{ $razorOrder['id'] }}",
            "handler"     : function (response) {
                console.log("Razorpay Success Handler Triggered", response);
                
                // Show Full Screen Overlay
                var overlay = document.getElementById('payment-overlay');
                if (overlay) {
                    overlay.style.display = 'flex';
                }

                // Show spinner on button as well
                var btn = document.getElementById('rzp-button1');
                if (btn) {
                    btn.disabled = true;
                    var btnSpan = btn.querySelector('span');
                    if (btnSpan) btnSpan.textContent = 'Verifying Payment…';
                    var btnSpinner = document.getElementById('btn-spinner');
                    if (btnSpinner) btnSpinner.style.display = 'block';
                }

                console.log("Populating verification form...");
                var payIdInput = document.getElementById('razorpay_payment_id');
                var ordIdInput = document.getElementById('razorpay_order_id');
                var sigInput = document.getElementById('razorpay_signature');
                var verifyForm = document.getElementById('razorpay-form');

                if (payIdInput && ordIdInput && sigInput && verifyForm) {
                    payIdInput.value = response.razorpay_payment_id;
                    ordIdInput.value = response.razorpay_order_id || "{{ $razorOrder['id'] }}";
                    sigInput.value  = response.razorpay_signature;
                    
                    console.log("Submitting verification form to: " + verifyForm.action);
                    
                    // Small delay to ensure UI updates are rendered
                    setTimeout(function() {
                        verifyForm.submit();
                    }, 500);
                } else {
                    console.error("Critical Error: Verification form or inputs not found in DOM!");
                    alert("Something went wrong with the payment verification. Please do not close this page and contact support.");
                }
            },
            "prefill": {
                "name"    : "{{ $order->customer_name }}",
                "email"   : "{{ $order->customer_email }}",
                "contact" : "{{ $order->customer_phone }}"
            },
            "notes": {
                "order_number": "{{ $order->order_number }}"
            },
            "theme": {
                "color": "#A91B43"
            },
            "modal": {
                "ondismiss": function() {
                    console.log('Payment modal dismissed by user.');
                    // Reset button state
                    var btn = document.getElementById('rzp-button1');
                    if (btn) {
                        btn.disabled = false;
                        btn.querySelector('span').textContent = 'Pay Securely ₹{{ number_format($order->grand_total, 2) }}';
                        document.getElementById('btn-spinner').style.display = 'none';
                    }
                    // Show manual refresh option
                    document.getElementById('manual-verify-box').style.display = 'block';
                }
            }
        };

        var rzp1 = new Razorpay(options);

        window.openRazorpay = function(btn) {
            console.log("Opening Razorpay Modal...");
            // Show loading state on button
            btn.disabled = true;
            var span = btn.querySelector('span');
            if (span) span.textContent = 'Opening Secure Gateway…';
            var spinner = document.getElementById('btn-spinner');
            if (spinner) spinner.style.display = 'block';
            
            rzp1.open();
        };

        // Auto-open payment modal on page load
        window.addEventListener('load', function () {
            var btn = document.getElementById('rzp-button1');
            if (btn) {
                setTimeout(function() { 
                    console.log("Auto-opening Razorpay...");
                    window.openRazorpay(btn); 
                }, 800);
            }
        });
    })();
</script>
@endpush
@endsection
