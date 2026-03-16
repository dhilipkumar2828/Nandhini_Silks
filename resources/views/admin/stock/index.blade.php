@extends('admin.layouts.admin')

@section('title', 'Stock Maintenance')

@section('content')
<div class="card-glass p-0 rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-white/50">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Inventory Management</h2>
            <p class="text-xs text-slate-500 font-medium">Update stock levels and manage thresholds</p>
        </div>
        <div class="flex space-x-3">
             <a href="{{ route('admin.products.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
            <button type="submit" form="bulkStockForm" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                Update All Changes
            </button>
        </div>
    </div>

    <form id="bulkStockForm" action="{{ route('admin.stock.update-bulk') }}" method="POST">
        @csrf
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                        <th class="py-4 px-6">Product Details</th>
                        <th class="py-4 px-4 text-center">Current Qty</th>
                        <th class="py-4 px-4 text-center">Reserved</th>
                        <th class="py-4 px-4 text-center">Available</th>
                        <th class="py-4 px-4 text-center">Low Threshold</th>
                        <th class="py-4 px-4">Supplier</th>
                        <th class="py-4 px-6 text-right">Status / History</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @foreach($products as $product)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all group">
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-800 text-sm group-hover:text-[#a91b43] transition-colors">{{ $product->name }}</div>
                            <div class="flex items-center space-x-3 mt-1">
                                <span class="bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded text-[10px] font-bold">{{ $product->sku ?? 'NO-SKU' }}</span>
                                <span class="text-[10px] font-bold text-slate-400 font-mono">{{ $product->category->name ?? 'Category NA' }}</span>
                            </div>
                        </td>

                        <td class="py-4 px-4 text-center">
                            <input type="number" name="stock[{{ $product->id }}][quantity]" value="{{ $product->stock_quantity }}" 
                                class="w-16 text-center bg-slate-50 border border-slate-200 px-2 py-1.5 rounded-lg text-sm font-bold text-slate-800 focus:border-[#a91b43] outline-none transition-all">
                        </td>

                        <td class="py-4 px-4 text-center">
                            <input type="number" name="stock[{{ $product->id }}][reserved_stock]" value="{{ $product->reserved_stock }}" 
                                class="w-16 text-center bg-transparent border border-transparent px-2 py-1.5 rounded-lg text-sm font-medium text-slate-400 focus:bg-slate-50 focus:border-slate-200 outline-none transition-all">
                        </td>

                        <td class="py-4 px-4 text-center">
                            <span class="text-sm font-black {{ $product->available_stock <= $product->low_stock_threshold ? 'text-rose-500' : 'text-emerald-500' }}">
                                {{ $product->available_stock }}
                            </span>
                        </td>

                        <td class="py-4 px-4 text-center">
                             <input type="number" name="stock[{{ $product->id }}][low_stock_threshold]" value="{{ $product->low_stock_threshold }}" 
                                class="w-14 text-center bg-amber-50/50 border border-amber-100 px-1 py-1 rounded-md text-[11px] font-bold text-amber-600 outline-none">
                        </td>

                        <td class="py-4 px-4">
                            <input type="text" name="stock[{{ $product->id }}][supplier]" value="{{ $product->supplier }}" 
                                class="w-28 bg-transparent border-b border-slate-100 hover:border-slate-200 focus:border-[#a91b43] px-1 py-1 text-[11px] text-slate-500 italic outline-none transition-all" placeholder="Add supplier...">
                        </td>

                        <td class="py-4 px-6 text-right">
                            <div class="flex flex-col items-end space-y-1.5">
                                <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-widest 
                                    @if($product->stock_status == 'instock') bg-emerald-50 text-emerald-600 
                                    @elseif($product->stock_status == 'lowstock') bg-amber-50 text-amber-600 
                                    @else bg-rose-50 text-rose-600 @endif">
                                    {{ $product->stock_status }}
                                </span>
                                <a href="{{ route('admin.stock.logs', $product->id) }}" class="text-[10px] font-bold text-[#a91b43] hover:underline flex items-center">
                                    <i class="fas fa-history mr-1.5"></i> Activity Logs
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>
@endsection
