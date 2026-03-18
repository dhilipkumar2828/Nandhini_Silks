@extends('frontend.layouts.app')

@section('title', 'My Reviews | Nandhini Silks')

@push('styles')
<style>
    .review-tabs {
        display: flex;
        gap: 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
    }

    .review-tab {
        padding-bottom: 15px;
        font-size: 15px;
        font-weight: 600;
        color: #999;
        cursor: pointer;
        position: relative;
    }

    .review-tab.active {
        color: var(--pink);
    }

    .review-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--pink);
    }

    .review-item-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        display: flex;
        gap: 25px;
    }

    .review-product-img {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        object-fit: cover;
    }

    .review-content-side {
        flex: 1;
    }

    .review-product-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
    }

    .review-stars {
        color: #ffc107;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .review-text {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .review-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #999;
    }

    .review-actions {
        display: flex;
        gap: 15px;
    }

    .review-action-btn {
        background: none;
        border: none;
        color: var(--pink);
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
    }

    .pending-review-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff5f7;
        padding: 20px;
        border-radius: 15px;
        border: 1px dashed var(--pink);
        margin-bottom: 15px;
    }

    .btn-review-now {
        background: var(--pink);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
    }

    @media (max-width: 600px) {
        .review-item-card {
            flex-direction: column;
        }

        .review-product-img {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endpush

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp; <span>My Reviews</span>
            </div>

            <div class="account-layout">
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">John Doe</h2>
                        <p class="account-user-email">john.doe@example.com</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link active"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('login') }}" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Reviews</h1>
                    </div>

                    <div class="review-tabs">
                        <div class="review-tab active">Published Reviews (2)</div>
                        <div class="review-tab">Pending Reviews (1)</div>
                    </div>

                    <div id="publishedReviews">
                        <div class="review-item-card">
                            <img src="{{ asset('images/pro1.png') }}" alt="" class="review-product-img">
                            <div class="review-content-side">
                                <h3 class="review-product-name">Royal Gold Handloom Silk Saree</h3>
                                <div class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                                <p class="review-text">The quality of the silk is amazing. Drapes so beautifully and the gold jari work is very intricate. Perfect for my cousin's wedding!</p>
                                <div class="review-footer">
                                    <span>Reviewed on Oct 20, 2023</span>
                                    <div class="review-actions">
                                        <button class="review-action-btn">Edit</button>
                                        <button class="review-action-btn" style="color: #999;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="review-item-card">
                            <img src="{{ asset('images/pro2.png') }}" alt="" class="review-product-img">
                            <div class="review-content-side">
                                <h3 class="review-product-name">Classic Red Kanchipuram Saree</h3>
                                <div class="review-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                                <p class="review-text">Vibrant red color as shown in pictures. The material is slightly heavier than expected but looks very royal.</p>
                                <div class="review-footer">
                                    <span>Reviewed on Sep 30, 2023</span>
                                    <div class="review-actions">
                                        <button class="review-action-btn">Edit</button>
                                        <button class="review-action-btn" style="color: #999;">Delete</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="pendingReviews" style="display: none;">
                        <div class="pending-review-card">
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <img src="{{ asset('images/kids_pattu_pavadai_pink_gold_1773214272993.png') }}" alt="" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover;">
                                <div>
                                    <h4 style="margin-bottom: 4px;">Traditional Pink Pattu Pavadai</h4>
                                    <p style="font-size: 12px; color: #777;">Purchased on Oct 10, 2023</p>
                                </div>
                            </div>
                            <button class="btn-review-now">Write a Review</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.review-tab');
        const published = document.getElementById('publishedReviews');
        const pending = document.getElementById('pendingReviews');

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                tabs.forEach(item => item.classList.remove('active'));
                tab.classList.add('active');
                published.style.display = index === 0 ? 'block' : 'none';
                pending.style.display = index === 1 ? 'block' : 'none';
            });
        });
    });
</script>
@endpush
