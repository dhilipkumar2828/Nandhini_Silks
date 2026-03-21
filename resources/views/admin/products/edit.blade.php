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

            {{-- ===== ATTRIBUTES + VARIANT IMAGES ===== --}}
            @if(isset($attributes) && $attributes->count())
            @php
                $savedAttributes  = $product->attributes ?? [];
                $savedColorImages = $product->color_images ?? [];
            @endphp
            <div class="card-glass p-6 rounded-2xl">
                <h3 class="text-base font-bold text-slate-800 mb-1 flex items-center">
                    <i class="fas fa-tags mr-2 text-[#a91b43]"></i> Attributes & Variant Images
                </h3>
                <p class="text-[10px] text-slate-400 mb-5">
                    Check/uncheck attribute values. Image slots appear for checked variants — upload new images to update or add images for that variant.
                </p>

                <div class="space-y-6">
                    @foreach($attributes as $attribute)
                    <div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xs font-black text-slate-700 uppercase tracking-wider">
                                @if($attribute->group) {{ $attribute->group }} — @endif {{ $attribute->name }}
                            </span>
                            @php
                                $hasSwatches = $attribute->values->contains(fn($v) =>
                                    $v->swatch_value && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v->swatch_value)
                                );
                            @endphp
                            @if($hasSwatches)
                                <span class="text-[9px] bg-rose-50 text-[#a91b43] px-2 py-0.5 rounded font-bold">COLOR</span>
                            @endif
                        </div>

                        {{-- Chips --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @forelse($attribute->values as $value)
                                @php
                                    $isChecked = in_array($value->id, (array)($savedAttributes[$attribute->id] ?? []));
                                    $swatch    = $value->swatch_value;
                                    $isHex     = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                    $hasImages = !empty($savedColorImages[$value->id]);
                                @endphp
                                <label class="attr-chip inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border-2 text-xs cursor-pointer transition-all select-none {{ $isChecked ? 'border-[#a91b43] bg-rose-50/30' : 'border-slate-200 bg-white hover:border-[#a91b43]' }}"
                                    data-attr-id="{{ $attribute->id }}"
                                    data-value-id="{{ $value->id }}"
                                    data-value-name="{{ $value->name }}"
                                    data-swatch="{{ $swatch }}">
                                    <input type="checkbox"
                                        name="attributes[{{ $attribute->id }}][]"
                                        value="{{ $value->id }}"
                                        class="accent-[#a91b43] attr-checkbox"
                                        {{ $isChecked ? 'checked' : '' }}>
                                    @if($isHex)
                                        <span class="w-4 h-4 rounded-full border border-slate-300 flex-shrink-0 shadow-sm" style="background:{{ $swatch }}"></span>
                                    @elseif($swatch)
                                        <img src="{{ asset('uploads/'.$swatch) }}" class="w-4 h-4 rounded-full object-cover flex-shrink-0">
                                    @endif
                                    <span class="font-semibold text-slate-700">{{ $value->name }}</span>
                                    @if($hasImages)
                                        <span class="w-4 h-4 rounded-full bg-emerald-100 flex items-center justify-center ml-0.5" title="{{ count($savedColorImages[$value->id]) }} image(s)">
                                            <i class="fas fa-check text-[7px] text-emerald-500"></i>
                                        </span>
                                    @endif
                                </label>
                            @empty
                                <span class="text-[10px] text-slate-400 italic">No values added.</span>
                            @endforelse
                        </div>

                        {{-- Variant Slots container --}}
                        <div id="variantSlots_{{ $attribute->id }}" class="space-y-3">
                            {{-- Pre-render slots for already-checked values --}}
                            @foreach($attribute->values as $value)
                                @php
                                    $isChecked = in_array($value->id, (array)($savedAttributes[$attribute->id] ?? []));
                                    $swatch    = $value->swatch_value;
                                    $isHex     = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                                    $hasImages = !empty($savedColorImages[$value->id]);
                                @endphp
                                @if($isChecked)
                                <div class="variant-slot rounded-xl border border-slate-200 bg-white overflow-hidden"
                                    data-slot-value-id="{{ $value->id }}"
                                    data-slot-attr-id="{{ $attribute->id }}">
                                    <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-b border-slate-100">
                                        <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                                            @if($isHex)
                                                <span class="w-3.5 h-3.5 rounded-full inline-block border border-slate-200" style="background:{{ $swatch }}"></span>
                                            @endif
                                            <i class="fas fa-layer-group text-[#a91b43]"></i>
                                            Images for: <strong class="text-[#a91b43]">{{ $value->name }}</strong>
                                            @if($hasImages)
                                                <span class="ml-1 px-1.5 py-0.5 bg-emerald-100 text-emerald-600 text-[8px] font-bold rounded">{{ count($savedColorImages[$value->id]) }} saved</span>
                                            @endif
                                        </span>
                                        <span class="text-[9px] text-slate-400">Shown when customer picks this variant</span>
                                    </div>
                                    <div class="p-4">
                                        {{-- Existing images --}}
                                        @if($hasImages)
                                        <div class="flex flex-wrap gap-2 mb-3">
                                            @foreach($savedColorImages[$value->id] as $cImg)
                                            <img src="{{ asset('uploads/'.$cImg) }}" class="w-14 h-14 rounded-lg object-cover border border-slate-200 shadow-sm">
                                            @endforeach
                                        </div>
                                        <p class="text-[10px] text-amber-600 mb-2 flex items-center gap-1">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            Upload new images below to replace the {{ count($savedColorImages[$value->id]) }} existing image(s) for <strong>{{ $value->name }}</strong>.
                                        </p>
                                        @endif

                                        <div class="variant-preview flex flex-wrap gap-2 mb-2 min-h-[2px]"></div>
                                        <label class="flex items-center gap-3 border-2 border-dashed border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#a91b43] hover:bg-rose-50/20 transition-all group">
                                            <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-rose-50 flex-shrink-0 transition-all">
                                                <i class="fas fa-upload text-slate-400 group-hover:text-[#a91b43] transition-colors"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-xs font-bold text-slate-600 group-hover:text-[#a91b43] transition-colors">
                                                    {{ $hasImages ? 'Replace' : 'Upload' }} images for {{ $value->name }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">PNG, JPG, WEBP • Multiple allowed</p>
                                            </div>
                                            <input type="file" name="color_images[{{ $value->id }}][]" multiple accept="image/*" class="hidden variant-file-input">
                                        </label>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    @if(!$loop->last)<hr class="border-slate-100">@endif
                    @endforeach
                </div>
            </div>
            @endif

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
                    <select name="related_products[]" multiple class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all" style="height: 120px;">
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ in_array($p->id, old('related_products', $product->related_products ?? [])) ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Hold Ctrl (Windows) or Command (Mac) to select multiple products.</p>
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

{{-- ===== VARIANT SLOT TEMPLATE ===== --}}
<template id="variantSlotTemplate">
    <div class="variant-slot rounded-xl border border-slate-200 bg-white overflow-hidden" data-slot-value-id="__VID__" data-slot-attr-id="__AID__">
        <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-b border-slate-100">
            <span class="text-xs font-bold text-slate-700 flex items-center gap-2">
                <span class="swatch-preview"></span>
                <i class="fas fa-layer-group text-[#a91b43]"></i>
                Images for: <strong class="text-[#a91b43] variant-name-label"></strong>
            </span>
            <span class="text-[9px] text-slate-400">Shown when customer selects this variant</span>
        </div>
        <div class="p-4">
            <div class="variant-preview flex flex-wrap gap-2 mb-3 min-h-[2px]"></div>
            <label class="flex items-center gap-3 border-2 border-dashed border-slate-200 rounded-xl p-3 cursor-pointer hover:border-[#a91b43] hover:bg-rose-50/20 transition-all group">
                <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center group-hover:bg-rose-50 transition-all flex-shrink-0">
                    <i class="fas fa-upload text-slate-400 group-hover:text-[#a91b43] transition-colors"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-slate-600 group-hover:text-[#a91b43] transition-colors">Upload variant images</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">PNG, JPG, WEBP • Multiple allowed</p>
                </div>
                <input type="file" name="color_images[__VID__][]" multiple accept="image/*" class="hidden variant-file-input">
            </label>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
$(document).ready(function () {

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

    // ── Attribute checkbox → variant image slots ───────────────────────
    $(document).on('change', '.attr-checkbox', function () {
        var chip      = $(this).closest('.attr-chip');
        var attrId    = chip.data('attr-id');
        var valueId   = chip.data('value-id');
        var name      = chip.data('value-name');
        var swatch    = chip.data('swatch') || '';
        var container = $('#variantSlots_' + attrId);

        if ($(this).is(':checked')) {
            chip.addClass('border-[#a91b43] bg-rose-50/30');
            if (container.find('.variant-slot[data-slot-value-id="'+valueId+'"]').length === 0) {
                container.append(buildVariantSlot(attrId, valueId, name, swatch));
            }
        } else {
            chip.removeClass('border-[#a91b43] bg-rose-50/30');
            container.find('.variant-slot[data-slot-value-id="'+valueId+'"]').slideUp(200, function () { $(this).remove(); });
        }
    });

    // ── Live preview for variant images (event delegation) ─────────────
    $(document).on('change', '.variant-file-input', function () {
        var preview = $(this).closest('.variant-slot').find('.variant-preview')[0];
        previewFiles(this.files, preview);
    });

    // ── Validation ─────────────────────────────────────────────────────
    $('#productForm').validate({
        rules: {
            name          : 'required',
            category_id   : 'required',
            regular_price : { required: true, number: true, min: 0 },
            stock_quantity: { required: true, digits: true, min: 0 }
        }
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