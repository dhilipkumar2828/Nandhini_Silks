@extends('admin.layouts.admin')

@section('title', 'Manage Banners')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center">
                <a href="{{ route('admin.banners.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-lg font-bold text-slate-800">Add Banners</h2>
            </div>
            <button type="button" onclick="addBannerRow()" class="flex items-center px-4 py-2 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 transition-all shadow-lg active:scale-95">
                <i class="fas fa-plus mr-1.5"></i> Add More Banner
            </button>
        </div>

        <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div id="bannerRows" class="space-y-6">
                <!-- Initial Row -->
                <div class="banner-row relative bg-slate-50/50 p-6 rounded-xl border border-slate-100 group">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                        <div class="md:col-span-4 space-y-4">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Banner Image (1920x800) <span class="text-rose-500">*</span></label>
                                <div class="relative group/img">
                                    <input type="file" name="banners[0][image]" required
                                        class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                        onchange="previewImage(this)">
                                    <div class="mt-2 hidden preview-container">
                                        <img src="" class="w-full h-24 object-cover rounded-lg border border-slate-200 shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Banner Title</label>
                                <input type="text" name="banners[0][title]"
                                    class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                    placeholder="Summer Collection">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Link URL</label>
                                <input type="text" name="banners[0][link]"
                                    class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                    placeholder="https://...">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Display Order</label>
                                <input type="number" name="banners[0][display_order]" value="0" required
                                    class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800">
                            </div>

                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Status</label>
                                <select name="banners[0][status]" class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-slate-100">
                <a href="{{ route('admin.banners.index') }}" class="px-6 py-2 rounded-lg text-xs text-slate-400 font-black uppercase tracking-widest hover:text-slate-600 transition-all">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-10 py-2.5 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-[#940437] shadow-xl shadow-pink-900/20 transition-all active:scale-[0.98]">
                    Save All Banners
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let rowCount = 1;

    function addBannerRow() {
        const container = document.getElementById('bannerRows');
        const newRow = document.createElement('div');
        newRow.className = 'banner-row relative bg-slate-50/50 p-6 rounded-xl border border-slate-100 group animate-in slide-in-from-top-2 duration-300';
        newRow.innerHTML = `
            <button type="button" onclick="this.closest('.banner-row').remove()" class="absolute -top-2 -right-2 w-6 h-6 bg-rose-500 text-white rounded-full flex items-center justify-center text-[10px] shadow-lg hover:bg-rose-600 transition-all z-10">
                <i class="fas fa-times"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                <div class="md:col-span-4 space-y-4">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Banner Image (1920x800) <span class="text-rose-500">*</span></label>
                        <div class="relative group/img">
                            <input type="file" name="banners[${rowCount}][image]" required
                                class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                                onchange="previewImage(this)">
                            <div class="mt-2 hidden preview-container">
                                <img src="" class="w-full h-24 object-cover rounded-lg border border-slate-200 shadow-sm">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Banner Title</label>
                        <input type="text" name="banners[${rowCount}][title]"
                            class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                            placeholder="Summer Collection">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Link URL</label>
                        <input type="text" name="banners[${rowCount}][link]"
                            class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800"
                            placeholder="https://...">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Display Order</label>
                        <input type="number" name="banners[${rowCount}][display_order]" value="${rowCount}" required
                            class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Status</label>
                        <select name="banners[${rowCount}][status]" class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-xs outline-none focus:border-[#a91b43] transition-all text-slate-800">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newRow);
        rowCount++;
    }


    function previewImage(input) {
        const container = input.nextElementSibling;
        const img = container.querySelector('img');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                container.classList.remove('hidden');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
