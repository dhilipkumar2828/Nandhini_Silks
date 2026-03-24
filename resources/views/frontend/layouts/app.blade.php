<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Nandhini Silks')</title>
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
            font-size: 14px;
            font-weight: 600;
            color: #111;
            margin: 0 0 4px;
            text-decoration: none;
            display: block;
        }

        .cart-item-mini-meta {
            font-size: 12px;
            color: #888;
            margin-bottom: 8px;
        }

        .cart-item-mini-price {
            font-size: 15px;
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
            font-size: 16px;
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

        /* --- Modern Premium Footer Styles --- */
        .site-footer {
            background-color: #111111 !important;
            color: #ffffff;
            font-family: 'Plus Jakarta Sans', sans-serif;
            padding: 80px 0 0;
            margin-top: 60px;
        }

        .footer-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1.2fr;
            gap: 60px;
        }

        .footer-brand .footer-logo {
            height: 60px;
            margin-bottom: 25px;
            object-fit: contain;
        }

        .footer-brand p {
            color: #999;
            line-height: 1.8;
            font-size: 0.95rem;
            margin-bottom: 30px;
            max-width: 320px;
        }

        .footer-heading {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 30px;
            position: relative;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -10px;
            width: 40px;
            height: 2px;
            background-color: #A91B43;
        }

        .footer-links-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links-list li {
            margin-bottom: 15px;
        }

        .footer-links-list a {
            color: #999;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .footer-links-list a:hover {
            color: #A91B43;
            padding-left: 8px;
        }

        .footer-contact-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
        }

        .contact-icon-box {
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.05);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.3s ease;
        }

        .contact-item:hover .contact-icon-box {
            background: #A91B43;
            transform: translateY(-3px);
        }

        .contact-icon-box i {
            font-size: 16px;
            color: #ffffff;
        }

        .contact-icon-box img {
            display: none; /* Hide old SVGs if any */
        }

        .contact-text-box h4 {
            color: #fff;
            font-size: 0.85rem;
            margin: 0 0 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .contact-text-box p {
            color: #999;
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.4;
        }

        .footer-socials {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }

        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            background: #A91B43;
            transform: rotate(360deg);
        }

        .social-link i {
            color: #ffffff;
            font-size: 16px;
        }

        .social-link img {
            display: none;
        }

        .footer-bottom {
            margin-top: 80px;
            padding: 30px 0;
            border-top: 1px solid rgba(255,255,255,0.05);
            text-align: center;
        }

        .footer-bottom-flex {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .copyright-text {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }

        .payment-icons {
            display: flex;
            gap: 15px;
            opacity: 0.6;
        }

        @media (max-width: 1200px) {
            .footer-container { grid-template-columns: 1fr 1fr; gap: 40px; }
        }

        @media (max-width: 768px) {
            .footer-container { grid-template-columns: 1fr; gap: 50px; text-align: center; }
            .footer-heading::after { left: 50%; transform: translateX(-50%); }
            .footer-brand p { margin: 0 auto 30px; }
            .footer-socials { justify-content: center; }
            .contact-item { flex-direction: column; align-items: center; }
            .footer-bottom-flex { justify-content: center; flex-direction: column; }
            .footer-links-list a:hover { padding-left: 0; font-weight: 600; }
        }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

</head>

<body>
    <header class="top-header">
        <div class="page-shell header-row">
            <a href="{{ route('home') }}" class="brand-link">
                <img class="brand" src="{{ asset('images/image 1.png') }}" alt="Logo" />
            </a>

            <div class="header-right">
                <div class="search-wrap">
                    <form action="{{ route('search') }}" method="GET" class="search-box">
                        <img src="{{ asset('images/search.svg') }}" alt="Search" />
                        <input type="text" name="q" placeholder="Search" aria-label="Search" value="{{ request('q') }}" />
                        <button type="submit" style="display: none;"></button>
                    </form>
                </div>

                <div class="actions">
                    <button class="icon-btn" type="button" aria-label="Favorites"
                        onclick="window.location.href='{{ route('wishlist') }}'">
                        <img src="{{ asset('images/favorite.svg') }}" alt="" width="18" height="23">
                        @if($wishlistCount > 0)
                            <span class="cart-count wishlist-count">{{ $wishlistCount }}</span>
                        @endif
                    </button>
                    <button class="icon-btn" type="button" aria-label="Cart" id="cartDrawerBtn">
                        <img src="{{ asset('images/local_mall.svg') }}" alt="" width="14" height="20" />
                        <span class="cart-count" style="{{ $cartCount > 0 ? '' : 'display:none;' }}">{{ $cartCount }}</span>
                    </button>
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
                <div class="mobile-menu-header">
                    <a href="{{ route('home') }}">
                        <img src="{{ asset('images/image 1.png') }}" alt="Nandhini Silks" class="mobile-menu-logo">
                    </a>
                </div>
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

            const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    if (window.innerWidth <= mobileBreakpoint) {
                        const parent = toggle.parentElement;
                        // Only prevent default if it has a dropdown
                        if (parent.querySelector('.dropdown-content')) {
                            e.preventDefault();
                            parent.classList.toggle('mobile-open');
                        }
                    }
                });
            });

            // Mobile Child Dropdown Toggles
            const hasChildrenLinks = document.querySelectorAll('.has-children > a');
            hasChildrenLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    if (window.innerWidth <= mobileBreakpoint) {
                        e.preventDefault();
                        const parent = link.parentElement;
                        parent.classList.toggle('mobile-open');
                    }
                });
            });

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
        <div class="footer-container">
            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/image 1.png') }}" alt="Nandhini Silks" class="footer-logo">
                </a>
                <p>Nandhini Silks embodies the timeless elegance of traditional craftsmanship. We bring you the finest sarees curated with passion and quality.</p>
                <div class="footer-socials">
                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                    <a href="https://wa.me/919994504410" class="social-link"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>

            <!-- Categories Column -->
            <div class="footer-nav">
                <h3 class="footer-heading">Categories</h3>
                <ul class="footer-links-list">
                    @foreach($headerCategories->take(6) as $category)
                        <li><a href="{{ url('category/'.$category->slug) }}">{{ $category->name }}</a></li>
                    @endforeach
                    <li><a href="{{ route('shop') }}">New Arrivals</a></li>
                </ul>
            </div>

            <!-- Support Column -->
            <div class="footer-nav">
                <h3 class="footer-heading">Customer Care</h3>
                <ul class="footer-links-list">
                    <li><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('exchange-policy') }}">Exchange Policy</a></li>
                    <li><a href="{{ url('shipping-policy') }}">Shipping Policy</a></li>
                    <li><a href="{{ url('terms-conditions') }}">Terms of Service</a></li>
                    <li><a href="{{ url('fabric-care') }}">Fabric Care</a></li>
                    <li><a href="{{ url('contact') }}">Contact Support</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="footer-nav">
                <h3 class="footer-heading">Store Location</h3>
                <div class="footer-contact-info">
                    <div class="contact-item">
                        <div class="contact-icon-box"><i class="fas fa-envelope"></i></div>
                        <div class="contact-text-box">
                            <h4>Email Us</h4>
                            <p>nandhinisilks.arani@gmail.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-box"><i class="fas fa-phone-alt"></i></div>
                        <div class="contact-text-box">
                            <h4>Call Us</h4>
                            <p>+91 96295 52822<br>+91 99945 04410</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <div class="contact-icon-box"><i class="fas fa-map-marker-alt"></i></div>
                        <div class="contact-text-box">
                            <h4>Our Address</h4>
                            <p>Nandhini Silks, 416/9 Aranmanai St,<br>S.V. Nagaram, Arni - 632317</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <div class="footer-bottom-flex">
                <p class="copyright-text">© {{ date('Y') }} Nandhini Silks. Crafted by Reality Graphics.</p>
                <div class="payment-icons">
                    <i class="fab fa-cc-visa text-2xl"></i>
                    <i class="fab fa-cc-mastercard text-2xl"></i>
                    <i class="fab fa-google-pay text-2xl"></i>
                    <i class="fab fa-apple-pay text-2xl"></i>
                </div>
            </div>
        </div>
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
                    @guest
                        toastr.info('Please login to save your wishlist.');
                        setTimeout(() => window.location.href = '{{ route("login") }}', 1000);
                        return;
                    @endguest
                    const productId = btn.getAttribute('data-product-id');
                    const svg = btn.querySelector('svg');
                    const icon = btn.querySelector('i');
                    
                    let isInWishlist = false;
                    if (svg) {
                        isInWishlist = svg.getAttribute('fill') === '#A91B43';
                    } else if (icon) {
                        isInWishlist = icon.classList.contains('fa-solid');
                    }

                    const url = isInWishlist ? `/wishlist/remove/${productId}` : `/wishlist/add/${productId}`;

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update all buttons for this product
                            const allBtns = document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"]`);
                            allBtns.forEach(b => {
                                const s = b.querySelector('svg');
                                const i = b.querySelector('i');
                                if (s) s.setAttribute('fill', isInWishlist ? '#666' : '#A91B43');
                                if (i) {
                                    if (isInWishlist) {
                                        i.classList.replace('fa-solid', 'fa-regular');
                                    } else {
                                        i.classList.replace('fa-regular', 'fa-solid');
                                    }
                                }
                            });

                            // Update Header Count
                            const wishlistCountBadges = document.querySelectorAll('.wishlist-count');
                            if (wishlistCountBadges.length > 0) {
                                wishlistCountBadges.forEach(badge => {
                                    badge.textContent = data.count;
                                    badge.style.display = data.count > 0 ? 'inline-block' : 'none';
                                });
                            } else if (data.count > 0) {
                                window.location.reload();
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
                <span id="miniCartSubtotal">₹0</span>
            </div>
            <a href="{{ route('cart') }}" class="cart-drawer-btn cart-drawer-btn-primary">View Full Cart</a>
            <a href="{{ route('checkout') }}" class="cart-drawer-btn cart-drawer-btn-secondary">Checkout Now</a>
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

        if(cartDrawerBtn) cartDrawerBtn.addEventListener('click', openDrawer);
        if(closeCart) closeCart.addEventListener('click', closeDrawer);
        if(cartOverlay) cartOverlay.addEventListener('click', closeDrawer);

        function updateMiniCart() {
            const content = document.getElementById('miniCartContent');
            const subtotal = document.getElementById('miniCartSubtotal');
            
            fetch('/cart/mini-cart', {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                if(data.items.length === 0) {
                    content.innerHTML = '<div class="text-center py-20"><img src="{{ asset("images/local_mall.svg") }}" style="width: 50px; opacity: 0.2; margin: 0 auto 15px;"><p style="color: #999;">Your cart is empty</p></div>';
                    document.getElementById('miniCartFooter').style.display = 'none';
                } else {
                    document.getElementById('miniCartFooter').style.display = 'block';
                    let html = '';
                    data.items.forEach(item => {
                        html += `
                            <div class="cart-item-mini" style="position: relative;">
                                <img src="${item.image_url}" class="cart-item-mini-img" alt="${item.name}">
                                <div class="cart-item-mini-info">
                                    <a href="/product/${item.slug}" class="cart-item-mini-name">${item.name}</a>
                                    <div class="cart-item-mini-meta">
                                        ${item.size ? 'Size: ' + item.size : ''} 
                                        ${item.color ? ' | Color: ' + item.color : ''}
                                        <br>Qty: ${item.quantity}
                                    </div>
                                    <div class="cart-item-mini-price">₹${item.price.toLocaleString()}</div>
                                </div>
                                <button onclick="removeMiniCartItem('${item.key}')" style="position: absolute; top: 0; right: 0; background: none; border: none; color: #ff4d4d; cursor: pointer; font-size: 16px; padding: 5px;">&times;</button>
                            </div>
                        `;
                    });
                    content.innerHTML = html;
                    subtotal.textContent = '₹' + data.subTotal.toLocaleString();
                }
                
                // Sync main cart badges
                const cartCountBadges = document.querySelectorAll('.cart-count');
                cartCountBadges.forEach(badge => {
                    badge.textContent = data.totalItems || 0;
                    badge.style.display = (data.totalItems > 0) ? 'inline-block' : 'none';
                });
            });
        }

        // Expose openDrawer globally to be used by AJAX cart additions
        window.openCartDrawer = openDrawer;

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
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.location.origin + "/cart/remove/" + key;
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = "{{ csrf_token() }}";
                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
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
                    fetch('/cart/remove/' + key, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if(data.success) {
                            toastr.success(data.message || 'Item removed.');
                            updateMiniCart();
                            if(window.location.pathname === '/cart') {
                                window.location.reload();
                            }
                        }
                    });
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
            // Auto-add * to required labels
            $('input[required], select[required], textarea[required], input[data-rule-required="true"]').each(function() {
                var label = $(this).closest('.form-group, .mb-4, .mb-3, .checkout-field').find('label').first();
                if (label.length) {
                    if (!label.find('.text-rose-500').length && !label.find('.text-red-500').length && label.text().indexOf('*') === -1) {
                        label.append('<span style="color: #a91b43; margin-left: 4px;">*</span>');
                    }
                }
            });

            // Initialize validation on forms with 'validate-form' class
            $('.validate-form').each(function() {
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
                        error.insertAfter(element);
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

        toastr.options = {"closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000"};
        @if(session('success')) toastr.success("{{ session('success') }}"); @endif
        @if(session('error')) toastr.error("{{ session('error') }}"); @endif
        @if($errors->any()) @foreach($errors->all() as $error) toastr.error("{{ $error }}"); @endforeach @endif
    </script>
    @stack('scripts')
</body>

</html>
