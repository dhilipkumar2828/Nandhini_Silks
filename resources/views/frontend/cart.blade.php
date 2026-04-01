@extends('frontend.layouts.app')

@section('title', 'Shopping Cart | Nandhini Silks')

@section('content')
    @push('styles')
    <style>
        @media (max-width: 768px) {
            .cart-page {
                padding: 18px 0 30px;
            }

            .cart-items-list,
            .cart-summary {
                padding: 18px;
                border-radius: 16px;
            }

            .cart-item-row {
                grid-template-columns: 72px 1fr auto !important;
                grid-template-areas:
                    "image info remove"
                    "image price price"
                    "image qty qty" !important;
                align-items: start !important;
                gap: 8px 14px !important;
                padding: 16px 0 !important;
            }

            .cart-item-img {
                width: 72px;
                height: 72px;
            }

            .cart-item-info h3 {
                font-size: 15px;
                line-height: 1.4;
                margin-bottom: 6px !important;
            }

            .cart-item-info .item-variants {
                font-size: 10px !important;
                line-height: 1.5;
            }

            .cart-item-price {
                font-size: 16px;
                font-weight: 700;
            }

            .quantity-picker {
                justify-self: start !important;
                transform: scale(0.95);
                transform-origin: left center;
                margin-top: 2px;
            }

            .remove-item {
                font-size: 20px;
                line-height: 1;
                padding: 0;
            }

            .summary-title {
                font-size: 18px;
                margin-bottom: 18px;
                padding-bottom: 12px;
            }

            .summary-row,
            .summary-total {
                font-size: 15px;
                gap: 12px;
            }

            .summary-total {
                font-size: 18px;
            }

            .coupon-input-group {
                flex-direction: column;
                gap: 10px;
            }

            .coupon-input,
            .btn-apply-coupon {
                width: 100%;
                border-radius: 8px;
            }

            .btn-apply-coupon {
                min-height: 46px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 15px;
                font-weight: 700;
                letter-spacing: 0.02em;
            }

            .btn-checkout,
            .btn-continue-shopping {
                margin-top: 0;
                min-height: 48px;
                font-size: 16px;
            }
        }

        .back-to-shop:hover {
            color: #A91B43 !important;
            transform: translateX(-5px);
        }
    </style>
    @endpush
    <main class="cart-page">
        <div class="page-shell">
            <div style="margin-bottom: 15px;">
                <a href="{{ route('shop') }}" class="back-to-shop" style="display: inline-flex; align-items: center; gap: 8px; color: #ad8b4e; text-decoration: none; font-weight: 700; transition: all 0.3s ease; font-size: 18px;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="margin-top: 2px;"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    Continue Shopping
                </a>
            </div>

            <h1 class="auth-title" style="text-align: left; margin-bottom: 25px; display: flex; align-items: center; gap: 15px;">
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
                            <div class="cart-header-row"
                                style="display: grid; grid-template-columns: 80px 1fr 120px 150px 40px; gap: 20px; padding-bottom: 8px; border-bottom: 2px solid #eee; margin-bottom: 10px; font-weight: 700; color: #333;">
                                <span style="font-size: 17px;">Product</span>
                                <span style="font-size: 17px; padding-left: 20px;">Details</span>
                                <span style="font-size: 17px; margin-left: -15px;">Unit Price</span>
                                <span style="font-size: 17px;">Quantity</span>
                                <span style="font-size: 17px;"></span>
                            </div>

                            @foreach ($items as $item)
                                <div class="cart-item-row" id="item-{{ $item['key'] }}">
                                    <div class="cart-item-img">
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                    </div>
                                    <div class="cart-item-info" style="padding-left: 20px;">
                                        <h3 style="margin-bottom: 5px;">{{ $item['name'] }}</h3>
                                        @if(!empty($item['display_attributes']))
                                            <div class="item-variants" style="font-size: 13px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; display: flex; flex-wrap: wrap; gap: 5px;">
                                                @foreach($item['display_attributes'] as $attr)
                                                    <span class="variant-tag" style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">{{ $attr['name'] }}: {{ $attr['value'] }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <div class="cart-item-price" style="margin-left: -15px;">&#8377;{{ number_format($item['price'], 0) }}</div>
                                    <div class="quantity-picker">
                                        <button type="button" class="qty-btn" onclick="updateCartQty('{{ $item['key'] }}', -1)">-</button>
                                        <input type="text" class="qty-input" name="quantities[{{ $item['key'] }}]" value="{{ $item['quantity'] }}" readonly>
                                        <button type="button" class="qty-btn" onclick="updateCartQty('{{ $item['key'] }}', 1)">+</button>
                                    </div>
                                    <button type="button" class="remove-item" onclick="removeItem('{{ $item['key'] }}')" aria-label="Remove item" style="color: #ff3b30; font-size: 24px; font-weight: bold; background: none; border: none; cursor: pointer; transition: transform 0.2s;">&times;</button>
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
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span id="shippingDisp">{{ $shipping > 0 ? '₹' . number_format($shipping, 2) : 'FREE' }}</span>
                    </div>
                    @if($tax > 0)
                    <div class="summary-row">
                        <span>Estimated Tax (GST <span id="taxRateLabel">{{ $taxPercentage ?? 0 }}</span>%)</span>
                        <span id="taxDisp">&#8377;{{ number_format($tax ?? 0, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-row" style="color: #2e7d32; font-weight: 600;">
                        <span>Coupon Discount</span>
                        <span id="discountDisp">-&#8377;{{ number_format($discount ?? 0, 2) }}</span>
                    </div>

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
                                <input type="text" name="code" class="coupon-input" placeholder="Promo code" value="{{ old('code') }}">
                                <button type="submit" class="btn-apply-coupon">Apply</button>
                            </form>
                        @endif
                    </div>

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
            const input = document.querySelector(`input[name="quantities[${key}]\"]`);
            if (!input) return;
            let current = parseInt(input.value) || 0;
            current += val;
            if (current < 1) return;
            input.value = current;

            // Debounce AJAX call
            clearTimeout(window.cartUpdateTimer);
            window.cartUpdateTimer = setTimeout(() => {
                ajaxUpdateCart(key, current);
                // SHOW SMALL FEEDEBACK
                toastr.success('Updating quantity...', '', { timeOut: 1000, progressBar: false });
            }, 500);
        }

        function ajaxUpdateCart(key, qty) {
            // Show loading state on summary spans
            ['subtotalDisp','taxDisp','totalDisp'].forEach(id => {
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
                return res.json();
            })
            .then(data => {
                if (!data) return;

                // Update totals if returned
                if (data.subTotal !== undefined) {
                    setAmt('subtotalDisp', data.subTotal);
                    setAmt('taxDisp', data.tax);
                    setAmt('totalDisp', data.grandTotal);
                    
                    if (data.taxPercentage !== undefined) {
                        const taxLabel = document.getElementById('taxRateLabel');
                        if (taxLabel) taxLabel.textContent = data.taxPercentage;
                    }
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

                    // toastr.success('Cart updated.');
                    
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

        window.refreshCartPage = function() {
            // Since cart list is complex, we just reload the page to get fresh state correctly
            window.location.reload();
        };

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
