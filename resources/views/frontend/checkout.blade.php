@extends('frontend.layouts.app')

@section('title', 'Checkout | Nandhini Silks')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --pink-dark: #940437;
    }
    .checkout-page-container {
        font-family: 'Outfit', sans-serif;
        background: #fffcf5;
        min-height: 100vh;
        padding: 60px 0;
    }
    .card-v4 {
        background: #fff;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        margin-bottom: 30px;
    }
    .section-title-v4 {
        font-size: 24px;
        font-weight: 700;
        color: var(--pink-dark);
        margin-bottom: 30px;
    }
    .address-grid-v4 {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .address-card-v4 {
        border: 1px solid #eee;
        border-radius: 16px;
        padding: 20px;
        position: relative;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        background: #fafafa;
    }
    .address-card-v4:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.05);
    }
    .address-card-v4.active {
        border: 2px solid var(--pink-dark);
        background: #fff;
    }
    .address-tag-v4 {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        color: #1e88e5;
        background: #e3f2fd;
        padding: 3px 10px;
        border-radius: 6px;
        display: inline-block;
        margin-bottom: 12px;
    }
    .edit-link-v4 {
        position: absolute;
        top: 20px;
        right: 20px;
        color: var(--pink-dark);
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }
    .address-name-v4 {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }
    .address-text-v4 {
        font-size: 13px;
        color: #666;
        line-height: 1.6;
    }
    .address-phone-v4 {
        font-size: 13px;
        color: #666;
        margin-top: 10px;
    }
    .separator-v4 {
        border-top: 1px dashed #ddd;
        margin: 30px 0;
    }
    .btn-add-address-v4 {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 12px 25px;
        border: 1px solid #ddd;
        border-radius: 50px;
        background: #fff;
        color: #333;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-add-address-v4:hover {
        background: #fafafa;
        border-color: #333;
    }
    .btn-review-v4 {
        background: var(--pink-dark);
        color: #fff;
        padding: 15px 45px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 15px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-review-v4:hover {
        background: #7a032d;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(148, 4, 55, 0.2);
    }
    .summary-card-v4 {
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.03);
        position: sticky;
        top: 20px;
    }
    .summary-title-v4 {
        font-size: 18px;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
    }
    .summary-row-v4 {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        color: #666;
    }
    .grand-total-v4 {
        border-top: 1px solid #eee;
        margin-top: 20px;
        padding-top: 20px;
        display: flex;
        justify-content: space-between;
        font-size: 20px;
        font-weight: 700;
        color: var(--pink-dark);
    }
    .switch-v4 {
      position: relative;
      display: inline-block;
      width: 44px;
      height: 22px;
    }
    .switch-v4 input { 
      opacity: 0;
      width: 0;
      height: 0;
    }

    .checkout-section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        gap: 12px;
    }

    .checkout-section-header .section-title-v4 {
        margin: 0;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .checkout-saved-address-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
        flex-shrink: 0;
        align-self: center;
    }
    .slider-v4 {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: .4s;
      border-radius: 22px;
    }
    .slider-v4:before {
      position: absolute;
      content: "";
      height: 14px;
      width: 14px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: .4s;
      border-radius: 50%;
    }
    input:checked + .slider-v4 {
      background-color: var(--pink-dark);
    }
    input:checked + .slider-v4:before {
      transform: translateX(22px);
    }
    .form-input-v4 {
        width: 100%;
        padding: 12px 15px;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        font-size: 14px;
        outline: none;
        font-family: 'Outfit', sans-serif;
    }
    .form-input-v4:focus {
        border-color: var(--pink-dark);
    }

    @media (max-width: 768px) {
        .checkout-section-header {
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            text-align: center;
        }

        .checkout-section-header .section-title-v4 {
            font-size: 18px !important;
            line-height: 1.2;
        }

        .checkout-saved-address-btn {
            padding: 6px 12px !important;
            font-size: 10px !important;
            line-height: 1.2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 34px;
            align-self: center;
        }

        .checkout-saved-address-btn i {
            margin-right: 4px !important;
        }

        .summary-title-v4 {
            justify-content: center;
            text-align: center;
        }

        .card-v4,
        .summary-card-v4 {
            margin-left: auto;
            margin-right: auto;
        }
    }
</style>
@endpush

