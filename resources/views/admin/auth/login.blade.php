<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="icon" type="image/png" href="{{ asset('images/nandhini-logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fffcf0; /* Matching top-header background */
        }
        .glass {
            background: #ffffff;
            border: 1px solid #eaeaea;
            box-shadow: 0 8px 32px rgba(148, 4, 55, 0.08);
        }
        .gradient-text {
            background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .input-focus:focus {
            border-color: #a91b43;
            box-shadow: 0 0 0 4px rgba(169, 27, 67, 0.1);
        }
        .brand-logo {
            width: 180px;
            mix-blend-mode: multiply;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-6 text-slate-900">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" class="brand-logo">
            </div>
            <h1 class="text-3xl font-bold gradient-text mb-2">Admin Portal</h1>
            <p class="text-slate-500">Authorized Access Only</p>
        </div>

        <div class="glass p-8 rounded-3xl shadow-2xl relative overflow-hidden">
            {{-- Credential Mismatch Error Display at Top --}}
            @if (Session::has('error'))
                <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-sm flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ Session::get('error') }}</span>
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-6" novalidate>
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full bg-white border border-slate-200 text-slate-900 px-4 py-3 rounded-xl outline-none transition-all input-focus @error('email') border-red-400 @enderror"
                        placeholder="Enter your email">
                    @error('email')
                        <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative group">
                        <input type="password" name="password" id="password" required
                            class="w-full bg-white border border-slate-200 text-slate-900 px-4 py-3 rounded-xl outline-none transition-all input-focus @error('password') border-red-400 @enderror"
                            placeholder="Enter your password">
                        <button type="button" id="togglePassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#a91b43] transition-colors p-1">
                            <i class="fa-regular fa-eye-slash" id="eyeIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-2 ml-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-sm pt-1">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-[#a91b43] focus:ring-[#a91b43]">
                        <span class="ml-2 text-slate-600 font-medium">Remember me</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full bg-[#a91b43] hover:bg-[#940437] text-white font-bold py-3.5 rounded-xl transition-all shadow-lg shadow-pink-900/10 active:scale-[0.98] mt-2">
                    Sign In
                </button>
            </form>
        </div>

        <p class="text-center mt-8 text-slate-400 text-xs tracking-wide">
            &copy; {{ date('Y') }} Admin Panel. All rights reserved.
        </p>
    </div>

    <!-- Toastr & jQuery -->
    <!-- jQuery (Removing Toastr but keeping script block for Eye toggle) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle Password Visibility
        const passwordInput = document.querySelector('#password');
        const toggleBtn = document.querySelector('#togglePassword');
        const eyeIcon = document.querySelector('#eyeIcon');

        if(toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });
        }
    </script>
</body>
</html>
