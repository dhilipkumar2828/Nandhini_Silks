@extends('admin.layouts.admin')

@section('title', 'Edit Banner')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.banners.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Edit Banner</h2>
        </div>

        <form id="bannerForm" action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Desktop Image (1920x800)</label>
                    <div class="mb-3">
                         <img src="{{ asset('uploads/'.$banner->image) }}" class="h-32 w-full object-cover rounded-xl border border-slate-100 shadow-sm">
                    </div>
                    <input type="file" name="image"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Mobile Image (800x1200)</label>
                    <div class="mb-3">
                        @if($banner->image_mobile)
                            <img src="{{ asset('uploads/'.$banner->image_mobile) }}" class="h-32 w-full object-contain bg-slate-100 rounded-xl border border-slate-100 shadow-sm">
                        @else
                            <div class="h-32 w-full bg-slate-50 rounded-xl border border-dashed border-slate-200 flex items-center justify-center text-slate-400 text-xs">No mobile image</div>
                        @endif
                    </div>
                    <input type="file" name="image_mobile"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Banner Title</label>
                    <input type="text" name="title" value="{{ old('title', $banner->title) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Link URL</label>
                    <input type="text" name="link" value="{{ old('link', $banner->link) }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Display Order <span class="text-rose-500">*</span></label>
                    <input type="number" name="display_order" value="{{ old('display_order', $banner->display_order) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-widest">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status', $banner->status) == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $banner->status) == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 mt-4 border-t border-slate-100">
                <a href="{{ route('admin.banners.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Update Banner
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
