@extends('frontend.layouts.app')

@section('title', 'Login & Register | Nandhini Silks')

@section('content')
    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-form-side">
                <div class="auth-header">
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Elevate your elegance with Nandhini Silks</p>
                </div>

                <div class="auth-tabs" id="authTabs">
                    @if(session('error'))
                        <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 12px; font-weight: bold;">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert" style="color: #10b981; background: #d1fae5; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 12px; font-weight: bold;">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif
                    
                    @php
                        $visibleErrors = collect($errors->all())
                            ->reject(fn ($error) => $error === 'The provided credentials do not match our records.');
                    @endphp

                    @if($visibleErrors->isNotEmpty())
                        <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 12px;">
                            <ul style="list-style: none; padding: 0; margin: 0;">
                                @foreach($visibleErrors as $error)
                                    <li style="font-weight: bold; margin-bottom: 2px;"><i class="fas fa-times-circle mr-1"></i> {{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <button class="auth-tab active" id="loginTab">Login</button>
                    <button class="auth-tab" id="registerTab">Register</button>
                </div>

                <!-- Login Form -->
                <form id="loginForm" class="auth-form validate-form" method="post" action="{{ route('login.submit') }}" novalidate>
                    @csrf
                    <div class="form-group" id="loginEmailGroup">
                        <label class="form-label" for="loginEmail">Email Address</label>
                        <input class="form-input" type="email" id="loginEmail" name="email" value="{{ old('email') }}" placeholder="Enter your email"
                            required
                            data-msg-required="Please enter your email address."
                            data-msg-email="Please enter a valid email address.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="loginPassword">Password</label>
                        <input class="form-input" type="password" id="loginPassword" name="password"
                            placeholder="Enter your password" required
                            data-msg-required="Please enter your password.">
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="{{ route('password.request') }}" class="forgot-password" id="forgotPassBtn">Forgot Password?</a>
                    </div>

                    <button type="submit" class="auth-submit">Login</button>
                </form>

                <!-- Register Form (Hidden by default) -->
                <form id="registerForm" class="auth-form validate-form" style="display: none;" method="POST" action="{{ route('register') }}" novalidate>
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="regName">Full Name</label>
                        <input class="form-input" type="text" id="regName" name="name" value="{{ old('name') }}" placeholder="Enter your full name"
                            required
                            data-msg-required="Please enter your full name.">
                    </div>
                    <div class="form-group" id="regEmailGroup">
                        <label class="form-label" for="regEmail">Email Address</label>
                        <input class="form-input" type="email" id="regEmail" name="email" value="{{ old('email') }}" placeholder="Enter your email"
                            required
                            data-msg-required="Please enter your email address."
                            data-msg-email="Please enter a valid email address.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regPhone">Phone Number</label>
                        <input class="form-input" type="tel" id="regPhone" name="phone" value="{{ old('phone') }}"
                            minlength="10" maxlength="10" data-rule-digits="true"
                            placeholder="Enter your phone number" required
                            data-msg-required="Please enter your phone number."
                            data-msg-digits="Please enter a valid 10-digit phone number."
                            data-msg-minlength="Please enter a valid 10-digit phone number."
                            data-msg-maxlength="Please enter a valid 10-digit phone number.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regPassword">Password</label>
                        <input class="form-input" type="password" id="regPassword" name="password"
                            placeholder="Create a password" required minlength="8"
                            data-msg-required="Please create a password."
                            data-msg-minlength="Password must be at least 8 characters long.">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regConfirm">Confirm Password</label>
                        <input class="form-input" type="password" id="regConfirm" name="password_confirmation" data-rule-equalTo="#regPassword"
                            placeholder="Confirm your password" required
                            data-msg-required="Please confirm your password."
                            data-msg-equalTo="Password confirmation does not match.">
                    </div>

                    <div class="form-options">
                        <label class="remember-me" style="display: flex; align-items: center; gap: 8px;">
                            <input type="checkbox" name="terms" required checked data-msg-required="Please accept Terms and Conditions to continue."> I agree to the <a href="{{ url('terms-conditions') }}" style="color: #a91b43;">Terms and Conditions</a>
                        </label>
                    </div>

                    <button type="submit" class="auth-submit">Register</button>
                </form>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const authTitle = document.querySelector('.auth-title');

        loginTab.onclick = () => {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            loginTab.classList.add('active');
            registerTab.classList.remove('active');
            authTitle.textContent = 'Welcome Back';
        };

        registerTab.onclick = () => {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            registerTab.classList.add('active');
            loginTab.classList.remove('active');
            authTitle.textContent = 'Join Us';
        };

        function clearFormValidation(formElement) {
            if (!window.jQuery) return;
            const $form = $(formElement);
            $form.find('.error-text').remove();
            $form.find('.error-border').removeClass('error-border');
            if ($form.data('validator')) {
                $form.validate().resetForm();
            }
        }

        loginTab.addEventListener('click', () => clearFormValidation(registerForm));
        registerTab.addEventListener('click', () => clearFormValidation(loginForm));

        // If there are errors other than login email/password mismatches, switch to register tab
        @if($errors->has('name') || $errors->has('phone') || $errors->has('password_confirmation'))
            registerTab.click();
        @endif
    </script>
@endpush
