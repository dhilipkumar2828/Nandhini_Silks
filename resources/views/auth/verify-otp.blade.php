@extends('frontend.layouts.app')

@section('title', 'Verify OTP | Nandhini Silks')

@section('content')
    <main class="auth-page">
        <div class="auth-container">
            <div class="auth-form-side">
                <div class="auth-header text-center">
                    {{-- <div class="auth-icon" style="background: linear-gradient(135deg, #fdf2f8 0%, #fff1f2 100%); width: 85px; height: 85px; border-radius: 24px; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px; box-shadow: 0 10px 25px -5px rgba(169, 27, 67, 0.15); transform: rotate(-5deg);">
                        <i class="fas fa-shield-halved" style="font-size: 38px; color: #a91b43;"></i>
                    </div> --}}
                    <h1 class="auth-title">Verify Your Account</h1>
                    <p class="auth-subtitle">Elevate your elegance securely with Nandhini Silks</p>
                </div>

                <div class="auth-tabs" style="justify-content: center; margin-bottom: 30px;">
                    <div class="auth-tab active" style="cursor: default; background: transparent; color: #111; border-color: #a91b43;">Email Verification</div>
                </div>

                @if(session('success'))
                    <div class="alert" style="color: #10b981; background: #d1fae5; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; font-weight: 600; text-align: center; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert" style="color: #ef4444; background: #fee2e2; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 13px;">
                        <ul style="list-style: none; padding: 0; margin: 0;">
                            @foreach($errors->all() as $error)
                                <li style="font-weight: 600; text-align: center;"><i class="fas fa-times-circle mr-1"></i> {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('otp.verify.submit') }}" method="POST" class="auth-form">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="otp" style="text-align: center; width: 100%; display: block; font-weight: 700; color: #444; margin-bottom: 12px; letter-spacing: 1px; text-transform: uppercase;">6-Digit OTP Code</label>
                        <input class="form-input" type="text" id="otp" name="otp" maxlength="6" 
                            placeholder="&bull; &bull; &bull; &bull; &bull; &bull;" required
                            style="text-align: center; font-size: 28px; letter-spacing: 12px; height: 65px; border-radius: 15px; border: 2px solid #eee; background: #fafafa; transition: all 0.3s;"
                            onfocus="this.style.borderColor='#a91b43'; this.style.background='#fff'; this.style.boxShadow='0 0 0 4px rgba(169, 27, 67, 0.05)';"
                            onblur="this.style.borderColor='#eee'; this.style.background='#fafafa'; this.style.boxShadow='none';">
                        <p style="text-align: center; font-size: 11px; color: #888; margin-top: 10px; font-weight: 600;">Please check your spam folder if you don't see the email</p>
                    </div>

                    <button type="submit" class="auth-submit" style="height: 55px; font-size: 16px; font-weight: 800; border-radius: 15px; margin-top: 10px;">Verify Account</button>
                </form>

                <div style="margin-top: 35px; padding-top: 25px; border-top: 1px solid #f0f0f0; text-align: center;">
                    <p style="color: #666; font-size: 14px; font-weight: 600;">Didn't receive the code?</p>
                    <form action="{{ route('otp.resend') }}" method="POST">
                        @csrf
                        <button type="submit" style="color: #a91b43; background: transparent; border: none; font-weight: 800; font-size: 14px; cursor: pointer; padding: 5px 10px; margin-top: 5px; transition: all 0.2s;" onmouseover="this.style.textDecoration='underline'; this.style.transform='scale(1.05)';" onmouseout="this.style.textDecoration='none'; this.style.transform='scale(1)';">Resend New OTP</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection
