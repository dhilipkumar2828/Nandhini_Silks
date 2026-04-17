<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'description',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'display_order',
        'show_in_menu'
    ];

    protected $casts = [
        'status' => 'boolean',
        'show_in_menu' => 'boolean'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function subCategories()
    {
        return $this->hasMany(SubCategory::class);
    }

    public function childCategories()
    {
        return $this->hasMany(ChildCategory::class);
    }
}
