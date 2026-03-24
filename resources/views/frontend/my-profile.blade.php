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
                            <img src="{{ $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ $user->name }}</h2>
                        <p class="account-user-email">{{ $user->email }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link active"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Profile</h1>
                    </div>

                    @if(session('success'))
                        <div style="background: #f6ffed; color: #52c41a; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #b7eb8f;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div style="background: #fff2f0; color: #f5222d; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffccc7;">
                            <ul style="margin: 0; padding-left: 20px;">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form class="profile-card validate-form" action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        <div class="profile-header-edit">
                            <div class="profile-pic-container">
                                <img src="{{ $user->profile_picture ? asset('uploads/'.$user->profile_picture) : asset('images/user-avatar.svg') }}" 
                                     alt="{{ $user->name }}" class="profile-pic" id="profilePicPreview">
                                <div class="edit-pic-btn" onclick="document.getElementById('profilePhotoInput').click()">&#128247;</div>
                                <input type="file" id="profilePhotoInput" style="display: none;" accept="image/*" onchange="uploadProfilePhoto(this)">
                            </div>
                            <div>
                                <h3 style="margin-bottom: 5px;">{{ $user->name }}</h3>
                                <p style="color: #999; font-size: 13px;">Manage your personal information and security.</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Email Address <span class="verify-badge">&#10003; Verified</span></label>
                                <input type="email" class="form-control" name="email" value="{{ $user->email }}" readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Phone Number <span class="verify-badge">&#10003; Verified</span></label>
                                <input type="tel" class="form-control" name="phone" value="{{ $user->phone ?? '' }}" placeholder="Enter phone number" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="Male" {{ ($user->gender == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ ($user->gender == 'Female') ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ ($user->gender == 'Other') ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" name="dob" value="{{ $user->dob ? $user->dob->format('Y-m-d') : '' }}">
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

@push('scripts')
    <script>
        function uploadProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('photo', input.files[0]);
                formData.append('_token', '{{ csrf_token() }}');

                // Visual feedback
                const btn = document.querySelector('.edit-pic-btn');
                const originalContent = btn.innerHTML;
                btn.innerHTML = '...';
                btn.style.pointerEvents = 'none';

                fetch('{{ route("profile.photo") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('profilePicPreview').src = data.url;
                        // Also update sidebar avatar and header icon if they exist
                        const sidebarAvatar = document.querySelector('.account-avatar img');
                        if (sidebarAvatar) sidebarAvatar.src = data.url;
                        const headerProfilePic = document.getElementById('headerProfilePic');
                        if (headerProfilePic) headerProfilePic.src = data.url;
                        
                        toastr.success('Profile picture updated successfully!');
                    } else {
                        toastr.error(data.message || 'Error updating profile picture.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while uploading.');
                })
                .finally(() => {
                    btn.innerHTML = originalContent;
                    btn.style.pointerEvents = 'auto';
                });
            }
        }
    </script>
@endpush
