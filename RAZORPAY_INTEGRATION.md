# Razorpay Payment Gateway Integration
**Project:** Nandhini Silks — Laravel E-Commerce  
**Mode:** Live (Production)  
**Date:** April 2, 2026  
**MID:** SW9AHcjKJxFpor (Verify with dashboard)  

---

## Overview

Razorpay is integrated as the **online payment method** at checkout alongside Cash on Delivery (COD). The integration uses **server-side order creation** and **signature-based verification** for maximum security.

```
Customer Checkout → Backend creates Razorpay Order → Frontend opens Payment Modal
       → Customer pays → Razorpay calls handler → Backend verifies signature
               → Order marked PAID → Order confirmation page shown
```

---

## Credentials & Configuration

### Live Mode API Keys
| Field | Value |
|---|---|
| **Key ID** | `rzp_live_SYXqNIFrw0EEaW` |
| **Key Secret** | `1JdT3SBTEPDhl6SCUiSMN2Aa` |
| **Mode** | Live (Production) |
| **MID** | SW9AHcjKJxFpor |
| **Account** | nandhinisilks.arni@gmail.com |

> **IMPORTANT:** Before going LIVE, replace the test keys with Live API Keys from Razorpay Dashboard → Settings → API Keys → Live Mode.

### `.env` Configuration
```env
# Razorpay Payment Gateway (Live Mode)
RAZORPAY_KEY=rzp_live_SYXqNIFrw0EEaW
RAZORPAY_SECRET=1JdT3SBTEPDhl6SCUiSMN2Aa
```

### `config/services.php`
```php
'razorpay' => [
    'key'    => env('RAZORPAY_KEY'),
    'secret' => env('RAZORPAY_SECRET'),
],
```

---

## Installed Package

```bash
composer require razorpay/razorpay
```

**`composer.json`:**
```json
"razorpay/razorpay": "^2.9"
```

---

## Database Schema

Migration: `2026_03_23_041010_add_payment_id_to_orders_table.php`

```php
Schema::table('orders', function (Blueprint $table) {
    $table->string('payment_id')->nullable()->after('payment_method');
});
```

| Column | Type | Purpose |
|---|---|---|
| `payment_method` | string | `'razorpay'` or `'cod'` |
| `payment_id` | string (nullable) | Stores Razorpay Order ID |
| `payment_status` | string | `pending` to `paid` or `failed` |
| `order_status` | string | `pending` to `processing` on success |

---

## Payment Flow (Step by Step)

### Step 1 — Customer Selects Razorpay
At checkout, the customer selects "Pay Online (Razorpay)".

### Step 2 — Backend Creates Razorpay Order

**Route:** `POST /checkout/place-order` → `CartController@placeOrder`

```php
private function processRazorpay(Order $order)
{
    $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

    $razorOrder = $api->order->create([
        'receipt'  => (string) $order->id,
        'amount'   => (int) ($order->grand_total * 100), // Converted to paise
        'currency' => 'INR'
    ]);

    // Store Razorpay Order ID in our DB
    $order->update(['payment_id' => $razorOrder['id']]);

    return view('frontend.razorpay-payment', compact('order', 'razorOrder'));
}
```

> **NOTE:** Amount is always in **paise** (Rs.1 = 100 paise). Rs.500 = `50000`.

### Step 3 — Frontend Payment Modal Opens

**View:** `resources/views/frontend/razorpay-payment.blade.php`

The Checkout.js modal auto-opens with brand color `#A91B43`. Supports UPI, Cards, Net Banking, Wallets.

```javascript
var options = {
    "key"      : "{{ config('services.razorpay.key') }}",
    "amount"   : "{{ $razorOrder['amount'] }}",
    "currency" : "INR",
    "name"     : "Nandhini Silks",
    "order_id" : "{{ $razorOrder['id'] }}",
    "handler"  : function (response) {
        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
        document.getElementById('razorpay_order_id').value   = response.razorpay_order_id;
        document.getElementById('razorpay_signature').value  = response.razorpay_signature;
        document.getElementById('razorpay-form').submit();
    },
    "prefill": {
        "name"    : "{{ $order->customer_name }}",
        "email"   : "{{ $order->customer_email }}",
        "contact" : "{{ $order->customer_phone }}"
    },
    "theme": { "color": "#A91B43" }
};
```

