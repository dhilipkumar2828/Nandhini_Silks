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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
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
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
        }

        .address-card-v4.active {
            border: 2px solid var(--pink-dark);
            background: #fff;
        }

        .address-tag-v4 {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: #1e88e5;
            background: #e3f2fd;
            padding: 3px 10px;
            border-radius: 6px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .address-name-v4 {
            font-size: 16px;
            font-weight: 700;
            color: #333;
            margin-bottom: 8px;
        }

        .address-text-v4 {
            font-size: 14px;
            color: #666;
            line-height: 1.6;
        }

        .address-phone-v4 {
            font-size: 14px;
            color: #666;
            margin-top: 10px;
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
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
            position: sticky;
            top: 20px;
        }

        .summary-title-v4 {
            font-size: 19px;
            /* Increased from 18px */
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }

        .summary-row-v4 {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 16px;
            /* Increased from 15px */
            color: #666;
        }

        .grand-total-v4 {
            border-top: 1px solid #eee;
            margin-top: 20px;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            font-size: 21px;
            /* Increased from 20px */
            font-weight: 700;
            color: var(--pink-dark);
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

        .form-input-v4 {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            font-family: 'Outfit', sans-serif;
            background: #fff;
            color: #333;
        }

        .form-input-v4:focus {
            border-color: var(--pink-dark);
            box-shadow: 0 0 0 3px rgba(148, 4, 55, 0.08);
        }

        select.form-input-v4 {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            cursor: pointer;
        }

        @media (max-width: 964px) {
            .checkout-grid {
                grid-template-columns: 1fr !important;
                gap: 18px !important;
            }

            .checkout-main,
            .checkout-sidebar {
                width: 100% !important;
                min-width: 0 !important;
            }

            .checkout-sidebar {
                order: 2;
            }

            .checkout-sidebar .summary-card-v4 {
                position: static !important;
                top: auto !important;
            }

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
                font-size: 11px !important;
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

            /* Responsive Address Cards */
            #savedAddressesSection {
                padding-bottom: 12px !important;
            }

            .saved-address-card {
                width: calc(33.33% - 10px) !important;
                flex: 0 0 calc(33.33% - 10px) !important;
            }
        }

        @media (min-width: 965px) {
            .saved-address-card {
                width: calc(33.33% - 10px) !important;
                flex: 0 0 calc(33.33% - 10px) !important;
            }
        }

        @media (max-width: 580px) {
            .saved-address-card {
                width: 85% !important;
                flex: 0 0 85% !important;
            }
        }

        .saved-address-card:hover {
            border-color: var(--pink-dark) !important;
        }

        .saved-address-card .check-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 17px;
            animation: scaleIn 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }

        /* CUSTOM SCROLLBAR - VISIBLE */
        #savedAddressesSection {
            overflow-x: scroll !important; /* Force scroll visibility potential */
            padding-bottom: 20px !important;
        }

        #savedAddressesSection::-webkit-scrollbar {
            height: 7px;
            display: block !important;
        }

        #savedAddressesSection::-webkit-scrollbar-track {
            background: #f8f8f8;
            border-radius: 10px;
            border: 1px solid #eee;
        }

        #savedAddressesSection::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
            border: 1px solid #777;
        }

        #savedAddressesSection::-webkit-scrollbar-thumb:hover {
            background: var(--pink-dark);
        }
    </style>
@endpush

