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
        'images',
        'video_url',
        'regular_price',
        'sale_price',
        'discount_percent',
        'tax_class',
        'stock_quantity',
        'low_stock_threshold',
        'stock_status',
        'weight',
        'dimensions',
        'shipping_class',
        'attributes',
        'variants',
        'related_products',
        'tags',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'is_featured'
    ];

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
        'variants' => 'array',
        'status' => 'boolean',
        'is_featured' => 'boolean'
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
}
