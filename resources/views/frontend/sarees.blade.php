@extends('frontend.layouts.app')

@section('title', ($category->name ?? 'Shop') . ' - Nandhini Silks')

@section('content')
    <main class="category-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>{{ $category->name }}</span>
            </div>

            <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle" aria-expanded="false" style="display: none;">
                <span>Filters</span>
                <span class="mobile-filter-toggle-icon">+</span>
            </button>

            <div class="mobile-filter-overlay" id="mobileFilterOverlay"></div>

            <div class="category-layout">
                <!-- Sidebar Filters -->
                <aside class="filters-sidebar" id="filtersSidebar">
                    <div class="filter-drawer-header">
                        <h3 class="filter-drawer-title">Filters</h3>
                        <button type="button" class="filter-drawer-close" id="filterDrawerClose" aria-label="Close filters">&times;</button>
                    </div>
                    <form action="{{ url()->current() }}" method="GET" id="filterForm">
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="filter-group-content price-range-container">
                                <div class="slider-track-modern">
                                    <div class="slider-fill-modern" id="sliderFill"></div>
                                    <input type="range" name="min_price" id="min_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('min_price', $filterData['min_price'] ?? 0) }}" class="range-slider-modern">
                                    <input type="range" name="max_price" id="max_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('max_price', $filterData['max_price'] ?? 50000) }}" class="range-slider-modern">
                                </div>
                                <div class="range-values-modern">
                                    <span class="price-val">₹<span id="min_price_val">{{ number_format(request('min_price', $filterData['min_price'] ?? 0), 0) }}</span></span>
                                    <span class="price-val">₹<span id="max_price_val">{{ number_format(request('max_price', $filterData['max_price'] ?? 50000), 0) }}</span></span>
                                </div>
                            </div>
                        </div>

                        <div class="filter-group">
                            <h3 class="filter-title">Category</h3>
                            <div class="filter-group-content">
                                <ul class="filter-list">
                                    @foreach($filterData['categories'] as $cat)
                                        <li class="filter-item">
                                            <label class="custom-checkbox">
                                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}" {{ in_array($cat->id, (array)request('categories', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                                <span class="checkmark"></span>
                                                <span class="label-text">{{ $cat->name }}</span>
                                            </label>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        @foreach($filterData['attributes'] as $attr)
                            @if($attr->values->isNotEmpty())
                                <div class="filter-group">
                                    <h3 class="filter-title">{{ $attr->name }}</h3>
                                    <div class="filter-group-content">
                                        @if(strtolower($attr->name) == 'color')
                                            <div class="color-swatches-grid-modern">
                                                @foreach($attr->values as $val)
                                                    @php
                                                        $swatch = $val->swatch_value;
                                                        $isHex = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                        $isChecked = in_array($val->id, (array)request('attr.'.$attr->id, []));
                                                    @endphp
                                                    <label class="swatch-container-modern" title="{{ $val->name }}">
                                                        <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" {{ $isChecked ? 'checked' : '' }} onchange="this.form.submit()">
                                                        @php
                                                            $bgStyle = '#eee';
                                                            if($swatch) {
                                                                $bgStyle = $isHex ? $swatch : 'url('.asset('uploads/'.$swatch).') center/cover';
                                                            } else {
                                                                if(preg_match('/^[a-zA-Z]+$/', $val->name)) $bgStyle = strtolower($val->name);
                                                            }
                                                        @endphp
                                                        <span class="swatch-circle-modern" style="background: {{ $bgStyle }};"></span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @else
                                            <ul class="filter-list">
                                                @foreach($attr->values as $val)
                                                    <li class="filter-item">
                                                        <label class="custom-checkbox">
                                                            <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" {{ in_array($val->id, (array)request('attr.'.$attr->id, [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                                            <span class="checkmark"></span>
                                                            <span class="label-text">{{ $val->name }}</span>
                                                        </label>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        <div class="filter-group mt-4">
                            <label class="stock-toggle-modern">
                                <span class="toggle-label">In Stock Only</span>
                                <div class="toggle-container">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </div>
                            </label>
                        </div>

                        <div class="filter-actions mt-5">
                            <button type="submit" class="apply-filters-btn-modern">Apply Filters</button>
                            <a href="{{ url()->current() }}" class="clear-filters-link">Clear All</a>
                        </div>
                    </form>
                </aside>

                @include('frontend.partials.product-listing', ['products' => $products, 'category' => $category])
            </div>
        </div>
    </main>

    <style>
        /* Modern Premium Sidebar Filters */
        .filters-sidebar {
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.03);
            border: 1px solid #f0f0f0;
            position: sticky;
            top: 20px;
        }

        .mobile-filter-toggle {
            display: none;
            width: 100%;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            margin-bottom: 16px;
            border: 1px solid rgba(169, 27, 67, 0.14);
            border-radius: 12px;
            background: #fff;
            color: #A91B43;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }

        .mobile-filter-toggle-icon {
            font-size: 20px;
            line-height: 1;
            transition: transform 0.3s ease;
        }

        .filter-group {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .filter-group:last-child {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }

        .mobile-filter-overlay {
            display: none;
        }

        .filter-drawer-header {
            display: none;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .filter-group-content {
            display: block;
        }

        .custom-checkbox {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            user-select: none;
            padding-top: 10px;
        }

        .custom-checkbox input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkmark {
            height: 20px;
            width: 20px;
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 6px;
            margin-right: 12px;
            flex-shrink: 0;
            transition: all 0.2s;
            position: relative;
        }

        .custom-checkbox:hover input ~ .checkmark {
            border-color: #A91B43;
        }

        .custom-checkbox input:checked ~ .checkmark {
            background-color: #A91B43;
            border-color: #A91B43;
        }

        .checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 6px;
            top: 2px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .label-text {
            font-size: 0.95rem;
            color: #555;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .custom-checkbox input:checked ~ .label-text {
            color: #222;
            font-weight: 600;
        }

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-item {
            margin-bottom: 12px;
        }

        .price-range-container {
            padding: 10px 5px;
        }

        .slider-track-modern {
            position: relative;
            width: 100%;
            height: 5px;
            background: #f0f0f0;
            margin: 25px 0;
            border-radius: 10px;
        }

        .slider-fill-modern {
            position: absolute;
            height: 100%;
            background: #A91B43;
            border-radius: 10px;
        }

        .range-slider-modern {
            position: absolute;
            width: 100%;
            pointer-events: none;
            appearance: none;
            height: 6px;
            background: none;
            outline: none;
            top: -8px;
            margin: 0;
        }

        .range-slider-modern::-webkit-slider-runnable-track {
            height: 8px;
            background: transparent;
            border-radius: 10px;
        }

        .range-slider-modern::-moz-range-track {
            height: 8px;
            background: transparent;
            border-radius: 10px;
        }

        .range-slider-modern::-webkit-slider-thumb {
            pointer-events: auto;
            -webkit-appearance: none;
            appearance: none;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            position: relative;
            z-index: 3;
        }

        .range-slider-modern::-moz-range-thumb {
            pointer-events: auto;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #A91B43;
            cursor: pointer;
            border: 2px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.18);
            position: relative;
            z-index: 3;
        }

        .range-values-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 10px;
        }

        .price-separator {
            display: none;
        }

        .price-val {
            font-size: 16px;
            font-weight: 700;
            color: #222;
            background: #f5f5f5;
            padding: 8px 16px;
            border-radius: 10px;
            display: inline-block;
        }

        .color-swatches-grid-modern {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .swatch-container-modern {
            cursor: pointer;
            position: relative;
        }

        .swatch-container-modern input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .swatch-circle-modern {
            display: block;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid transparent;
            box-shadow: 0 0 0 1px #eee;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
        }

        .swatch-container-modern:hover .swatch-circle-modern {
            transform: scale(1.1);
        }

        .swatch-container-modern input:checked ~ .swatch-circle-modern {
            border-color: #fff;
            box-shadow: 0 0 0 2px #A91B43;
            transform: scale(1.1);
        }

        .stock-toggle-modern {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            cursor: pointer;
        }

        .toggle-label {
            font-size: 1rem;
            font-weight: 700;
            color: #222;
        }

        .toggle-container {
            position: relative;
            width: 50px;
            height: 26px;
        }

        .toggle-container input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ddd;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        .toggle-container input:checked + .toggle-slider {
            background-color: #A91B43;
        }

        .toggle-container input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }

        .filter-actions {
            margin-top: 28px;
        }

        .apply-filters-btn-modern {
            width: 100%;
            padding: 14px;
            background: #A91B43;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(169, 27, 67, 0.2);
        }

        .apply-filters-btn-modern:hover {
            background: #8b1637;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(169, 27, 67, 0.3);
        }

        .clear-filters-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
        }

        @media (max-width: 1024px) {
            .mobile-filter-toggle {
                display: flex;
            }
            .mobile-filter-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.35);
                backdrop-filter: blur(2px);
                z-index: 9998;
            }

            .mobile-filter-overlay.active {
                display: block;
            }

            .filters-sidebar {
                display: block !important;
                position: fixed;
                top: 0;
                right: 0;
                width: min(390px, 100%);
                height: 100dvh;
                background: #fff;
                z-index: 9999;
                transform: translateX(100%);
                transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border-radius: 0;
                padding: 0;
                box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
                overflow-y: auto;
            }

            .filters-sidebar.mobile-open {
                transform: translateX(0);
            }

            .filter-drawer-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 20px;
                border-bottom: 1px solid #f1f5f9;
                position: sticky;
                top: 0;
                background: #fff;
                z-index: 10;
            }

            .filter-drawer-title {
                font-size: 1.25rem;
                font-weight: 700;
                color: #0f172a;
                margin: 0;
            }

            .filter-drawer-close {
                background: #f1f5f9;
                border: none;
                width: 36px;
                height: 36px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 22px;
                color: #64748b;
                cursor: pointer;
                transition: all 0.2s;
            }

            #filterForm {
                padding: 20px;
            }

            .filter-group .filter-title {
                position: relative;
                cursor: pointer;
                margin-bottom: 0 !important;
                font-size: 17px;
                border: none;
            }
            .filter-group .filter-title::after {
                content: '+';
                position: absolute;
                right: 0;
                color: #A91B43;
                font-weight: 400;
                font-size: 20px;
            }
            .filter-group.is-open .filter-title::after { content: '−'; }
            .filter-group-content { display: none; padding: 5px 0 15px; }
            .filter-group.is-open .filter-group-content { display: block; }
            body.filter-open { overflow: hidden; }
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Grid/List View Toggle Logic
        const viewBtns = document.querySelectorAll('.view-btn');
        const productContainer = document.getElementById('productListingGrid');

        viewBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                viewBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                if (btn.getAttribute('data-view') === 'list' || btn.title === 'List View') {
                    productContainer.classList.add('view-list');
                } else {
                    productContainer.classList.remove('view-list');
                }
            });
        });

        // Mobile Filter Toggle & Accordion Logic
        const mobileFilterToggle = document.getElementById('mobileFilterToggle');
        const filtersSidebar = document.getElementById('filtersSidebar');
        const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
        const filterDrawerClose = document.getElementById('filterDrawerClose');
        const mobileFilterToggleIcon = document.querySelector('.mobile-filter-toggle-icon');

        if (mobileFilterToggle && filtersSidebar) {
            const closeFilters = () => {
                filtersSidebar.classList.remove('mobile-open');
                mobileFilterOverlay?.classList.remove('active');
                document.body.classList.remove('filter-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '+';
            };

            const openFilters = () => {
                filtersSidebar.classList.add('mobile-open');
                mobileFilterOverlay?.classList.add('active');
                document.body.classList.add('filter-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '−';
            };

            mobileFilterToggle.addEventListener('click', () => {
                if (filtersSidebar.classList.contains('mobile-open')) {
                    closeFilters();
                } else {
                    openFilters();
                }
            });

            mobileFilterOverlay?.addEventListener('click', closeFilters);
            filterDrawerClose?.addEventListener('click', closeFilters);

            // Accordion Logic
            document.querySelectorAll('.filter-group .filter-title').forEach((title) => {
                title.addEventListener('click', () => {
                    if (window.innerWidth > 1024) return;
                    const group = title.closest('.filter-group');
                    if (!group) return;
                    group.classList.toggle('is-open');
                });
            });

            // Open first group by default on mobile
            if (window.innerWidth <= 1024) {
                const firstGroup = filtersSidebar.querySelector('.filter-group');
                firstGroup?.classList.add('is-open');
            }
        }

        // Price range display logic with fill update
        const minInput = document.getElementById('min_price_input');
        const maxInput = document.getElementById('max_price_input');
        const minVal = document.getElementById('min_price_val');
        const maxVal = document.getElementById('max_price_val');
        const sliderFill = document.getElementById('sliderFill');

        function updateSlider() {
            const min = parseInt(minInput.value);
            const max = parseInt(maxInput.value);
            
            const minPercent = ((min - minInput.min) / (minInput.max - minInput.min)) * 100;
            const maxPercent = ((max - minInput.min) / (minInput.max - minInput.min)) * 100;

            if(sliderFill) {
                sliderFill.style.left = minPercent + '%';
                sliderFill.style.width = (maxPercent - minPercent) + '%';
            }
            
            if(minVal) minVal.innerText = min.toLocaleString();
            if(maxVal) maxVal.innerText = max.toLocaleString();
        }

        if(minInput && maxInput) {
            minInput.addEventListener('input', updateSlider);
            maxInput.addEventListener('input', updateSlider);
            updateSlider(); // Initial call
        }
    </script>
@endpush
