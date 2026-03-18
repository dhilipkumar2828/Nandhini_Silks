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
                        <article class="product-card-v2">
                            <a href="{{ url('product-detail') }}" style="text-decoration: none; color: inherit;">
                                <div class="product-image-v2">
                                    <img src="{{ asset('images/saree_royal_gold_handloom_silk_1773214820441.png') }}"
                                        alt="Pure Silk Saree">
                                </div>
                                <div class="product-info-v2">
                                    <span class="product-category-v2">Pure Silk</span>
                                    <h3 class="product-name-v2">Royal Gold Handloom Silk</h3>
                                    <p class="product-price-v2">₹7,490</p>
                                </div>
                            </a>
                        </article>
                    </div>

                    <div class="no-results-state" id="noResults" style="display: none;">
                        <h2 class="no-results-title">No results found</h2>
                        <p class="no-results-text">We couldn't find anything matching your search.</p>
                        <a href="{{ url('sarees') }}" class="btn-load-more" style="text-decoration: none;">Browse
                            Collections</a>
                    </div>
                </section>
            </div>
        </div>
    </main>
@endsection
