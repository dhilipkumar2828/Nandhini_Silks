@extends('frontend.layouts.app')

@section('title', 'My Addresses | Nandhini Silks')

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; /
                &nbsp; <span>Addresses</span>
            </div>

            <div class="account-layout">
                <!-- Sidebar -->
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ Auth::user() ? Auth::user()->name : 'Guest User' }}</h2>
                        <p class="account-user-email">{{ Auth::user() ? Auth::user()->email : '' }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link active"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <!-- Addresses Content -->
                <div class="account-content">
                    <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">Saved Addresses</h1>
                    </div>

                    <div class="address-grid" id="addressGrid">
                        @foreach($addresses as $addr)
                        <div class="address-card-v3 {{ $addr->is_default ? 'default' : '' }}" id="addr-{{ $addr->id }}">
                            @if($addr->is_default)<span class="default-badge-v3">Default</span>@endif
                            <h3 class="address-name-v3">{{ optional(Auth::user())->name }} ({{ $addr->label }})</h3>
                            <div class="address-details-v3">
                                <span class="addr-street">{{ $addr->address1 }}</span><br>
                                @if($addr->address2)<span class="addr-street">{{ $addr->address2 }}</span><br>@endif
                                <span class="addr-city-state">{{ $addr->city }}, {{ $addr->state }} - {{ $addr->zip }}</span><br>
                                <span class="addr-country">{{ $addr->country }}</span><br>
                                Phone: <span class="addr-phone">{{ optional(Auth::user())->phone }}</span>
                            </div>
                        </div>
                        @endforeach

                        <!-- Add New Address Button -->
                        <div class="btn-add-address" id="addAddressBtn" style="cursor: pointer;" onclick="openAddressModal()">
                            <span style="font-size: 24px;">+</span>
                            <span>Add New Address</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address Modal -->
        <div id="addressModal" class="address-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; align-items: center; justify-content: center;">
            <div class="modal-content" style="background: #fff; padding: 40px; border-radius: 20px; width: 600px; max-width: 90%; position: relative; box-shadow: 0 10px 40px rgba(0,0,0,0.15);">
                <button onclick="closeAddressModal()" style="position: absolute; right: 25px; top: 25px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
                
                <h2 style="margin-top: 0; font-size: 24px; color: #333; margin-bottom: 8px; font-weight: 700;">Add New Address</h2>
                <p style="color: #999; font-size: 14px; margin-bottom: 30px; margin-top: 0;">Items will be delivered to this address.</p>
                
                <form action="{{ route('addresses.store') }}" method="POST">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Address Label</label>
                            <input type="text" name="label" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;" placeholder="e.g. Home, Office">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Phone Number</label>
                            <input type="tel" value="{{ optional(Auth::user())->phone }}" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px; background: #f9f9f9;">
                        </div>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Street Address / House No.</label>
                        <input type="text" name="address1" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;" placeholder="Door No, Street name">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">City</label>
                            <input type="text" name="city" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">State</label>
                            <input type="text" name="state" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px;">
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Pincode</label>
                            <input type="text" name="zip" required style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #333;">Country</label>
                            <input type="text" name="country" value="India" readonly style="width: 100%; padding: 12px 15px; border: 1px solid #e0e0e0; border-radius: 10px; font-size: 14px; background: #f9f9f9;">
                        </div>
                    </div>

                    <button type="submit" style="width: 100%; padding: 14px; background: #940437; color: #fff; border: none; border-radius: 12px; font-weight: 700; font-size: 16px; cursor: pointer; transition: background 0.3s;">Save Address Details</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        function openAddressModal() {
            document.getElementById('addressModal').style.display = 'flex';
        }

        function closeAddressModal() {
            document.getElementById('addressModal').style.display = 'none';
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addressModal');
            if (event.target == modal) {
                closeAddressModal();
            }
        }
    </script>
@endsection
