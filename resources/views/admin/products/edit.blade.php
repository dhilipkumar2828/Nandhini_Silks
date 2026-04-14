@extends('admin.layouts.admin')

@section('title', 'Edit Product — ' . $product->name)

@section('content')
<div class="space-y-6">
<form id="productForm" action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @php 
        $isVar = (old('is_variant') ?? ($product->product_variants && $product->product_variants->count() > 0)); 
    @endphp

    <div class="max-w-[1200px] mx-auto space-y-6 pb-20">
        {{-- ===== MAIN CONTENT ===== --}}
        <div class="space-y-6">

            {{-- 1. General Information --}}
            <div class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-info-circle mr-2 text-[#a91b43]"></i> General Information
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Product Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" id="productName" value="{{ old('name', $product->name) }}" required
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <div id="nameErrorMsg" class="text-[10px] text-rose-500 font-bold mt-1 hidden">This name already exists!</div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Product Slug <span class="text-rose-500">*</span></label>
                            <input type="text" name="slug" id="productSlug" value="{{ old('slug', $product->slug) }}" required
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <div id="slugErrorMsg" class="text-[10px] text-rose-500 font-bold mt-1 hidden">This slug already exists!</div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div id="generalSkuField" class="{{ $isVar ? 'hidden' : '' }}">
                            <label class="block text-xs font-bold text-slate-700 mb-1">SKU <span class="text-rose-500 font-bold">*</span></label>
                            <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" required
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Barcode / EAN</label>
                            <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">ISBN</label>
                            <input type="text" name="isbn" value="{{ old('isbn', $product->isbn) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand', $product->brand) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Offer Collection</label>
                            <input type="text" name="offer_collection" value="{{ old('offer_collection', $product->offer_collection) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="e.g. Summer Sale, New Arrival">
                        </div> --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Video URL</label>
                            <input type="url" name="video_url" value="{{ old('video_url', $product->video_url) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" placeholder="https://youtube.com/...">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Description</label>
                        <div id="short_description_editor" class="bg-slate-50 border border-slate-200 rounded-lg text-sm" style="height:150px;"></div>
                        <textarea name="short_description" id="short_description" class="hidden">{{ old('short_description', $product->short_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Specification</label>
                        <div id="full_description_editor" class="bg-slate-50 border border-slate-200 rounded-lg text-sm" style="height:220px;"></div>
                        <textarea name="full_description" id="full_description" class="hidden">{{ old('full_description', $product->full_description) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- 2. Product Variants (Switch) --}}
            <div id="productVariantSwitchSection" class="card-glass p-4 rounded-2xl border-2 border-[#a91b43]/20 flex items-center justify-between shadow-md bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center text-[#a91b43] shadow-inner">
                        <i class="fas fa-layer-group text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-tight">Product Variants</h3>
                        <p class="text-[10px] text-slate-400 font-medium tracking-tight">Managing variations for this product.</p>
                    </div>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_variant" id="isVariantCheckbox" value="1" {{ $isVar ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#a91b43]"></div>
                </label>
            </div>


            {{-- 4b. Single Product Pricing & Images (If NOT variant) --}}
            <div id="pricingStockSection" class="{{ $isVar ? 'hidden' : '' }} space-y-6">
                <div class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-tag mr-2 text-[#a91b43]"></i> Pricing & Stock Details
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Regular Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">₹</span>
                                <input type="number" name="regular_price" id="regular_price" step="0.01" min="0" value="{{ old('regular_price', $product->regular_price) }}" required
                                    class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Sale Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">₹</span>
                                <input type="number" name="sale_price" id="sale_price" step="0.01" min="0" value="{{ old('sale_price', $product->sale_price) }}"
                                    class="w-full bg-slate-50 border border-slate-200 pl-7 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stock Status</label>
                            <select name="stock_status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                                <option value="instock" {{ $product->stock_status == 'instock' ? 'selected' : '' }}>In Stock</option>
                                <option value="outofstock" {{ $product->stock_status == 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                                <option value="onbackorder" {{ $product->stock_status == 'onbackorder' ? 'selected' : '' }}>On Backorder</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stock Quantity</label>
                            <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Low Stock Alert</label>
                            <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Weight (KG) <span class="text-rose-500">*</span></label>
                            <input type="number" name="weight" step="0.01" min="0.1" value="{{ old('weight', $product->weight ?? '0.10') }}" required
                                class="w-full bg-[#fff5f5] border border-rose-200 px-3 py-2 rounded-lg text-sm font-bold text-rose-700 outline-none focus:border-[#a91b43] transition-all" placeholder="0.10">
                             <p class="text-[10px] text-rose-500 font-bold mt-1 uppercase tracking-tighter">
                                <i class="fas fa-exclamation-triangle"></i> Mandatory: Use format 0.10 (100g) or 0.50 (500g)
                            </p>
                        </div>
                    </div>
                </div>

                <div id="productImagesSection" class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-images mr-2 text-[#a91b43]"></i> Product Images <span class="text-rose-500 font-bold ml-1">*</span>
                    </h3>
                    
                    @php $existingImgs = is_array($product->images) ? $product->images : (json_decode($product->images ?? '[]', true) ?? []); @endphp
                    <div class="gallery-unified-container p-4 bg-slate-50/50 rounded-2xl border border-slate-100 mb-4 {{ count($existingImgs) > 0 ? '' : 'hidden' }}" id="galleryWrapper">
                        <div id="unifiedGalleryList" class="flex flex-wrap gap-4">
                            @foreach($existingImgs as $idx => $img)
                            <div class="relative group w-24 h-24 bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
                                <img src="{{ asset('uploads/'.$img) }}" class="w-full h-full rounded-xl object-cover">
                                <input type="hidden" name="existing_images[]" value="{{ $img }}">
                                <button type="button" class="remove-existing-general-image absolute -top-2 -right-2 bg-rose-600 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-lg z-10 opacity-100 transition-all text-[10px]" title="Remove image">
                                    <i class="fas fa-times"></i>
                                </button>
                                @if($idx === 0) 
                                    <span class="absolute -bottom-2 -left-2 bg-[#a91b43] text-[8px] text-white font-black px-2 py-0.5 rounded-full shadow-md z-10 uppercase tracking-tighter">Primary</span> 
                                @endif
                            </div>
                            @endforeach
                            <div id="generalImagesPreview" class="flex flex-wrap gap-4 contents"></div>
                        </div>
                    </div>

                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer hover:border-[#a91b43] transition-all bg-white group">
                        <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 mb-2 group-hover:text-[#a91b43] transition-colors"></i>
                        <span class="text-xs font-bold text-slate-500">Upload new gallery images <span class="text-rose-500">*</span></span>
                        <input type="file" name="images[]" id="generalImagesInput" {{ count($existingImgs) > 0 ? '' : 'required' }} multiple accept="image/*" class="hidden">
                    </label>
                    <div id="imagesErrorMsg" class="text-[10px] text-rose-500 font-bold mt-1 hidden text-center">This field is required.</div>
                    <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
                </div>
            </div>
            

            {{-- 4. Variant Management Matrix --}}
            <div id="variantConfigurationSection" class="{{ $isVar ? '' : 'hidden' }} space-y-6">
                <div class="card-glass p-8 rounded-[2.5rem] border-2 border-[#a91b43]/10 shadow-2xl relative overflow-hidden bg-white">
                    <div class="absolute top-0 right-0 w-96 h-96 bg-[#a91b43]/5 rounded-full -mr-48 -mt-48 blur-3xl"></div>
                    <div class="flex items-center justify-between mb-8 relative z-10">
                        <div class="flex items-center gap-5">
                            <div class="w-14 h-14 bg-rose-50 rounded-2xl flex items-center justify-center text-[#a91b43] shadow-md border border-rose-100">
                                <i class="fas fa-layer-group text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-2xl font-black text-slate-800 tracking-tighter">Variant Management Matrix</h3>
                                <p class="text-xs text-slate-400 mt-0.5 font-medium italic">Synchronized hub for variations.</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-10 relative z-10">
                        {{-- 1. Configuration Suite (Full Width Top) --}}
                        <div class="w-full">
                             <div class="p-8 bg-slate-50/80 rounded-[2rem] border-2 border-slate-100/50">
                                <div class="flex items-center gap-4 mb-6">
                                    <div class="w-10 h-10 bg-[#a91b43] text-white rounded-xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-sliders-h"></i>
                                    </div>
                                    <h4 class="text-sm font-black text-slate-800 uppercase tracking-widest">Configuration Suite</h4>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                                    <div class="md:col-span-1">
                                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">Select Attribute</label>
                                        <select id="attributePicker" class="select2-searchable">
                                            <option value="">+ Add Variant Attribute</option>
                                              @foreach($attributes as $attribute)
                                                @php $hasValues = !empty($product->attributes[$attribute->id]); @endphp
                                                <option value="attr_row_{{ $attribute->id }}" data-attr-name="{{ $attribute->name }}" {{ $hasValues ? 'disabled' : '' }}>
                                                    {{ $attribute->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div id="activeAttributeRows" class="md:col-span-3 flex flex-wrap gap-4">
                                        @foreach($attributes as $attribute)
                                            @php $hasValues = !empty($product->attributes[$attribute->id]); @endphp
                                            <div id="attr_row_{{ $attribute->id }}" class="hidden-attr-row {{ $hasValues ? '' : 'hidden' }} p-5 bg-white rounded-3xl border-2 border-slate-100 relative shadow-sm min-w-[250px]">
                                                <button type="button" class="remove-attr-row absolute -top-3 -right-3 bg-white text-rose-500 w-8 h-8 rounded-full flex items-center justify-center border-2 border-slate-50 shadow-md hover:bg-rose-50 transition-all">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                                <label class="block text-[10px] font-black text-slate-800 uppercase tracking-widest mb-3 tracking-tight">
                                                    {{ $attribute->name }}
                                                </label>
                                                <select name="attributes[{{ $attribute->id }}][]" multiple class="select2-searchable attr-dropdown-matrix" data-attr-id="{{ $attribute->id }}" data-attr-name="{{ $attribute->name }}">
                                                    @foreach($attribute->values as $value)
                                                        @php $isChecked = in_array($value->id, (array)($product->attributes[$attribute->id] ?? [])); @endphp
                                                        <option value="{{ $value->id }}" data-value-name="{{ $value->name }}" {{ $isChecked ? 'selected' : '' }}>{{ $value->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                             </div>
                        </div>

                        {{-- 2. Matrix Table (Full Width Bottom) --}}
                        <div class="w-full">
                            <div id="variantMatrixWrapper" class="{{ ($product->product_variants && $product->product_variants->count()) ? '' : 'hidden' }}">
                                <div class="overflow-x-auto rounded-[2rem] border-2 border-slate-100 shadow-2xl bg-white">
                                    <table class="w-full text-left text-[11px] border-collapse min-w-[1000px]">
                                        <thead><tr class="bg-slate-50/50 text-slate-500 uppercase font-black border-b border-slate-100"></tr></thead>
                                        <tbody id="variantMatrixBody"></tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="matrixPlaceholder" class="{{ ($product->product_variants && $product->product_variants->count()) ? 'hidden' : '' }} flex flex-col items-center justify-center p-20 border-4 border-dashed border-slate-50 rounded-[3rem] text-slate-300">
                                <i class="fas fa-table-list text-5xl mb-4 text-rose-100"></i>
                                <h4 class="text-base font-black text-slate-700">Waiting for configuration...</h4>
                                <p class="text-[10px] mt-2">Select attributes above to build your variant matrix.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- 3. Product Settings & Categorization --}}
            <div id="settingsSection" class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-folder-tree mr-2 text-[#a91b43]"></i> Settings & Categorization
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status</label>
                        <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>Draft</option>
                            <option value="archived" {{ old('status', $product->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Featured</label>
                        <select name="is_featured" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="0" {{ old('is_featured', $product->is_featured) == '0' ? 'selected' : '' }}>No</option>
                            <option value="1" {{ old('is_featured', $product->is_featured) == '1' ? 'selected' : '' }}>Yes — Homepage</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tax Setting</label>
                        <select name="tax_class_id" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                            <option value="">No Tax / Standard</option>
                            @foreach($taxClasses as $tax)
                                <option value="{{ $tax->id }}" {{ old('tax_class_id', $product->tax_class_id) == $tax->id ? 'selected' : '' }}>{{ $tax->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Display Order</label>
                        <input type="number" name="display_order" value="{{ old('display_order', $product->display_order ?? 0) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                    <div class="pt-4 border-t border-slate-100 col-span-full grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Primary Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" id="category_id" required class="w-full select2-searchable">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Sub Category <span class="text-rose-500">*</span></label>
                            <select name="sub_category_id" id="sub_category_id" required class="w-full select2-searchable">
                                <option value="">Select Sub Category</option>
                                @foreach($subCategories as $sub)
                                    <option value="{{ $sub->id }}" {{ old('sub_category_id', $product->sub_category_id) == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Child Category</label>
                            <select name="child_category_id" id="child_category_id" class="w-full select2-searchable">
                                <option value="">--- Select Child Category ---</option>
                                @foreach($childCategories as $child)
                                    <option value="{{ $child->id }}" {{ old('child_category_id', $product->child_category_id) == $child->id ? 'selected' : '' }}>{{ $child->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Offer Collections Section --}}
                    <div class="pt-6 border-t border-slate-100 col-span-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">Offer Collections / Specials</label>
                                <select name="offer_collections[]" id="offer_collections" class="w-full select2-searchable" multiple>
                                    @php $selectedCollections = $product->offerCollections->pluck('id')->toArray(); @endphp
                                    @foreach($offerCollections as $collection)
                                        <option value="{{ $collection->id }}" @selected(in_array($collection->id, $selectedCollections))>
                                            {{ $collection->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex items-center gap-3 pb-2">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="show_offer_on_homepage" value="1" @checked($product->show_offer_on_homepage) class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#a91b43]"></div>
                                </label>
                                <div>
                                    <span class="text-sm font-bold text-slate-700">Show in Home Page</span>
                                    <p class="text-[10px] text-slate-400 font-medium tracking-tight">Product will display in Offer Collection section on homepage</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5. SEO Details --}}
            <div id="seoSection" class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-search mr-2 text-[#a91b43]"></i> SEO Optimization
                </h3>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Meta Keywords</label>
                            <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Meta Description</label>
                        <textarea name="meta_description" rows="2"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">{{ old('meta_description', $product->meta_description) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tags (Comma Separated)</label>
                        @php $tagsString = is_array($product->tags) ? implode(', ', $product->tags) : $product->tags; @endphp
                        <input type="text" name="tags" value="{{ old('tags', $tagsString) }}"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all">
                    </div>
                </div>
            </div>

            {{-- 6. Related Products --}}
            <div class="card-glass p-6 rounded-2xl shadow-sm bg-white">
                <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                    <i class="fas fa-link mr-2 text-[#a91b43]"></i> Related Product Suggestions
                </h3>
                <select name="related_products[]" id="related_products" multiple class="w-full select2-searchable">
                    @foreach($products as $p)
                        @if($p->id != $product->id)
                            <option value="{{ $p->id }}" {{ in_array($p->id, (array)old('related_products', $product->related_products ?? [])) ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            {{-- 7. UPDATE PRODUCT --}}
            <div class="card-glass p-4 rounded-3xl border-2 border-[#a91b43]/10 shadow-2xl bg-gradient-to-br from-white to-rose-50/10">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-4 rounded-2xl text-sm font-black hover:bg-[#8e1436] transition-all flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i> UPDATE PRODUCT DETAILS
                </button>
            </div>

        </div>{{-- end main content --}}
    </div>
</form>
<div id="hidden-file-store" class="hidden"></div>
</div>
@endsection

@push('scripts')
<link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
<style>
.ql-toolbar.ql-snow { border-radius: 8px 8px 0 0 !important; border-color: #e2e8f0 !important; background: #fff; padding: 6px 8px !important; }
.ql-container.ql-snow { border-radius: 0 0 8px 8px !important; border-color: #e2e8f0 !important; background: #f8fafc; font-size: 13px; }
.ql-editor { font-family: inherit; line-height: 1.6; }
.ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; }
.ql-toolbar .ql-stroke { stroke: #64748b; }
.ql-toolbar .ql-fill { fill: #64748b; }
.ql-toolbar button:hover .ql-stroke, .ql-toolbar button.ql-active .ql-stroke { stroke: #a91b43 !important; }
.ql-toolbar button:hover .ql-fill, .ql-toolbar button.ql-active .ql-fill { fill: #a91b43 !important; }
</style>
<script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
<script>
$(document).ready(function() {

    // --- Quill Rich Text Editors ---
    const quillToolbar = [
        ['bold', 'italic', 'underline', 'strike'],
        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
        ['link', 'clean']
    ];

    const quillDesc = new Quill('#short_description_editor', {
        theme: 'snow',
        placeholder: 'Enter product description...',
        modules: { toolbar: quillToolbar }
    });
    const descContent = $('#short_description').val();
    if (descContent) quillDesc.clipboard.dangerouslyPasteHTML(descContent);

    const quillSpec = new Quill('#full_description_editor', {
        theme: 'snow',
        placeholder: 'Enter product specification...',
        modules: { toolbar: quillToolbar }
    });
    const specContent = $('#full_description').val();
    if (specContent) quillSpec.clipboard.dangerouslyPasteHTML(specContent);

    // --- Consolidated Form Submission Validation ---
    $('#productForm').on('submit', function(e) {
        let isValid = true;
        $('#imagesErrorMsg, #nameErrorMsg, #slugErrorMsg').addClass('hidden');
        $('.border-rose-500').removeClass('border-rose-500');

        // 1. Check Uniqueness (Async check results)
        if (!$('#nameErrorMsg').hasClass('hidden')) {
            toastr.error("Product name already exists!");
            $('#productName').addClass('border-rose-500');
            isValid = false;
        }
        if (!$('#slugErrorMsg').hasClass('hidden')) {
            toastr.error("Product slug already exists!");
            $('#productSlug').addClass('border-rose-500');
            isValid = false;
        }

        // 2. Weight Validation
        const isVar = $('#isVariantCheckbox').is(':checked');
        if (!isVar) {
            const weightInput = $('input[name="weight"]');
            const weightVal = parseFloat(weightInput.val());
            if (isNaN(weightVal) || weightVal < 0.1 || weightVal > 10) {
                toastr.error("Weight must be between 0.10 and 10 KG!");
                weightInput.addClass('border-rose-500');
                isValid = false;
            }
        } else {
            $('.v-weight-field').each(function() {
                const val = parseFloat($(this).val());
                if (isNaN(val) || val < 0.1 || val > 10) {
                    toastr.error("Variant weight must be between 0.10 and 10 KG!");
                    $(this).addClass('border-rose-500');
                    isValid = false;
                    return false;
                }
            });
        }

        // 3. Image & Variant Validation
        if (!isVar) {
            const $imgInput = $('#generalImagesInput');
            const existingCount = $('input[name="existing_images[]"]').length;
            if ((!$imgInput[0].files || $imgInput[0].files.length === 0) && existingCount === 0) {
                isValid = false;
                $('#imagesErrorMsg').removeClass('hidden');
                toastr.error("Please ensure at least one product image exists.");
            }
        } else {
            const $rows = $('#variantMatrixBody tr');
            if ($rows.length > 0) {
                $rows.each(function() {
                    const $row = $(this);
                    const $vImg = $row.find('.v-file-input');
                    const $vPrice = $row.find('.v-price-input');
                    const $vSku = $row.find('input[name*="v_sku"]');
                    
                    let existingVarImgs = [];
                    try {
                        const existingVal = $row.find('.existing-images-input').val();
                        existingVarImgs = JSON.parse(existingVal || '[]');
                    } catch(e) { existingVarImgs = []; }

                    if ($vPrice.val() === '') {
                        isValid = false;
                        $vPrice.addClass('border-rose-500');
                    }
                    if ($vSku.val() === '') {
                        isValid = false;
                        $vSku.addClass('border-rose-500');
                    }

                    if ((!$vImg[0].files || $vImg[0].files.length === 0) && existingVarImgs.length === 0) {
                        isValid = false;
                        $row.find('label').addClass('border-rose-500');
                        $row.find('.v-img-error-msg').removeClass('hidden');
                    }
                });
            }
        }

        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const $firstError = $('.border-rose-500').first();
            if ($firstError.length) {
                $([document.documentElement, document.body]).animate({
                    scrollTop: $firstError.offset().top - 200
                }, 500);
            }
            return false;
        }

        // Populate hidden fields from Quill
        $('#short_description').val(quillDesc.root.innerHTML);
        $('#full_description').val(quillSpec.root.innerHTML);
    });
    // --- End Consolidated Submission ---
    // --- End Quill ---

    function slugify(text) {
        return text.toString().toLowerCase().replace(/\s+/g, '-').replace(/[^\w\-]+/g, '').replace(/\-\-+/g, '-').replace(/^-+/, '').replace(/-+$/, '');
    }

    $('#productName').on('input', function() {
        const val = $(this).val();
        $('#productSlug').val(slugify(val)).trigger('change');
        checkUniqueness('name', val, '#nameErrorMsg');
    });

    $('#productSlug').on('change', function() {
        checkUniqueness('slug', $(this).val(), '#slugErrorMsg');
    });

    function checkUniqueness(field, value, errorId) {
        if (!value) return;
        $.get("{{ route('admin.products.check-uniqueness') }}", { field: field, value: value, exclude: "{{ $product->id }}" }, function(res) {
            if (res.exists) {
                $(errorId).removeClass('hidden').fadeIn();
                $(`input[name="${field}"]`).addClass('border-rose-500 bg-rose-50');
            } else {
                $(errorId).fadeOut(() => $(errorId).addClass('hidden'));
                $(`input[name="${field}"]`).removeClass('border-rose-500 bg-rose-50');
            }
        });
    }

    $('.select2-searchable').select2({ width: '100%', placeholder: "Select..." });

    $('#isVariantCheckbox').on('change', function() {
        const isVar = $(this).is(':checked');
        if (isVar) {
            $('#variantConfigurationSection').removeClass('hidden').hide().fadeIn(500);
            $('#pricingStockSection, #generalSkuField, #productImagesSection').fadeOut(300, () => $('#pricingStockSection, #generalSkuField, #productImagesSection').addClass('hidden'));
            $('#regular_price').removeAttr('required');
        } else {
            $('#variantConfigurationSection').fadeOut(300, () => $('#variantConfigurationSection').addClass('hidden'));
            $('#pricingStockSection, #generalSkuField, #productImagesSection').removeClass('hidden').hide().fadeIn(500);
            $('#regular_price').attr('required', 'required');
        }
    });

    $(document).on('change', '#attributePicker', function() {
        const rowId = $(this).val();
        if(!rowId) return;
        $(`#${rowId}`).removeClass('hidden').hide().fadeIn(400);
        $(this).find(`option[value="${rowId}"]`).attr('disabled', 'disabled');
        $(this).val('').trigger('change.select2');
        $(`#${rowId}`).find('.select2-searchable').select2({ width: '100%' });
    });

    $(document).on('click', '.remove-attr-row', function() {
        const $row = $(this).closest('.hidden-attr-row');
        const rowId = $row.attr('id');
        if(confirm("Remove attribute?")) {
            $row.fadeOut(300, function() {
                $(this).addClass('hidden').find('select').val(null).trigger('change');
                $('#attributePicker').find(`option[value="${rowId}"]`).removeAttr('disabled').trigger('change.select2');
                generateVariantMatrix();
            });
        }
    });

    const existingVariants = {!! json_encode($product->product_variants) !!};
    let removedCombinations = new Set();
    $(document).on('change', '.attr-dropdown-matrix', generateVariantMatrix);
    $(document).on('click', '.remove-variant-row', function() {
        const $row = $(this).closest('tr');
        const combo = $row.find('.variant-comb-input').val();
        if(confirm("Confirm removal of this variant? Unused attribute tags will be automatically removed from the Configuration Suite.")) {
            const ids = combo.split(',');
            const $allOtherRows = $('#variantMatrixBody tr').not($row);
            
            ids.forEach(id => {
                let remains = false;
                $allOtherRows.each(function(){
                    if($(this).find('.variant-comb-input').val().split(',').includes(id)) remains = true;
                });
                
                if(!remains) {
                    $('.attr-dropdown-matrix').each(function(){
                        const $s = $(this);
                        let v = $s.val();
                        if(v && v.includes(id)) {
                            v = v.filter(x => x != id);
                            $s.val(v).trigger('change');
                        }
                    });
                }
            });

            removedCombinations.add(combo);
            generateVariantMatrix();
        }
    });

    if($('#isVariantCheckbox').is(':checked')) generateVariantMatrix();

    function generateVariantMatrix() {
        let currentUIData = {};
        $('#variantMatrixBody tr').each(function() {
            const combo = $(this).find('.variant-comb-input').val();
            const $fileInput = $(this).find('.v-file-input');
            if(combo) {
                currentUIData[combo] = {
                    price: $(this).find(`input[name="v_price[${combo}]"]`).val(),
                    sale_price: $(this).find(`input[name="v_sale_price[${combo}]"]`).val(),
                    stock: $(this).find(`input[name="v_stock[${combo}]"]`).val(),
                    low: $(this).find(`input[name="v_low_stock[${combo}]"]`).val(),
                    sku: $(this).find(`input[name="v_sku[${combo}]"]`).val(),
                    weight: $(this).find(`input[name="v_weight[${combo}]"]`).val(),
                    existing_images: $(this).find('.existing-images-input').val(),
                    new_preview: $(this).find('.v-new-previews').html()
                };
                // Preserve files
                if($fileInput[0] && $fileInput[0].files.length > 0) {
                    $fileInput.attr('id', 'temp_file_' + combo.replace(/,/g, '_'));
                    $('#hidden-file-store').append($fileInput);
                }
            }
        });

        let selectedValues = [];
        let selectedNames = [];
        $('.attr-dropdown-matrix').each(function() {
            const $s = $(this);
            const valIds = $s.val();
            if(valIds && valIds.length > 0) {
                const attrId = $s.data('attr-id'), attrName = $s.data('attr-name');
                selectedNames.push({id: attrId, name: attrName});
                selectedValues.push(valIds.map(vid => ({id: vid, name: $s.find(`option[value="${vid}"]`).text()})));
            }
        });

        if(selectedNames.length === 0) {
            $('#variantMatrixWrapper').addClass('hidden');
            $('#matrixPlaceholder').removeClass('hidden');
            return;
        }

        $('#variantMatrixWrapper').removeClass('hidden');
        $('#matrixPlaceholder').addClass('hidden');

        const $thead = $('#variantMatrixWrapper thead tr').empty();
        selectedNames.forEach(a => $thead.append(`<th class="px-5 py-4 text-[#334155] font-extrabold uppercase tracking-widest text-[10px] bg-slate-50/80">${a.name}</th>`));
        $thead.append(`
            <th class="px-4 py-4 text-[#334155] font-extrabold uppercase tracking-widest text-[10px] bg-slate-50/80">Pricing (₹) <span class="text-rose-500">*</span></th>
            <th class="px-4 py-4 text-[#334155] font-extrabold uppercase tracking-widest text-[10px] bg-slate-50/80">Stock / Alert</th>
            <th class="px-4 py-4 text-[#334155] font-extrabold uppercase tracking-widest text-[10px] bg-slate-50/80">Logistics</th>
            <th class="px-4 py-4 text-[#334155] font-extrabold uppercase tracking-widest text-[10px] bg-slate-50/80 border-l-2 border-slate-100">Images <span class="text-rose-500">*</span></th>
            <th class="px-3 py-4 bg-slate-50/80"></th>
        `);

        const combinations = cartesian(selectedValues);
        const $tbody = $('#variantMatrixBody').empty();

        combinations.forEach((comb, idx) => {
            const comboIds = comb.map(v => v.id).join(',');
            if(removedCombinations.has(comboIds)) return;

            // FUZZY MATCHING: If exact combo key doesn't exist, look for a parent combination (subset)
            let ui = currentUIData[comboIds];
            if(!ui) {
                const newIds = comboIds.split(',');
                const fuzzyKey = Object.keys(currentUIData).find(oldKey => {
                    const oldIds = oldKey.split(',');
                    return oldIds.every(id => newIds.includes(id));
                });
                if(fuzzyKey) ui = currentUIData[fuzzyKey];
            }

            const existing = findExisting(comboIds);
            let cells = comb.map(v => `<td class="px-5 py-5"><span class="px-3 py-1 bg-rose-50 text-[#a91b43] rounded-full font-bold text-[11px] border border-rose-100 whitespace-nowrap">${v.name}</span></td>`).join('');
            
            const $row = $(`
                <tr class="border-b border-slate-100 hover:bg-slate-50/30 transition-colors align-top">
                    ${cells}
                    <td class="px-4 py-4">
                        <div class="flex flex-col gap-2 min-w-[130px]">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Price (₹) <span class="text-rose-500">*</span></label>
                                <input type="number" min="0" name="v_price[${comboIds}]" value="${ui ? ui.price : (existing ? existing.price : ($('#regular_price').val() || ''))}" required class="v-price-input w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-black text-[#a91b43] focus:border-[#a91b43] outline-none transition-all" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Offer Price</label>
                                <input type="number" min="0" name="v_sale_price[${comboIds}]" value="${ui ? ui.sale_price : (existing ? existing.sale_price : '')}" class="v-sale-price-input w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-bold text-slate-500 focus:border-[#a91b43] outline-none transition-all" placeholder="0.00">
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col gap-2 min-w-[110px]">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Stock Qty</label>
                                <input type="number" min="0" name="v_stock[${comboIds}]" value="${ui ? ui.stock : (existing ? existing.stock_quantity : '0')}" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-bold text-slate-800 outline-none focus:border-[#a91b43] transition-all" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-rose-400 uppercase tracking-widest mb-1">Low Alert</label>
                                <input type="number" min="0" name="v_low_stock[${comboIds}]" value="${ui ? ui.low : (existing ? (existing.low_stock_threshold || 0) : 0)}" class="w-full bg-white border border-slate-200 rounded-lg px-3 py-1.5 text-sm font-bold text-rose-400 outline-none focus:border-[#a91b43] transition-all" placeholder="0">
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex flex-col gap-2 min-w-[150px]">
                            <div>
                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Variant SKU <span class="text-rose-500">*</span></label>
                                <input type="text" name="v_sku[${comboIds}]" value="${ui ? ui.sku : (existing ? (existing.sku || '') : '')}" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs font-bold text-slate-600 outline-none focus:border-[#a91b43] transition-all" placeholder="e.g. SKU-001">
                            </div>
                            <div>
                                <label class="block text-[9px] font-black text-[#a91b43] uppercase tracking-widest mb-1">Weight (KG)</label>
                                <input type="number" step="0.01" min="0.1" name="v_weight[${comboIds}]" value="${ui ? ui.weight : (existing ? (existing.weight || '0.10') : '0.10')}" class="v-weight-field w-full bg-white border border-rose-200 rounded-lg px-3 py-1.5 text-xs font-bold text-rose-700 outline-none focus:border-[#a91b43] transition-all" placeholder="0.10">
                                <p class="text-[7px] text-rose-500 font-bold mt-1 uppercase leading-tight">
                                    Format: 0.10 (100g)
                                </p>
                            </div>
                            <input type="hidden" name="variant_combinations[]" value="${comboIds}" class="variant-comb-input">
                        </div>
                    </td>
                    <td class="px-4 py-4 border-l-2 border-slate-100 min-w-[220px]">
                        <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Images <span class="text-rose-500">*</span></label>
                        <div class="flex flex-row items-start gap-3">
                            <label class="shrink-0 w-11 h-11 flex flex-col items-center justify-center bg-white border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:border-[#a91b43] hover:text-[#a91b43] transition-all group shadow-sm">
                                <i class="fas fa-camera text-slate-300 group-hover:text-[#a91b43] group-hover:scale-110 transition-all text-base"></i>
                                <span class="text-[8px] text-slate-300 group-hover:text-[#a91b43] font-bold mt-0.5">Add</span>
                                <input type="file" name="v_images[${comboIds}][]" ${ (() => {
                                    let arr = [];
                                    if (ui && ui.existing_images) {
                                        try { arr = JSON.parse(ui.existing_images); } catch(e) { arr = []; }
                                    } else if (!ui && existing) {
                                        arr = existing.images || existing.image;
                                        if(typeof arr === 'string') try { arr = JSON.parse(arr); } catch(e) { arr = [arr]; }
                                    }
                                    return (arr && arr.length > 0) ? '' : 'required';
                                })() } class="v-file-input hidden" multiple accept="image/*">
                            </label>
                            <div class="v-preview-container flex flex-row flex-wrap items-center gap-2 overflow-x-auto max-w-[350px] custom-scrollbar">
                                <div class="v-existing-previews flex flex-row flex-wrap gap-2">
                                    ${(() => {
                                        let arr = [];
                                        if (ui && ui.existing_images) {
                                            try { arr = JSON.parse(ui.existing_images); } catch(e) { arr = []; }
                                        } else if (!ui && existing) {
                                            arr = existing.images || existing.image;
                                            if(typeof arr === 'string') try { arr = JSON.parse(arr); } catch(e) { arr = [arr]; }
                                        }
                                        if(!Array.isArray(arr)) arr = arr ? [arr] : [];
                                        
                                        return arr.map(img => `
                                            <div class="relative shrink-0 flex items-center bg-white border border-slate-200 p-0.5 rounded-lg group w-12 h-12 shadow-sm overflow-visible">
                                                <img src="${window.UPLOAD_URL}/${img}" class="w-full h-full rounded-md object-cover">
                                                <button type="button" class="remove-variant-image absolute -top-1.5 -right-1.5 bg-[#a91b43] text-white w-4 h-4 rounded-full flex items-center justify-center border border-white shadow-sm z-20 text-[8px]" data-type="existing" data-combo="${comboIds}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>`).join('');
                                    })()}
                                </div>
                                <div class="v-new-previews flex flex-row flex-wrap gap-2">
                                    ${ui ? ui.new_preview || '' : ''}
                                </div>
                            </div>
                            <input type="hidden" name="v_existing_images[${comboIds}]" value='${ui ? ui.existing_images : (existing && (existing.images || existing.image) ? (typeof (existing.images || existing.image) === 'string' ? (existing.images || existing.image) : JSON.stringify(existing.images || existing.image)) : "[]")}' class="existing-images-input">
                        </div>
                        <div class="v-img-error-msg error-text text-[10px] text-rose-500 font-bold mt-1 hidden">Image required.</div>
                    </td>
                    <td class="px-3 py-4 text-right">
                        <button type="button" class="remove-variant-row w-7 h-7 rounded-full flex items-center justify-center text-slate-300 hover:bg-rose-50 hover:text-rose-500 transition-all">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </td>
                </tr>
            `);

            // Restore files with fuzzy matching
            let $preserved = $('#hidden-file-store').find('#temp_file_' + comboIds.replace(/,/g, '_'));
            if(!$preserved.length) {
                 const newIds = comboIds.split(',');
                 $('#hidden-file-store input').each(function() {
                     const oldCombo = $(this).attr('id').replace('temp_file_', '').replace(/_/g, ',');
                     const oldIds = oldCombo.split(',');
                     if(oldIds.every(id => newIds.includes(id))) {
                         $preserved = $(this).clone(true); // Clone to allow multiple inheriting rows
                         return false; 
                     }
                 });
            }

            if($preserved.length) {
                $row.find('.v-file-input').replaceWith($preserved.removeAttr('id'));
            }

            $tbody.append($row);
        });
    }

    function findExisting(ids) {
        if(!existingVariants) return null;
        const sIn = ids.split(',').sort().join(',');
        return existingVariants.find(v => {
            if(!v.combination) return false;
            return sIn === Object.values(v.combination).flat().sort().join(',');
        });
    }

    // Duplicate submit handler merged into the main consolidated handler above.

    // Toggle required attributes based on variant state
    function updateRequiredState() {
        const isVar = $('#isVariantCheckbox').is(':checked');
        $('#regular_price').prop('required', !isVar);
        $('input[name="sku"]').prop('required', !isVar);
        
        // Image requiredness depends on existing images
        const existingCount = $('input[name="existing_images[]"]').length;
        $('#generalImagesInput').prop('required', !isVar && existingCount === 0);
    }

    $('#isVariantCheckbox').on('change', function() {
        updateRequiredState();
        const isVar = $(this).is(':checked');
        $('#pricingStockSection').toggleClass('hidden', isVar);
        $('#generalSkuField').toggleClass('hidden', isVar);
    });
    
    // Set initial state
    updateRequiredState();

    function cartesian(args) {
        let r = [], max = args.length - 1;
        function helper(arr, i) { for (let j = 0, l = args[i].length; j < l; j++) { let a = arr.slice(0); a.push(args[i][j]); if (i === max) r.push(a); else helper(a, i + 1); } }
        helper([], 0); return r;
    }

    // Price Validation for Main Fields
    $('#regular_price, #sale_price').on('input', function() {
        const price = parseFloat($('#regular_price').val()) || 0;
        const sale = parseFloat($('#sale_price').val()) || 0;

        if (sale > price && price > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Sale Price',
                text: 'Main sale price cannot be greater than regular price.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $('#sale_price').val(price);
        }
    });

    // Price Validation for Variants
    $(document).on('input', '.v-price-input, .v-sale-price-input', function() {
        const $row = $(this).closest('tr');
        const price = parseFloat($row.find('.v-price-input').val()) || 0;
        const sale = parseFloat($row.find('.v-sale-price-input').val()) || 0;

        if (sale > price && price > 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid Sale Price',
                text: 'Sale price cannot be greater than regular price.',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000
            });
            $row.find('.v-sale-price-input').val(price);
        }
    });

    $(document).on('change', '.v-file-input', function() {
        const $input = $(this);
        const $td = $input.closest('td');
        const newPreview = $td.find('.v-new-previews');
        const comboIds = $td.closest('tr').find('.variant-comb-input').val();
        
        const dt = new DataTransfer();
        // Keep existing files if any (from our custom tracker)
        const oldFiles = $input.data('files-list') || [];
        oldFiles.forEach(f => dt.items.add(f));
        
        // Add new selection
        Array.from(this.files).forEach(f => dt.items.add(f));
        
        this.files = dt.files;
        $input.data('files-list', Array.from(this.files));

        newPreview.empty();
        Array.from(this.files).forEach((f, idx) => {
            const r = new FileReader();
            r.onload = e => newPreview.append(`
                <div class="relative shrink-0 flex items-center bg-white border border-slate-200 p-0.5 rounded-xl group w-14 h-14 shadow-sm overflow-visible">
                    <img src="${e.target.result}" class="w-full h-full rounded-lg object-cover">
                    <button type="button" class="remove-variant-image absolute -top-1.5 -right-1.5 bg-[#a91b43] text-white w-5 h-5 rounded-full flex items-center justify-center border-2 border-white shadow-md z-20 opacity-100 transition-all text-[10px]" data-type="new" data-index="${idx}" data-combo="${comboIds}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`);
            r.readAsDataURL(f);
        });
    });

    $(document).on('click', '.remove-variant-image', function() {
        const $btn = $(this);
        const type = $btn.data('type');
        const combo = $btn.data('combo');
        const $td = $btn.closest('td');

        if(type === 'existing') {
            const $hidden = $td.find('.existing-images-input');
            let images = [];
            try { 
                images = JSON.parse($hidden.val() || '[]'); 
            } catch(e) { images = []; }
            
            const imgUrl = $btn.siblings('img').attr('src').replace(window.UPLOAD_URL + '/', '');
            images = images.filter(img => img !== imgUrl);
            $hidden.val(JSON.stringify(images));
            $btn.parent().remove();
        } else {
            const $input = $td.find('.v-file-input');
            const dt = new DataTransfer();
            const files = $input.data('files-list') || Array.from($input[0].files);
            const indexToRemove = parseInt($btn.attr('data-index'));
            
            const newFilesList = [];
            for(let i=0; i<files.length; i++) {
                if(i !== indexToRemove) {
                    dt.items.add(files[i]);
                    newFilesList.push(files[i]);
                }
            }
            $input[0].files = dt.files;
            $input.data('files-list', newFilesList);
            $btn.parent().remove();
            
            // Re-index new image previews
            $td.find('.remove-variant-image[data-type="new"]').each(function(i) {
                $(this).attr('data-index', i).data('index', i);
            });
        }
    });

    $('#generalImagesInput').on('change', function() {
        const $input = $(this);
        const preview = $('#generalImagesPreview');
        const dt = new DataTransfer();
        
        // Keep existing new files
        const oldFiles = $input.data('files-list') || [];
        oldFiles.forEach(f => dt.items.add(f));
        
        // Add new selection
        Array.from(this.files).forEach(f => dt.items.add(f));
        
        this.files = dt.files;
        $input.data('files-list', Array.from(this.files));

        preview.empty();
        if (this.files.length > 0 || $('input[name="existing_images[]"]').length > 0) {
            $('#galleryWrapper').removeClass('hidden');
        }

        Array.from(this.files).forEach((f, idx) => {
            const r = new FileReader();
            r.onload = e => preview.append(`
                <div class="relative group w-24 h-24 bg-white rounded-xl shadow-sm border border-slate-200 overflow-visible">
                    <img src="${e.target.result}" class="w-full h-full rounded-xl object-cover">
                    <button type="button" class="remove-general-image-new absolute -top-2 -right-2 bg-rose-600 text-white w-6 h-6 rounded-full flex items-center justify-center border-2 border-white shadow-lg z-10 opacity-100 transition-all text-[10px]" data-index="${idx}">
                        <i class="fas fa-times"></i>
                    </button>
                </div>`);
            r.readAsDataURL(f);
        });
    });

    $(document).on('click', '.remove-general-image-new', function() {
        const $btn = $(this);
        const $input = $('#generalImagesInput');
        const dt = new DataTransfer();
        const files = $input.data('files-list') || Array.from($input[0].files);
        const indexToRem = parseInt($btn.data('index'));
        
        const newFilesList = [];
        for(let i=0; i<files.length; i++) {
            if(i !== indexToRem) {
                dt.items.add(files[i]);
                newFilesList.push(files[i]);
            }
        }
        $input[0].files = dt.files;
        $input.data('files-list', newFilesList);
        $btn.parent().remove();
        
        // Hide wrapper if no images left
        if ($('#unifiedGalleryList').children(':visible').length === 0) {
            $('#galleryWrapper').addClass('hidden');
        }
        
        // Re-index remaining ones
        $('#generalImagesPreview .remove-general-image-new').each(function(i) {
            $(this).attr('data-index', i).data('index', i);
        });
    });

    $(document).on('click', '.remove-existing-general-image', function() {
        if(confirm("Are you sure you want to remove this image from the product?")) {
            $(this).parent().fadeOut(300, function() {
                $(this).remove();
                if ($('#unifiedGalleryList').children(':visible').length === 0) {
                    $('#galleryWrapper').addClass('hidden');
                }
            });
        }
    });
    
    // Cascading Category Selects
    var isFirstCategoryChange = true;
    $('#category_id').on('change', function () {
        var id = $(this).val();
        
        // Skip AJAX if it's the initial load and options are already pre-rendered by PHP
        if (isFirstCategoryChange) {
            isFirstCategoryChange = false;
            if ($('#sub_category_id option').length > 1) return;
        }

        $('#sub_category_id').html('<option value="">Select Sub Category</option>');
        $('#child_category_id').html('<option value="">--- Select Child Category ---</option>');
        
        if (id) {
            $.getJSON("{{ url('admin/get-sub-categories') }}/" + id, function (d) {
                $.each(d, function (k, v) { 
                    $('#sub_category_id').append('<option value="'+v.id+'">'+v.name+'</option>'); 
                });
                $('#sub_category_id').trigger('change.select2'); // Notify Select2
            });
        }
    });

    var isFirstSubCategoryChange = true;
    $('#sub_category_id').on('change', function () {
        var id = $(this).val();

        // Skip AJAX if it's initial load and child categories are already pre-rendered
        if (isFirstSubCategoryChange) {
            isFirstSubCategoryChange = false;
            if ($('#child_category_id option').length > 1) return;
        }

        $('#child_category_id').html('<option value="">--- Select Child Category ---</option>');
        
        if (id) {
            $.getJSON("{{ url('admin/get-child-categories') }}/" + id, function (d) {
                $.each(d, function (k, v) { 
                    $('#child_category_id').append('<option value="'+v.id+'">'+v.name+'</option>'); 
                });
                $('#child_category_id').trigger('change.select2'); // Notify Select2
            });
        }
    });
});
</script>
@endpush