@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="space-y-6">
<form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT COL ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- General Info --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-[#a91b43]"></i> General Information
                </h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Unique SKU">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Barcode / EAN</label>
                            <input type="text" name="barcode" value="{{ old('barcode') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Optional ISBN">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Brand name">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Short Description</label>
                        <textarea name="short_description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all"
                            placeholder="Brief overview">{{ old('short_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Description</label>
                        <textarea name="full_description" rows="5"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all"
                            placeholder="Detailed description">{{ old('full_description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== PRODUCT IMAGES (General) ===== --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center">
                    <i class="fas fa-images mr-2 text-[#a91b43]"></i> Product Images
                    <span class="ml-2 text-[10px] font-normal text-slate-400">(General gallery — shown by default)</span>
                </h3>
                <p class="text-[10px] text-slate-400 mb-4">Upload main product images. For each Color/Variant selected below, you can also add variant-specific images.</p>

                {{-- Upload zone --}}
                <div id="generalImagesPreview" class="flex flex-wrap gap-2 mb-3 min-h-[4px]"></div>
                <label id="generalUploadLabel"
                    class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#a91b43] hover:bg-rose-50/30 transition-all group">
                    <i class="fas fa-cloud-upload-alt text-3xl text-slate-300 group-hover:text-[#a91b43] transition-colors mb-2"></i>
                    <span class="text-xs font-bold text-slate-400 group-hover:text-[#a91b43]">Click to upload images</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">PNG, JPG, WEBP • Multiple files allowed • Max 2MB each</span>
                    <input type="file" name="images[]" id="generalImagesInput" multiple accept="image/*" class="hidden">
                    <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
                </label>

                {{-- Video URL --}}
                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Video URL (YouTube / Vimeo)</label>
                    <input type="url" name="video_url" value="{{ old('video_url') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all"
                        placeholder="https://youtube.com/watch?v=...">
                </div>
            </div>

            {{-- ===== VARIANT MATRIX (PERFECT CONCEPT) ===== --}}
            <div class="card-glass p-6 rounded-2xl border-2 border-[#a91b43]/10">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-bold text-slate-800 flex items-center">
                        <i class="fas fa-th mr-2 text-[#a91b43]"></i> Product Variants
                    </h3>
                    <span class="text-[10px] font-bold text-[#a91b43] bg-rose-50 px-2 py-0.5 rounded-full uppercase tracking-widest">Dynamic Matrix</span>
                </div>
                <p class="text-[10px] text-slate-400 mb-5 italic">
                    Select attributes below to generate the variant matrix. Each combination will have its own price, stock, and multiple images.
                </p>

                <div id="variantMatrixContainer" class="hidden space-y-4">
                    <div class="overflow-x-auto rounded-xl border border-slate-100 shadow-sm bg-slate-50/30">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-3 py-2.5 font-black text-slate-700 uppercase tracking-tighter">Variant</th>
                                    <th class="px-3 py-2.5 font-black text-slate-700 uppercase tracking-tighter w-24 text-center">Price</th>
                                    <th class="px-3 py-2.5 font-black text-slate-700 uppercase tracking-tighter w-20 text-center">Stock</th>
                                    <th class="px-3 py-2.5 font-black text-slate-700 uppercase tracking-tighter w-24 text-center">SKU</th>
                                    <th class="px-3 py-2.5 font-black text-slate-700 uppercase tracking-tighter w-12 text-center">
                                        <button type="button" id="resetVariantMatrix" class="text-slate-400 hover:text-[#a91b43] transition-colors" title="Reset Matrix (Restore Deleted Rows)">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="variantMatrixBody">
                                {{-- JS injects rows here --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- New Color Images Section --}}
                <div id="colorImagesSection" class="mt-6 hidden border-t border-slate-100 pt-5">
                    <h4 class="text-sm font-bold text-slate-800 mb-3 flex items-center">
                        <i class="fas fa-palette text-[#a91b43] mr-2"></i> Upload Images per Color
                    </h4>
                    <div id="colorImagesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        {{-- JS injects color cards here --}}
                    </div>
                </div>

                {{-- Attribute Selectors --}}
                <div class="mt-8 space-y-6">
                    @foreach($attributes as $attribute)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                {{ $attribute->group ? $attribute->group . ' — ' : '' }}{{ $attribute->name }}
                            </span>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($attribute->values as $value)
                                <label class="attr-chip inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border-2 border-slate-200 bg-white text-xs cursor-pointer hover:border-[#a91b43] transition-all select-none"
                                    data-attr-id="{{ $attribute->id }}"
                                    data-attr-name="{{ $attribute->name }}"
                                    data-value-id="{{ $value->id }}"
                                    data-value-name="{{ $value->name }}">
                                    <input type="checkbox"
                                        name="attributes[{{ $attribute->id }}][]"
                                        value="{{ $value->id }}"
                                        class="accent-[#a91b43] attr-checkbox-matrix">
                                    <span class="font-semibold text-slate-700">{{ $value->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pricing --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-tag mr-2 text-[#a91b43]"></i> Pricing & Stock
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Regular Price <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-400">₹</span>
                            <input type="number" name="regular_price" id="regular_price" step="0.01" value="{{ old('regular_price') }}" required
                                class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sale Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-400">₹</span>
                            <input type="number" name="sale_price" id="sale_price" step="0.01" value="{{ old('sale_price') }}"
                                class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Discount %</label>
                        <input type="number" name="discount_percent" id="discount_percent" step="0.01" value="{{ old('discount_percent') }}" readonly
                            class="w-full bg-slate-100 border border-slate-100 px-3 py-2 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stock Qty <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold" value="{{ old('low_stock_threshold', 5) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Weight (grams)</label>
                        <input type="text" name="weight" value="{{ old('weight') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="250">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stock Status</label>
                        <select name="stock_status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="instock">In Stock</option>
                            <option value="outofstock">Out of Stock</option>
                            <option value="backorder">Backorder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Shipping Class</label>
                        <input type="text" name="shipping_class" value="{{ old('shipping_class') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Standard">
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-search mr-2 text-[#a91b43]"></i> SEO Details
                </h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Title</label>
                        <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">{{ old('meta_description') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Enter keywords separated by commas">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tags <span class="text-slate-400 font-normal">(comma separated)</span></label>
                        <input type="text" name="tags" value="{{ old('tags') }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="silk, saree, kanjivaram">
                    </div>
                </div>
            </div>

            {{-- Related Products --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-link mr-2 text-[#a91b43]"></i> Related Products
                </h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Select Related Products</label>
                    <select name="related_products[]" id="related_products" multiple class="w-full select2-searchable">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('related_products', [])) ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Search and select multiple products.</p>
                </div>
            </div>

        </div>{{-- end left col --}}

        {{-- ===== RIGHT COL ===== --}}
        <div class="space-y-6">

            {{-- Publish --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4">Publish</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="1">Published / Active</option>
                            <option value="0">Draft</option>
                            <option value="archived">Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" value="{{ old('display_order', 0) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Featured</label>
                        <select name="is_featured" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="0">No</option>
                            <option value="1">Yes — Show on Homepage</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tax Settings --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4">Tax Settings</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tax Class</label>
                    <select name="tax_class_id" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        <option value="">No Tax / Standard</option>
                        @foreach($taxClasses as $tax)
                            <option value="{{ $tax->id }}" {{ old('tax_class_id') == $tax->id ? 'selected' : '' }}>{{ $tax->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Category --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4">Category</h3>
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" id="category_id" required
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sub Category</label>
                        <select name="sub_category_id" id="sub_category_id"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">Select Sub Category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Child Category</label>
                        <select name="child_category_id" id="child_category_id"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">Select Child Category</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <div class="card-glass p-5 rounded-2xl">
                <button type="submit"
                    class="w-full bg-[#a91b43] text-white py-3 rounded-xl text-sm font-bold hover:bg-[#940437] shadow-lg transition-all active:scale-95">
                    <i class="fas fa-check mr-2"></i> Publish Product
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="block mt-3 text-center py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 border border-slate-100 transition-all">
                    Discard
                </a>
            </div>
        </div>
    </div>
</form>
</div>

{{-- ===== VARIANT MATRIX ROW TEMPLATE ===== --}}
<template id="variantRowTemplate">
    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
        <td class="px-3 py-3">
            <span class="font-bold text-slate-800 variant-name"></span>
            <input type="hidden" name="variant_combinations[]" class="variant-comb-input">
        </td>
        <td class="px-3 py-3 text-center">
            <input type="number" name="v_price[]" step="0.01" class="w-full bg-white border border-slate-200 rounded px-2 py-1 outline-none focus:border-[#a91b43] text-center font-bold text-indigo-900" placeholder="₹">
        </td>
        <td class="px-3 py-3 text-center">
            <input type="number" name="v_stock[]" class="w-full bg-white border border-slate-200 rounded px-2 py-1 outline-none focus:border-[#a91b43] text-center" value="10">
        </td>
        <td class="px-3 py-3 text-center">
            <input type="text" name="v_sku[]" class="w-full bg-slate-50/50 border-slate-200 hover:bg-white focus:bg-white border rounded px-2 py-1 outline-none text-center text-[10px]" placeholder="SKU">
        </td>
        <td class="px-3 py-3 text-center">
            <button type="button" class="text-rose-400 hover:text-rose-600 remove-variant-row transition-all" title="Remove this combination">
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

    // ── Initialize Select2 ─────────────────────────────────────────────
    $('.select2-searchable, #category_id, #sub_category_id, #child_category_id, #tax_class_id').select2({
        width: '100%',
        placeholder: "Select or search..."
    });

    // Tracking for manually removed variant rows
    var removedCombinations = new Set();
    
    $(document).on('click', '.remove-variant-row', function() {
        var combId = $(this).closest('tr').find('.variant-comb-input').val();
        removedCombinations.add(combId);
        generateVariantMatrix();
    });

    $(document).on('click', '#resetVariantMatrix', function() {
        if(confirm("Restore all previously removed variant rows?")) {
            removedCombinations.clear();
            generateVariantMatrix();
        }
    });

    // ── General images preview ─────────────────────────────────────────
    $('#generalImagesInput').on('change', function () {
        previewFiles(this.files, document.getElementById('generalImagesPreview'));
    });

    // ── Discount auto-calc ─────────────────────────────────────────────
    $('#regular_price, #sale_price').on('input', function () {
        var r = parseFloat($('#regular_price').val()) || 0;
        var s = parseFloat($('#sale_price').val())    || 0;
        $('#discount_percent').val((r > 0 && s > 0 && s < r) ? ((r-s)/r*100).toFixed(2) : '');
    });

    // ── Category cascade ───────────────────────────────────────────────
    $('#category_id').on('change', function () {
        var id = $(this).val();
        $('#sub_category_id').html('<option value="">Select Sub Category</option>');
        $('#child_category_id').html('<option value="">Select Child Category</option>');
        if (id) $.getJSON('/admin/get-sub-categories/' + id, function (d) {
            $.each(d, function (k, v) { $('#sub_category_id').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });

    $('#sub_category_id').on('change', function () {
        var id = $(this).val();
        $('#child_category_id').html('<option value="">Select Child Category</option>');
        if (id) $.getJSON('/admin/get-child-categories/' + id, function (d) {
            $.each(d, function (k, v) { $('#child_category_id').append('<option value="'+v.id+'">'+v.name+'</option>'); });
        });
    });

    // ── Variant Matrix Logic ─────────────────────────────────────────
    $(document).on('change', '.attr-checkbox-matrix', function () {
        generateVariantMatrix();
    });

    function generateVariantMatrix() {
        var selected = {};
        $('.attr-checkbox-matrix:checked').each(function() {
            var chip   = $(this).closest('.attr-chip');
            var attrId = chip.data('attr-id');
            var valId  = $(this).val();
            var name   = chip.data('value-name');
            var attrName = chip.data('attr-name') || '';
            if(!selected[attrId]) selected[attrId] = [];
            selected[attrId].push({id: valId, name: name, attrName: attrName});
            chip.addClass('border-[#a91b43] bg-rose-50/30 shadow-sm');
        });
        
        $('.attr-checkbox-matrix:not(:checked)').each(function(){
            $(this).closest('.attr-chip').removeClass('border-[#a91b43] bg-rose-50/30 shadow-sm');
        });

        var attrIds = Object.keys(selected);
        if(attrIds.length === 0) {
            $('#variantMatrixContainer').addClass('hidden');
            $('#variantMatrixBody').empty();
            return;
        }

        // Store existing rows to preserve data/images
        var existingRows = {};
        $('#variantMatrixBody tr').each(function() {
            var combId = $(this).find('.variant-comb-input').val();
            existingRows[combId] = $(this).detach();
        });

        // Extract Color attribute if exists
        var colorAttrId = null;
        var colorValues = [];
        Object.keys(selected).forEach(attrId => {
            if(selected[attrId][0].attrName.toLowerCase().indexOf('color') !== -1) {
                colorAttrId = attrId;
                colorValues = selected[attrId];
            }
        });

        // 1. Generate Variant Matrix combinations
        var combinations = cartesian(Object.values(selected));
        $('#variantMatrixBody').empty();
        $('#variantMatrixContainer').removeClass('hidden');

        var template = document.getElementById('variantRowTemplate').content;
        var activeRowIndex = 0;

        combinations.forEach(function(comb) {
            var names = comb.map(v => v.name).join(' - ');
            var ids   = comb.map(v => v.id).join(',');
            
            // Skip manually removed combinations
            if(removedCombinations.has(ids)) return;

            var $row;
            if(existingRows[ids]) {
                $row = existingRows[ids];
            } else {
                var clone = document.importNode(template, true);
                $row = $(clone.querySelector('tr'));
                $row.find('.variant-name').html(names + '<input type="file" class="hidden-v-file hidden" multiple accept="image/*">');
                $row.find('.variant-comb-input').val(ids);

                var mainPrice = $('#sale_price').val() || $('#regular_price').val();
                if(mainPrice) $row.find('input[name="v_price[]"]').val(mainPrice);
            }
            
            var colorVal = comb.find(v => v.attrName.toLowerCase().indexOf('color') !== -1);
            if (colorVal) {
                $row.attr('data-color-name', colorVal.name);
            }
            
            // Critical: Re-index hidden image inputs correctly using activeRowIndex
            $row.find('.hidden-v-file').attr('name', 'v_images[' + activeRowIndex + '][]');
            activeRowIndex++;

            $('#variantMatrixBody').append($row);
        });

        // 2. Generate Color Image Sections (Flipkart style)
        var $colorGrid = $('#colorImagesGrid');
        // Save old file inputs to avoid losing selected files on re-render
        var oldColorInputs = {};
        $('.color-group-file-input').each(function() {
            oldColorInputs[$(this).closest('.color-image-card').data('color-name')] = this.files;
        });

        $colorGrid.empty();
        if(colorValues.length > 0) {
            $('#colorImagesSection').removeClass('hidden');
            colorValues.forEach(c => {
                var tpl = `
                    <div class="color-image-card bg-white border border-slate-200 rounded-xl p-4 shadow-sm relative overflow-hidden group" data-color-name="${c.name}">
                        <div class="absolute top-0 left-0 w-1 h-full bg-[#a91b43]"></div>
                        <div class="font-bold text-sm text-slate-800 mb-3 ml-2 flex items-center justify-between">
                            <span><i class="fas fa-fill-drip text-[#a91b43]/50 mr-1 text-xs"></i> ${c.name}</span>
                        </div>
                        <label class="cursor-pointer bg-slate-50 border border-dashed border-slate-300 rounded-lg block p-6 text-center hover:bg-[#a91b43]/5 hover:border-[#a91b43]/30 transition-all mb-2">
                            <i class="fas fa-cloud-upload-alt text-slate-400 mb-2 text-xl group-hover:text-[#a91b43] transition-colors"></i>
                            <div class="text-[11px] font-bold text-slate-600">Select Images for ${c.name}</div>
                            <input type="file" class="color-group-file-input hidden" multiple accept="image/*">
                        </label>
                        <div class="color-preview-container flex flex-wrap gap-2"></div>
                    </div>
                `;
                var $card = $(tpl);
                
                // Restore old files if they existed
                if(oldColorInputs[c.name] && oldColorInputs[c.name].length > 0) {
                    var $input = $card.find('.color-group-file-input')[0];
                    var dt = new DataTransfer();
                    for(let i=0; i<oldColorInputs[c.name].length; i++) dt.items.add(oldColorInputs[c.name][i]);
                    $input.files = dt.files;
                    renderColorPreviews($input.files, $card.find('.color-preview-container'));
                }

                $colorGrid.append($card);
            });
        } else {
            $('#colorImagesSection').addClass('hidden');
        }
    }

    // ── Apply Images to Matrix Rows ─────────────────────
    $(document).on('change', '.color-group-file-input', function() {
        var files = this.files;
        var $card = $(this).closest('.color-image-card');
        var colorName = $card.data('color-name');
        
        renderColorPreviews(files, $card.find('.color-preview-container'));

        // Magic: Map these files to the hidden inputs of ALL matrix rows with this color
        var dt = new DataTransfer();
        if(files && files.length > 0) {
            for (var i = 0; i < files.length; i++) dt.items.add(files[i]);
        }

        $('#variantMatrixBody tr').each(function() {
            if($(this).attr('data-color-name') === colorName) {
                var hiddenInput = $(this).find('.hidden-v-file')[0];
                if(hiddenInput) hiddenInput.files = dt.files;
                
                // Flash row to indicate it received the files
                $(this).addClass('bg-emerald-50/50');
                setTimeout(() => $(this).removeClass('bg-emerald-50/50'), 500);
            }
        });
    });

    function renderColorPreviews(files, $container) {
        $container.empty();
        if (files && files.length > 0) {
            Array.from(files).forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e){
                    $container.append('<img src="'+e.target.result+'" class="w-12 h-12 rounded object-cover border border-slate-200 shadow-sm">');
                }
                reader.readAsDataURL(file);
            });
        }
    }

    function cartesian(args) {
        var r = [], max = args.length - 1;
        function helper(arr, i) {
            for (var j = 0, l = args[i].length; j < l; j++) {
                var a = arr.slice(0);
                a.push(args[i][j]);
                if (i == max) r.push(a);
                else helper(a, i + 1);
            }
        }
        helper([], 0);
        return r;
    }

    $(document).on('change', '.v-file-input', function() {
        var files = this.files;
        var $container = $(this).closest('td').find('.v-preview-container');
        $container.empty();
        if (files && files.length > 0) {
            Array.from(files).forEach(function(file) {
                var reader = new FileReader();
                reader.onload = function(e){
                    $container.append('<div class="v-preview w-8 h-8 rounded border border-slate-100 bg-slate-50 flex items-center justify-center overflow-hidden"><img src="'+e.target.result+'" class="w-full h-full object-cover"></div>');
                }
                reader.readAsDataURL(file);
            });
        }
    });
 

});

// ── Build one variant slot HTML ────────────────────────────────────────
function buildVariantSlot(attrId, valueId, name, swatch) {
    var tpl = document.getElementById('variantSlotTemplate').innerHTML;
    // Replace placeholders
    tpl = tpl.split('__VID__').join(valueId);
    tpl = tpl.split('__AID__').join(attrId);

    var $el = $(tpl);
    $el.find('.variant-name-label').text(name);

    // Build swatch preview
    var swatchHtml = '';
    if (swatch && /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(swatch)) {
        swatchHtml = '<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:' + swatch + ';border:1px solid #e2e8f0;vertical-align:middle;"></span>';
    }
    $el.find('.swatch-preview').html(swatchHtml);

    return $el;
}

// ── Image preview helper ───────────────────────────────────────────────
function previewFiles(files, previewEl) {
    previewEl.innerHTML = '';
    if (!files) return;
    Array.from(files).forEach(function (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var wrap = document.createElement('div');
            wrap.className = 'relative';
            var img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-16 h-16 rounded-lg object-cover border border-slate-200 shadow-sm transition-all';
            
            // Primary Selector
            var selector = document.createElement('div');
            selector.className = 'absolute -top-1 -right-1 bg-white rounded-full border border-slate-200 shadow-sm cursor-pointer z-10 w-5 h-5 flex items-center justify-center hover:scale-110 transition-all primary-badge';
            selector.innerHTML = '<i class="fas fa-check text-[8px] text-slate-300"></i>';
            selector.title = "Make Primary";
            
            var index = previewEl.children.length;
            if(index === 0) { 
                selector.classList.add('bg-pink-100', 'border-pink-500'); 
                selector.querySelector('i').classList.remove('text-slate-300');
                selector.querySelector('i').classList.add('text-pink-600');
                img.classList.add('ring-2', 'ring-pink-500');
            }

            selector.onclick = function() {
                document.querySelectorAll('.primary-badge').forEach(b => {
                    b.classList.remove('bg-pink-100', 'border-pink-500');
                    b.querySelector('i').classList.remove('text-pink-600');
                    b.querySelector('i').classList.add('text-slate-300');
                });
                document.querySelectorAll('.relative img').forEach(i => i.classList.remove('ring-2', 'ring-pink-500'));
                
                this.classList.add('bg-pink-100', 'border-pink-500');
                this.querySelector('i').classList.remove('text-slate-300');
                this.querySelector('i').classList.add('text-pink-600');
                img.classList.add('ring-2', 'ring-pink-500');
                document.getElementById('primary_image_index').value = index;
            };

            wrap.appendChild(img);
            wrap.appendChild(selector);
            previewEl.appendChild(wrap);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush