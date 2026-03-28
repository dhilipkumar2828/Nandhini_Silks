@extends('frontend.layouts.app')

@section('title', 'My Orders | Nandhini Silks')

@push('styles')
<style>
    .orders-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .order-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 1px solid #f5f5f5;
        margin-bottom: 15px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .order-status-row {
        display: flex;
        justify-content: flex-end;
        align-self: center;
    }

    .order-meta {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 4px;
        min-width: 0;
    }

    .meta-label {
        font-size: 16px;
        color: #999;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .meta-value {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        white-space: nowrap;
    }

    .meta-value-total {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .order-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .order-items-preview {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-grow: 1;
    }

    .order-img {
        width: 70px;
        height: 70px;
        border-radius: 8px;
        object-fit: cover;
        border: 1px solid #eee;
    }

    .order-items-count {
        font-size: 15px;
        color: #777;
    }

    .order-actions {
        display: flex;
        gap: 10px;
    }

    .btn-outline {
        padding: 10px 20px;
        border: 1px solid var(--pink);
        color: var(--pink);
        background: #fff;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .btn-outline:hover {
        background: var(--pink);
        color: #fff;
    }

    .btn-action {
        padding: 10px 20px;
        background: #f5f5f5;
        border: none;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        color: #666;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-action:hover {
        background: #eee;
    }

    .payment-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 13px;
        font-weight: 700;
        margin-left: 10px;
        line-height: 1;
        white-space: nowrap;
    }

    .pay-paid {
        background: #e6fffb;
        color: #08979c;
    }

    .pay-pending {
        background: #fffbe6;
        color: #d48806;
    }

    .pay-failed {
        background: #fff1f0;
        color: #cf1322;
    }

    .pay-refunded {
        background: #f9f0ff;
        color: #722ed1;
    }

    .pay-partial {
        background: #e6f4ff;
        color: #1677ff;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 5px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
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

    @media (max-width: 600px) {
        .order-card {
            padding: 16px;
        }

        .order-header {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
        }

        .order-status-row {
            justify-content: flex-start;
            align-self: stretch;
        }

        .order-meta {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            column-gap: 10px;
            row-gap: 6px;
            align-items: flex-start;
            width: 100%;
        }

        .meta-item {
            gap: 2px;
            min-width: 0;
        }

        .meta-item:nth-child(2) {
            align-items: center;
            text-align: center;
        }

        .meta-item:last-child {
            align-items: flex-end;
            justify-self: end;
            text-align: right;
        }

        .meta-label {
            font-size: 10px;
            letter-spacing: 0.03em;
        }

        .meta-value {
            font-size: 12px;
        }

        .meta-value-total {
            gap: 4px;
            align-items: flex-start;
            justify-content: flex-end;
        }

        .payment-status,
        .status-badge {
            font-size: 10px;
            padding: 4px 8px;
        }

        .order-body {
            flex-direction: column;
            align-items: stretch;
            gap: 14px;
        }

        .order-items-preview {
            width: 100%;
            align-items: center;
            gap: 12px;
        }

        .order-img {
            width: 60px;
            height: 60px;
            flex-shrink: 0;
        }

        .order-items-count {
            display: block;
            line-height: 1.5;
            text-align: left;
            word-break: break-word;
        }

        .order-actions {
            width: 100%;
            flex-direction: column;
            gap: 8px;
        }

        .order-actions .btn-outline,
        .order-actions .btn-action {
            width: 100%;
            text-align: center;
        }
    }
</style>
@endpush

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp; <span>My Orders</span>
            </div>

            <div class="account-layout">
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ Auth::user()->profile_picture ? asset('uploads/'.Auth::user()->profile_picture) : asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ Auth::user()->name }}</h2>
                        <p class="account-user-email">{{ Auth::user()->email }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link active"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Orders</h1>
                    </div>

                    <div class="orders-container">
                        @forelse(Auth::user()->orders()->latest()->get() as $order)
                        @php
                            $paymentStatus = strtolower(trim((string) $order->payment_status));
                            $orderStatus = strtolower(trim((string) $order->order_status));

                            $paymentStatusClass = match($paymentStatus) {
                                'paid' => 'pay-paid',
                                'failed' => 'pay-failed',
                                'refunded' => 'pay-refunded',
                                'partial' => 'pay-partial',
                                default => 'pay-pending',
                            };

                            $orderStatusClass = match($orderStatus) {
                                'processing' => 'status-processing',
                                'dispatched' => 'status-dispatched',
                                'shipped' => 'status-shipped',
                                'delivered' => 'status-delivered',
                                'failed' => 'status-failed',
                                'cancelled' => 'status-cancelled',
                                'refunded' => 'status-refunded',
                                default => 'status-pending',
                            };
                        @endphp
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-meta">
                                    <div class="meta-item">
                                        <span class="meta-label">Order ID</span>
                                        <span class="meta-value">#NS{{ $order->id }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Date Placed</span>
                                        <span class="meta-value">{{ $order->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <span class="meta-label">Total Amount</span>
                                        <span class="meta-value meta-value-total">&#8377;{{ number_format($order->grand_total, 0) }} 
                                            <span class="payment-status {{ $paymentStatusClass }}">
                                                {{ ucfirst($paymentStatus) }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                <div class="order-status-row">
                                    <span class="status-badge {{ $orderStatusClass }}">{{ ucfirst($orderStatus) }}</span>
                                </div>
                            </div>
                            <div class="order-body">
                                <div class="order-items-preview">
                                    @php $firstItem = $order->items->first(); @endphp
                                    @if($firstItem)
                                        <img src="{{ $firstItem->getImageUrl() }}" alt="" class="order-img">
                                    @else
                                        <img src="{{ asset('images/pro1.png') }}" alt="" class="order-img">
                                    @endif
                                    
                                    @if($order->items->count() > 1)
                                        <a href="{{ url('order-detail') }}?id={{ $order->id }}" class="order-items-count" style="text-decoration: none;">+ {{ $order->items->count() - 1 }} other item(s)</a>
                                    @endif
                                </div>
                                <div class="order-actions">
                                    <a href="{{ url('order-detail') }}?id={{ $order->id }}" class="btn-outline">View Details</a>
                                    <button class="btn-action">Track Order</button>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div style="text-align: center; padding: 50px; background: #fff; border-radius: 15px;">
                                <div style="font-size: 50px; margin-bottom: 20px;">&#128230;</div>
                                <h3 style="color: #333;">No orders yet</h3>
                                <p style="color: #999;">When you place an order, it will appear here.</p>
                                <a href="{{ url('shop') }}" class="btn-outline" style="display: inline-block; margin-top: 20px;">Start Shopping</a>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
