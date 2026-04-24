<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubCategory extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'image',
        'description',
        'specification',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'display_order'
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function childCategories()
    {
        return $this->hasMany(ChildCategory::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
