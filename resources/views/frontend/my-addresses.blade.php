@extends('frontend.layouts.app')

@section('title', 'My Addresses | Nandhini Silks')

@section('content')
    <style>
        .address-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        @media (max-width: 500px) {
            .address-form-grid {
                grid-template-columns: 1fr !important;
                gap: 15px !important;
            }

            .modal-content-inner {
                padding: 25px 15px !important;
            }

            .modal-content h2 {
                font-size: 20px !important;
            }
        }
    </style>
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a>
                &nbsp; /
                &nbsp; <span>Addresses</span>
            </div>

            <div class="account-layout">
                <!-- Sidebar -->
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ optional(Auth::user())->profile_picture ? asset('uploads/' . optional(Auth::user())->profile_picture) : asset('images/user-avatar.svg') }}"
                                alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ Auth::user() ? Auth::user()->name : 'Guest User' }}</h2>
                        <p class="account-user-email">{{ Auth::user() ? Auth::user()->email : '' }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}"
                                class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My
                                    Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My
                                    Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}"
                                class="account-nav-link active"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My
                                    Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}"
                                class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)"
                                onclick="document.getElementById('logout-form').submit()"
                                class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <!-- Addresses Content -->
                <div class="account-content">
                    <div class="section-header"
                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">Saved Addresses</h1>
                    </div>

                    <div class="address-grid" id="addressGrid">
                        @foreach($addresses as $addr)
                            <div class="address-card-v3 {{ $addr->is_default ? 'default' : '' }}" id="addr-{{ $addr->id }}">
                                @if($addr->is_default)<span class="default-badge-v3">Default</span>@endif
                                <h3 class="address-name-v3">{{ $addr->recipient_name ?? optional(Auth::user())->name }}
                                    ({{ $addr->label }})</h3>
                                <div class="address-details-v3">
                                    <span class="addr-street">{{ $addr->address1 }}</span><br>

                                    <span class="addr-city-state">{{ $addr->city }}, {{ $addr->state }} -
                                        {{ $addr->zip }}</span><br>
                                    <span class="addr-country">{{ $addr->country }}</span><br>
                                    Phone: <span
                                        class="addr-phone">{{ $addr->recipient_phone ?? optional(Auth::user())->phone }}</span>
                                </div>
                                <div style="display: flex; gap: 10px; flex-wrap: wrap; margin-top: 16px;">
                                    <button type="button" onclick="openEditAddressModal({
                                                                                                id: {{ $addr->id }},
                                                                                                label: @js($addr->label),
                                                                                                address1: @js($addr->address1),
                                                                                                city: @js($addr->city),
                                                                                                state: @js($addr->state),
                                                                                                zip: @js($addr->zip),
                                                                                                country: @js($addr->country ?? 'India'),
                                                                                                recipient_name: @js($addr->recipient_name ?? optional(Auth::user())->name),
                                                                                                recipient_phone: @js($addr->recipient_phone ?? optional(Auth::user())->phone)
                                                                                            })"
                                        style="padding: 10px 16px; border-radius: 10px; border: 1px solid #940437; background: #fff; color: #940437; font-size: 13px; font-weight: 700; cursor: pointer;">
                                        Edit Address
                                    </button>
                                    <form id="delete-address-form-{{ $addr->id }}"
                                        action="{{ route('addresses.destroy', $addr) }}" method="POST" style="margin: 0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDeleteAddress({{ $addr->id }})"
                                            style="padding: 10px 16px; border-radius: 10px; border: 1px solid #d92d20; background: #fff; color: #d92d20; font-size: 13px; font-weight: 700; cursor: pointer;">
                                            Delete Address
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach

                        <!-- Add New Address Button -->
                        <div class="btn-add-address" id="addAddressBtn" style="cursor: pointer;"
                            onclick="openAddressModal()">
                            <span style="font-size: 24px;">+</span>
                            <span>Add New Address</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Modal -->
        <div id="addressModal" class="address-modal"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
            <div class="modal-content"
                style="background: #fff; border-radius: 20px; width: 600px; max-width: 90%; max-height: 90vh; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.15); overflow: hidden; display: flex; flex-direction: column;">
                <button onclick="closeAddressModal()"
                    style="position: absolute; right: 25px; top: 25px; background: #fff; border: none; font-size: 24px; cursor: pointer; color: #999; z-index: 100; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">&times;</button>

                <div class="modal-content-inner" style="padding: 40px; overflow-y: auto; flex: 1;">
                    <h2 id="addressModalTitle"
                        style="margin-top: 0; font-size: 24px; color: #333; margin-bottom: 8px; font-weight: 700;">Add New
                        Address</h2>
                    <p id="addressModalSubtitle" style="color: #999; font-size: 14px; margin-bottom: 30px; margin-top: 0;">
                        Items
                        will be delivered to this address.</p>

                    <form id="addressForm" action="{{ route('addresses.store') }}" method="POST" class="validate-form"
                        novalidate>
                        @csrf
                        <input type="hidden" name="_method" id="addressFormMethod" value="POST">
                        <div class="address-form-grid">
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Address
                                    Label</label>
                                <input type="text" id="address_label" name="label" required
                                    oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    placeholder="e.g. Home, Office" data-msg-required="Please enter an address label.">
                            </div>
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Recipient
                                    Full Name</label>
                                <input type="text" id="address_recipient_name" name="recipient_name" required
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    placeholder="Full Name">
                            </div>
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Phone
                                    Number</label>
                                <input type="tel" id="address_phone" name="recipient_phone"
                                    value="{{ optional(Auth::user())->phone }}" required minlength="10" maxlength="10"
                                    data-rule-digits="true" inputmode="numeric"
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    data-msg-required="Please enter mobile number."
                                    data-msg-digits="Please enter a valid 10-digit mobile number."
                                    data-msg-minlength="Please enter a valid 10-digit mobile number."
                                    data-msg-maxlength="Please enter a valid 10-digit mobile number.">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 20px;">
                            <label
                                style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Street
                                Address / House No.</label>
                            <input type="text" id="address_address1" name="address1" required minlength="5"
                                style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                placeholder="Door No, Street name" data-msg-required="Please enter your street address."
                                data-msg-minlength="Address must be at least 5 characters.">
                        </div>

                        <div class="address-form-grid">
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">City</label>
                                <input type="text" id="address_city" name="city" required
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                    data-msg-required="Please enter city.">
                            </div>
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">State</label>
                                <input type="text" id="address_state" name="state" required
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                    data-msg-required="Please enter state.">
                            </div>
                        </div>

                        <div class="address-form-grid" style="margin-bottom: 30px;">
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Pincode</label>
                                <input type="text" id="address_zip" name="zip" required minlength="6" maxlength="6"
                                    data-rule-digits="true" inputmode="numeric"
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    data-msg-required="Please enter pincode."
                                    data-msg-digits="Please enter a valid 6-digit pincode."
                                    data-msg-minlength="Please enter a valid 6-digit pincode."
                                    data-msg-maxlength="Please enter a valid 6-digit pincode.">
                            </div>
                            <div class="form-group">
                                <label
                                    style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Country</label>
                                <input type="text" id="address_country" name="country" value="India" required
                                    style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;"
                                    oninput="this.value=this.value.replace(/[^A-Za-z\\s]/g,'')"
                                    data-msg-required="Please enter country.">
                            </div>
                        </div>

                        <div style="display: flex; gap: 15px; margin-top: 20px;">
                            <button type="submit" id="addressSubmitButton"
                                style="flex: 1; background: #940437; color: #fff; border: none; padding: 15px; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; transition: all 0.3s ease;">Save
                                Address Details</button>
                            <button type="button" onclick="closeAddressModal()"
                                style="background: #f8f9fa; color: #666; border: 1px solid #e0e0e0; padding: 15px 25px; border-radius: 12px; font-weight: 600; font-size: 16px; cursor: pointer;">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
        const addressModal = document.getElementById('addressModal');
        const addressForm = document.getElementById('addressForm');
        const addressFormMethod = document.getElementById('addressFormMethod');
        const addressModalTitle = document.getElementById('addressModalTitle');
        const addressModalSubtitle = document.getElementById('addressModalSubtitle');
        const addressSubmitButton = document.getElementById('addressSubmitButton');
        const addressFields = {
            label: document.getElementById('address_label'),
            phone: document.getElementById('address_phone'),
            address1: document.getElementById('address_address1'),
            city: document.getElementById('address_city'),
            state: document.getElementById('address_state'),
            zip: document.getElementById('address_zip'),
            country: document.getElementById('address_country'),
            recipient_name: document.getElementById('address_recipient_name'),
        };

        function clearAddressValidation() {
            if (!window.jQuery) return;
            const $form = $('#addressForm');
            $form.find('.error-text').remove();
            $form.find('.error-border').removeClass('error-border');
            if ($form.data('validator')) {
                $form.validate().resetForm();
            }
        }

        function resetAddressForm() {
            addressForm.action = `{{ route('addresses.store') }}`;
            addressFormMethod.value = 'POST';
            addressModalTitle.textContent = 'Add New Address';
            addressModalSubtitle.textContent = 'Items will be delivered to this address.';
            addressSubmitButton.textContent = 'Save';
            addressFields.label.value = '';
            addressFields.phone.value = @js(optional(Auth::user())->phone ?? '');
            addressFields.address1.value = '';
            addressFields.city.value = '';
            addressFields.state.value = '';
            addressFields.zip.value = '';
            addressFields.country.value = 'India';
            addressFields.recipient_name.value = @js(optional(Auth::user())->name ?? '');
            clearAddressValidation();
        }

        function openAddressModal() {
            resetAddressForm();
            addressModal.style.display = 'flex';
        }

        function openEditAddressModal(address) {
            addressForm.action = "{{ url('addresses') }}/" + address.id;
            addressFormMethod.value = 'PUT';
            addressModalTitle.textContent = 'Edit Address';
            addressModalSubtitle.textContent = 'Update your saved address details.';
            addressSubmitButton.textContent = 'Update';
            addressFields.label.value = address.label || '';
            addressFields.phone.value = address.recipient_phone || '';
            addressFields.address1.value = address.address1 || '';
            addressFields.city.value = address.city || '';
            addressFields.state.value = address.state || '';
            addressFields.zip.value = address.zip || '';
            addressFields.country.value = address.country || 'India';
            addressFields.recipient_name.value = address.recipient_name || '';
            clearAddressValidation();
            addressModal.style.display = 'flex';
        }

        function closeAddressModal() {
            addressModal.style.display = 'none';
            clearAddressValidation();
        }

        window.onclick = function (event) {
            if (event.target == addressModal) {
                closeAddressModal();
            }
        }

        function confirmDeleteAddress(addressId) {
            Swal.fire({
                title: 'Delete this address?',
                text: "This saved address will be removed from your account.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d92d20',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                borderRadius: '15px'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-address-form-' + addressId).submit();
                }
            });
        }
    </script>
@endsection