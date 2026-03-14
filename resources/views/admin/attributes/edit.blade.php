@extends('admin.layouts.admin')

@section('title', 'Edit Attribute')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.attributes.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Attribute</h2>
    </div>

    <form action="{{ route('admin.attributes.update', $attribute->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Attribute Group</label>
                <input type="text" name="group" value="{{ $attribute->group }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" required>
                @error('group') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Attribute Name</label>
                <input type="text" name="name" value="{{ $attribute->name }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all" required>
                @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 focus:outline-none focus:border-[#a91b43] text-sm transition-all appearance-none bg-white">
                    <option value="1" {{ $attribute->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$attribute->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold shadow-lg shadow-[#a91b43]/20 hover:bg-[#940437] transition-all">
                    Update Attribute
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
