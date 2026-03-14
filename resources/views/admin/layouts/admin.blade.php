<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Nandhini Silks Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
        }
        .sidebar-glass {
            background: #ffffff;
            border-right: 1px solid #e2e8f0;
            box-shadow: 4px 0 24px rgba(0,0,0,0.02);
        }
        .card-glass {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.03);
        }
        .nav-link {
            transition: all 0.3s ease;
            color: #64748b;
        }
        .nav-link:hover {
            background: rgba(169, 27, 67, 0.05);
            color: #a91b43;
        }
        .nav-link.active {
            background: #a91b43;
            color: #ffffff;
            box-shadow: 0 10px 20px rgba(169, 27, 67, 0.2);
        }
        .gradient-bg {
            background: linear-gradient(90deg, #a91b43 0%, #fbb624 100%);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
        .error {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        input.error, select.error, textarea.error {
            border-color: #ef4444 !important;
        }
    </style>
</head>
<body class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="sidebar-glass w-60 fixed h-full flex flex-col z-50 text-slate-600">
        <div class="px-6 py-6 border-b border-slate-50 flex flex-col items-center">
            <img src="{{ asset('images/image 1.png') }}" alt="Nandhini Silks" class="h-10 w-auto mix-blend-multiply mb-1">
            <span class="text-[8px] font-black text-slate-300 uppercase tracking-[0.2em]">Management Suite</span>
        </div>
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-scrollbar">
            <div class="px-4 py-2 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Main Menu</div>
            
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center px-4 py-2.5 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-home text-base"></i></div>
                <span class="font-bold ml-2 text-xs">Analytics</span>
            </a>
        
            <!-- Catalog Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.categories.*', 'admin.sub-categories.*', 'admin.child-categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full nav-link flex items-center px-4 py-2.5 rounded-xl transition-all" :class="open ? 'bg-slate-50 text-[#a91b43]' : ''">
                    <div class="w-6 flex justify-center"><i class="fas fa-book-open text-base"></i></div>
                    <span class="font-bold ml-2 text-xs text-left flex-1">Catalog</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                </button>
                
                <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="pl-4 mt-1 space-y-1">
                    <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                        <div class="w-4 flex justify-center"><i class="fas fa-layer-group text-[10px]"></i></div>
                        <span class="font-bold ml-2 text-[10px]">Categories</span>
                    </a>

                    <a href="{{ route('admin.sub-categories.index') }}" class="nav-link {{ request()->routeIs('admin.sub-categories.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                        <div class="w-4 flex justify-center"><i class="fas fa-indent text-[10px]"></i></div>
                        <span class="font-bold ml-2 text-[10px]">Sub Categories</span>
                    </a>

                    <a href="{{ route('admin.child-categories.index') }}" class="nav-link {{ request()->routeIs('admin.child-categories.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                        <div class="w-4 flex justify-center"><i class="fas fa-outdent text-[10px]"></i></div>
                        <span class="font-bold ml-2 text-[10px]">Child Categories</span>
                    </a>

                    <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                        <div class="w-4 flex justify-center"><i class="fas fa-box text-[10px]"></i></div>
                        <span class="font-bold ml-2 text-[10px]">Products</span>
                    </a>
                </div>
            </div>


            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }} flex items-center px-4 py-2.5 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-shopping-cart text-base"></i></div>
                <span class="font-bold ml-2 text-xs">Orders</span>
            </a>

           

            <div class="px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">Customer Insight</div>

            <a href="#" class="nav-link flex items-center px-4 py-2.5 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-user-tie text-base"></i></div>
                <span class="font-bold ml-2 text-xs">Clients</span>
            </a>

            <a href="#" class="nav-link flex items-center px-4 py-2.5 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-truck-fast text-base"></i></div>
                <span class="font-bold ml-2 text-xs">Logistics</span>
                <span class="ml-auto bg-amber-100 text-amber-600 text-[10px] font-black px-2 py-0.5 rounded-lg">12</span>
            </a>

            <div class="px-4 py-3 text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-2">System</div>

            <a href="#" class="nav-link flex items-center px-4 py-2.5 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-sliders text-base"></i></div>
                <span class="font-bold ml-2 text-xs">Preferences</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-60 p-6 bg-[#f9fafc]">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Nandhini Silks Console • Live</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <!-- Date Info -->
                <div class="text-right hidden sm:block">
                    <p class="text-xs font-black text-slate-800">{{ date('d M Y') }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">{{ date('l') }}</p>
                </div>

                <div class="h-6 w-[1px] bg-slate-200 hidden sm:block"></div>

                <!-- Notifications -->
                <button class="w-9 h-9 flex items-center justify-center card-glass rounded-lg hover:bg-[#a91b43] hover:text-white transition-all group relative">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-rose-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile and Logout -->
                <div class="flex items-center space-x-3 bg-white p-1 pr-3 rounded-xl border border-slate-100 shadow-sm">
                    <div class="w-8 h-8 rounded-lg gradient-bg flex items-center justify-center font-black text-sm text-white shadow-md">
                        {{ substr(Auth::guard('admin')->user()->name, 0, 1) }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-800 leading-none mb-0.5">{{ Auth::guard('admin')->user()->name }}</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">Admin</span>
                    </div>
                    <div class="w-[1px] h-5 bg-slate-100 mx-1"></div>
                    <form action="{{ route('admin.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="w-7 h-7 flex items-center justify-center bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all rounded-lg shadow-sm group">
                            <i class="fas fa-power-off text-[10px]"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    <script>
        // Toastr Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        @if(Session::has('success'))
            toastr.success("{{ Session::get('success') }}");
        @endif

        @if(Session::has('error'))
            toastr.error("{{ Session::get('error') }}");
        @endif

        @if($errors->any())
            @foreach($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        @endif
    </script>
    @stack('scripts')
</body>
</html>
