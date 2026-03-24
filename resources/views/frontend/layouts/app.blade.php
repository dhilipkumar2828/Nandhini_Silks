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
                        @php
                            $wishlistCount = count(session('wishlist', []));
                        @endphp
                        @if($wishlistCount > 0)
                            <span class="cart-count wishlist-count">{{ $wishlistCount }}</span>
                        @endif
                    </button>
                    <button class="icon-btn" type="button" aria-label="Cart"
                        onclick="window.location.href='{{ route('cart') }}'">
                        <img src="{{ asset('images/local_mall.svg') }}" alt="" width="14" height="20" />
                        @php
                            $cartCount = collect(session('cart', []))->sum('quantity');
                        @endphp
                        <span class="cart-count">{{ $cartCount }}</span>
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

            // Global Wishlist Logic
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.wishlist-btn');
                if (btn) {
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        toastr.options = {"closeButton": true, "progressBar": true, "positionClass": "toast-top-right", "timeOut": "5000"};
        @if(session('success')) toastr.success("{{ session('success') }}"); @endif
        @if(session('error')) toastr.error("{{ session('error') }}"); @endif
        @if($errors->any()) @foreach($errors->all() as $error) toastr.error("{{ $error }}"); @endforeach @endif
    </script>
    @stack('scripts')
</body>

</html>
