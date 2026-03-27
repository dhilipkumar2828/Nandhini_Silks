<article class="product-card-v2" data-product-id="{{ $product->id }}">
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-link" style="text-decoration: none; color: inherit; display: block;">
        <div class="product-image-v2" style="position: relative;">
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
            @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
            <button type="button" class="btn-wishlist-detail wishlist-btn" 
                    data-product-id="{{ $product->id }}" 
                    aria-label="{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                    style="position: absolute; top: 15px; right: 15px; width: 42px; height: 42px; background: rgba(255,255,255,0.9); border: none; display: flex; align-items: center; justify-content: center; border-radius: 50%; cursor: pointer; box-shadow: 0 4px 10px rgba(0,0,0,0.1); z-index: 10;">
                <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart" 
                   style="color: #A91B43; font-size: 18px;"></i>
            </button>
        </div>
        <div class="product-info-v2">
            @if($product->reviews_count > 0)
            <div class="product-rating-v2" style="color: #f1c40f; font-size: 14px; margin-bottom: 5px;">
                @php $rating = round($product->average_rating); @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $rating)
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-regular fa-star"></i>
                    @endif
                @endfor
                <span style="color: #666; font-size: 11px; margin-left: 4px;">({{ $product->reviews_count }})</span>
            </div>
            @endif
            <span class="product-category-v2" style="font-size: 10px; text-transform: uppercase; color: #888; letter-spacing: 1px; font-weight: 700;">{{ $product->category->name ?? 'Collection' }}</span>
            <h3 class="product-name-v2" style="font-size: 15px; font-weight: 700; color: #1a1a1a; margin: 3px 0 8px;">{{ $product->name }}</h3>
            <p class="product-desc-v2" style="font-size: 12px; color: #666; height: 35px; overflow: hidden; margin-bottom: 12px;">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 50) }}</p>
            <div class="product-price-v2" style="margin-top: 10px; display: flex; align-items: center; gap: 10px;">
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
        View Details
    </a>
</article>
