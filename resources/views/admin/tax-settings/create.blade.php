@extends('admin.layouts.admin')

@section('title', 'Create Tax Setting')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.tax-settings.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Add New Tax Setting</h2>
    </div>

    <form action="{{ route('admin.tax-settings.store') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Tax Name (e.g. GST 5%)</label>
                <input type="text" name="name" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" placeholder="GST 12%" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Percentage (%)</label>
                <input type="number" step="0.01" name="percentage" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" placeholder="12.00" required>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all">
                    Create Tax Setting
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
