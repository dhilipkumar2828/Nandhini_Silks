<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use App\Mail\VerficationOTP;
use App\Mail\NewRegistrationAdminAlert;

class UserAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email is wrong.',
            ])->onlyInput('email');
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'password' => 'Password is wrong.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));

        if (!$user->is_verified) {
            // If not verified, logout and redirect to OTP verification
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $user->otp = $otp;
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();

            try {
                Mail::to($user->email)->send(new VerficationOTP($otp));
            } catch (\Exception $e) {
                Log::error('Login Verification OTP Failure: ' . $e->getMessage());
            }

            Auth::logout();
            $request->session()->put('pending_verification_user_id', $user->id);
            return redirect()->route('otp.verify.form')->with('error', 'Your account is not verified. A new OTP has been sent to your email.');
        }

        $request->session()->regenerate();
        $user->last_login_at = now();
        $user->save();
        
        // Sync cart from session to database
        (new \App\Http\Controllers\CartController)->syncCartOnLogin();
        
        return redirect()->intended(route('home'))->with('success', 'Welcome back, ' . $user->name . '!');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'regex:/^[A-Za-z\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['required', 'regex:/^[0-9]{10}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.regex' => 'Name must contain only alphabets.',
            'phone.regex' => 'Please enter a valid 10-digit phone number.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        // In Laravel 12 with 'password' => 'hashed' cast, we don't need Hash::make() 
        // if we want to avoid double hashing, but standard practice is often to pass 
        // the plain string and let the model handle it OR hash it here and ensure 
        // the model doesn't re-hash. Since the model has the 'hashed' cast, 
        // we'll pass it directly to be safe or use Hash::make and check if it breaks.
        // Actually, to be absolutely sure what's failing, let's hash it but 
        // I suspect the logic below is what they need.
        
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => $request->password,
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'is_verified' => false,
        ]);

        // Instead of full login, we'll store basic ID in session for verification
        $request->session()->put('pending_verification_user_id', $user->id);

        // Send OTP email to customer (separate try-catch so admin alert always fires)
        try {
            Mail::to($user->email)->send(new VerficationOTP($otp));
            Log::info('Registration OTP sent successfully to customer: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Registration OTP to CUSTOMER failed [' . $user->email . ']: ' . $e->getMessage());
        }

        // Small delay to avoid "Too many emails per second" (550 error)
        sleep(2);

        // Notify Admin (separate block so customer mail failure doesn't block this)
        try {
            $adminEmail = Setting::getAdminEmail();
            Mail::to($adminEmail)->send(new NewRegistrationAdminAlert($user));
            Log::info('Admin registration alert sent for user: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Admin registration alert failed: ' . $e->getMessage());
        }

        return redirect()->route('otp.verify.form')->with('success', 'A verification OTP has been sent to your email.');
    }

    public function showVerifyForm(Request $request)
    {
        if (!$request->session()->has('pending_verification_user_id')) {
            return redirect()->route('register');
        }
        return view('auth.verify-otp');
    }

    public function verifyOTP(Request $request)
    {
        $request->validate(['otp' => 'required|string|size:6']);

        $userId = $request->session()->get('pending_verification_user_id');
        if (!$userId) {
            return redirect()->route('register');
        }

        $user = User::findOrFail($userId);

        if ($user->otp === $request->otp && $user->otp_expires_at > now()) {
            $user->is_verified = true;
            $user->email_verified_at = now();
            $user->otp = null;
            $user->otp_expires_at = null;
            $user->save();

            Auth::login($user);
            $request->session()->forget('pending_verification_user_id');
            
            // Sync cart from session to database
            (new \App\Http\Controllers\CartController)->syncCartOnLogin();

            return redirect()->route('home')->with('success', 'Email verified successfully! Welcome to Nandhini Silks.');
        }

        return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
    }

    public function resendOTP(Request $request)
    {
        $userId = $request->session()->get('pending_verification_user_id');
        if (!$userId) return redirect()->route('register');

        $user = User::findOrFail($userId);
        $otp = sprintf("%06d", mt_rand(1, 999999));
        
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new VerficationOTP($otp));
        } catch (\Exception $e) {
            Log::error('OTP Resend Failure: ' . $e->getMessage());
        }

        return back()->with('success', 'A new OTP has been sent to your email.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Logged out successfully.');
    }

    // Password Reset Methods
    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        Log::info('Password reset process started for email: ' . $request->email);

        try {
            $status = \Illuminate\Support\Facades\Password::broker()->sendResetLink(
                $request->only('email')
            );

            Log::info('Password reset broker return status: ' . __($status));

            if ($status === \Illuminate\Support\Facades\Password::RESET_LINK_SENT) {
                return redirect()->route('login')->with('success', __($status));
            }

            Log::warning('Password reset failed due to broker status: ' . __($status));
            return back()->withErrors(['email' => __($status)]);
            
        } catch (\Exception $e) {
            Log::error('CRITICAL: Password reset email exception for ' . $request->email . ': ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors(['email' => 'Oops! Something went wrong while sending the email. Error: ' . $e->getMessage()]);
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = \Illuminate\Support\Facades\Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = $password; // Letting the 'hashed' cast handle it
                $user->setRememberToken(\Illuminate\Support\Str::random(60));
                $user->save();
            }
        );

        return $status === \Illuminate\Support\Facades\Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('success', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
