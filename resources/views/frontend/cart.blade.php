@extends('frontend.layouts.app')

@section('title', 'Shopping Cart | Nandhini Silks')

@section('content')
    @push('styles')
        <style>
            /* Desktop Layout - Fixed Width Flexbox for Perfect Alignment */
            @media (min-width: 769px) {
                .cart-layout-grid {
                    display: flex !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    gap: 0 !important;
                    width: 100%;
                }

                .col-product {
                    width: 100px;
                    flex-shrink: 0;
                }

                .col-details {
                    flex-grow: 1;
                    padding: 0 20px;
                    min-width: 0;
                }

                .col-price {
                    width: 140px;
                    flex-shrink: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .col-quantity {
                    width: 160px;
                    flex-shrink: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }



                .col-remove {
                    width: 80px;
                    flex-shrink: 0;
                    display: flex;
                    justify-content: center;
                }

                /* Header Specifics */
                .cart-header-row {
                    padding-bottom: 20px;
                    border-bottom: 2px solid #f0f0f0;
                    margin-bottom: 15px;
                    font-weight: 800;
                    color: #222;
                    font-size: 16px;
                }

                .cart-header-row span {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    text-align: left;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    color: #888;
                }

                .cart-header-row .col-product,
                .cart-header-row .col-details {
                    justify-content: flex-start;
                    text-align: left;
                }

                /* Reduce space on medium screens */
                @media (max-width: 1100px) {
                    .col-details {
                        padding: 0 15px;
                    }

                    .col-price {
                        width: 110px;
                    }

                    .col-quantity {
                        width: 150px;
                    }
                }
            }

            .cart-grid {
                display: grid !important;
                grid-template-columns: 1fr 380px !important;
                gap: 40px !important;
                align-items: start !important;
                max-width: 1540px;
                margin: 0 auto;
                width: 100%;
            }

            .cart-items-list,
            .cart-summary {
                background: #fff;
                border-radius: 20px;
                padding: 30px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            }

            .cart-item-row {
                padding: 24px 0 !important;
                border-bottom: 1px solid #f0f0f0 !important;
            }

            .cart-item-row:last-child {
                border-bottom: none !important;
            }

            .cart-item-img {
                width: 100px;
                height: 125px;
                border-radius: 12px;
                overflow: hidden;
                background: #f9f9f9;
            }

            .cart-item-img img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .cart-item-info h3 {
                font-size: 18px;
                font-weight: 700;
                margin: 0 0 5px;
                color: #111;
            }

            .cart-item-price {
                font-size: 19px;
                font-weight: 800;
                color: #A91B43;
            }

            .quantity-picker {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0;
                width: fit-content;
            }

            .remove-item {
                display: flex;
                align-items: flex-start;
                justify-content: center;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                background: #fff5f5 !important;
                color: #ff3b30 !important;
                border: 1px solid #ffebeb !important;
                font-size: 20px !important;
                transition: all 0.2s;
                cursor: pointer;
            }

            .remove-item:hover {
                background: #ff3b30 !important;
                color: #fff !important;
                transform: scale(1.1);
            }

            .cart-summary {
                position: sticky;
                top: 24px;
            }

            .summary-title {
                font-size: 22px;
                font-weight: 700;
                color: #333;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid #f5f5f5;
            }

            .summary-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 15px;
                color: #666;
                font-size: 16px;
            }

            .summary-total {
                display: flex;
                justify-content: space-between;
                margin-top: 25px;
                padding-top: 20px;
                border-top: 2px solid #f5f5f5;
                font-size: 20px;
                font-weight: 800;
                color: #A91B43;
            }

            /* Responsive Adjustments for Ultra-Wide and Large Desktop */
            @media (min-width: 1600px) {
                .cart-grid {
                    grid-template-columns: 1fr 420px !important;
                }
            }

            /* Tablet Viewport (Stacking Sidebar) */
            @media (max-width: 1100px) {
                .cart-grid {
                    grid-template-columns: 1fr !important;
                    gap: 30px !important;
                }

                .cart-summary {
                    position: static !important;
                    max-width: 100%;
                }
            }

            /* Mobile and Small Screen Adjustments */
            @media (max-width: 768px) {
                .cart-page {
                    padding: 15px 0 40px;
                    width: 95%;
                    margin: 0 auto;
                }

                .cart-header-row {
                    display: none !important;
                }

                .cart-items-list,
                .cart-summary {
                    padding: 20px;
                    border-radius: 16px;
                }

                .cart-item-row {
                    grid-template-columns: 90px 1fr auto !important;
                    grid-template-areas:
                        "image info remove"
                        "image price remove"
                        "image tax remove"
                        "image qty remove" !important;
                    align-items: center !important;
                    gap: 5px 15px !important;
                    padding: 20px 0 !important;
                    border-bottom: 1px solid #f0f0f0 !important;
                    display: grid !important;
                }

                .cart-item-img {
                    grid-area: image;
                    width: 90px;
                    height: 115px;
                }

                .cart-item-info {
                    grid-area: info;
                    padding-left: 0 !important;
                }

                .cart-item-info h3 {
                    font-size: 16px;
                    line-height: 1.4;
                    margin-bottom: 5px !important;
                }

                .cart-item-info .item-variants {
                    font-size: 11px !important;
                }

                .cart-item-price {
                    grid-area: price;
                    text-align: left;
                    font-size: 17px;
                    margin-left: 0 !important;
                    position: relative;
                    right: 0px;
                }



                .quantity-picker {
                    grid-area: qty;
                    justify-self: start !important;
                    margin: 0 !important;
                    scale: 0.95;
                    transform-origin: left;
                    position: relative;
                    right: 0px;
                }

                .remove-item {
                    grid-area: remove;
                    align-self: center !important;
                    justify-self: end !important;
                    margin: 0 !important;
                    font-size: 20px !important;
                }

                /* Summary Coupon Field stacking */
                .coupon-input-group {
                    flex-direction: row !important;
                    gap: 12px !important;
                }

                .coupon-input-group input,
                .coupon-input-group button {
                    width: 20% !important;
                }
            }

            @media (max-width: 480px) {
                .cart-item-row {
                    grid-template-columns: 80px 1fr auto !important;
                }

                .coupon-input-group input,
                .coupon-input-group button {
                    width: 30% !important;
                }

                .cart-item-img {
                    width: 80px;
                    height: 130px;
                }
            }

            .back-to-shop:hover {
                color: #A91B43 !important;
                transform: translateX(-5px);
            }

            /* Pincode Check Styles in Summary */
            .summary-pincode-check {
                margin: 20px 0;
                padding: 15px;
                background: #fdfafb;
                border-radius: 12px;
                border: 1px solid #f8e8ee;
            }

            .summary-pincode-check p {
                font-size: 13px;
                font-weight: 700;
                color: #333;
                margin-bottom: 8px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .pincode-summary-group {
                display: flex;
                gap: 8px;
            }

            .pincode-summary-input {
                flex: 1;
                height: 38px;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 0 12px;
                font-size: 14px;
                outline: none;
            }

            .pincode-summary-input:focus {
                border-color: #A91B43;
            }

            .btn-pincode-summary {
                background: #A91B43;
                color: #fff;
                border: none;
                border-radius: 8px;
                padding: 0 12px;
                font-size: 12px;
                font-weight: 700;
                cursor: pointer;
                transition: all 0.3s;
            }

            .btn-pincode-summary:hover {
                background: #8e1638;
            }

            .btn-pincode-summary:disabled {
                opacity: 0.6;
                cursor: not-allowed;
            }

            #summaryDeliveryNote {
                font-size: 12px;
                margin-top: 8px;
                line-height: 1.4;
            }
        </style>
    @endpush
    <main class="cart-page">
        <div class="page-shell">
            {{-- <div style="margin-bottom: 15px;">
                <a href="{{ route('shop') }}" class="back-to-shop"
                    style="display: inline-flex; align-items: center; gap: 8px; color: #ad8b4e; text-decoration: none; font-weight: 700; transition: all 0.3s ease; font-size: 18px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                        stroke-linecap="round" stroke-linejoin="round" style="margin-top: 2px;">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    Continue Shopping
                </a>
            </div> --}}

            <h1 class="auth-title"
                style="text-align: left; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
                Your Shopping Cart
            </h1>

            @php
                $hasItems = isset($items) && count($items) > 0;
            @endphp

            <div class="cart-grid">
                <div class="cart-items-list">
                    @if ($hasItems)
                        <form id="cartForm" method="POST" action="{{ route('cart.update') }}">
                            @csrf
                            <div class="cart-header-row cart-layout-grid">
                                <span class="col-product">Product</span>
                                <span class="col-details">Details</span>
                                <span class="col-price">Unit Price</span>
                                <span class="col-quantity">Quantity</span>

                                <span class="col-remove">Action</span>
                            </div>

                            @foreach ($items as $item)
                                <div class="cart-item-row cart-layout-grid" id="item-{{ $item['key'] }}">
                                    <div class="cart-item-img col-product">
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                    </div>
                                    <div class="cart-item-info col-details">
                                        <h3>{{ $item['name'] }}</h3>
                                        @if(!empty($item['display_attributes']))
                                            <div class="item-variants"
                                                style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: flex; flex-wrap: wrap; gap: 5px;">
                                                @foreach($item['display_attributes'] as $attr)
                                                    <span class="variant-tag"
                                                        style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">{{ $attr['name'] }}:
                                                        {{ $attr['value'] }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="cart-item-price col-price">
                                        &#8377;{{ number_format($item['price'], 0) }}</div>
                                    <div class="quantity-picker col-quantity">
                                        <div class="picker-container"
                                            style="display: flex; align-items: center; background: #f8f9fa; border-radius: 10px; padding: 4px; border: 1px solid #eee; width: fit-content;"
                                            data-stock="{{ $item['stock_quantity'] ?? 0 }}">
                                            <button type="button" class="qty-btn"
                                                onclick="updateCartQty('{{ $item['key'] }}', -1)">-</button>
                                            <input type="text" class="qty-input" name="quantities[{{ $item['key'] }}]"
                                                value="{{ $item['quantity'] }}" readonly>
                                            <button type="button" class="qty-btn"
                                                onclick="updateCartQty('{{ $item['key'] }}', 1)">+</button>
                                        </div>
                                    </div>

                                    <div class="col-remove">
                                        <button type="button" class="remove-item" onclick="removeItem('{{ $item['key'] }}')"
                                            aria-label="Remove item">&times;</button>
                                    </div>
                                </div>
                            @endforeach
                        </form>
                    @else
                        <div class="no-products">
                            <p>Your cart is empty.</p>
                        </div>
                    @endif
                </div>

                <aside class="cart-summary">
                    <h2 class="summary-title">Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal ({{ $itemCount ?? 0 }} items)</span>
                        <span id="subtotalDisp">&#8377;{{ number_format($subTotal ?? 0, 2) }}</span>
                    </div>
                    {{-- <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shippingDisp">
                            @if(session()->has('shipping_rate'))
                                {{ $shipping > 0 ? '₹' . number_format($shipping, 2) : 'FREE' }}
                            @else
                                <span style="font-size: 12px; color: #888; font-weight: 500;">(Calculated after check out)</span>
                            @endif
                        </span>
                    </div> --}}

                    

                    @if($discount > 0)
                    <div class="summary-row" style="color: #2e7d32; font-weight: 600;">
                        <span>Coupon Discount</span>
                        <span id="discountDisp">-&#8377;{{ number_format($discount ?? 0, 2) }}</span>
                    </div>
                    @endif

                    <div class="coupon-section">
                        <p style="font-size: 15px; font-weight: 600; color: #333;">Have a coupon code?</p>
                        @if(session('success'))
                            <div style="font-size: 12px; color: #2e7d32; margin-bottom: 8px;">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div style="font-size: 12px; color: #c62828; margin-bottom: 8px;">{{ session('error') }}</div>
                        @endif
                        @error('code')
                            <div style="font-size: 12px; color: #c62828; margin-bottom: 8px;">{{ $message }}</div>
                        @enderror

                        @if($coupon)
                            <div class="applied-coupons">
                                <div class="coupon-tag">
                                    <span>{{ $coupon->code }}</span>
                                    <form method="POST" action="{{ route('cart.coupon.remove') }}">
                                        @csrf
                                        <button type="submit" class="remove-coupon" aria-label="Remove coupon">x</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <form method="POST" action="{{ route('cart.coupon.apply') }}" class="coupon-input-group">
                                @csrf
                                <input type="text" name="code" class="coupon-input" placeholder="Promo code"
                                    value="{{ old('code') }}">
                                <button type="submit" class="btn-apply-coupon">Apply</button>
                            </form>
                        @endif
                    </div>

                    {{-- Delivery check removed as per customer request (Shipping process only in checkout) --}}
                    {{-- @if($hasItems)
                        <div class="summary-pincode-check">
                            <p><i class="fas fa-truck" style="color: #A91B43;"></i> Delivery Availability</p>
                            <div class="pincode-summary-group">
                                <input type="text" class="pincode-summary-input" maxlength="6" 
                                    placeholder="Enter Pincode" value="{{ session('checked_pincode') }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 6)">
                                <button type="button" class="btn-pincode-summary">Check</button>
                            </div>
                            <div id="summaryDeliveryNote">
                                @if(session('checked_pincode') && session('checked_pincode_edd'))
                                    <span style="color: #2e7d32; font-weight: 600;"><i class="fas fa-check-circle"></i> Delivery by {{ session('checked_pincode_edd') }}</span>
                                @endif
                            </div>
                        </div>
                    @endif --}}

                    <div class="summary-total">
                        <span>Total</span>
                        <span id="totalDisp">&#8377;{{ number_format($grandTotal ?? 0, 2) }}</span>
                    </div>

                    <div class="cart-footer-btns">
                        @if ($hasItems)
                            <button type="submit" form="cartForm" name="action" value="checkout" class="btn-checkout"
                                style="width: 100%; text-decoration: none; text-align: center;">Proceed to Checkout</button>
                        @endif
                        <a href="{{ route('shop') }}" class="btn-continue-shopping">Continue Shopping</a>
                    </div>
                </aside>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        function updateCartQty(key, val) {
            const input = document.querySelector(`input[name="quantities[${key}]"]`);
            if (!input) return;
            
            const container = input.closest('.picker-container');
            const stock = parseInt(container?.dataset.stock) || 0;
            
            let current = parseInt(input.value) || 0;
            let target = current + val;
            
            if (target < 1) return;
            if (val > 0 && target > stock) {
                toastr.error(`Only ${stock} items available in stock.`);
                return;
            }
            
            input.value = target;

            // Debounce AJAX call
            clearTimeout(window.cartUpdateTimer);
            window.cartUpdateTimer = setTimeout(() => {
                ajaxUpdateCart(key, target, current); // Pass current to revert if failed
                if (val > 0) {
                    toastr.success('Quantity Increased', '', { timeOut: 1500, progressBar: true });
                } else {
                    toastr.info('Quantity Decreased', '', { timeOut: 1500, progressBar: true });
                }
            }, 300);
        }

        function ajaxUpdateCart(key, qty, oldQty) {
            // Show loading state on summary spans
            ['subtotalDisp', 'taxDisp', 'totalDisp'].forEach(id => {
                const el = document.getElementById(id);
                if (el) { el.style.opacity = '0.4'; }
            });

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                || '{{ csrf_token() }}';

            const formData = new FormData();
            formData.append('_token', token);
            formData.append(`quantities[${key}]`, qty);

            fetch('{{ route("cart.update") }}', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            })
                .then(res => {
                    if (res.status === 419) {
                        Swal.fire({
                            title: 'Session Expired',
                            text: 'Your session has expired. Please refresh the page to continue.',
                            icon: 'warning',
                            confirmButtonText: 'Refresh Page',
                            confirmButtonColor: '#A91B43'
                        }).then(() => {
                            window.location.reload();
                        });
                        throw new Error('CSRF token mismatch');
                    }
                    
                    return res.json().then(data => {
                        if (!res.ok) {
                            // Handle Validation Errors (like stock limit)
                            toastr.error(data.message || 'Failed to update cart');
                            if (oldQty !== undefined) {
                                const input = document.querySelector(`input[name="quantities[${key}]"]`);
                                if (input) input.value = oldQty;
                            }
                            // Reset opacity
                            ['subtotalDisp', 'taxDisp', 'totalDisp'].forEach(id => {
                                const el = document.getElementById(id);
                                if (el) { el.style.opacity = '1'; }
                            });
                            throw new Error(data.message || 'Server error');
                        }
                        return data;
                    });
                })
                .then(data => {
                    if (!data) return;

                    // Update totals if returned
                    if (data.subTotal !== undefined) {
                        setAmt('subtotalDisp', data.subTotal);
                        setAmt('totalDisp', data.grandTotal);

                        const shipEl = document.getElementById('shippingDisp');
                        if (shipEl) {
                            shipEl.textContent = data.shipping > 0 ? '₹' + fmt(data.shipping) : 'FREE';
                            shipEl.style.opacity = '1';
                        }
                        const discEl = document.getElementById('discountDisp');
                        if (discEl) {
                            discEl.textContent = '-₹' + fmt(data.discount || 0);
                            discEl.style.opacity = '1';
                        }



                        // BROADCAST to other tabs (but skip same-tab reload)
                        if (window.notifyCartUpdate) {
                            // We use a small flag to skip self-reload if we were to implement it
                            localStorage.setItem('nandhini_cart_updated', Date.now());
                        }

                    } else {
                        window.location.reload();
                    }
                })
                .catch((error) => {
                    if (error.message !== 'CSRF token mismatch') {
                        // Silently refresh or show error
                        toastr.error('Connection error. Updating cart...');
                        setTimeout(() => window.location.reload(), 1000);
                    }
                });
        }

        window.refreshCartPage = function () {
            // Since cart list is complex, we just reload the page to get fresh state correctly
            window.location.reload();
        };

        // Cart Page Pincode Check
        document.querySelector('.btn-pincode-summary')?.addEventListener('click', function() {
            const btn = this;
            const input = document.querySelector('.pincode-summary-input');
            const note = document.getElementById('summaryDeliveryNote');
            const pincode = input.value.trim();

            if (pincode.length !== 6) {
                toastr.warning('Please enter a 6-digit pincode.');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            note.innerHTML = '<span style="color: #666;">Checking...</span>';

            fetch('{{ route("check-serviceability") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ pincode: pincode })
            })
            .then(res => res.json())
            .then(data => {
                console.log('Full API Response:', data); // Added for debugging
                btn.disabled = false;
                btn.innerHTML = 'Check';

                if (data.success) {
                    note.innerHTML = `<span style="color: #2e7d32; font-weight: 600;"><i class="fas fa-check-circle"></i> ${data.message}</span>`;
                    toastr.success(data.message);
                    
                    // Instant update summary from returned totals
                    if (data.totals) {
                        setAmt('subtotalDisp', data.totals.subTotal);
                        setAmt('taxDisp', data.totals.tax);
                        setAmt('totalDisp', data.totals.grandTotal);
                        
                        const shipEl = document.getElementById('shippingDisp');
                        if (shipEl) {
                            shipEl.textContent = data.totals.shipping > 0 ? '₹' + fmt(data.totals.shipping) : 'FREE';
                        }
                    }
                } else {
                    note.innerHTML = `<span style="color: #c62828; font-weight: 600;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    toastr.error(data.message);
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = 'Check';
                note.innerHTML = '<span style="color: #c62828;">Error checking availability.</span>';
            });
        });

        function setAmt(id, val) {
            const el = document.getElementById(id);
            if (!el) return;
            el.textContent = '₹' + fmt(val);
            el.style.opacity = '1';
            el.style.transition = 'opacity 0.3s ease';
        }

        function fmt(val) {
            return Number(val).toLocaleString('en-IN', { maximumFractionDigits: 2, minimumFractionDigits: 2 });
        }
    </script>
@endpush