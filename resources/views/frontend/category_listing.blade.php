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
                    <form id="filterForm" action="{{ request()->fullUrl() }}" method="GET">
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="price-range-container">
                                <div class="slider-track">
                                    <div class="slider-fill"></div>
                                    <input type="range" name="min_price" id="min_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('min_price', $filterData['min_price']) }}" class="range-slider">
                                    <input type="range" name="max_price" id="max_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('max_price', $filterData['max_price']) }}" class="range-slider">
                                </div>
                                <div class="range-values">
                                    <span id="min_price_val">₹{{ number_format(request('min_price', $filterData['min_price']), 0) }}</span>
                                    <span id="max_price_val">₹{{ number_format(request('max_price', $filterData['max_price']), 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Category</h3>
                            <ul class="filter-list">
                                @foreach($filterData['categories'] as $cat)
                                    <li class="filter-item">
                                        <label class="filter-label">
                                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                                {{ in_array($cat->id, request('categories', [])) ? 'checked' : '' }}> {{ $cat->name }}
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        @foreach($filterData['attributes'] as $attr)
                            <div class="filter-group">
                                <h3 class="filter-title">{{ $attr->name }}</h3>
                                @if(strtolower($attr->name) == 'color')
                                    <div class="color-dots">
                                        @foreach($attr->values as $val)
                                            @php
                                                $swatch = $val->swatch_value;
                                                $isHex = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                $active = in_array($val->id, request('attr.'.$attr->id, []));
                                            @endphp
                                            <div class="color-dot {{ $active ? 'active' : '' }}" 
                                                 style="background: {{ $isHex ? $swatch : ($swatch ? 'url('.asset('uploads/'.$swatch).') center/cover' : '#eee') }};" 
                                                 title="{{ $val->name }}"
                                                 data-value-id="{{ $val->id }}"
                                                 onclick="toggleAttr({{ $attr->id }}, {{ $val->id }})"></div>
                                        @endforeach
                                    </div>
                                    <div id="attr_{{ $attr->id }}_inputs" style="display: none;">
                                        @foreach($attr->values as $val)
                                            @if(in_array($val->id, request('attr.'.$attr->id, [])))
                                                <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" checked>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <ul class="filter-list">
                                        @foreach($attr->values as $val)
                                            <li class="filter-item">
                                                <label class="filter-label">
                                                    <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}"
                                                        {{ in_array($val->id, request('attr.'.$attr->id, [])) ? 'checked' : '' }}> {{ $val->name }}
                                                </label>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        @endforeach

                        <div class="filter-group">
                            <label class="stock-toggle">
                                <span>In Stock Only</span>
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                <div class="toggle-switch">
                                    <div class="toggle-dot"></div>
                                </div>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-apply-filters" style="width: 100%; margin-top: 20px; background: #A91B43; color: white; padding: 12px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">Apply Filters</button>
                    </form>
                </aside>

                <!-- Product Listing -->
                <section class="product-listing">
                    <!-- Filter Chips -->
                    <div class="filter-chips-section">
                        <div class="chips-container">
                            @php 
                                $mainLabel = "All " . $category->name;
                                if ($category instanceof \App\Models\Category) $mainLabel .= " Wear";
                            @endphp
                            <span class="chip active">{{ $mainLabel }}</span>

                            @if($category instanceof \App\Models\Category && $category->subCategories->count() > 0)
                                @foreach($category->subCategories as $sub)
                                    <a href="{{ url('category/'.$category->slug.'/'.$sub->slug) }}" style="text-decoration: none;">
                                        <span class="chip">{{ $sub->name }}</span>
                                    </a>
                                @endforeach
                            @elseif($category instanceof \App\Models\SubCategory)
                                @foreach($category->category->subCategories as $sibling)
                                    @if($sibling->id != $category->id)
                                        <a href="{{ url('category/'.$category->category->slug.'/'.$sibling->slug) }}" style="text-decoration: none;">
                                            <span class="chip">{{ $sibling->name }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            @elseif($category instanceof \App\Models\ChildCategory)
                                @foreach($category->subCategory->childCategories as $sibling)
                                    @if($sibling->id != $category->id)
                                        <a href="{{ url('category/'.$category->category->slug.'/'.$category->subCategory->slug.'/'.$sibling->slug) }}" style="text-decoration: none;">
                                            <span class="chip">{{ $sibling->name }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            @endif

                            {{-- Dynamic Sorting Chips --}}
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'newest']) }}" style="text-decoration: none;">
                                <span class="chip {{ request('sort') == 'newest' ? 'active' : '' }}">New Arrivals</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'popularity']) }}" style="text-decoration: none;">
                                <span class="chip {{ request('sort') == 'popularity' ? 'selected' : '' }}">Best Sellers</span>
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'trending']) }}" style="text-decoration: none;">
                                <span class="chip">Trending</span>
                            </a>
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

                            <form action="{{ request()->fullUrl() }}" method="GET" style="margin-left: 15px;">
                                @foreach(request()->except('sort') as $key => $val)
                                    @if(is_array($val))
                                        @foreach($val as $subKey => $subVal)
                                            @if(is_array($subVal))
                                                @foreach($subVal as $innerVal)
                                                    <input type="hidden" name="{{ $key }}[{{ $subKey }}][]" value="{{ $innerVal }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}[]" value="{{ $subVal }}">
                                            @endif
                                        @endforeach
                                    @else
                                        <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                                    @endif
                                @endforeach
                                <select class="sort-select" name="sort" onchange="this.form.submit()">
                                    <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Sort By: Popularity</option>
                                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                </select>
                            </form>
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
                                        <span class="product-category-v2">{{ $product->category->name }}</span>
                                        <h3 class="product-name-v2">{{ $product->name }}</h3>
                                        <p class="product-desc-v2">Premium {{ $product->name }} collection from Nandhini Silks.</p>
                                        <p class="product-price-v2">
                                            ₹{{ number_format($product->price, 0) }}
                                            <span class="product-price-old">₹{{ number_format($product->price * 1.25, 0) }}</span>
                                        </p>
                                    </div>
                                </a>
                                <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-v2" style="text-decoration: none; display: block; text-align: center;">View Details</a>
                            </article>
                            @endforeach
                        @else
                            <div class="no-results-v2" style="grid-column: 1/-1; text-align: center; padding: 100px 0;">
                                <div style="font-size: 64px; color: #eee; margin-bottom: 20px;">
                                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                                    </svg>
                                </div>
                                <h3 style="color: #333; margin-bottom: 10px;">No Products Found</h3>
                                <p style="color: #999;">Try adjusting your filters or checking another category.</p>
                            </div>
                        @endif
                    </div>

                    <div class="pagination-container" style="margin-top: 50px;">
                        {{ $products->appends(request()->query())->links() }}
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

        // Price range display logic
        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        const minVal = document.getElementById('min_price_val');
        const maxVal = document.getElementById('max_price_val');

        if(minInput && maxInput) {
            minInput.addEventListener('input', () => {
                minVal.innerText = '₹' + parseInt(minInput.value).toLocaleString();
            });
            maxInput.addEventListener('input', () => {
                maxVal.innerText = '₹' + parseInt(maxInput.value).toLocaleString();
            });
        }

        function toggleAttr(groupId, valueId) {
            const container = document.getElementById('attr_' + groupId + '_inputs');
            let input = container.querySelector('input[value="' + valueId + '"]');
            
            if (input) {
                input.remove();
            } else {
                input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'attr[' + groupId + '][]';
                input.value = valueId;
                input.checked = true;
                container.appendChild(input);
            }
            
            // Highlight color dot
            event.target.classList.toggle('active');
        }
    </script>
    <style>
        .chip { 
            display: inline-flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 40px; 
            padding: 0 20px; 
            border-radius: 20px; 
            font-size: 14px; 
            font-weight: 500;
            vertical-align: middle;
            margin-bottom: 5px;
        }
        .chips-container a { display: inline-flex; }
        .range-slider {
            position: absolute;
            width: 100%;
            pointer-events: none;
            appearance: none;
            height: 2px;
            background: none;
            outline: none;
            top: 2px;
        }
        .range-slider::-webkit-slider-thumb {
            pointer-events: auto;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .color-dot.active {
            border: 3px solid #fff !important;
            box-shadow: 0 0 0 2px #A91B43 !important;
        }
    </style>
@endpush
