<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | Nandhini Silks Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.02);
        }

        .card-glass {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
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

        input.error,
        select.error,
        textarea.error {
            border-color: #ef4444 !important;
        }

        /* Select2 Custom Styles to match your theme */
        .select2-container--default .select2-selection--single, 
        .select2-container--default .select2-selection--multiple {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 0.5rem;
            min-height: 38px;
        }
        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #a91b43 !important;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 38px;
            font-size: 0.875rem;
            color: #1e293b;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #a91b43;
            border: none;
            color: white;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
        }
        .select2-dropdown {
            border-color: #e2e8f0;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .select2-results__option--highlighted[aria-selected] {
            background-color: #a91b43 !important;
        }

        /* Custom Toastr Colors */
        #toast-container > .toast {
            background-image: none !important;
            padding: 15px 20px 15px 50px !important;
            box-shadow: 0 5px 15px rgba(169, 27, 67, 0.2) !important;
            opacity: 1 !important;
        }
        #toast-container > .toast-success, 
        #toast-container > .toast-error, 
        #toast-container > .toast-info, 
        #toast-container > .toast-warning {
            background-color: #a91b43 !important;
        }

        /* Mandatory Field marker */
        .required-label::after {
            content: " *";
            color: #ef4444;
            font-weight: bold;
        }
        .error-text {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        input.error-border, select.error-border, textarea.error-border {
            border-color: #ef4444 !important;
        }
        input[type="file"]::file-selector-button {
            background-color: #a91b43;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-size: 0.875rem;
            font-weight: 600;
            transition: background-color 0.2s;
            margin-right: 1rem;
        }
        input[type="file"]::file-selector-button:hover {
            background-color: #940437;
        }
    </style>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <style>

        /* Flatpickr Custom Styles */
        .flatpickr-calendar {
            width: 307.875px !important;
        }
        .dayContainer {
            min-width: 307.875px !important;
            max-width: 307.875px !important;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: #a91b43 !important;
            border-color: #a91b43 !important;
        }
        .flatpickr-months .flatpickr-month, .flatpickr-current-month .flatpickr-monthDropdown-months {
            background: #a91b43 !important;
        }
        .flatpickr-weekdays {
            background: #a91b43 !important;
        }
        span.flatpickr-weekday {
            background: #a91b43 !important;
            color: white !important;
        }
        .flatpickr-months .flatpickr-prev-month svg, 
        .flatpickr-months .flatpickr-next-month svg {
            width: 14px !important;
            height: 14px !important;
            fill: #fff !important;
        }
    </style>
</head>

<body class="min-h-screen flex">
    <!-- Sidebar -->
    <aside class="sidebar-glass w-60 fixed h-full flex flex-col z-50 text-slate-600">
        <div class="px-6 py-8 border-b border-slate-50 flex flex-col items-center">
            <img src="{{ asset('images/image 1.png') }}" alt="Nandhini Silks"
                class="h-14 w-auto mix-blend-multiply mb-2">
            <span class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Management Suite</span>
        </div>
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto custom-scrollbar">
            <div class="px-4 py-3 text-[13px] font-bold text-slate-400 uppercase tracking-widest">Main Menu</div>

            <a href="{{ route('admin.dashboard') }}"
                class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-home text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Analytics</span>
            </a>

            <!-- Catalog Dropdown -->
            <div x-data="{ open: {{ request()->routeIs('admin.categories.*', 'admin.sub-categories.*', 'admin.child-categories.*', 'admin.attributes.*', 'admin.attribute-values.*', 'admin.products.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="w-full nav-link flex items-center px-4 py-3 rounded-xl transition-all" :class="open ? 'bg-slate-50 text-[#a91b43]' : ''">
                    <div class="w-6 flex justify-center"><i class="fas fa-book-open text-base"></i></div>
                    <span class="font-bold ml-2 text-sm text-left flex-1">Catalog</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="pl-4 mt-1 space-y-1">
                        <a href="{{ route('admin.categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-layer-group text-xs"></i></div>
                            <span class="font-bold ml-2 text-[12px]">Categories</span>
                        </a>

                        <a href="{{ route('admin.sub-categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.sub-categories.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-indent text-xs"></i></div>
                            <span class="font-bold ml-2 text-[12px]">Sub Categories</span>
                        </a>

                        <a href="{{ route('admin.child-categories.index') }}"
                            class="nav-link {{ request()->routeIs('admin.child-categories.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-outdent text-xs"></i></div>
                            <span class="font-bold ml-2 text-[12px]">Child Categories</span>
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                            class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }} flex items-center px-3 py-2 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-box text-xs"></i></div>
                            <span class="font-bold ml-2 text-[12px]">Products</span>
                        </a>

                        <div x-data="{ openAttr: {{ request()->routeIs('admin.attributes.*', 'admin.attribute-values.*') ? 'true' : 'false' }} }">
                            <button @click="openAttr = !openAttr"
                                class="w-full nav-link flex items-center px-3 py-2 rounded-xl transition-all"
                                :class="openAttr ? 'bg-slate-50 text-[#a91b43]' : ''">
                                <div class="w-4 flex justify-center"><i class="fas fa-tags text-xs"></i></div>
                                <span class="font-bold ml-2 text-[12px] text-left flex-1">Attributes</span>
                                <i class="fas fa-chevron-down text-[8px] transition-transform duration-300"
                                    :class="openAttr ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="openAttr" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0" class="pl-4 mt-1 space-y-1">
                                <a href="{{ route('admin.attributes.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                                    <span class="font-bold ml-2 text-[11px]">Attribute Group</span>
                                </a>
                                <a href="{{ route('admin.attribute-values.index') }}"
                                    class="nav-link {{ request()->routeIs('admin.attribute-values.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                                    <span class="font-bold ml-2 text-[11px]">Attribute Values</span>
                                </a>
                            </div>
                        </div>
                </div>
            </div>



            <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-shopping-cart text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Orders</span>
            </a>

            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-users text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Users</span>
            </a>

           


            <div class="px-4 py-3 text-[13px] font-bold text-slate-400 uppercase tracking-widest mt-2">Customer Insight
            </div>

            <a href="{{ route('admin.banners.index') }}"
                class="nav-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-image text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Banners</span>
            </a>

            <a href="{{ route('admin.ads.index') }}"
                class="nav-link {{ request()->routeIs('admin.ads.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-ad text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Advertisements</span>
            </a>

            <a href="{{ route('admin.testimonials.index') }}"
                class="nav-link {{ request()->routeIs('admin.testimonials.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-comment-dots text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Testimonials</span>
            </a>

            <div class="px-4 py-3 text-[13px] font-bold text-slate-400 uppercase tracking-widest mt-2">System</div>

            <!-- Tax & Shipping Settings Dropdown -->
            <div
                x-data="{ open: {{ request()->routeIs('admin.tax-classes.*', 'admin.tax-rates.*', 'admin.shipping-classes.*', 'admin.shipping-rates.*') ? 'true' : 'false' }} }">
                <button @click="open = !open"
                    class="w-full nav-link flex items-center px-4 py-3 rounded-xl transition-all"
                    :class="open ? 'bg-slate-50 text-[#a91b43]' : ''">
                    <div class="w-6 flex justify-center"><i class="fas fa-truck text-base"></i></div>
                    <span class="font-bold ml-2 text-sm text-left flex-1">Tax & Shipping</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-300"
                        :class="open ? 'rotate-180' : ''"></i>
                </button>
                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" class="pl-4 mt-1 space-y-1">

                        <a href="{{ route('admin.tax-classes.index') }}"
                            class="nav-link {{ request()->routeIs('admin.tax-classes.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-layer-group text-[11px]"></i></div>
                            <span class="font-bold ml-2 text-[11px]">Tax Classes</span>
                        </a>

                        <a href="{{ route('admin.tax-rates.index') }}"
                            class="nav-link {{ request()->routeIs('admin.tax-rates.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-chart-line text-[11px]"></i></div>
                            <span class="font-bold ml-2 text-[11px]">Tax Rates</span>
                        </a>

                        <a href="{{ route('admin.shipping-classes.index') }}"
                            class="nav-link {{ request()->routeIs('admin.shipping-classes.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-shipping-fast text-[11px]"></i></div>
                            <span class="font-bold ml-2 text-[11px]">Shipping Classes</span>
                        </a>

                        <a href="{{ route('admin.shipping-rates.index') }}"
                            class="nav-link {{ request()->routeIs('admin.shipping-rates.*') ? 'active' : '' }} flex items-center px-3 py-1.5 rounded-xl transition-all">
                            <div class="w-4 flex justify-center"><i class="fas fa-coins text-[11px]"></i></div>
                            <span class="font-bold ml-2 text-[11px]">Shipping Rates</span>
                        </a>
                </div>
            </div>

            <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-ticket text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Coupons</span>
            </a>

            <a href="{{ route('admin.stock.index') }}" class="nav-link {{ request()->routeIs('admin.stock.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-boxes-stacked text-base"></i></div>
                <span class="font-bold ml-2 text-sm">Stock Maintenance</span>
            </a>

            <a href="{{ route('admin.settings.index') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }} flex items-center px-4 py-3 rounded-xl">
                <div class="w-6 flex justify-center"><i class="fas fa-cog text-base"></i></div>
                <span class="font-bold ml-2 text-sm">System Settings</span>
            </a>

            <!-- Sidebar Profile -->
            <div class="mt-auto border-t border-slate-50 p-4">
                <a href="{{ route('admin.profile.index') }}" class="nav-link flex items-center p-2 rounded-xl {{ request()->routeIs('admin.profile.*') ? 'bg-slate-50 text-[#a91b43]' : '' }}">
                    <div class="w-8 h-8 rounded-lg overflow-hidden flex items-center justify-center shadow-sm">
                        @php $admin = Auth::guard('admin')->user(); @endphp
                        @if($admin->profile_photo)
                            <img src="{{ asset($admin->profile_photo) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full gradient-bg flex items-center justify-center font-black text-xs text-white">
                                {{ substr($admin->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-[13px] font-black text-slate-700 truncate capitalize">{{ $admin->name }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">View Profile</p>
                    </div>
                    <i class="fas fa-chevron-right ml-auto text-[11px] text-slate-300"></i>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-60 p-6 bg-[#f9fafc]">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-black text-slate-900 tracking-tight">@yield('title', 'Dashboard')</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <p class="text-slate-400 text-[10px] font-bold uppercase tracking-widest">Nandhini Silks Console •
                        Live</p>
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
                <button
                    class="w-9 h-9 flex items-center justify-center card-glass rounded-lg hover:bg-[#a91b43] hover:text-white transition-all group relative">
                    <i class="fas fa-bell text-sm"></i>
                    <span
                        class="absolute top-2 right-2 w-1.5 h-1.5 bg-rose-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center space-x-3 bg-white p-1 pr-3 rounded-xl border border-slate-100 shadow-sm hover:border-[#a91b43]/20 transition-all">
                        <div class="w-8 h-8 rounded-lg overflow-hidden flex items-center justify-center shadow-md">
                            @php $admin = Auth::guard('admin')->user(); @endphp
                            @if($admin->profile_photo)
                                <img src="{{ asset($admin->profile_photo) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full gradient-bg flex items-center justify-center font-black text-sm text-white">
                                    {{ substr($admin->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-xs font-black text-slate-800 leading-none mb-0.5 capitalize">{{ $admin->name }}</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tight">{{ $admin->role }}</span>
                        </div>
                        <i class="fas fa-chevron-down text-[10px] text-slate-300 transition-transform duration-300" :class="open ? 'rotate-180' : ''"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                         class="absolute right-0 mt-2 w-56 card-glass rounded-2xl py-2 z-[60] overflow-hidden" 
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-slate-50 mb-1">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Quick Access</p>
                            <p class="text-xs font-black text-slate-900 truncate capitalize">{{ Auth::guard('admin')->user()->name }}</p>
                        </div>

                        <a href="{{ route('admin.profile.index') }}" class="flex items-center space-x-3 px-4 py-2 text-slate-600 hover:bg-[#a91b43]/5 hover:text-[#a91b43] transition-all group">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-[#a91b43]/10 transition-colors">
                                <i class="fas fa-user-circle text-sm"></i>
                            </div>
                            <span class="text-xs font-bold">My Profile</span>
                        </a>

                        <a href="{{ route('admin.manage-admins.index') }}" class="flex items-center space-x-3 px-4 py-2 text-slate-600 hover:bg-[#a91b43]/5 hover:text-[#a91b43] transition-all group">
                            <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center group-hover:bg-[#a91b43]/10 transition-colors">
                                <i class="fas fa-user-plus text-sm"></i>
                            </div>
                            <span class="text-xs font-bold">Add Admin</span>
                        </a>

                        <div class="h-[1px] bg-slate-50 my-1 mx-2"></div>

                        <form action="{{ route('admin.logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center space-x-3 px-4 py-2 text-rose-600 hover:bg-rose-50 transition-all group">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 flex items-center justify-center group-hover:bg-rose-100 transition-colors">
                                    <i class="fas fa-power-off text-sm"></i>
                                </div>
                                <span class="text-xs font-bold">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        @yield('content')
    </main>

    <script>
        window.UPLOAD_URL = "{{ asset('uploads') }}";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        $(document).ready(function() {
            // Initialize Flatpickr for dates
            $('input[type="date"]').flatpickr({
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
            });
            $('input[type="datetime-local"]').flatpickr({
                enableTime: true,
                altInput: true,
                altFormat: "F j, Y H:i",
                dateFormat: "Y-m-d H:i",
            });
        });

        $(document).ready(function() {
            // Auto-add <span>*</span> to required labels
            $('input[required], select[required], textarea[required], input[data-rule-required="true"]').each(function() {
                var label = $(this).closest('.form-group, .mb-4, .mb-3, .space-y-1\\.5, .mb-1').find('label').first();
                if (label.length) {
                    // Only add if no star already exists
                    if (!label.find('.text-rose-500').length && !label.find('.text-red-500').length && label.text().indexOf('*') === -1) {
                        label.append('<span class="text-rose-500 ml-1">*</span>');
                    }
                }
            });

            // Initialize validation on all admin forms
            $('form:not(.no-validate)').each(function() {
                $(this).validate({
                    errorElement: 'span',
                    errorClass: 'error-text',
                    highlight: function(element) {
                        $(element).addClass('error-border');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('error-border');
                    },
                    errorPlacement: function(error, element) {
                        if (element.hasClass('select2-hidden-accessible')) {
                            error.insertAfter(element.next('.select2-container'));
                        } else if (element.parent('.input-group').length) {
                            error.insertAfter(element.parent());
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            });

            // Re-validate select2 on change
            $('.select2-hidden-accessible').on('change', function() {
                $(this).valid();
            });
        });

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
        // Global Slugify Function
        function slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-')         // Replace multiple - with single -
                .replace(/^-+/, '')             // Trim - from start of text
                .replace(/-+$/, '');            // Trim - from end of text
        }
    </script>
    @stack('scripts')
</body>

</html>


