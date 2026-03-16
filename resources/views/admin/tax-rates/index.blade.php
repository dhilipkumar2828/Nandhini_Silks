@extends('admin.layouts.admin')

@section('title', 'Tax Rates')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Tax Rates</h2>
            <p class="text-xs text-slate-400 font-medium">Define specific rates based on location</p>
        </div>
        <a href="{{ route('admin.tax-rates.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Rate
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2">Rate Name</th>
                    <th class="pb-3">Class</th>
                    <th class="pb-3">Location (C/S/Z)</th>
                    <th class="pb-3">Rate (%)</th>
                    <th class="pb-3">Priority</th>
                    <th class="pb-3">Options</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($taxRates as $rate)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 font-bold text-slate-800">{{ $rate->name }}</td>
                    <td class="py-3">
                        <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $rate->taxClass->name }}</span>
                    </td>
                    <td class="py-3 text-xs text-slate-400">
                        {{ $rate->country ?? '*' }} / {{ $rate->state ?? '*' }} / {{ $rate->zip ?? '*' }}
                    </td>
                    <td class="py-3 font-black text-slate-800">{{ number_format($rate->rate, 2) }}%</td>
                    <td class="py-3 text-center">
                        <span class="bg-slate-100 text-slate-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $rate->priority }}</span>
                    </td>
                    <td class="py-3">
                        <div class="flex flex-col space-y-0.5">
                            <span class="text-[9px] font-bold {{ $rate->is_compound ? 'text-amber-500' : 'text-slate-300' }}">
                                <i class="fas fa-layer-group mr-1 font-bold"></i> Compound
                            </span>
                            <span class="text-[9px] font-bold {{ $rate->applies_to_shipping ? 'text-indigo-500' : 'text-slate-300' }}">
                                <i class="fas fa-truck mr-1 font-bold"></i> Shipping
                            </span>
                        </div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $rate->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $rate->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.tax-rates.edit', $rate->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $rate->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $rate->id }}" action="{{ route('admin.tax-rates.destroy', $rate->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Tax Rate?',
            text: "This rate will no longer be applied to calculations!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#a91b43',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
