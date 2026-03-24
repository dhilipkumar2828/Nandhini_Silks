<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'combination',
        'attribute_values',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'image',
        'images',
        'status',
    ];

    protected $casts = [
        'combination' => 'array',
        'attribute_values' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'images' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->price > 0 && $this->sale_price && $this->sale_price < $this->price) {
            return round((($this->price - $this->sale_price) / $this->price) * 100, 2);
        }
        return 0;
    }
}
