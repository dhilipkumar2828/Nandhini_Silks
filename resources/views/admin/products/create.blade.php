@extends('admin.layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="space-y-6">
    <form id="productForm" action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Basic Info & Description -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Product Information -->
                <div class="card-glass p-6 rounded-2xl">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-info-circle mr-2 text-[#a91b43]"></i> General Information
                    </h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Product Name <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="Enter product name">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">SKU</label>
                                <input type="text" name="sku" value="{{ old('sku') }}"
                                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                    placeholder="Unique SKU code">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-xs font-bold text-slate-700">Barcode / EAN</label>
                                <input type="text" name="barcode" value="{{ old('barcode') }}"
                                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                    placeholder="Barcode value">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Short Description</label>
                            <textarea name="short_description" rows="2"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="Brief overview for product summary">{{ old('short_description') }}</textarea>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Full Description</label>
                            <textarea name="full_description" rows="5"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="Detailed product description">{{ old('full_description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Media Section -->
                <div class="card-glass p-6 rounded-2xl">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-camera mr-2 text-[#a91b43]"></i> Product Media
                    </h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Product Images</label>
                            <input type="file" name="images[]" multiple
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                            <p class="text-[10px] text-slate-400">Multiple selection supported. Recommended size: 800x800px.</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Video URL (YouTube/Vimeo)</label>
                            <input type="url" name="video_url" value="{{ old('video_url') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="https://youtube.com/watch?v=...">
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="card-glass p-6 rounded-2xl">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <i class="fas fa-tag mr-2 text-[#a91b43]"></i> Pricing & Stock
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Regular Price <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">₹</span>
                                <input type="number" name="regular_price" step="0.01" value="{{ old('regular_price') }}" required
                                    class="w-full bg-slate-50 border border-slate-200 pl-8 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Sale Price</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-400">₹</span>
                                <input type="number" name="sale_price" id="sale_price" step="0.01" value="{{ old('sale_price') }}"
                                    class="w-full bg-slate-50 border border-slate-200 pl-8 pr-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Discount (%)</label>
                            <input type="number" name="discount_percent" id="discount_percent" step="0.01" value="{{ old('discount_percent') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="e.g. 10">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Stock Quantity <span class="text-rose-500">*</span></label>
                            <input type="number" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" required
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Sidebar Options -->
            <div class="space-y-6">
                <!-- Status & Organise -->
                <div class="card-glass p-6 rounded-2xl">
                    <h3 class="text-base font-bold text-slate-800 mb-4">Organization</h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Status</label>
                            <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                <option value="1">Active</option>
                                <option value="0">Draft</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Featured</label>
                            <select name="is_featured" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Category <span class="text-rose-500">*</span></label>
                            <select name="category_id" id="category_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Sub Category</label>
                            <select name="sub_category_id" id="sub_category_id" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                <option value="">Select Sub Category</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Child Category</label>
                            <select name="child_category_id" id="child_category_id" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                <option value="">Select Child Category</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Brand</label>
                            <input type="text" name="brand" value="{{ old('brand') }}"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                placeholder="Brand name">
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card-glass p-6 rounded-2xl">
                    <h3 class="text-base font-bold text-slate-800 mb-4">SEO Details</h3>
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Meta Title</label>
                            <input type="text" name="meta_title"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-xs font-bold text-slate-700">Meta Description</label>
                            <textarea name="meta_description" rows="3"
                                class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end space-x-3">
            <a href="{{ route('admin.products.index') }}" class="px-8 py-2.5 rounded-xl text-sm font-bold text-slate-600 hover:bg-white/50 transition-all">Discard</a>
            <button type="submit" class="bg-[#a91b43] text-white px-10 py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] shadow-xl shadow-pink-900/10 transition-all active:scale-95">
                Publish Product
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Cascade: Category -> SubCategory
        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            $('#sub_category_id').empty().append('<option value="">Select Sub Category</option>');
            $('#child_category_id').empty().append('<option value="">Select Child Category</option>');
            if(category_id) {
                $.ajax({
                    url: '/admin/get-sub-categories/' + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        // Cascade: SubCategory -> ChildCategory
        $('#sub_category_id').on('change', function() {
            var sub_category_id = $(this).val();
            $('#child_category_id').empty().append('<option value="">Select Child Category</option>');
            if(sub_category_id) {
                $.ajax({
                    url: '/admin/get-child-categories/' + sub_category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $.each(data, function(key, value) {
                            $('#child_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            }
        });

        $("#productForm").validate({
            rules: {
                name: "required",
                category_id: "required",
                regular_price: { required: true, number: true },
                stock_quantity: { required: true, number: true }
            }
        });
    });
</script>
@endpush
