@extends('frontend.layouts.app')

@section('title', 'Login & Register | Nandhini Silks')

@push('styles')
<style>
    .password-input-wrap {
        position: relative;
    }

    .password-input-wrap .form-input {
        padding-right: 44px;
    }

    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        color: #888;
        cursor: pointer;
        width: 24px;
        height: 24px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }
</style>
@endpush

@section('content')
    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-form-side">
                <div class="auth-header">
                    <h1 class="auth-title">Welcome Back</h1>
                    <p class="auth-subtitle">Elevate your elegance with Nandhini Silks</p>
                </div>

                <div class="auth-tabs" id="authTabs">
                    {{-- @if(session('error'))
                        <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 12px; font-weight: bold;">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="alert" style="color: #10b981; background: #d1fae5; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 12px; font-weight: bold;">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif --}}
                    
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
                        @error('email')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="loginPassword">Password</label>
                        <div class="password-input-wrap">
                            <input class="form-input" type="password" id="loginPassword" name="password"
                                placeholder="Enter your password" required
                                data-msg-required="Please enter your password.">
                            <button type="button" class="password-toggle-btn" data-target="loginPassword" aria-label="Show password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
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
                            oninput="this.value=this.value.replace(/[^A-Za-z\s]/g,'')"
                            pattern="^[A-Za-z\s]+$"
                            required
                            data-msg-required="Please enter your full name."
                            data-msg-pattern="Name must contain only alphabets.">
                        @error('name')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
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
                            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                            minlength="10" maxlength="10" pattern="^[0-9]{10}$" data-rule-digits="true"
                            placeholder="Enter your phone number" required
                            data-msg-required="Please enter your phone number."
                            data-msg-digits="Please enter a valid 10-digit phone number."
                            data-msg-minlength="Please enter a valid 10-digit phone number."
                            data-msg-maxlength="Please enter a valid 10-digit phone number."
                            data-msg-pattern="Please enter a valid 10-digit phone number.">
                        @error('phone')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regPassword">Password</label>
                        <div class="password-input-wrap">
                            <input class="form-input" type="password" id="regPassword" name="password"
                                placeholder="Create a password" required minlength="8" autocomplete="new-password"
                                data-msg-required="Please create a password."
                                data-msg-minlength="Password must be at least 8 characters long.">
                            <button type="button" class="password-toggle-btn" data-target="regPassword" aria-label="Show password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="regConfirm">Confirm Password</label>
                        <div class="password-input-wrap">
                            <input class="form-input" type="password" id="regConfirm" name="password_confirmation" data-rule-equalTo="#regPassword"
                                placeholder="Confirm your password" required autocomplete="new-password"
                                data-msg-required="Please confirm your password."
                                data-msg-equalTo="Password confirmation does not match.">
                            <button type="button" class="password-toggle-btn" data-target="regConfirm" aria-label="Show password">
                                <i class="fa-regular fa-eye"></i>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
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

        document.querySelectorAll('.password-toggle-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const inputId = button.getAttribute('data-target');
                const input = document.getElementById(inputId);
                const icon = button.querySelector('i');
                if (!input || !icon) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);
                button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });

        // If there are errors other than login email/password mismatches, switch to register tab
        @if($errors->has('name') || $errors->has('phone') || $errors->has('password_confirmation'))
            registerTab.click();
        @endif
    </script>
@endpush
