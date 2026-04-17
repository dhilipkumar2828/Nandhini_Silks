<article class="product-card-v2" data-product-id="{{ $product->id }}">
    <a href="{{ route('product.show', $product->slug) }}" class="product-card-link">
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
            @php $inWishlist = in_array($product->id, session('wishlist', [])); @endphp
            <button type="button" class="btn-wishlist-detail wishlist-btn" 
                    data-product-id="{{ $product->id }}" 
                    aria-label="{{ $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' }}">
                <i class="{{ $inWishlist ? 'fa-solid' : 'fa-regular' }} fa-heart"></i>
            </button>
        </div>
        <div class="product-info-v2">
            <div class="product-rating-v2" style="{{ $product->reviews_count > 0 ? '' : 'visibility: hidden;' }}">
                @php $rating = round($product->average_rating ?? 0); @endphp
                @for ($i = 1; $i <= 5; $i++)
                    @if ($i <= $rating)
                        <i class="fa-solid fa-star"></i>
                    @else
                        <i class="fa-regular fa-star"></i>
                    @endif
                @endfor
                <span class="review-count">({{ $product->reviews_count ?? 0 }})</span>
            </div>
            <span class="product-category-v2">{{ $product->category->name ?? 'Collection' }}</span>
            <h3 class="product-name-v2">{{ \Illuminate\Support\Str::limit($product->name, 55) }}</h3>
            {{-- <span class="read-more-link">Read More...</span> --}}
            <p class="product-desc-v2">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 100) }}</p>
            <div class="product-price-v2">
                @if($product->sale_price > 0)
                    <span class="price-current">₹{{ number_format($product->sale_price, 0) }}</span>
                    <span class="product-price-old" style="text-decoration: line-through !important;">₹{{ number_format($product->regular_price ?? $product->price, 0) }}</span>
                @else
                    <span class="price-current">₹{{ number_format($product->price, 0) }}</span>
                    @if(isset($product->regular_price) && $product->regular_price > $product->price)
                        <span class="product-price-old" style="text-decoration: line-through !important;">₹{{ number_format($product->regular_price, 0) }}</span>
                    @endif
                @endif
            </div>
        </div>
    </a>
    <a href="{{ route('product.show', $product->slug) }}" class="add-to-cart-v2">
        View Details
    </a>
</article>
