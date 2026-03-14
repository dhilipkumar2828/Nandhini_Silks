@extends('admin.layouts.admin')

@section('title', 'Edit Sub Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <form id="subCategoryForm" action="{{ route('admin.sub-categories.update', $subCategory->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Parent Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('category_id', $subCategory->category_id) == $category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Sub Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $subCategory->name) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Display Order <span class="text-rose-500">*</span></label>
                    <input type="number" name="display_order" value="{{ old('display_order', $subCategory->display_order) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Sub Category Image</label>
                    <div class="flex items-center space-x-3">
                        @if($subCategory->image)
                            <img src="{{ asset('uploads/' . $subCategory->image) }}" class="w-10 h-10 rounded-lg object-cover">
                        @endif
                        <input type="file" name="image" 
                            class="flex-1 bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status', $subCategory->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $subCategory->status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Description</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">{{ old('description', $subCategory->description) }}</textarea>
            </div>

            <hr class="border-slate-100 my-6">

            <div class="space-y-3">
                <h3 class="text-base font-bold text-slate-800">SEO Settings</h3>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $subCategory->meta_title) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Description</label>
                    <textarea name="meta_description" rows="2"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">{{ old('meta_description', $subCategory->meta_description) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.sub-categories.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Update Sub Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#subCategoryForm").validate({
            rules: {
                category_id: "required",
                name: "required",
                display_order: "required",
                status: "required"
            }
        });
    });
</script>
@endpush
