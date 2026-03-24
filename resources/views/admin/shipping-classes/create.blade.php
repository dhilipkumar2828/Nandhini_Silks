@extends('admin.layouts.admin')

@section('title', 'Add New Shipping Class')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-8 rounded-2xl shadow-sm">
        <div class="flex justify-between items-center mb-8">
            <h2 class="text-xl font-bold text-slate-800">Add New Shipping Class</h2>
            <a href="{{ route('admin.shipping-classes.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-700">
                <i class="fas fa-arrow-left mr-1.5"></i> Back to List
            </a>
        </div>

        <form action="{{ route('admin.shipping-classes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Class Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Standard, Small Item, etc." required
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>

                <!-- Display Order -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Display Order</label>
                    <input type="number" name="display_order" value="{{ old('display_order', 0) }}"
                           class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">
                </div>
            </div>

            <!-- Description -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider">Description</label>
                <textarea name="description" rows="3" placeholder="Optional description..."
                          class="w-full bg-slate-50/50 border border-slate-100 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Status -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold uppercase text-slate-500 tracking-wider block mb-2">Status</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="1" checked class="text-[#a91b43] focus:ring-[#a91b43]">
                        <span class="text-xs font-bold text-slate-600">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="status" value="0" class="text-slate-400 focus:ring-slate-400">
                        <span class="text-xs font-bold text-slate-500">Inactive</span>
                    </label>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-4 rounded-xl text-sm font-bold shadow-lg shadow-[#a91b43]/20 hover:bg-[#940437] transition-all">
                    Save Shipping Class
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
