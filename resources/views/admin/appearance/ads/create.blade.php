@extends('admin.layouts.admin')

@section('title', 'Create Ad')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.ads.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Add New Ad</h2>
        </div>

        <form id="adForm" action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Ad Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800"
                        placeholder="e.g. Sidebar Promo">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Ad Image <span class="text-rose-500">*</span></label>
                    <input type="file" name="image" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    @error('image') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Link URL</label>
                    <input type="text" name="link" value="{{ old('link') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="https://nandhinisilks.com/shop">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Open Behavior <span class="text-rose-500">*</span></label>
                    <select name="open_new_tab" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="0" {{ old('open_new_tab') == '0' ? 'selected' : '' }}>Same Tab</option>
                        <option value="1" {{ old('open_new_tab') == '1' ? 'selected' : '' }}>New Tab</option>
                    </select>
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
                <a href="{{ route('admin.ads.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Ad
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#adForm").validate({
            rules: {
                title: "required",
                image: "required",
                status: "required"
            },
            messages: {
                title: "Please enter ad title",
                image: "Please upload ad image",
                status: "Please select status"
            }
        });
    });
</script>
@endpush
