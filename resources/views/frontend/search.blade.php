@extends('frontend.layouts.app')

@section('title', 'Search Results - Nandhini Silks')

@push('styles')
    <style>
        .search-results-page {
            padding-bottom: 56px;
        }

        .filter-chips-section {
            margin-bottom: 25px;
        }

        .chips-container {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            scrollbar-width: none;
        }

        .chips-container::-webkit-scrollbar {
            display: none;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 40px;
            padding: 0 20px;
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 50px;
            font-size: 14px;
            font-weight: 600;
            color: #4b5563;
            white-space: nowrap;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .chip:hover {
            border-color: #A91B43;
            color: #A91B43;
            background: #fffcf0;
        }

        .chip.active {
            background: #A91B43;
            border-color: #A91B43;
            color: #fff;
            box-shadow: 0 4px 12px rgba(169, 27, 67, 0.15);
        }

        .filters-sidebar {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
            position: sticky;
            top: 20px;
        }

        .mobile-filter-toggle {
            display: none;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            margin-bottom: 16px;
            border: 1px solid rgba(169, 27, 67, 0.14);
            border-radius: 12px;
            background: #fff;
            color: #A91B43;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }

        .mobile-filter-toggle-icon {
            font-size: 20px;
            line-height: 1;
            transition: transform 0.3s ease;
        }

        .mobile-filter-toggle[aria-expanded="true"] .mobile-filter-toggle-icon {
            transform: rotate(45deg);
        }

        .filter-group {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .mobile-filter-overlay {
            display: none;
        }

        .filter-drawer-header {
            display: none;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .filter-group-content {
            display: block;
        }

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
            flex-shrink: 0;
            transition: all 0.2s;
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

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-item {
            margin-bottom: 12px;
        }

        .price-range-container {
            padding: 10px 5px;
        }

        .slider-track-modern {
            position: relative;
            width: 100%;
            height: 5px;
            background: #f0f0f0;
            margin: 25px 0;
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
            top: -8px;
            margin: 0;
        }

        .range-slider-modern::-webkit-slider-runnable-track {
            height: 8px;
            background: transparent;
            border-radius: 10px;
        }

        .range-slider-modern::-moz-range-track {
            height: 8px;
            background: transparent;
            border-radius: 10px;
        }

        .range-slider-modern::-webkit-slider-thumb {
            pointer-events: auto;
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            position: relative;
            z-index: 3;
        }

        .range-slider-modern::-moz-range-thumb {
            pointer-events: auto;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            position: relative;
            z-index: 3;
        }

        .range-values-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .price-separator {
            font-weight: 500;
            color: #888;
        }

        .price-val {
            font-size: 16px;
            font-weight: 700;
            color: #222;
            background: #f5f5f5;
            padding: 8px 16px;
            border-radius: 10px;
            display: inline-block;
        }

        .price-separator {
            display: none;
        }

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

        .filter-actions {
            margin-top: 28px;
        }

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

        .view-toggle {
            display: flex;
            gap: 8px;
        }

        .view-btn {
            width: 36px;
            height: 36px;
            border: 1px solid #eee;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .view-btn.active {
            background: #A91B43;
            color: #fff;
            border-color: #A91B43;
        }

        @media (max-width: 1024px) {
            .mobile-filter-toggle {
                display: flex;
            }
            .mobile-filter-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.35);
                backdrop-filter: blur(2px);
                z-index: 9998;
            }

            .mobile-filter-overlay.active {
                display: block;
            }

            .filters-sidebar {
                display: block !important;
                position: fixed;
                top: 0;
                right: 0;
                width: min(390px, 100%);
                height: 100dvh;
                z-index: 9999;
                overflow-y: auto;
                border-radius: 22px 0 0 22px;
                padding: 18px 18px 26px;
                box-shadow: -24px 0 50px rgba(15, 23, 42, 0.18);
                transform: translateX(102%);
                transition: transform 0.28s ease;
            }

            .filters-sidebar.mobile-open {
                transform: translateX(0);
            }

            .filter-drawer-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 18px;
                padding-bottom: 14px;
                border-bottom: 1px solid #eee;
            }

            .filter-drawer-title {
                margin: 0;
                font-size: 18px;
                font-weight: 700;
                color: #222;
            }

            .filter-drawer-close {
                width: 38px;
                height: 38px;
                border: 1px solid #e5e7eb;
                border-radius: 50%;
                background: #fff;
                color: #111827;
                font-size: 20px;
                line-height: 1;
                cursor: pointer;
            }

            body.filter-open {
                overflow: hidden;
            }

            .filter-title {
                cursor: pointer;
                position: relative;
                padding-right: 24px;
            }

            .filter-title::after {
                content: "+";
                position: absolute;
                right: 0;
                top: 50%;
                transform: translateY(-52%);
                width: 22px;
                height: 22px;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #a91b43;
                font-size: 18px;
                line-height: 1;
            }

            .filter-group.is-open .filter-title::after {
                content: "−";
            }

            .filter-group:first-child .filter-title::after {
                content: "−";
            }

            .filter-group-content {
                display: none;
                padding-top: 2px;
            }

            .filter-group.is-open .filter-group-content {
                display: block;
            }

            .filter-group:first-child .filter-group-content {
                display: block;
            }
        }

        @media (max-width: 640px) {
            .filters-sidebar {
                padding: 16px 16px 22px;
                border-radius: 18px 0 0 18px;
            }

            .filter-title {
                font-size: 17px;
            }

            .label-text {
                font-size: 14px;
            }

            .price-val {
                font-size: 16px;
            }

            .mobile-filter-toggle {
                padding: 13px 16px;
                border-radius: 10px;
            }

            .filter-title {
                font-size: 16px;
            }

            .filter-group {
                margin-bottom: 22px;
                padding-bottom: 20px;
            }
        }
    </style>
@endpush

@section('content')
    <main class="category-page search-results-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>Search Results</span>
            </div>

            <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle" aria-expanded="false" style="display: none;">
                <span>Filters</span>
                <span class="mobile-filter-toggle-icon">+</span>
            </button>

            <div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>

            <div class="category-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar" id="filtersSidebar">
                    <div class="filter-drawer-header">
                        <h3 class="filter-drawer-title">Filters</h3>
                        <button type="button" class="filter-drawer-close" id="filterDrawerClose" aria-label="Close filters">&times;</button>
                    </div>
                    <form id="filterForm" action="{{ route('search') }}" method="GET">
                        <input type="hidden" name="q" value="{{ request('q') }}">
                        
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="filter-group-content price-range-container">
                                <div class="slider-track-modern">
                                    <div class="slider-fill-modern" id="sliderFill"></div>
                                    <input type="range" name="min_price" id="min_price_input" min="{{ $min_price }}" max="{{ $max_price }}" value="{{ request('min_price', $min_price) }}" class="range-slider-modern">
                                    <input type="range" name="max_price" id="max_price_input" min="{{ $min_price }}" max="{{ $max_price }}" value="{{ request('max_price', $max_price) }}" class="range-slider-modern">
                                </div>
                                <div class="range-values-modern">
                                    <span class="price-val">₹<span id="min_price_val">{{ number_format(request('min_price', $min_price), 0) }}</span></span>
                                    <span class="price-separator">-</span>
                                    <span class="price-val">₹<span id="max_price_val">{{ number_format(request('max_price', $max_price), 0) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Category</h3>
                            <ul class="filter-group-content filter-list">
                                @foreach($categories as $cat)
                                    <li class="filter-item">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                                {{ in_array($cat->id, (array)request('categories', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                            <span class="checkmark"></span>
                                            <span class="label-text">{{ $cat->name }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach($attributes as $attr)
                            @if($attr->values->isNotEmpty())
                                <div class="filter-group">
                                    <h3 class="filter-title">{{ $attr->name }}</h3>
                                    @if(strtolower($attr->name) == 'color')
                                        <div class="filter-group-content color-swatches-grid-modern">
                                            @foreach($attr->values as $val)
                                                @php
                                                    $swatch = $val->swatch_value;
                                                    $isHex = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                    $active = in_array($val->id, (array)request('attr.'.$attr->id, []));
                                                @endphp
                                                <label class="swatch-container-modern" title="{{ $val->name }}">
                                                    <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" 
                                                        {{ $active ? 'checked' : '' }} onchange="this.form.submit()">
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
                                        <ul class="filter-group-content filter-list">
                                            @foreach($attr->values as $val)
                                                <li class="filter-item">
                                                    <label class="custom-checkbox">
                                                        <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}"
                                                            {{ in_array($val->id, (array)request('attr.'.$attr->id, [])) ? 'checked' : '' }} onchange="this.form.submit()">
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

                        <div class="filter-group">
                            <label class="stock-toggle-modern">
                                <span class="toggle-label">In Stock Only</span>
                                <div class="toggle-container">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </div>
                            </label>
                        </div>

                        <div class="filter-actions mt-4">
                            <button type="submit" class="apply-filters-btn-modern">Apply Filters</button>
                            <a href="{{ route('search', ['q' => request('q')]) }}" class="clear-filters-link">Clear All</a>
                        </div>
                    </form>
                </aside>

                <section class="product-listing">
                    <!-- Filter Chips -->
                    <div class="filter-chips-section">
                        <div class="chips-container">
                            <span class="chip active">All Results</span>
                        </div>
                    </div>

                    <div class="product-listing-header">
                        <div class="header-left">
                            <h2 class="category-main-title">Search: "{{ request('q') }}"</h2>
                            <span class="result-count">Showing {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} of {{ $products->total() ?? 0 }} products</span>
                        </div>

                        <div style="display: flex; align-items: center;">
                            <div class="view-toggle">
                                <button class="view-btn active" title="Grid View" data-view="grid">
                                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M4 4h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 10h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 16h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4z" /></svg>
                                </button>
                                <button class="view-btn" title="List View" data-view="list">
                                    <svg width="18" height="18" viewBox="0 0 24 24"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="product-grid-main" id="resultsGrid">
                        @forelse($products as $product)
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
                        @empty
                            <div class="no-results-state" id="noResults" style="display: block; width: 100%; grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                                <h2 class="no-results-title">No results found</h2>
                                <p class="no-results-text">We couldn't find anything matching your search.</p>
                                <a href="{{ url('shop') }}" class="btn-load-more" style="text-decoration: none; display: inline-block; margin-top: 20px;">Browse Collections</a>
                            </div>
                        @endforelse
                    </div>

                    @if($products->hasPages())
                        <div class="pagination-wrap" style="margin-top: 40px; display: flex; justify-content: center;">
                            {{ $products->appends(['q' => request('q')])->links() }}
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const resultsGrid = document.getElementById('resultsGrid');
        const viewButtons = document.querySelectorAll('.view-btn');

        if (resultsGrid && viewButtons.length) {
            viewButtons.forEach((button) => {
                button.addEventListener('click', function () {
                    const selectedView = button.getAttribute('data-view');
                    viewButtons.forEach((btn) => btn.classList.remove('active'));
                    button.classList.add('active');
                    resultsGrid.classList.toggle('view-list', selectedView === 'list');
                });
            });
        }

        const mobileFilterToggle = document.getElementById('mobileFilterToggle');
        const filtersSidebar = document.getElementById('filtersSidebar');
        const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
        const filterDrawerClose = document.getElementById('filterDrawerClose');
        const mobileFilterToggleIcon = document.querySelector('.mobile-filter-toggle-icon');

        if (mobileFilterToggle && filtersSidebar) {
            const closeFilters = () => {
                filtersSidebar.classList.remove('mobile-open');
                mobileFilterOverlay?.classList.remove('active');
                document.body.classList.remove('filter-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '+';
            };

            const openFilters = () => {
                filtersSidebar.classList.add('mobile-open');
                mobileFilterOverlay?.classList.add('active');
                document.body.classList.add('filter-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '−';
            };

            mobileFilterToggle.addEventListener('click', () => {
                if (filtersSidebar.classList.contains('mobile-open')) {
                    closeFilters();
                } else {
                    openFilters();
                }
            });

            mobileFilterOverlay?.addEventListener('click', closeFilters);
            filterDrawerClose?.addEventListener('click', closeFilters);

            document.querySelectorAll('.filter-group .filter-title').forEach((title) => {
                title.addEventListener('click', () => {
                    if (window.innerWidth > 1024) return;
                    const group = title.closest('.filter-group');
                    if (!group) return;
                    group.classList.toggle('is-open');
                });
            });

            if (window.innerWidth <= 1024) {
                const firstGroup = filtersSidebar.querySelector('.filter-group');
                firstGroup?.classList.add('is-open');
            }
        }

        // Price range display logic
        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        if(minInput && maxInput) {
            const minVal = document.getElementById('min_price_val');
            const maxVal = document.getElementById('max_price_val');
            const sliderFill = document.getElementById('sliderFill');

            function updateSlider() {
                const min = parseInt(minInput.value);
                const max = parseInt(maxInput.value);
                const minPercent = ((min - minInput.min) / (minInput.max - minInput.min)) * 100;
                const maxPercent = ((max - minInput.min) / (minInput.max - minInput.min)) * 100;
                sliderFill.style.left = minPercent + '%';
                sliderFill.style.width = (maxPercent - minPercent) + '%';
                minVal.innerText = min.toLocaleString();
                maxVal.innerText = max.toLocaleString();
            }
            minInput.addEventListener('input', updateSlider);
            maxInput.addEventListener('input', updateSlider);
            minInput.addEventListener('change', () => minInput.form.submit());
            maxInput.addEventListener('change', () => maxInput.form.submit());
            updateSlider();
        }
    </script>
@endpush
