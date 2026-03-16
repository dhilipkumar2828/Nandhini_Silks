@extends('frontend.layouts.app')

@section('title', 'Checkout | Nandhini Silks')

@push('styles')
<style>
    .checkout-step-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      max-width: 800px;
      margin: 0 auto 40px auto;
      background: #fff;
      padding: 20px 40px;
      border-radius: 50px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.03);
    }
    .step-item {
      display: flex;
      align-items: center;
      gap: 10px;
      font-weight: 600;
      color: #999;
      flex: 1;
      justify-content: center;
      position: relative;
    }
    .step-item:not(:last-child)::after {
        content: "";
        position: absolute;
        right: -20px;
        top: 50%;
        width: 15px;
        height: 2px;
        background: #eee;
    }
    .payment-option-v3 {
      border: 1px solid #ddd;
      border-radius: 12px;
      padding: 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      background: #fff;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .payment-option-v3.active {
      border-color: var(--pink);
      background: #fff9fa;
    }
    .step-item.active {
        color: var(--pink);
    }
    .step-item.active .step-num {
        background: var(--pink);
        color: #fff;
    }
    .step-num {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #eee;
        color: #999;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
    }
    .step-item.completed {
        color: #2e7d32;
    }
    .completed .step-num {
        background: #e8f5e9;
        color: #2e7d32;
    }
    .address-card {
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 15px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s ease;
        background: #fafafa;
    }
    .address-card.active {
        border-color: var(--pink);
        background: #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    .address-label-badge {
        font-size: 10px;
        text-transform: uppercase;
        background: #eee;
        padding: 2px 8px;
        border-radius: 4px;
        margin-bottom: 8px;
        display: inline-block;
    }
    .review-section-box {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .review-title {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 12px;
        color: #333;
        display: flex;
        justify-content: space-between;
    }
    .review-content {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
    }
</style>
@endpush

@section('content')
<main class="cart-page" style="background: #fffcf0; padding-top: 40px;">
    <div class="page-shell">
        
        <div class="checkout-grid" style="display: grid; grid-template-columns: 1fr 350px; gap: 40px;">
            <div class="checkout-main">
                <!-- Step 1: Delivery Address -->
                <div id="step-1" class="checkout-step-content">
                    <div class="checkout-section">
                        <h2 class="checkout-title" style="margin-bottom: 20px;">Shipping Address</h2>
                        <div class="saved-addresses" id="addressList" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 30px;">
                            <div class="address-card active" onclick="selectAddress(this)">
                                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                    <span class="address-label-badge" style="background: #e3f2fd; color: #1565c0;">Home</span>
                                </div>
                                <div class="addr-name" style="font-weight: 600; margin-bottom: 5px;">Raswanth Sabarish</div>
                                <div class="address-text" style="font-size: 13px; color: #666;">
                                    <span class="addr-line1">416/9 Aranmanai Street, S.V. Nagaram</span><br>
                                    <span class="addr-city-state">Arni, Tamil Nadu - 632317</span><br>
                                    Phone: <span class="addr-phone">+91 96295 52822</span>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button class="btn-step btn-next" onclick="goToStep(2)">Review Order</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Order Review -->
                <div id="step-2" class="checkout-step-content" style="display: none;">
                    <div class="checkout-section">
                        <h2 class="checkout-title">Review Your Order</h2>
                        
                        <div class="review-section-box">
                            <div class="review-title">Delivery Address <a href="#" onclick="goToStep(1)" style="color: var(--pink); font-size: 12px; text-decoration: none;">Change</a></div>
                            <div class="review-content">
                                <div id="reviewShippingAddr">
                                    <strong>Raswanth Sabarish (Home)</strong><br>
                                    416/9 Aranmanai Street, S.V. Nagaram<br>
                                    Arni, Tamil Nadu - 632317<br>
                                    Phone: +91 96295 52822
                                </div>
                            </div>
                        </div>

                        <div class="review-section-box">
                            <div class="review-title">Items & Delivery</div>
                            <div class="review-items-list">
                                <div style="display: flex; gap: 15px; margin-bottom: 0;">
                                    <img src="{{ asset('images/product_detail.png') }}" width="60" height="75" style="object-fit: cover; border-radius: 4px;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600;">Royal Gold Silk Saree</div>
                                        <div style="font-size: 12px; color: #666;">Qty: 1 | Color: Gold</div>
                                        <div style="font-weight: 700; color: var(--pink); margin-top: 5px;">₹7,490</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                            <button class="btn-step btn-prev" onclick="goToStep(1)">Back</button>
                            <button class="btn-step btn-next" onclick="goToStep(3)">Continue to Payment</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Payment Method -->
                <div id="step-3" class="checkout-step-content" style="display: none;">
                    <div class="checkout-section">
                        <h2 class="checkout-title">Secure Payment</h2>
                        
                        <div class="payment-option-v3 active" style="margin-bottom: 25px;">
                            <img src="https://razorpay.com/favicon.png" width="24" alt="Razorpay">
                            <div style="flex: 1;">
                                <div style="font-weight: 700;">Razorpay Secure</div>
                                <div style="font-size: 13px; color: #666;">UPI, Cards, NetBanking or Wallets</div>
                            </div>
                            <div style="width: 20px; height: 20px; border-radius: 50%; border: 6px solid var(--pink);"></div>
                        </div>

                        <div style="display: flex; justify-content: space-between; margin-top: 30px;">
                            <button class="btn-step btn-prev" onclick="goToStep(2)">Back</button>
                            <button class="btn-step btn-next" style="padding: 12px 60px; font-size: 18px;" onclick="alert('Place Order logic reverted.')">Pay & Place Order</button>
                        </div>
                    </div>
                </div>
            </div>

            <aside class="cart-summary">
                <h2 class="summary-title" style="margin-bottom: 15px;">Order Summary</h2>
                <div class="summary-row"><span>Subtotal (2 items)</span><span>₹15,990</span></div>
                <div class="summary-row"><span>Delivery Charges</span><span style="color: #2e7d32;">FREE</span></div>
                <div class="summary-row"><span>GST (5%)</span><span>₹800</span></div>
                <div class="summary-total" style="border-top: 2px solid #eee; padding-top: 15px; font-size: 22px;">
                    <span>Grand Total</span>
                    <span>₹16,790</span>
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function selectAddress(el) {
        document.querySelectorAll('#addressList .address-card').forEach(card => card.classList.remove('active'));
        el.classList.add('active');
        
        const name = el.querySelector('.addr-name').innerText;
        const line1 = el.querySelector('.addr-line1').innerText;
        const cityState = el.querySelector('.addr-city-state').innerText;
        const phone = el.querySelector('.addr-phone').innerText;
        
        document.getElementById('reviewShippingAddr').innerHTML = `<strong>${name}</strong><br>${line1}<br>${cityState}<br>Phone: ${phone}`;
    }

    function goToStep(num) {
        document.querySelectorAll('.checkout-step-content').forEach(step => step.style.display = 'none');
        document.getElementById('step-' + num).style.display = 'block';
        window.scrollTo({ top: 100, behavior: 'smooth' });
    }
</script>
@endpush
