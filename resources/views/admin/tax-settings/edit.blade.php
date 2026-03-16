@extends('admin.layouts.admin')

@section('title', 'Edit Tax Setting')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.tax-settings.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Tax Setting</h2>
    </div>

    <form action="{{ route('admin.tax-settings.update', $taxSetting->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Tax Name</label>
                <input type="text" name="name" value="{{ $taxSetting->name }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Percentage (%)</label>
                <input type="number" step="0.01" name="percentage" value="{{ $taxSetting->percentage }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                    <option value="1" {{ $taxSetting->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$taxSetting->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all">
                    Update Tax Setting
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
