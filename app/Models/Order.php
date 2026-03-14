<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'sub_total',
        'discount',
        'tax',
        'shipping',
        'grand_total',
        'payment_method',
        'payment_status',
        'order_status',
        'delivery_address',
        'admin_notes',
        'tracking_number',
        'courier_name',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
