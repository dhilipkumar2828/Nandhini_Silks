<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildCategory extends Model
{
    protected $fillable = [
        'category_id',
        'sub_category_id',
        'name',
        'slug',
        'image',
        'description',
        'meta_title',
        'meta_description',
        'status',
        'display_order'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class);
    }
}
