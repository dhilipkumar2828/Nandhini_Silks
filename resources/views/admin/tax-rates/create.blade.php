@extends('admin.layouts.admin')

@section('title', 'Create Tax Rate')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.tax-rates.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Add New Tax Rate</h2>
        </div>

        <form id="taxRateForm" action="{{ route('admin.tax-rates.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Tax Class <span class="text-rose-500">*</span></label>
                    <select name="tax_class_id" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800 font-bold">
                        <option value="">Select a Class</option>
                        @foreach($taxClasses as $class)
                            <option value="{{ $class->id }}" {{ old('tax_class_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Rate Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800"
                        placeholder="e.g. TN GST 12%">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Country (ISO)</label>
                    <input type="text" name="country" value="{{ old('country') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="IN" maxlength="2">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">State</label>
                    <input type="text" name="state" value="{{ old('state') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="Tamil Nadu">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">ZIP / Postcode</label>
                    <input type="text" name="zip" value="{{ old('zip') }}"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="* (for all)">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Tax Rate (%) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.0001" name="rate" value="{{ old('rate', 0) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] font-black text-slate-800 transition-all">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Priority <span class="text-rose-500">*</span></label>
                    <input type="number" name="priority" value="{{ old('priority', 1) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <input type="hidden" name="is_compound" value="0">
                    <input type="checkbox" name="is_compound" value="1" {{ old('is_compound') ? 'checked' : '' }} class="w-4 h-4 text-[#a91b43] border-slate-300 rounded focus:ring-[#a91b43]">
                    <div>
                        <span class="block text-xs font-bold text-slate-800">Compound Tax</span>
                        <span class="text-[10px] text-slate-400">Apply this tax on top of other taxes</span>
                    </div>
                </div>

                <div class="flex items-center space-x-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                    <input type="hidden" name="applies_to_shipping" value="0">
                    <input type="checkbox" name="applies_to_shipping" value="1" {{ old('applies_to_shipping') ? 'checked' : '' }} class="w-4 h-4 text-[#a91b43] border-slate-300 rounded focus:ring-[#a91b43]">
                    <div>
                        <span class="block text-xs font-bold text-slate-800">Apply to Shipping</span>
                        <span class="text-[10px] text-slate-400">Calculate tax on shipping costs as well</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.tax-rates.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Tax Rate
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#taxRateForm").validate({
            rules: {
                tax_class_id: "required",
                name: "required",
                rate: "required",
                priority: "required"
            },
            messages: {
                tax_class_id: "Please select a tax class",
                name: "Please enter rate name",
                rate: "Please enter rate percentage",
                priority: "Please set priority"
            }
        });
    });
</script>
@endpush
