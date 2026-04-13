@extends('admin.layouts.admin')

@section('title', 'Create Attribute Value')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.attribute-values.index', ['attribute_id' => $selectedAttributeId]) }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Add Attribute Value</h2>
        </div>

        <form id="attributeValueForm" action="{{ route('admin.attribute-values.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Select Attribute <span class="text-rose-500">*</span></label>
                    <select name="attribute_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="">Select Attribute</option>
                        @foreach($attributes as $attr)
                            <option value="{{ $attr->id }}" {{ $selectedAttributeId == $attr->id ? 'selected' : '' }}>
                                {{ $attr->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('attribute_id') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Value Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="valueName" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800 font-bold"
                        placeholder="e.g. Red, XL, Cotton">
                    <p class="text-[10px] text-slate-400 font-medium italic"><i class="fas fa-info-circle mr-1"></i> Tip: Use commas to add multiple values at once (e.g. S, M, L, XL)</p>
                    @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5" id="slugContainer">
                    <label class="block text-xs font-bold text-slate-700">Attribute Value Slug (auto-generated) <span class="text-rose-500">*</span></label>
                    <input type="text" name="slug" id="valueSlug" value="{{ old('slug') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="attribute-value-slug">
                    <p class="text-[10px] text-slate-400 font-medium italic slug-multiple-hint hidden"><i class="fas fa-magic mr-1"></i> Individual slugs will be auto-generated for multi-values.</p>
                    @error('slug') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Swatch Color (Hex)</label>
                    <div class="flex space-x-2">
                        <input type="color" id="swatch_picker" value="{{ old('swatch_value', '#ffffff') }}" class="h-9 w-10 p-1 bg-slate-50 border border-slate-200 rounded-lg cursor-pointer" oninput="document.getElementById('swatch_value').value = this.value">
                        <input type="text" name="swatch_value" id="swatch_value" value="{{ old('swatch_value') }}"
                            class="flex-1 bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                            placeholder="#ffffff">
                    </div>
                    <p class="text-[10px] text-slate-400">Use color OR upload an image below.</p>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Swatch Image</label>
                    <input type="file" name="swatch_image" accept="image/*"
                        class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-700">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Display Order <span class="text-rose-500">*</span></label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.attribute-values.index', ['attribute_id' => $selectedAttributeId]) }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Value
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        document.getElementById('valueName').addEventListener('input', function() {
            if(this.value.includes(',')) {
                $('.slug-multiple-hint').removeClass('hidden');
                $('#valueSlug').attr('readonly', true).addClass('bg-slate-100/50').val('Auto-generating...');
            } else {
                $('.slug-multiple-hint').addClass('hidden');
                $('#valueSlug').attr('readonly', false).removeClass('bg-slate-100/50').val(slugify(this.value));
            }
        });

        $("#attributeValueForm").validate({
            rules: {
                attribute_id: "required",
                name: "required",
                display_order: "required",
                status: "required"
            },
            messages: {
                attribute_id: "Please select attribute",
                name: "Please enter value name",
                display_order: "Please enter display order",
                status: "Please select status"
            }
        });
    });
</script>
@endpush
