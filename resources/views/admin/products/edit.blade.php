@extends('admin.layouts.admin')

@section('title', 'Edit Product — ' . $product->name)

@section('content')
<div class="space-y-6">
<form id="productForm" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

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
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">SKU</label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Barcode / EAN</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Brand</label>
                        <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Short Description</label>
                        <textarea name="short_description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Full Description</label>
                        <textarea name="full_description" rows="5"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">{{ old('full_description', $product->full_description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ===== PRODUCT IMAGES (General) ===== --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center">
                    <i class="fas fa-images mr-2 text-[#a91b43]"></i> Product Images
                    <span class="ml-2 text-[10px] font-normal text-slate-400">(General gallery)</span>
                </h3>
                <p class="text-[10px] text-slate-400 mb-4">These are the main product images. Uploading new ones will replace existing ones.</p>

                {{-- Existing general images --}}
                @php
                    $existingImgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?? []);
                @endphp
                @if(count($existingImgs) > 0)
                <div class="flex flex-wrap gap-2 mb-4">
                    @foreach($existingImgs as $idx => $img)
                    <div class="relative group">
                        <img src="{{ asset('uploads/'.$img) }}" class="w-16 h-16 rounded-lg object-cover border border-slate-200 shadow-sm">
                        @if($idx === 0)
                        <span class="absolute -top-1.5 -right-1.5 bg-[#a91b43] text-white text-[7px] font-black rounded px-1 leading-tight">MAIN</span>
                        @endif
                    </div>
                    @endforeach
                </div>
                <p class="text-[10px] text-amber-500 mb-3 flex items-center gap-1">
                    <i class="fas fa-exclamation-triangle"></i> Uploading new images will replace all existing general images.
                </p>
                @endif

                <div id="generalImagesPreview" class="flex flex-wrap gap-2 mb-3 min-h-[2px]"></div>
                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#a91b43] hover:bg-rose-50/30 transition-all group">
                    <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 group-hover:text-[#a91b43] transition-colors mb-1.5"></i>
                    <span class="text-xs font-bold text-slate-400 group-hover:text-[#a91b43]">Click to upload new images</span>
                    <span class="text-[10px] text-slate-300 mt-0.5">PNG, JPG, WEBP • Multiple files allowed</span>
                    <input type="file" name="images[]" id="generalImagesInput" multiple accept="image/*" class="hidden">
                </label>

                <div class="mt-4">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Video URL</label>
                    <input type="url" name="video_url" value="{{ old('video_url', $product->video_url) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="YouTube / Vimeo URL">
                </div>
            </div>

            {{-- ===== VARIANT MATRIX (NEW CONCEPT) ===== --}}
            <div class="card-glass p-6 rounded-2xl border-2 border-[#a91b43]/10">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="text-base font-bold text-slate-800 flex items-center">
                        <i class="fas fa-th mr-2 text-[#a91b43]"></i> Product Variants
                    </h3>
                    <span class="text-[10px] font-bold text-[#a91b43] bg-rose-50 px-2 py-0.5 rounded-full uppercase tracking-widest">Dynamic Matrix</span>
                </div>
                <p class="text-[10px] text-slate-400 mb-5 italic">
                    Select attributes below to generate the variant matrix. Set price & stock in the matrix. Upload images per Color.
                </p>

                <div id="variantMatrixContainer" class="{{ ($product->product_variants && $product->product_variants->count()) ? '' : 'hidden' }} space-y-4">
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
                                @if($product->product_variants && $product->product_variants->count())
                                    @foreach($product->product_variants as $vIdx => $v)
                                    @php
                                        // Flatten combination to string for JS/Form logic
                                        $combIds = [];
                                        if(is_array($v->combination)) {
                                            $combIds = array_values($v->combination);
                                        }
                                        $idsString = implode(',', array_column(array_map(fn($arr) => (array)$arr, $combIds), 0));
                                        $names = implode(' - ', array_values($v->attribute_values ?? []));
                                    @endphp
                                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors" data-comb-ids="{{ $idsString }}">
                                        <td class="px-3 py-3">
                                            <span class="font-bold text-slate-800 variant-name">{{ $names }}</span>
                                            <input type="hidden" name="variant_combinations[]" value="{{ $idsString }}" class="variant-comb-input">
                                            <input type="file" name="v_images[{{ $vIdx }}][]" multiple class="hidden-v-file hidden" accept="image/*">
                                            <input type="hidden" name="v_existing_images[]" value="{{ is_array($v->images) ? json_encode($v->images) : ($v->images ?: json_encode($v->image ? [$v->image] : [])) }}" class="existing-images-input">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" name="v_price[]" value="{{ $v->price }}" step="0.01" class="w-full bg-white border border-slate-200 rounded px-2 py-1 outline-none focus:border-[#a91b43]" placeholder="₹">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="number" name="v_stock[]" value="{{ $v->stock_quantity }}" class="w-full bg-white border border-slate-200 rounded px-2 py-1 outline-none focus:border-[#a91b43]">
                                        </td>
                                        <td class="px-3 py-3">
                                            <input type="text" name="v_sku[]" value="{{ $v->sku }}" class="w-full bg-slate-50/50 border-slate-200 hover:bg-white focus:bg-white border rounded px-2 py-1 outline-none text-center text-[10px]" placeholder="SKU">
                                        </td>
                                    </tr>
                                    @endforeach
                                @endif
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
                                @php
                                    $isChecked = in_array($value->id, (array)($product->attributes[$attribute->id] ?? []));
                                @endphp
                                <label class="attr-chip inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border-2 {{ $isChecked ? 'border-[#a91b43] bg-rose-50/30' : 'border-slate-200 bg-white' }} text-xs cursor-pointer hover:border-[#a91b43] transition-all select-none"
                                    data-attr-id="{{ $attribute->id }}"
                                    data-attr-name="{{ $attribute->name }}"
                                    data-value-id="{{ $value->id }}"
                                    data-value-name="{{ $value->name }}">
                                    <input type="checkbox"
                                        name="attributes[{{ $attribute->id }}][]"
                                        value="{{ $value->id }}"
                                        class="accent-[#a91b43] attr-checkbox-matrix"
                                        {{ $isChecked ? 'checked' : '' }}>
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
                            <input type="number" name="regular_price" id="regular_price" step="0.01"
                                value="{{ old('regular_price', $product->regular_price) }}" required
                                class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sale Price</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-400">₹</span>
                            <input type="number" name="sale_price" id="sale_price" step="0.01"
                                value="{{ old('sale_price', $product->sale_price) }}"
                                class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Discount %</label>
                        <input type="number" name="discount_percent" id="discount_percent" step="0.01"
                            value="{{ old('discount_percent', $product->discount_percent) }}" readonly
                            class="w-full bg-slate-100 border border-slate-100 px-3 py-2 rounded-lg text-sm text-slate-500 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stock Qty <span class="text-rose-500">*</span></label>
                        <input type="number" name="stock_quantity"
                            value="{{ old('stock_quantity', $product->stock_quantity) }}" required
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Low Stock Alert</label>
                        <input type="number" name="low_stock_threshold"
                            value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Weight (grams)</label>
                        <input type="text" name="weight" value="{{ old('weight', $product->weight) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stock Status</label>
                        <select name="stock_status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="instock"    {{ ($product->stock_status ?? 'instock') == 'instock'    ? 'selected':'' }}>In Stock</option>
                            <option value="outofstock" {{ ($product->stock_status ?? '') == 'outofstock' ? 'selected':'' }}>Out of Stock</option>
                            <option value="backorder"  {{ ($product->stock_status ?? '') == 'backorder'  ? 'selected':'' }}>Backorder</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Shipping Class</label>
                        <input type="text" name="shipping_class" value="{{ old('shipping_class', $product->shipping_class) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
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
                        <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Keywords</label>
                        <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="Enter keywords separated by commas">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tags <span class="text-slate-400 font-normal">(comma separated)</span></label>
                        @php $tagsString = is_array($product->tags) ? implode(', ', $product->tags) : $product->tags; @endphp
                        <input type="text" name="tags" value="{{ old('tags', $tagsString) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
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
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('related_products', $product->related_products ?? [])) ? 'selected' : '' }}>{{ $p->name }}</option>
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
                            <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Published / Active</option>
                            <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Featured</label>
                        <select name="is_featured" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="0" {{ old('is_featured', $product->is_featured) == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_featured', $product->is_featured) == '1' ? 'selected' : '' }}>Yes — Show on Homepage</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Tax Settings --}}
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-4">Tax Settings</h3>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tax Class</label>
                    <select name="tax_class" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        <option value="">No Tax / Standard</option>
                        @foreach($taxClasses as $tax)
                            <option value="{{ $tax->slug }}" {{ old('tax_class', $product->tax_class) == $tax->slug ? 'selected' : '' }}>{{ $tax->name }}</option>
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
                                <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Sub Category</label>
                        <select name="sub_category_id" id="sub_category_id"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">Select Sub Category</option>
                            @foreach($subCategories as $sub)
                                <option value="{{ $sub->id }}" {{ old('sub_category_id', $product->sub_category_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Child Category</label>
                        <select name="child_category_id" id="child_category_id"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">Select Child Category</option>
                            @foreach($childCategories as $child)
                                <option value="{{ $child->id }}" {{ old('child_category_id', $product->child_category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Save --}}
            <div class="card-glass p-5 rounded-2xl">
                <button type="submit"
                    class="w-full bg-[#a91b43] text-white py-3 rounded-xl text-sm font-bold hover:bg-[#940437] shadow-lg transition-all active:scale-95">
                    <i class="fas fa-check mr-2"></i> Update Product
                </button>
                <a href="{{ route('admin.products.index') }}"
                    class="block mt-3 text-center py-2.5 rounded-xl text-sm font-bold text-slate-500 hover:bg-slate-50 border border-slate-100 transition-all">
                    Discard Changes
                </a>
            </div>
        </div>
    </div>
</form>
</div>

{{-- ===== VARIANT MATRIX TEMPLATE ===== --}}
<template id="variantRowTemplate">
    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-colors">
        <td class="px-3 py-3">
            <span class="font-bold text-slate-800 variant-name"></span>
            <input type="hidden" name="variant_combinations[]" class="variant-comb-input">
            <input type="hidden" name="v_existing_images[]" class="existing-images-input">
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
        placeholder: "Search and select..."
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

    // ── Variant Matrix Logic (Cartesian) ─────────────────────────────
    var existingVariants = {!! json_encode($product->product_variants) !!};
    
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
                var existingInDb = findExistingVariant(ids);
                
                var clone = document.importNode(template, true);
                $row = $(clone.querySelector('tr'));
                $row.find('.variant-name').html('<span class="name-text">' + names + '</span><input type="file" class="hidden-v-file hidden" multiple accept="image/*">');
                $row.find('.variant-comb-input').val(ids);
                
                    if(existingInDb) {
                        $row.find('input[name="v_price[]"]').val(existingInDb.price);
                        $row.find('input[name="v_stock[]"]').val(existingInDb.stock_quantity);
                        $row.find('input[name="v_sku[]"]').val(existingInDb.sku);
                        
                        var dat = existingInDb.images;
                        if(!dat && existingInDb.image) dat = [existingInDb.image];
                        if(typeof dat !== 'string') dat = JSON.stringify(dat);
                        $row.find('.existing-images-input').val(dat);
                    } else {
                        var mainPrice = $('#sale_price').val() || $('#regular_price').val();
                        if(mainPrice) $row.find('input[name="v_price[]"]').val(mainPrice);
                    }
                }
            
            var colorVal = comb.find(v => v.attrName.toLowerCase().indexOf('color') !== -1);
            if (colorVal) {
                $row.attr('data-color-name', colorVal.name);
            }

            // Critical: Re-index using activeRowIndex
            $row.find('.hidden-v-file').attr('name', 'v_images[' + activeRowIndex + '][]');
            $row.find('.existing-images-input').attr('name', 'v_existing_images['+activeRowIndex+']');
            activeRowIndex++;

            $('#variantMatrixBody').append($row);
        });

        // 2. Generate Color Image Sections (Flipkart style)
        var $colorGrid = $('#colorImagesGrid');
        
        var oldColorInputs = {};
        $('.color-group-file-input').each(function() {
            var col = $(this).closest('.color-image-card').data('color-name');
            if(this.files && this.files.length > 0) oldColorInputs[col] = this.files;
        });

        $colorGrid.empty();
        if(colorValues.length > 0) {
            $('#colorImagesSection').removeClass('hidden');
            colorValues.forEach(c => {
                var tpl = `
                    <div class="color-image-card bg-white border border-slate-200 rounded-xl p-4 shadow-sm relative overflow-hidden group" data-color-name="${c.name}">
                        <div class="absolute top-0 left-0 w-1 h-full bg-[#a91b43]"></div>
                        <div class="font-bold text-sm text-slate-800 mb-3 ml-2 flex items-center justify-between">
                            <span><span class="inline-block w-3 h-3 rounded-full border border-slate-300 mr-1 shadow-inner" style="background-color: ${c.name.toLowerCase()}"></span> ${c.name}</span>
                        </div>
                        <label class="cursor-pointer bg-slate-50 border border-dashed border-slate-300 rounded-lg block p-4 text-center hover:bg-rose-50 transition-all mb-2">
                            <i class="fas fa-cloud-upload-alt text-slate-400 mb-2 text-xl group-hover:text-[#a91b43]"></i>
                            <div class="text-[11px] font-bold text-slate-600">Upload Images for ${c.name}</div>
                            <input type="file" class="color-group-file-input hidden" multiple accept="image/*">
                        </label>
                        <div class="color-preview-container flex flex-wrap gap-2"></div>
                    </div>
                `;
                var $card = $(tpl);
                
                var $preview = $card.find('.color-preview-container');
                if(oldColorInputs[c.name]) {
                    var $input = $card.find('.color-group-file-input')[0];
                    var dt = new DataTransfer();
                    for(let i=0; i<oldColorInputs[c.name].length; i++) dt.items.add(oldColorInputs[c.name][i]);
                    $input.files = dt.files;
                    renderColorPreviews($input.files, $preview);
                } else {
                    // Pre-fill existing DB images if any row of this color has them
                    var existingImgs = null;
                    $('#variantMatrixBody tr[data-color-name="' + c.name + '"]').each(function() {
                        var dat = $(this).find('.existing-images-data').attr('data-images');
                        if (dat) {
                            try {
                                var parsed = JSON.parse(dat);
                                if(parsed && parsed.length > 0) { existingImgs = parsed; return false; } 
                            } catch(e) {}
                        }
                    });
                    
                    if (existingImgs) {
                        existingImgs.forEach(img => {
                            $preview.append('<img src="/uploads/'+img+'" class="w-10 h-10 rounded object-cover border border-slate-200 shadow-sm">');
                        });
                    }
                }

                $colorGrid.append($card);
            });
        } else {
            $('#colorImagesSection').addClass('hidden');
        }
    }

    function findExistingVariant(idsString) {
        if(!existingVariants) return null;
        var incomingIds = idsString.split(',').map(Number).sort();
        
        return existingVariants.find(function(v) {
            if(!v.combination) return false;
            // combination is { "1": [10], "2": [15] }
            var savedIds = Object.values(v.combination).flat().map(Number).sort();
            return JSON.stringify(incomingIds) === JSON.stringify(savedIds);
        });
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

    // ── Apply Images to Matrix Rows ─────────────────────
    $(document).on('change', '.color-group-file-input', function() {
        var files = this.files;
        var $card = $(this).closest('.color-image-card');
        var colorName = $card.data('color-name');
        
        renderColorPreviews(files, $card.find('.color-preview-container'));

        var dt = new DataTransfer();
        if(files && files.length > 0) {
            for (var i = 0; i < files.length; i++) dt.items.add(files[i]);
        }

        $('#variantMatrixBody tr').each(function() {
            if($(this).attr('data-color-name') === colorName) {
                var hiddenInput = $(this).find('.hidden-v-file')[0];
                if(hiddenInput) hiddenInput.files = dt.files;
                
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
                    $container.append('<img src="'+e.target.result+'" class="w-10 h-10 rounded object-cover border border-slate-200 shadow-sm">');
                }
                reader.readAsDataURL(file);
            });
        }
    }
    
    // Auto-generate on load if attributes are selected
    if($('.attr-checkbox-matrix:checked').length > 0) {
        generateVariantMatrix();
    }

    // ── Validation ─────────────────────────────────────────────────────
    $('#productForm').validate({
        rules: {
            name          : 'required',
            category_id   : 'required',
            regular_price : { required: true, number: true, min: 0 },
            stock_quantity: { required: true, digits: true, min: 0 }
        }
    });

    // ── Apply Images to Same Color Group ─────────────────────
    $(document).on('click', '.sync-images-btn', function() {
        var $btn = $(this);
        var $row = $btn.closest('tr');
        var group = $row.attr('data-sync-group');
        if (!group) return;

        var sourceInput = $row.find('.v-file-input')[0];
        if (!sourceInput.files || sourceInput.files.length === 0) {
            alert('Please select files by clicking Upload before applying to others.');
            return;
        }

        var sourceFiles = sourceInput.files;
        var dt = new DataTransfer();
        for (var i = 0; i < sourceFiles.length; i++) {
            dt.items.add(sourceFiles[i]);
        }

        $('#variantMatrixBody tr[data-sync-group="' + group + '"]').each(function() {
            if (this === $row[0]) return; // Skip original row
            
            var targetInput = $(this).find('.v-file-input')[0];
            targetInput.files = dt.files;
            
            // Trigger change to update previews
            $(targetInput).trigger('change');
            
            // Add a visual flash effect
            var $previewBlock = $(this).find('.v-preview-container');
            $previewBlock.addClass('ring-2 ring-emerald-400 ring-offset-2 transition-all rounded');
            setTimeout(() => {
                $previewBlock.removeClass('ring-2 ring-emerald-400 ring-offset-2');
            }, 1000);
        });
        
        $btn.text('Applied!');
        $btn.addClass('bg-emerald-50 text-emerald-600').removeClass('bg-indigo-50 text-indigo-600');
        setTimeout(() => {
            $btn.text('Apply to all ' + group);
            $btn.removeClass('bg-emerald-50 text-emerald-600').addClass('bg-indigo-50 text-indigo-600');
        }, 2000);
    });
});

// ── Build variant slot from template ──────────────────────────────────
function buildVariantSlot(attrId, valueId, name, swatch) {
    var tpl = document.getElementById('variantSlotTemplate').innerHTML;
    tpl = tpl.split('__VID__').join(valueId);
    tpl = tpl.split('__AID__').join(attrId);

    var $el = $(tpl);
    $el.find('.variant-name-label').text(name);

    if (swatch && /^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(swatch)) {
        $el.find('.swatch-preview').html(
            '<span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:' + swatch + ';border:1px solid #cbd5e1;vertical-align:middle;"></span>'
        );
    }
    return $el;
}

// ── Image preview helper ───────────────────────────────────────────────
function previewFiles(files, previewEl) {
    if (!previewEl || !files) return;
    previewEl.innerHTML = '';
    Array.from(files).forEach(function (file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-16 h-16 rounded-lg object-cover border border-slate-200 shadow-sm';
            previewEl.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
}
</script>
@endpush