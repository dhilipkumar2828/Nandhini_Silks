<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nandhini Silks')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/nandhini-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('styles')
    <style>
        /* Cart Drawer Styles */
        .cart-drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .cart-drawer-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .cart-drawer {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 400px;
            height: 100%;
            background: #fff;
            z-index: 2001;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
        }

        .cart-drawer.active {
            right: 0;
        }

        .cart-drawer-header {
            padding: 24px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cart-drawer-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }

        .cart-drawer-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #999;
            transition: color 0.2s;
        }

        .cart-drawer-close:hover {
            color: #A91B43;
        }

        .cart-drawer-items {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        .cart-item-mini {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid #f9f9f9;
        }

        .cart-item-mini-img {
            width: 80px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            background: #f5f5f5;
        }

        .cart-item-mini-info {
            flex: 1;
        }

        .cart-item-mini-name {
            font-size: 15px;
            font-weight: 600;
            color: #111;
            margin: 0 0 4px;
            text-decoration: none;
            display: block;
        }

        .cart-item-mini-meta {
            font-size: 13px;
            color: #888;
            margin-bottom: 8px;
        }

        .cart-item-mini-price {
            font-size: 16px;
            font-weight: 700;
            color: #A91B43;
        }

        .cart-drawer-footer {
            padding: 24px;
            background: #fdfaf0;
            border-top: 1px solid #eee;
        }

        .cart-drawer-summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .cart-drawer-summary span {
            font-size: 17px;
            font-weight: 600;
            color: #111;
        }

        .cart-drawer-btn {
            display: block;
            width: 100%;
            padding: 16px;
            text-align: center;
            border-radius: 10px;
            font-weight: 700;
            text-decoration: none;
            margin-bottom: 12px;
            transition: all 0.3s;
        }

        .cart-drawer-btn-primary {
            background: #A91B43;
            color: #fff;
        }

        .cart-drawer-btn-secondary {
            background: #fff;
            color: #111;
            border: 1px solid #ddd;
        }

        .cart-drawer-btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            .cart-drawer {
                max-width: 85%;
            }
        }

        /* Validation Styles */
        .required-label::after {
            content: " *";
            color: #A91B43;
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

        .product-name-v2, .recently-viewed-name {
            display: -webkit-box !important;
            -webkit-line-clamp: 2 !important;
            -webkit-box-orient: vertical !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            min-height: 2.8em !important;
            margin-bottom: 4px !important;
        }

        .read-more-link {
            font-size: 12px;
            color: #A91B43;
            font-weight: 600;
            text-decoration: underline;
            margin-bottom: 8px;
            display: block;
            cursor: pointer;
        }

        .header-actions-group {
            display: flex;
            align-items: center;
            gap: 15px;
            justify-content: flex-end;
        }

        .tracking-box {
            width: 200px !important;
            border-color: #666 !important;
        }
        
        .tracking-box input {
            color: #555 !important;
        }

        @media (max-width: 991px) {
            .tracking-wrap {
                display: block !important;
            }
        }

        @media (max-width: 860px) {
            .header-row {
                display: grid !important;
                grid-template-columns: auto auto;
                justify-content: space-between !important;
                align-items: center !important;
                gap: 15px 10px !important;
                padding-bottom: 15px !important;
            }
            .brand-link {
                grid-column: 1;
                grid-row: 1;
            }
            .brand {
                width: 140px !important;
            }
            .header-right {
                display: contents !important;
            }
            .actions {
                grid-column: 2;
                grid-row: 1;
                width: auto !important;
                justify-content: flex-end !important;
                margin: 0 !important;
            }
            .header-actions-group {
                grid-column: 1 / span 2;
                grid-row: 2;
                width: 100% !important;
                display: flex !important;
                flex-direction: row !important;
                gap: 10px !important;
                justify-content: space-between !important;
            }
            .search-wrap, .tracking-wrap {
                flex: 1;
                min-width: 0;
            }
            .search-box, .tracking-box {
                width: 100% !important;
                max-width: 100% !important;
                height: 40px !important;
            }
            .search-box input, .tracking-box input {
                font-size: 14px !important;
            }
        }

        @media (max-width: 580px) {
            .header-actions-group {
                flex-direction: column !important;
                gap: 8px !important;
            }
            .search-wrap, .tracking-wrap {
                width: 100%;
            }
        }
    </style>

    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">
    <style>
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


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body class="{{ request()->routeIs('home') ? 'home-page' : 'inner-page' }}">
    <header class="top-header">
        <div class="page-shell header-row">
            <a href="{{ route('home') }}" class="brand-link">
                <img class="brand" src="{{ asset('images/nandhini-logo.png') }}" alt="Nandhini Silks" />
            </a>

            <div class="header-right">
                <div class="header-actions-group">
                    <div class="search-wrap">
                        <form action="{{ route('search') }}" method="GET" class="search-box">
                            <img src="{{ asset('images/search.svg') }}" alt="Search" />
                            <input type="text" name="q" placeholder="Search" aria-label="Search" value="{{ request('q') }}" />
                            <button type="submit" style="display: none;"></button>
                        </form>
                    </div>

                    @auth
                        @if(Auth::user()->orders()->count() > 0)
                            <div class="tracking-wrap">
                                <form action="{{ route('track-order') }}" method="GET" class="search-box tracking-box">
                                    <i class="fas fa-truck" style="margin-left: 5px; color: #666; font-size: 14px;"></i>
                                    <input type="text" name="id" placeholder="Track Order ID" aria-label="Track Order" value="{{ request()->routeIs('track-order') ? request('id') : '' }}" required />
                                </form>
                            </div>
                        @endif
                    @endauth
                </div>

                <div class="actions">
                    <button class="icon-btn" type="button" aria-label="Favorites"
                        onclick="window.location.href='{{ route('wishlist') }}'">
                        <img src="{{ asset('images/favorite.svg') }}" alt="" width="18" height="23">
                        <span class="cart-count wishlist-count-badge" style="{{ $wishlistCount > 0 ? '' : 'display:none;' }}">{{ $wishlistCount }}</span>
                    </button>
                    <a href="{{ route('cart') }}" class="icon-btn1" aria-label="Cart">
                        <img src="{{ asset('images/local_mall.svg') }}" alt="" width="14" height="20" />
                        <span class="cart-count cart-count-badge" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
                    </a>
                    @auth
                        <button class="icon-btn" type="button" aria-label="Profile"
                            onclick="window.location.href='{{ route('my-account') }}'">
                            <img id="headerProfilePic" src="{{ Auth::user()->profile_picture ? asset('uploads/'.Auth::user()->profile_picture) : asset('images/user-avatar.svg') }}" 
                                 alt="Profile" width="22" height="22" style="border-radius: 50%; object-fit: cover;">
                        </button>
                    @else
                        <button class="login-btn" type="button" onclick="window.location.href='{{ route('login') }}'">Sign in /
                            Login</button>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <nav class="nav-bar" aria-label="Primary">
        <div class="nav-inner">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
                <span class="hamburger-bar"></span>
            </button>
            <div class="nav-links" id="navLinks">
                <a href="{{ route('home') }}" class="nav-item">Home</a>

                <a href="{{ route('shop') }}" class="nav-item">Shop</a>
                
                    {{-- <div class="mobile-menu-header">
                        <a href="{{ route('home') }}">
                            <img src="{{ asset('images/image 1.png') }}" alt="Nandhini Silks" class="mobile-menu-logo">
                        </a>
                    </div> --}}
                @foreach($headerCategories as $category)
                    <div class="nav-item-wrapper">
                        <a href="{{ url('category/'.$category->slug) }}" class="nav-item @if($category->subCategories->count() > 0) nav-dropdown-toggle @endif">{{ $category->name }}</a>
                        @if($category->subCategories->count() > 0)
                            <div class="dropdown-content">
                                @foreach($category->subCategories as $subCategory)
                                    @if($subCategory->childCategories->count() > 0)
                                        <div class="has-children">
                                            <a href="{{ url('category/'.$category->slug.'/'.$subCategory->slug) }}">{{ $subCategory->name }}</a>
                                            <div class="child-dropdown">
                                                @foreach($subCategory->childCategories as $child)
                                                    <a href="{{ url('category/'.$category->slug.'/'.$subCategory->slug.'/'.$child->slug) }}">{{ $child->name }}</a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <a href="{{ url('category/'.$category->slug.'/'.$subCategory->slug) }}">{{ $subCategory->name }}</a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach


                <a href="{{ url('about') }}" class="nav-item">About</a>
                <a href="{{ url('contact') }}" class="nav-item">Contact us</a>
                </div>
            </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menuToggle');
            const navLinks = document.getElementById('navLinks');
            const mobileBreakpoint = 768;

            if (!menuToggle || !navLinks) {
                return;
            }

            const closeMenu = () => {
                navLinks.classList.remove('active');
                menuToggle.classList.remove('active');
                menuToggle.setAttribute('aria-expanded', 'false');
                document.body.classList.remove('menu-open');

                document.querySelectorAll('.nav-item-wrapper.mobile-open, .has-children.mobile-open').forEach(item => {
                    item.classList.remove('mobile-open');
                });
            };

            const openMenu = () => {
                navLinks.classList.add('active');
                menuToggle.classList.add('active');
                menuToggle.setAttribute('aria-expanded', 'true');
                document.body.classList.add('menu-open');
            };

            menuToggle.setAttribute('aria-expanded', 'false');

            menuToggle.addEventListener('click', () => {
                if (navLinks.classList.contains('active')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

//             const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
//             dropdownToggles.forEach(toggle => {
//                 toggle.addEventListener('click', (e) => {
//                     if (window.innerWidth <= mobileBreakpoint) {
//                         const parent = toggle.parentElement;
//                         // Only prevent default if it has a dropdown
//                         if (parent.querySelector('.dropdown-content')) {
//                             e.preventDefault();
//                             parent.classList.toggle('mobile-open');
//                         }
//                     }
//                 });
//             });

//             // Mobile Child Dropdown Toggles
//             const hasChildrenLinks = document.querySelectorAll('.has-children > a');
//             hasChildrenLinks.forEach(link => {
//                 link.addEventListener('click', (e) => {
//                     if (window.innerWidth <= mobileBreakpoint) {
//                         e.preventDefault();
//                         const parent = link.parentElement;
//                         parent.classList.toggle('mobile-open');
//                     }
//                 });
//             });

            document.addEventListener('click', (e) => {
                if (window.innerWidth <= mobileBreakpoint &&
                    navLinks.classList.contains('active') &&
                    !navLinks.contains(e.target) &&
                    !menuToggle.contains(e.target)) {
                    closeMenu();
                }
            });

            navLinks.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', () => {
                    if (window.innerWidth <= mobileBreakpoint &&
                        !link.classList.contains('nav-dropdown-toggle') &&
                        !link.parentElement.classList.contains('has-children')) {
                        closeMenu();
                    }
                });
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth > mobileBreakpoint) {
                    closeMenu();
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const mobileBreakpoint = 768;
            const accountSidebars = document.querySelectorAll('.account-page .account-sidebar');

            if (!accountSidebars.length) {
                return;
            }

            accountSidebars.forEach((sidebar, index) => {
                const existingToggle = sidebar.querySelector('.account-sidebar-toggle');

                if (!existingToggle) {
                    const toggle = document.createElement('button');
                    toggle.type = 'button';
                    toggle.className = 'account-sidebar-toggle';
                    toggle.setAttribute('aria-expanded', 'false');
                    toggle.setAttribute('aria-controls', `accountSidebarNav${index}`);
                    toggle.innerHTML = '<span>My Account Menu</span><span class="account-sidebar-toggle-icon"><i class="fa-solid fa-chevron-down"></i></span>';
                    const userInfo = sidebar.querySelector('.account-user-info');
                    if (userInfo && userInfo.nextSibling) {
                        sidebar.insertBefore(toggle, userInfo.nextSibling);
                    } else if (userInfo) {
                        sidebar.appendChild(toggle);
                    } else {
                        sidebar.insertBefore(toggle, sidebar.firstChild);
                    }
                }

                const nav = sidebar.querySelector('.account-nav');
                if (nav) {
                    nav.id = nav.id || `accountSidebarNav${index}`;
                }
            });

            const syncAccountSidebarState = () => {
                const isMobile = window.innerWidth <= mobileBreakpoint;

                accountSidebars.forEach((sidebar) => {
                    const toggle = sidebar.querySelector('.account-sidebar-toggle');
                    if (!toggle) {
                        return;
                    }

                    sidebar.classList.toggle('account-sidebar-collapsible', isMobile);

                    if (isMobile) {
                        const isOpen = sidebar.classList.contains('open');
                        toggle.hidden = false;
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    } else {
                        toggle.hidden = true;
                        sidebar.classList.remove('open');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
            };

            document.addEventListener('click', (event) => {
                const toggle = event.target.closest('.account-sidebar-toggle');

                if (!toggle) {
                    return;
                }

                const sidebar = toggle.closest('.account-sidebar');
                if (!sidebar || window.innerWidth > mobileBreakpoint) {
                    return;
                }

                const isOpen = sidebar.classList.toggle('open');
                toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            });

            window.addEventListener('resize', syncAccountSidebarState);
            syncAccountSidebarState();
        });
    </script>

    <main>
        @yield('content')
    </main>

    <footer class="site-footer" aria-label="Footer">
        <div class="footer-inner">
          <h2 class="footer-title">Contact us</h2>
          <p class="footer-address">Nandhini Silks <br>416/9 Aranmanai Street, S.V. Nagaram <br>Arni - 632317,
            Thiruvannamalai dist</p>

          <div class="footer-contact-grid">
            <div class="footer-contact-item">
              <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/telephone.svg') }}" alt=""></span>
              <p class="footer-contact-text">+91 96295 52822</p>
            </div>
            <div class="footer-contact-item">
              <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/telephone.svg') }}" alt=""></span>
              <p class="footer-contact-text">+91 99945 04410</p>
            </div>
            <div class="footer-contact-item">
              <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/email.svg') }}" alt=""></span>
              <p class="footer-contact-text">noreply@nandhinisilks.com</p>
            </div>
          </div>

          <div class="footer-extra-touch">
            <div class="footer-extra-title">Get In Touch</div>
            <div class="footer-extra-icons">
              <div class="footer-extra-box">
                <div class="footer-extra-glyph"><a href=""><img src="{{ asset('images/Vector4.svg') }}" alt=""></a></div>
              </div>
              <div class="footer-extra-box-1"><a href=""><img src="{{ asset('images/Group.svg') }}" alt=""></a></div>
            </div>
          </div>
        </div>

        <div class="footer-bottom">
          <div class="footer-bottom-inner">
            <ul class="footer-links">
              <li><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
              <li><a href="{{ url('exchange-policy') }}">Exchange Policy</a></li>
              <li><a href="{{ url('shipping-policy') }}">Shipping Policy</a></li>
              <li><a href="{{ url('terms-conditions') }}">Terms of Service</a></li>
              <li><a href="{{ url('fabric-care') }}">Fabric Care</a></li>
              <li><a href="{{ url('cancellation') }}">Cancellation</a></li>
            </ul>
          </div>
        </div>
        <p class="footer-copy">@ {{ date('Y') }} Nandhini Silks | By Reality Graphics</p>
    </footer>
    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <button id="backToTop" class="back-to-top" title="Go to top">
        &#8593;
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backToTop = document.getElementById('backToTop');
            
            window.addEventListener('scroll', () => {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('visible');
                } else {
                    backToTop.classList.remove('visible');
                }
            });

            backToTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });

            // Global Wishlist Logic
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.wishlist-btn');
                if (btn) {
                    e.preventDefault();
                    e.stopPropagation();
                    @guest
                        window.location.href = '{{ route("login") }}';
                        return;
                    @endguest
                    const productId = btn.getAttribute('data-product-id');
                    const svg = btn.querySelector('svg');
                    const icon = btn.querySelector('i');
                    
                    let isInWishlist = false;
                    if (svg) {
                        isInWishlist = btn.getAttribute('aria-pressed') === 'true';
                    } else if (icon) {
                        isInWishlist = icon.classList.contains('fa-solid');
                    }

                    const url = isInWishlist ? `{{ url('wishlist/remove') }}/${productId}` : `{{ url('wishlist/add') }}/${productId}`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.status === 419) {
                            // CSRF Token mismatch / Session expired
                            Swal.fire({
                                title: 'Session Expired',
                                text: 'Your session has expired. Please refresh the page to continue.',
                                icon: 'warning',
                                confirmButtonText: 'Refresh Page',
                                confirmButtonColor: '#A91B43'
                            }).then(() => {
                                window.location.reload();
                            });
                            throw new Error('CSRF token mismatch');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Update all buttons for this product
                            const allBtns = document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`);
                            allBtns.forEach(b => {
                                const s = b.querySelector('svg');
                                const i = b.querySelector('i');
                                b.setAttribute('aria-pressed', isInWishlist ? 'false' : 'true');
                                if (s) {
                                    s.setAttribute('fill', isInWishlist ? '#FBF2D1' : '#927541');
                                    s.setAttribute('stroke', '#927541');
                                    s.setAttribute('stroke-width', '2.5');
                                }
                                if (i) {
                                    if (isInWishlist) {
                                        i.classList.replace('fa-solid', 'fa-regular');
                                    } else {
                                        i.classList.replace('fa-regular', 'fa-solid');
                                    }
                                }
                            });

                            if (typeof toastr !== 'undefined') {
                                toastr.success(data.message || (isInWishlist ? 'Removed from wishlist' : 'Added to wishlist'));
                            }

                            // Update Header Count
                            const wishlistCountBadges = document.querySelectorAll('.wishlist-count-badge');
                            if (wishlistCountBadges.length > 0) {
                                wishlistCountBadges.forEach(badge => {
                                    badge.textContent = data.count;
                                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                                });
                            }

                            // Specific logic for Wishlist Page: Remove card if on wishlist page
                            if (window.location.pathname.includes('/wishlist') && isInWishlist) {
                                const card = document.querySelector(`.product-card-v2[data-product-id="${productId}"]`);
                                if (card) {
                                    card.style.opacity = '0';
                                    card.style.transform = 'scale(0.9)';
                                    card.style.transition = 'all 0.3s ease';
                                    setTimeout(() => {
                                        card.remove();
                                        const grid = document.getElementById('wishlistGrid');
                                        const emptyState = document.getElementById('emptyState');
                                        if (grid && document.querySelectorAll('#wishlistGrid .product-card-v2').length === 0) {
                                            grid.style.display = 'none';
                                            if (emptyState) emptyState.style.display = 'block';
                                        }
                                    }, 300);
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    </script>
    <!-- Cart Drawer -->
    <div class="cart-drawer-overlay" id="cartOverlay"></div>
    <div class="cart-drawer" id="cartDrawer">
        <div class="cart-drawer-header">
            <h2 class="cart-drawer-title">Shopping Cart</h2>
            <button class="cart-drawer-close" id="closeCart">&times;</button>
        </div>
        <div class="cart-drawer-items" id="miniCartContent">
            <!-- Dynamic Content -->
            <div class="text-center py-10">
                <p>Loading your cart...</p>
            </div>
        </div>
        <div class="cart-drawer-footer" id="miniCartFooter">
            <div class="cart-drawer-summary">
                <span>Subtotal</span>
                <span id="miniCartSubtotal">&#8377;0</span>
            </div>
            <a href="{{ route('cart') }}" class="cart-drawer-btn cart-drawer-btn-primary">Cart</a>
            <a href="{{ route('checkout') }}" class="cart-drawer-btn cart-drawer-btn-secondary">Checkout</a>
        </div>
    </div>

    <script>
        const cartDrawer = document.getElementById('cartDrawer');
        const cartOverlay = document.getElementById('cartOverlay');
        const cartDrawerBtn = document.getElementById('cartDrawerBtn');
        const closeCart = document.getElementById('closeCart');

        function openDrawer() {
            cartDrawer.classList.add('active');
            cartOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            updateMiniCart();
        }

        function closeDrawer() {
            cartDrawer.classList.remove('active');
            cartOverlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        // if(cartDrawerBtn) cartDrawerBtn.addEventListener('click', openDrawer);
        if(closeCart) closeCart.addEventListener('click', closeDrawer);
        if(cartOverlay) cartOverlay.addEventListener('click', closeDrawer);

        // Global Cart Synchronization (Bidirectional across Tabs)
        window.addEventListener('storage', function(e) {
            if (e.key === 'nandhini_cart_updated') {
                // If cart was updated in another tab, refresh UI
                if (window.updateMiniCart) window.updateMiniCart();
                // If page has a specific sync function (like PDP), call it
                if (window.syncCartStateWithServer) window.syncCartStateWithServer();
                // If on cart page, maybe show a toast or refresh
                if (window.location.pathname === '{{ url("cart") }}') {
                    // We avoid full reload to not disrupt user, but we could re-fetch
                    if (window.refreshCartPage) window.refreshCartPage();
                }
            }
        });

        function notifyCartUpdate() {
            localStorage.setItem('nandhini_cart_updated', Date.now());
        }

        // Expose functions globally to be used by AJAX cart additions
        window.openCartDrawer = openDrawer;
        window.updateMiniCart = function() {
            const content = document.getElementById('miniCartContent');
            const subtotal = document.getElementById('miniCartSubtotal');
            if(!content) return;

            fetch('{{ url("cart/mini-cart") }}', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.items.length === 0) {
                    content.innerHTML = '<div class="text-center py-20"><img src="{{ asset("images/local_mall.svg") }}" style="width: 50px; opacity: 0.2; margin: 0 auto 15px;"><p style="color: #999;">Your cart is empty</p></div>';
                    const footer = document.getElementById('miniCartFooter');
                    if(footer) footer.style.display = 'none';
                } else {
                    const footer = document.getElementById('miniCartFooter');
                    if(footer) footer.style.display = 'block';
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <div class="cart-item-mini" style="position: relative;">
                                <img src="${item.image_url}" class="cart-item-mini-img" alt="${item.name}">
                                <div class="cart-item-mini-info">
                                    <a href="/product/${item.slug}" class="cart-item-mini-name">${item.name}</a>
                                    <div class="cart-item-mini-meta">
                                        ${item.display_attributes && item.display_attributes.length > 0 
                                            ? item.display_attributes.map(attr => `${attr.name}: ${attr.value}`).join(' | ') 
                                            : ''}
                                        <br>Qty: ${item.quantity}
                                    </div>
                                    <div class="cart-item-mini-price">&#8377;${(item.price * item.quantity).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</div>
                                </div>
                                <button onclick="removeMiniCartItem('${item.key}')" style="font-size: 17px; position: absolute; top: 0; right: 0; background: none; border: none; color: #ff4d4d; cursor: pointer; padding: 5px;">&times;</button>
                            </div>
                        `;
                    });
                    content.innerHTML = html;
                    if(subtotal) subtotal.textContent = '\u20B9' + data.subTotal.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
                
                // Sync main cart badges
                const cartCountBadges = document.querySelectorAll('.cart-count-badge');
                cartCountBadges.forEach(badge => {
                    badge.textContent = data.totalItems || 0;
                    badge.style.display = (data.totalItems > 0) ? 'inline-block' : 'none';
                });
            });
        };
        window.notifyCartUpdate = notifyCartUpdate;

        function removeItem(key) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Remove this item from your cart?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#A91B43',
                cancelButtonColor: '#111',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("{{ url('cart/remove') }}/" + key, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            toastr.success(data.message || 'Item removed.');
                            notifyCartUpdate(); // Trigger sync
                            // If on cart page, refresh or remove row surgically
                            if (window.location.pathname.includes('/cart')) {
                                // Full reload once is fine for removal, it doesn't pollute history like a form post does
                                window.location.reload();
                            } else if (window.updateMiniCart) {
                                window.updateMiniCart();
                            }
                        }
                    })
                    .catch(err => {
                        console.error('Removal error:', err);
                        window.location.reload();
                    });
                }
            });
        }

        function removeMiniCartItem(key) {
            Swal.fire({
                title: 'Remove item?',
                text: "Do you want to remove this item?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#A91B43',
                cancelButtonColor: '#111',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ url("cart/remove") }}/' + key, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (response.status === 419) {
                            Swal.fire({
                                title: 'Session Expired',
                                text: 'Your session has expired. Please refresh the page to continue.',
                                icon: 'warning',
                                confirmButtonText: 'Refresh Page',
                                confirmButtonColor: '#A91B43'
                            }).then(() => {
                                window.location.reload();
                            });
                            throw new Error('CSRF token mismatch');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if(data.success) {
                            toastr.success(data.message || 'Item removed.');
                            updateMiniCart();
                            notifyCartUpdate();
                            if(window.location.pathname === '{{ url("cart") }}') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Global AJAX Setup for jQuery
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global AJAX Error Handling for jQuery (Handle 419 Session Expired)
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            if (xhr.status === 419) {
                Swal.fire({
                    title: 'Session Expired',
                    text: 'Your session has expired. Please refresh the page to continue.',
                    icon: 'warning',
                    confirmButtonText: 'Refresh Page',
                    confirmButtonColor: '#A91B43'
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.20.0/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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
            function getFieldContainer(element) {
                return $(element).closest('.form-group, .mb-4, .mb-3, .checkout-field, .review-form-group');
            }

            function getFieldLabel(element) {
                const container = getFieldContainer(element);
                const label = container.find('label').first();
                const fallback = $(element).attr('placeholder') || $(element).attr('name') || 'this field';

                if (!label.length) {
                    return fallback.replace(/_/g, ' ').trim();
                }

                return label.text()
                    .replace(/\*/g, '')
                    .replace(/verified/ig, '')
                    .replace(/\s+/g, ' ')
                    .trim() || fallback.replace(/_/g, ' ').trim();
            }

            function setDefaultValidationMessages($field) {
                const type = ($field.attr('type') || '').toLowerCase();
                const name = ($field.attr('name') || '').toLowerCase();
                const label = getFieldLabel($field);

                if ($field.prop('required') && !$field.attr('data-msg-required')) {
                    $field.attr('data-msg-required', `Please enter ${label.toLowerCase()}.`);
                }

                if (type === 'email' && !$field.attr('data-msg-email')) {
                    $field.attr('data-msg-email', 'Please enter a valid email address.');
                }

                if ((type === 'tel' || name.includes('phone')) && !$field.attr('data-msg-digits')) {
                    $field.attr('data-msg-digits', 'Please enter a valid phone number.');
                }

                if ((name.includes('zip') || name.includes('pincode')) && !$field.attr('data-msg-digits')) {
                    $field.attr('data-msg-digits', 'Please enter a valid pincode.');
                }

                if ($field.attr('minlength') && !$field.attr('data-msg-minlength')) {
                    $field.attr('data-msg-minlength', `Please enter at least ${$field.attr('minlength')} characters for ${label.toLowerCase()}.`);
                }

                if ($field.attr('maxlength') && !$field.attr('data-msg-maxlength')) {
                    $field.attr('data-msg-maxlength', `Please enter no more than ${$field.attr('maxlength')} characters for ${label.toLowerCase()}.`);
                }

                if ($field.attr('data-rule-equalTo') && !$field.attr('data-msg-equalTo')) {
                    $field.attr('data-msg-equalTo', 'Please enter the same value again.');
                }
            }

            // Auto-add * to required labels
            $('input[required], select[required], textarea[required], input[data-rule-required="true"]').each(function() {
                var label = getFieldContainer(this).find('label').first();
                if (label.length) {
                    if (!label.find('.text-rose-500').length && !label.find('.text-red-500').length && label.text().indexOf('*') === -1) {
                        label.append('<span style="color: #a91b43; margin-left: 4px;">*</span>');
                    }
                }
            });

            // Initialize validation on forms with 'validate-form' class
            $('.validate-form').each(function() {
                $(this).find('input, select, textarea').each(function() {
                    setDefaultValidationMessages($(this));
                });

                $(this).validate({
                    errorElement: 'span',
                    errorClass: 'error-text',
                    ignore: ':hidden:not(select):not(textarea):not([type="hidden"])',
                    highlight: function(element) {
                        $(element).addClass('error-border');
                        getFieldContainer(element).addClass('has-error');
                    },
                    unhighlight: function(element) {
                        $(element).removeClass('error-border');
                        getFieldContainer(element).removeClass('has-error');
                    },
                    errorPlacement: function(error, element) {
                        const wrap = element.closest('.password-input-wrap');
                        if (wrap.length) {
                            error.insertAfter(wrap);
                        } else {
                            error.insertAfter(element);
                        }
                    },
                    invalidHandler: function(event, validator) {
                        // Scroll to first error
                        if (validator.errorList.length) {
                            $('html, body').animate({
                                scrollTop: $(validator.errorList[0].element).offset().top - 100
                            }, 500);
                        }
                    }
                });
            });
        });

        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "15000",
            "extendedTimeOut": "5000",
            "preventDuplicates": true,
            "newestOnTop": true
        };
        @if(session('status')) toastr.success("{{ session('status') }}"); @endif
        @if(session('success')) toastr.success("{{ session('success') }}"); @endif
        @if(session('error')) toastr.error("{{ session('error') }}"); @endif
        @if($errors->any()) @foreach($errors->all() as $error) toastr.error("{{ $error }}"); @endforeach @endif
    </script>
    @stack('scripts')
</body>

</html>
