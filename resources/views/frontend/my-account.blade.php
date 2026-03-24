@extends('frontend.layouts.app')

@section('title', 'Account Dashboard | Nandhini Silks')

@push('styles')
<style>
    .order-item-mini {
        justify-content: space-between;
        gap: 14px;
    }

    .mini-order-info {
        flex: 1;
        min-width: 0;
        padding-left: 10px;
    }

    .mini-order-id {
        display: block;
        font-size: 14px;
        font-weight: 700;
        color: #1f2937;
        line-height: 1.35;
    }

    .mini-order-date {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
        line-height: 1.4;
    }

    .order-item-mini .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .order-item-mini .status-pending {
        background: #fff7e6;
        color: #d46b08;
    }

    .order-item-mini .status-processing {
        background: #fffbe6;
        color: #d48806;
    }

    .order-item-mini .status-dispatched,
    .order-item-mini .status-shipped {
        background: #e6f4ff;
        color: #1677ff;
    }

    .order-item-mini .status-delivered {
        background: #f6ffed;
        color: #389e0d;
    }

    .order-item-mini .status-failed,
    .order-item-mini .status-cancelled {
        background: #fff1f0;
        color: #cf1322;
    }

    .order-item-mini .status-refunded {
        background: #f9f0ff;
        color: #722ed1;
    }
</style>
@endpush

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>My Account</span>
            </div>

            <div class="account-layout">
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ Auth::user()->name }}</h2>
                        <p class="account-user-email">{{ Auth::user()->email }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link active"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="dashboard-welcome">
                        <h1 class="dashboard-title">Welcome back, {{ explode(' ', Auth::user()->name)[0] }}!</h1>
                        <p class="dashboard-subtitle">From your account dashboard you can view your recent orders, manage your shipping and billing addresses, and edit your password and account details.</p>
                    </div>

                    <div class="dashboard-stats">
                        <a href="{{ url('my-orders') }}" class="stat-card">
                            <div class="stat-icon">&#128230;</div>
                            <div class="stat-info">
                                <span class="stat-value">{{ $orderCount }}</span>
                                <span class="stat-label">Total Orders</span>
                            </div>
                        </a>
                        <a href="{{ url('wishlist') }}" class="stat-card">
                            <div class="stat-icon">&#10084;&#65039;</div>
                            <div class="stat-info">
                                <span class="stat-value">{{ $wishlistCount }}</span>
                                <span class="stat-label">In Wishlist</span>
                            </div>
                        </a>
                        <a href="{{ url('my-addresses') }}" class="stat-card">
                            <div class="stat-icon">&#127968;</div>
                            <div class="stat-info">
                                <span class="stat-value">{{ $addressCount }}</span>
                                <span class="stat-label">Saved Addresses</span>
                            </div>
                        </a>
                    </div>

                    <div class="dashboard-grid">
                        <div class="dashboard-section">
                            <div class="section-header">
                                <h3 class="section-title">Recent Orders</h3>
                                <a href="{{ url('my-orders') }}" class="view-all-link">View All</a>
                            </div>
                            <div class="order-list">
                                @forelse($recentOrders as $order)
                                @php
                                    $orderStatus = strtolower(trim((string) $order->order_status));
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
                                <a href="{{ url('order-detail') }}?id={{ $order->id }}" class="order-item-mini">
                                    <div class="mini-order-info">
                                        <span class="mini-order-id">#NS{{ $order->id }}</span>
                                        <span class="mini-order-date">{{ $order->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <span class="status-badge {{ $orderStatusClass }}">{{ ucfirst($orderStatus) }}</span>
                                </a>
                                @empty
                                    <p style="padding: 20px; color: #999; text-align: center;">No orders found yet.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="dashboard-section">
                            <div class="section-header">
                                <h3 class="section-title">Account Details</h3>
                                <a href="{{ url('my-profile') }}" class="view-all-link">Edit Profile</a>
                            </div>
                            <div class="account-summary-mini">
                                <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                                <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                                <p><strong>Phone:</strong> {{ Auth::user()->phone ?? 'Not provided' }}</p>
                                <p style="margin-top: 20px; font-size: 13px; color: #777;">Member since: {{ Auth::user()->created_at->format('F Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
