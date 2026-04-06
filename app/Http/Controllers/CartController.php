<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\UserAddress;
use App\Models\Coupon;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CartController extends Controller
{
    public function checkServiceability(Request $request)
    {
        $pincode = $request->input('pincode');
        $weight = $request->input('weight', 0.5);

        if (!$pincode) {
            return response()->json(['success' => false, 'message' => 'Please enter a pincode.']);
        }

        $shiprocket = new \App\Services\ShiprocketService();
        $result = $shiprocket->checkServiceability($pincode, $weight);
        
        Log::info('Shiprocket Serviceability Result:', $result);

        if ($result['status'] && isset($result['data']['available_courier_companies']) && count($result['data']['available_courier_companies']) > 0) {
            $courier = $result['data']['available_courier_companies'][0];
            $edd = $courier['etd'] ?? '3-5 days';

            // Save to session for stickiness
            session()->put('checked_pincode', $pincode);
            session()->put('checked_pincode_edd', $edd);

            return response()->json([
                'success' => true,
                'message' => "Estimated delivery by " . $edd,
                'edd' => $edd
            ]);
        }

        session()->forget(['checked_pincode', 'checked_pincode_edd']);
        return response()->json([
            'success' => false,
            'message' => $result['message'] ?? 'Delivery not available for this pincode.'
        ]);
    }

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
            'taxPercentage' => $totals['taxPercentage'],
            'shipping' => $totals['shipping'],
            'grandTotal' => $totals['grandTotal'],
            'itemCount' => $totals['itemCount'],
            'coupon' => $totals['coupon'],
        ]);
    }

    public function add(Request $request, Product $product)
    {
        Log::info('ADD TO CART REQUEST:', $request->all());
        $quantity = (int) $request->input('quantity', 1);
        // For non-ajax, we still want a minimum of 1
        if (!$request->ajax() && $quantity < 1) $quantity = 1;
        
        $attributes = $request->input('attributes', []);
        $cart = $this->getCart();

        // 1. Determine Variant
        $isVariant = $product->product_variants->count() > 0;
        $matchedVariant = null;
        $price = $this->productPrice($product);
        $imagePath = $this->productImagePath($product);
        $size = '';
        $color = '';

        if ($isVariant && !empty($attributes)) {
            foreach ($product->product_variants as $v) {
                $combination = $v->combination;
                if (is_string($combination)) {
                    $combination = json_decode($combination, true);
                }
                $combination = (array)$combination;
                $match = true;
                foreach ($attributes as $aid => $avid) {
                    if (!isset($combination[$aid]) || !in_array((int)$avid, (array)$combination[$aid])) {
                        $match = false; break;
                    }
                }
                if ($match) {
                    $matchedVariant = $v;
                    if ($v->price > 0) $price = $v->price;
                    if ($v->sale_price > 0) $price = $v->sale_price;
                    if ($v->image) $imagePath = $v->image;
                    break;
                }
            }
        }

        // 2. Selection Check & Mandatory Selection
        if ($isVariant && empty($attributes)) {
            return $this->errorResponse('Please select options first.', $request);
        }

        // 3. Stock Check
        $availableStock = $isVariant ? ($matchedVariant ? (int)$matchedVariant->stock_quantity : 0) : (int)$product->stock_quantity;
        
        Log::info('ADD TO CART CHECK:', [
            'product_id' => $product->id,
            'is_variant' => $isVariant,
            'variant_id' => $matchedVariant ? $matchedVariant->id : null,
            'available_stock' => $availableStock,
            'request_qty' => $quantity
        ]);

        if ($availableStock <= 0) {
            return $this->errorResponse('This product/variation is out of stock.', $request);
        }
        
        if ($availableStock < $quantity) {
             return $this->errorResponse('Only ' . $availableStock . ' items available.', $request);
        }

        // 4. Unique Key & Existing Qty Check
        $cartKey = (string)$product->id;
        if (!empty($attributes)) {
            ksort($attributes);
            foreach ($attributes as $aid => $avid) {
                $cartKey .= '_' . $aid . '_' . $avid;
            }
        }

        $existingQty = isset($cart[$cartKey]) ? (int)$cart[$cartKey]['quantity'] : 0;
        
        // If updating an existing item, we allow negative quantity for syncing
        if (isset($cart[$cartKey]) && ($existingQty + $quantity) <= 0) {
            unset($cart[$cartKey]);
            $this->putCart($cart);
            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Item removed.', 'cartCount' => collect($cart)->sum('quantity')]);
            }
            return redirect()->route('cart')->with('success', 'Item removed.');
        }

        if (($existingQty + $quantity) > $availableStock) {
            return $this->errorResponse('You already have ' . $existingQty . ' in cart. Total stock is ' . $availableStock . '.', $request);
        }

        // 5. Build/Update Cart Item
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            // Get Readable Names Dynamically
            $size = '';
            $color = '';
            $length = '';
            $age = '';
            $display_attributes = [];

            foreach ($attributes as $aid => $avid) {
                $val = \App\Models\AttributeValue::with('attribute')->find($avid);
                if ($val && $val->attribute) {
                    $attrName = $val->attribute->name;
                    $attrNameLower = strtolower($attrName);
                    
                    $display_attributes[] = [
                        'name' => $attrName,
                        'value' => $val->name
                    ];

                    if (str_contains($attrNameLower, 'size')) $size = $val->name;
                    elseif (str_contains($attrNameLower, 'color') || str_contains($attrNameLower, 'colour')) $color = $val->name;
                    elseif (str_contains($attrNameLower, 'length')) $length = $val->name;
                    elseif (str_contains($attrNameLower, 'age')) $age = $val->name;
                }
            }

            $cart[$cartKey] = [
                'product_id' => $product->id,
                'variant_id' => $matchedVariant ? $matchedVariant->id : null,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => (float)$price,
                'image_path' => $imagePath,
                'image_url' => $this->productImageUrl($imagePath),
                'quantity' => $quantity,
                'attributes' => $attributes,
                'display_attributes' => $display_attributes,
                'size' => $size,
                'color' => $color,
                'length' => $length,
                'age' => $age,
            ];
        }

        $this->putCart($cart);
        $totalItems = collect($cart)->sum('quantity');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true, 
                'message' => 'Successfully added to cart!', 
                'cartCount' => $totalItems
            ]);
        }
        
        $target = ($request->input('action') === 'checkout') ? 'checkout' : 'cart';
        return redirect()->route($target)->with('success', 'Added to cart.');
    }

    private function errorResponse($message, $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return redirect()->back()->with('error', $message)->withInput();
    }

    public function getMiniCart()
    {
        $cart = $this->getCart();
        $items = [];
        $totalItems = 0;
        foreach ($cart as $key => $item) {
            $item['key'] = $key;
            $items[] = $item;
            $totalItems += $item['quantity'];
        }
        $totals = $this->calculateTotals($items);
        
        return response()->json([
            'items' => $items,
            'subTotal' => $totals['subTotal'],
            'totalItems' => $totalItems
        ]);
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

        // AJAX request — return JSON totals for dynamic display
        if ($request->ajax() || $request->wantsJson()) {
            $items = array_values($cart);
            $totals = $this->calculateTotals($items);
            return response()->json([
                'success'    => true,
                'subTotal'   => $totals['subTotal'],
                'tax'        => $totals['tax'],
                'taxPercentage' => $totals['taxPercentage'],
                'shipping'   => $totals['shipping'],
                'discount'   => $totals['discount'],
                'grandTotal' => $totals['grandTotal'],
                'itemCount'  => $totals['itemCount'],
            ]);
        }

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
        
        $totalItems = collect($cart)->sum('quantity');

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item removed.',
                'count' => $totalItems
            ]);
        }

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
        $addresses = Auth::check() ? Auth::user()->addresses : collect();

        return view('frontend.checkout', [
            'items' => $items,
            'subTotal' => $totals['subTotal'],
            'discount' => $totals['discount'],
            'tax' => $totals['tax'],
            'taxPercentage' => $totals['taxPercentage'],
            'shipping' => $totals['shipping'],
            'grandTotal' => $totals['grandTotal'],
            'itemCount' => $totals['itemCount'],
            'coupon' => $totals['coupon'],
            'addresses' => $addresses,
        ]);
    }

    public function updateShippingDestination(Request $request)
    {
        $destination = [
            'country' => $request->input('country', 'India'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
        ];

        session()->put('shipping_destination', $destination);

        $cart = $this->getCart();
        $items = array_values($cart);
        $totals = $this->calculateTotals($items);

        return response()->json([
            'success' => true,
            'shipping' => $totals['shipping'],
            'tax' => $totals['tax'],
            'taxPercentage' => $totals['taxPercentage'],
            'grandTotal' => $totals['grandTotal'],
            'shippingFormatted' => $totals['shipping'] > 0 ? '₹' . number_format($totals['shipping'], 2) : 'FREE',
            'taxFormatted' => '₹' . number_format($totals['tax'], 2),
            'grandTotalFormatted' => '₹' . number_format($totals['grandTotal'], 2),
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
            'customer_phone' => ['required', 'regex:/^\d{10}$/'],
            'delivery_address' => 'required|string',
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'pincode' => ['required', 'regex:/^\d{6}$/'],
            'country' => ['required', 'string', 'max:255'],
            'billing_phone' => ['nullable', 'regex:/^\d{10}$/'],
            'billing_city' => ['nullable', 'string', 'max:255'],
            'billing_state' => ['nullable', 'string', 'max:255'],
            'billing_pincode' => ['nullable', 'regex:/^\d{6}$/'],
            'billing_country' => ['nullable', 'string', 'max:255'],
            'payment_method' => 'nullable|string|max:50',
            'save_address' => 'nullable|boolean',
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

        if ($isDifferentBilling) {
            $request->validate([
                'billing_name' => 'required|string|max:255',
                'billing_phone' => ['required', 'regex:/^\d{10}$/'],
                'billing_address' => 'required|string',
                'billing_city' => ['required', 'string', 'max:255'],
                'billing_state' => ['required', 'string', 'max:255'],
                'billing_pincode' => ['required', 'regex:/^\d{6}$/'],
                'billing_country' => ['required', 'string', 'max:255'],
                'billing_email' => 'required|email|max:255',
            ]);
        }

        // Build full delivery address from individual fields
        $addressParts = array_filter([
            $request->input('delivery_address'),
            $request->input('city'),
            $request->input('state'),
            $request->input('country'),
            $request->input('pincode') ? 'PIN: ' . $request->input('pincode') : null,
        ]);
        $fullDeliveryAddress = implode(', ', $addressParts);

        // Build billing address
        if ($isDifferentBilling && $request->filled('billing_address')) {
            $billingParts = array_filter([
                $request->input('billing_address'),
                $request->input('billing_city'),
                $request->input('billing_state'),
                $request->input('billing_country'),
                $request->input('billing_pincode') ? 'PIN: ' . $request->input('billing_pincode') : null,
            ]);
            $fullBillingAddress = implode(', ', $billingParts);
        } else {
            $fullBillingAddress = $fullDeliveryAddress;
        }

        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
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
                'order_status' => 'order placed',
                'delivery_address' => $fullDeliveryAddress,
                'billing_address' => $fullBillingAddress,
                'shipping_city' => $request->input('city'),
                'shipping_state' => $request->input('state'),
                'shipping_pincode' => $request->input('pincode'),
                'shipping_country' => $request->input('country', 'India'),
                'edd' => session('checked_pincode_edd'),
            ]);

            // Save Address if requested
            if (Auth::check() && $request->has('save_address')) {
                UserAddress::firstOrCreate([
                    'user_id' => Auth::id(),
                    'address1' => $request->input('delivery_address'),
                    'city' => $request->input('city'),
                    'state' => $request->input('state'),
                    'zip' => $request->input('pincode'),
                ], [
                    'label' => 'Standard Address',
                    'recipient_name' => $request->input('customer_name'),
                    'recipient_phone' => $request->input('customer_phone'),
                    'country' => $request->input('country', 'India'),
                    'is_default' => !Auth::user()->addresses()->exists(),
                ]);
            }

            // Increment Coupon usage if applicable
            // (We'll do this once, after saving the order items)

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'product_image' => $item['image_path'] ?? null,
                    'size' => $item['size'] ?? null,
                    'color' => $item['color'] ?? null,
                    'attributes' => $item['display_attributes'] ?? [],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total' => $item['price'] * $item['quantity'],
                ]);

                // Deduction of Stock with Movement Log [VARIANT AWARE FIX]
                if ($product) {
                    $variantId = $item['variant_id'] ?? null;
                    $itemQty = (int) $item['quantity'];
                    
                    if ($variantId) {
                        $variant = \App\Models\ProductVariant::find($variantId);
                        if ($variant) {
                            $oldVStock = (int) $variant->stock_quantity;
                            $newVStock = max(0, $oldVStock - $itemQty);
                            $variant->update(['stock_quantity' => $newVStock]);
                            
                            StockMovement::create([
                                'product_id' => $product->id,
                                'type' => 'sale',
                                'quantity' => $itemQty,
                                'balance_after' => $newVStock,
                                'reason' => 'Sold variant ' . $variant->sku . ' in Order #' . $order->order_number,
                            ]);
                        }
                    } else {
                        // Regular product (no variants)
                        $oldStock = (int) $product->stock_quantity;
                        $newStock = max(0, $oldStock - $itemQty);
                        $product->update(['stock_quantity' => $newStock]);
                        
                        StockMovement::create([
                            'product_id' => $product->id,
                            'type' => 'sale',
                            'quantity' => $itemQty,
                            'balance_after' => $newStock,
                            'reason' => 'Sold in Order #' . $order->order_number,
                        ]);
                    }

                    // Always sync parent product stock as the sum of its variants if it has any
                    if ($product->product_variants->count() > 0) {
                        $totalVariantStock = $product->product_variants->sum('stock_quantity');
                        $product->update([
                            'stock_quantity' => $totalVariantStock,
                            'stock_status' => $totalVariantStock > 0 ? 'instock' : 'outofstock'
                        ]);
                    } else {
                        // For simple products without variants
                        if ($product->stock_quantity <= 0) {
                            $product->update(['stock_status' => 'outofstock']);
                        }
                    }
                }
            }

            if ($coupon) {
                $coupon->increment('times_used');
            }

            DB::commit();

            if ($paymentMethod === 'razorpay') {
                try {
                    return $this->processRazorpay($order);
                } catch (\Throwable $paymentError) {
                    Log::error('Razorpay Order Creation Failed: ' . $paymentError->getMessage());
                    // Don't fail the whole order if just Razorpay creation fails
                    // but redirect back with a helpful message
                    return redirect()->route('checkout')->with('error', 'Payment gateway error: ' . $paymentError->getMessage() . '. Please try another method or contact us.');
                }
            }

            // Send order emails for COD
            $this->sendOrderEmails($order);

            // Push to Shiprocket for COD
            if ($paymentMethod === 'cod') {
                $shiprocket = new \App\Services\ShiprocketService();
                $srResult = $shiprocket->createOrder($order);
                if ($srResult['status']) {
                    Log::info('COD Order Pushed to Shiprocket: ' . $order->order_number);
                } else {
                    Log::error('Shiprocket COD Push Failed: ' . $srResult['message']);
                }
            }

            // Clear cart ONLY for COD here. Razorpay will clear in verifyRazorpay
            if (Auth::guard('web')->check()) {
                \App\Models\CartItem::where('user_id', Auth::guard('web')->id())->delete();
            }
            session()->forget(['cart', 'coupon_id', 'checked_pincode', 'checked_pincode_edd']);
            return redirect()->route('order-confirmation', $order)->with('success', 'Your order has been placed successfully! 🎉');

        } catch (\Throwable $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            Log::error('Order Placement Failed: ' . $e->getMessage());
            return redirect()->route('checkout')->with('error', 'Order failed: ' . $e->getMessage());
        }
    }

    private function sendOrderEmails(Order $order)
    {
        try {
            // Send to customer
            \Illuminate\Support\Facades\Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));
            
            // Small delay to avoid "Too many emails per second" (550 error)
            sleep(2);

            // Send to admin
            $adminEmail = \App\Models\Setting::getAdminEmail();
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\OrderAdminAlert($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order Email Failure: ' . $e->getMessage());
        }
    }

    /**
     * Always read Razorpay credentials fresh from the .env file on disk.
     * This bypasses both the config cache and the process environment
     * (so php artisan serve does NOT need to be restarted after .env changes).
     */
    private function getRazorpayCredentials(): array
    {
        $envPath = base_path('.env');
        $parsed  = [];
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                $line = trim($line);
                // Skip empty lines or comments
                if ($line === '' || str_starts_with($line, '#')) continue;
                
                if (str_contains($line, '=')) {
                    [$k, $v] = explode('=', $line, 2);
                    $key = trim($k);
                    $val = trim($v);
                    
                    // Strip inline comments if any (simple approach)
                    if (str_contains($val, ' #')) {
                       $val = trim(explode(' #', $val)[0]);
                    }
                    
                    // Strip quotes if any
                    if (str_starts_with($val, '"') && str_ends_with($val, '"')) {
                        $val = trim($val, '"');
                    } elseif (str_starts_with($val, "'") && str_ends_with($val, "'")) {
                        $val = trim($val, "'");
                    }
                    
                    $parsed[$key] = $val;
                }
            }
        }
        // Priority 1: Direct file read (bypasses cache)
        $fileKey = $parsed['RAZORPAY_KEY'] ?? null;
        $fileSecret = $parsed['RAZORPAY_SECRET'] ?? null;
        
        // Priority 2: config (loaded from env)
        $configKey = config('services.razorpay.key');
        $configSecret = config('services.razorpay.secret');
        
        // Priority 3: direct env() (as a last resort)
        $envKey = env('RAZORPAY_KEY');
        $envSecret = env('RAZORPAY_SECRET');
        
        $key = $fileKey ?: ($configKey ?: $envKey);
        $secret = $fileSecret ?: ($configSecret ?: $envSecret);
        
        Log::info('Razorpay Credentials Check:', [
            'key_found_in' => $fileKey ? 'file' : ($configKey ? 'config' : ($envKey ? 'env_function' : 'NOT FOUND')),
            'key_prefix' => $key ? substr($key, 0, 9) : 'NULL',
            'secret_len' => $secret ? strlen($secret) : 0
        ]);

        return [
            'key'    => $key,
            'secret' => $secret,
        ];
    }

    private function processRazorpay(Order $order)
    {
        $creds = $this->getRazorpayCredentials();
        $api   = new Api($creds['key'], $creds['secret']);

        Log::info('Razorpay processRazorpay — Key: ' . substr($creds['key'], 0, 12) . '... Secret len: ' . strlen($creds['secret']));

        $razorOrder = $api->order->create([
            'receipt'  => (string) $order->id,
            'amount'   => (int) ($order->grand_total * 100),
            'currency' => 'INR'
        ]);

        $razorOrderId = $razorOrder['id'];

        // Use DB::table for a guaranteed, direct UPDATE (bypasses model caching)
        DB::table('orders')
            ->where('id', $order->id)
            ->update(['payment_id' => $razorOrderId, 'updated_at' => now()]);

        // Verify it was actually saved
        $saved = DB::table('orders')->where('id', $order->id)->value('payment_id');
        Log::info('Razorpay processRazorpay — Saved payment_id in DB: ' . ($saved ?? 'NULL'));

        if ($saved !== $razorOrderId) {
            Log::error('Razorpay processRazorpay — payment_id DID NOT save! Expected: ' . $razorOrderId . ' Got: ' . ($saved ?? 'NULL'));
            throw new \RuntimeException('Failed to save payment_id to order.');
        }

        // Reload order so verifyRazorpay can find it later
        $order = Order::find($order->id);

        return view('frontend.razorpay-payment', compact('order', 'razorOrder'));
    }

    public function verifyRazorpay(Request $request)
    {
        $creds  = $this->getRazorpayCredentials();
        $key    = $creds['key'];
        $secret = $creds['secret'];

        Log::info('Razorpay Verify — Key: ' . substr($key, 0, 12) . '... Secret len: ' . strlen($secret));
        Log::info('Razorpay Verify — order_id: '   . $request->razorpay_order_id);
        Log::info('Razorpay Verify — payment_id: ' . $request->razorpay_payment_id);
        Log::info('Razorpay Verify — signature: '  . $request->razorpay_signature);

        // Confirm the order exists BEFORE even verifying signature
        $order = Order::where('payment_id', $request->razorpay_order_id)->first();
        Log::info('Razorpay Verify — DB lookup: ' . ($order ? 'Found order #' . $order->id : 'NOT FOUND'));

        if (!$order) {
            // Fallback: maybe payment_id wasn't saved — try to find by DB directly
            $orderId = DB::table('orders')
                ->where('payment_id', $request->razorpay_order_id)
                ->value('id');
            Log::info('Razorpay Verify — Fallback DB lookup order id: ' . ($orderId ?? 'NULL'));
            if ($orderId) {
                $order = Order::find($orderId);
            }
        }

        $signatureStatus = true;
        $api = new Api($key, $secret);

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id'   => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature'  => $request->razorpay_signature,
            ]);
            Log::info('Razorpay Verify — Signature OK ✅');
        } catch (\Exception $e) {
            Log::error('Razorpay Verify — Signature FAILED: ' . $e->getMessage());
            $signatureStatus = false;
        }

        if ($signatureStatus && $order) {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);

            if (Auth::check()) {
                \App\Models\CartItem::where('user_id', Auth::id())->delete();
            }
            session()->forget(['cart', 'coupon_id', 'checked_pincode', 'checked_pincode_edd']);

            $this->sendOrderEmails($order);

            // Push to Shiprocket after Payment Success
            $shiprocket = new \App\Services\ShiprocketService();
            $srResult = $shiprocket->createOrder($order);
            if ($srResult['status']) {
                Log::info('Razorpay Order Pushed to Shiprocket: ' . $order->order_number);
            } else {
                Log::error('Shiprocket Razorpay Push Failed: ' . $srResult['message']);
            }

            return redirect()->route('order-confirmation', $order)->with('success', 'Payment successful! Your order is confirmed. 🎉');
        }

        if (!$order) {
            Log::error('Razorpay Verify — Order not found for payment_id: ' . $request->razorpay_order_id);
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
        if (Auth::check()) {
            $dbItems = \App\Models\CartItem::where('user_id', Auth::id())->get();
            $cart = [];
            foreach ($dbItems as $item) {
                // Determine a unique key for the cart item
                $cartKey = $item->product_id;
                $attributes = $item->attributes ?? [];
                if (!empty($attributes)) {
                    ksort($attributes);
                    foreach ($attributes as $id => $val) {
                        $cartKey .= '_' . $id . '_' . $val;
                    }
                }

                $product = $item->product;
                if (!$product) continue;

                $price = $item->variant ? ($item->variant->sale_price ?: $item->variant->price) : ($product->sale_price ?: $product->regular_price ?: $product->price);
                $imagePath = ($item->variant && $item->variant->image) ? $item->variant->image : $product->image_path;
                
                if (!$imagePath && !empty($product->images)) {
                    $imgs = is_string($product->images) ? json_decode($product->images, true) : $product->images;
                    if (is_array($imgs) && count($imgs) > 0) {
                        $imagePath = $imgs[0];
                    }
                }
                
                // Get size and color names for display dynamically
                $size = '';
                $color = '';
                $length = '';
                $age = '';
                $display_attributes = [];

                foreach($attributes as $aid => $avid) {
                    $val = \App\Models\AttributeValue::with('attribute')->find($avid);
                    if($val && $val->attribute) {
                        $attrName = $val->attribute->name;
                        $attrNameLower = strtolower($attrName);

                        $display_attributes[] = [
                            'name' => $attrName,
                            'value' => $val->name
                        ];

                        if(str_contains($attrNameLower, 'size')) $size = $val->name;
                        elseif(str_contains($attrNameLower, 'color') || str_contains($attrNameLower, 'colour')) $color = $val->name;
                        elseif(str_contains($attrNameLower, 'length')) $length = $val->name;
                        elseif(str_contains($attrNameLower, 'age')) $age = $val->name;
                    }
                }

                $cart[$cartKey] = [
                    'product_id' => $item->product_id,
                    'variant_id' => $item->product_variant_id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => (float) $price,
                    'image_path' => $imagePath,
                    'image_url' => $this->productImageUrl($imagePath),
                    'quantity' => (int) $item->quantity,
                    'attributes' => $attributes,
                    'display_attributes' => $display_attributes,
                    'size' => $size,
                    'color' => $color,
                    'length' => $length,
                    'age' => $age,
                ];
            }
            return $cart;
        }
        return session()->get('cart', []);
    }

    private function putCart(array $cart): void
    {
        if (Auth::check()) {
            $userId = Auth::id();
            // This is a simplified "replace all" approach for sync.
            // Ideally should update specifically, but for now we'll match by user.
            \App\Models\CartItem::where('user_id', $userId)->delete();
            foreach ($cart as $item) {
                \App\Models\CartItem::create([
                    'user_id' => $userId,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'quantity' => $item['quantity'],
                    'attributes' => $item['attributes'],
                ]);
            }
        } else {
            session()->put('cart', $cart);
        }
    }

    public function syncCartOnLogin()
    {
        if (Auth::check()) {
            $sessionCart = session()->get('cart', []);
            if (!empty($sessionCart)) {
                $dbCart = $this->getCart(); // gets current DB items
                // Merge session items into DB
                foreach ($sessionCart as $key => $sItem) {
                    if (isset($dbCart[$key])) {
                        $dbCart[$key]['quantity'] += $sItem['quantity'];
                    } else {
                        $dbCart[$key] = $sItem;
                    }
                }
                $this->putCart($dbCart);
                session()->forget('cart');
            }
        }
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
        
        // Shipping Calculation
        $shipping = $this->calculateShipping($items);
        
        $couponResult = $this->resolveCoupon($items, $subTotal);
        $discount = $couponResult['discount'];
        $coupon = $couponResult['coupon'];
        $taxableAmount = max(0, $subTotal - $discount);
        
        // DYNAMIC TAX CALCULATION [PERFECT FIX]
        $tax = 0;
        $firstRate = 0;
        foreach ($items as $item) {
            $product = Product::with('taxClass.rates')->find($item['product_id']);
            if ($product) {
                // Get the float rate (0.1, 0.05 etc)
                $rate = $this->getTaxRate($product);
                if ($firstRate === 0) {
                    $firstRate = $rate * 100; // Track first applicable rate in percentage (10, 5 etc)
                }
                $tax += ($item['price'] * $item['quantity']) * $rate;
            }
        }

        $tax = round($tax, 2);
        $taxPercentage = $firstRate ?: 5; // Default to 5 if none found
        $grandTotal = round($taxableAmount + $tax + $shipping, 2);

        return compact('subTotal', 'discount', 'tax', 'taxPercentage', 'shipping', 'grandTotal', 'itemCount', 'coupon');
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
            Log::info('COUPON REJECTED: Not found or inactive.', ['coupon_id' => $couponId]);
            return $this->invalidateCoupon();
        }

        $now = now();
        if ($coupon->valid_from && $now->lt($coupon->valid_from)) {
            Log::info('COUPON REJECTED: Before valid date.', ['code' => $coupon->code, 'now' => $now, 'valid_from' => $coupon->valid_from]);
            return $this->invalidateCoupon();
        }
        if ($coupon->expires_at && $now->gt($coupon->expires_at)) {
            Log::info('COUPON REJECTED: Expired.', ['code' => $coupon->code, 'now' => $now, 'expires_at' => $coupon->expires_at]);
            return $this->invalidateCoupon();
        }
        if ($coupon->usage_limit && $coupon->times_used >= $coupon->usage_limit) {
            Log::info('COUPON REJECTED: Usage limit reached.', ['code' => $coupon->code, 'used' => $coupon->times_used, 'limit' => $coupon->usage_limit]);
            return $this->invalidateCoupon();
        }
        if ($coupon->min_order_amount && $subTotal < $coupon->min_order_amount) {
            Log::info('COUPON REJECTED: Min order amount not met.', ['code' => $coupon->code, 'subtotal' => $subTotal, 'min' => $coupon->min_order_amount]);
            return $this->invalidateCoupon();
        }

        $eligibleSubtotal = $this->eligibleSubtotal($items, $coupon);
        if ($eligibleSubtotal <= 0) {
            Log::info('COUPON REJECTED: No eligible items in cart.', ['code' => $coupon->code, 'applicable_products' => $coupon->applicable_products]);
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

        Log::info('COUPON ELIGIBILITY CHECK:', [
            'code' => $coupon->code,
            'applicable_products' => $productIds,
            'applicable_categories' => $categoryIds,
            'cart_items_count' => count($items)
        ]);

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
            
            $isProductEligible = in_array($productId, $productIds);
            $isCategoryEligible = $categoryId && in_array($categoryId, $categoryIds);

            Log::info('Checking Cart Item Eligibility:', [
                'p_id' => $productId,
                'c_id' => $categoryId,
                'p_match' => $isProductEligible,
                'c_match' => $isCategoryEligible
            ]);

            if ($isProductEligible || $isCategoryEligible) {
                $sum += $item['price'] * $item['quantity'];
            }
        }

        return round($sum, 2);
    }

    private function calculateShipping(array $items): float
    {
        $totalShipping = 0;
        
        // Get shipping info from session if available (set during checkout)
        $destination = session()->get('shipping_destination', [
            'country' => 'India', // Default
            'state' => null,
            'zip' => null
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id'], ['*']);
            if (!$product) continue;

            $shippingClassId = null;
            
            // Check variant first if it exists
            if (!empty($item['variant_id'])) {
                $variant = \App\Models\ProductVariant::find($item['variant_id'], ['*']);
                if ($variant && $variant->shipping_class_id) {
                    $shippingClassId = $variant->shipping_class_id;
                }
            }

            // Fallback to parent product shipping class
            if (!$shippingClassId) {
                $shippingClassId = $product->shipping_class_id;
            }

            if (!$shippingClassId) continue;

            $shippingClass = \App\Models\ShippingClass::find($shippingClassId);
            if (!$shippingClass || !$shippingClass->status) continue;

            // Find best matching rate
            $rate = \App\Models\ShippingRate::where('shipping_class_id', $shippingClass->id)
                ->where('status', 1)
                ->where(function($query) use ($destination) {
                    $query->where('country', $destination['country'])
                          ->orWhere('country', 'All')
                          ->orWhereNull('country');
                })
                ->orderByRaw("CASE WHEN country = ? THEN 0 ELSE 1 END", [$destination['country']])
                ->first();

            if ($rate) {
                // Modified: Add shipping cost ONCE per unique product entry in cart, 
                // regardless of its quantity. This ensures multiple products add up 
                // but single product increment doesn't add extra shipping.
                $totalShipping += (float)$rate->cost;
            }
        }

        return round($totalShipping, 2);
    }

    private function invalidateCoupon(): array
    {
        session()->forget('coupon_id');
        return ['coupon' => null, 'discount' => 0];
    }
}
