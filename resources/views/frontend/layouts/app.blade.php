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
    @stack('styles')

</head>

<body>
    <header class="top-header">
        <div class="page-shell header-row">
            <a href="{{ url('/') }}" class="brand-link">
                <img class="brand" src="{{ asset('images/image 1.png') }}" alt="Logo" />
            </a>

            <div class="header-right">
                <div class="search-wrap">
                    <div class="search-box">
                        <img src="{{ asset('images/search.svg') }}" alt="Search" />
                        <input type="text" placeholder="Search" aria-label="Search" />
                    </div>
                </div>

                <div class="actions">
                    <button class="icon-btn" type="button" aria-label="Favorites"
                        onclick="window.location.href='{{ url('wishlist') }}'">
                        <img src="{{ asset('images/favorite.svg') }}" alt="" width="18" height="23">
                    </button>
                    <button class="icon-btn" type="button" aria-label="Cart"
                        onclick="window.location.href='{{ url('cart') }}'">
                        <img src="{{ asset('images/local_mall.svg') }}" alt="" width="14" height="20" />
                        <span class="cart-count">5</span>
                    </button>
                    <button class="login-btn" type="button" onclick="window.location.href='{{ route('login') }}'">Sign in /
                        Login</button>
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
                <div class="nav-item-wrapper">
                    <a href="{{ url('sarees') }}" class="nav-item nav-dropdown-toggle">Sarees</a>
                    <div class="dropdown-content">
                        <a href="{{ url('sarees') }}">All Sarees</a>
                        <div class="has-children">
                            <a href="#">Silk Sarees</a>
                            <div class="child-dropdown">
                                <a href="{{ url('sarees') }}">Kanchipuram Silk</a>
                                <a href="{{ url('sarees') }}">Banarasi Silk</a>
                                <a href="{{ url('sarees') }}">Soft Silk</a>
                            </div>
                        </div>
                        <a href="{{ url('sarees') }}">Cotton Sarees</a>
                        <a href="{{ url('sarees') }}">Fancy Sarees</a>
                        <a href="{{ url('sarees') }}">Wedding Collections</a>
                    </div>
                </div>

                <div class="nav-item-wrapper">
                    <a href="{{ url('women') }}" class="nav-item nav-dropdown-toggle">Women</a>
                    <div class="dropdown-content">
                        <a href="{{ url('women') }}">All Clothing</a>
                        <div class="has-children">
                            <a href="#">Ethnic Wear</a>
                            <div class="child-dropdown">
                                <a href="{{ url('women') }}">Kurtas</a>
                                <a href="{{ url('women') }}">Lehengas</a>
                                <a href="{{ url('women') }}">Salwar Suits</a>
                            </div>
                        </div>
                        <a href="{{ url('women') }}">Ready Made</a>
                        <a href="{{ url('women') }}">Dress Materials</a>
                    </div>
                </div>

                <div class="nav-item-wrapper">
                    <a href="{{ url('mens') }}" class="nav-item nav-dropdown-toggle">Mens</a>
                    <div class="dropdown-content">
                        <a href="{{ url('mens') }}">Shirts</a>
                        <a href="{{ url('mens') }}">Dhotis</a>
                        <a href="{{ url('mens') }}">Ethnic Wear</a>
                        <div class="has-children">
                            <a href="#">Wedding Wear</a>
                            <div class="child-dropdown">
                                <a href="{{ url('mens') }}">Silk Shirts</a>
                                <a href="{{ url('mens') }}">Pattu Dhotis</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nav-item-wrapper">
                    <a href="{{ url('kids') }}" class="nav-item nav-dropdown-toggle">Kids</a>
                    <div class="dropdown-content">
                        <a href="{{ url('kids') }}">Boys Wear</a>
                        <a href="{{ url('kids') }}">Girls Wear</a>
                        <a href="{{ url('kids') }}">Pattu Paavadai</a>
                    </div>
                </div>

                <a href="{{ url('about') }}" class="nav-item">About</a>
                <a href="{{ url('contact') }}" class="nav-item">Contact us</a>
            </div>
        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const menuToggle = document.getElementById('menuToggle');
            const navLinks = document.getElementById('navLinks');

            menuToggle.addEventListener('click', () => {
                navLinks.classList.toggle('active');
                menuToggle.classList.toggle('active');
            });

            const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768) {
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
                    if (window.innerWidth <= 768) {
                        e.preventDefault();
                        const parent = link.parentElement;
                        parent.classList.toggle('mobile-open');
                    }
                });
            });
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
                    <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/telephone.svg') }}"
                            alt=""></span>
                    <p class="footer-contact-text">+91 96295 52822</p>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/telephone.svg') }}"
                            alt=""></span>
                    <p class="footer-contact-text">+91 99945 04410</p>
                </div>
                <div class="footer-contact-item">
                    <span class="footer-extra-box-1" aria-hidden="true"><img src="{{ asset('images/email.svg') }}"
                            alt=""></span>
                    <p class="footer-contact-text">nandhinisilks.arani@gmail.com</p>
                </div>
            </div>

            <div class="footer-extra-touch">
                <div class="footer-extra-title">Get In Touch</div>
                <div class="footer-extra-icons">
                    <div class="footer-extra-box">
                        <div class="footer-extra-glyph"><a href=""><img src="{{ asset('images/Vector4.svg') }}"
                                    alt=""></a></div>
                    </div>
                    <div class="footer-extra-box-1"><a href=""><img src="{{ asset('images/Group.svg') }}" alt=""></a>
                    </div>
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
    <!-- Bootstrap 5.3 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
        });
    </script>
@stack('scripts')
</body>

</html>