@section('content')
<main class="checkout-page-container">
    <div class="page-shell">
        <form id="singleCheckoutForm" class="validate-form" action="{{ route('checkout.place') }}" method="POST">
            @csrf
            <input type="hidden" name="payment_method" id="payment_method_input" value="razorpay">
            <input type="hidden" name="customer_email" value="{{ Auth::user()?->email }}">

            <div class="checkout-grid" style="display: grid; grid-template-columns: 1fr 380px; gap: 40px; align-items: start;">
                <div class="checkout-main">
                    @if(session('error'))
                        <div style="background: #ffebee; color: #c62828; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; font-weight: 500; border: 1px solid #ffcdd2;">
                            {{ session('error') }}
                        </div>
                    @endif

                    {{-- SHIPPING ADDRESS SECTION --}}
                    <div class="card-v4" style="margin-bottom: 25px; position: relative;">
                        <div class="checkout-section-header">
                            <h2 class="section-title-v4" style="font-size: 20px; margin: 0;">Shipping Address</h2>
                            
                            @if($addresses->count() > 0)
                            <button type="button" onclick="openAddressModal()" class="checkout-saved-address-btn" style="background: var(--pink-light); color: var(--pink-dark); border: 1px solid var(--pink); border-radius: 8px; padding: 6px 15px; font-size: 11px; font-weight: 700; cursor: pointer; transition: all 0.2s ease;">
                                <i class="fa-solid fa-address-book" style="margin-right: 5px;"></i> Or Use Saved Address
                            </button>
                            @endif
                        </div>
                        
                        <div id="checkoutAddressForm">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                <div class="form-group">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">FULL NAME</label>
                                    <input type="text" name="customer_name" id="field_name" placeholder="Full Name" class="form-input-v4" value="{{ Auth::user()?->name }}" required>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PHONE NUMBER</label>
                                    <input type="tel" name="customer_phone" id="field_phone" placeholder="Phone Number" class="form-input-v4" value="{{ Auth::user()?->phone }}" required>
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">DELIVERY ADDRESS</label>
                                    <input type="text" name="delivery_address" id="field_address" placeholder="Flat No, Street, Area" class="form-input-v4" required>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">CITY</label>
                                    <input type="text" id="field_city" placeholder="City" class="form-input-v4" required>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">STATE</label>
                                    <input type="text" id="field_state" placeholder="State" class="form-input-v4" required>
                                </div>
                                <div class="form-group">
                                    <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PINCODE</label>
                                    <input type="text" id="field_zip" placeholder="Pincode" class="form-input-v4" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SAVED ADDRESS MODAL --}}
                    <div id="addressModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
                        <div style="background: #fff; width: 90%; max-width: 600px; border-radius: 20px; padding: 30px; position: relative; max-height: 85vh; overflow-y: auto;">
                            <button type="button" onclick="closeAddressModal()" style="position: absolute; top: 20px; right: 20px; border: none; background: none; font-size: 24px; color: #999; cursor: pointer;">&times;</button>
                            <h3 style="font-size: 18px; font-weight: 700; color: #333; margin-bottom: 25px;">Choose a Saved Address</h3>
                            
                            <div class="address-grid-v4" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                @foreach($addresses as $addr)
                                <div class="address-card-v4" 
                                     data-name="{{ Auth::user()->name }}"
                                     data-phone="{{ Auth::user()->phone }}"
                                     data-addr1="{{ $addr->address1 }}"
                                     data-city="{{ $addr->city }}"
                                     data-state="{{ $addr->state }}"
                                     data-zip="{{ $addr->zip }}"
                                     onclick="autofillAddress(this); closeAddressModal();"
                                     style="cursor: pointer; border: 1px solid #eee; padding: 15px; border-radius: 12px; transition: all 0.2s ease;">
                                    <span class="address-tag-v4" style="font-size: 8px;">{{ $addr->label }}</span>
                                    <div class="address-name-v4" style="font-size: 14px; font-weight: 700; margin-top: 5px;">{{ Auth::user()->name }}</div>
                                    <div class="address-text-v4" style="font-size: 12px; color: #666; margin-top: 3px;">{{ $addr->address1 }}, {{ $addr->city }}</div>
                                    <div class="address-phone-v4" style="font-size: 12px; color: #888; margin-top: 5px;">{{ Auth::user()->phone }}</div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR --}}
                <aside class="checkout-sidebar">
                    <div class="summary-card-v4" style="position: sticky; top: 100px;">
                        <h3 class="summary-title-v4" style="font-size: 16px; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-receipt" style="color: var(--pink);"></i>
                            Order Summary
                        </h3>

                        {{-- Items List in Sidebar --}}
                        <div style="margin-bottom: 20px; max-height: 150px; overflow-y: auto; padding-right: 5px;">
                            @foreach($items as $item)
                            <div style="display: flex; gap: 10px; margin-bottom: 10px; align-items: center;">
                                <img src="{{ $item['image_url'] }}" style="width: 30px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <div style="flex: 1; min-width: 0;">
                                    <div style="font-size: 11px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $item['name'] }}</div>
                                    <div style="font-size: 9px; color: #888;">Qty: {{ $item['quantity'] }}</div>
                                </div>
                                <div style="font-size: 11px; font-weight: 600;">₹{{ number_format($item['price'] * $item['quantity'], 0) }}</div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="summary-row-v4">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subTotal, 0) }}</span>
                        </div>
                        <div class="summary-row-v4">
                            <span>Delivery Charges</span>
                            <span style="color: #2ecc71; font-weight: 700; font-size: 12px;">FREE</span>
                        </div>
                        <div class="summary-row-v4">
                            <span>GST (5%)</span>
                            <span>₹{{ number_format($tax, 0) }}</span>
                        </div>
                        @if($discount > 0)
                        <div class="summary-row-v4" style="color: #2ecc71; font-weight: 600;">
                            <span>Discount</span>
                            <span>-₹{{ number_format($discount, 0) }}</span>
                        </div>
                        @endif

                        <div class="grand-total-v4" style="font-size: 20px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                            <span>Total</span>
                            <span>₹{{ number_format($grandTotal, 0) }}</span>
                        </div>

                        {{-- PAYMENT METHOD SELECTION --}}
                        <div style="margin-top: 25px; display: flex; flex-direction: column; gap: 10px;" id="paymentOptions">
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #A91B43; border-radius: 12px; transition: all 0.2s ease; background: #fffcfd;" onclick="selectPayment('razorpay', this)">
                                <input type="radio" name="pay_choice_radio" checked style="accent-color: var(--pink);">
                                <div style="flex: 1;">
                                    <div style="font-size: 12px; font-weight: 700; color: #333;">Secure Payment</div>
                                    <div style="font-size: 10px; color: #888;">UPI, Cards, NetBanking</div>
                                </div>
                                <i class="fa-solid fa-shield-halved" style="color: #2e7d32; font-size: 14px;"></i>
                            </label>

                            <label style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #f0f0f0; border-radius: 12px; transition: all 0.2s ease; background: #fff;" onclick="selectPayment('cod', this)">
                                <input type="radio" name="pay_choice_radio" style="accent-color: var(--pink);">
                                <div style="flex: 1;">
                                    <div style="font-size: 12px; font-weight: 700; color: #333;">Cash on Delivery</div>
                                    <div style="font-size: 10px; color: #888;">Pay when you receive</div>
                                </div>
                                <i class="fa-solid fa-truck-fast" style="color: #ef6c00; font-size: 14px;"></i>
                            </label>
                        </div>

                        <button type="submit" class="btn-review-v4" style="width: 100%; margin-top: 20px; height: 50px; font-size: 16px; letter-spacing: 0.5px;">
                            Pay & Place Order
                        </button>

                        <div style="text-align: center; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0.6;">
                            <img src="https://razorpay.com/favicon.png" width="14">
                            <span style="font-size: 10px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 1px;">Razorpay Secured</span>
                        </div>
                    </div>
                </aside>
            </div>
        </form>
    </div>
