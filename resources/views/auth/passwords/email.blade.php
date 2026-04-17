@extends('frontend.layouts.app')

@section('title', 'Forgot Password | Nandhini Silks')

@section('content')
<main class="auth-page">
    <div class="auth-container">
        <div class="auth-form-side">
            <div class="auth-header">
                <h1 class="auth-title">Reset Password</h1>
                <p class="auth-subtitle">Enter your email to receive a password reset link</p>
            </div>

            <div class="auth-tabs">
                @if(session('status'))
                    <div class="alert" style="color: #10b981; background: #d1fae5; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; font-weight: bold;">
                        <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
                    </div>
                @endif
                
                @if($errors->any())
                    <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                        @foreach($errors->all() as $error)
                            <div style="font-weight: bold;"><i class="fas fa-times-circle mr-1"></i> {{ $error }}</div>
                        @endforeach
                    </div>
                @endif
            </div>

            <form class="auth-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input class="form-input" type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your registered email" required autofocus>
                </div>

                <button type="submit" class="auth-submit">Send Reset Link</button>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="{{ route('login') }}" style="color: #a91b43; font-weight: bold; text-decoration: none;">Back to Login</a>
                </div>
            </form>
        </div>
    </div>
</main>
@endsection
