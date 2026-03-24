<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = [
        'shipping_class_id',
        'name',
        'country',
        'state',
        'zip',
        'cost',
        'status',
        'display_order'
    ];

    public function shippingClass()
    {
        return $this->belongsTo(ShippingClass::class);
    }
}