@section('content')
    <main class="checkout-page-container">
        <div class="page-shell">
            <form id="singleCheckoutForm" class="validate-form" action="{{ route('checkout.place') }}" method="POST"
                novalidate>
                @csrf
                <input type="hidden" name="payment_method" id="payment_method_input" value="razorpay">
                <input type="hidden" name="customer_email" value="{{ Auth::user()?->email }}">

                <div class="checkout-grid"
                    style="display: grid; grid-template-columns: 1fr 380px; gap: 40px; align-items: start;">
                    <div class="checkout-main">
                        @if(session('error'))
                            <div
                                style="background: #ffebee; color: #c62828; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 14px; font-weight: 500; border: 1px solid #ffcdd2;">
                                {{ session('error') }}
                            </div>
                        @endif

                        {{-- SHIPPING ADDRESS --}}
                        <div class="card-v4" style="margin-bottom: 25px; position: relative;">
                            <div class="shipping-header" style="margin-bottom: 20px;">
                                <h2 class="section-title-v4" style="font-size: 20px; margin-bottom: 10px;">Shipping Address
                                </h2>

                                @if($addresses->count() > 0)
                                    <div
                                        style="background: #fff9fa; border: 1px solid #fce7f3; padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                                        <label
                                            style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 14px; font-weight: 700; color: var(--pink-dark);">
                                            <input type="checkbox" id="useSavedAddressToggle"
                                                onchange="toggleSavedAddresses(this)"
                                                style="accent-color: var(--pink-dark); width: 18px; height: 18px; cursor: pointer;">
                                            Use a saved address
                                        </label>

                                        <div id="savedAddressesSection"
                                            style="display: none; margin-top: 15px; overflow-x: auto; padding: 5px 0; scroll-behavior: smooth; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
                                            <div style="display: flex; gap: 15px; width: 100%;">
                                                @foreach($addresses as $addr)
                                                    <div class="saved-address-card" onclick="selectSavedAddress(this)"
                                                        data-name="{{ $addr->recipient_name ?? Auth::user()->name }}"
                                                        data-phone="{{ $addr->recipient_phone ?? Auth::user()->phone }}"
                                                        data-addr="{{ $addr->address1 }}" data-city="{{ $addr->city }}"
                                                        data-state="{{ $addr->state }}"
                                                        data-country="{{ $addr->country ?? 'India' }}" data-zip="{{ $addr->zip }}"
                                                        style="border: 2px solid #eee; border-radius: 12px; padding: 12px; cursor: pointer; transition: all 0.2s ease; background: #fff; position: relative; box-shadow: 0 2px 8px rgba(0,0,0,0.02); scroll-snap-align: start; flex-shrink: 0;">
                                                        <div
                                                            style="display: flex; justify-content: space-between; align-items: flex-start;">
                                                            <span
                                                                style="font-size: 9px; font-weight: 800; text-transform: uppercase; color: var(--pink-dark); background: #fff0f3; padding: 2px 8px; border-radius: 4px;">{{ $addr->label }}</span>
                                                            <i class="fa-solid fa-circle-check check-icon"
                                                                style="color: #2ecc71; display: none;"></i>
                                                        </div>
                                                        <div
                                                            style="font-weight: 700; font-size: 13px; color: #111; margin-top: 10px;">
                                                            {{ $addr->recipient_name ?? Auth::user()->name }}</div>
                                                        <div
                                                            style="font-size: 11px; color: #666; margin-top: 4px; line-height: 1.4; height: 32px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                            {{ $addr->address1 }}
                                                        </div>
                                                        <div
                                                            style="font-size: 11px; font-weight: 600; color: #333; margin-top: 4px;">
                                                            {{ $addr->city }}, {{ $addr->zip }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @php
                                $indianStates = [
                                    'Andhra Pradesh', 'Arunachal Pradesh', 'Assam', 'Bihar', 'Chhattisgarh',
                                    'Goa', 'Gujarat', 'Haryana', 'Himachal Pradesh', 'Jharkhand', 'Karnataka',
                                    'Kerala', 'Madhya Pradesh', 'Maharashtra', 'Manipur', 'Meghalaya', 'Mizoram',
                                    'Nagaland', 'Odisha', 'Punjab', 'Rajasthan', 'Sikkim', 'Tamil Nadu',
                                    'Telangana', 'Tripura', 'Uttar Pradesh', 'Uttarakhand', 'West Bengal',
                                    'Andaman and Nicobar Islands', 'Chandigarh',
                                    'Dadra and Nagar Haveli and Daman and Diu',
                                    'Delhi', 'Jammu and Kashmir', 'Ladakh', 'Lakshadweep', 'Puducherry'
                                ];
                            @endphp

                            <div id="checkoutAddressForm">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">FULL NAME</label>
                                        <input type="text" name="customer_name" id="field_name" placeholder="Full Name" class="form-input-v4" value="{{ Auth::user()?->name }}" required
                                            data-msg-required="Please enter full name.">
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PHONE NUMBER</label>
                                        <input type="tel" name="customer_phone" id="field_phone" placeholder="Phone Number" class="form-input-v4" value="{{ Auth::user()?->phone }}" required minlength="10" maxlength="10" data-rule-digits="true"
                                            data-msg-required="Please enter phone number."
                                            data-msg-digits="Please enter a valid 10-digit phone number."
                                            data-msg-minlength="Please enter a valid 10-digit phone number."
                                            data-msg-maxlength="Please enter a valid 10-digit phone number.">
                                    </div>
                                    <div class="form-group" style="grid-column: span 2;">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">DELIVERY ADDRESS</label>
                                        <input type="text" name="delivery_address" id="field_address" placeholder="Flat No, Street, Area" class="form-input-v4" required
                                            data-msg-required="Please enter delivery address.">
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">CITY</label>
                                        <input type="text" name="city" id="field_city" placeholder="City" class="form-input-v4" required
                                            oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                            data-msg-required="Please enter city.">
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">STATE</label>
                                        <select name="state" id="field_state" class="form-input-v4" required
                                            data-msg-required="Please select state.">
                                            <option value="">— Select State —</option>
                                            @foreach($indianStates as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PINCODE</label>
                                        <input type="text" name="pincode" id="field_zip" placeholder="Pincode" class="form-input-v4" 
                                            value="{{ old('pincode', session('checked_pincode')) }}" required minlength="6" maxlength="6" data-rule-digits="true"
                                            inputmode="numeric"
                                            oninput="checkPincodeAvailability(this.value)"
                                            data-msg-required="Please enter pincode."
                                            data-msg-digits="Please enter a valid 6-digit pincode."
                                            data-msg-minlength="Please enter a valid 6-digit pincode."
                                            data-msg-maxlength="Please enter a valid 6-digit pincode.">
                                        <div id="pincodeAvailabilityMsg" style="font-size: 11px; margin-top: 5px; min-height: 15px;">
                                            @if(session('checked_pincode') && session('checked_pincode_edd'))
                                                <span style="color: #27ae60; font-weight: 600;"><i class="fas fa-check-circle"></i> Estimated delivery by {{ session('checked_pincode_edd') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">COUNTRY</label>
                                        <input type="text" name="country" id="field_country" placeholder="Country" class="form-input-v4" value="India" required
                                            oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                            data-msg-required="Please enter country.">
                                    </div>
                                </div>
                                @if(Auth::check())
                                <div style="margin-top: 10px;">
                                    <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #444; font-weight: 600; user-select: none;">
                                        <input type="checkbox" name="save_address" value="1" 
                                            style="accent-color: var(--pink-dark); width: 16px; height: 16px; cursor: pointer;">
                                        Save this address for future use
                                    </label>
                                </div>
                                @endif
                            </div>
                        </div>       <div class="card-v4" style="margin-bottom: 25px;">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                                <h2 style="font-size: 20px; font-weight: 700; color: var(--pink-dark); margin: 0;">Billing
                                    Address</h2>
                                <label
                                    style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-size: 13px; color: #444; font-weight: 600; user-select: none;">
                                    <input type="checkbox" id="sameAsShipping" name="same_as_shipping" value="1" checked
                                        style="accent-color: var(--pink-dark); width: 16px; height: 16px; cursor: pointer;"
                                        onchange="toggleBillingForm(this)">
                                    Same as Shipping Address
                                </label>
                            </div>

                            <div id="billingAddressSummary" style="font-size: 13px; color: #888; font-style: italic;">
                                <i class="fa-solid fa-circle-check" style="color: #2ecc71; margin-right: 6px;"></i>
                                Billing address is same as your shipping address.
                            </div>

                            <div id="billingAddressForm" style="display: none;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">FULL
                                            NAME</label>
                                        <input type="text" name="billing_name" id="billing_name" placeholder="Billing Name"
                                            class="form-input-v4">
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PHONE
                                            NUMBER</label>
                                        <input type="tel" name="billing_phone" id="billing_phone"
                                            placeholder="Billing Phone" class="form-input-v4" minlength="10" maxlength="10"
                                            data-rule-digits="true" inputmode="numeric"
                                            data-msg-digits="Please enter a valid 10-digit billing phone number."
                                            data-msg-minlength="Please enter a valid 10-digit billing phone number."
                                            data-msg-maxlength="Please enter a valid 10-digit billing phone number.">
                                    </div>
                                    <div class="form-group" style="grid-column: span 2;">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">BILLING
                                            ADDRESS</label>
                                        <input type="text" name="billing_address" id="billing_address_field"
                                            placeholder="Flat No, Street, Area" class="form-input-v4">
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">CITY</label>
                                        <input type="text" name="billing_city" id="billing_city" placeholder="City"
                                            class="form-input-v4"
                                            oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')">
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">STATE</label>
                                        <select name="billing_state" id="billing_state" class="form-input-v4">
                                            <option value="">— Select State —</option>
                                            @foreach($indianStates as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">PINCODE</label>
                                        <input type="text" name="billing_pincode" id="billing_pincode" placeholder="Pincode"
                                            class="form-input-v4" minlength="6" maxlength="6" data-rule-digits="true"
                                            inputmode="numeric"
                                            data-msg-digits="Please enter a valid 6-digit billing pincode."
                                            data-msg-minlength="Please enter a valid 6-digit billing pincode."
                                            data-msg-maxlength="Please enter a valid 6-digit billing pincode.">
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">COUNTRY</label>
                                        <input type="text" name="billing_country" id="billing_country" placeholder="Country"
                                            class="form-input-v4" value="India"
                                            oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')">
                                    </div>
                                    <div class="form-group">
                                        <label
                                            style="font-size: 11px; font-weight: 700; color: #666; margin-bottom: 5px; display: block;">EMAIL</label>
                                        <input type="email" name="billing_email" id="billing_email"
                                            placeholder="Billing Email" class="form-input-v4"
                                            value="{{ Auth::user()?->email }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- SIDEBAR --}}
                    <aside class="checkout-sidebar">
                        <div class="summary-card-v4" style="position: sticky; top: 100px;">
                            <h3 class="summary-title-v4"
                                style="font-size: 18px; display: flex; align-items: center; gap: 10px;">
                                <i class="fa-solid fa-receipt" style="color: #A91B43;"></i>
                                Order Summary
                            </h3>

                            {{-- Items --}}
                            <div style="margin-bottom: 20px; max-height: 180px; overflow-y: auto; padding-right: 5px;">
                                @foreach($items as $item)
                                    <div style="display: flex; gap: 10px; margin-bottom: 12px; align-items: center;">
                                        <img src="{{ $item['image_url'] }}"
                                            style="width: 34px; height: 44px; object-fit: cover; border-radius: 6px; flex-shrink: 0;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div
                                                style="font-size: 13px; font-weight: 600; color: #333; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                                {{ $item['name'] }}</div>
                                            <div style="font-size: 11px; color: #888;">
                                                Qty: {{ $item['quantity'] }}
                                                @if(!empty($item['display_attributes']))
                                                    @foreach($item['display_attributes'] as $attr)
                                                        · {{ $attr['name'] }}: {{ $attr['value'] }}
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        <div style="font-size: 13px; font-weight: 700; color: #333; flex-shrink: 0;">
                                            &#8377;{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div style="border-top: 1px dashed #eee; padding-top: 16px; margin-bottom: 4px;">
                                <div class="summary-row-v4">
                                    <span>Subtotal</span>
                                    <span>&#8377;{{ number_format($subTotal, 2) }}</span>
                                </div>
                                <div class="summary-row-v4">
                                    <span>Delivery Charges</span>
                                    <span id="shipping_cost_display"
                                        style="{{ $shipping > 0 ? '' : 'color: #2ecc71; font-weight: 700;' }} font-size: 12px;">
                                        {{ $shipping > 0 ? '₹' . number_format($shipping, 2) : 'FREE' }}
                                    </span>
                                </div>
                                <div class="summary-row-v4">
                                    <span>Tax (<span id="tax_rate_label">{{ $taxPercentage ?? 5 }}</span>%)</span>
                                    <span id="tax_cost_display">&#8377;{{ number_format($tax, 2) }}</span>
                                </div>
                                @if($discount > 0)
                                    <div class="summary-row-v4" style="color: #2ecc71; font-weight: 600;">
                                        <span>Discount</span>
                                        <span>-&#8377;{{ number_format($discount, 2) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="grand-total-v4"
                                style="font-size: 20px; margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee;">
                                <span>Total</span>
                                <span id="grand_total_display">&#8377;{{ number_format($grandTotal, 2) }}</span>
                            </div>

                            {{-- PAYMENT METHOD --}}
                            <div style="margin-top: 25px; display: flex; flex-direction: column; gap: 10px;"
                                id="paymentOptions">
                                <label
                                    style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #A91B43; border-radius: 12px; transition: all 0.2s ease; background: #fffcfd;"
                                    onclick="selectPayment('razorpay', this)">
                                    <input type="radio" name="pay_choice_radio" checked style="accent-color: #A91B43;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 13px; font-weight: 700; color: #333;">Secure Payment</div>
                                        <div style="font-size: 11px; color: #888;">UPI, Cards, NetBanking</div>
                                    </div>
                                    <i class="fa-solid fa-shield-halved" style="color: #2e7d32; font-size: 14px;"></i>
                                </label>

                                <label
                                    style="cursor: pointer; display: flex; align-items: center; gap: 12px; padding: 12px; border: 1px solid #f0f0f0; border-radius: 12px; transition: all 0.2s ease; background: #fff;"
                                    onclick="selectPayment('cod', this)">
                                    <input type="radio" name="pay_choice_radio" style="accent-color: #A91B43;">
                                    <div style="flex: 1;">
                                        <div style="font-size: 13px; font-weight: 700; color: #333;">Cash on Delivery</div>
                                        <div style="font-size: 11px; color: #888;">Pay when you receive</div>
                                    </div>
                                    <i class="fa-solid fa-truck-fast" style="color: #ef6c00; font-size: 14px;"></i>
                                </label>
                            </div>

                            <button type="submit" class="btn-review-v4"
                                style="width: 100%; margin-top: 20px; height: 50px; font-size: 15px; letter-spacing: 0.5px; border-radius: 12px;">
                                Place Order
                            </button>

                            <div
                                style="text-align: center; margin-top: 15px; display: flex; align-items: center; justify-content: center; gap: 8px; opacity: 0.5;">
                                <img src="https://razorpay.com/favicon.png" width="14">
                                <span
                                    style="font-size: 11px; font-weight: 700; color: #666; text-transform: uppercase; letter-spacing: 1px;">Razorpay
                                    Secured</span>
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
    function applyBillingValidation() {
        if (!window.jQuery) return;
        const $checkoutForm = $('#singleCheckoutForm');
        if (!$checkoutForm.data('validator')) return;

        const shouldRequireBilling = !document.getElementById('sameAsShipping').checked;
        const $billingFields = $('#billing_name, #billing_phone, #billing_address_field, #billing_city, #billing_state, #billing_pincode, #billing_country, #billing_email');

        if (shouldRequireBilling) {
            $('#billing_name').rules('add', { required: true, messages: { required: 'Please enter billing name.' } });
            $('#billing_phone').rules('add', { required: true, digits: true, minlength: 10, maxlength: 10, messages: { required: 'Please enter billing phone number.' } });
            $('#billing_address_field').rules('add', { required: true, messages: { required: 'Please enter billing address.' } });
            $('#billing_city').rules('add', { required: true, messages: { required: 'Please enter billing city.' } });
            $('#billing_state').rules('add', { required: true, messages: { required: 'Please select billing state.' } });
            $('#billing_pincode').rules('add', { required: true, digits: true, minlength: 6, maxlength: 6, messages: { required: 'Please enter billing pincode.' } });
            $('#billing_country').rules('add', { required: true, messages: { required: 'Please enter billing country.' } });
            $('#billing_email').rules('add', { required: true, email: true, messages: { required: 'Please enter billing email.' } });
        } else {
            $billingFields.each(function() {
                const $field = $(this);
                $field.rules('remove', 'required');
                $field.removeClass('error-border');
                const $error = $field.siblings('span.error-text');
                if ($error.length) $error.remove();
            });
        }
    }

    function toggleBillingForm(checkbox) {
        const form = document.getElementById('billingAddressForm');
        const summary = document.getElementById('billingAddressSummary');
        if (checkbox.checked) {
            form.style.display = 'none';
            summary.style.display = 'block';
        } else {
            form.style.display = 'block';
            summary.style.display = 'none';
        }
        applyBillingValidation();
    }

    function selectPayment(method, element) {
        document.getElementById('payment_method_input').value = method;
        document.querySelectorAll('#paymentOptions label').forEach(l => {
            l.style.border = '1px solid #f0f0f0';
            l.style.background = '#fff';
            l.querySelector('input').checked = false;
        });
        element.style.border = '1px solid #A91B43';
        element.style.background = '#fffcfd';
        element.querySelector('input').checked = true;
    }

    function toggleSavedAddresses(checkbox) {
        const section = document.getElementById('savedAddressesSection');
        section.style.display = checkbox.checked ? 'block' : 'none';
    }

    function selectSavedAddress(element) {
        document.querySelectorAll('.saved-address-card').forEach(c => {
            c.style.borderColor = '#eee';
            c.querySelector('.check-icon').style.display = 'none';
        });
        element.style.borderColor = 'var(--pink-dark)';
        element.querySelector('.check-icon').style.display = 'block';

        document.getElementById('field_name').value = element.getAttribute('data-name');
        document.getElementById('field_phone').value = element.getAttribute('data-phone');
        document.getElementById('field_address').value = element.getAttribute('data-addr');
        document.getElementById('field_city').value = element.getAttribute('data-city');
        
        const stateSelect = document.getElementById('field_state');
        stateSelect.value = element.getAttribute('data-state');
        const countryInput = document.getElementById('field_country');
        if (countryInput) countryInput.value = element.getAttribute('data-country') || 'India';
        
        document.getElementById('field_zip').value = element.getAttribute('data-zip');
        
        if (element.getAttribute('data-zip')) {
            checkPincodeAvailability(element.getAttribute('data-zip'));
        }
        updateShipping();
    }

    function updateShipping() {
        const state = document.getElementById('field_state').value;
        const zip = document.getElementById('field_zip').value;
        if (!state && !zip) return;

        fetch("{{ route('cart.shipping.update') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ state: state, zip: zip, country: document.getElementById('field_country')?.value || 'India' })
        })
        .then(r => {
            if (r.status === 419) {
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
            return r.json();
        })
        .then(response => {
            if (response.success) {
                document.getElementById('shipping_cost_display').textContent = response.shippingFormatted;
                document.getElementById('tax_cost_display').textContent = response.taxFormatted;
                document.getElementById('grand_total_display').textContent = response.grandTotalFormatted;

                if (response.taxPercentage !== undefined) {
                    const taxLabel = document.getElementById('tax_rate_label');
                    if (taxLabel) taxLabel.textContent = response.taxPercentage;
                }

                const shippingEl = document.getElementById('shipping_cost_display');
                if (response.shipping > 0) {
                    shippingEl.style.color = '';
                    shippingEl.style.fontWeight = '';
                } else {
                    shippingEl.style.color = '#2ecc71';
                    shippingEl.style.fontWeight = '700';
                }
            }
        })
        .catch(error => {
            if (error.message !== 'CSRF token mismatch') {
                console.error('Shipping update error:', error);
            }
        });
    }

    let isPincodeServiceable = true; // Default to true so it doesn't block before checking

    let pinTimeout = null;
    function checkPincodeAvailability(val) {
        const msgEl = document.getElementById('pincodeAvailabilityMsg');
        if (val && val.length === 6 && !isNaN(val)) {
            if (pinTimeout) clearTimeout(pinTimeout);
            
            pinTimeout = setTimeout(() => {
                if(msgEl) msgEl.innerHTML = '<span style="color: #666;"><i class="fas fa-spinner fa-spin"></i> Checking availability...</span>';
                
                fetch("{{ route('check-serviceability') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ pincode: val })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        isPincodeServiceable = true;
                        if(msgEl) msgEl.innerHTML = `<span style="color: #27ae60; font-weight: 600;"><i class="fas fa-check-circle"></i> Estimated delivery by ${data.edd}</span>`;
                        updateShipping();
                    } else {
                        isPincodeServiceable = false;
                        if(msgEl) msgEl.innerHTML = `<span style="color: #e74c3c; font-weight: 600;"><i class="fas fa-times-circle"></i> ${data.message}</span>`;
                    }
                })
                .catch(err => {
                    isPincodeServiceable = true; // Recoverable? Default back to true but log it
                    if(msgEl) msgEl.innerHTML = '';
                    console.error('Pincode check error:', err);
                });
            }, 500); 
        } else {
            isPincodeServiceable = true;
            if(msgEl) msgEl.innerHTML = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        applyBillingValidation();
        
        const zipField = document.getElementById('field_zip');
        if (zipField && zipField.value) {
            checkPincodeAvailability(zipField.value);
        }

        document.getElementById('field_state')?.addEventListener('change', updateShipping);
        document.getElementById('field_zip')?.addEventListener('input', function() {
            checkPincodeAvailability(this.value);
        });
        document.getElementById('field_zip')?.addEventListener('change', function() {
            updateShipping();
        });

        document.getElementById('singleCheckoutForm')?.addEventListener('submit', function(e) {
            if (!isPincodeServiceable) {
                e.preventDefault();
                Swal.fire({
                    title: 'Delivery Not Available',
                    text: 'Please enter a serviceable pincode to proceed with your order.',
                    icon: 'warning',
                    confirmButtonColor: '#A91B43'
                });
                return false;
            }

            const addr = document.getElementById('field_address').value.trim();
            const city = document.getElementById('field_city').value.trim();
            const state = document.getElementById('field_state').value.trim();
            const zip = document.getElementById('field_zip').value.trim();

            if (addr && city && !addr.includes(city)) {
                document.getElementById('field_address').value = `${addr}, ${city}, ${state} - ${zip}`;
            }
        });
    });
    </script>
@endpush
