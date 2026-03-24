<article class="product-card-v2" data-product-id="{{ $product->id }}">
    <div class="card-actions-overlay">
        <button class="overlay-btn wishlist-toggle {{ in_array($product->id, session('wishlist', [])) ? 'active' : '' }}" 
                data-product-id="{{ $product->id }}" 
                aria-label="Add to Wishlist">
            <i class="{{ in_array($product->id, session('wishlist', [])) ? 'fa-solid' : 'fa-regular' }} fa-heart" style="color: #A91B43; font-size: 16px;"></i>
        </button>
    </div>
    <a href="{{ route('product.show', $product->slug) }}" style="text-decoration: none; color: inherit;">
        <div class="product-image-v2">
            @php
                $imagePath = $product->image_path;
                if (!$imagePath && method_exists($product, 'images')) {
                    $images = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    $imagePath = (is_array($images) && count($images) > 0) ? $images[0] : null;
                }
            @endphp
            <img src="{{ $imagePath ? (str_contains($imagePath, 'uploads/') ? asset($imagePath) : asset('images/' . $imagePath)) : asset('images/pro.png') }}"
                 alt="{{ $product->name }}">
        </div>
        <div class="product-info-v2">
            <div class="product-rating-v2" style="color: #f1c40f; font-size: 14px; margin-bottom: 5px;">★★★★★</div>
            <span class="product-category-v2" style="font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 1px; font-weight: 700;">{{ $product->category->name ?? 'Collection' }}</span>
            <h3 class="product-name-v2" style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 3px 0 8px;">{{ $product->name }}</h3>
            <p class="product-desc-v2" style="font-size: 12px; color: #666; height: 35px; overflow: hidden; margin-bottom: 12px;">{{ Str::limit(strip_tags($product->description), 50) }}</p>
            <div class="product-price-v2" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
                @if($product->sale_price > 0)
                    <span style="color: #A91B43; font-weight: 800; font-size: 18px;">₹{{ number_format($product->sale_price, 0) }}</span>
                    <span class="product-price-old" style="text-decoration: line-through; color: #999; font-size: 12px;">₹{{ number_format($product->regular_price ?? $product->price, 0) }}</span>
                @else
                    <span style="color: #A91B43; font-weight: 800; font-size: 18px;">₹{{ number_format($product->price, 0) }}</span>
                    @if(isset($product->regular_price) && $product->regular_price > $product->price)
                        <span class="product-price-old" style="text-decoration: line-through; color: #999; font-size: 12px;">₹{{ number_format($product->regular_price, 0) }}</span>
                    @endif
                @endif
            </div>
        </div>
    </a>
    <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-v2" style="text-decoration: none; background: #fffcfd; color: #A91B43; border-top: 1px solid #eee; display: flex; align-items: center; justify-content: center; gap: 8px; font-size: 13px; font-weight: 700; padding: 12px;">
        <i class="fa-solid fa-eye" style="font-size: 12px;"></i> View Details
    </a>
</article>
