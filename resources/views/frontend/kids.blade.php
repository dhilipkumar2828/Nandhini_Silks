@extends('frontend.layouts.app')

@section('title', 'Kids - Nandhini Silks')

@section('content')
    <main class="category-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>Kids Collection</span>
            </div>

            <div class="category-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar">
                    <div class="filter-group">
                        <h3 class="filter-title">Price Range</h3>
                        <div class="price-range-container">
                            <div class="slider-track">
                                <div class="slider-fill"></div>
                                <div class="slider-handle" style="left: 20%;"></div>
                                <div class="slider-handle" style="left: 80%;"></div>
                            </div>
                            <div class="range-values">
                                <span>₹500</span>
                                <span>₹20,000</span>
                            </div>
                        </div>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Age Group</h3>
                        <ul class="filter-list">
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 0 - 2 Years</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 3 - 5 Years</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 6 - 10 Years</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 11 - 15
                                    Years</label></li>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Kids Category</h3>
                        <ul class="filter-list">
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Pattu
                                    Parikadai</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Boys Silk
                                    Set</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Baby Frocks</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Festive Wear</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox" id="half-saree-checkbox"> Half Saree</label>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Fabric</h3>
                        <ul class="filter-list">
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Pure Silk</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Soft Cotton</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Organza</label></li>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <h3 class="filter-title">Color</h3>
                        <ul class="filter-list">
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Yellow</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Pink</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Light Blue</label>
                            </li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> Multi-color</label>
                            </li>
                        </ul>
                    </div>

                    <div class="filter-group" id="size-filter-group" style="display: none;">
                        <h3 class="filter-title">Size</h3>
                        <ul class="filter-list">
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 24</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 26</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 28</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 30</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 32</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 34</label></li>
                            <li class="filter-item"><label class="filter-label"><input type="checkbox"> 36</label></li>
                        </ul>
                    </div>

                    <div class="filter-group">
                        <label class="stock-toggle">
                            <span>In Stock Only</span>
                            <input type="checkbox" hidden checked>
                            <div class="toggle-switch">
                                <div class="toggle-dot"></div>
                            </div>
                        </label>
                    </div>
                </aside>

                <!-- Product Listing -->
                <section class="product-listing">
                    <!-- Filter Chips -->
                    <div class="filter-chips-section">
                        <div class="chips-container">
                            <span class="chip active">All Kids Wear</span>
                            <span class="chip">Traditional Sets</span>
                            <span class="chip">Pattu Pavadai</span>
                            <span class="chip">Baby Silk Wear</span>
                            <span class="chip">New Arrivals</span>
                        </div>
                    </div>

                    <div class="product-listing-header">
                        <div class="header-left">
                            <h2 class="category-main-title">{{ $category->name }}</h2>
                            <span class="result-count">Showing
                                {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of
                                {{ $products->total() ?? 0 }} products</span>
                        </div>

                        <div style="display: flex; align-items: center;">
                            <div class="view-toggle">
                                <button class="view-btn active" title="Grid View">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path
                                            d="M4 4h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 10h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 16h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4z" />
                                    </svg>
                                </button>
                                <button class="view-btn" title="List View">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path
                                            d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" />
                                    </svg>
                                </button>
                            </div>

                            <select class="sort-select">
                                <option>Sort By: Popularity</option>
                                <option>Price: Low to High</option>
                                <option>Price: High to Low</option>
                                <option>Newest First</option>
                            </select>
                        </div>
                    </div>

                    <div class="product-grid-main">
                        @if ($products->count() > 0)
                            @foreach ($products as $product)
                                <article class="product-card-v2">
                                    <div class="card-actions-overlay">
                                        <button class="overlay-btn" aria-label="Add to Wishlist">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="#666">
                                                <path
                                                    d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        style="text-decoration: none; color: inherit;">
                                        <div class="product-image-v2">
                                            <img src="{{ $product->image_path ? asset('images/' . $product->image_path) : asset('images/pro.png') }}"
                                                alt="{{ $product->name }}">
                                        </div>
                                        <div class="product-info-v2">
                                            <div class="product-rating-v2">★★★★★</div>
                                            <span class="product-category-v2">{{ $product->category->name ?? 'Collection' }}</span>
                                            <h3 class="product-name-v2">{{ $product->name }}</h3>
                                            <p class="product-desc-v2">{{ Str::limit(strip_tags($product->description), 80) }}</p>
                                            <p class="product-price-v2">
                                                ₹{{ number_format($product->price, 0) }}
                                                @if($loop->index % 3 == 0)
                                                    <span
                                                        class="product-price-old">₹{{ number_format($product->price * 1.2, 0) }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </a>
                                    <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-v2" style="text-decoration: none; display: block; text-align: center;">View Details</a>
                                </article>
                            @endforeach
                        @else
                            <div class="no-products">
                                <p>No products found in this category.</p>
                            </div>
                        @endif
                    </div>

                    <div class="load-more-container" style="text-align: center; margin-top: 40px;">
                        <button class="btn-load-more"
                            style="background: #A91B43; color: white; padding: 12px 30px; border: none; border-radius: 50px; cursor: pointer; font-weight: 600;">Load
                            More Products</button>
                    </div>

                    <div class="pagination-container" style="margin-top: 30px;">
                        {{ $products->links() }}
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // Grid/List View Toggle Logic
        const viewBtns = document.querySelectorAll('.view-btn');
        const productContainer = document.querySelector('.product-grid-main');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                if (btn.title === 'List View') {
                    productContainer.classList.add('view-list');
                } else {
                    productContainer.classList.remove('view-list');
                }
            });
        });

        // Toggle Size Filter based on Half Saree Checkbox
        const halfSareeCheckbox = document.getElementById('half-saree-checkbox');
        const sizeFilterGroup = document.getElementById('size-filter-group');

        if (halfSareeCheckbox && sizeFilterGroup) {
            halfSareeCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    sizeFilterGroup.style.display = 'block';
                } else {
                    sizeFilterGroup.style.display = 'none';
                }
            });
        }
    </script>
@endpush
