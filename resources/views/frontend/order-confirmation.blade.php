@extends('frontend.layouts.app')

@section('title', 'Order Confirmed | Nandhini Silks')

@push('styles')
<style>
    .confirmation-page {
      background: #fffcf0;
      padding: 60px 0;
      min-height: 80vh;
    }
    .success-card {
      max-width: 800px;
      margin: 0 auto;
      background: #fff;
      border-radius: 24px;
      padding: 50px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.05);
      text-align: center;
    }
    .success-icon {
      width: 80px;
      height: 80px;
      background: #e8f5e9;
      color: #2e7d32;
      border-radius: 50%;
      display: grid;
      place-items: center;
      margin: 0 auto 30px;
      font-size: 40px;
    }
    .order-status-title {
      font-size: 32px;
      color: #333;
      margin-bottom: 10px;
    }
    .order-status-text {
      color: #666;
      margin-bottom: 40px;
    }
    .confirmation-details-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 30px;
      text-align: left;
      margin-top: 40px;
      padding-top: 40px;
      border-top: 1px solid #eee;
    }
    .detail-box {
      background: #fafafa;
      padding: 20px;
      border-radius: 12px;
    }
    .detail-label {
      font-size: 12px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 8px;
      font-weight: 700;
    }
    .detail-value {
      font-weight: 600;
      color: #333;
    }
    .order-summary-table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
      text-align: left;
    }
    .order-summary-table th {
      padding: 12px 0;
      border-bottom: 2px solid #eee;
      color: #333;
      font-size: 14px;
    }
    .order-summary-table td {
      padding: 15px 0;
      border-bottom: 1px solid #f5f5f5;
      font-size: 14px;
    }
    .conf-actions {
      display: flex;
      gap: 15px;
      justify-content: center;
      margin-top: 50px;
      flex-wrap: wrap;
    }
    .btn-conf {
      padding: 12px 30px;
      border-radius: 50px;
      font-weight: 600;
      text-decoration: none;
      transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      font-size: 14px;
      min-width: 180px;
      height: 48px;
    }
    .btn-primary-conf {
      background: var(--pink);
      color: #fff;
      border: 1px solid var(--pink);
    }
    .btn-secondary-conf {
      background: #fafafa;
      color: #333;
      border: 1px solid #e0e0e0;
    }
    .btn-conf:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(148, 4, 55, 0.15);
      color: #fff;
      background: #a91b43;
      border-color: #a91b43;
    }
    .btn-secondary-conf:hover {
      background: #fff;
      color: var(--pink);
      border-color: var(--pink);
      box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<main class="confirmation-page">
    <div class="page-shell">
        <div class="success-card">
            <div class="success-icon">&#10003;</div>
            <h1 class="order-status-title">Thank You for Your Order!</h1>
            <p class="order-status-text">Your order has been placed successfully. A confirmation email has been sent to your registered address.</p>

            <div class="confirmation-details-grid">
                <div class="detail-box">
                    <div class="detail-label">Order Number</div>
                    <div class="detail-value">#NS-{{ date('Y') }}-{{ $order ? $order->id : 'XXXXX' }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Estimated Delivery</div>
                    <div class="detail-value">{{ now()->addDays(5)->format('F d, Y') }} - {{ now()->addDays(7)->format('F d, Y') }}</div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Delivery Address</div>
                    <div class="detail-value" style="font-weight: 400; line-height: 1.5;">
                        @if($order)
                            <strong>{{ $order->customer_name }}</strong><br>
                            {!! nl2br(e($order->delivery_address)) !!}
                        @else
                            <strong>Customer Name</strong><br>
                            Address Details
                        @endif
                    </div>
                </div>
                <div class="detail-box">
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value" style="display: flex; align-items: center; gap: 8px;">
                        @if($order && $order->payment_method == 'razorpay')
                            <img src="https://razorpay.com/favicon.png" width="16" alt=""> Razorpay (Secured)
                        @else
                            {{ strtoupper($order?->payment_method ?? 'COD') }}
                        @endif
                    </div>
                </div>
            </div>

            <h3 style="text-align: left; margin-top: 40px; font-size: 18px;">Order Summary</h3>
            <table class="order-summary-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th style="text-align: right;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @if($order)
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <img src="{{ $item->product && $item->product->image_path ? asset('images/' . $item->product->image_path) : asset('images/product_detail.png') }}" width="40" height="50" style="object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <div style="font-weight: 600;">{{ $item->product_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $item->quantity }}</td>
                            <td style="text-align: right;">₹{{ number_format($item->total, 0) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td colspan="2" style="text-align: right; border: none; padding-top: 25px;">Subtotal:</td>
                            <td style="text-align: right; border: none; padding-top: 25px; font-weight: 600;">₹{{ number_format($order->sub_total, 0) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td colspan="2" style="text-align: right; border: none;">Discount:</td>
                            <td style="text-align: right; border: none; color: #2e7d32; font-weight: 600;">-₹{{ number_format($order->discount, 0) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="2" style="text-align: right; border: none;">Shipping:</td>
                            <td style="text-align: right; border: none; color: #2e7d32; font-weight: 600;">{{ $order->shipping > 0 ? '₹'.number_format($order->shipping, 0) : 'FREE' }}</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align: right; border: none;">GST (5%):</td>
                            <td style="text-align: right; border: none; font-weight: 600;">₹{{ number_format($order->tax, 0) }}</td>
                        </tr>
                        <tr class="total-row" style="font-size: 20px; font-weight: 700;">
                            <td colspan="2" style="text-align: right; border: none; padding-top: 15px; color: #333;">Total Paid:</td>
                            <td class="total-paid" style="text-align: right; border: none; padding-top: 15px; color: var(--pink);">₹{{ number_format($order->grand_total, 0) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="conf-actions">
                <a href="{{ route('home') }}" class="btn-conf btn-secondary-conf">Continue Shopping</a>
                <button onclick="handleDownload()" class="btn-conf btn-primary-conf" style="cursor: pointer;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Invoice
                </button>
                <a href="{{ route('my-orders') }}" class="btn-conf btn-secondary-conf">Track My Order</a>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/invoice.js') }}"></script>
<script>
    function handleDownload() {
        @if($order)
        const orderData = {
            orderNumber: "{{ $order->id }}",
            date: "{{ $order->created_at->format('d/m/Y') }}",
            customer: {
                name: "{{ $order->customer_name }}",
                address: "{{ str_replace(["\r", "\n"], ' ', $order->delivery_address) }}",
                phone: "{{ $order->customer_phone }}"
            },
            items: [
                @foreach($order->items as $item)
                {
                    name: "{{ $item->product_name }}",
                    variant: "-",
                    hsn: "5007",
                    qty: {{ $item->quantity }},
                    rate: {{ $item->price }},
                    taxRate: 5
                },
                @endforeach
            ],
            paymentMethod: "{{ $order->payment_method }}",
            subtotal: {{ $order->sub_total }},
            taxAmount: {{ $order->tax }},
            shipping: {{ $order->shipping }},
            total: {{ $order->grand_total }}
        };

        if (typeof InvoiceGenerator !== 'undefined') {
            InvoiceGenerator.download(orderData);
        } else {
            alert('Invoice generator is still loading. Please try again.');
        }
        @else
        alert('Order data not found.');
        @endif
    }
</script>
@endpush
