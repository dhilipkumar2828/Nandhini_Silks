<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = [
        'attribute_id',
        'name',
        'slug',
        'swatch_value',
        'display_order',
        'status',
    ];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }
}
