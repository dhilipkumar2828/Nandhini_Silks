@extends('frontend.layouts.app')

@section('title', 'Order Confirmed! | Nandhini Silks')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    .confirmation-page {
        background: linear-gradient(135deg, #fffcf0 0%, #fff5f8 100%);
        padding: 60px 0 80px;
        min-height: 85vh;
        font-family: 'Outfit', sans-serif;
    }
    .success-card {
        max-width: 820px;
        margin: 0 auto;
        background: #fff;
        border-radius: 28px;
        padding: 55px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .success-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 5px;
        background: linear-gradient(90deg, #A91B43, #e07b63, #A91B43);
        background-size: 200% auto;
        animation: shimmer 2s linear infinite;
    }
    @keyframes shimmer {
        to { background-position: 200% center; }
    }

    /* Animated checkmark */
    .success-icon-wrap {
        margin: 0 auto 28px;
        width: 90px;
        height: 90px;
    }
    .checkmark-circle {
        fill: none;
        stroke: #2e7d32;
        stroke-width: 2;
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark {
        transform-box: fill-box;
        transform-origin: 50% 50%;
        stroke: #2e7d32;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
        fill: none;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.5s forwards;
    }
    .checkmark-bg {
        fill: #e8f5e9;
        stroke: none;
        animation: none;
    }
    @keyframes stroke {
        100% { stroke-dashoffset: 0; }
    }

    .order-status-title {
        font-size: 30px;
        font-weight: 800;
        color: #1a1a1a;
        margin-bottom: 8px;
    }
    .order-status-text {
        color: #777;
        font-size: 14px;
        margin-bottom: 10px;
        line-height: 1.6;
    }
    .redirect-bar-wrap {
        background: #f5f5f5;
        border-radius: 50px;
        height: 5px;
        max-width: 300px;
        margin: 16px auto 5px;
        overflow: hidden;
    }
    .redirect-bar {
        height: 100%;
        background: linear-gradient(90deg, #A91B43, #e07b63);
        border-radius: 50px;
        width: 100%;
        animation: shrink 8s linear forwards;
        transform-origin: left;
    }
    @keyframes shrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .redirect-text {
        font-size: 12px;
        color: #aaa;
        margin-bottom: 30px;
    }
    .confirmation-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        text-align: left;
        margin-top: 35px;
        padding-top: 35px;
        border-top: 1px solid #f0f0f0;
    }
    .detail-box {
        background: #fafafa;
        padding: 18px 20px;
        border-radius: 14px;
        border: 1px solid #f0f0f0;
    }
    .detail-label {
        font-size: 10px;
        color: #bbb;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 6px;
        font-weight: 700;
    }
    .detail-value {
        font-weight: 600;
        color: #333;
        font-size: 14px;
        line-height: 1.5;
    }
    .order-summary-table {
        width: 100%;
        margin-top: 30px;
        border-collapse: collapse;
        text-align: left;
    }
    .order-summary-table th {
        padding: 12px 10px;
        border-bottom: 2px solid #f0f0f0;
        color: #aaa;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 700;
    }
    .order-summary-table td {
        padding: 16px 10px;
        border-bottom: 1px solid #f8f8f8;
        font-size: 14px;
        color: #444;
        vertical-align: middle;
    }
    .tax-row td { color: #888; font-size: 12px; }
    .total-row td { font-size: 18px !important; font-weight: 700 !important; padding-top: 18px !important; }
    .conf-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 45px;
        flex-wrap: wrap;
    }
    .btn-conf {
        padding: 13px 28px;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 14px;
        min-width: 170px;
        height: 50px;
        font-family: 'Outfit', sans-serif;
        cursor: pointer;
        border: none;
    }
    .btn-primary-conf {
        background: var(--pink, #A91B43);
        color: #fff;
    }
    .btn-primary-conf:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(148, 4, 55, 0.25);
        color: #fff;
        background: #8c0135;
    }
    .btn-secondary-conf {
        background: #fff;
        color: #333;
        border: 1.5px solid #e0e0e0;
    }
    .btn-secondary-conf:hover {
        background: #fafafa;
        border-color: #ccc;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.06);
        color: #333;
    }

    /* Confetti particles */
    .confetti-wrap {
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 100px;
        overflow: hidden;
        pointer-events: none;
    }
    .confetti-particle {
        position: absolute;
        width: 8px;
        height: 8px;
        border-radius: 2px;
        top: -10px;
        animation: confettiFall 2s ease-in forwards;
    }
    @keyframes confettiFall {
        0%   { top: -10px; opacity: 1; transform: rotate(0deg) translateX(0); }
        100% { top: 110px; opacity: 0; transform: rotate(720deg) translateX(var(--x)); }
    }
    @media (max-width: 768px) {
        .success-card { padding: 30px 20px; }
        .confirmation-details-grid { grid-template-columns: 1fr; }
        .order-status-title { font-size: 24px; }
    }
</style>
@endpush

@section('content')
<main class="confirmation-page">
    <div class="page-shell">
        <div class="success-card" id="successCard">

            <!-- Confetti -->
            <div class="confetti-wrap" id="confettiWrap"></div>

            <!-- Animated Check Icon -->
            <div class="success-icon-wrap">
                <svg viewBox="0 0 52 52" width="90" height="90">
                    <circle class="checkmark-bg" cx="26" cy="26" r="25"/>
                    <circle class="checkmark-circle" cx="26" cy="26" r="25"/>
                    <path class="checkmark" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>

            <h1 class="order-status-title">🎉 Order Placed Successfully!</h1>
            <p class="order-status-text">
                Thank you for shopping with Nandhini Silks.<br>
                Your order has been confirmed and will be delivered soon.
            </p>

            <!-- Auto-redirect countdown -->
            <div class="redirect-bar-wrap"><div class="redirect-bar" id="redirectBar"></div></div>
            <p class="redirect-text" id="redirectText">Redirecting to My Orders in <strong id="countdown">8</strong>s…</p>

            <!-- Order Details Grid -->
            <div class="confirmation-details-grid">
                <div class="detail-box">
                    <div class="detail-label">Order Number</div>
                    <div class="detail-value" style="color: #A91B43; font-size: 16px;">
                        {{ $order ? '#' . $order->order_number : 'N/A' }}
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Estimated Delivery</div>
                    <div class="detail-value">
                        {{ $order->edd ?? (now()->addDays(5)->format('d M') . ' – ' . now()->addDays(7)->format('d M, Y')) }}
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Delivery Address</div>
                    <div class="detail-value" style="font-weight: 400; font-size: 13px;">
                        @if($order)
                            <strong>{{ $order->customer_name }}</strong><br>
                            {!! nl2br(e($order->delivery_address)) !!}
                        @else
                            —
                        @endif
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">
                        @if($order && $order->payment_method == 'razorpay')
                            <span style="display:flex;align-items:center;gap:6px;">
                                <img src="https://razorpay.com/favicon.png" width="14" alt=""> Online (Razorpay)
                            </span>
                        @else
                            <span>💵 Cash on Delivery</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary Table -->
            <h3 style="text-align: left; margin-top: 35px; font-size: 16px; font-weight: 700; color: #333;">Order Summary</h3>
            <table class="order-summary-table">
                <thead>
                    <tr>
                        <th style="padding-left: 0;">Product</th>
                        <th style="text-align: right;">Qty</th>
                        <th style="text-align: right;">Tax</th>
                        <th style="text-align: right; padding-right: 0;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order)
                        @foreach($order->items as $item)
                        <tr>
                            <td style="padding-left: 0;">
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <img src="{{ $item->product && $item->product->image_path ? asset('images/' . $item->product->image_path) : asset('images/product_detail.png') }}"
                                         width="45" height="55" style="object-fit: cover; border-radius: 8px; flex-shrink: 0; border: 1px solid #eee;">
                                    <div>
                                        <div style="font-weight: 700; font-size: 14px; color: #1a1a1a;">{{ $item->product_name }}</div>
                                        @if($item->size || $item->color)
                                            <div style="font-size: 11px; color: #94a3b8; margin-top: 2px; font-weight: 600;">
                                                {{ $item->color ? 'Color: '.$item->color : '' }}
                                                {{ ($item->color && $item->size) ? ' · ' : '' }}
                                                {{ $item->size ? 'Size: '.$item->size : '' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right; font-weight: 600;">{{ $item->quantity }}</td>
                            <td style="text-align: right;">
                                <div style="font-weight: 700; color: #444;">&#8377;{{ number_format($item->tax_amount ?? 0, 2) }}</div>
                                <div style="font-size: 10px; color: #94a3b8; font-weight: 600;">({{ $item->tax_rate ?? 0 }}%)</div>
                            </td>
                            <td style="text-align: right; font-weight: 800; color: #1a1a1a; padding-right: 0;">&#8377;{{ number_format($item->total, 0) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" style="text-align: right; border: none; padding-top: 20px; color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Subtotal</td>
                            <td style="text-align: right; border: none; padding-top: 20px; font-weight: 700; color: #333; padding-right: 0;">&#8377;{{ number_format($order->sub_total, 0) }}</td>
                        </tr>
                        <tr class="tax-row">
                            <td colspan="3" style="text-align: right; border: none; color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Tax (GST)</td>
                            <td style="text-align: right; border: none; font-weight: 700; color: #333; padding-right: 0;">&#8377;{{ number_format($order->tax, 2) }}</td>
                        </tr>
                        <tr class="tax-row">
                            <td colspan="3" style="text-align: right; border: none; color: #888; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Shipping</td>
                            <td style="text-align: right; border: none; font-weight: 700; color: {{ $order->shipping > 0 ? '#333' : '#2e7d32' }}; padding-right: 0;">
                                {{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 0) : 'FREE' }}
                            </td>
                        </tr>
                        <tr class="tax-row">
                            <td colspan="3" style="text-align: right; border: none; color: #2e7d32; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Discount</td>
                            <td style="text-align: right; border: none; font-weight: 700; color: #2e7d32; padding-right: 0;">-&#8377;{{ number_format($order->discount, 0) }}</td>
                        </tr>
                        <tr class="total-row">
                            <td colspan="3" style="text-align: right; border: none; color: #333; font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px;">Total Paid</td>
                            <td style="text-align: right; border: none; color: #A91B43; font-size: 24px; padding-right: 0;">&#8377;{{ number_format($order->grand_total, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <!-- Actions -->
            <div class="conf-actions">
                <a href="{{ route('home') }}" class="btn-conf btn-secondary-conf">
                    🛍 Continue Shopping
                </a>
                <button onclick="handleDownload()" class="btn-conf btn-secondary-conf">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Invoice
                </button>
                <a href="{{ route('my-orders') }}" class="btn-conf btn-primary-conf">
                    📦 My Orders
                </a>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/invoice.js') }}?v=1.1"></script>
<script>
    // --- Confetti ---
    (function() {
        const colors = ['#A91B43','#e07b63','#f0c040','#6ad48a','#60b0f0','#c07de0'];
        const wrap = document.getElementById('confettiWrap');
        if (!wrap) return;
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('div');
            el.className = 'confetti-particle';
            el.style.left = Math.random() * 100 + '%';
            el.style.background = colors[Math.floor(Math.random() * colors.length)];
            el.style.setProperty('--x', (Math.random() * 80 - 40) + 'px');
            el.style.animationDelay = (Math.random() * 1.2) + 's';
            el.style.animationDuration = (1.5 + Math.random() * 1) + 's';
            el.style.width = (6 + Math.random() * 6) + 'px';
            el.style.height = (6 + Math.random() * 6) + 'px';
            el.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
            wrap.appendChild(el);
        }
    })();

    // --- Auto-redirect countdown ---
    let secs = 8;
    const countdownEl = document.getElementById('countdown');
    const timer = setInterval(() => {
        secs--;
        if (countdownEl) countdownEl.textContent = secs;
        if (secs <= 0) {
            clearInterval(timer);
            window.location.href = '{{ route("my-orders") }}';
        }
    }, 1000);

    // Cancel redirect if user hovers over card
    document.getElementById('successCard').addEventListener('mouseenter', () => {
        clearInterval(timer);
        const bar = document.getElementById('redirectBar');
        const text = document.getElementById('redirectText');
        if (bar) bar.style.display = 'none';
        if (text) text.style.display = 'none';
    });

    // --- Invoice Download ---
    function handleDownload() {
        @if($order)
        const orderData = {
            orderNumber: "{{ $order ? $order->order_number : '' }}",
            date: "{{ $order ? $order->created_at->format('d/m/Y') : date('d/m/Y') }}",
            customer: {
                name: "{{ $order ? $order->customer_name : '' }}",
                address: "{{ $order ? str_replace(["\r", "\n"], ' ', $order->delivery_address) : '' }}",
                phone: "{{ $order ? $order->customer_phone : '' }}"
            },
            items: [
                @if($order)
                    @foreach($order->items as $item)
                    {
                        name: "{{ $item->product_name }}",
                        image: "{{ $item->product && $item->product->image_path ? asset('images/' . $item->product->image_path) : asset('images/product_detail.png') }}",
                        variant: "{{ ($item->color ? $item->color : '') . ($item->size ? ' / '.$item->size : '') ?: '-' }}",
                        hsn: "5007",
                        qty: {{ $item->quantity }},
                        rate: {{ $item->price }},
                        taxRate: {{ $item->tax_rate ?? 0 }},
                        taxAmount: {{ $item->tax_amount ?? 0 }}
                    },
                    @endforeach
                @endif
            ],
            paymentMethod: "{{ $order ? ($order->payment_method == 'razorpay' ? 'Online (Razorpay)' : 'Cash on Delivery') : '' }}",
            subtotal: {{ $order ? $order->sub_total : 0 }},
            taxAmount: {{ $order ? $order->tax : 0 }},
            shipping: {{ $order ? $order->shipping : 0 }},
            discount: {{ $order ? $order->discount : 0 }},
            couponCode: "{{ $order ? $order->coupon_code : '' }}",
            total: {{ $order ? $order->grand_total : 0 }}
        };
        if (typeof InvoiceGenerator !== 'undefined') {
            InvoiceGenerator.download(orderData);
        } else {
            alert('Invoice generator still loading. Please try again in a moment.');
        }

        @else
        alert('Order data not found.');
        @endif
    }
</script>
@endpush
