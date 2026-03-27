<article class="product-card-v2" data-product-id="{{ $product->id }}">
    <div class="card-actions-overlay">
        @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
        <button class="overlay-btn wishlist-btn {{ $inWishlist ? 'active' : '' }}" 
                data-product-id="{{ $product->id }}" 
                aria-label="Add to Wishlist">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="{{ $inWishlist ? '#A91B43' : 'currentColor' }}">
                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
            </svg>
        </button>
    </div>
    <a href="{{ route('product.show', $product->slug) }}" class="product-link">
        <div class="product-image-v2">
            @php
                $imagePath = $product->image_path;
                if (!$imagePath && !empty($product->images)) {
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    $imagePath = (is_array($images) && count($images) > 0) ? $images[0] : null;
                }
                
                $displayImage = asset('images/pro.png');
                if ($imagePath) {
                    if (Str::startsWith($imagePath, 'products/') || Str::startsWith($imagePath, 'categories/')) {
                        $displayImage = asset('uploads/' . $imagePath);
                    } elseif (Str::startsWith($imagePath, 'images/')) {
                        $displayImage = asset($imagePath);
                    } else {
                        $displayImage = asset('images/' . $imagePath);
                    }
                }
            @endphp
            <img src="{{ $displayImage }}" alt="{{ $product->name }}" loading="lazy">
        </div>
        <div class="product-info-v2">
            <div class="product-rating-v2">★★★★★</div>
            <span class="product-category-v2">{{ $product->category->name ?? 'Collection' }}</span>
            <h3 class="product-name-v2">{{ $product->name }}</h3>
            <p class="product-desc-v2">{{ Str::limit(strip_tags($product->description), 120) }}</p>
            <div class="product-price-v2">
                @if($product->sale_price > 0)
                    <span class="price-current">₹{{ number_format($product->sale_price, 0) }}</span>
                    <span class="product-price-old">₹{{ number_format($product->regular_price ?? $product->price, 0) }}</span>
                @else
                    <span class="price-current">₹{{ number_format($product->price, 0) }}</span>
                    @if(isset($product->regular_price) && $product->regular_price > $product->price)
                        <span class="product-price-old">₹{{ number_format($product->regular_price, 0) }}</span>
                    @endif
                @endif
            </div>
        </div>
    </a>
    <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-v2">
        View Collection
    </a>
</article>
