@extends('admin.layouts.admin')

@section('title', 'Add Child Category')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <form id="childCategoryForm" action="{{ route('admin.child-categories.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Parent Category <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Sub Category <span class="text-rose-500">*</span></label>
                    <select name="sub_category_id" id="sub_category_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="">Select Sub Category</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Child Category Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="childCategoryName" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Enter child category name">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Child Category Slug (auto-generated) <span class="text-rose-500">*</span></label>
                    <input type="text" name="slug" id="childCategorySlug" value="{{ old('slug') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="child-category-slug">
                    @error('slug') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Display Order <span class="text-rose-500">*</span></label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}" required min="0"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Child Category Image <span class="text-rose-500">*</span></label>
                    <input type="file" name="image" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    @error('image') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <div class="flex bg-slate-100 p-1 rounded-xl w-fit">
                        <label class="relative flex-1">
                            <input type="radio" name="status" value="1" class="sr-only peer" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                            <div class="px-4 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                                Active
                            </div>
                        </label>
                        <label class="relative flex-1">
                            <input type="radio" name="status" value="0" class="sr-only peer" {{ old('status') == '0' ? 'checked' : '' }}>
                            <div class="px-4 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                                Inactive
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Description</label>
                <textarea name="description" rows="3"
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                    placeholder="Enter description">{{ old('description') }}</textarea>
            </div>

            <hr class="border-slate-100 my-6">

            <div class="space-y-3">
                <h3 class="text-base font-bold text-slate-800">SEO Settings</h3>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Meta title">
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Description</label>
                    <textarea name="meta_description" rows="2"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Meta description">{{ old('meta_description') }}</textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Keyword1, Keyword2">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.child-categories.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Child Category
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const nameInput = document.getElementById('childCategoryName');
        const slugInput = document.getElementById('childCategorySlug');
        let timer;

        nameInput.addEventListener('input', function() {
            slugInput.value = slugify(this.value);
            checkSlug(slugInput.value);
            checkName(this.value);
        });

        slugInput.addEventListener('input', function() {
            this.value = slugify(this.value);
            checkSlug(this.value);
        });

        function checkName(name) {
            clearTimeout(timer);
            if (!name) return;
            
            timer = setTimeout(() => {
                $.get('{{ route("admin.child-categories.check-name") }}', { name: name }, function(data) {
                    const errorId = 'name-error';
                    let errorMsg = document.getElementById(errorId);
                    
                    if (data.exists) {
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.id = errorId;
                            errorMsg.className = 'text-rose-500 text-[10px] mt-1 font-bold';
                            nameInput.parentNode.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'This child category name is already taken!';
                        nameInput.classList.add('border-rose-500');
                    } else {
                        if (errorMsg) errorMsg.remove();
                        nameInput.classList.remove('border-rose-500');
                    }
                });
            }, 500);
        }

        function checkSlug(slug) {
            clearTimeout(timer);
            if (!slug) return;
            
            timer = setTimeout(() => {
                $.get('{{ route("admin.child-categories.check-slug") }}', { slug: slug }, function(data) {
                    const errorId = 'slug-error';
                    let errorMsg = document.getElementById(errorId);
                    
                    if (data.exists) {
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.id = errorId;
                            errorMsg.className = 'text-rose-500 text-[10px] mt-1 font-bold';
                            slugInput.parentNode.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'This slug is already taken!';
                        slugInput.classList.add('border-rose-500');
                    } else {
                        if (errorMsg) errorMsg.remove();
                        slugInput.classList.remove('border-rose-500');
                    }
                });
            }, 500);
        }

        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            if(category_id) {
                $.ajax({
                    url: "{{ url('admin/get-sub-categories') }}/" + category_id,
                    type: "GET",
                    dataType: "json",
                    success: function(data) {
                        $('#sub_category_id').empty();
                        $('#sub_category_id').append('<option value="">Select Sub Category</option>');
                        $.each(data, function(key, value) {
                            $('#sub_category_id').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    }
                });
            } else {
                $('#sub_category_id').empty();
                $('#sub_category_id').append('<option value="">Select Sub Category</option>');
            }
        });

        $("#childCategoryForm").validate({
            rules: {
                category_id: "required",
                sub_category_id: "required",
                name: "required",
                display_order: "required",
                status: "required"
            }
        });
    });
</script>
@endpush
