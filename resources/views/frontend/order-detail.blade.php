@extends('frontend.layouts.app')

@section('title', 'Order Details #NS7842 | Nandhini Silks')

@push('styles')
<style>
    .order-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 20px;
    }

    .order-id-badge {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }

    .order-actions-top {
        display: flex;
        gap: 15px;
    }

    .timeline-card {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        margin-bottom: 30px;
        border: 1px solid #f0f0f0;
    }

    .timeline {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 20px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        top: 15px;
        left: 5%;
        right: 5%;
        height: 2px;
        background: #eee;
        z-index: 1;
    }

    .timeline-step {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }

    .step-icon {
        width: 32px;
        height: 32px;
        background: #fff;
        border: 2px solid #eee;
        border-radius: 50%;
        display: grid;
        place-items: center;
        margin: 0 auto 10px;
        font-size: 14px;
        color: #999;
        transition: all 0.3s ease;
    }

    .timeline-step.active .step-icon {
        background: var(--pink);
        border-color: var(--pink);
        color: #fff;
    }

    .timeline-step.completed .step-icon {
        background: #52c41a;
        border-color: #52c41a;
        color: #fff;
    }

    .step-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .step-date {
        font-size: 11px;
        color: #999;
        display: block;
    }

    .order-info-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .info-section {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        border: 1px solid #f0f0f0;
        margin-bottom: 30px;
    }

    .info-title {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
        border-bottom: 1px solid #f5f5f5;
        padding-bottom: 15px;
    }

    .order-items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .order-items-table th {
        text-align: left;
        font-size: 12px;
        color: #999;
        text-transform: uppercase;
        padding: 10px 0;
        border-bottom: 1px solid #f5f5f5;
    }

    .order-items-table td {
        padding: 20px 0;
        border-bottom: 1px solid #f9f9f9;
        vertical-align: middle;
    }

    .item-cell {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .item-img {
        width: 60px;
        height: 60px;
        border-radius: 6px;
        object-fit: cover;
    }

    .item-name {
        font-weight: 600;
        font-size: 14px;
        color: #333;
    }

    .item-meta {
        font-size: 12px;
        color: #999;
    }

    .item-actions-cell {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .action-link {
        font-size: 12px;
        color: var(--pink);
        text-decoration: none;
        font-weight: 600;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #666;
    }

    .summary-row.total {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 2px solid #f5f5f5;
        font-size: 18px;
        font-weight: 700;
        color: #333;
    }

    .address-card p {
        margin-bottom: 5px;
        font-size: 14px;
        color: #666;
    }

    .tracking-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
        margin-top: 10px;
    }

    .courier-link {
        color: var(--pink);
        font-weight: 600;
        text-decoration: underline;
    }

    .account-nav-link {
        padding: 10px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .status-badge {
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-delivered {
        background: #e6f7ff;
        color: #1890ff;
    }

    @media (max-width: 900px) {
        .order-info-grid {
            grid-template-columns: 1fr;
        }

        .timeline::before {
            display: none;
        }

        .timeline {
            flex-direction: column;
            gap: 20px;
            text-align: left;
        }

        .timeline-step {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .step-icon {
            margin: 0;
        }
    }
</style>
@endpush

@section('content')
<main class="account-page">
    <div class="page-shell">
        <div class="breadcrumb">
            <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
            <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp;
            <a href="{{ url('my-orders') }}">My Orders</a> &nbsp; / &nbsp;
            <span>Order Details</span>
        </div>

        <div class="order-detail-header">
            <div>
                <h1 class="order-id-badge">Order #NS{{ $order->id }}</h1>
                <p style="color: #999; margin-top: 5px;">Placed on {{ $order->created_at->format('M d, Y') }} &middot; {{ $order->created_at->format('h:i A') }}</p>
            </div>
            <div class="order-actions-top">
                <button onclick="handleDownload({{ json_encode([
                    'orderNumber' => 'NS' . $order->id,
                    'date' => $order->created_at->format('M d, Y'),
                    'customer' => [
                        'name' => $order->billing_name ?: $order->customer_name,
                        'address' => str_replace(["\r", "\n"], ', ', $order->delivery_address),
                        'phone' => $order->billing_phone ?: $order->customer_phone
                    ],
                    'items' => $order->items->map(function($item) {
                        return [
                            'name' => $item->product_name,
                            'image' => $item->getImageUrl(),
                            'variant' => ($item->color ? 'Color: '.$item->color : '') . ($item->size ? ' | Size: '.$item->size : ''),
                            'hsn' => "5007",
                            'qty' => $item->quantity,
                            'rate' => (float)$item->price,
                            'taxRate' => 12
                        ];
                    })->toArray(),
                    'paymentMethod' => str_replace('_', ' ', strtoupper($order->payment_method)),
                    'subtotal' => (float)$order->sub_total,
                    'taxAmount' => (float)$order->tax,
                    'shipping' => (float)$order->shipping,
                    'total' => (float)$order->grand_total
                ]) }})" class="account-nav-link"
                    style="background: #fff; border: 1px solid #ddd; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                    Download Invoice
                </button>
                <a href="{{ route('shop') }}" class="account-nav-link"
                    style="background: var(--pink); color: #fff; border: none; cursor: pointer; text-decoration: none; display: inline-block;">
                    Buy More
                </a>
            </div>
        </div>

        <div class="timeline-card">
            <h3 class="info-title">Order Status</h3>
            <div class="timeline">
                <div class="timeline-step completed">
                    <div class="step-icon">&#10003;</div>
                    <span class="step-label">Order Placed</span>
                    <span class="step-date">{{ $order->created_at->format('M d') }}</span>
                </div>
                <div class="timeline-step {{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'completed' : ($order->order_status == 'pending' ? 'active' : '') }}">
                    <div class="step-icon">{{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? '✓' : '●' }}</div>
                    <span class="step-label">Confirmed</span>
                    <span class="step-date">{{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'Done' : 'Pending' }}</span>
                </div>
                <div class="timeline-step {{ in_array($order->order_status, ['dispatched', 'delivered']) ? 'completed' : ($order->order_status == 'processing' ? 'active' : '') }}">
                    <div class="step-icon">{{ in_array($order->order_status, ['dispatched', 'delivered']) ? '✓' : '●' }}</div>
                    <span class="step-label">Shipped</span>
                    <span class="step-date">{{ $order->order_status == 'dispatched' || $order->order_status == 'delivered' ? 'Done' : 'Processing' }}</span>
                </div>
                <div class="timeline-step {{ $order->order_status == 'delivered' ? 'completed' : '' }}">
                    <div class="step-icon">{{ $order->order_status == 'delivered' ? '✓' : '○' }}</div>
                    <span class="step-label">Delivered</span>
                    <span class="step-date">{{ $order->order_status == 'delivered' ? 'Completed' : 'Expected' }}</span>
                </div>
            </div>

            @if($order->tracking_number)
            <div class="tracking-info">
                <p style="font-size: 14px;"><strong>Tracking ID:</strong> {{ $order->tracking_number }} <span
                        style="margin: 0 10px; color: #ccc;">|</span> <strong>Courier:</strong> {{ $order->courier_name ?? 'Standard' }} <a href="#"
                        class="courier-link" style="margin-left: 10px;">Track on Website</a></p>
            </div>
            @endif
        </div>

        <div class="order-info-grid">
            <div class="grid-left">
                <div class="info-section">
                    <h3 class="info-title">Order Items</h3>
                    <table class="order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="item-cell">
                                        <img src="{{ $item->getImageUrl() }}" alt="" class="item-img">
                                        <div>
                                            <div class="item-name">{{ $item->product_name }}</div>
                                            <div class="item-meta">
                                                @if($item->color) Color: {{ $item->color }} | @endif 
                                                @if($item->size) Size: {{ $item->size }} @endif
                                                @if(!$item->color && !$item->size) Regular Type @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>&#8377;{{ number_format($item->price, 0) }}</td>
                                <td class="item-qty">{{ $item->quantity }}</td>
                                <td>&#8377;{{ number_format($item->price * $item->quantity, 0) }}</td>
                                <td class="item-actions-cell">
                                    <a href="#" class="action-link">Write Review</a>
                                    <a href="#" class="action-link" style="color: #999;">Need Help?</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid-right">
                <div class="info-section">
                    <h3 class="info-title">Order Summary</h3>
                    <div class="summary-details">
                        <div class="summary-row subtotal-row">
                            <span>Subtotal</span>
                            <span class="subtotal-val">&#8377;{{ number_format($order->sub_total, 0) }}</span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span style="color: #52c41a;">{{ $order->shipping > 0 ? '₹'.number_format($order->shipping, 0) : 'FREE' }}</span>
                        </div>
                        <div class="summary-row tax-row">
                            <span>Tax (GST)</span>
                            <span class="tax-val">&#8377;{{ number_format($order->tax, 0) }}</span>
                        </div>
                        @if($order->discount > 0)
                        <div class="summary-row">
                            <span>Discount</span>
                            <span style="color: #e74c3c;">-&#8377;{{ number_format($order->discount, 0) }}</span>
                        </div>
                        @endif
                        <div class="summary-row total">
                            <span>Total</span>
                            <span class="total-val">&#8377;{{ number_format($order->grand_total, 0) }}</span>
                        </div>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">Delivery Address</h3>
                    <div class="address-card">
                        <p><strong class="cust-name">{{ $order->billing_name ?: $order->customer_name }}</strong></p>
                        <div class="addr-lines" style="font-size: 14px; color: #666;">
                            {!! nl2br(e($order->delivery_address)) !!}
                        </div>
                        <p class="phone-line" style="margin-top: 10px;">Phone: {{ $order->billing_phone ?: $order->customer_phone }}</p>
                    </div>
                </div>

                <div class="info-section">
                    <h3 class="info-title">Payment Method</h3>
                    <div class="payment-info-card">
                        <p class="pay-method" style="font-size: 14px; font-weight: 600; text-transform: uppercase;">
                            {{ str_replace('_', ' ', $order->payment_method) }}
                        </p>
                        <p style="font-size: 12px; color: #999;">Status: {{ ucfirst($order->payment_status) }}</p>
                        <span class="status-badge {{ $order->payment_status == 'paid' ? 'status-delivered' : '' }}" style="display: inline-block; margin-top: 10px;">
                            {{ $order->payment_status == 'paid' ? 'Payment Successful' : 'Payment '.ucfirst($order->payment_status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="{{ asset('js/invoice.js') }}"></script>
<script>
    function handleDownload(orderData) {
        if (typeof InvoiceGenerator !== 'undefined') {
            InvoiceGenerator.download(orderData);
        } else {
            console.error('InvoiceGenerator not found. Please check if invoice.js is loaded.');
            alert('Invoice generator is still loading. Please try again.');
        }
    }
</script>
@endpush
