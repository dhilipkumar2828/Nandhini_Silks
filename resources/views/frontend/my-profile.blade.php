@extends('frontend.layouts.app')

@section('title', 'My Profile | Nandhini Silks')

@push('styles')
<style>
    .profile-card {
        background: #fff;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        border: 1px solid #f0f0f0;
        margin-bottom: 30px;
    }

    .profile-header-edit {
        display: flex;
        align-items: center;
        gap: 30px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }

    .profile-pic-container {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .profile-pic {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .edit-pic-btn {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 32px;
        height: 32px;
        background: var(--pink);
        color: #fff;
        border-radius: 50%;
        display: grid;
        place-items: center;
        border: 2px solid #fff;
        cursor: pointer;
        font-size: 14px;
        transition: transform 0.2s ease;
    }

    .edit-pic-btn:hover {
        transform: scale(1.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #333;
    }

    .form-control {
        padding: 12px 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--pink);
        outline: none;
    }

    .verify-badge {
        font-size: 11px;
        background: #f6ffed;
        color: #52c41a;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 700;
        margin-left: 10px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .btn-save {
        padding: 12px 30px;
        background: var(--pink);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        cursor: pointer;
        transition: opacity 0.3s ease;
    }

    .btn-save:hover {
        opacity: 0.9;
    }

    .danger-zone {
        margin-top: 50px;
        padding-top: 30px;
        border-top: 1px solid #f5f5f5;
    }

    .btn-delete {
        color: #f5222d;
        background: none;
        border: none;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: underline;
    }

    @media (max-width: 600px) {
        .form-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp; <span>My Profile</span>
            </div>

            <div class="account-layout">
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">John Doe</h2>
                        <p class="account-user-email">john.doe@example.com</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link active"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('login') }}" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Profile</h1>
                    </div>

                    <form class="profile-card" onsubmit="event.preventDefault()">
                        <div class="profile-header-edit">
                            <div class="profile-pic-container">
                                <img src="{{ asset('images/user-avatar.svg') }}" alt="John Doe" class="profile-pic">
                                <div class="edit-pic-btn">&#128247;</div>
                            </div>
                            <div>
                                <h3 style="margin-bottom: 5px;">John Doe</h3>
                                <p style="color: #999; font-size: 13px;">Manage your personal information and security.</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" value="John Doe">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="verify-badge">&#10003; Verified</span></label>
                                <input type="email" class="form-control" value="john.doe@example.com">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="verify-badge">&#10003; Verified</span></label>
                                <input type="tel" class="form-control" value="+91 98765 43210">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select class="form-control">
                                    <option selected>Male</option>
                                    <option>Female</option>
                                    <option>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" value="1990-05-15">
                            </div>
                        </div>

                        <h3 class="info-title" style="margin-top: 40px;">Change Password</h3>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Current Password</label>
                                <input type="password" class="form-control" placeholder="********">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">New Password</label>
                                <input type="password" class="form-control" placeholder="Enter new password">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" placeholder="Confirm new password">
                            </div>
                        </div>

                        <div style="margin-top: 40px;">
                            <button type="submit" class="btn-save">Save Changes</button>
                        </div>

                        <div class="danger-zone">
                            <h4 style="color: #333; margin-bottom: 10px;">Account Security</h4>
                            <p style="color: #999; font-size: 13px; margin-bottom: 20px;">Once you delete your account, there is no going back. Please be certain.</p>
                            <button type="button" class="btn-delete">Delete Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
