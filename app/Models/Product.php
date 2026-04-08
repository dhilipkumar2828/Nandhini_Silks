<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'child_category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'brand',
        'short_description',
        'full_description',
        'isbn',
        'image',
        'images',
        'primary_image',
        'video_url',
        'price',
        'regular_price',
        'sale_price',
        'discount_percent',
        'tax_class',
        'tax_class_id',
        'stock_quantity',
        'reserved_stock',
        'low_stock_threshold',
        'stock_status',
        'restock_quantity',
        'restock_date',
        'offer_collection',
        'weight',
        'dimensions',
        'attributes',
        'variants',
        'color_images',
        'related_products',
        'tags',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'display_order',
        'show_offer_on_homepage',
        'is_featured'
    ];

    protected $casts = [
        'images'           => 'array',
        'attributes'       => 'array',
        'variants'         => 'array',
        'color_images'     => 'array',
        'related_products' => 'array',
        'tags'             => 'array',
        'status'           => 'string',
        'is_featured'      => 'boolean',
        'restock_date'     => 'date',
        'regular_price'    => 'decimal:2',
        'sale_price'       => 'decimal:2',
        'price'            => 'decimal:2',
        'discount_percent' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function childCategory()
    {
        return $this->belongsTo(ChildCategory::class);
    }

    public function taxClass()
    {
        return $this->belongsTo(TaxClass::class, 'tax_class_id');
    }



    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function product_variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function getAvailableStockAttribute()
    {
        return max(0, $this->stock_quantity - ($this->reserved_stock ?? 0));
    }

    public function getDiscountPercentAttribute($value)
    {
        if ($value) return $value;
        if ($this->regular_price > 0 && $this->sale_price && $this->sale_price < $this->regular_price) {
            return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100, 2);
        }
        return 0;
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class)->where('status', 1);
    }

    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('stars'), 1) ?: 0.0;
    }

    public function getReviewsCountAttribute()
    {
        return $this->reviews()->count();
    }

    public function offerCollections()
    {
        return $this->belongsToMany(OfferCollection::class);
    }
}

