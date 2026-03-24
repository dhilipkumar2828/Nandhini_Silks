@extends('frontend.layouts.app')

@section('title', 'Shopping Cart | Nandhini Silks')

@section('content')
    <main class="cart-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>Shopping Cart</span>
            </div>

            <h1 class="auth-title" style="text-align: left; margin-bottom: 20px;">Your Shopping Cart</h1>

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
                                <span>Product</span>
                                <span style="padding-left: 20px;">Details</span>
                                <span style="margin-left: -15px;">Unit Price</span>
                                <span>Quantity</span>
                                <span></span>
                            </div>

                            @foreach ($items as $item)
                                <div class="cart-item-row" id="item-{{ $item['key'] }}">
                                    <div class="cart-item-img">
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}">
                                    </div>
                                    <div class="cart-item-info" style="padding-left: 20px;">
                                        <h3 style="margin-bottom: 5px;">{{ $item['name'] }}</h3>
                                        @if(!empty($item['size']) || !empty($item['color']))
                                            <div class="item-variants" style="font-size: 11px; color: #666; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">
                                                @if(!empty($item['size'])) <span class="variant-tag" style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px; margin-right: 5px;">Size: {{ $item['size'] }}</span> @endif
                                                @if(!empty($item['color'])) <span class="variant-tag" style="background: #f5f5f5; padding: 2px 6px; border-radius: 4px;">Color: {{ $item['color'] }}</span> @endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="cart-item-price" style="margin-left: -15px;">&#8377;{{ number_format($item['price'], 0) }}</div>
                                    <div class="quantity-picker">
                                        <button type="button" class="qty-btn" onclick="updateCartQty('{{ $item['key'] }}', -1)">-</button>
                                        <input type="text" class="qty-input" name="quantities[{{ $item['key'] }}]" value="{{ $item['quantity'] }}" readonly>
                                        <button type="button" class="qty-btn" onclick="updateCartQty('{{ $item['key'] }}', 1)">+</button>
                                    </div>
                                    <button type="button" class="remove-item" onclick="removeItem('{{ $item['key'] }}')" aria-label="Remove item">x</button>
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
                        <span id="subtotalDisp">&#8377;{{ number_format($subTotal ?? 0, 0) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>FREE</span>
                    </div>
                    <div class="summary-row">
                        <span>Estimated Tax (GST 5%)</span>
                        <span id="taxDisp">&#8377;{{ number_format($tax ?? 0, 0) }}</span>
                    </div>
                    <div class="summary-row" style="color: #2e7d32; font-weight: 600;">
                        <span>Coupon Discount</span>
                        <span id="discountDisp">-&#8377;{{ number_format($discount ?? 0, 0) }}</span>
                    </div>

                    <div class="coupon-section">
                        <p style="font-size: 14px; font-weight: 600; color: #333;">Have a coupon code?</p>
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
                        <span id="totalDisp">&#8377;{{ number_format($grandTotal ?? 0, 0) }}</span>
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
            let current = parseInt(input.value) || 0;
            current += val;
            if (current < 1) current = 1;
            input.value = current;
        }

        function removeItem(key) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ url('cart/remove') }}/" + key;
            
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = "{{ csrf_token() }}";
            
            form.appendChild(csrf);
            document.body.appendChild(form);
            form.submit();
        }

    </script>
@endpush
