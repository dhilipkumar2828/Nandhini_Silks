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
</style>
@endpush

@section('content')
<main class="checkout-page-container">
    <div class="page-shell">
        <div class="checkout-grid" style="display: grid; grid-template-columns: 1fr 350px; gap: 40px;">
            <div class="checkout-main">
                @if(session('success'))
                    <div style="background: #e8f5e9; color: #2e7d32; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; font-weight: 500; border: 1px solid #c8e6c9;">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div style="background: #ffebee; color: #c62828; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; font-weight: 500; border: 1px solid #ffcdd2;">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div style="background: #ffebee; color: #c62828; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; font-weight: 500; border: 1px solid #ffcdd2;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <!-- STEP 1: ADDRESS -->
                <div id="step-1" class="checkout-step-content">
                    <div class="card-v4">
                        <h2 class="section-title-v4">Shipping Address</h2>
                        
                        <!-- Saved Addresses Grid -->
                        <div id="savedAddressesList" class="address-grid-v4">
                            @forelse($addresses as $addr)
                            <div class="address-card-v4 {{ $loop->first ? 'active' : '' }}" 
                                 onclick="selectSavedAddress(this, '{{ addslashes($addr->address1 . ($addr->address2 ? ', ' . $addr->address2 : '') . ', ' . $addr->city . ', ' . $addr->state . ' - ' . $addr->zip) }}')">
                                <span class="address-tag-v4">{{ $addr->label }}</span>
                                <a href="{{ route('my-addresses') }}" class="edit-link-v4">Edit</a>
                                <div class="address-name-v4">{{ Auth::user()->name }}</div>
                                <div class="address-text-v4">{{ $addr->address1 }}</div>
                                @if($addr->address2) <div class="address-text-v4">{{ $addr->address2 }}</div> @endif
                                <div class="address-text-v4">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->zip }}</div>
                                <div class="address-phone-v4">Phone: {{ Auth::user()->phone }}</div>
                            </div>
                            @empty
                                <div style="grid-column: 1/-1; padding: 30px; border: 2px dashed #eee; border-radius: 20px; text-align: center; color: #999;">
                                    <p>No saved addresses found in your account.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="separator-v4"></div>

                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <button type="button" class="btn-add-address-v4" onclick="toggleNewAddressForm()">
                                <span style="font-size: 20px; line-height: 1;">+</span> Add New Address
                            </button>
                            @if(count($addresses) > 0)
                            <button type="button" class="btn-review-v4" onclick="goToStep(2)">Review Order</button>
                            @endif
                        </div>

                        <!-- New Address Form (Structured) -->
                        <div id="newAddressSection" style="display: none; margin-top: 40px; padding-top: 30px; border-top: 1px dashed #eee;">
                            <form action="{{ route('addresses.store') }}" method="POST">
                                @csrf
                                <div style="display: flex; justify-content: space-between; margin-bottom: 25px;">
                                    <h3 style="font-size: 18px; font-weight: 700;">Enter Address Details</h3>
                                    <button type="button" onclick="toggleNewAddressForm()" style="color: #999; border: none; background: none; cursor: pointer; font-size: 13px;">Cancel</button>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Recipient Name</label>
                                        <input type="text" id="guest_name" required class="form-input-v4" value="{{ Auth::user()?->name }}">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Recipient Email</label>
                                        <input type="email" id="guest_email" required class="form-input-v4" value="{{ Auth::user()?->email }}">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Contact Phone</label>
                                        <input type="tel" id="guest_phone" required class="form-input-v4" value="{{ Auth::user()?->phone }}">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Address Label</label>
                                        <input type="text" name="label" id="guest_label" required class="form-input-v4" placeholder="e.g. Home, Office">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Country</label>
                                        <input type="text" name="country" id="guest_country" value="India" readonly class="form-input-v4">
                                    </div>
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Flat, House no., Building, Company, Apartment</label>
                                    <input type="text" name="address1" id="guest_address1" required class="form-input-v4" placeholder="Street address etc.">
                                </div>

                                <div style="margin-bottom: 20px;">
                                    <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Area, Street, Sector, Village</label>
                                    <input type="text" name="address2" id="guest_address2" class="form-input-v4" placeholder="Optional">
                                </div>

                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">City</label>
                                        <input type="text" name="city" id="guest_city" required class="form-input-v4">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">State</label>
                                        <input type="text" name="state" id="guest_state" required class="form-input-v4">
                                    </div>
                                    <div>
                                        <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 8px; color: #333;">Pincode</label>
                                        <input type="text" name="zip" id="guest_zip" required class="form-input-v4">
                                    </div>
                                </div>

                                @auth
                                    <button type="submit" class="btn-review-v4" style="width: 100%; border-radius: 12px;">Save Address Details</button>
                                @else
                                    <button type="button" class="btn-review-v4" onclick="useGuestAddress()" style="width: 100%; border-radius: 12px;">Use this Address</button>
                                @endauth
                            </form>
                        </div>
                    </div>

                    <!-- Billing Address Section -->
                    <div class="card-v4">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <h3 style="font-size: 18px; font-weight: 700; color: #333;">Billing Address</h3>
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <span style="font-size: 13px; color: #666;">Same as Shipping</span>
                                <label class="switch-v4">
                                    <input type="checkbox" id="billingToggle" form="checkoutForm" name="same_as_shipping" checked onchange="toggleBillingForm()">
                                    <span class="slider-v4"></span>
                                </label>
                            </div>
                        </div>

                        <div id="billingAddressForm" style="display: none; margin-top: 30px; background: #fafafa; padding: 25px; border-radius: 16px; border: 1px solid #eee;">
                            <div class="form-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                <div class="form-group">
                                    <label style="display: block; font-size: 12px; color: #999; margin-bottom: 8px;">Full Name</label>
                                    <input type="text" name="billing_name" form="checkoutForm" class="form-input-v4">
                                </div>
                                <div class="form-group">
                                    <label style="display: block; font-size: 12px; color: #999; margin-bottom: 8px;">Phone</label>
                                    <input type="tel" name="billing_phone" form="checkoutForm" class="form-input-v4">
                                </div>
                                <div class="form-group" style="grid-column: span 2;">
                                    <label style="display: block; font-size: 12px; color: #999; margin-bottom: 8px;">Full Billing Address</label>
                                    <textarea name="billing_address" form="checkoutForm" class="form-input-v4" style="min-height: 80px;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: REVIEW -->
                <div id="step-2" class="checkout-step-content" style="display: none;">
                    <div class="card-v4">
                        <h2 class="section-title-v4">Confirm Your Order</h2>
                        <div style="background: #fafafa; padding: 25px; border-radius: 16px; margin-bottom: 25px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 15px; font-weight: 700;">
                                <span>Shipping To</span>
                                <a href="javascript:void(0)" onclick="goToStep(1)" style="color: var(--pink-dark); font-size: 12px;">Change</a>
                            </div>
                            <div id="reviewShippingText" style="font-size: 14px; color: #666; line-height: 1.6;"></div>
                        </div>
                        
                        <div class="separator-v4"></div>

                        @foreach($items as $item)
                        <div style="display: flex; gap: 20px; margin-bottom: 25px; align-items: center;">
                            <img src="{{ $item['image_url'] }}" style="width: 70px; height: 90px; object-fit: cover; border-radius: 12px;">
                            <div style="flex: 1;">
                                <div style="font-weight: 700; color: #333; font-size: 16px;">{{ $item['name'] }}</div>
                                <div style="font-size: 13px; color: #666;">Qty: {{ $item['quantity'] }}</div>
                                <div style="font-weight: 700; color: var(--pink-dark); margin-top: 5px;">₹{{ number_format($item['price'], 0) }}</div>
                            </div>
                        </div>
                        @endforeach

                        <div style="display: flex; justify-content: space-between; margin-top: 40px;">
                            <button type="button" onclick="goToStep(1)" style="color: #666; font-weight: 600; background: none; border: none; cursor: pointer;">Back</button>
                            <button type="button" class="btn-review-v4" onclick="goToStep(3)">Pay & Place Order</button>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: PAYMENT -->
                <div id="step-3" class="checkout-step-content" style="display: none;">
                    <form id="checkoutForm" action="{{ route('checkout.place') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="razorpay">
                        <input type="hidden" name="customer_name" id="final_name">
                        <input type="hidden" name="customer_email" id="final_email" value="{{ Auth::user()?->email }}">
                        <input type="hidden" name="customer_phone" id="final_phone">
                        <input type="hidden" name="delivery_address" id="final_address">
                        
                        <div class="card-v4">
                            <h2 class="section-title-v4">Secure Payment</h2>
                            <div style="padding: 25px; border: 2px solid var(--pink-dark); background: #fff9fa; border-radius: 20px; display: flex; align-items: center; gap: 20px; margin-bottom: 30px;">
                                <img src="https://razorpay.com/favicon.png" width="30">
                                <div style="flex: 1;">
                                    <div style="font-weight: 700; color: #333;">Razorpay Secure Checkout</div>
                                    <div style="font-size: 13px; color: #666;">Pay via UPI, Cards, Wallets or NetBanking</div>
                                </div>
                                <div style="width: 20px; height: 20px; border: 6px solid var(--pink-dark); border-radius: 50%;"></div>
                            </div>

                            <label style="display: flex; gap: 12px; cursor: pointer; font-size: 13px; color: #666; margin-bottom: 40px;">
                                <input type="checkbox" checked required style="margin-top: 3px;">
                                <span>I agree to the <a href="#" style="color: var(--pink-dark);">Terms Conditions</a> and privacy policies.</span>
                            </label>

                            <div style="display: flex; justify-content: space-between; items-center;">
                                <button type="button" onclick="goToStep(2)" style="color: #666; font-weight: 600; background: none; border: none; cursor: pointer;">Review Again</button>
                                <button type="submit" class="btn-review-v4">Confirm & Pay Now</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <aside>
                <div class="summary-card-v4">
                    <h3 class="summary-title-v4">Order Summary</h3>
                    
                    <div class="summary-row-v4">
                        <span>Subtotal ({{ $itemCount }} items)</span>
                        <span>₹{{ number_format($subTotal, 0) }}</span>
                    </div>
                    <div class="summary-row-v4">
                        <span>Delivery Charges</span>
                        <span style="color: #2e7d32; font-weight: 600;">FREE</span>
                    </div>
                    <div class="summary-row-v4">
                        <span>GST (5%)</span>
                        <span>₹{{ number_format($tax, 0) }}</span>
                    </div>
                    @if($discount > 0)
                    <div class="summary-row-v4" style="color: #2e7d32;">
                        <span>Coupon Savings</span>
                        <span>-₹{{ number_format($discount, 0) }}</span>
                    </div>
                    @endif

                    <div class="grand-total-v4">
                        <span>Grand Total</span>
                        <span>₹{{ number_format($grandTotal, 0) }}</span>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <img src="https://razorpay.com/favicon.png" width="16" style="vertical-align: middle; margin-right: 5px;">
                        <span style="font-size: 11px; font-weight: 700; color: #999; letter-spacing: 1px;">RAZORPAY SECURED</span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>
