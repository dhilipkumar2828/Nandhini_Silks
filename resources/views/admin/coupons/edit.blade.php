@extends('admin.layouts.admin')

@section('title', 'Edit Coupon')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Edit Coupon</h2>
            <p class="text-xs text-slate-400 font-medium">Update coupon rules and limits</p>
        </div>
        <a href="{{ route('admin.coupons.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">Back to Coupons</a>
    </div>

    <form method="POST" action="{{ route('admin.coupons.update', $coupon->id) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Coupon Code</label>
                <input type="text" name="code" value="{{ old('code', $coupon->code) }}" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Discount Type</label>
                <select name="type" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    <option value="percentage" {{ old('type', $coupon->type) === 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ old('type', $coupon->type) === 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Discount Value</label>
                <input type="number" name="discount_value" step="0.01" value="{{ old('discount_value', $coupon->discount_value) }}" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Minimum Order Amount</label>
                <input type="number" name="min_order_amount" step="0.01" value="{{ old('min_order_amount', $coupon->min_order_amount) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Maximum Discount</label>
                <input type="number" name="max_discount" step="0.01" value="{{ old('max_discount', $coupon->max_discount) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Usage Limit</label>
                <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Per User Limit</label>
                <input type="number" name="per_user_limit" value="{{ old('per_user_limit', $coupon->per_user_limit) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Valid From</label>
                <input type="datetime-local" name="valid_from" value="{{ old('valid_from', optional($coupon->valid_from)->format('Y-m-d\\TH:i')) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Expiry Date</label>
                <input type="datetime-local" name="expires_at" value="{{ old('expires_at', optional($coupon->expires_at)->format('Y-m-d\\TH:i')) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Applicable Products</label>
                <div class="relative">
                    <div class="multi-select" data-target="coupon-products" data-placeholder="Search products..."></div>
                </div>
                <select id="coupon-products" name="applicable_products[]" multiple class="hidden">
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" {{ in_array($product->id, $coupon->applicable_products ?? []) ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400">Leave empty to apply to all products.</p>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Applicable Categories</label>
                <div class="relative">
                    <div class="multi-select" data-target="coupon-categories" data-placeholder="Search categories..."></div>
                </div>
                <select id="coupon-categories" name="applicable_categories[]" multiple class="hidden">
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ in_array($category->id, $coupon->applicable_categories ?? []) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400">Leave empty to apply to all categories.</p>
            </div>
            <div class="space-y-1.5 flex items-center gap-3">
                <input type="checkbox" name="first_order_only" value="1" {{ old('first_order_only', $coupon->first_order_only) ? 'checked' : '' }}>
                <label class="text-xs font-bold text-slate-700">First Order Only</label>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Status</label>
                <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    <option value="1" {{ old('status', $coupon->status) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status', $coupon->status) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Times Used</label>
                <input type="text" value="{{ $coupon->times_used }}" disabled class="w-full bg-slate-100 border border-slate-200 px-3 py-2 rounded-lg text-sm text-slate-500">
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.coupons.index') }}" class="px-8 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-white/50 transition-all">Cancel</a>
            <button type="submit" class="bg-[#a91b43] text-white px-10 py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] shadow-xl shadow-pink-900/10 transition-all active:scale-95">
                Update Coupon
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.multi-select').forEach((wrapper) => {
        const select = document.getElementById(wrapper.dataset.target);
        if (!select) return;

        const placeholder = wrapper.dataset.placeholder || 'Search...';
        wrapper.innerHTML = `
            <div class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus-within:border-[#a91b43] transition-all text-slate-700 flex flex-wrap items-center gap-2 cursor-text">
                <div class="multi-select-tags flex flex-wrap items-center gap-1"></div>
                <input type="text" class="multi-select-search flex-1 min-w-[120px] text-xs outline-none bg-transparent" placeholder="${placeholder}">
                <button type="button" class="multi-select-toggle text-slate-400 text-[10px]"><i class="fas fa-chevron-down"></i></button>
            </div>
            <div class="multi-select-dropdown absolute z-50 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-56 overflow-auto hidden"></div>
        `;

        const searchInput = wrapper.querySelector('.multi-select-search');
        const dropdown = wrapper.querySelector('.multi-select-dropdown');
        const tagsWrap = wrapper.querySelector('.multi-select-tags');

        const optionItems = Array.from(select.options).map((opt) => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50 cursor-pointer';
            label.dataset.value = opt.value;
            label.dataset.text = opt.text.toLowerCase();
            label.innerHTML = `
                <input type="checkbox" class="accent-[#a91b43]">
                <span>${opt.text}</span>
            `;
            const checkbox = label.querySelector('input');
            checkbox.checked = opt.selected;
            checkbox.addEventListener('change', () => {
                opt.selected = checkbox.checked;
                syncTags();
            });
            return label;
        });

        optionItems.forEach((item) => dropdown.appendChild(item));

        function syncTags() {
            tagsWrap.innerHTML = '';
            const selected = Array.from(select.selectedOptions);
            selected.forEach((opt) => {
                const tag = document.createElement('span');
                tag.className = 'bg-[#a91b43] text-white text-[10px] px-2 py-0.5 rounded-full flex items-center gap-1';
                tag.innerHTML = `<span>${opt.text}</span><button type="button" class="text-white text-[10px] leading-none">x</button>`;
                tag.querySelector('button').addEventListener('click', (e) => {
                    e.stopPropagation();
                    opt.selected = false;
                    const item = dropdown.querySelector(`[data-value="${opt.value}"] input`);
                    if (item) item.checked = false;
                    syncTags();
                });
                tagsWrap.appendChild(tag);
            });
        }

        syncTags();

        function openDropdown() {
            dropdown.classList.remove('hidden');
        }

        function closeDropdown() {
            dropdown.classList.add('hidden');
            searchInput.value = '';
            optionItems.forEach((item) => item.classList.remove('hidden'));
        }

        wrapper.addEventListener('click', () => {
            openDropdown();
            searchInput.focus();
        });

        searchInput.addEventListener('input', () => {
            const term = searchInput.value.trim().toLowerCase();
            optionItems.forEach((item) => {
                const match = item.dataset.text.includes(term);
                item.classList.toggle('hidden', term && !match);
            });
        });

        document.addEventListener('click', (event) => {
            if (!wrapper.contains(event.target)) {
                closeDropdown();
            }
        });
    });
</script>
@endpush
