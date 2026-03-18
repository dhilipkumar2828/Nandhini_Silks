@extends('frontend.layouts.app')

@section('title', 'My Wishlist | Nandhini Silks')

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp; <span>Wishlist</span>
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
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link active"><span>Wishlist</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('login') }}" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Wishlist</h1>
                    </div>

                    <div class="wishlist-grid" id="wishlistGrid">
                        <article class="product-card-v2">
                            <div class="card-actions-overlay">
                                <button class="overlay-btn remove-from-wishlist" title="Remove" style="color: #ff4444;">&times;</button>
                            </div>
                            <a href="{{ url('product-detail') }}" style="text-decoration: none; color: inherit;">
                                <div class="product-image-v2">
                                    <img src="{{ asset('images/saree_royal_gold_handloom_silk_1773214820441.png') }}" alt="Royal Gold Silk Saree">
                                </div>
                                <div class="product-info-v2">
                                    <span class="product-category-v2" style="color: #2e7d32;">&#9679; In Stock</span>
                                    <h3 class="product-name-v2">Royal Gold Handloom Silk</h3>
                                    <p class="product-price-v2">&#8377;7,490</p>
                                </div>
                            </a>
                            <button class="add-to-cart-v2">Move to Cart</button>
                        </article>
                    </div>

                    <div id="emptyState" style="display: none; text-align: center; padding: 60px 0;">
                        <div style="font-size: 60px; color: #eee; margin-bottom: 20px;">&#10084;</div>
                        <h2 style="color: #333; margin-bottom: 10px;">Your wishlist is empty</h2>
                        <a href="{{ url('sarees') }}" class="auth-submit"
                            style="display: inline-block; width: auto; padding: 12px 40px; text-decoration: none;">Explore
                            Collections</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.remove-from-wishlist').forEach(btn => {
            btn.onclick = function () {
                const card = this.closest('.product-card-v2');
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    if (document.querySelectorAll('.product-card-v2').length === 0) {
                        document.getElementById('wishlistGrid').style.display = 'none';
                        document.getElementById('emptyState').style.display = 'block';
                    }
                }, 300);
            };
        });
    </script>
@endpush
