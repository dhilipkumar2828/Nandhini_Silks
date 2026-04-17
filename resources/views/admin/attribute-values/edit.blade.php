@extends('admin.layouts.admin')

@section('title', 'Edit Attribute Value')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.attribute-values.index', ['attribute_id' => $attributeValue->attribute_id]) }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Attribute Value</h2>
    </div>

    <form action="{{ route('admin.attribute-values.update', $attributeValue->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Select Attribute</label>
                <select name="attribute_id" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all appearance-none bg-white" required>
                    @foreach($attributes as $attr)
                        <option value="{{ $attr->id }}" {{ $attributeValue->attribute_id == $attr->id ? 'selected' : '' }}>
                            {{ $attr->name }}
                        </option>
                    @endforeach
                </select>
                @error('attribute_id') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Value Name</label>
                <input type="text" name="name" id="valueName" value="{{ old('name', $attributeValue->name) }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" required>
                @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Attribute Value Slug (auto-generated)</label>
                <input type="text" name="slug" id="valueSlug" value="{{ old('slug', $attributeValue->slug) }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" required>
                @error('slug') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Swatch Color (Hex)</label>
                <div class="flex space-x-2">
                    <input type="color" id="swatch_picker" value="{{ $attributeValue->swatch_value ?? '#ffffff' }}" class="h-10 w-10 p-0 rounded-lg border border-slate-100 cursor-pointer" oninput="document.getElementById('swatch_value').value = this.value">
                    <input type="text" name="swatch_value" id="swatch_value" value="{{ $attributeValue->swatch_value }}" class="flex-1 px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" placeholder="#ffffff">
                </div>
                <p class="text-[10px] text-slate-400 mt-1">Use color OR upload an image below.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Swatch Image</label>
                <input type="file" name="swatch_image" accept="image/*" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all bg-white">
                @php
                    $swatch = $attributeValue->swatch_value;
                    $isColor = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                @endphp
                @if($swatch && !$isColor)
                    <div class="mt-2 text-[10px] text-slate-400">Current image:</div>
                    <img src="{{ asset('uploads/' . $swatch) }}" alt="Swatch" class="mt-1 w-10 h-10 rounded-lg border border-slate-100 object-cover">
                @endif
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Display Order</label>
                <input type="number" name="display_order" value="{{ $attributeValue->display_order }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all appearance-none bg-white">
                    <option value="1" {{ $attributeValue->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$attributeValue->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-[#a91b43]/20 hover:bg-[#940437] transition-all">
                    Update Value
                </button>
            </div>
        </div>
    </form>
</div>
@push('scripts')
<script>
    document.getElementById('valueName').addEventListener('input', function() {
        document.getElementById('valueSlug').value = slugify(this.value);
    });
</script>
@endpush
@endsection
