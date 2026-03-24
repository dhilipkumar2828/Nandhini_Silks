@extends('frontend.layouts.app')

@section('title', 'Search Results - Nandhini Silks')

@section('content')
    <main class="category-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>Search Results</span>
            </div>

            <div class="search-query-header">
                <h1 class="search-query-text">Results for: <span class="search-query-keyword">"{{ request('q') }}"</span>
                </h1>
                <p class="result-count">Found results matching your search</p>
            </div>

            <div class="category-layout">
                <aside class="filters-sidebar">
                    <div class="filter-group">
                        <h3 class="filter-title">Price Range</h3>
                    </div>
                </aside>

                <section class="product-listing">
                    <div class="product-listing-header">
                        <div class="header-left">
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
                        </div>
                    </div>

                    <div class="product-grid-main" id="resultsGrid">
                        @forelse($products as $product)
                            <article class="product-card-v2">
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
                                        <span class="product-category-v2">{{ $product->category->name ?? 'Collection' }}</span>
                                        <h3 class="product-name-v2">{{ $product->name }}</h3>
                                        <p class="product-price-v2">₹{{ number_format($product->price, 0) }}</p>
                                    </div>
                                </a>
                            </article>
                        @empty
                            <div class="no-results-state" id="noResults" style="display: block; width: 100%; grid-column: 1 / -1; text-align: center; padding: 60px 0;">
                                <h2 class="no-results-title">No results found</h2>
                                <p class="no-results-text">We couldn't find anything matching your search query "{{ request('q') }}".</p>
                                <a href="{{ url('shop') }}" class="btn-load-more" style="text-decoration: none; display: inline-block; margin-top: 20px;">Browse
                                    Collections</a>
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
