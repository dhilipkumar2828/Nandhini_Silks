<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'order_number',
        'coupon_id',
        'coupon_code',
        'customer_name',
        'customer_email',
        'customer_phone',
        'billing_name',
        'billing_email',
        'billing_phone',
        'different_billing_address',
        'sub_total',
        'discount',
        'tax',
        'shipping',
        'grand_total',
        'payment_method',
        'payment_status',
        'order_status',
        'delivery_address',
        'billing_address',
        'admin_notes',
        'tracking_number',
        'courier_name',
    ];

    protected $casts = [
        'sub_total'                 => 'decimal:2',
        'discount'                  => 'decimal:2',
        'tax'                       => 'decimal:2',
        'shipping'                  => 'decimal:2',
        'grand_total'               => 'decimal:2',
        'different_billing_address' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function getFormattedOrderNumberAttribute()
    {
        return $this->order_number ?? 'ORD-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getPaymentStatusBadgeAttribute()
    {
        return match($this->payment_status) {
            'paid'     => ['label' => 'Paid',     'class' => 'bg-emerald-50 text-emerald-600'],
            'refunded' => ['label' => 'Refunded', 'class' => 'bg-indigo-50 text-indigo-600'],
            'failed'   => ['label' => 'Failed',   'class' => 'bg-rose-50 text-rose-600'],
            default    => ['label' => 'Unpaid',   'class' => 'bg-amber-50 text-amber-600'],
        };
    }

    public function getOrderStatusBadgeAttribute()
    {
        return match($this->order_status) {
            'processing'  => ['label' => 'Processing',  'class' => 'bg-blue-50 text-blue-600'],
            'dispatched'  => ['label' => 'Dispatched',  'class' => 'bg-orange-50 text-orange-600'],
            'delivered'   => ['label' => 'Delivered',   'class' => 'bg-emerald-50 text-emerald-600'],
            'cancelled'   => ['label' => 'Cancelled',   'class' => 'bg-rose-50 text-rose-600'],
            default       => ['label' => 'Pending',     'class' => 'bg-slate-50 text-slate-600'],
        };
    }
}

