<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferCollection extends Model
{
    protected $fillable = ['name', 'slug', 'status'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
