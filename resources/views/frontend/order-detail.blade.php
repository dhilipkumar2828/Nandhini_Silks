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
            width: 100% !important;
            border-collapse: collapse !important;
            table-layout: auto;
        }

        .order-items-table tr {
            border-bottom: 1px solid #e5e7eb;
            /* Joint line across the whole row */
        }

        .order-items-table th,
        .order-items-table td {
            padding: 22px 15px;
            vertical-align: middle;
            text-align: center !important;
        }

        /* Product column specific override */
        .order-items-table th:first-child,
        .order-items-table td:first-child {
            text-align: left !important;
            width: 45%;
        }

        .order-items-table th {
            font-size: 11px;
            color: #888;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #fafafa;
            border-top: 1px solid #f1f1f1;
        }

        .item-cell {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .item-img {
            width: 76px;
            height: 76px;
            border-radius: 10px;
            object-fit: cover;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border: 1px solid #f1f1f1;
        }

        .item-name {
            font-weight: 700;
            font-size: 14px;
            color: #1a202c;
            word-break: break-word;
            line-height: 1.5;
            max-width: 70%;
            margin-bottom: 6px;
        }

        .item-meta {
            font-size: 11px;
            color: #718096;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 6px;
        }

        .item-meta span {
            background: #f7fafc;
            border: 1px solid #edf2f7;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
            font-weight: 600;
            color: #4a5568;
        }

        .item-actions-cell {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
        }

        .action-link {
            font-size: 12px;
            color: var(--pink);
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-link:hover {
            opacity: 0.8;
            transform: translateY(-1px);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(4px);
            z-index: 1000;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .modal-content {
            background: #fff;
            width: 100%;
            max-width: 450px;
            border-radius: 20px;
            overflow: hidden;
            animation: modalSlide 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes modalSlide {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #f5f5f5;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-body {
            padding: 24px;
        }

        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .rating-stars input {
            display: none;
        }

        .rating-stars label {
            cursor: pointer;
            font-size: 32px;
            color: #e5e7eb;
            transition: all 0.2s ease;
        }

        .rating-stars label:hover,
        .rating-stars label:hover~label,
        .rating-stars input:checked~label {
            color: #fbbf24;
            transform: scale(1.1);
        }

        .review-textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s ease;
            resize: none;
        }

        .review-textarea:focus {
            border-color: var(--pink);
            box-shadow: 0 0 0 4px rgba(169, 27, 67, 0.05);
        }

        /* Modal Styling Utilities */
        .modal-product-thumbnail {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            object-fit: cover;
            background: #f8fafc;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .modal-title {
            font-size: 16px;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .modal-subtitle {
            font-size: 10px;
            color: #94a3b8;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 0;
        }

        .modal-product-name {
            font-size: 14px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 2px;
        }

        .submit-btn {
            width: 100%;
            background: #a91b43;
            color: #fff;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(169, 27, 67, 0.2);
        }

        .submit-btn:hover {
            background: #940437;
            transform: translateY(-1px);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .stars-label {
            font-size: 12px;
            font-weight: 700;
            color: #475569;
            display: block;
            margin-bottom: 8px;
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
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            line-height: 1;
            white-space: nowrap;
        }

        .status-pending {
            background: #fff7e6;
            color: #d46b08;
        }

        .status-processing {
            background: #fffbe6;
            color: #d48806;
        }

        .status-dispatched,
        .status-shipped {
            background: #e6f4ff;
            color: #1677ff;
        }

        .status-delivered {
            background: #f6ffed;
            color: #389e0d;
        }

        .status-failed,
        .status-cancelled {
            background: #fff1f0;
            color: #cf1322;
        }

        .status-refunded {
            background: #f9f0ff;
            color: #722ed1;
        }

        .payment-status-note {
            margin-top: 8px;
            font-size: 12px;
            color: #999;
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

        @media (max-width: 768px) {
            .timeline-card {
                padding: 20px 18px;
            }

            .timeline {
                gap: 14px;
                margin-top: 12px;
            }

            .timeline-step {
                align-items: center;
                gap: 12px;
                padding: 10px 12px;
                border: 1px solid #f1e7ea;
                border-radius: 12px;
                background: #fffafc;
            }

            .step-icon {
                width: 30px;
                height: 30px;
                flex-shrink: 0;
            }

            .step-label {
                display: block;
                font-size: 13px;
                line-height: 1.3;
            }

            .step-date {
                margin-top: 2px;
                font-size: 11px;
                line-height: 1.3;
            }

            .info-section {
                padding: 18px;
            }

            .order-items-table,
            .order-items-table thead,
            .order-items-table tbody,
            .order-items-table tr,
            .order-items-table th,
            .order-items-table td {
                display: block;
                width: 100%;
            }

            .order-items-table thead {
                display: none;
            }

            .order-items-table tbody {
                display: flex;
                flex-direction: column;
                gap: 14px;
            }

            .order-items-table tr {
                padding: 14px;
                border: 1px solid #f1e7ea;
                border-radius: 14px;
                background: #fff;
            }

            .order-items-table td {
                padding: 0;
                border-bottom: none;
            }

            .order-items-table td+td {
                margin-top: 10px;
            }

            .item-cell {
                align-items: flex-start;
                gap: 12px;
            }

            .item-img {
                width: 56px;
                height: 56px;
                flex-shrink: 0;
            }

            .item-name {
                margin-bottom: 4px;
                line-height: 1.35;
            }

            .item-meta {
                line-height: 1.5;
            }

            .order-items-table td:not(:first-child) {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 16px;
                font-size: 14px;
            }

            .order-items-table td:not(:first-child)::before {
                content: attr(data-label);
                color: #8f8f8f;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                flex-shrink: 0;
            }

            .item-actions-cell {
                align-items: flex-start;
                gap: 6px;
            }

            .item-actions-cell::before {
                align-self: flex-start;
            }

            .action-link {
                font-size: 13px;
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
                <h1 class="order-id-badge">Order #{{ $order->order_number }}</h1>
                <p style="color: #999; margin-top: 5px;">Placed on {{ $order->created_at->format('M d, Y') }} &middot; {{ $order->created_at->format('h:i A') }}</p>
                @if($order->edd)
                <p style="color: #27ae60; font-weight: 600; margin-top: 5px;"><i class="fas fa-truck"></i> Expected Delivery: {{ $order->edd }}</p>
                @endif
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

                @if($order->order_status === 'delivered')
                    @if(!$order->return_status)
                        <button onclick="openReturnModal()" class="account-nav-link"
                            style="background: #fff; border: 1px solid #ff4d4f; color: #ff4d4f; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-undo"></i> Return items
                        </button>
                    @else
                        @php $badge = $order->return_status_badge; @endphp
                        <div class="status-badge {{ $badge['class'] }}" style="padding: 10px 20px; border-radius: 50px;">
                            <i class="fas fa-shipping-fast mr-2"></i> {{ $badge['label'] }}
                        </div>
                        @if($order->reverse_awb)
                            <div class="tracking-info" style="margin: 0; padding: 10px 15px;">
                                <span style="font-size: 11px; color: #888; font-weight: 700;">REVERSE AWB:</span> 
                                <span style="font-weight: 700; color: #333;">{{ $order->reverse_awb }}</span>
                            </div>
                        @endif
                    @endif
                @endif
            </div>
        </div>

            <div class="order-detail-header">
                <div>
                    <h1 class="order-id-badge">Order #{{ $order->order_number }}</h1>
                    <p style="color: #999; margin-top: 5px;">Placed on {{ $order->created_at->format('M d, Y') }} &middot;
                        {{ $order->created_at->format('h:i A') }}
                    </p>
                </div>
                <div class="timeline-step {{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'completed' : (in_array($order->order_status, ['pending', 'order placed']) ? 'active' : '') }}">
                    <div class="step-icon">{{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? '✓' : '●' }}</div>
                    <span class="step-label">Processing</span>
                    <span class="step-date">{{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'Done' : 'Order Placed' }}</span>
                </div>
                <div class="timeline-step {{ in_array($order->order_status, ['dispatched', 'delivered']) ? 'completed' : ($order->order_status == 'processing' ? 'active' : '') }}">
                    <div class="step-icon">{{ in_array($order->order_status, ['dispatched', 'delivered']) ? '✓' : '●' }}</div>
                    <span class="step-label">Dispatched</span>
                    <span class="step-date">{{ $order->order_status == 'dispatched' || $order->order_status == 'delivered' ? 'Done' : 'Processing' }}</span>
                </div>
                <div class="timeline-step {{ $order->order_status == 'delivered' ? 'completed' : '' }}">
                    <div class="step-icon">{{ $order->order_status == 'delivered' ? '✓' : '○' }}</div>
                    <span class="step-label">Delivered</span>
                    <span class="step-date">{{ $order->order_status == 'delivered' ? 'Completed' : ($order->edd ?? 'Expected') }}</span>
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
                    <div
                        class="timeline-step {{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'completed' : ($order->order_status == 'pending' ? 'active' : '') }}">
                        <div class="step-icon">
                            {{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? '✓' : '●' }}
                        </div>
                        <span class="step-label">Processing</span>
                        <span
                            class="step-date">{{ in_array($order->order_status, ['processing', 'dispatched', 'delivered']) ? 'Done' : 'Pending' }}</span>
                    </div>
                    <div
                        class="timeline-step {{ in_array($order->order_status, ['dispatched', 'delivered']) ? 'completed' : ($order->order_status == 'processing' ? 'active' : '') }}">
                        <div class="step-icon">{{ in_array($order->order_status, ['dispatched', 'delivered']) ? '✓' : '●' }}
                        </div>
                        <span class="step-label">Dispatched</span>
                        <span
                            class="step-date">{{ $order->order_status == 'dispatched' || $order->order_status == 'delivered' ? 'Done' : 'Processing' }}</span>
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
                                style="margin: 0 10px; color: #ccc;">|</span> <strong>Courier:</strong>
                            {{ $order->courier_name ?? 'Standard' }} <a href="#" class="courier-link"
                                style="margin-left: 10px;">Track on Website</a></p>
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
                                        <td data-label="Product">
                                            <div class="item-cell">
                                                <img src="{{ $item->getImageUrl() }}" alt="" class="item-img">
                                                <div style="flex: 1; min-width: 100%;">
                                                    <div class="item-name">{{ $item->product_name }}</div>
                                                    <div class="item-meta">
                                                        @if(!empty($item->attributes))
                                                            @foreach($item->attributes as $attr)
                                                                <span>{{ $attr['name'] }}: {{ $attr['value'] }}</span>
                                                            @endforeach
                                                        @else
                                                            @if($item->color || $item->size)
                                                                @if($item->color) <span>Color: {{ $item->color }}</span> @endif
                                                                @if($item->size) <span>Size: {{ $item->size }}</span> @endif
                                                            @else
                                                                <span
                                                                    style="background: none; color: #94a3b8; font-style: italic; padding: 0;">Standard
                                                                    Unit</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-label="Price">&#8377;{{ number_format($item->price, 0) }}</td>
                                        <td class="item-qty" data-label="Qty">{{ $item->quantity }}</td>
                                        <td data-label="Subtotal">&#8377;{{ number_format($item->price * $item->quantity, 0) }}
                                        </td>
                                        <td class="item-actions-cell" data-label="Actions" style="margin-top: 25px;">
                                            <a href="javascript:void(0)" class="action-link"
                                                onclick="openReviewModal('{{ $item->product_id }}', '{{ e($item->product_name) }}', '{{ $item->getImageUrl() }}')">Write
                                                Review</a>
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
                                <span class="subtotal-val">&#8377;{{ number_format($order->sub_total, 2) }}</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping</span>
                                <span
                                    style="color: #52c41a;">{{ $order->shipping > 0 ? '₹' . number_format($order->shipping, 2) : 'FREE' }}</span>
                            </div>
                            <div class="summary-row tax-row">
                                <span>Tax (GST)</span>
                                <span class="tax-val">&#8377;{{ number_format($order->tax, 2) }}</span>
                            </div>
                            @if($order->discount > 0)
                                <div class="summary-row">
                                    <span>Discount</span>
                                    <span style="color: #e74c3c;">-&#8377;{{ number_format($order->discount, 2) }}</span>
                                </div>
                            @endif
                            <div class="summary-row total">
                                <span>Total</span>
                                <span class="total-val">&#8377;{{ number_format($order->grand_total, 2) }}</span>
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
                            <p class="phone-line" style="margin-top: 10px;">Phone:
                                {{ $order->billing_phone ?: $order->customer_phone }}
                            </p>
                        </div>
                    </div>

                    <div class="info-section">
                        <h3 class="info-title">Payment Method</h3>
                        @php
                            $paymentStatus = strtolower(trim((string) $order->payment_status));
                            $paymentStatusClass = match ($paymentStatus) {
                                'paid' => 'status-delivered',
                                'failed' => 'status-failed',
                                'refunded' => 'status-refunded',
                                'processing' => 'status-processing',
                                'dispatched' => 'status-dispatched',
                                default => 'status-pending',
                            };
                        @endphp
                        <div class="payment-info-card">
                            <p class="pay-method" style="font-size: 14px; font-weight: 600; text-transform: uppercase;">
                                {{ str_replace('_', ' ', $order->payment_method) }}
                            </p>
                            <p class="payment-status-note">Status: {{ ucfirst($paymentStatus) }}</p>
                            <span class="status-badge {{ $paymentStatusClass }}" style="margin-top: 10px;">
                                {{ $paymentStatus == 'paid' ? 'Payment Successful' : 'Payment ' . ucfirst($paymentStatus) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Review Modal -->
<div id="reviewModal" class="modal-overlay">
    <div class="modal-content" style="box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div class="modal-header">
            <h3 class="modal-title">Write a Review</h3>
            <button onclick="closeReviewModal()" class="text-slate-400"
                style="background:none; border:none; cursor:pointer;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="reviewForm" method="POST">
            @csrf
            <div class="modal-body">
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px;">
                    <img id="modalProductImg" src="" class="modal-product-thumbnail">
                    <div>
                        <h4 id="modalProductName" class="modal-product-name">Product Name</h4>
                        <p class="modal-subtitle">Share your experience</p>
                    </div>
                </div>

                <div class="rating-stars">
                    <input type="radio" id="star5" name="stars" value="5" required />
                    <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star4" name="stars" value="4" />
                    <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star3" name="stars" value="3" />
                    <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star2" name="stars" value="2" />
                    <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                    <input type="radio" id="star1" name="stars" value="1" />
                    <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                </div>

                <div style="margin-top: 10px;">
                    <label class="stars-label">Your Feedback</label>
                    <textarea name="review" class="review-textarea" rows="4"
                        placeholder="How was the product quality and delivery? (Min. 10 characters)" required
                        minlength="10"></textarea>
                </div>

                <button type="submit" class="submit-btn" style="margin-top: 24px;">
                    Submit Review
                </button>
            </div>
        </form>
<!-- Return Modal -->
<div id="returnModal" class="modal-overlay" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
    <div class="modal-content" style="background:#fff; width: 450px; border-radius: 20px; padding: 30px; position: relative; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <button onclick="closeReturnModal()" style="position: absolute; top: 20px; right: 20px; background: none; border: none; cursor: pointer; color: #94a3b8; font-size: 20px;"><i class="fas fa-times"></i></button>
        
        <div style="margin-bottom: 24px; text-align: center;">
            <div style="width: 60px; height: 60px; background: #fff1f0; border-radius: 50%; display: grid; place-items: center; margin: 0 auto 15px;">
                <i class="fas fa-undo" style="font-size: 24px; color: #ff4d4f;"></i>
            </div>
            <h2 class="modal-title">Request a Return</h2>
            <p class="modal-subtitle" style="font-size: 13px !important; color: #64748b !important; text-transform: none !important; letter-spacing: normal !important; margin-top: 4px !important;">Tell us why you want to return this order</p>
        </div>

        <form id="returnForm" action="{{ route('order.return.request', $order->id) }}" method="POST">
            @csrf
            <div style="margin-top: 10px;">
                <label class="stars-label">Reason for Return</label>
                <textarea name="reason" rows="4" placeholder="Example: Wrong size delivered, Damaged product, etc." required style="width: 100%; border-radius: 12px; border: 1.5px solid #e2e8f0; padding: 15px; font-size: 14px; resize: none; outline: none; transition: border-color 0.2s;"></textarea>
            </div>

            <button type="submit" class="submit-btn" style="margin-top: 24px; background: #ff4d4f; box-shadow: 0 4px 6px -1px rgba(255, 77, 79, 0.2);">
                Submit Return Request
            </button>
        </form>
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

        function openReviewModal(productId, productName, productImg) {
            const modal = document.getElementById('reviewModal');
            const form = document.getElementById('reviewForm');

            document.getElementById('modalProductName').textContent = productName;
            document.getElementById('modalProductImg').src = productImg;

    function openReturnModal() {
        const modal = document.getElementById('returnModal');
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeReturnModal() {
        const modal = document.getElementById('returnModal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Close on outside click
    window.onclick = function(event) {
        const reviewModal = document.getElementById('reviewModal');
        const returnModal = document.getElementById('returnModal');
        if (event.target == reviewModal) {
            closeReviewModal();
        }
        if (event.target == returnModal) {
            closeReturnModal();
        }
    }
</script>
@endpush
