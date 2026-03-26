@extends('admin.layouts.admin')

@section('title', 'Edit Offer Collection')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl bg-white shadow-sm">
        <h2 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
            <i class="fas fa-edit mr-2 text-[#a91b43]"></i> Update Offer Collection
        </h2>

        <form action="{{ route('admin.offer-collections.update', $offerCollection->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Collection Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="collectionName" value="{{ old('name', $offerCollection->name) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm outline-none focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all text-slate-800 font-semibold"
                        placeholder="e.g. Pongal Offer, Summer Sale">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-4 py-2.5 rounded-xl text-sm outline-none focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all text-slate-800 font-semibold cursor-pointer">
                        <option value="active" {{ old('status', $offerCollection->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $offerCollection->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-6 border-t border-slate-50">
                <a href="{{ route('admin.offer-collections.index') }}" class="px-6 py-2.5 rounded-xl text-sm text-slate-500 hover:bg-slate-50 transition-all font-bold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-10 py-2.5 rounded-xl text-sm hover:bg-[#940437] shadow-xl shadow-[#a91b43]/10 transition-all font-bold active:scale-[0.98]">
                    Update Collection
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