</main>
@endsection

@push('scripts')
<script>
    function selectPayment(method, element) {
        document.getElementById('payment_method_input').value = method;
        
        // Visual Styling
        document.querySelectorAll('#paymentOptions label').forEach(l => {
            l.style.border = '1px solid #f0f0f0';
            l.style.background = '#fff';
            l.querySelector('input').checked = false;
        });
        
        element.style.border = '1px solid #A91B43';
        element.style.background = '#fffcfd';
        element.querySelector('input').checked = true;
    }

    function openAddressModal() {
        document.getElementById('addressModal').style.display = 'flex';
    }

    function closeAddressModal() {
        document.getElementById('addressModal').style.display = 'none';
    }

    // Close modal if clicking outside
    window.onclick = function(event) {
        let modal = document.getElementById('addressModal');
        if (event.target == modal) {
            closeAddressModal();
        }
    }

    function autofillAddress(element) {
        // Remove active class from all cards
        document.querySelectorAll('.address-card-v4').forEach(c => {
            c.classList.remove('active');
            c.style.borderColor = '#eee';
        });
        
        // Add active to current
        element.classList.add('active');
        element.style.borderColor = 'var(--pink)';

        // Fill Fields
        document.getElementById('field_name').value = element.getAttribute('data-name');
        document.getElementById('field_phone').value = element.getAttribute('data-phone');
        document.getElementById('field_address').value = element.getAttribute('data-addr1');
        document.getElementById('field_city').value = element.getAttribute('data-city');
        document.getElementById('field_state').value = element.getAttribute('data-state');
        document.getElementById('field_zip').value = element.getAttribute('data-zip');

        // Scroll to form for visibility
        window.scrollTo({ top: document.getElementById('checkoutAddressForm').offsetTop - 100, behavior: 'smooth' });
    }

    $('#singleCheckoutForm').on('submit', function(e) {
        if ($(this).valid()) {
            // Final sync of combined address field if needed by backend
            const addr = document.getElementById('field_address').value;
            const city = document.getElementById('field_city').value;
            const state = document.getElementById('field_state').value;
            const zip = document.getElementById('field_zip').value;
            
            // Only concatenate if not already concatenated
            if (!addr.includes(city) || !addr.includes(zip)) {
                document.getElementById('field_address').value = `${addr}, ${city}, ${state} - ${zip}`;
            }
            return true;
        }
        return false;
    });
</script>
@endpush
