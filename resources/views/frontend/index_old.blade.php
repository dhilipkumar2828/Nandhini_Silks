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
            margin-left: auto;
            margin-right: auto;
            padding-left: 100px;
            padding-right: 100px;
            box-sizing: border-box;
            overflow: visible;
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
        }

        .featured-inner {
            max-width: 1360px;
            margin: 0 auto;
            padding: 0 100px;
            box-sizing: border-box;
        }

        .collection-section,
        .featured-section,
        .category-section {
            margin-top: 0;
            margin-bottom: 50px;
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
        .testimonial-swiper {
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
        .testimonial-swiper-wrap {
            position: relative;
        }

        /* ── All nav buttons: vertically centered, absolutely placed ── */
        .collection-next,
        .collection-prev,
        .category-next,
        .category-prev,
        .testimonial-next,
        .testimonial-prev,
        .featured-next,
        .featured-prev {
            position: absolute;
            top: 40%;
            transform: translateY(-50%);
            z-index: 1000;
        }

        .category-prev,
        .featured-prev,
        .collection-prev,
        .testimonial-prev {
            left: -80px;
        }

        .category-next,
        .featured-next,
        .collection-next,
        .testimonial-next {
            right: -80px;
        }

        .testimonial-prev,
        .testimonial-next {
            top: 50%; /* Center on the full card for testimonials */
        }

        .collection-next,
        .collection-prev,
        .category-next,
        .category-prev,
        .testimonial-next,
        .testimonial-prev,
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

        .promo-section {
            margin-top: 8px;
            margin-bottom: 72px;
        }

        .offer-content,
        .wedding-content {
            height: 100%;
        }

        .testimonial-section {
            margin-top: 16px;
            margin-bottom: 80px;
        }

        .testimonial-swiper {
            padding: 12px 10px 8px; /* Slightly more side padding */
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

            .featured-inner,
            .promo-section,
            .testimonial-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
                margin-bottom: 30px;
            }

            /* ── Special case for featured-inner which is already a sub-container ── */
            .featured-section {
                margin-bottom: 5px;
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
                margin: 0 auto !important;
            }

            .category-image-shell {
                width: 85px !important;
                height: 85px !important;
                margin: 0 auto !important;
            }

            .category-name {
                font-size: 13px !important;
                margin-top: 8px !important;
                white-space: nowrap;
            }

            .category-prev {
                left: -35px;
                top: 45%;
            }

            .category-next {
                right: -35px;
                top: 45%;
            }

            /* ── Promo section (Offer & Wedding) ── */
            .promo-section {
                padding-left: 20px !important;
                padding-right: 20px !important;
                gap: 24px !important;
            }

            .offer-card,
            .wedding-card {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                min-height: auto !important;
            }

            .offer-image-wrap,
            .wedding-image-wrap {
                position: static !important;
                width: 100% !important;
                height: 200px !important;
                overflow: hidden !important;
            }

            .offer-image,
            .wedding-image {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: top !important;
            }

            .offer-content,
            .wedding-content {
                width: 100% !important;
                padding: 24px 15px !important;
                align-items: center !important;
            }

            .offer-title,
            .offer-text,
            .wedding-text {
                max-width: 100% !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }

            .wedding-heading-svg {
                width: 180px !important;
                height: auto !important;
                margin: 0 auto 10px !important;
            }

            .offer-link,
            .wedding-cta {
                align-self: center !important;
                margin-top: 10px !important;
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
        }
    </style>
@endpush

@section('content')
    <section class="hero" aria-label="Hero Banner">
        <div class="swiper hero-swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <img src="{{ asset('images/banner 1.png') }}" alt="Banner 1" />
                </div>
                <div class="swiper-slide">
                    <img src="{{ asset('images/banner 1.jpg') }}" alt="Banner 2" />
                </div>
            </div>
            <!-- Add Navigation and Pagination -->
            <div class="swiper-button-next hero-next"></div>
            <div class="swiper-button-prev hero-prev"></div>
            <div class="swiper-pagination hero-pagination"></div>
        </div>
    </section>

    <section class="collection-section" aria-labelledby="saree-collections-title">
        <h2 id="saree-collections-title" class="collection-title">Saree Collections</h2>
        <div class="collection-swiper-wrap">
            <div class="swiper collection-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <article class="collection-card">
                            <a href="{{ url('sarees') }}" class="card-link-wrapper">
                                <div class="collection-image-wrap">
                                    <img src="{{ asset('images/Image.png') }}" alt="Pure Silk Saree" />
                                </div>
                                <h3 class="collection-name">Pure Silk Saree</h3>
                            </a>
                            <button class="collection-cta" type="button"
                                onclick="window.location.href='{{ url('sarees') }}'">Shop
                                Now</button>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="collection-card">
                            <a href="{{ url('sarees') }}" class="card-link-wrapper">
                                <div class="collection-image-wrap">
                                    <img src="{{ asset('images/Image (1).png') }}" alt="Tissue Silk Saree" />
                                </div>
                                <h3 class="collection-name">Tissue Silk Saree</h3>
                            </a>
                            <button class="collection-cta" type="button"
                                onclick="window.location.href='{{ url('sarees') }}'">Shop
                                Now</button>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="collection-card">
                            <a href="{{ url('sarees') }}" class="card-link-wrapper">
                                <div class="collection-image-wrap">
                                    <img src="{{ asset('images/Image (2).png') }}" alt="Cotton Sarees" />
                                </div>
                                <h3 class="collection-name">Cotton Sarees</h3>
                            </a>
                            <button class="collection-cta" type="button"
                                onclick="window.location.href='{{ url('sarees') }}'">Shop
                                Now</button>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="collection-card">
                            <a href="{{ url('sarees') }}" class="card-link-wrapper">
                                <div class="collection-image-wrap">
                                    <img src="{{ asset('images/Image (3).png') }}" alt="Soft Silk Saree" />
                                </div>
                                <h3 class="collection-name">Soft Silk Saree</h3>
                            </a>
                            <button class="collection-cta" type="button"
                                onclick="window.location.href='{{ url('sarees') }}'">Shop
                                Now</button>
                        </article>
                    </div>

                    <div class="swiper-slide">
                        <article class="collection-card">
                            <a href="{{ url('product/exquisite-silk-saree') }}" class="card-link-wrapper">
                                <div class="collection-image-wrap">
                                    <img src="{{ asset('images/product1_1.jpg') }}" alt="Exquisite Silk Saree" />
                                </div>
                                <h3 class="collection-name">Exquisite Silk Saree</h3>
                            </a>
                            <button class="collection-cta" type="button"
                                onclick="window.location.href='{{ url('product/exquisite-silk-saree') }}'">Shop
                                Now</button>
                        </article>
                    </div>
                </div>
            </div>
            <!-- Navigation outside swiper for correct button positioning -->
            <div class="swiper-button-next collection-next"></div>
            <div class="swiper-button-prev collection-prev"></div>
        </div>
    </section>

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
                                    <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
                                    <a href="{{ route('product.show', $product->slug) }}" class="card-link-wrapper">
                                        <div class="featured-media">
                                            @php
                                                $fallbackImage = 'images/pro' . ($loop->index % 4 > 0 ? $loop->index % 4 : '') . '.png';
                                                if ($loop->index % 4 == 0) {
                                                    $fallbackImage = 'images/pro3.png';
                                                }
                                                if ($loop->index % 4 == 1) {
                                                    $fallbackImage = 'images/pro2.png';
                                                }
                                                if ($loop->index % 4 == 2) {
                                                    $fallbackImage = 'images/pro1.png';
                                                }
                                                if ($loop->index % 4 == 3) {
                                                    $fallbackImage = 'images/pro.png';
                                                }
                                            @endphp
                                            <img src="{{ $product->image_path ? asset('images/' . $product->image_path) : asset($fallbackImage) }}"
                                                alt="{{ $product->name }}" />
                                            @if ($loop->index % 4 == 0)
                                                <span class="featured-badge">New Arrival</span>
                                            @elseif($loop->index % 4 == 1)
                                                <span class="featured-badge">10% Off</span>
                                            @elseif($loop->index % 4 == 3)
                                                <span class="featured-badge">Hot Deal</span>
                                            @endif
                                        </div>
                                        <h3 class="featured-name">{{ $product->name }}</h3>
                                    </a>
                                    <div class="featured-footer">
                                        <span class="featured-price">&#8377; {{ number_format($product->price, 0) }}
                                            INR</span>
                                        <button class="featured-cart" type="button"
                                            aria-label="Add {{ $product->name }} to cart">
                                            <img src="{{ asset('images/Vector.svg') }}" alt="" />
                                        </button>
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

    <section class="category-section" aria-labelledby="browse-categories-title">
        <h2 id="browse-categories-title" class="category-title">Browse Our Categories</h2>
        @php
            $homeCategories = [
                ['href' => url('women'), 'image' => asset('images/Rectangle 9.png'), 'alt' => 'Sarees', 'name' => 'Sarees', 'imageClass' => 'category-image'],
                ['href' => url('mens'), 'image' => asset('images/Rectangle 9 (1).png'), 'alt' => 'Shirts', 'name' => 'Shirts', 'imageClass' => 'category-image'],
                ['href' => url('kids'), 'image' => asset('images/Rectangle 9 (2).png'), 'alt' => 'Girl', 'name' => 'Girl', 'imageClass' => 'category-image'],
                ['href' => url('kids'), 'image' => asset('images/Rectangle 9 (3).png'), 'alt' => 'Boy', 'name' => 'Boy', 'imageClass' => 'category-image'],
                ['href' => url('kids'), 'image' => asset('images/Rectangle 9 (4).png'), 'alt' => 'Half Saree', 'name' => 'Half Saree', 'imageClass' => 'category-image'],
                ['href' => url('mens'), 'image' => asset('images/Rectangle 9 (5).png'), 'alt' => 'Dhoti', 'name' => 'Dhoti', 'imageClass' => 'category-image category-image--dhoti'],
            ];
        @endphp
        <div class="category-swiper-wrap">
            <div class="swiper category-swiper">
                <div class="swiper-wrapper">
                    @for ($copy = 0; $copy < 2; $copy++)
                        @foreach ($homeCategories as $category)
                            <div class="swiper-slide">
                                <a class="category-link" href="{{ $category['href'] }}" style="text-decoration: none;"
                                    @if ($copy === 1) aria-hidden="true" tabindex="-1" @endif>
                                    <article class="category-card">
                                        <div class="category-image-shell">
                                            <img class="{{ $category['imageClass'] }}" src="{{ $category['image'] }}"
                                                alt="{{ $category['alt'] }}" />
                                            <span class="category-ring"></span>
                                        </div>
                                        <h3 class="category-name">{{ $category['name'] }}</h3>
                                    </article>
                                </a>
                            </div>
                        @endforeach
                    @endfor
                </div>
            </div>
            <!-- Navigation outside swiper for correct button positioning -->
            <div class="swiper-button-next category-next"></div>
            <div class="swiper-button-prev category-prev"></div>
        </div>
    </section>

    <section class="promo-section" aria-label="Promotions">
        <article class="offer-card">
            <div class="offer-image-wrap">
                <img class="offer-image" src="{{ asset('images/Rectangle 31.png') }}" alt="Special Offer" />
            </div>
            <div class="offer-content">
                <h2 class="offer-title">Up to 20% off, only for this month</h2>
                <p class="offer-text">Shop now and enjoy up to 10% off on selected items. Limited time only!</p>
                <a class="offer-link" href="{{ url('sarees') }}">
                    <span>Shop Now</span>
                    <span class="offer-link-arrow" aria-hidden="true">&#8594;</span>
                </a>
            </div>
        </article>

        <article class="wedding-card">
            <div class="wedding-image-wrap">
                <img class="wedding-image" src="{{ asset('images/image 2.png') }}" alt="Wedding collections" />
            </div>
            <div class="wedding-content">
                <svg class="wedding-heading-svg" viewBox="0 0 1000 500">
                    <defs>
                        <linearGradient id="textFill" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="#A91B43" />
                            <stop offset="105%" stop-color="#F2A329" />
                        </linearGradient>
                        <radialGradient id="strokeGradient" cx="50%" cy="50%" r="40%">
                            <stop offset="0%" stop-color="#A91B43" />
                            <stop offset="50%" stop-color="#EF9F29" />
                            <stop offset="80%" stop-color="#A91B43" />
                        </radialGradient>
                    </defs>
                    <text x="0" y="180" text-anchor="start" fill="url(#textFill)" stroke="url(#strokeGradient)"
                        stroke-width="3" paint-order="stroke">
                        Wedding
                    </text>
                    <text x="0" y="400" text-anchor="start" fill="url(#textFill)" stroke="url(#strokeGradient)"
                        stroke-width="3" paint-order="stroke">
                        Collections
                    </text>
                </svg>
                <p class="wedding-text">Flamboyantly charming, the Scarlet Satin Delight Saree embodies grandeur and
                    romance. Rendered in a vivacious shade of red.</p>
                <button class="wedding-cta" type="button" onclick="window.location.href='{{ url('sarees') }}'">Shop
                    Now</button>
            </div>
        </article>
    </section>

    <section class="testimonial-section" aria-labelledby="testimonial-title">
        <p class="testimonial-kicker">Testimonial</p>
        <h2 id="testimonial-title" class="testimonial-title">Speaking from their hearts</h2>
        <div class="testimonial-vector-wrap">
            <img class="testimonial-vector" src="{{ asset('images/Vector2.svg') }}" alt="Quote icon" />
        </div>

        <div class="testimonial-swiper-wrap">
            <div class="swiper testimonial-swiper">
                <div class="swiper-wrapper">
                    @for ($copy = 0; $copy < 2; $copy++)
                        <div class="swiper-slide" @if ($copy === 1) aria-hidden="true" tabindex="-1" @endif>
                            <article class="testimonial-card">
                                <h3 class="testimonial-card-title">Beautiful elegant <br>saree</h3>
                                <p class="testimonial-text">I recently ordered this saree online, and I am extremely happy with my
                                    purchase. The saree looked exactly like the pictures shown on the website. The color was
                                    vibrant,
                                    and the fabric quality was even better than I expected.</p>
                                <p class="testimonial-name">Ramya</p>
                            </article>
                        </div>
                        <div class="swiper-slide" @if ($copy === 1) aria-hidden="true" tabindex="-1" @endif>
                            <article class="testimonial-card">
                                <h3 class="testimonial-card-title">Stunning Design <br>& Quality</h3>
                                <p class="testimonial-text">The craftsmanship is truly exceptional. I wore it for a wedding and
                                    received so many compliments. The delivery was prompt and the packaging was premium. Highly
                                    recommend Nandhini Silks!</p>
                                <p class="testimonial-name">Priya</p>
                            </article>
                        </div>
                        <div class="swiper-slide" @if ($copy === 1) aria-hidden="true" tabindex="-1" @endif>
                            <article class="testimonial-card">
                                <h3 class="testimonial-card-title">Perfect for <br>Occasions</h3>
                                <p class="testimonial-text">Finding authentic silk sarees online can be tricky, but this was a
                                    great experience. The texture is soft and the pallu design is intricate. Will definitely buy
                                    more in the future.</p>
                                <p class="testimonial-name">Deepika</p>
                            </article>
                        </div>
                    @endfor
                </div>
            </div>
            <!-- Navigation outside swiper for correct button positioning -->
            <div class="swiper-button-next testimonial-next"></div>
            <div class="swiper-button-prev testimonial-prev"></div>
        </div>
    </section>

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
                        640: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        1024: { slidesPerView: 4 },
                        1280: { slidesPerView: 4 },
                    }
                });

                new Swiper('.category-swiper', {
                    slidesPerView: 3, // Base mobile specifically
                    slidesPerGroup: 1,
                    spaceBetween: 8,
                    loop: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.category-next',
                        prevEl: '.category-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 3, spaceBetween: 12 },
                        768: { slidesPerView: 4, spaceBetween: 15 },
                        1024: { slidesPerView: 5, spaceBetween: 20 },
                        1280: { slidesPerView: 6, spaceBetween: 24 },
                    }
                });

                new Swiper('.testimonial-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    loop: true,
                    centeredSlides: true,
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
                        768: { slidesPerView: 2, spaceBetween: 30 },
                        1024: { slidesPerView: 3, spaceBetween: 40 },
                    }
                });
                const featuredSwiper = new Swiper('.featured-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 18,
                    loop: true,
                    autoplay: {
                        delay: 3500,
                        disableOnInteraction: false,
                    },
                    navigation: {
                        nextEl: '.featured-next',
                        prevEl: '.featured-prev',
                    },
                    breakpoints: {
                        640: { slidesPerView: 2 },
                        768: { slidesPerView: 3 },
                        1024: { slidesPerView: 4 },
                    },
                    on: {
                        init: function () {
                            updateProgressBar(this);
                        },
                        slideChange: function () {
                            updateProgressBar(this);
                        },
                        resize: function () {
                            updateProgressBar(this);
                        }
                    }
                });

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

            });
        </script>
    @endpush
@endsection
