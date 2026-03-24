@extends('frontend.layouts.app')

@section('title', ($category->name ?? 'Shop') . ' - Nandhini Silks')

@section('content')
    <main class="category-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>{{ $category->name }}</span>
            </div>

            <div class="category-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar">
                    <form action="{{ url()->current() }}" method="GET" id="filterForm">
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="price-range-container">
                                <div class="slider-track-modern">
                                    <div class="slider-fill-modern" id="sliderFill"></div>
                                    <input type="range" name="min_price" id="min_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('min_price', $filterData['min_price']) }}" class="range-slider-modern">
                                    <input type="range" name="max_price" id="max_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('max_price', $filterData['max_price']) }}" class="range-slider-modern">
                                </div>
                                <div class="range-values-modern">
                                    <span class="price-val">₹<span id="min_price_val">{{ number_format(request('min_price', $filterData['min_price'] ?? 0), 0) }}</span></span>
                                    <span class="price-val">₹<span id="max_price_val">{{ number_format(request('max_price', $filterData['max_price'] ?? 50000), 0) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Category</h3>
                            <ul class="filter-list">
                                @foreach($filterData['categories'] as $cat)
                                    <li class="filter-item">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, (array)request('categories')) ? 'checked' : '' }} onchange="this.form.submit()">
                                            <span class="checkmark"></span>
                                            <span class="label-text">{{ $cat->name }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach($filterData['attributes'] as $attr)
                            @if($attr->values->isNotEmpty())
                                <div class="filter-group">
                                    <h3 class="filter-title">{{ $attr->name }}</h3>
                                    @if(strtolower($attr->name) == 'color')
                                        <div class="color-swatches-grid-modern">
                                            @foreach($attr->values as $val)
                                                @php
                                                    $swatch = $val->swatch_value;
                                                    $isHex = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                    $isChecked = in_array($val->id, (array)request('attr.'.$attr->id));
                                                @endphp
                                                <label class="swatch-container-modern" title="{{ $val->name }}">
                                                    <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" {{ $isChecked ? 'checked' : '' }} onchange="this.form.submit()">
                                                    @php
                                                        $bgStyle = '#eee';
                                                        if($swatch) {
                                                            $bgStyle = $isHex ? $swatch : 'url('.asset('uploads/'.$swatch).') center/cover';
                                                        } else {
                                                            if(preg_match('/^[a-zA-Z]+$/', $val->name)) $bgStyle = strtolower($val->name);
                                                        }
                                                    @endphp
                                                    <span class="swatch-circle-modern" style="background: {{ $bgStyle }};"></span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="filter-list">
                                            @foreach($attr->values as $val)
                                                <li class="filter-item">
                                                    <label class="custom-checkbox">
                                                        <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" {{ in_array($val->id, (array)request('attr.'.$attr->id)) ? 'checked' : '' }} onchange="this.form.submit()">
                                                        <span class="checkmark"></span>
                                                        <span class="label-text">{{ $val->name }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        <div class="filter-group mt-4">
                            <label class="stock-toggle-modern">
                                <span class="toggle-label">In Stock Only</span>
                                <div class="toggle-container">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </div>
                            </label>
                        </div>

                        <div class="filter-actions mt-5">
                            <button type="submit" class="apply-filters-btn-modern">Apply Filters</button>
                            <a href="{{ url()->current() }}" class="clear-filters-link">Clear All</a>
                        </div>
                    </form>
                </aside>

                <!-- Product Listing -->
                <section class="product-listing">
                    <!-- Filter Chips -->
                    <div class="filter-chips-section">
                        <div class="chips-container">
                            <span class="chip active"> {{ $category->name }}</span>
                            <span class="chip">New Arrivals</span>
                            <span class="chip">Best Sellers</span>
                            <span class="chip">Trending</span>
                        </div>
                    </div>

                    <div class="product-listing-header">
                        <div class="header-left">
                            <h2 class="category-main-title">{{ $category->name }}</h2>
                            <span class="result-count">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? 0 }} products</span>
                        </div>

                        <div style="display: flex; align-items: center;">
                            <div class="view-toggle">
                                <button class="view-btn active" title="Grid View">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path d="M4 4h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 10h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 16h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4z" />
                                    </svg>
                                </button>
                                <button class="view-btn" title="List View">
                                    <svg width="18" height="18" viewBox="0 0 24 24">
                                        <path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" />
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
                                    @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
                                    <button class="overlay-btn wishlist-btn" aria-label="Add to Wishlist" data-product-id="{{ $product->id }}">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $inWishlist ? '#A91B43' : '#666' }}">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                        </svg>
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
                                        <div class="product-rating-v2">★★★★★</div>
                                        <span class="product-category-v2">{{ $product->category->name ?? 'Collection' }}</span>
                                        <h3 class="product-name-v2">{{ $product->name }}</h3>
                                        <p class="product-desc-v2">{{ Str::limit(strip_tags($product->description), 80) }}</p>
                                        <p class="product-price-v2">
                                            ₹{{ number_format($product->price, 0) }}
                                            @if($product->regular_price > $product->price)
                                                <span class="product-price-old">₹{{ number_format($product->regular_price, 0) }}</span>
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
                        <button class="btn-load-more" style="background: #A91B43; color: white; padding: 12px 30px; border: none; border-radius: 50px; cursor: pointer; font-weight: 600;">Load More Products</button>
                    </div>

                    <div class="pagination-container" style="margin-top: 30px;">
                        {{ $products->links() }}
                    </div>
                </section>
            </div>
        </div>
    </main>

    <style>
        /* Modern Premium Sidebar Filters */
        .filters-sidebar {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
            position: sticky;
            top: 20px;
        }

        .filter-group {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-item {
            margin-bottom: 12px;
        }

        /* Custom Modern Checkbox */
        .custom-checkbox {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            user-select: none;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 6px;
            margin-right: 12px;
            transition: all 0.3s ease;
            position: relative;
        }

        .custom-checkbox:hover input ~ .checkmark {
            border-color: #A91B43;
        }

        .custom-checkbox input:checked ~ .checkmark {
            background-color: #A91B43;
            border-color: #A91B43;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .label-text {
            font-size: 0.95rem;
            color: #555;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .custom-checkbox input:checked ~ .label-text {
            color: #222;
            font-weight: 600;
        }

        /* Color Swatches Grid */
        .color-swatches-grid-modern {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .swatch-container-modern {
            cursor: pointer;
            position: relative;
        }

        .swatch-container-modern input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .swatch-circle-modern {
            display: block;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid transparent;
            box-shadow: 0 0 0 1px #eee;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .swatch-container-modern:hover .swatch-circle-modern {
            transform: scale(1.1);
        }

        .swatch-container-modern input:checked ~ .swatch-circle-modern {
            border-color: #fff;
            box-shadow: 0 0 0 2px #A91B43;
            transform: scale(1.1);
        }

        /* Stock Toggle Switch */
        .stock-toggle-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            cursor: pointer;
        }

        .toggle-label {
            font-size: 1rem;
            font-weight: 700;
            color: #222;
        }

        .toggle-container {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-container input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ddd;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .toggle-container input:checked + .toggle-slider {
            background-color: #A91B43;
        }

        .toggle-container input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        /* Price Slider Modern */
        .slider-track-modern {
            position: relative;
            width: 100%;
            height: 6px;
            background: #eee;
            margin: 20px 0;
            border-radius: 10px;
        }

        .slider-fill-modern {
            position: absolute;
            height: 100%;
            background: #A91B43;
            border-radius: 10px;
        }

        .range-slider-modern {
            position: absolute;
            width: 100%;
            pointer-events: none;
            appearance: none;
            height: 6px;
            background: none;
            outline: none;
            top: 0;
            margin: 0;
        }

        .range-slider-modern::-webkit-slider-thumb {
            pointer-events: auto;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        .range-values-modern {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .price-val {
            font-size: 0.9rem;
            font-weight: 700;
            color: #222;
            background: #f8f8f8;
            padding: 4px 10px;
            border-radius: 6px;
        }

        /* Actions */
        .apply-filters-btn-modern {
            width: 100%;
            padding: 14px;
            background: #A91B43;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(169, 27, 67, 0.2);
        }

        .apply-filters-btn-modern:hover {
            background: #8b1637;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(169, 27, 67, 0.3);
        }

        .clear-filters-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .clear-filters-link:hover {
            color: #A91B43;
        }
    </style>
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

        // Price range display logic with fill update
        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        const minVal = document.getElementById('min_price_val');
        const maxVal = document.getElementById('max_price_val');
        const sliderFill = document.getElementById('sliderFill');

        function updateSlider() {
            const min = parseInt(minInput.value);
            const max = parseInt(maxInput.value);
            if(min > max - 100) {
                if(this === minInput) minInput.value = max - 100;
                else maxInput.value = min + 100;
                return;
            }
            const minPercent = ((min - minInput.min) / (minInput.max - minInput.min)) * 100;
            const maxPercent = ((max - minInput.min) / (minInput.max - minInput.min)) * 100;

            if(sliderFill) {
                sliderFill.style.left = minPercent + '%';
                sliderFill.style.width = (maxPercent - minPercent) + '%';
            }
            
            if(minVal) minVal.innerText = min.toLocaleString();
            if(maxVal) maxVal.innerText = max.toLocaleString();
        }

        if(minInput && maxInput) {
            minInput.addEventListener('input', updateSlider);
            maxInput.addEventListener('input', updateSlider);
            minInput.addEventListener('change', () => minInput.form.submit());
            maxInput.addEventListener('change', () => maxInput.form.submit());
            updateSlider(); // Initial call
        }
    </script>
@endpush