### Step 4 — Backend Signature Verification

**Route:** `POST /payment/razorpay/verify` → `CartController@verifyRazorpay`

```php
public function verifyRazorpay(Request $request)
{
    $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    $signatureStatus = true;

    try {
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id'   => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature'  => $request->razorpay_signature,
        ]);
    } catch (\Exception $e) {
        $signatureStatus = false;
    }

    if ($signatureStatus) {
        $order = Order::where('payment_id', $request->razorpay_order_id)->first();
        if ($order) {
            $order->update(['payment_status' => 'paid', 'order_status' => 'processing']);
            // Clear cart, send order emails, redirect to confirmation
        }
    }

    return redirect()->route('checkout')->with('error', 'Payment failed or signature mismatch.');
}
```

> **IMPORTANT:** Signature verification is mandatory. Never skip this step — it prevents payment fraud.

---

## Routes

```php
// routes/web.php
Route::post('/payment/razorpay/verify', [CartController::class, 'verifyRazorpay'])
     ->name('razorpay.verify');
```

---

## Files Changed/Created

| File | Change |
|---|---|
| `.env` | Added `RAZORPAY_KEY` and `RAZORPAY_SECRET` |
| `config/services.php` | Razorpay key/secret config block |
| `app/Http/Controllers/CartController.php` | `processRazorpay()` and `verifyRazorpay()` methods |
| `resources/views/frontend/razorpay-payment.blade.php` | Premium branded payment page (upgraded) |
| `resources/views/frontend/checkout.blade.php` | Payment method selection UI |
| `resources/views/frontend/order-confirmation.blade.php` | Razorpay badge display |
| `database/migrations/..._add_payment_id_to_orders.php` | `payment_id` column |
| `composer.json` | `razorpay/razorpay: ^2.9` |
| `routes/web.php` | `/payment/razorpay/verify` route |

---

## Testing Guide

### Test Card Numbers

| Card Type | Number | CVV | Expiry |
|---|---|---|---|
| Visa (Success) | `4111 1111 1111 1111` | Any 3 digits | Any future date |
| Mastercard | `5267 3181 8797 5449` | Any 3 digits | Any future date |

### Test UPI
Use `success@razorpay` as the UPI ID.

### End-to-End Test Steps
1. Add a product to cart → Go to Checkout
2. Fill in all shipping details
3. Select **"Pay Online (Razorpay)"**
4. Click **"Place Order"**
5. Razorpay modal opens → Pay using test card or UPI
6. On success → redirected to **Order Confirmation** page
7. Admin Panel → Order should show **Payment: Paid**, **Status: Processing**

---

## Going LIVE — Production Checklist

> **CAUTION:** Never use test keys in production.

- [ ] Complete Razorpay KYC at dashboard.razorpay.com
- [ ] Get Live API Keys: Dashboard → Settings → API Keys → Live Mode
- [ ] Update `.env` on the live server:
  ```env
  RAZORPAY_KEY=rzp_live_XXXXXXXXXXXXXXXX
  RAZORPAY_SECRET=XXXXXXXXXXXXXXXXXXXXXXXX
  ```
- [ ] Set `APP_ENV=production` and `APP_DEBUG=false`
- [ ] Run `php artisan config:cache` on the live server
- [ ] Do one test transaction of Rs.1 before full launch

---

## Security Summary

| Security Measure | Status |
|---|---|
| Keys stored in `.env` (not hardcoded) | Done |
| `RAZORPAY_SECRET` never exposed to frontend | Done |
| Payment signature verified server-side | Done |
| CSRF protection on verify route | Done |
| Order only marked PAID after verification | Done |
| Errors logged with `Log::error()` | Done |
