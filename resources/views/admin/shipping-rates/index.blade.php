@extends('admin.layouts.admin')

@section('title', 'Shipping Rates')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Shipping Rates</h2>
                <p class="text-[10px] text-slate-400 font-medium">Define costs for each shipping class</p>
            </div>
            <form method="GET" action="{{ route('admin.shipping-rates.index') }}" class="flex items-center pt-2 md:pt-0">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                </select>
            </form>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('admin.shipping-rates.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search rates..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);"
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.shipping-rates.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </form>

            <div class="w-full sm:w-40">
                @php
                    $currentStatus = request('status', 'all');
                    $statuses = [
                        'all' => 'All Status',
                        'active' => 'Active',
                        'inactive' => 'Inactive'
                    ];
                @endphp
                <div class="relative">
                    <select onchange="window.location.href=this.value" 
                            class="appearance-none bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] block w-full px-4 py-2 transition-all outline-none cursor-pointer shadow-sm">
                        @foreach($statuses as $key => $label)
                            <option value="{{ route('admin.shipping-rates.index', ['status' => $key, 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.shipping-rates.create') }}" class="bg-[#a91b43] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all whitespace-nowrap shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Add New Rate
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Name</th>
                    <th class="pb-3">Shipping Class</th>
                    <th class="pb-3">Location</th>
                    <th class="pb-3">Cost</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($shippingRates as $rate)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 font-bold text-slate-800">{{ $rate->name }}</td>
                    <td class="py-3">
                        <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                            {{ $rate->shippingClass->name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="py-3 text-slate-500 text-xs">
                        {{ $rate->country ?? 'Global' }} {{ $rate->state ? '/ '.$rate->state : '' }}
                    </td>
                    <td class="py-3 font-bold text-slate-800">₹{{ number_format($rate->cost, 2) }}</td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $rate->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $rate->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.shipping-rates.edit', $rate->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('admin.shipping-rates.destroy', $rate->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="mt-6">
        {{ $shippingRates->links() }}
    </div>
</div>
@endsection
