@extends('frontend.layouts.app')

@section('title', 'Nandhini Silks - Home')

@push('styles')
    <style>
        .hero {
            margin-bottom: 56px;
        }

        .collection-section,
        .category-section,
        .promo-section,
        .testimonial-section {
            max-width: 1360px;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
            padding-left: 100px;
            padding-right: 100px;
            box-sizing: border-box;
            overflow: visible;
        }

        .promo-section {
            overflow-x: clip;
            /* Prevent horizontal page scroll from buttons/content */
        }

        .card-link-wrapper {
            text-decoration: none;
            color: inherit;
            display: block;
            transition: opacity 0.3s ease;
        }

        .card-link-wrapper:hover {
            opacity: 0.9;
        }

        .featured-section {
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .featured-decor {
            position: absolute;
            pointer-events: none;
            opacity: 0.8;
            height: auto !important;
            width: auto !important;
            max-width: 320px !important;
            z-index: 0;
        }

        .featured-decor-left {
            left: 0;
            bottom: 0;
            top: auto !important;
            transform: rotate(0deg) !important;
        }

        .featured-decor-right {
            right: 0;
            top: 0;
            transform: rotate(180deg) !important;
        }


        .featured-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 100px;
            box-sizing: border-box;
        }

        .collection-section,
        .category-section {
            margin-top: 0;
            margin-bottom: 50px;
        }

        .featured-section {
            margin-top: 0;
            margin-bottom: 0;
            padding-top: 60px;
            padding-bottom: 60px;
        }


        .collection-title,
        .featured-title,
        .category-title,
        .testimonial-title {
            margin-left: auto;
            margin-right: auto;
            text-align: center;
        }

        .collection-swiper,
        .category-swiper,
        .testimonial-swiper,
        .promo-swiper {
            padding: 8px 4px 20px;
            position: relative;
        }

        .collection-swiper {
            padding: 8px 0 20px;
            overflow: hidden;
        }

        .category-swiper {
            padding: 8px 4px 20px;
            overflow: hidden;
        }

        /* ── Shared nav-button wrapper for non-featured sections ── */
        .collection-swiper-wrap,
        .category-swiper-wrap,
        .testimonial-swiper-wrap,
        .promo-swiper-wrap {
            position: relative;
            width: 100%;
        }

        .promo-swiper-wrap {
            /* Containment handled by parent section */
        }

        /* ── All nav buttons: vertically centered, absolutely placed ── */
        .collection-next,
        .collection-prev,
        .category-next,
        .category-prev,
        .testimonial-next,
        .testimonial-prev,
        .promo-next,
        .promo-prev,
        .featured-next,
        .featured-prev {
            position: absolute;
            top: 45%;
            transform: translateY(-50%);
            z-index: 1000;
        }

        .category-prev,
        .featured-prev,
        .collection-prev,
        .testimonial-prev,
        .promo-prev {
            left: -80px;
        }

        .promo-prev {
            left: -50px;
        }

        .category-next,
        .featured-next,
        .collection-next,
        .testimonial-next,
        .promo-next {
            right: -80px;
        }

        .promo-next {
            right: -50px;
        }

        .testimonial-prev,
        .testimonial-next {
            top: 50%;
            /* Center on the full card for testimonials */
        }

        .collection-next,
        .collection-prev,
        .category-next,
        .category-prev,
        .testimonial-next,
        .testimonial-prev,
        .promo-next,
        .promo-prev,
        .featured-next,
        .featured-prev {
            box-shadow: 0 10px 22px rgba(169, 27, 67, 0.22);
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .collection-next::after,
        .collection-prev::after,
        .category-next::after,
        .category-prev::after,
        .testimonial-next::after,
        .testimonial-prev::after,
        .promo-next::after,
        .promo-prev::after,
        .featured-next::after,
        .featured-prev::after {
            font-size: 18px;
            font-weight: bold;
        }

        .featured-subtitle {
            margin-bottom: 0;
            text-align: center;
        }

        .collection-card,
        .featured-card,
        .testimonial-card {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .collection-name {
            min-height: 52px;
            margin: 16px 0 10px;
            line-height: 1.3;
        }

        .collection-cta,
        .featured-footer {
            margin-top: auto;
        }

        .collection-cta {
            width: 90px;
            height: 28px;
            border-radius: 8px;
            background: #f3a349;
            color: #ffffff;
            font-size: 15px;
            font-weight: 300;
            text-decoration: underline;
            box-shadow: none;
            transition: opacity 0.3s;
        }

        .collection-cta:hover {
            transform: none;
            box-shadow: none;
            opacity: 0.9;
        }

        .featured-name {
            min-height: 46px;
        }

        .category-title,
        .collection-title,
        .featured-title,
        .testimonial-title {
            text-wrap: balance;
        }

        .category-card {
            padding-bottom: 10px;
        }

        /* ── Fresh Promo Section Styled for Dynamic Backend Banners ── */
        .promo-section {
            width: 100%;
            max-width: 1360px;
            margin: 48px auto 72px;
            padding: 0 100px;
            box-sizing: border-box;
            /* CRITICAL: Disconnect layout from content feedback */
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            contain: layout;
            position: relative;
            background: none !important;
        }

        .promo-swiper {
            width: 100% !important;
            overflow: hidden !important;
            padding: 10px 0 50px;
            /* Room for pagination dots */
            /* CRITICAL: Prevent grid-based width expansion */
            min-width: 0;
        }

        .promo-swiper .swiper-wrapper {
            align-items: stretch;
            display: flex;
        }

        .promo-swiper .swiper-slide {
            height: auto;
            display: flex;
            align-items: stretch;
            justify-content: center;
            /* CRITICAL: Break the infinite growth loop in Swiper */
            min-width: 0;
            max-width: 100% !important;
        }

        /* Single Promo Polish - Centered with 50% width on Desktop */
        .promo-swiper.single-promo-mode .swiper-wrapper {
            justify-content: center !important;
            transform: none !important; /* CRITICAL: Stop Swiper from shifting the single slide */
            transition: none !important;
            display: flex !important;
        }

        .promo-swiper.single-promo-mode .swiper-slide {
            max-width: 50% !important;
            width: 50% !important;
            margin: 0 auto !important;
            flex-shrink: 0 !important;
        }

        .promo-banner {
            display: block;
            width: 100%;
            position: relative;
            text-decoration: none;
            border-radius: 20px;
            overflow: hidden;
            /* Surgical Gradient Border: only border, no background color */
            border: 2px solid transparent;
            background: linear-gradient(white, white) padding-box,
                        linear-gradient(135deg, #a91b43 0%, #ef9f29 52%, #a91b43 100%) border-box;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            /* Standard Proportions for 2-column view */
            aspect-ratio: 16 / 9;
        }

        .promo-banner:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .promo-banner img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            border-radius: 17px; /* Slightly smaller for border gap */
        }

        /* Nav positioning remains standard but closer to section */
        .promo-swiper-wrap {
            position: relative;
            width: 100%;
        }

        .promo-prev {
            left: -50px;
        }

        .promo-next {
            right: -50px;
        }

        .promo-pagination {
            bottom: 0 !important;
        }

        .promo-pagination .swiper-pagination-bullet {
            background: #a91b43;
            opacity: 0.3;
        }

        .promo-pagination .swiper-pagination-bullet-active {
            opacity: 1;
            background: #a91b43;
        }

        @media (max-width: 1024px) {
            .promo-section {
                padding: 0 12px;
                margin-top: 24px;
                margin-bottom: 48px;
            }

            .promo-prev,
            .promo-next {
                display: none;
            }

            .promo-banner {
                aspect-ratio: 16 / 9;
                max-height: none;
                max-width: none;
                /* Allow full width on mobile */
            }

            .promo-swiper.single-promo-mode .swiper-slide {
                max-width: 90% !important;
                width: 90% !important;
            }

            .promo-banner img {
                /* aspect-ratio: auto; */
            }
        }

        .testimonial-section {
            margin-top: 16px;
            margin-bottom: 80px;
        }

        .testimonial-swiper {
            padding: 12px 10px 8px;
            /* Slightly more side padding */
            overflow: hidden;
        }

        .testimonial-swiper .swiper-wrapper {
            align-items: stretch;
        }

        .testimonial-swiper .swiper-slide {
            height: auto;
            display: flex;
        }

        .testimonial-vector-wrap {
            margin-bottom: 32px;
        }

        .testimonial-card {
            width: 100% !important;
            flex: 1 1 auto !important;
            min-width: 0;
            min-height: 320px;
            border-radius: 18px;
            background: #e9e9e9;
            padding: 28px 24px 24px;
            box-sizing: border-box;
        }

        .testimonial-card-title {
            min-height: auto;
            margin-bottom: 18px;
            line-height: 1.25;
        }

        .testimonial-text {
            font-size: 16px;
            line-height: 1.7;
        }

        .testimonial-name {
            margin-top: auto;
            padding-top: 16px;
        }

        /* testimonial nav handled in shared block above */

        @media (min-width: 769px) and (max-width: 1024px) {
            .hero-swiper .swiper-slide {
                background: #f7f1e5;
            }

            .hero-swiper .swiper-slide img {
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                object-fit: contain !important;
                display: block;
            }
        }

        @media (max-width: 768px) {
            .hero {
                margin-bottom: 20px;
            }

            /* ── Nav buttons: smaller on mobile ── */
            .collection-next,
            .collection-prev,
            .category-next,
            .category-prev,
            .testimonial-next,
            .testimonial-prev,
            .promo-next,
            .promo-prev,
            .featured-next,
            .featured-prev,
            .hero-next,
            .hero-prev {
                width: 32px !important;
                height: 32px !important;
            }

            .collection-next::after,
            .collection-prev::after,
            .category-next::after,
            .category-prev::after,
            .testimonial-next::after,
            .testimonial-prev::after,
            .promo-next::after,
            .promo-prev::after,
            .featured-next::after,
            .featured-prev::after,
            .hero-next::after,
            .hero-prev::after {
                font-size: 13px !important;
            }

            /* ── Section spacing ── */
            /* ── Global Section Padding (Mobile) ── */
            .collection-section,
            .category-section {
                padding-left: 45px !important;
                padding-right: 45px !important;
                margin-bottom: 30px;
            }

            .category-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .featured-inner,
            .promo-section,
            .testimonial-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
                margin-bottom: 30px;
            }

            /* ── Special case for featured-inner which is already a sub-container ── */
            .featured-section {
                margin-bottom: 30px;
            }

            /* Nav buttons sit on the left/right edge of the section padding area */
            .collection-prev {
                left: -35px;
                top: 40%;
            }

            .collection-next {
                right: -35px;
                top: 40%;
            }

            /* No inner swiper padding — full width of padded container */
            .collection-swiper {
                padding-left: 0;
                padding-right: 0;
            }

            .collection-card,
            .featured-card {
                align-items: center !important;
                text-align: center !important;
            }

            .collection-name,
            .featured-name {
                min-height: auto;
                font-size: 15px;
                margin-top: 12px !important;
                margin-bottom: 12px !important;
                width: 100%;
            }

            .collection-cta,
            .featured-footer {
                position: static !important;
                margin-top: 8px !important;
                align-self: center !important;
            }

            /* ── Featured / New Arrivals section ── */
            .featured-title {
                font-size: 22px !important;
            }

            .featured-subtitle {
                font-size: 13px !important;
                padding-bottom: 16px !important;
            }

            .featured-swiper-container {
                margin-top: 14px;
            }

            /* Section gets its own side padding; swiper fills the full padded container */
            .featured-inner {
                padding-left: 40px !important;
                padding-right: 40px !important;
            }

            .featured-swiper {
                padding-left: 0;
                padding-right: 0;
            }

            /* Nav buttons on edge of padded area */
            .featured-prev {
                left: -35px;
                top: 40%;
            }

            .featured-next {
                right: -35px;
                top: 40%;
            }

            /* Hide decorative flourish images on mobile — they overflow */
            .featured-decor {
                display: none !important;
            }

            /* Handled in global section padding block */

            .category-swiper {
                padding-left: 0;
                padding-right: 0;
            }

            .category-swiper-wrap {
                overflow: hidden;
                padding: 0 12px;
            }

            .category-swiper .swiper-slide {
                display: flex !important;
                justify-content: center !important;
                align-items: stretch !important;
            }

            .category-swiper .swiper-wrapper {
                align-items: flex-start;
            }

            .category-link {
                flex: none !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .category-card {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding-bottom: 10px;
                height: 100% !important;
                width: 100% !important;
                max-width: 156px !important;
                margin: 0 auto !important;
            }

            .category-image-shell {
                width: 136px !important;
                height: 136px !important;
                margin: 0 auto !important;
            }

            .category-name {
                font-size: clamp(11px, 3vw, 13px) !important;
                margin-top: 8px !important;
                white-space: normal;
                line-height: 1.35;
                min-height: 2.7em;
                text-wrap: balance;
            }

            .category-prev {
                left: 0 !important;
                top: 45%;
            }

            .category-next {
                right: 0 !important;
                top: 45%;
            }

            @media (max-width: 575px) {
                .category-section {
                    padding-left: 16px !important;
                    padding-right: 16px !important;
                }

                .category-swiper-wrap {
                    padding: 0 6px;
                }

                .category-card {
                    max-width: 148px !important;
                }

                .category-image-shell {
                    width: 128px !important;
                    height: 128px !important;
                }
            }

            @media (max-width: 399px) {
                .category-section {
                    padding-left: 12px !important;
                    padding-right: 12px !important;
                }

                .category-swiper-wrap {
                    padding: 0 4px;
                }

                .category-card {
                    max-width: 132px !important;
                }

                .category-prev,
                .category-next {
                    width: 28px !important;
                    height: 28px !important;
                }

                .category-prev::after,
                .category-next::after {
                    font-size: 11px !important;
                }

                .category-image-shell {
                    width: 112px !important;
                    height: 112px !important;
                }

                .category-name {
                    font-size: 10px !important;
                }
            }

            /* ── Promo section (Offer & Wedding) ── */
            .promo-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .promo-swiper-wrap {
                overflow: hidden;
                padding: 0 24px;
            }

            .promo-swiper {
                padding-left: 0;
                padding-right: 0;
            }

            .promo-card {
                border-radius: 20px !important;
            }

            .promo-card::before,
            .promo-card-media {
                border-radius: 17px !important;
            }

            .promo-card-media {
                width: 100% !important;
                aspect-ratio: 16 / 10;
            }

            .promo-prev {
                left: 0 !important;
                top: 50%;
                z-index: 1001;
            }

            .promo-next {
                right: 0 !important;
                top: 50%;
                z-index: 1001;
            }

            .testimonial-swiper {
                padding-left: 0;
                padding-right: 0;
            }

            .testimonial-card {
                min-height: auto;
                padding: 24px 20px;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .testimonial-prev {
                left: 2px;
                top: 50%;
                z-index: 1001;
            }

            .testimonial-next {
                right: 2px;
                top: 50%;
                z-index: 1001;
            }

            .collection-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
            }

            .collection-swiper-wrap {
                overflow: hidden;
                padding: 0 34px;
            }

            .collection-swiper .swiper-slide {
                display: flex !important;
                justify-content: center !important;
                align-items: stretch !important;
            }

            .collection-card {
                flex: none !important;
                width: 100% !important;
                max-width: 250px !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            .collection-image-wrap {
                width: 100% !important;
            }

            .collection-cta {
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
                width: 96px !important;
                height: 30px !important;
                font-size: 13px !important;
                margin-top: 8px !important;
                margin-left: auto !important;
                margin-right: auto !important;
                visibility: visible !important;
                opacity: 1 !important;
            }

            .collection-prev,
            .collection-next {
                display: flex !important;
                width: 32px !important;
                height: 32px !important;
                top: 38% !important;
                z-index: 1002 !important;
                background: #fff !important;
            }

            .collection-prev {
                left: 0 !important;
            }

            .collection-next {
                right: 0 !important;
            }
        }

        /* Category Slider Alignment Pixes */
        .category-swiper .swiper-slide {
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .category-image-shell {
            width: 110px !important;
            height: 110px !important;
            margin: 0 auto;
            border-radius: 50%;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-image {
            width: 92% !important;
            height: 92% !important;
        }

        .category-name {
            font-size: 14px !important;
            margin-top: 15px !important;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            line-height: 1.2;
            width: 100%;
        }

        .sub-category-card .category-name {
            color: #f3a349 !important;
        }

        .sub-category-card .category-ring {
            background: none !important;
            border: 2px solid #f3a349 !important;
            -webkit-mask: none !important;
            mask: none !important;
        }

        .category-ring {
            border-width: 2px !important;
        }

        /* Price alignment fixes */
        .featured-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            margin-top: 12px;
        }

        .featured-price-wrap {
            display: flex;
            align-items: baseline;
            gap: 8px;
        }

        .featured-price {
            color: #744707;
            font-size: 16px !important;
            font-weight: 700 !important;
        }

        .featured-card .old-price {
            font-size: 13px !important;
            color: #888 !important;
            text-decoration: line-through;
        }
    </style>
@endpush

@section('content')
    <section class="hero" aria-label="Hero Banner">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                @forelse($banners as $banner)
                    <div class="swiper-slide">
                        <img src="{{ asset('uploads/' . $banner->image) }}" alt="{{ $banner->title ?? 'Promo Banner' }}" />
                    </div>
                @empty
                    <div class="swiper-slide">
                        <img src="{{ asset('images/banner 1.png') }}" alt="Default Banner" />
                    </div>
                @endforelse
            </div>
            <!-- Add Navigation and Pagination -->
            <div class="swiper-button-next hero-next"></div>
            <div class="swiper-button-prev hero-prev"></div>
            <div class="swiper-pagination hero-pagination"></div>
        </div>
    </section>

    @if ($subCategories->count() > 0)
        <section class="collection-section" aria-labelledby="saree-collections-title">
            <h2 id="saree-collections-title" class="collection-title">Saree Collections</h2>
            <div class="collection-swiper-wrap">
                <div class="swiper collection-swiper">
                    <div class="swiper-wrapper">
                        @forelse($subCategories as $sub)
                            <div class="swiper-slide">
                                <article class="collection-card">
                                    <a href="{{ route('category.show', $sub->slug) }}" class="card-link-wrapper">
                                        <div class="collection-image-wrap">
                                            <img src="{{ $sub->image ? asset('uploads/' . $sub->image) : asset('images/Image.png') }}"
                                                alt="{{ $sub->name }}" />
                                        </div>
                                        <h3 class="collection-name">{{ $sub->name }}</h3>
                                    </a>
                                    <button class="collection-cta" type="button"
                                        onclick="window.location.href='{{ route('category.show', $sub->slug) }}'">Shop
                                        Now</button>
                                </article>
                            </div>
                        @empty
                            No Sub Categories
                        @endforelse
                    </div>
                </div>
                <!-- Navigation outside swiper for correct button positioning -->
                <div class="swiper-button-next collection-next"></div>
                <div class="swiper-button-prev collection-prev"></div>
            </div>
        </section>
    @endif

    @if ($featuredProducts->count() > 0)
        <section class="featured-section" aria-labelledby="featured-title">
            <img class="featured-decor featured-decor-left"
                src="{{ asset('images/177ac6ca-e05e-455e-b85a-ac15d09dd31f 2.png') }}" alt="" />
            <img class="featured-decor featured-decor-right"
                src="{{ asset('images/177ac6ca-e05e-455e-b85a-ac15d09dd31f 1.png') }}" alt="" />

            <div class="featured-inner">
                <h2 id="featured-title" class="featured-title">New Arrivals</h2>
                <p class="featured-subtitle">Fresh weaves, added daily - discover sarees handwoven just for you</p>

                <div class="featured-swiper-container" style="position: relative;">
                    <div class="swiper featured-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($featuredProducts as $product)
                                <div class="swiper-slide">
                                    <article class="featured-card">
                                        <a href="{{ route('product.show', $product->slug) }}" class="card-link-wrapper">
                                            <div class="featured-media">
                                                @php
                                                    $productImage = 'images/pro.png';
                                                    if (
                                                        $product->images &&
                                                        is_array($product->images) &&
                                                        count($product->images) > 0
                                                    ) {
                                                        $productImage = 'uploads/' . $product->images[0];
                                                    } elseif ($product->image_path) {
                                                        $productImage = 'images/' . $product->image_path;
                                                    }
                                                @endphp
                                                <img src="{{ asset($productImage) }}" alt="{{ $product->name }}" />
                                                @if ($product->is_featured)
                                                    <span class="featured-badge">New Arrival</span>
                                                @endif
                                                @if ($product->discount_percent > 0)
                                                    <span class="featured-badge"
                                                        style="top: 40px;">{{ round($product->discount_percent) }}%
                                                        Off</span>
                                                @endif
                                            </div>
                                            <h3 class="featured-name">{{ $product->name }}</h3>
                                        </a>
                                        <div class="featured-footer" style="margin-top: -19px;">
                                            <div class="featured-price-wrap">
                                                <span class="featured-price">&#8377; {{ number_format($product->price, 0) }}</span>
                                                @if ($product->regular_price > $product->price)
                                                    <span class="old-price">₹{{ number_format($product->regular_price, 0) }}</span>
                                                @endif
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
                                                <button class="wishlist-btn" type="button"
                                                    data-product-id="{{ $product->id }}" aria-label="Add to wishlist"
                                                    style="background: none; border: none; cursor: pointer; padding: 0; display: flex; align-items: center;">
                                                    <svg width="20" height="20" viewBox="0 0 24 24"
                                                        fill="{{ $inWishlist ? '#A91B43' : '#666' }}">
                                                        <path
                                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                    </svg>
                                                </button>
                                                <button class="featured-cart add-to-cart-btn" type="button"
                                                    data-product-id="{{ $product->id }}"
                                                    aria-label="Add {{ $product->name }} to cart">
                                                    <img src="{{ asset('images/Vector.svg') }}" alt="" />
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Add Navigation Outside Swiper -->
                    <div class="swiper-button-next featured-next"></div>
                    <div class="swiper-button-prev featured-prev"></div>
                </div>

                <div class="featured-progress" id="featuredProgress">
                    <span id="currentSlide">01</span>
                    <div class="featured-progress-track" id="progressTrack">
                        <div class="featured-progress-bg"></div>
                        <div class="featured-progress-fill" id="progressFill"></div>
                    </div>
                    <span>{{ sprintf('%02d', count($featuredProducts)) }}</span>
                </div>
            </div>
        </section>
    @endif

    @foreach($offerCollections as $collection)
        <section class="featured-section" aria-labelledby="collection-title-{{ $collection->id }}">
            <div class="featured-inner">
                <h2 id="collection-title-{{ $collection->id }}" class="featured-title">{{ $collection->name }}</h2>
                <p class="featured-subtitle">Curated collections just for you</p>

                <div class="featured-swiper-container" style="position: relative;">
                    <div class="swiper collection-products-swiper">
                        <div class="swiper-wrapper">
                            @foreach ($collection->products as $product)
                                <div class="swiper-slide">
                                    <article class="featured-card">
                                        <a href="{{ route('product.show', $product->slug) }}" class="card-link-wrapper">
                                            <div class="featured-media">
                                                @php
                                                    $productImage = 'images/pro.png';
                                                    if ($product->images && is_array($product->images) && count($product->images) > 0) {
                                                        $productImage = 'uploads/' . $product->images[0];
                                                    } elseif ($product->image_path) {
                                                        $productImage = 'images/' . $product->image_path;
                                                    }
                                                @endphp
                                                <img src="{{ asset($productImage) }}" alt="{{ $product->name }}" />
                                                @if ($product->discount_percent > 0)
                                                    <span class="featured-badge" style="top: 10px;">{{ round($product->discount_percent) }}% Off</span>
                                                @endif
                                            </div>
                                            <h3 class="featured-name">{{ $product->name }}</h3>
                                        </a>
                                        <div class="featured-footer">
                                            <div class="featured-price-wrap">
                                                <span class="featured-price">&#8377; {{ number_format($product->price, 0) }}</span>
                                                @if ($product->regular_price > $product->price)
                                                    <span class="old-price">₹{{ number_format($product->regular_price, 0) }}</span>
                                                @endif
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
                                                <button class="wishlist-btn" type="button" data-product-id="{{ $product->id }}">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="{{ $inWishlist ? '#A91B43' : '#666' }}">
                                                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                    </svg>
                                                </button>
                                                <button class="featured-cart add-to-cart-btn" data-product-id="{{ $product->id }}">
                                                    <img src="{{ asset('images/Vector.svg') }}" alt="" />
                                                </button>
                                            </div>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Unique Nav for this swiper -->
                    <div class="swiper-button-next featured-next collection-next-{{ $collection->id }}"></div>
                    <div class="swiper-button-prev featured-prev collection-prev-{{ $collection->id }}"></div>
                </div>
            </div>
            <!-- Ensure no gap between consecutive featured sections by using simple structure here -->
        </section>
    @endforeach

    @if($categories->count() > 0)
        <section class="category-section" aria-labelledby="browse-categories-title">
            <h2 id="browse-categories-title" class="category-title">Browse Our Categories</h2>
            <div class="category-swiper-wrap">
                <div class="swiper category-swiper">
                    <div class="swiper-wrapper">
                        @foreach ($categories as $cat)
                            <div class="swiper-slide">
                                <a class="category-link" href="{{ route('category.show', $cat->slug) }}"
                                    style="text-decoration: none;">
                                    <article class="category-card">
                                        <div class="category-image-shell">
                                            <img class="category-image"
                                                src="{{ $cat->image ? asset('uploads/' . $cat->image) : asset('images/Rectangle 9.png') }}"
                                                alt="{{ $cat->name }}" />
                                            <span class="category-ring"></span>
                                        </div>
                                        <h3 class="category-name">{{ $cat->name }}</h3>
                                    </article>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Navigation outside swiper for correct button positioning -->
                <div class="swiper-button-next category-next"></div>
                <div class="swiper-button-prev category-prev"></div>
            </div>
        </section>
    @endif


    <!-- Fully Reconstructed Promo Section (Backend Dynamic Structure) -->
    <section class="promo-section" aria-label="Promotions">
        @if (isset($ads) && $ads->count() > 0)
            <div class="promo-swiper-wrap">
                <div class="swiper promo-swiper {{ $ads->count() == 1 ? 'single-promo-mode' : '' }}">
                    <div class="swiper-wrapper">
                        @foreach ($ads->take(6) as $ad)
                            <div class="swiper-slide">
                                <a class="promo-banner" href="{{ $ad->link ?: route('shop') }}"
                                    @if ($ad->open_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                    aria-label="{{ $ad->title ?: 'Promotional banner' }}">
                                    <img src="{{ asset('uploads/' . $ad->image) }}"
                                        alt="{{ $ad->title ?: 'Promotional banner' }}" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <!-- Pagination dots like testimonial -->
                    <div class="swiper-pagination promo-pagination"></div>
                </div>
                <div class="swiper-button-next promo-next"></div>
                <div class="swiper-button-prev promo-prev"></div>
            </div>
        @endif
    </section>


    @if ($testimonials->count() > 0)
        <section class="testimonial-section" aria-labelledby="testimonial-title">
            <p class="testimonial-kicker">Testimonial</p>
            <h2 id="testimonial-title" class="testimonial-title">Speaking from their hearts</h2>
            <div class="testimonial-vector-wrap">
                <img class="testimonial-vector" src="{{ asset('images/Vector2.svg') }}" alt="Quote icon" />
            </div>

            <div class="testimonial-swiper-wrap">
                <div class="swiper testimonial-swiper">
                    <div class="swiper-wrapper">
                        @forelse($testimonials as $testimonial)
                            <div class="swiper-slide">
                                <article class="testimonial-card">
                                    <div class="mb-4">
                                        <p class="testimonial-name !mt-0 !pt-0">{{ $testimonial->name }}</p>
                                        <div class="flex items-center gap-1">
                                            @for ($i = 0; $i < ($testimonial->rating ?? 5); $i++)
                                                <i class="fas fa-star text-amber-500 text-[8px]"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <h3 class="testimonial-card-title">
                                    <p class="testimonial-text">{{ $testimonial->review }}</p>
                                </article>
                            </div>
                        @empty
                            No reviews yet
                        @endforelse
                    </div>
                </div>
                <!-- Navigation outside swiper for correct button positioning -->
                <div class="swiper-button-next testimonial-next"></div>
                <div class="swiper-button-prev testimonial-prev"></div>
            </div>
        </section>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                // Initialize Swiper for each section
                new Swiper('.hero-swiper', {
                    slidesPerView: 1,
                    loop: true,
                    autoplay: {
                        delay: 5000,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.hero-next',
                        prevEl: '.hero-prev',
                    },
                    pagination: {
                        el: '.hero-pagination',
                        clickable: true,
                    }
                });

                new Swiper('.collection-swiper', {
                    slidesPerView: 1,
                    slidesPerGroup: 1,
                    spaceBetween: 10,
                    loop: true,
                    watchOverflow: false,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.collection-next',
                        prevEl: '.collection-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2
                        },
                        768: {
                            slidesPerView: 3
                        },
                        1024: {
                            slidesPerView: 4
                        },
                        1280: {
                            slidesPerView: 4
                        },
                    }
                });

                const categoryWrapper = document.querySelector('.category-swiper .swiper-wrapper');
                const originalCategorySlides = categoryWrapper ? Array.from(categoryWrapper.children) : [];

                const originalCategoryCount = originalCategorySlides.length;

                if (categoryWrapper && originalCategoryCount > 1) {
                    while (categoryWrapper.children.length < Math.max(originalCategoryCount * 3, 18)) {
                        originalCategorySlides.forEach((slide) => {
                            const clone = slide.cloneNode(true);
                            clone.classList.add('category-slide-clone');
                            categoryWrapper.appendChild(clone);
                        });
                    }
                }

                const categorySlideCount = document.querySelectorAll('.category-swiper .swiper-slide').length;
                const safeCategoryCount = Math.max(originalCategoryCount, 1);
                const enableCategoryLoop = originalCategoryCount > 1;

                new Swiper('.category-swiper', {
                    slidesPerView: Math.min(2, safeCategoryCount),
                    slidesPerGroup: 1,
                    spaceBetween: 8,
                    speed: 900,
                    centeredSlides: false,
                    centeredSlidesBounds: false,
                    centerInsufficientSlides: true,
                    loop: enableCategoryLoop,
                    loopedSlides: categorySlideCount,
                    loopAdditionalSlides: categorySlideCount,
                    loopPreventsSliding: true,
                    roundLengths: true,
                    watchOverflow: false,
                    observer: true,
                    observeParents: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                        pauseOnMouseEnter: false,
                        waitForTransition: true,
                    },
                    navigation: {
                        nextEl: '.category-next',
                        prevEl: '.category-prev',
                    },
                    breakpoints: {
                        0: {
                            slidesPerView: Math.min(2, safeCategoryCount),
                            spaceBetween: -4
                        },
                        400: {
                            slidesPerView: Math.min(2, safeCategoryCount),
                            spaceBetween: -2
                        },
                        480: {
                            slidesPerView: Math.min(2, safeCategoryCount),
                            spaceBetween: 0
                        },
                        640: {
                            slidesPerView: Math.min(3, safeCategoryCount),
                            spaceBetween: 12
                        },
                        768: {
                            slidesPerView: Math.min(4, safeCategoryCount),
                            spaceBetween: 14
                        },
                        1024: {
                            slidesPerView: Math.min(5, safeCategoryCount),
                            slidesPerGroup: 1,
                            spaceBetween: 12
                        },
                        1280: {
                            slidesPerView: Math.min(6, safeCategoryCount),
                            slidesPerGroup: 1,
                            spaceBetween: 12
                        },
                    }
                });

                new Swiper('.testimonial-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    centeredSlides: false,
                    watchOverflow: false,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.testimonial-next',
                        prevEl: '.testimonial-prev',
                    },
                    breakpoints: {
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 24
                        },
                        1280: {
                            slidesPerView: 3,
                            spaceBetween: 40
                        },
                    }
                });

                const promoSlideCount = document.querySelectorAll('.promo-swiper .swiper-slide').length;
                if (promoSlideCount > 0) {
                    new Swiper('.promo-swiper', {
                        roundLengths: true,
                        watchOverflow: true,
                        slidesPerView: 1,
                        spaceBetween: 16,
                        loop: promoSlideCount > 1,
                        centeredSlides: false,
                        autoplay: promoSlideCount > 1 ? {
                            delay: 4000,
                            disableOnInteraction: false
                        } : false,
                        pagination: {
                            el: '.promo-pagination',
                            clickable: true,
                        },
                        navigation: {
                            nextEl: '.promo-next',
                            prevEl: '.promo-prev',
                        },
                        breakpoints: {
                            0: {
                                slidesPerView: promoSlideCount > 1 ? 1.15 : 1,
                                spaceBetween: 12,
                                centeredSlides: promoSlideCount > 1,
                            },
                            768: {
                                slidesPerView: promoSlideCount >= 2 ? 2 : 1,
                                spaceBetween: 24,
                            },
                            1024: {
                                slidesPerView: promoSlideCount >= 2 ? 2 : 1,
                                spaceBetween: 40,
                            }
                        }
                    });
                }

                const featuredSwiper = new Swiper('.featured-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 18,
                    loop: {{ count($featuredProducts) > 1 ? 'true' : 'false' }},
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.featured-next',
                        prevEl: '.featured-prev',
                    },
                    breakpoints: {
                        640: {
                            slidesPerView: 2
                        },
                        768: {
                            slidesPerView: 3
                        },
                        1024: {
                            slidesPerView: 4
                        },
                    },
                    on: {
                        init: function() {
                            updateProgressBar(this);
                        },
                        slideChange: function() {
                            updateProgressBar(this);
                        },
                        resize: function() {
                            updateProgressBar(this);
                        }
                    }
                });

                // Initialize Offer Collection Swipers
                @foreach($offerCollections as $collection)
                new Swiper('.collection-products-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 18,
                    loop: {{ $collection->products->count() > 1 ? 'true' : 'false' }},
                    autoplay: {
                        delay: 3500 + {{ $loop->index * 500 }},
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.collection-next-{{ $collection->id }}',
                        prevEl: '.collection-prev-{{ $collection->id }}',
                    },
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        1024: { slidesPerView: 4 },
                    }
                });
                @endforeach

                function getFeaturedProductCount(swiper) {
                    return swiper.slides.filter(slide => !slide.classList.contains('swiper-slide-duplicate')).length;
                }

                function updateProgressBar(swiper) {
                    const progressFill = document.getElementById('progressFill');
                    const currentSlide = document.getElementById('currentSlide');

                    if (!progressFill || !currentSlide) return;

                    const totalProducts = getFeaturedProductCount(swiper);
                    const currentIndex = Math.min(swiper.realIndex, Math.max(totalProducts - 1, 0));
                    const progress = totalProducts <= 1 ? 100 : (currentIndex / (totalProducts - 1)) * 100;

                    progressFill.style.width = `${Math.min(Math.max(progress, 0), 100)}%`;
                    currentSlide.textContent = String(currentIndex + 1).padStart(2, '0');
                }

                function seekFeaturedProducts(event) {
                    const progressTrack = document.getElementById('progressTrack');
                    if (!progressTrack) return;

                    const rect = progressTrack.getBoundingClientRect();
                    const clickOffset = Math.min(Math.max(event.clientX - rect.left, 0), rect.width);
                    const clickRatio = rect.width === 0 ? 0 : clickOffset / rect.width;
                    const totalProducts = getFeaturedProductCount(featuredSwiper);
                    const targetIndex = totalProducts <= 1 ? 0 : Math.round(clickRatio * (totalProducts - 1));

                    featuredSwiper.slideToLoop(targetIndex);
                }

                const progressTrack = document.getElementById('progressTrack');
                if (progressTrack) {
                    progressTrack.style.cursor = 'pointer';
                    progressTrack.addEventListener('click', seekFeaturedProducts);
                }

                // AJAX Add to Cart for Home Page buttons
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('.add-to-cart-btn');
                    if (btn && !btn.closest('#pdpForm')) {
                        const productId = btn.getAttribute('data-product-id');

                        fetch(`{{ url('cart/add') }}/${productId}`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    toastr.success(data.message || 'Added to cart.');
                                    if (window.updateMiniCart) window.updateMiniCart();
                                } else {
                                    toastr.error(data.message || 'Error adding to cart.');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                toastr.error('Something went wrong.');
                            });
                    }
                });
            });
        </script>
    @endpush
@endsection
