@extends('frontend.layouts.app')

@section('title', ($product->name ?? 'Product') . ' | Nandhini Silks')

@section('content')
    <main class="product-detail-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ url('/') }}">Home</a> &nbsp; / &nbsp; 
                <a href="{{ url('sarees') }}">Sarees</a> &nbsp; / &nbsp;
                <span>{{ $product->name }}</span>
            </div>

            <div class="product-detail-grid">
                <!-- Gallery Section -->
                <div class="product-gallery">
                    @php
                        $allImages = [];
                        if ($product->images && count($product->images) > 0) {
                            foreach ($product->images as $img) {
                                $allImages[] = asset('images/' . $img);
                            }
                        } else {
                            $allImages[] = asset('images/pro.png');
                        }
                        $mainImage = $allImages[0];
                    @endphp
                    <div class="main-product-image" id="zoomContainer">
                        <img src="{{ $mainImage }}" alt="{{ $product->name }}" id="mainImg">
                    </div>
                    <div class="product-thumbnails">
                        @foreach($allImages as $i => $imgUrl)
                            <div class="thumbnail {{ $i === 0 ? 'active' : '' }}" onclick="changeImg('{{ $imgUrl }}', this)">
                                <img src="{{ $imgUrl }}" alt="View {{ $i + 1 }}">
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Info Section -->
                <div class="product-info-details">
                    <div class="product-meta">
                        <p class="product-brand">{{ $product->category->name ?? 'Nandhini Silks Exclusive' }}</p>
                        <p class="product-meta-item">SKU: <span class="product-sku">NS-{{ strtoupper(Str::slug($product->name)) }}</span></p>
                    </div>

                    <h1 class="product-title-detail">{{ $product->name }}</h1>

                    <div class="product-rating">
                        <div class="stars">★★★★★</div>
                        <span>(4.8 Rating • 12 Reviews)</span>
                    </div>

                    <div class="product-price-section">
                        <span class="current-price">₹{{ number_format($product->price, 0) }}</span>
                        <span class="old-price" style="text-decoration: line-through; color: #999; margin-left: 10px; font-size: 0.9em;">₹{{ number_format($product->price * 1.25, 0) }}</span>
                        <span class="discount-badge" style="background: #e74c3c; color: #fff; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; margin-left: 10px; vertical-align: middle;">20% OFF</span>
                    </div>

                    <div class="stock-status" style="margin-top: 15px;">
                        @if($product->stock > 0)
                            <span class="stock-badge stock-in">● In Stock ({{ $product->stock }} left)</span>
                        @else
                            <span class="stock-badge stock-out" style="color: #666;">● Out of Stock</span>
                        @endif
                    </div>

                    <div class="product-description-short" style="margin-top: 20px; color: #555; line-height: 1.6;">
                        {!! Str::limit(strip_tags($product->description), 250) !!}
                    </div>

                    <!-- Attributes: Color (Mocked) -->
                    <div class="attribute-section" style="margin-top: 25px;">
                        <h3 class="attribute-title" style="font-size: 14px; margin-bottom: 10px; text-transform: uppercase; color: #888;">Select Color:</h3>
                        <div class="swatch-container" style="display: flex; gap: 10px;">
                            <div class="color-swatch active" style="background: #A91B43; width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd; cursor: pointer;" title="Maroon"></div>
                            <div class="color-swatch" style="background: #D4AF37; width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd; cursor: pointer;" title="Gold"></div>
                            <div class="color-swatch" style="background: #000080; width: 30px; height: 30px; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 0 0 1px #ddd; cursor: pointer;" title="Navy"></div>
                        </div>
                    </div>

                    <!-- Attributes: Size/Type (Mocked) -->
                    <div class="attribute-section" style="margin-top: 20px;">
                        <h3 class="attribute-title" style="font-size: 14px; margin-bottom: 10px; text-transform: uppercase; color: #888;">Select Size / Length:</h3>
                        <div class="swatch-container" style="display: flex; gap: 10px;">
                            <button class="size-btn active" style="padding: 8px 15px; border: 1px solid #A91B43; background: #fff; color: #A91B43; border-radius: 5px; cursor: pointer;">Standard (6.2m)</button>
                            <button class="size-btn" style="padding: 8px 15px; border: 1px solid #ddd; background: #fff; color: #666; border-radius: 5px; cursor: pointer;">Long (7.5m)</button>
                        </div>
                    </div>

                    <!-- Quantity / Actions -->
                    <div class="product-actions-group" style="margin-top: 30px;">
                        <div class="quantity-picker" style="margin-bottom: 20px; display: flex; align-items: center; border: 1px solid #ddd; width: fit-content; border-radius: 5px;">
                            <button class="qty-btn" onclick="updateQty(-1)" style="padding: 5px 15px; background: none; border: none; font-size: 20px; cursor: pointer;">−</button>
                            <input type="text" class="qty-input" value="1" readonly id="qtyDisp" style="width: 40px; text-align: center; border: none; font-weight: 600;">
                            <button class="qty-btn" onclick="updateQty(1)" style="padding: 5px 15px; background: none; border: none; font-size: 20px; cursor: pointer;">+</button>
                        </div>

                        <div class="product-actions" style="display: flex; gap: 15px; align-items: center;">
                            <button class="btn-add-cart" style="flex: 1; background: #A91B43; color: #fff; padding: 15px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                Add to Cart
                            </button>
                            <button class="btn-buy-now" style="flex: 1; background: #333; color: #fff; padding: 15px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                                Buy It Now
                            </button>
                            <button class="btn-wishlist-detail" aria-label="Add to Wishlist" style="background: #fdf2f5; border: none; padding: 10px; border-radius: 8px; cursor: pointer;">
                                <img src="{{ asset('images/favorite.svg') }}" alt="" width="24">
                            </button>
                        </div>
                    </div>

                    <div class="share-section" style="margin-top: 25px; border-top: 1px solid #eee; padding-top: 20px;">
                        <span class="share-title" style="font-size: 14px; color: #888;">Share this product:</span>
                        <div class="share-links" style="display: flex; gap: 15px; margin-top: 10px;">
                            <!-- SVG Social Icons (Simplified) -->
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/></svg></a>
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.417-.923 3.9 3.9 0 0 0 .923-1.417c.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.999 0zm0 1.44c2.144 0 2.405.008 3.25.047.781.036 1.206.166 1.493.28.384.148.658.324.945.61.285.286.463.56.611.944.113.287.243.712.28 1.493.038.845.046 1.106.046 3.25s-.008 2.405-.047 3.25c-.036.781-.166 1.206-.28 1.493-.148.384-.324.658-.61.945-.287.285-.56.463-.945.611-.286.113-.712.243-1.493.28-.845.038-1.106.047-3.25.047s-2.405-.009-3.25-.047c-.781-.036-1.206-.166-1.493-.28a3.14 3.14 0 0 1-.945-.611 3.14 3.14 0 0 1-.61-.945c-.114-.287-.244-.712-.28-1.493-.039-.845-.047-1.106-.047-3.25s.008-2.405.047-3.25c.036-.781.166-1.206.28-1.493.148-.384.324-.658.61-.945.286-.287.561-.463.945-.611.287-.113.712-.243 1.493-.28C5.594 1.448 5.854 1.44 8 1.44z"/><path d="M8 3.86a4.14 4.14 0 1 0 0 8.28 4.14 4.14 0 0 0 0-8.28zm0 6.84a2.7 2.7 0 1 1 0-5.4 2.7 2.7 0 0 1 0 5.4zm4.316-6.685a.972.972 0 1 1-1.944 0 .972.972 0 0 1 1.944 0z"/></svg></a>
                            <a href="#" style="color: #666;"><svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16"><path d="M13.601 2.326A7.854 7.854 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.061 3.972L0 16l4.204-1.102a7.933 7.933 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.898 7.898 0 0 0 13.6 2.326zM7.994 14.521a6.573 6.573 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.557 6.557 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592zm3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.729.729 0 0 0-.529.247c-.182.198-.691.677-.691 1.654 0 .977.71 1.916.81 2.049.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232z"/></svg></a>
                        </div>
                    </div>

                    <div class="delivery-check" style="margin-top: 30px; background: #fdfaf0; padding: 20px; border-radius: 10px;">
                        <p class="delivery-title" style="font-weight: 600; margin-bottom: 10px;">Check Delivery Availability</p>
                        <div class="pincode-input-group" style="display: flex; gap: 10px;">
                            <input type="text" class="pincode-input" placeholder="Enter Pincode" style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <button class="btn-pincode" style="background: #A91B43; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">Check</button>
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

                <div class="tab-pane active" id="tabDesc" style="padding-top: 30px;">
                    <div style="color: #666; line-height: 1.8;">
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
            <section class="related-products" style="margin-top: 80px;">
                <h2 style="font-size: 32px; color: #ad8b4e; margin-bottom: 40px; font-weight: 600; text-align: center;">Related Collections</h2>
                <div class="product-grid-main" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    {{-- Loop twice to "duplicate" products as per user request for visual density --}}
                    @foreach($relatedProducts->concat($relatedProducts) as $related)
                    <article class="product-card-v2">
                        <a href="{{ route('product.show', $related->slug) }}" style="text-decoration: none; color: inherit;">
                            <div class="product-image-v2">
                                <img src="{{ $related->image_path ? asset('images/' . $related->image_path) : asset('images/pro.png') }}" alt="{{ $related->name }}">
                            </div>
                            <div class="product-info-v2">
                                <h3 class="product-name-v2">{{ $related->name }}</h3>
                                <p class="product-price-v2">₹{{ number_format($related->price, 0) }}</p>
                            </div>
                        </a>
                    </article>
                    @endforeach
                </div>
            </section>
            @endif

            <!-- Recently Viewed Section (Mocked) -->
            <section class="recently-viewed" style="margin-top: 60px; margin-bottom: 60px;">
                <h2 style="font-size: 32px; color: #ad8b4e; margin-bottom: 40px; font-weight: 600; text-align: center;">Recently Viewed</h2>
                <div class="product-grid-main" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    @for($i = 0; $i < 2; $i++) {{-- Duplicate the mock items to fill the 4 columns --}}
                    <article class="product-card-v2">
                        <div class="product-image-v2">
                            <img src="{{ asset('images/pro1.png') }}" alt="Pure Silk Saree">
                        </div>
                        <div class="product-info-v2">
                            <h3 class="product-name-v2">Pure Silk Saree</h3>
                            <p class="product-price-v2">₹4,290</p>
                        </div>
                    </article>
                    <article class="product-card-v2">
                        <div class="product-image-v2">
                            <img src="{{ asset('images/pro2.png') }}" alt="Banarasi Weave">
                        </div>
                        <div class="product-info-v2">
                            <h3 class="product-name-v2">Banarasi Weave</h3>
                            <p class="product-price-v2">₹3,890</p>
                        </div>
                    </article>
                    @endfor
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
            let current = parseInt(input.value);
            current += val;
            if (current < 1) current = 1;
            input.value = current;
        }
        function switchTab(e, tabId) {
            const btns = document.querySelectorAll('.tab-btn');
            btns.forEach(b => b.classList.remove('active'));
            e.currentTarget.classList.add('active');
            const panes = document.querySelectorAll('.tab-pane');
            panes.forEach(p => p.style.display = 'none');
            document.getElementById(tabId).style.display = 'block';
        }
    </script>
@endpush
