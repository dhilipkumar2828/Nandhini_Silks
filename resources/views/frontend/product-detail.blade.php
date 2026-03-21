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

                        // Color-specific Images
                        $colorImagesMap = $product->color_images ?? [];
                        foreach ($colorImagesMap as $colorId => $imgs) {
                            foreach ((array)$imgs as $img) {
                                $allImages[] = [
                                    'url' => asset('uploads/' . (str_starts_with($img, 'products/') ? $img : 'products/' . $img)),
                                    'color_id' => $colorId
                                ];
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
                    <div class="product-thumbnails">
                        @foreach($allImages as $i => $imgData)
                            <div class="thumbnail {{ $i === 0 ? 'active' : '' }}" 
                                 data-color-id="{{ $imgData['color_id'] }}"
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

                    <div class="product-rating" style="margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                        <div class="stars" style="line-height: 1; color: #FFB800; font-size: 13px;">★★★★★</div>
                        <span style="font-size: 12px; color: #888; font-weight: 500;">4.8 (12 Reviews)</span>
                    </div>

                    <div class="product-price-section" style="margin-bottom: 12px; display: flex; align-items: baseline; line-height: 1;">
                        <span class="current-price" style="font-size: 28px; font-weight: 800; color: #A91B43;">₹{{ number_format($product->price, 0) }}</span>
                        @if($product->regular_price > $product->price)
                            <span class="old-price" style="text-decoration: line-through; color: #bbb; margin-left: 10px; font-size: 16px;">₹{{ number_format($product->regular_price, 0) }}</span>
                        @else
                            <span class="old-price" style="text-decoration: line-through; color: #bbb; margin-left: 10px; font-size: 16px;">₹{{ number_format($product->price * 1.25, 0) }}</span>
                        @endif
                        <span class="discount-badge" style="background: #e74c3c; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; margin-left: 10px; text-transform: uppercase;">20% OFF</span>
                    </div>

                    <div class="stock-status" style="margin-bottom: 15px;">
                        @if($product->stock_quantity > 0)
                            <span class="stock-badge stock-in" style="font-size: 12px; font-weight: 600; color: #2ecc71; background: #f0fff4; padding: 4px 10px; border-radius: 4px; border: 1px solid #c6f6d5;">
                                IN STOCK
                            </span>
                        @else
                            <span class="stock-badge stock-out" style="font-size: 12px; font-weight: 600; color: #e74c3c; background: #fff5f5; padding: 4px 10px; border-radius: 4px; border: 1px solid #fed7d7;">
                                OUT OF STOCK
                            </span>
                        @endif
                    </div>

                    <div class="product-description-short" style="margin-bottom: 0px; color: #666; line-height: 1.5; font-size: 14px; max-width: 500px;">
                        {!! Str::limit(strip_tags($product->description), 150) !!}
                    </div>

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
                    <p>No reviews yet for this product.</p>
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

            <!-- Recently Viewed Section (Mocked) -->
            <section class="recently-viewed" style="margin-top: 60px; margin-bottom: 60px; overflow: hidden;">
                <h2 style="font-size: 32px; color: #ad8b4e; margin-bottom: 40px; font-weight: 600; text-align: center;">
                    Recently Viewed</h2>
                <div class="swiper-wrap-outer" style="position: relative; padding: 0 60px;">
                    <div class="swiper recently-swiper" style="padding: 10px 5px;">
                        <div class="swiper-wrapper">
                            @for($i = 0; $i < 6; $i++) {{-- Mock 6 items --}}
                                <div class="swiper-slide">
                                    <article class="product-card-v2" style="height: 100%;">
                                        <div class="product-image-v2">
                                            <img src="{{ asset('images/pro'.(($i%4)+1).'.png') }}" alt="Silk Saree" onerror="this.src='{{ asset('images/pro1.png') }}'">
                                        </div>
                                        <div class="product-info-v2">
                                            <h3 class="product-name-v2">Pure Silk Saree</h3>
                                            <p class="product-price-v2">₹4,290</p>
                                        </div>
                                    </article>
                                </div>
                            @endfor
                        </div>
                    </div>
                    <div class="swiper-button-next recently-next"></div>
                    <div class="swiper-button-prev recently-prev"></div>
                </div>
            </section>
        </div>
    </main>
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
            let current = parseInt(input.value);
            current += val;
            if (current < 1) current = 1;
            input.value = current;
            if (hiddenInput) hiddenInput.value = current;
        }
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
        }

        function updateGallery(colorId) {
            const thumbs = document.querySelectorAll('.thumbnail');
            let firstFound = null;
            
            thumbs.forEach(t => {
                const thumbColorId = t.getAttribute('data-color-id');
                // Show if generic (null) OR matches colorId
                if(!thumbColorId || thumbColorId === 'null' || thumbColorId == colorId) {
                    t.style.display = 'block';
                    if(!firstFound) firstFound = t;
                } else {
                    t.style.display = 'none';
                }
            });

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

            }
        });
    </script>
@endpush

