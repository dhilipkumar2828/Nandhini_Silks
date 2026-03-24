<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    private function getTaxRate(Product $product): float
    {
        if ($product->taxClass && $product->taxClass->rates->isNotEmpty()) {
            $activeRate = $product->taxClass->rates->where('status', 1)->first();
            return $activeRate ? ($activeRate->rate / 100) : 0.05;
        }
        return 0.05;
    }

    public function index()
    {
        $cart = $this->getCart();
        $items = [];
        foreach ($cart as $key => $item) {
            $item['key'] = $key;
            $items[] = $item;
        }
        $totals = $this->calculateTotals($items);

        return view('frontend.cart', [
            'items' => $items,
            'subTotal' => $totals['subTotal'],
            'discount' => $totals['discount'],
            'tax' => $totals['tax'],
            'shipping' => $totals['shipping'],
            'grandTotal' => $totals['grandTotal'],
            'itemCount' => $totals['itemCount'],
            'coupon' => $totals['coupon'],
        ]);
    }

    public function add(Request $request, Product $product)
    {
        Log::info('ADD TO CART REQUEST:', $request->all());
        $quantity = max(1, (int) $request->input('quantity', 1));
        $attributes = $request->input('attributes', []);
        $cart = $this->getCart();

        // Create a unique key for the cart item based on product ID and attributes
        $cartKey = $product->id;
        if (!empty($attributes)) {
            ksort($attributes);
            foreach ($attributes as $id => $val) {
                $cartKey .= '_' . $id . '_' . $val;
            }
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $price = $this->productPrice($product);
            $imagePath = $this->productImagePath($product);
            $size = '';
            $color = '';

            // Try to find matching variant for specific image/price
            if (!empty($attributes)) {
                $variants = $product->product_variants;
                foreach ($variants as $variant) {
                    $combination = $variant->combination; 
                    if (!is_array($combination)) {
                        $combination = is_string($combination) ? json_decode($combination, true) : [];
                    }

                    Log::info("CHECKING VARIANT {$variant->id}:", ['combination' => $combination, 'attributes' => $attributes]);
                    $match = true;
                    foreach ($attributes as $aid => $avid) {
                        $avidInt = (int) $avid;
                        if (!isset($combination[$aid]) || !in_array($avidInt, $combination[$aid])) {
                            $match = false;
                            break;
                        }
                    }
                    if ($match) {
                        Log::info("MATCH FOUND! Variant ID: {$variant->id}");
                        if ($variant->price > 0) $price = $variant->price;
                        if ($variant->sale_price > 0) $price = $variant->sale_price;
                        if ($variant->image) $imagePath = $variant->image;
                        break;
                    }
                }

                // Get size and color names for display
                foreach($attributes as $aid => $avid) {
                    $val = \App\Models\AttributeValue::with('attribute')->find($avid);
                    if($val && $val->attribute) {
                        $attrName = strtolower($val->attribute->name);
                        if(str_contains($attrName, 'size')) $size = $val->name;
                        if(str_contains($attrName, 'color')) $color = $val->name;
                    }
                }
            }

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $price,
                'image_path' => $imagePath,
                'image_url' => $this->productImageUrl($imagePath),
                'quantity' => $quantity,
                'attributes' => $attributes,
                'size' => $size,
                'color' => $color,
            ];
        }

        $this->putCart($cart);

        $action = $request->input('action', 'cart');
        if ($action === 'checkout') {
            return redirect()->route('checkout');
        }

        return redirect()->route('cart')->with('success', 'Added to cart.');
    }

    public function update(Request $request)
    {
        $quantities = $request->input('quantities', []);
        $cart = $this->getCart();

        foreach ($quantities as $key => $qty) {
            if (!isset($cart[$key])) {
                continue;
            }

            $qty = (int) $qty;
            if ($qty <= 0) {
                unset($cart[$key]);
            } else {
                $cart[$key]['quantity'] = $qty;
            }
        }

        $this->putCart($cart);

        if ($request->input('action') === 'checkout') {
            return redirect()->route('checkout');
        }

        return redirect()->route('cart')->with('success', 'Cart updated.');
    }

    public function remove($key)
    {
        $cart = $this->getCart();
        if (isset($cart[$key])) {
            unset($cart[$key]);
        }
        $this->putCart($cart);

        return redirect()->route('cart')->with('success', 'Item removed.');
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper(trim($request->input('code')));
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon || !$coupon->status) {
            return redirect()->back()->withInput()->with('error', 'Invalid or inactive coupon code.');
        }

        session()->put('coupon_id', $coupon->id);

        $cart = $this->getCart();
        $items = array_values($cart);
        $totals = $this->calculateTotals($items);

        if (!$totals['coupon']) {
            session()->forget('coupon_id');
            return redirect()->back()->withInput()->with('error', 'Coupon is not applicable to your cart.');
        }

        return redirect()->back()->withInput()->with('success', 'Coupon applied successfully.');
    }

    public function removeCoupon()
    {
        session()->forget('coupon_id');
        return redirect()->back()->withInput()->with('success', 'Coupon removed.');
    }

    public function checkout()
    {
        $cart = $this->getCart();
        $items = [];
        foreach ($cart as $key => $item) {
            $item['key'] = $key;
            $items[] = $item;
        }

        if (count($items) === 0) {
            return redirect()->route('shop')->with('error', 'Your cart is empty.');
        }

        $totals = $this->calculateTotals($items);
        $addresses = auth()->check() ? auth()->user()->addresses : collect();

        return view('frontend.checkout', [
            'items' => $items,
            'subTotal' => $totals['subTotal'],
            'discount' => $totals['discount'],
            'tax' => $totals['tax'],
            'shipping' => $totals['shipping'],
            'grandTotal' => $totals['grandTotal'],
            'itemCount' => $totals['itemCount'],
            'coupon' => $totals['coupon'],
            'addresses' => $addresses,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $cart = $this->getCart();
        $items = array_values($cart);

        if (count($items) === 0) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'delivery_address' => 'required|string',
            'payment_method' => 'nullable|string|max:50',
        ]);

        $totals = $this->calculateTotals($items);
        $coupon = $totals['coupon'];

        if ($coupon) {
            // Coupon logic already implemented as requested
            if ($coupon->first_order_only) {
                $hasOrders = Order::where('customer_email', $request->input('customer_email'))->exists();
                if ($hasOrders) {
                    session()->forget('coupon_id');
                    return redirect()->route('checkout')->with('error', 'This coupon is valid for first-time orders only.');
                }
            }

            if ($coupon->per_user_limit) {
                $usedCount = Order::where('coupon_id', $coupon->id)
                    ->where('customer_email', $request->input('customer_email'))
                    ->count();
                if ($usedCount >= $coupon->per_user_limit) {
                    session()->forget('coupon_id');
                    return redirect()->route('checkout')->with('error', 'You have reached the usage limit for this coupon.');
                }
            }
        }

        $isDifferentBilling = !$request->has('same_as_shipping');
        $paymentMethod = $request->input('payment_method', 'cod');

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'coupon_id' => $coupon ? $coupon->id : null,
                'coupon_code' => $coupon ? $coupon->code : null,
                'customer_name' => $request->input('customer_name'),
                'customer_email' => $request->input('customer_email'),
                'customer_phone' => $request->input('customer_phone'),
                'billing_name' => $isDifferentBilling ? $request->input('billing_name') : $request->input('customer_name'),
                'billing_email' => $isDifferentBilling ? $request->input('billing_email') : $request->input('customer_email'),
                'billing_phone' => $isDifferentBilling ? $request->input('billing_phone') : $request->input('customer_phone'),
                'different_billing_address' => $isDifferentBilling,
                'sub_total' => $totals['subTotal'],
                'discount' => $totals['discount'],
                'tax' => $totals['tax'],
                'shipping' => $totals['shipping'],
                'grand_total' => $totals['grandTotal'],
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'order_status' => 'pending',
                'delivery_address' => $request->input('delivery_address'),
                'billing_address' => $isDifferentBilling ? $request->input('billing_address') : $request->input('delivery_address'),
            ]);

            // Increment Coupon usage if applicable
            $coupon = $totals['coupon'];
            if ($coupon) {
                $coupon->increment('times_used');
            }

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image_path'] ?? null,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Deduction of Stock with Movement Log [PERFECT FIX]
                if ($product) {
                    $oldStock = (int) $product->stock_quantity;
                    $itemQty = (int) $item['quantity'];
                    $newStock = max(0, $oldStock - $itemQty);

                    $product->update(['stock_quantity' => $newStock]);
                    
                    StockMovement::create([
                        'product_id' => $product->id,
                        'type' => 'sale',
                        'quantity' => $itemQty,
                        'balance_after' => $newStock,
                        'reason' => 'Sold in Order #' . $order->order_number,
                    ]);

                    // Update Status to Out of Stock if zero
                    if ($product->stock_quantity <= 0) {
                        $product->update(['stock_status' => 'outofstock']);
                    }
                }
            }

            if ($coupon) {
                $coupon->increment('times_used');
            }

            DB::commit();
            if ($paymentMethod === 'razorpay') {
                return $this->processRazorpay($order);
            }

            // Clear cart ONLY for COD here. Razorpay will clear in verifyRazorpay
            session()->forget(['cart', 'coupon_id']);
            return redirect()->route('my-orders')->with('success', 'Your order is completed successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order Placement Failed: ' . $e->getMessage());
            // Show the actual error message to help the user debug (e.g. Razorpay keys)
            return redirect()->route('checkout')->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    private function processRazorpay(Order $order)
    {
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $razorOrder = $api->order->create([
            'receipt' => (string) $order->id,
            'amount' => (int) ($order->grand_total * 100),
            'currency' => 'INR'
        ]);

        $order->update(['payment_id' => $razorOrder['id']]);

        return view('frontend.razorpay-payment', compact('order', 'razorOrder'));
    }

    public function verifyRazorpay(Request $request)
    {
        $signatureStatus = true;
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

        try {
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];
            $api->utility->verifyPaymentSignature($attributes);
        } catch (\Exception $e) {
            $signatureStatus = false;
        }

        if ($signatureStatus) {
            $order = Order::where('payment_id', $request->razorpay_order_id)->first();
            if ($order) {
                $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
                session()->forget(['cart', 'coupon_id']);
                return redirect()->route('my-orders')->with('success', 'Your order is completed successfully');
            }
        }

        return redirect()->route('checkout')->with('error', 'Payment failed or signature mismatch.');
    }

    public function orderConfirmation(Order $order = null)
    {
        if ($order) {
            $order->load('items.product');
        }

        return view('frontend.order-confirmation', [
            'order' => $order,
        ]);
    }

    private function getCart(): array
    {
        return session()->get('cart', []);
    }

    private function putCart(array $cart): void
    {
        session()->put('cart', $cart);
    }

    private function calculateTotals(array $items): array
    {
        $subTotal = 0;
        $itemCount = 0;

        foreach ($items as $item) {
            $subTotal += $item['price'] * $item['quantity'];
            $itemCount += $item['quantity'];
        }

        $subTotal = round($subTotal, 2);
        $shipping = 0;
        $couponResult = $this->resolveCoupon($items, $subTotal);
        $discount = $couponResult['discount'];
        $coupon = $couponResult['coupon'];
        $taxableAmount = max(0, $subTotal - $discount);
        
        // DYNAMIC TAX CALCULATION [PERFECT FIX]
        $tax = 0;
        foreach ($items as $item) {
            $product = Product::with('taxClass.rates')->find($item['product_id']);
            if ($product) {
                $rate = $this->getTaxRate($product);
                $tax += ($item['price'] * $item['quantity']) * $rate;
            }
        }

        $tax = round($tax, 2);
        $grandTotal = round($taxableAmount + $tax + $shipping, 2);

        return compact('subTotal', 'discount', 'tax', 'shipping', 'grandTotal', 'itemCount', 'coupon');
    }

    private function productPrice(Product $product): float
    {
        if (!is_null($product->sale_price) && $product->sale_price > 0) {
            return (float) $product->sale_price;
        }
        if (!is_null($product->regular_price) && $product->regular_price > 0) {
            return (float) $product->regular_price;
        }
        if (!is_null($product->price)) {
            return (float) $product->price;
        }

        return 0.0;
    }

    private function productImagePath(Product $product): ?string
    {
        if (!empty($product->image_path)) {
            return $product->image_path;
        }

        $images = $product->images;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }

        if (is_array($images) && count($images) > 0) {
            return $images[0];
        }

        return null;
    }

    private function productImageUrl(?string $imagePath): string
    {
        if (!$imagePath) {
            return asset('images/pro.png');
        }

        if (Str::startsWith($imagePath, 'images/')) {
            return asset($imagePath);
        }

        if (Str::startsWith($imagePath, 'products/') || Str::startsWith($imagePath, 'categories/')) {
            return asset('uploads/' . $imagePath);
        }

        return asset('images/' . $imagePath);
    }

    private function resolveCoupon(array $items, float $subTotal): array
    {
        $couponId = session()->get('coupon_id');
        if (!$couponId) {
            return ['coupon' => null, 'discount' => 0];
        }

        $coupon = Coupon::find($couponId);
        if (!$coupon || !$coupon->status) {
            return $this->invalidateCoupon();
        }

        $now = now();
        if ($coupon->valid_from && $now->lt($coupon->valid_from)) {
            return $this->invalidateCoupon();
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            return $this->invalidateCoupon();
        }
        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            return $this->invalidateCoupon();
        }
        if ($coupon->min_order_amount && $subTotal < $coupon->min_order_amount) {
            return $this->invalidateCoupon();
        }

        $eligibleSubtotal = $this->eligibleSubtotal($items, $coupon);
        if ($eligibleSubtotal <= 0) {
            return $this->invalidateCoupon();
        }

        if ($coupon->type === 'percentage') {
            $discount = $eligibleSubtotal * ($coupon->discount_value / 100);
        } else {
            $discount = $coupon->discount_value;
        }

        if ($coupon->max_discount) {
            $discount = min($discount, $coupon->max_discount);
        }

        $discount = min($discount, $eligibleSubtotal);

        return [
            'coupon' => $coupon,
            'discount' => round($discount, 2),
        ];
    }

    private function eligibleSubtotal(array $items, Coupon $coupon): float
    {
        $productIds = $coupon->applicable_products ?? [];
        $categoryIds = $coupon->applicable_categories ?? [];

        $sum = 0;

        if (empty($productIds) && empty($categoryIds)) {
            foreach ($items as $item) {
                $sum += $item['price'] * $item['quantity'];
            }
            return round($sum, 2);
        }

        $itemProductIds = array_column($items, 'product_id');
        $productCategories = Product::whereIn('id', $itemProductIds)->pluck('category_id', 'id')->toArray();

        foreach ($items as $item) {
            $productId = $item['product_id'];
            $categoryId = $productCategories[$productId] ?? null;

            if (in_array($productId, $productIds, false) || ($categoryId && in_array($categoryId, $categoryIds, false))) {
                $sum += $item['price'] * $item['quantity'];
            }
        }

        return round($sum, 2);
    }

    private function invalidateCoupon(): array
    {
        session()->forget('coupon_id');
        return ['coupon' => null, 'discount' => 0];
    }
}