@endsection

@push('scripts')
<script>
    let selectedDetails = {};

    function initSelection() {
        const active = document.querySelector('.address-card-v4.active');
        if(active) {
            selectedDetails = {
                name: "{{ addslashes(Auth::user()?->name ?? '') }}",
                phone: "{{ addslashes(Auth::user()?->phone ?? '') }}",
                address: active.getAttribute('onclick').match(/'([^']+)', '([^']+)'/)[2]
            };
        }
    }
    initSelection();

    function selectSavedAddress(element, address) {
        document.querySelectorAll('.address-card-v4').forEach(c => c.classList.remove('active'));
        element.classList.add('active');
        
        selectedDetails = {
            name: "{{ addslashes(Auth::user()?->name ?? '') }}",
            phone: "{{ addslashes(Auth::user()?->phone ?? '') }}",
            address: address
        };
    }

    function toggleNewAddressForm() {
        const section = document.getElementById('newAddressSection');
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
        if(section.style.display === 'block') {
             window.scrollTo({ top: section.offsetTop - 50, behavior: 'smooth' });
        }
    }

    function useGuestAddress() {
        const name = document.getElementById('guest_name').value;
        const email = document.getElementById('guest_email').value;
        const phone = document.getElementById('guest_phone').value;
        const addr1 = document.getElementById('guest_address1').value;
        const addr2 = document.getElementById('guest_address2').value;
        const city = document.getElementById('guest_city').value;
        const state = document.getElementById('guest_state').value;
        const zip = document.getElementById('guest_zip').value;

        if(!name || !email || !phone || !addr1 || !city || !state || !zip) {
            alert('Please fill all required address fields.');
            return;
        }

        selectedDetails = {
            name: name,
            email: email,
            phone: phone,
            address: `${addr1}${addr2 ? ', ' + addr2 : ''}, ${city}, ${state} - ${zip}`
        };

        goToStep(2);
    }

    function toggleBillingForm() {
        const checked = document.getElementById('billingToggle').checked;
        document.getElementById('billingAddressForm').style.display = checked ? 'none' : 'block';
    }

    function goToStep(num) {
        if(num === 2 && !selectedDetails.address) {
            alert('Please select or add an address first.');
            return;
        }

        document.querySelectorAll('.checkout-step-content').forEach(s => s.style.display = 'none');
        document.getElementById('step-' + num).style.display = 'block';

        if(num === 2) {
            document.getElementById('reviewShippingText').innerHTML = `<strong>${selectedDetails.name}</strong><br>${selectedDetails.address.replace(/\n/g, '<br>')}<br>Phone: ${selectedDetails.phone}`;
            // Sync to final hidden fields
            document.getElementById('final_name').value = selectedDetails.name;
            document.getElementById('final_email').value = selectedDetails.email || "{{ Auth::user()?->email }}";
            document.getElementById('final_phone').value = selectedDetails.phone;
            document.getElementById('final_address').value = selectedDetails.address;
        }
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
</script>
@endpush
