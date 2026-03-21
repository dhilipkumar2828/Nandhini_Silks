<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'applicable_on',
        'applicable_products',
        'applicable_categories',
        'usage_limit',
        'per_user_limit',
        'times_used',
        'valid_from',
        'expires_at',
        'first_order_only',
        'status',
    ];

    protected $casts = [
        'applicable_products'   => 'array',
        'applicable_categories' => 'array',
        'valid_from'            => 'datetime',
        'expires_at'            => 'datetime',
        'first_order_only'      => 'boolean',
        'status'                => 'boolean',
        'discount_value'        => 'decimal:2',
        'min_order_amount'      => 'decimal:2',
        'max_discount'          => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getIsExpiredAttribute()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getIsActiveAndValidAttribute()
    {
        return $this->status && !$this->is_expired;
    }
}

