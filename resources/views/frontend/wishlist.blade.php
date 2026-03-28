@extends('frontend.layouts.app')

@section('title', 'My Wishlist | Nandhini Silks')

@section('content')
    <style>
        .product-card-v2 {
            position: relative;
        }
        .card-actions-overlay {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
        }
        .remove-from-wishlist {
            background: rgba(255, 255, 255, 0.95);
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            cursor: pointer;
        }
        .remove-from-wishlist:hover {
            background: #A91B43;
            transform: scale(1.1);
        }
        .remove-from-wishlist:hover i {
            color: white !important;
        }
        .remove-from-wishlist i {
            font-size: 17px;
            color: #666;
            transition: all 0.3s ease;
        }
    </style>
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

                    <div class="wishlist-grid" id="wishlistGrid" style="display: {{ $products->isEmpty() ? 'none' : 'grid' }}; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
                        @foreach($products as $product)
                        <article class="product-card-v2" data-product-id="{{ $product->id }}">
                            <div class="card-actions-overlay">
                                <button class="overlay-btn remove-from-wishlist wishlist-btn" data-product-id="{{ $product->id }}" title="Remove from Wishlist">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                            <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                                <div class="product-image-v2">
                                    @php
                                        $productImage = 'images/pro.png';
                                        if ($product->images && is_array($product->images) && count($product->images) > 0) {
                                            $productImage = 'uploads/' . $product->images[0];
                                        } elseif ($product->image_path) {
                                            $productImage = 'images/' . $product->image_path;
                                        }
                                    @endphp
                                    <img src="{{ asset($productImage) }}" alt="{{ $product->name }}">
                                </div>
                                <div class="product-info-v2">
                                    <span class="product-category-v2" style="color: {{ $product->stock_quantity > 0 ? '#2e7d32' : '#d32f2f' }};">
                                        &#9679; {{ $product->stock_quantity > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                    <h3 class="product-name-v2">{{ $product->name }}</h3>
                                    <p class="product-price-v2">&#8377;{{ number_format($product->price, 0) }}</p>
                                </div>
                            </a>
                            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="add-to-cart-v2">Add to Cart</button>
                            </form>
                        </article>
                        @endforeach
                    </div>

                    <div id="emptyState" style="display: {{ $products->isEmpty() ? 'block' : 'none' }}; text-align: center; padding: 60px 0;">
                        <div style="font-size: 60px; color: #eee; margin-bottom: 20px;">&#10084;</div>
                        <h2 style="color: #333; margin-bottom: 10px;">Your wishlist is empty</h2>
                        <a href="{{ url('shop') }}" class="auth-submit"
                            style="display: inline-block; width: auto; padding: 12px 40px; text-decoration: none; background: #A91B43; color: white; border-radius: 5px;">Explore
                            Collections</a>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <!-- No specific scripts needed, handled globally -->
@endpush
