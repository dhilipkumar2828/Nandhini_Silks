<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_image',
        'price',
        'tax_rate',
        'tax_amount',
        'quantity',
        'total',
        'size',
        'color',
        'attributes',
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImageUrl()
    {
        $path = $this->product_image;
        if (!$path && $this->product) {
            $path = $this->product->primary_image;
            
            // Fallback to images[0] if primary_image is empty
            if (!$path && !empty($this->product->images)) {
                $images = $this->product->images;
                if (is_string($images)) {
                    $images = json_decode($images, true);
                }
                if (is_array($images) && count($images) > 0) {
                    $path = $images[0];
                }
            }
        }

        if (!$path) {
            return asset('images/pro1.png');
        }

        if (\Illuminate\Support\Str::startsWith($path, 'products/') || \Illuminate\Support\Str::startsWith($path, 'categories/')) {
            return asset('uploads/' . $path);
        }

        return asset('images/' . $path);
    }
}
