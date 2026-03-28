@php
    $listing_title = $title ?? ($category->name ?? 'Products');
    $current_products = $products ?? collect();
@endphp

<section class="product-listing">

    <div class="product-listing-header">
        <div class="header-left">
            <h2 class="category-main-title">{{ $listing_title }}</h2>
            <span class="result-count">Showing {{ $current_products->firstItem() ?? 0 }}-{{ $current_products->lastItem() ?? 0 }} of {{ $current_products->total() ?? 0 }} products</span>
        </div>

        <div style="display: flex; align-items: center;">
            <div class="view-toggle">
                <button class="view-btn active" title="Grid View" data-view="grid">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path d="M4 4h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 10h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4zM4 16h4v4H4zm6 0h4v4h-4zm6 0h4v4h-4z" />
                    </svg>
                </button>
                <button class="view-btn" title="List View" data-view="list">
                    <svg width="18" height="18" viewBox="0 0 24 24">
                        <path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z" />
                    </svg>
                </button>
            </div>

            <form action="{{ request()->fullUrl() }}" method="GET" style="margin-left: 15px;" id="sortForm">
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

    <div class="product-grid-main" id="productListingGrid">
        @forelse($current_products as $product)
            @include('frontend.partials.product-card', ['product' => $product])
        @empty
            <div class="no-products" style="grid-column: 1/-1; text-align: center; padding: 60px 0;">
                <p>No products found in this category.</p>
            </div>
        @endforelse
    </div>

    @if($current_products->hasPages())
        <div class="pagination-container" style="margin-top: 50px;">
            {{ $current_products->appends(request()->query())->links() }}
        </div>
    @endif
</section>
