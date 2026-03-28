@extends('frontend.layouts.app')

@section('title', ($category->name ?? 'Shop') . ' - Nandhini Silks')

@section('content')
    <main class="category-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <span>{{ $category->name }}</span>
            </div>

            <button type="button" class="mobile-filter-toggle" id="mobileFilterToggle" aria-expanded="false" aria-controls="filtersSidebar">
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
                    <form id="filterForm" action="{{ request()->url() }}" method="GET">
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="filter-group-content price-range-container">
                                <div class="slider-track-modern">
                                    <div class="slider-fill-modern" id="sliderFill"></div>
                                    <input type="range" name="min_price" id="min_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('min_price', $filterData['min_price']) }}" class="range-slider-modern">
                                    <input type="range" name="max_price" id="max_price_input" min="{{ $filterData['min_price'] }}" max="{{ $filterData['max_price'] }}" value="{{ request('max_price', $filterData['max_price']) }}" class="range-slider-modern">
                                </div>
                                <div class="range-values-modern">
                                    <span class="price-val">₹<span id="min_price_val">{{ number_format(request('min_price', $filterData['min_price']), 0) }}</span></span>
                                    <span class="price-separator">-</span>
                                    <span class="price-val">₹<span id="max_price_val">{{ number_format(request('max_price', $filterData['max_price']), 0) }}</span></span>
                                </div>
                            </div>
                        </div>

                        @if(isset($filterData['sub_categories']) && $filterData['sub_categories']->isNotEmpty())
                        <div class="filter-group">
                            <h3 class="filter-title">Sub-Category</h3>
                            <ul class="filter-group-content filter-list">
                                @foreach($filterData['sub_categories'] as $subCat)
                                    <li class="filter-item">
                                        <label class="custom-checkbox">
                                            <input type="checkbox" name="sub_categories[]" value="{{ $subCat->id }}"
                                                {{ in_array($subCat->id, (array)request('sub_categories', [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                            <span class="checkmark"></span>
                                            <span class="label-text">{{ $subCat->name }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @foreach($filterData['attributes'] as $attr)
                            @if($attr->values->isNotEmpty())
                                <div class="filter-group">
                                    <h3 class="filter-title">{{ $attr->name }}</h3>
                                    @if(strtolower($attr->name) == 'color')
                                        <div class="filter-group-content color-swatches-grid-modern">
                                            @foreach($attr->values as $val)
                                                @php
                                                    $swatch = $val->swatch_value;
                                                    $isHex = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                                    $active = in_array($val->id, (array)request('attr.'.$attr->id, []));
                                                @endphp
                                                <label class="swatch-container-modern" title="{{ $val->name }}">
                                                    <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}" 
                                                        {{ $active ? 'checked' : '' }} onchange="this.form.submit()">
                                                    @php
                                                        $bgStyle = '#eee';
                                                        if($swatch) {
                                                            $bgStyle = $isHex ? $swatch : 'url('.asset('uploads/'.$swatch).') center/cover';
                                                        } else {
                                                            // Fallback to name if it's a single word (likely a color name)
                                                            if(preg_match('/^[a-zA-Z]+$/', $val->name)) $bgStyle = strtolower($val->name);
                                                        }
                                                    @endphp
                                                    <span class="swatch-circle-modern" style="background: {{ $bgStyle }};"></span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <ul class="filter-group-content filter-list">
                                            @foreach($attr->values as $val)
                                                <li class="filter-item">
                                                    <label class="custom-checkbox">
                                                        <input type="checkbox" name="attr[{{ $attr->id }}][]" value="{{ $val->id }}"
                                                            {{ in_array($val->id, (array)request('attr.'.$attr->id, [])) ? 'checked' : '' }} onchange="this.form.submit()">
                                                        <span class="checkmark"></span>
                                                        <span class="label-text">{{ $val->name }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        @endforeach

                        <div class="filter-group">
                            <label class="stock-toggle-modern">
                                <span class="toggle-label">In Stock Only</span>
                                <div class="toggle-container">
                                    <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                                    <span class="toggle-slider"></span>
                                </div>
                            </label>
                        </div>
                        
                        <div class="filter-actions mt-4">
                            <button type="submit" class="apply-filters-btn-modern">Apply Filters</button>
                            <a href="{{ request()->url() }}" class="clear-filters-link">Clear All</a>
                        </div>
                    </form>
                </aside>

                <!-- Product Listing -->
                @include('frontend.partials.product-listing', ['products' => $products, 'category' => $category])
            </div>
        </div>
    </main>
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

            sliderFill.style.left = minPercent + '%';
            sliderFill.style.width = (maxPercent - minPercent) + '%';
            
            minVal.innerText = min.toLocaleString();
            maxVal.innerText = max.toLocaleString();
        }

        if(minInput && maxInput) {
            minInput.addEventListener('input', updateSlider);
            maxInput.addEventListener('input', updateSlider);
            minInput.addEventListener('change', () => minInput.form.submit());
            maxInput.addEventListener('change', () => maxInput.form.submit());
            updateSlider(); // Initial call
        }

        /* Mobile Filter System */
        const mobileFilterToggle = document.getElementById('mobileFilterToggle');
        const filtersSidebar = document.getElementById('filtersSidebar');
        const mobileFilterOverlay = document.getElementById('mobileFilterOverlay');
        const filterDrawerClose = document.getElementById('filterDrawerClose');
        const mobileFilterToggleIcon = document.querySelector('.mobile-filter-toggle-icon');

        if (mobileFilterToggle && filtersSidebar) {
            const closeFilters = () => {
                filtersSidebar.classList.remove('mobile-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '+';
                mobileFilterToggle.setAttribute('aria-expanded', 'false');
            };

            const openFilters = () => {
                filtersSidebar.classList.add('mobile-open');
                if (mobileFilterToggleIcon) mobileFilterToggleIcon.textContent = '−';
                mobileFilterToggle.setAttribute('aria-expanded', 'true');
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

            document.querySelectorAll('.filter-group .filter-title').forEach((title) => {
                title.addEventListener('click', () => {
                    if (window.innerWidth > 1024) return;
                    const group = title.closest('.filter-group');
                    if (!group) return;
                    group.classList.toggle('is-open');
                });
            });

            if (window.innerWidth <= 1024) {
                const firstGroup = filtersSidebar.querySelector('.filter-group');
                firstGroup?.classList.add('is-open');
            }
        }

        function toggleAttr(groupId, valueId) {
            const container = document.getElementById('attr_' + groupId + '_inputs');
            let input = container?.querySelector('input[value="' + valueId + '"]');
            if (input) {
                input.remove();
            } else if(container) {
                input = document.createElement('input');
                input.type = 'checkbox';
                input.name = 'attr[' + groupId + '][]';
                input.value = valueId;
                input.checked = true;
                container.appendChild(input);
            }
        }
    </script>
    <style>
        /* Mobile Overlay & Sidebar Refinements */
        .mobile-filter-overlay {
            display: none !important;
        }
        .mobile-filter-overlay.active { display: none !important; }
        
        .filter-drawer-header { display: none !important; }

        @media (max-width: 1024px) {
            .filters-sidebar {
                position: static;
                width: 100%;
                height: auto;
                background: #fff;
                padding: 16px;
                margin: 0 0 16px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.06);
                border: 1px solid rgba(169, 27, 67, 0.12);
                border-radius: 14px;
                overflow: hidden;
                transform: none;
                transition: none;
            }
            .filters-sidebar.mobile-open { display: block !important; }
            .filter-drawer-header {
                display: none !important;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 1px solid #f0f0f0;
            }
            .filter-drawer-title { font-size: 20px; font-weight: 700; margin: 0; }
            .filter-drawer-close { font-size: 28px; background: none; border: none; cursor: pointer; color: #666; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
            
            .filter-group .filter-title {
                position: relative;
                cursor: pointer;                margin-bottom: 0 !important;
                font-size: 16px;
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
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
        }
        .mobile-filter-toggle-icon {
            font-size: 20px;
            line-height: 1;
            transition: transform 0.3s ease;
        }
        .mobile-filter-toggle[aria-expanded="true"] .mobile-filter-toggle-icon {
            transform: rotate(45deg);
        }
        .filter-group {
            margin-bottom: 30px;
            border-bottom: 1px solid #eee;
            padding-bottom: 25px;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .filter-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #222;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .filter-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .filter-item {
            margin-bottom: 12px;
        }

        /* Custom Modern Checkbox */
        .custom-checkbox {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
            user-select: none;
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
            transition: all 0.3s ease;
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

        /* Color Swatches Grid */
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

        /* Stock Toggle Switch */
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

        /* Price Slider Modern */
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
            top: 0px;
            margin: 0;
            z-index: 2;
        }

        .range-slider-modern::-webkit-slider-runnable-track {
            background: transparent;
        }

        .range-slider-modern::-moz-range-track {
            background: transparent;
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
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
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

        /* Actions */
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
            .filters-sidebar {
                display: none !important;
            }
            .filters-sidebar.mobile-open {
                display: block !important;
            }
        }
        @media (max-width: 768px) {
            .product-listing-header {
                gap: 16px !important;
            }
            .product-listing-header > div:last-child {
                width: 100%;
                flex-direction: row;
                align-items: center !important;
                justify-content: space-between;
                gap: 10px;
                flex-wrap: nowrap;
            }
            .view-toggle {
                align-self: center;
                flex-shrink: 0;
            }
            .product-listing-header form {
                width: auto;
                margin-left: 0 !important;
                flex-shrink: 0;
            }
            .sort-select {
                width: auto;
                min-height: 38px;
                padding: 8px 12px;
                border-radius: 10px;
                font-size: 13px;
                background: #fff;
                margin: 0;
            }
        }
        @media (min-width: 769px) and (max-width: 1024px) {
            .mobile-filter-toggle {
                display: none !important;
            }
            .category-layout {
                grid-template-columns: 280px 1fr !important;
                gap: 30px !important;
            }
            .filters-sidebar {
                display: block !important;
                width: auto !important;
                padding: 25px !important;
                margin-bottom: 0 !important;
            }
            .product-grid-main:not(.view-list) {
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 25px !important;
            }
            .product-grid-main:not(.view-list) .product-card-v2 {
                padding: 12px !important;
            }
            .product-grid-main:not(.view-list) .product-image-v2 {
                aspect-ratio: 3 / 4 !important;
                margin-bottom: 10px !important;
            }
            .product-grid-main:not(.view-list) .product-name-v2 {
                font-size: inherit !important;
            }
            .product-grid-main:last-child {
                margin-bottom: 0 !important;
            }
        }
    </style>
@endpush
