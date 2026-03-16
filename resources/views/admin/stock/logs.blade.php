@extends('admin.layouts.admin')

@section('title', 'Stock Movement Log: ' . $product->name)

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex items-center mb-8">
        <a href="{{ route('admin.stock.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-black text-slate-800">Movement History</h2>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $product->name }} ({{ $product->sku }})</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-slate-50 p-4 rounded-xl">
            <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Current Stock</span>
            <span class="text-2xl font-black text-slate-800">{{ $product->stock_quantity }}</span>
        </div>
        <div class="bg-amber-50 p-4 rounded-xl">
            <span class="text-[9px] font-black text-amber-400 uppercase tracking-widest block mb-1">Reserved</span>
            <span class="text-2xl font-black text-amber-600">{{ $product->reserved_stock }}</span>
        </div>
        <div class="bg-emerald-50 p-4 rounded-xl">
            <span class="text-[9px] font-black text-emerald-400 uppercase tracking-widest block mb-1">Available</span>
            <span class="text-2xl font-black text-emerald-600">{{ $product->available_stock }}</span>
        </div>
        <div class="bg-rose-50 p-4 rounded-xl">
            <span class="text-[9px] font-black text-rose-400 uppercase tracking-widest block mb-1">Threshold</span>
            <span class="text-2xl font-black text-rose-600">{{ $product->low_stock_threshold }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-slate-400 text-[10px] font-black uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-4 px-2">Date & Time</th>
                    <th class="pb-4">Activity Type</th>
                    <th class="pb-4">Quantity</th>
                    <th class="pb-4">Running Balance</th>
                    <th class="pb-4">Transaction Details</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($logs as $log)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-4 px-2">
                        <div class="font-bold text-slate-700 text-xs">{{ $log->created_at->format('M d, Y') }}</div>
                        <div class="text-[10px] text-slate-400">{{ $log->created_at->format('H:i A') }}</div>
                    </td>
                    <td class="py-4">
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase 
                            @if($log->type == 'in') bg-emerald-100 text-emerald-600 
                            @elseif($log->type == 'out') bg-rose-100 text-rose-600 
                            @else bg-blue-100 text-blue-600 @endif">
                            {{ $log->type }}
                        </span>
                    </td>
                    <td class="py-4 font-black">
                        {{ $log->type == 'out' ? '-' : '+' }}{{ $log->quantity }}
                    </td>
                    <td class="py-4 font-mono text-xs text-slate-500">
                        {{ $log->balance_after }} units
                    </td>
                    <td class="py-4">
                        <div class="text-[11px] font-medium text-slate-600">{{ $log->reason ?? 'No description provided' }}</div>
                        @if($log->reference)
                            <div class="text-[9px] font-bold text-indigo-400 mt-0.5 uppercase">REF: #{{ $log->reference }}</div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-bold uppercase tracking-widest">No movement logs found for this product.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
