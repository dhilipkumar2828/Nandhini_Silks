@extends('frontend.layouts.app')

@section('title', 'Set New Password | Nandhini Silks')

@section('content')
<main class="auth-page">
    <div class="auth-container">
        <div class="auth-form-side">
            <div class="auth-header">
                <h1 class="auth-title">Set New Password</h1>
                <p class="auth-subtitle">Create a new secure password for your account</p>
            </div>

            <div class="auth-tabs">
                @if($errors->any())
                    <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        @foreach($errors->all() as $error)
                            <div style="font-weight: bold;"><i class="fas fa-times-circle mr-1"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </div>

            <form class="auth-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input" type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input class="form-input" type="password" id="password" name="password" placeholder="Enter new password" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password-confirm">Confirm New Password</label>
                    <input class="form-input" type="password" id="password-confirm" name="password_confirmation" placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="auth-submit">Reset Password</button>
            </form>
        </div>
    </div>
</main>
@endsection
