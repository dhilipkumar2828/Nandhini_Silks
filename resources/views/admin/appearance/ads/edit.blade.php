@extends('admin.layouts.admin')

@section('title', 'Edit Ad')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.ads.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Ad</h2>
    </div>

    <form action="{{ route('admin.ads.update', $ad->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Ad Title (Internal Reference)</label>
                <input type="text" name="title" value="{{ $ad->title }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Current Image</label>
                <img src="{{ asset('uploads/' . $ad->image) }}" class="w-32 h-32 object-cover rounded-xl mb-3 border border-slate-100 shadow-sm">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Change Image</label>
                <input type="file" name="image" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm">
                @error('image') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Link URL</label>
                <input type="text" name="link" value="{{ $ad->link }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Behavior</label>
                    <select name="open_new_tab" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                        <option value="0" {{ !$ad->open_new_tab ? 'selected' : '' }}>Open in Same Tab</option>
                        <option value="1" {{ $ad->open_new_tab ? 'selected' : '' }}>Open in New Tab</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                        <option value="1" {{ $ad->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$ad->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all">
                    Update Ad
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
