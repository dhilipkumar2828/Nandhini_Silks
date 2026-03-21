@extends('admin.layouts.admin')

@section('title', 'Edit Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <form id="categoryForm" action="{{ route('admin.categories.update', $category->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Display Order <span class="text-rose-500">*</span></label>
                    <input type="number" name="display_order" value="{{ old('display_order', $category->display_order) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Category Image</label>
                    @if($category->image)
                        <div class="mb-1.5">
                            <img src="{{ asset('uploads/' . $category->image) }}" class="w-16 h-16 rounded-lg object-cover border border-slate-100">
                        </div>
                    @endif
                    <input type="file" name="image" 
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status', $category->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $category->status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Description</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">{{ old('description', $category->description) }}</textarea>
            </div>

            <hr class="border-slate-100 my-6">

            <div class="space-y-3">
                <h3 class="text-base font-bold text-slate-800">SEO Settings</h3>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title', $category->meta_title) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Description</label>
                    <textarea name="meta_description" rows="2"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">{{ old('meta_description', $category->meta_description) }}</textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $category->meta_keywords) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Keyword1, Keyword2">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.categories.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Update Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#categoryForm").validate({
            rules: {
                name: "required",
                display_order: "required",
                status: "required"
            },
            messages: {
                name: "Please enter category name",
                display_order: "Please enter display order",
                status: "Please select status"
            }
        });
    });
</script>
@endpush
