<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingClass extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'status', 'display_order'];

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }
}
