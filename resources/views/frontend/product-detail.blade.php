@extends('frontend.layouts.app')

@section('title', ($product->name ?? 'Product') . ' | Nandhini Silks')

@section('content')
    @push('styles')
    <style>
        .swiper-wrap-outer {
            position: relative;
            padding: 0 60px;
        }
        .swiper-wrap-outer .swiper-button-next,
        .swiper-wrap-outer .swiper-button-prev {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            margin-top: 0;
            z-index: 1000;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 10px 22px rgba(169, 27, 67, 0.22);
            color: var(--pink);
            transition: all 0.3s ease;
        }
        .swiper-wrap-outer .swiper-button-next::after,
        .swiper-wrap-outer .swiper-button-prev::after {
            font-size: 18px;
            font-weight: bold;
        }
        .swiper-wrap-outer .swiper-button-prev {
            left: 5px;
        }
        .swiper-wrap-outer .swiper-button-next {
            right: 5px;
        }
        @media (max-width: 768px) {
            .swiper-wrap-outer {
                padding: 0 15px !important;
            }
            .swiper-wrap-outer .swiper-button-next,
            .swiper-wrap-outer .swiper-button-prev {
                display: none;
            }
        }
        .attribute-option.active.color-swatch {
            box-shadow: 0 0 0 2px #A91B43 !important;
            transform: scale(1.1);
        }
        .attribute-option.active.size-btn {
            background: #A91B43 !important;
            color: #fff !important;
            border-color: #A91B43 !important;
        }

        /* Unavailable/Out of Stock Swatch Style - Strike-through & Dashed */
        .attribute-option.unavailable {
            position: relative !important;
            opacity: 0.6 !important;
            cursor: not-allowed !important;
            pointer-events: none !important;
            background: #f8f9fa !important;
            color: #adb5bd !important;
            border: 1px dashed #dee2e6 !important;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .attribute-option.unavailable::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 1px;
            background: #adb5bd;
            transform: translateY(-50%);
            z-index: 10;
        }

        /* Diagonal line for color swatches instead of horizontal */
        .attribute-option.unavailable.color-swatch::after {
            left: 0;
            right: 0;
            top: 50%;
            transform: translateY(-50%) rotate(45deg);
        }

        .attribute-option.unavailable.size-btn {
            background-color: #f1f3f5 !important;
        }

        /* Override cache for thumbnails */
        .product-thumbnails {
            display: flex !important;
            flex-direction: column !important;
            gap: 12px !important;
            width: 70px !important;
            overflow-y: auto;
            max-height: 500px;
        }
        .product-thumbnails::-webkit-scrollbar {
            width: 3px;
        }
        .product-thumbnails::-webkit-scrollbar-thumb {
            background: #ddd;
            border-radius: 10px;
        }
        .thumbnail {
            width: 70px !important;
            height: 87.5px !important; /* 4:5 ratio */
            border-radius: 6px !important;
            overflow: hidden;
            border: 2px solid transparent !important;
            cursor: pointer;
            transition: border-color 0.2s ease, transform 0.2s ease;
            flex-shrink: 0;
            background: #f9f9f9;
        }
        .thumbnail.active, .thumbnail:hover {
            border-color: #A91B43 !important;
            transform: scale(1.02);
        }
        .thumbnail img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        @media (max-width: 900px) {
            .product-thumbnails {
                flex-direction: row !important;
                width: 100% !important;
                max-height: auto;
                overflow-x: auto;
                padding-bottom: 10px;
            }
        }

        .review-empty-state {
            text-align: center;
            padding: 56px 24px;
            background: linear-gradient(180deg, #fffdf8 0%, #fff7ef 100%);
            border: 1px solid rgba(169, 27, 67, 0.12);
            border-radius: 28px;
            box-shadow: 0 14px 32px rgba(169, 27, 67, 0.06);
        }

        .review-empty-icon {
            width: 68px;
            height: 68px;
            margin: 0 auto 18px;
            border-radius: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: #a91b43;
            box-shadow: 0 10px 20px rgba(169, 27, 67, 0.12);
            font-size: 28px;
        }

        .review-empty-title {
            margin: 0 0 8px;
            color: #1f2937;
            font-size: 18px;
            font-weight: 700;
        }

        .review-empty-text {
            margin: 0 0 24px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .review-write-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 24px;
            border: none;
            border-radius: 999px;
            background: linear-gradient(90deg, #a91b43 0%, #c62355 100%);
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            cursor: pointer;
            box-shadow: 0 12px 24px rgba(169, 27, 67, 0.22);
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .review-write-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 28px rgba(169, 27, 67, 0.28);
            filter: brightness(1.02);
        }

        .review-write-btn:active {
            transform: translateY(0);
        }

        .review-inline-panel {
            display: none;
            width: min(100%, 560px);
            background: #fff;
            border-radius: 24px;
            padding: 28px;
            border: 1px solid rgba(169, 27, 67, 0.12);
            box-shadow: 0 18px 36px rgba(169, 27, 67, 0.08);
            margin-top: 24px;
        }

        .review-inline-panel.open {
            display: block;
        }

        .review-entry-state.hidden {
            display: none;
        }

        .review-modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 20px;
        }

        .review-modal-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
        }

        .review-modal-close {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 999px;
            background: #f8e8ee;
            color: #a91b43;
            font-size: 20px;
            cursor: pointer;
        }

        .review-form-group {
            margin-bottom: 18px;
        }

        .review-form-label {
            display: block;
            margin-bottom: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 600;
        }

        .review-form-select,
        .review-form-textarea {
            width: 100%;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fff;
            color: #111827;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .review-form-select {
            height: 48px;
            padding: 0 14px;
        }

        .review-form-textarea {
            min-height: 150px;
            padding: 14px;
            resize: vertical;
            line-height: 1.6;
        }

        .review-form-select:focus,
        .review-form-textarea:focus {
            border-color: #a91b43;
            box-shadow: 0 0 0 3px rgba(169, 27, 67, 0.12);
        }

        .review-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .review-form-cancel,
        .review-form-submit {
            min-height: 46px;
            padding: 0 22px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .review-form-cancel {
            border: 1px solid #e5e7eb;
            background: #fff;
            color: #4b5563;
        }

        .review-form-submit {
            border: none;
            background: linear-gradient(90deg, #a91b43 0%, #c62355 100%);
            color: #fff;
            box-shadow: 0 12px 24px rgba(169, 27, 67, 0.2);
        }

        .quantity-section {
            width: 100%;
        }

        .quantity-picker {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center;
            min-height: 34px;
        }

        .qty-btn {
            width: 32px;
            height: 34px;
            padding: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px !important;
            line-height: 1;
            flex-shrink: 0;
        }

        .qty-input {
            width: 36px !important;
            min-width: 36px;
            height: 34px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 34px;
            font-size: 13px !important;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .quantity-section {
                margin-bottom: 18px !important;
            }

            .quantity-picker {
                width: 100% !important;
                max-width: 118px;
            }

            .product-actions-group {
                max-width: 100% !important;
            }

            .review-empty-state {
                padding: 42px 18px;
                border-radius: 22px;
            }

            .review-empty-title {
                font-size: 16px;
            }

            .review-empty-text {
                font-size: 13px;
            }

            .review-write-btn {
                width: 100%;
            }

            .review-inline-panel {
                padding: 22px 18px;
                border-radius: 20px;
            }

            .review-form-actions {
                flex-direction: column;
            }

            .review-form-cancel,
            .review-form-submit {
                width: 100%;
            }
        }
    </style>
    @endpush
    <main class="product-detail-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp;
                <a href="{{ url('sarees') }}">Sarees</a> &nbsp; / &nbsp;
                <span>{{ $product->name }}</span>
            </div>

            <div class="product-detail-grid">
                <!-- Gallery Section -->
                <div class="product-gallery">
                    @php
                        $allImages = [];
                        // Main Images
                        if ($product->images && is_array($product->images) && count($product->images) > 0) {
                            foreach ($product->images as $img) {
                                $allImages[] = [
                                    'url' => asset('uploads/' . (str_starts_with($img, 'products/') ? $img : 'products/' . $img)),
                                    'color_id' => null
                                ];
                            }
                        } elseif ($product->image_path) {
                            $allImages[] = [
                                'url' => asset('images/' . $product->image_path),
                                'color_id' => null
                            ];
                        }

                        // Color-specific Images (Legacy)
                        $colorImagesMap = $product->color_images ?? [];
                        foreach ($colorImagesMap as $colorId => $imgs) {
                            foreach ((array)$imgs as $img) {
                                $allImages[] = [
                                    'url' => asset('uploads/' . (str_starts_with($img, 'products/') ? $img : 'products/' . $img)),
                                    'color_id' => $colorId
                                ];
                            }
                        }

                        // Variant-specific Images (Multiple)
                        if ($product->product_variants) {
                            foreach($product->product_variants as $variant) {
                                $vImgs = is_array($variant->images) ? $variant->images : (json_decode($variant->images ?? '[]', true) ?? []);
                                if(empty($vImgs) && $variant->image) $vImgs = [$variant->image];
                                
                                foreach($vImgs as $vImg) {
                                    $allImages[] = [
                                        'url' => asset('uploads/' . $vImg),
                                        'color_id' => null,
                                        'variant_id' => $variant->id
                                    ];
                                }
                            }
                        }

                        if (empty($allImages)) {
                            $allImages[] = ['url' => asset('images/pro.png'), 'color_id' => null];
                        }
                        
                        $mainImage = $allImages[0]['url'];
                    @endphp
                    <div class="main-product-image" id="zoomContainer" style="position: relative;">
                        <img src="{{ $mainImage }}" alt="{{ $product->name }}" id="mainImg">
                        <button type="button" class="btn-wishlist-detail wishlist-btn" id="wishlistBtn" aria-label="Add to Wishlist" 
                                data-product-id="{{ $product->id }}"
                                style="position: absolute; top: 15px; right: 15px; width: 42px; height: 42px; background: rgba(255,255,255,0.9); border: none; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10;">
                            <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart" id="wishlistIcon" style="color: #A91B43; font-size: 18px;"></i>
                        </button>
                    </div>
                    <div class="product-thumbnails" id="thumbnailsContainer" style="display: none;">
                        @foreach($allImages as $i => $imgData)
                            <div class="thumbnail {{ $i === 0 ? 'active' : '' }}" 
                                 data-color-id="{{ $imgData['color_id'] }}"
                                 data-variant-id="{{ $imgData['variant_id'] ?? '' }}"
                                 onclick="changeImg('{{ $imgData['url'] }}', this)">
                                <img src="{{ $imgData['url'] }}" alt="View {{ $i + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Info Section -->
                <div class="product-info-details">
                    <div class="product-meta" style="margin-bottom: 5px;">
                        <p class="product-brand" style="margin: 0; line-height: 1; font-size: 13px; color: #ad8b4e; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">{{ $product->category->name ?? 'Nandhini Silks Exclusive' }}</p>
                        <p class="product-meta-item" style="margin: 4px 0 0; font-size: 11px; color: #999; font-weight: 500;">SKU: <span class="product-sku">{{ strtoupper($product->sku) ?: 'NS-' . strtoupper(Str::slug($product->name)) }}</span></p>
                    </div>

                    <h1 class="product-title-detail" style="margin: 0 0 5px; line-height: 1.1; font-size: 32px; font-weight: 700; color: #1a1a1a;">{{ $product->name }}</h1>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mb-2">
                        <div class="product-rating flex items-center gap-2">
                            <div class="stars flex" style="line-height: 1; color: #FFB800; font-size: 12px;">
                                @for($i=1; $i<=5; $i++)
                                    <i class="{{ $i <= round($product->average_rating) ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                            <span style="font-size: 11px; color: #888; font-weight: 500;">{{ number_format($product->average_rating, 1) }} ({{ $product->reviews_count }} Reviews)</span>
                        </div>
                        <div class="product-price-section flex items-baseline gap-2">
                            <span class="current-price" id="displayPrice" style="font-size: 24px; font-weight: 800; color: #A91B43;">₹{{ number_format($product->price, 0) }}</span>
                            @if($product->regular_price > $product->price)
                                <span class="old-price" id="displayRegularPrice" style="text-decoration: line-through; color: #bbb; font-size: 14px;">₹{{ number_format($product->regular_price, 0) }}</span>
                                <span class="discount-badge" id="displayDiscount" style="background: #e74c3c; color: #fff; padding: 2px 5px; border-radius: 4px; font-size: 9px; font-weight: 700; text-transform: uppercase;">{{ $product->discount_percent }}% OFF</span>
                            @endif
                        </div>
                        <div class="stock-status">
                            @php
                                $totalVariantStock = $product->product_variants->sum('stock_quantity');
                                $isInStock = ($product->product_variants->count() > 0) ? ($totalVariantStock > 0) : ($product->stock_quantity > 0);
                            @endphp
                            <span id="stockStatus" class="stock-badge {{ $isInStock ? 'stock-in' : 'stock-out' }}" 
                                   style="font-size: 9px; font-weight: 700; {{ $isInStock ? 'color: #2ecc71; background: #c0fbe15e; border: 1px solid #c6f6d5;' : 'color: #e74c3c; background: #fff5f5; border: 1px solid #fed7d7;' }} padding: 2px 6px; border-radius: 3px; text-transform: uppercase;">
                                {{ $isInStock ? 'IN STOCK' : 'OUT OF STOCK' }}
                            </span>
                        </div>
                    </div>
                    <p style="font-size: 10px; color: #999; margin-bottom: 8px; font-weight: 500; margin-top: -5px;">(Inclusive of all taxes)</p>

                    @if($product->description)
                    <div class="product-description-short" style="margin-bottom: 0px; color: #666; line-height: 1.5; font-size: 14px; max-width: 500px;">
                        {!! Str::limit(strip_tags($product->description), 150) !!}
                    </div>
                    @endif

                    <form class="product-actions" method="POST" action="{{ route('cart.add', $product->id) }}" id="pdpForm" style="display: block; width: 100%;">
                        @csrf
                        <input type="hidden" name="quantity" id="qtyInput" value="1">

                        @if(!empty($attributeGroups))
                            <div class="product-selections" style="margin-bottom: 20px;">
                                @foreach($attributeGroups as $group)
                                    @php
                                        $attrId = $group['attribute']->id;
                                        $attrName = $group['attribute']->name;
                                    @endphp
                                    <div class="attribute-section" style="margin-bottom: 15px;">
                                        <h3 class="attribute-title" style="font-size: 11px; margin-bottom: 8px; text-transform: uppercase; color: #999; font-weight: 700; letter-spacing: 0.5px;">
                                            Select {{ $attrName }}
                                        </h3>
                                        <input type="hidden" name="attributes[{{ $attrId }}]" id="attr_{{ $attrId }}" value="">
                                        <div class="swatch-container" style="display: flex; gap: 10px; flex-wrap: wrap;">
                                            @foreach($group['values'] as $value)
                                                @php
                                                    $swatch = $value->swatch_value;
                                                    // Fallback to variant image for color swatch if swatch_value is missing
                                                    if(!$swatch && strtolower($attrName) == 'color' && isset($colorImagesMap[$value->id])) {
                                                        $swatch = $colorImagesMap[$value->id][0];
                                                    }
                                                    $isColor = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                @endphp
                                                @if($swatch)
                                                    @if($isColor)
                                                        <div class="attribute-option color-swatch" 
                                                             data-attr-id="{{ $attrId }}" 
                                                             data-value-id="{{ $value->id }}"
                                                             onclick="selectAttribute(this)"
                                                             style="background: {{ $swatch }}; width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd; cursor: pointer; transition: all 0.2s;" 
                                                             title="{{ $value->name }}"></div>
                                                    @else
                                                        <div class="attribute-option color-swatch" 
                                                             data-attr-id="{{ $attrId }}" 
                                                             data-value-id="{{ $value->id }}"
                                                             onclick="selectAttribute(this)"
                                                             style="background-image: url('{{ asset('uploads/' . $swatch) }}'); background-size: cover; background-position: center; width: 32px; height: 32px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd; cursor: pointer; transition: all 0.2s;" 
                                                             title="{{ $value->name }}"></div>
                                                    @endif
                                                @else
                                                    <button type="button" 
                                                            class="attribute-option size-btn" 
                                                            data-attr-id="{{ $attrId }}" 
                                                            data-value-id="{{ $value->id }}"
                                                            onclick="selectAttribute(this)"
                                                            style="padding: 6px 16px; border: 1.5px solid #eee; background: #fff; color: #333; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; transition: all 0.2s;">
                                                        {{ $value->name }}
                                                    </button>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Quantity Selector -->
                        <div class="quantity-section" style="margin-bottom: 20px;">
                            <h3 style="font-size: 11px; margin-bottom: 8px; text-transform: uppercase; color: #999; font-weight: 700; letter-spacing: 0.5px;">Quantity</h3>
                            <div class="quantity-picker" style="display: flex; align-items: center; border: 1.5px solid #eee; width: fit-content; border-radius: 8px; overflow: hidden; background: #fff;">
                                <button type="button" class="qty-btn" onclick="updateQty(-1)" style="padding: 8px 15px; background: none; border: none; font-size: 16px; cursor: pointer; color: #333;">−</button>
                                <input type="text" class="qty-input" value="1" readonly id="qtyDisp" style="width: 40px; text-align: center; border: none; font-weight: 700; font-size: 14px; background: transparent;">
                                <button type="button" class="qty-btn" onclick="updateQty(1)" style="padding: 8px 15px; background: none; border: none; font-size: 16px; cursor: pointer; color: #333;">+</button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="product-actions-group" style="display: flex; gap: 12px; max-width: 500px;">
                            <button type="submit" name="action" value="cart" class="btn-add-cart" style="flex: 1; background: #A91B43; color: #fff; padding: 16px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px; letter-spacing: 0.5px; transition: background 0.2s;">
                                ADD TO CART
                            </button>
                            <button type="submit" name="action" value="checkout" class="btn-buy-now" style="flex: 1; background: #111; color: #fff; padding: 16px; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; font-size: 13px; letter-spacing: 0.5px; transition: background 0.2s;">
                                BUY IT NOW
                            </button>
                        </div>
                    </form>

                    <div class="share-section" style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                        <span class="share-title" style="font-size: 14px; color: #888;">Share this product:</span>
                        <div class="share-links" style="display: flex; gap: 15px; margin-top: 10px;">
                            <!-- SVG Social Icons (Simplified) -->
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951" />
                                </svg></a>
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.417-.923 3.9 3.9 0 0 0 .923-1.417c.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.999 0zm0 1.44c2.144 0 2.405.008 3.25.047.781.036 1.206.166 1.493.28.384.148.658.324.945.61.285.286.463.56.611.944.113.287.243.712.28 1.493.038.845.046 1.106.046 3.25s-.008 2.405-.047 3.25c-.036.781-.166 1.206-.28 1.493-.148.384-.324.658-.61.945-.287.285-.56.463-.945.611-.286.113-.712.243-1.493.28-.845.038-1.106.047-3.25.047s-2.405-.009-3.25-.047c-.781-.036-1.206-.166-1.493-.28a3.14 3.14 0 0 1-.945-.611 3.14 3.14 0 0 1-.61-.945c-.114-.287-.244-.712-.28-1.493-.039-.845-.047-1.106-.047-3.25s.008-2.405.047-3.25c.036-.781.166-1.206.28-1.493.148-.384.324-.658.61-.945.286-.287.561-.463.945-.611.287-.113.712-.243 1.493-.28C5.594 1.448 5.854 1.44 8 1.44z" />
                                    <path
                                        d="M8 3.86a4.14 4.14 0 1 0 0 8.28 4.14 4.14 0 0 0 0-8.28zm0 6.84a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4zm4.316-6.685a.972.972 0 1 1-1.944 0 .972.972 0 0 1 1.944 0z" />
                                </svg></a>
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor"
                                    viewBox="0 0 16 16">
                                    <path
                                        d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.061 3.972L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z" />
                                </svg></a>
                        </div>
                    </div>

                    <div class="delivery-check"
                        style="margin-top: 30px; background: #fdfaf0; padding: 20px; border-radius: 10px;">
                        <p class="delivery-title" style="font-weight: 600; margin-bottom: 10px;">Check Delivery Availability
                        </p>
                        <div class="pincode-input-group" style="display: flex; gap: 10px;">
                            <input type="text" class="pincode-input" placeholder="Enter Pincode"
                                style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <button class="btn-pincode"
                                style="background: #A91B43; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Check</button>
                        </div>
                        <p style="font-size: 12px; color: #666; margin-top: 8px;">Free shipping on orders above ₹5,000.</p>
                    </div>
                </div>
            </div>

            <!-- Detail Tabs Sections -->
            <div class="product-extra-info"
                style="margin-top: 60px; background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05);">
                <div class="tabs-info">
                    <button class="tab-btn active" onclick="switchTab(event, 'tabDesc')">Full Description</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabSpecs')">Specifications</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabReviews')">Reviews</button>
                    <button class="tab-btn" onclick="switchTab(event, 'tabShipping')">Shipping & Returns</button>
                </div>

                <div class="tab-pane active" id="tabDesc" style="padding-top: 20px;">
                    <style>
                        #tabDesc p { margin-bottom: 6px; }
                        #tabDesc li { margin-bottom: 2px; }
                    </style>
                    <div style="color: #666; line-height: 1.4; font-size: 15px;">
                        {!! $product->description !!}
                    </div>
                </div>

                <div class="tab-pane" id="tabSpecs" style="display: none; padding-top: 30px;">
                    <table class="specs-table">
                        <tr>
                            <td class="specs-label">Category</td>
                            <td class="specs-value">{{ $product->category->name ?? 'Collection' }}</td>
                        </tr>
                        <tr>
                            <td class="specs-label">Stock Status</td>
                            <td class="specs-value">{{ $product->stock > 0 ? 'Available' : 'Out of Stock' }}</td>
                        </tr>
                        <tr>
                            <td class="specs-label">Material</td>
                            <td class="specs-value">Pure Silk / Handloom</td>
                        </tr>
                    </table>
                </div>

                <div class="tab-pane" id="tabReviews" style="display: none; padding-top: 30px;">
                    <div class="reviews-container">
                        <div class="review-entry-state {{ ($errors->has('stars') || $errors->has('review')) ? 'hidden' : '' }}" id="reviewEntryState">
                        @if($product->reviews()->count() > 0)
                            <div class="space-y-6">
                                @foreach($product->reviews as $review)
                                    <div class="review-item border-b border-gray-100 pb-6">
                                        <div class="flex items-center gap-2 mb-2">
                                            <div class="text-[#FFB800] text-sm">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="{{ $i <= $review->stars ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>
                                            <span class="font-bold text-sm text-gray-800">{{ $review->user->name ?? 'User' }}</span>
                                            <span class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 leading-relaxed">{{ $review->review }}</p>
                                    </div>
                                @endforeach
                            </div>
                            @auth
                            <button type="button" class="review-write-btn" id="openReviewFormBtn" style="margin-top: 24px;">
                                <i class="far fa-pen-to-square"></i>
                                {{ $userReview ? 'Update Your Review' : 'Write a Review' }}
                            </button>
                            @else
                            <a href="{{ route('login') }}" class="review-write-btn" style="margin-top: 24px; text-decoration: none;">
                                <i class="far fa-pen-to-square"></i>
                                Write a Review
                            </a>
                            @endauth
                        @else
                            <div class="review-empty-state">
                                <div class="review-empty-icon">
                                    <i class="far fa-comments"></i>
                                </div>
                                <h4 class="review-empty-title">No reviews yet</h4>
                                <p class="review-empty-text">Be the first to share your thoughts about this product.</p>
                                @auth
                                <button type="button" class="review-write-btn" id="openReviewFormBtn">
                                    <i class="far fa-pen-to-square"></i>
                                    Write a Review
                                </button>
                                @else
                                <a href="{{ route('login') }}" class="review-write-btn" style="text-decoration: none;">
                                    <i class="far fa-pen-to-square"></i>
                                    Write a Review
                                </a>
                                @endauth
                            </div>
                        @endif
                        </div>

                        @auth
                        <div class="review-inline-panel {{ ($errors->has('stars') || $errors->has('review')) ? 'open' : '' }}" id="reviewInlinePanel">
                            <div class="review-modal-header">
                                <h3 class="review-modal-title">{{ $userReview ? 'Update Your Review' : 'Write a Review' }}</h3>
                                <button type="button" class="review-modal-close" id="closeReviewFormBtn" aria-label="Close review form">&times;</button>
                            </div>
                            <form method="POST" action="{{ route('product.review.store', $product) }}">
                                @csrf
                                <div class="review-form-group">
                                    <label class="review-form-label" for="reviewStars">Your Rating</label>
                                    <select class="review-form-select" name="stars" id="reviewStars" required>
                                        <option value="">Select rating</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('stars', $userReview->stars ?? '') == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="review-form-group">
                                    <label class="review-form-label" for="reviewText">Your Review</label>
                                    <textarea class="review-form-textarea" name="review" id="reviewText" required minlength="10" placeholder="Share your experience with this product...">{{ old('review', $userReview->review ?? '') }}</textarea>
                                </div>
                                <div class="review-form-actions">
                                    <button type="button" class="review-form-cancel" id="cancelReviewFormBtn">Cancel</button>
                                    <button type="submit" class="review-form-submit">{{ $userReview ? 'Update Review' : 'Submit Review' }}</button>
                                </div>
                            </form>
                        </div>
                        @endauth
                    </div>
                </div>

                <div class="tab-pane" id="tabShipping" style="display: none; padding-top: 30px;">
                    <div style="color: #666; line-height: 1.8;">
                        <h4 style="color: #333;">Domestic Shipping (India)</h4>
                        <li style="margin-left: 20px;">Standard Delivery: 5-7 business days.</li>
                        <li style="margin-left: 20px;">Express Delivery: 2-3 business days.</li>
                        <h4 style="color: #333; margin-top: 20px;">Return Policy</h4>
                        <p>Easy 7-day hassle-free returns. Tags must be intact.</p>
                    </div>
                </div>
            </div>

            <!-- Related Products Section -->
            @if(isset($relatedProducts) && $relatedProducts->count() > 0)
                <section class="related-products" style="margin-top: 40px; overflow: hidden;">
                    <h2 style="font-size: 32px; color: #ad8b4e; margin-bottom: 40px; font-weight: 600; text-align: center;">
                        Related Collections</h2>
                    <div class="swiper-wrap-outer" style="position: relative; padding: 0 60px;">
                        <div class="swiper related-swiper" style="padding: 10px 5px;">
                            <div class="swiper-wrapper">
                                @foreach($relatedProducts->concat($relatedProducts) as $related)
                                    <div class="swiper-slide">
                                        <article class="product-card-v2" style="height: 100%;">
                                            <a href="{{ route('product.show', $related->slug) }}"
                                                style="text-decoration: none; color: inherit;">
                                                <div class="product-image-v2">
                                                    <img src="{{ $related->image_path ? asset('images/' . $related->image_path) : asset('images/pro.png') }}"
                                                        alt="{{ $related->name }}">
                                                </div>
                                                <div class="product-info-v2">
                                                    <h3 class="product-name-v2">{{ $related->name }}</h3>
                                                    <p class="product-price-v2">₹{{ number_format($related->price, 0) }}</p>
                                                </div>
                                            </a>
                                        </article>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <!-- Navigation outside swiper container -->
                        <div class="swiper-button-next related-next"></div>
                        <div class="swiper-button-prev related-prev"></div>
                    </div>
                </section>
            @endif

            {{-- Recently Viewed Section --}}
            @if(isset($recentlyViewed) && count($recentlyViewed) > 0)
                <section class="recently-viewed" style="margin-top: 60px; margin-bottom: 60px; overflow: hidden;">
                    <h2 style="font-size: 28px; font-weight: 800; color: #1a1a1a; margin-bottom: 30px; text-align: left;">
                        Recently Viewed
                        <div style="width: 40px; height: 3px; background: #a91b43; margin-top: 8px;"></div>
                    </h2>
                    
                    <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 30px;">
                        @foreach($recentlyViewed as $recent)
                            @include('frontend.partials.product-card', ['product' => $recent])
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </main>

</div>
@endsection

@push('scripts')
    <script>
        function changeImg(src, thumb) {
            document.getElementById('mainImg').src = src;
            const thumbs = document.querySelectorAll('.thumbnail');
            thumbs.forEach(t => t.classList.remove('active'));
            thumb.classList.add('active');
        }
        function updateQty(val) {
            const input = document.getElementById('qtyDisp');
            const hiddenInput = document.getElementById('qtyInput');
            const maxQuantity = 10;
            let current = parseInt(input.value);
            current += val;
            if (current < 1) current = 1;
            if (current > maxQuantity) current = maxQuantity;
            input.value = current;
            if (hiddenInput) hiddenInput.value = current;
        }
        const productVariants = {!! json_encode($product->product_variants) !!};
        const basePrice = {{ $product->price }};
        const baseRegularPrice = {{ $product->regular_price ?: $product->price }};
        const baseSku = "{{ $product->sku }}";

        function selectAttribute(element) {
            const attrId = element.getAttribute('data-attr-id');
            const valueId = element.getAttribute('data-value-id');
            
            // Remove active from peers
            const container = element.parentElement;
            container.querySelectorAll('.attribute-option').forEach(opt => opt.classList.remove('active'));
            
            // Add active to current
            element.classList.add('active');
            
            // Update hidden input
            const input = document.getElementById('attr_' + attrId);
            if (input) input.value = valueId;

            // Update Gallery if Color
            if(element.classList.contains('color-swatch')) {
                updateGallery(valueId);
            }

            // Sync Variants and Availability
            updateOptionsAvailability();
            checkVariant();
        }

        function updateOptionsAvailability() {
            const allSections = document.querySelectorAll('.attribute-section');
            const selectedAttrs = {};
            
            // Collect currently selected attributes
            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                const attrId = input.id.replace('attr_', '');
                if(input.value) selectedAttrs[attrId] = parseInt(input.value);
            });

            allSections.forEach(section => {
                const sectionAttrId = section.querySelector('input[id^="attr_"]').id.replace('attr_', '');
                const options = section.querySelectorAll('.attribute-option');

                options.forEach(opt => {
                    const valueId = parseInt(opt.getAttribute('data-value-id'));
                    
                    // Assume we selected this option, can we find ANY variant that matches it 
                    // COMBINED with other currently selected attributes?
                    let testSelection = {...selectedAttrs};
                    testSelection[sectionAttrId] = valueId;

                    let isAvailable = productVariants.some(v => {
                        if(!v.combination) return false;
                        
                        // Check if this variant matches ALL our testSelection criteria
                        let matches = Object.entries(testSelection).every(([aid, vid]) => {
                           if(!v.combination[aid]) return false;
                           return v.combination[aid].includes(vid);
                        });

                        // It's ONLY available if it exists AND has stock > 0
                        return matches && v.stock_quantity > 0;
                    });

                    if(isAvailable) {
                        opt.style.opacity = '1';
                        opt.style.pointerEvents = 'auto';
                        opt.classList.remove('unavailable');
                    } else {
                        opt.style.opacity = '0.3';
                        opt.style.pointerEvents = 'none'; // Optional: disable clicking
                        opt.classList.add('unavailable');
                        // If it's the currently active one but now unavailable, mark it
                        if(opt.classList.contains('active')) {
                           // opt.classList.remove('active');
                        }
                    }
                });
            });
        }

        function updatePriceDisplay(sale, regular) {
            document.getElementById('displayPrice').innerText = '₹' + new Intl.NumberFormat().format(sale);
            const regEl = document.getElementById('displayRegularPrice');
            const discEl = document.getElementById('displayDiscount');
            
            if(regEl && regular > sale) {
                regEl.innerText = '₹' + new Intl.NumberFormat().format(regular);
                regEl.style.display = 'inline';
                if(discEl) {
                    let pct = Math.round(((regular - sale) / regular) * 100);
                    if(pct > 0) {
                        discEl.innerText = pct + '% OFF';
                        discEl.style.display = 'inline';
                    } else {
                        discEl.style.display = 'none';
                    }
                }
            } else if(regEl) {
                regEl.style.display = 'none';
                if(discEl) discEl.style.display = 'none';
            }
        }

        function checkVariant() {
            let selectedAttrs = [];
            document.querySelectorAll('input[id^="attr_"]').forEach(input => {
                if(input.value) selectedAttrs.push(parseInt(input.value));
            });

            // Match against v.combination (e.g. { "1": [10], "2": [15] })
            let matched = productVariants.find(v => {
                if(!v.combination) return false;
                let vValues = Object.values(v.combination).flat().map(Number);
                return selectedAttrs.length === vValues.length && selectedAttrs.every(id => vValues.includes(id));
            });

            if(matched) {
                // If variant has only one price column, assume it's the 'Sale price' 
                // and compare with Main Regular Price or its own if available
                let vSale = parseFloat(matched.sale_price || matched.price || basePrice);
                let vRegular = parseFloat(matched.price && matched.sale_price && matched.price > matched.sale_price ? matched.price : baseRegularPrice);
                
                updatePriceDisplay(vSale, vRegular);
                document.querySelector('.product-sku').innerText = matched.sku || baseSku;
                updateStockStatus(matched.stock_quantity);
                
                // Swap Main Image and filter thumbnails for this variant
                updateGallery(null, matched.id);
            } else {
                 updatePriceDisplay(basePrice, baseRegularPrice);
                 document.querySelector('.product-sku').innerText = baseSku;
                 updateStockStatus({{ $product->stock_quantity }});
                 updateGallery(null, null); // Show general images
            }
        }

        window.onload = function() {
            // Auto-select first option for each attribute section
            document.querySelectorAll('.attribute-section').forEach(section => {
                const firstOption = section.querySelector('.attribute-option:not(.unavailable)');
                if (firstOption) {
                    selectAttribute(firstOption);
                }
            });
            // Initial check
            checkVariant();
        };

        function updateStockStatus(qty) {
            const el = document.getElementById('stockStatus');
            if(qty > 0) {
                el.innerText = 'IN STOCK';
                el.className = 'stock-badge stock-in';
                el.style.color = '#2ecc71'; el.style.background = '#f0fff4'; el.style.borderColor = '#c6f6d5';
            } else {
                el.innerText = 'OUT OF STOCK';
                el.className = 'stock-badge stock-out';
                el.style.color = '#e74c3c'; el.style.background = '#fff5f5'; el.style.borderColor = '#fed7d7';
            }
        }

        function updateGallery(colorId, variantId) {
            const thumbs = document.querySelectorAll('.thumbnail');
            let firstFound = null;
            let countVisible = 0;

            // First pass: try to find exact matches
            thumbs.forEach(t => {
                const thumbColorId = t.getAttribute('data-color-id');
                const thumbVariantId = t.getAttribute('data-variant-id');
                
                let show = false;
                if(variantId && thumbVariantId == variantId) {
                    show = true;
                } else if(!variantId && colorId && (thumbColorId == colorId)) {
                    show = true;
                }

                if(show) {
                    t.style.display = 'block';
                    if(!firstFound) firstFound = t;
                    countVisible++;
                } else {
                    t.style.display = 'none';
                }
            });

            // Fallback: If no specific images found, show general images
            if(countVisible === 0) {
                thumbs.forEach(t => {
                    const thumbColorId = t.getAttribute('data-color-id');
                    const thumbVariantId = t.getAttribute('data-variant-id');
                    
                    // Show images that aren't tied to a specific color or variant
                    if((!thumbColorId || thumbColorId === 'null') && !thumbVariantId) {
                        t.style.display = 'block';
                        if(!firstFound) firstFound = t;
                        countVisible++;
                    }
                });
            }

            if(firstFound) {
                const img = firstFound.querySelector('img');
                if(img) changeImg(img.src, firstFound);
            }
        }
        function switchTab(e, tabId) {
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(p => p.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
        }

        // Initialize Swipers
        document.addEventListener('DOMContentLoaded', function() {
            const reviewEntryState = document.getElementById('reviewEntryState');
            const reviewInlinePanel = document.getElementById('reviewInlinePanel');
            const openReviewFormBtn = document.getElementById('openReviewFormBtn');
            const closeReviewFormBtn = document.getElementById('closeReviewFormBtn');
            const cancelReviewFormBtn = document.getElementById('cancelReviewFormBtn');

            const openReviewForm = () => {
                if (reviewInlinePanel) {
                    reviewInlinePanel.classList.add('open');
                    if (reviewEntryState) {
                        reviewEntryState.classList.add('hidden');
                    }
                    reviewInlinePanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            };

            const closeReviewForm = () => {
                if (reviewInlinePanel) {
                    reviewInlinePanel.classList.remove('open');
                    if (reviewEntryState) {
                        reviewEntryState.classList.remove('hidden');
                    }
                }
            };

            if (openReviewFormBtn) {
                openReviewFormBtn.addEventListener('click', openReviewForm);
            }

            if (closeReviewFormBtn) {
                closeReviewFormBtn.addEventListener('click', closeReviewForm);
            }

            if (cancelReviewFormBtn) {
                cancelReviewFormBtn.addEventListener('click', closeReviewForm);
            }

            // Auto Select First Options
            let hasSelection = false;
            document.querySelectorAll('.swatch-container').forEach(container => {
                const firstOpt = container.querySelector('.attribute-option');
                if (firstOpt) {
                    firstOpt.click();
                    hasSelection = true;
                }
            });

            if (!hasSelection) {
                // If simple product without attributes, just load base images
                updateGallery(null, null);
            }

            // Unhide the thumbnails container after JS filters it
            const tc = document.getElementById('thumbnailsContainer');
            if (tc) tc.style.display = 'flex';

            const swiperOptions = {
                slidesPerView: 2,
                spaceBetween: 20,
                loop: true,
                observer: true,
                observeParents: true,
                watchOverflow: true,
                autoplay: {
                    delay: 3500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: '.related-next',
                    prevEl: '.related-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    768: { slidesPerView: 3 },
                    1024: { slidesPerView: 4 },
                }
            };

            if (document.querySelector('.related-swiper')) {
                new Swiper('.related-swiper', swiperOptions);
            }
            if (document.querySelector('.recently-swiper')) {
                new Swiper('.recently-swiper', {
                    ...swiperOptions,
                    navigation: {
                        nextEl: '.recently-next',
                        prevEl: '.recently-prev',
                    }
                });
            }

        });
        // AJAX Add to Cart
        document.getElementById('pdpForm').addEventListener('submit', function(e) {
            const action = e.submitter ? e.submitter.value : 'cart';
            
            if (action === 'cart') {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'cart');

                fetch(this.getAttribute('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success(data.message || 'Added to cart.');
                        if (window.openCartDrawer) window.openCartDrawer();
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
    </script>
@endpush
