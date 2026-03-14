<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image_desktop',
        'image_mobile',
        'title',
        'link',
        'display_order',
        'status',
    ];
}
