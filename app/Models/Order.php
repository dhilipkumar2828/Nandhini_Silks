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
        'shipping_city',
        'shipping_state',
        'shipping_pincode',
        'shipping_country',
        'admin_notes',
        'tracking_number',
        'courier_name',
        // Shiprocket core fields
        'shiprocket_order_id',
        'shiprocket_shipment_id',
        'shiprocket_awb',
        'shiprocket_status',
        'shiprocket_courier_id',
        'shiprocket_courier_name',
        'shiprocket_label_url',
        'shiprocket_manifest_url',
        'shiprocket_invoice_url',
        'shiprocket_webhook_status',
        'pickup_scheduled_at',
        'edd',
        // Return fields
        'return_status',
        'return_reason',
        'return_admin_notes',
        'reverse_awb',
        'shiprocket_return_order_id',
        'shiprocket_return_shipment_id',
        'shipment_track_activities',
    ];

    protected $casts = [
        'sub_total'                 => 'decimal:2',
        'discount'                  => 'decimal:2',
        'tax'                       => 'decimal:2',
        'shipping'                  => 'decimal:2',
        'grand_total'               => 'decimal:2',
        'different_billing_address' => 'boolean',
        'shipment_track_activities' => 'array',
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
            'order placed'     => ['label' => 'Order Placed',     'class' => 'bg-slate-50 text-slate-600'],
            'processing'       => ['label' => 'Processing',       'class' => 'bg-amber-50 text-amber-600'],
            'ready to ship'    => ['label' => 'Ready to Ship',    'class' => 'bg-indigo-50 text-indigo-600'],
            'shipped'          => ['label' => 'Shipped',          'class' => 'bg-blue-50 text-blue-600'],
            'out for delivery' => ['label' => 'Out for Delivery', 'class' => 'bg-emerald-50 text-emerald-600'],
            'delivered'        => ['label' => 'Delivered',        'class' => 'bg-teal-50 text-teal-600'],
            'cancelled'        => ['label' => 'Cancelled',        'class' => 'bg-rose-50 text-rose-600'],
            'returned'         => ['label' => 'Returned',         'class' => 'bg-purple-50 text-purple-600'],
            'refunded'         => ['label' => 'Refunded',         'class' => 'bg-indigo-50 text-indigo-600'],
            default            => ['label' => 'Order Placed',     'class' => 'bg-slate-50 text-slate-600'],
        };
    }

    public function getReturnStatusBadgeAttribute()
    {
        return match($this->return_status) {
            'requested' => ['label' => 'Return Requested', 'class' => 'bg-amber-50 text-amber-600'],
            'approved'  => ['label' => 'Approved',         'class' => 'bg-emerald-50 text-emerald-600'],
            'rejected'  => ['label' => 'Rejected',         'class' => 'bg-rose-50 text-rose-600'],
            'picked'    => ['label' => 'Picked Up',        'class' => 'bg-blue-50 text-blue-600'],
            'received'  => ['label' => 'Received',         'class' => 'bg-indigo-50 text-indigo-600'],
            'refunded'  => ['label' => 'Refunded',         'class' => 'bg-emerald-50 text-emerald-600'],
            default     => ['label' => 'No Return',        'class' => 'bg-slate-50 text-slate-600'],
        };
    }
    public function syncStatus($statusInput, $shiprocketStatus = null, $trackingNumber = null)
    {
        $newStatus = strtolower(trim($statusInput));
        $oldStatus = $this->order_status;

        // Prevent overwriting a final state with an earlier one
        // (e.g., don't revert 'delivered' back to 'shipped' via cron glitch)
        $statusPriority = [
            'order placed'     => 1,
            'processing'       => 2,
            'ready to ship'    => 3,
            'shipped'          => 4,
            'out for delivery' => 5,
            'delivered'        => 6,
            'returned'         => 7,
            'refunded'         => 8,
            'cancelled'        => 9,
        ];

        $oldPriority = $statusPriority[$oldStatus] ?? 0;
        $newPriority = $statusPriority[$newStatus] ?? 0;

        // Only allow backwards movement for 'cancelled', 'returned', and 'processing' (reset) explicitly
        if ($newPriority < $oldPriority && !in_array($newStatus, ['cancelled', 'returned', 'processing'])) {
            \Illuminate\Support\Facades\Log::warning("Skipping status downgrade for Order #{$this->order_number}: {$oldStatus} → {$newStatus}");
            return false;
        }

        // If transitioning TO Cancelled or Processing, try to cancel the order in Shiprocket if it exists
        if (in_array($newStatus, ['cancelled', 'processing']) && $this->shiprocket_order_id) {
            try {
                $shiprocket = new \App\Services\ShiprocketService();
                $shiprocket->cancelOrder($this->shiprocket_order_id);
                \Illuminate\Support\Facades\Log::info("Cancelled Shiprocket Order #{$this->shiprocket_order_id} via syncStatus ({$newStatus})");
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to auto-cancel Shiprocket order during status change: " . $e->getMessage());
            }
        }

        $updates = ['order_status' => $newStatus];

        // If resetting to Processing or Cancelled, clear Shiprocket logistics info
        if (in_array($newStatus, ['processing', 'cancelled'])) {
            $updates['shiprocket_order_id']    = null;
            $updates['shiprocket_shipment_id'] = null;
            $updates['shiprocket_awb']         = null;
            $updates['shiprocket_status']      = null;
            $updates['shiprocket_courier_id']  = null;
            $updates['shiprocket_courier_name']= null;
            $updates['shiprocket_label_url']   = null;
            $updates['shiprocket_manifest_url']= null;
            $updates['shiprocket_invoice_url'] = null;
            $updates['pickup_scheduled_at']    = null;
            $updates['tracking_number']        = null;
            $updates['courier_name']           = null;
            $updates['edd']                    = null;
        }

        if ($shiprocketStatus && !in_array($newStatus, ['processing', 'cancelled'])) {
            $updates['shiprocket_status'] = $shiprocketStatus;
        }

        if ($trackingNumber && !in_array($newStatus, ['processing', 'cancelled'])) {
            $updates['tracking_number'] = $trackingNumber;
            // Also update AWB if not already set
            if (!$this->shiprocket_awb) {
                $updates['shiprocket_awb'] = $trackingNumber;
            }
            // Update courier_name if shiprocket_courier_name is available
            if ($this->shiprocket_courier_name && !$this->courier_name) {
                $updates['courier_name'] = $this->shiprocket_courier_name;
            }
        }

        // Special Logic: Delivered → mark payment as Paid (for COD)
        if ($newStatus === 'delivered' && $this->payment_status !== 'paid') {
            $updates['payment_status'] = 'paid';
        }

        // Special Logic: Cancelled/Returned → restore stock + mark refunded if prepaid
        if (in_array($newStatus, ['cancelled', 'returned']) && !in_array($oldStatus, ['cancelled', 'returned'])) {
            $this->restoreStock();
            if ($this->payment_status === 'paid' && $newStatus === 'cancelled') {
                $updates['payment_status'] = 'refunded';
            }
        }

        $this->update($updates);

        // Send Emails only if status actually changed
        if ($oldStatus !== $newStatus) {
            try {
                \Illuminate\Support\Facades\Mail::to($this->customer_email)->send(new \App\Mail\OrderStatusUpdate($this));
                $adminEmail = \App\Models\Setting::getAdminEmail();
                \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\OrderStatusUpdate($this, true));
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Failed to send status update email for Order #{$this->order_number}: " . $e->getMessage());
            }
        }

        return true;
    }

    public function restoreStock()
    {
        foreach ($this->items as $item) {
            $product = $item->product;
            if ($product) {
                $variantId = $item->variant_id;
                $restoreQty = (int) $item->quantity;

                if ($variantId) {
                    $variant = \App\Models\ProductVariant::find($variantId);
                    if ($variant) {
                        $variant->increment('stock_quantity', $restoreQty);
                        \App\Models\StockMovement::create([
                            'product_id' => $product->id,
                            'type' => 'restock',
                            'quantity' => $restoreQty,
                            'balance_after' => $variant->stock_quantity,
                            'reason' => 'Restored: Order #' . $this->order_number . ' ' . $this->order_status,
                        ]);
                    }
                } else {
                    $product->increment('stock_quantity', $restoreQty);
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'restock',
                        'quantity' => $restoreQty,
                        'balance_after' => $product->stock_quantity,
                        'reason' => 'Restored: Order #' . $this->order_number . ' ' . $this->order_status,
                    ]);
                }
                
                // Sync parent stock
                if ($product->product_variants->count() > 0) {
                    $totalVariantStock = $product->product_variants->sum('stock_quantity');
                    $product->update([
                        'stock_quantity' => $totalVariantStock,
                        'stock_status' => $totalVariantStock > 0 ? 'instock' : 'outofstock'
                    ]);
                } else {
                    if ($product->stock_quantity > 0) {
                        $product->update(['stock_status' => 'instock']);
                    }
                }
            }
        }
    }

    public function reduceStock()
    {
        foreach ($this->items as $item) {
            $product = $item->product;
            if (!$product) continue;

            $variantId = $item->variant_id;
            $itemQty = (int) $item->quantity;

            if ($variantId) {
                // LOCK THE VARIANT ROW - Prevent concurrent access
                $variant = \App\Models\ProductVariant::where('id', $variantId)->lockForUpdate()->first();
                if (!$variant) {
                    throw new \Exception("Product variant no longer available.");
                }

                if ($variant->stock_quantity < $itemQty) {
                    $itemDetails = $item->product_name ?? '-';
                    throw new \Exception("Sorry, only {$variant->stock_quantity} items left for " . $itemDetails . ". Someone else might have just purchased the remaining stock.");
                }

                $newVStock = $variant->stock_quantity - $itemQty;
                $variant->update(['stock_quantity' => $newVStock]);

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => $itemQty,
                    'balance_after' => $newVStock,
                    'reason' => 'Sold variant ' . $variant->sku . ' in Order #' . $this->order_number,
                ]);
            } else {
                // LOCK THE PRODUCT ROW - Prevent concurrent access
                $lockedProduct = \App\Models\Product::where('id', $product->id)->lockForUpdate()->first();
                if (!$lockedProduct) {
                    throw new \Exception("Product no longer available.");
                }

                if ($lockedProduct->stock_quantity < $itemQty) {
                    throw new \Exception("Sorry, only {$lockedProduct->stock_quantity} items left for " . $lockedProduct->name . ".");
                }

                $newStock = $lockedProduct->stock_quantity - $itemQty;
                $lockedProduct->update(['stock_quantity' => $newStock]);

                \App\Models\StockMovement::create([
                    'product_id' => $product->id,
                    'type' => 'sale',
                    'quantity' => $itemQty,
                    'balance_after' => $newStock,
                    'reason' => 'Sold in Order #' . $this->order_number,
                ]);
            }

            // Sync parent product stock status
            $this->syncProductStockStatus($product);
        }

        if ($this->coupon_id) {
            $coupon = \App\Models\Coupon::find($this->coupon_id);
            if ($coupon) {
                $coupon->increment('times_used');
            }
        }
    }

    private function syncProductStockStatus($product)
    {
        // Re-read fresh data after reduction
        $product = $product->fresh(['product_variants']);
        
        if ($product->product_variants->count() > 0) {
            $totalVariantStock = $product->product_variants->sum('stock_quantity');
            $product->update([
                'stock_quantity' => $totalVariantStock,
                'stock_status' => $totalVariantStock > 0 ? 'instock' : 'outofstock'
            ]);
        } else {
            if ($product->stock_quantity <= 0) {
                $product->update(['stock_status' => 'outofstock']);
            } else {
                $product->update(['stock_status' => 'instock']);
            }
        }
    }
}

