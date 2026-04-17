@extends('admin.layouts.admin')

@section('title', 'Manage Stock')

@section('content')
<div class="max-w-7xl mx-auto" x-data="{ 
    expandedGroups: [], 
    quickRestock(id, isVariant = false, hasVariants = false) {
        const prefix = isVariant ? 'variants' : 'stock';
        let qtyToRestock = document.querySelector(`input[id='restock-${prefix}-${id}']`);
        
        if (!qtyToRestock || !qtyToRestock.value || parseInt(qtyToRestock.value) <= 0) return;

        const addVal = parseInt(qtyToRestock.value);

        if (!isVariant && hasVariants) {
            if (!this.expandedGroups.includes(id)) this.expandedGroups.push(id);
            const firstVarRow = document.querySelector(`.variant-of-${id}`);
            if (firstVarRow) {
                const vid = firstVarRow.getAttribute('data-variant-id');
                const vQtyInput = document.querySelector(`input[name='variants[${vid}][quantity]']`);
                if (vQtyInput) {
                    vQtyInput.value = (parseInt(vQtyInput.value) || 0) + addVal;
                    qtyToRestock.value = '';
                    this.refreshParentStock(id);
                    if (window.toastr) toastr.success(`Added ${addVal} units`);
                    return;
                }
            }
        }
        
        const currentQtyInput = document.querySelector(`input[name='${prefix}[${id}][quantity]']`);
        const visualQtyEl = document.querySelector(`#visual-qty-${id}`);
        
        if (currentQtyInput) {
            currentQtyInput.value = (parseInt(currentQtyInput.value) || 0) + addVal;
            qtyToRestock.value = '';
            if (visualQtyEl) visualQtyEl.innerText = currentQtyInput.value;
            
            if (window.toastr) toastr.success(`Added ${addVal} units`);
        }
    },
    refreshParentStock(parentId) {
        let total = 0;
        document.querySelectorAll(`.variant-of-${parentId}`).forEach(row => {
            const input = row.querySelector('input[name*=\'[quantity]\']');
            if (input) total += (parseInt(input.value) || 0);
        });
        const visual = document.querySelector(`#visual-qty-${parentId}`);
        const hidden = document.querySelector(`input[name='stock[${parentId}][quantity]']`);
        if (visual) visual.innerText = total;
        if (hidden) hidden.value = total;
    }
}">
    <!-- Simplified Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800">Stock Management</h1>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-1">Quickly update inventory levels</p>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.products.index') }}" class="text-sm font-bold text-slate-400 hover:text-slate-600 transition-all">Cancel</a>
            <button type="submit" form="bulkStockForm" class="bg-[#a91b43] text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#940437] shadow-lg shadow-rose-900/10 transition-all active:scale-95">
                Update All Changes
            </button>
        </div>
    </div>

    <form id="bulkStockForm" action="{{ route('admin.stock.update-bulk') }}" method="POST">
        @csrf
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] border-b border-slate-100">
                            <th class="py-5 px-8 w-16">#</th>
                            <th class="py-5 px-4 w-[50%]">Product Details</th>
                            <th class="py-5 px-4 text-center w-40">In Stock</th>
                            <th class="py-5 px-4 text-center w-40">Threshold</th>
                            <th class="py-5 px-8 text-right w-32">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @foreach($products as $product)
                        @php $hasVariants = $product->product_variants->count() > 0; @endphp
                        <tr class="hover:bg-slate-50/30 transition-all group">
                            <td class="py-6 px-8 text-xs font-bold text-slate-300">
                                {{ $products->firstItem() + $loop->index }}
                            </td>
                            
                            <td class="py-6 px-4">
                                <div class="flex items-center gap-4">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-100 overflow-hidden shadow-sm">
                                            @if($product->primary_image)
                                                <img src="{{ asset('uploads/'.$product->primary_image) }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300"><i class="fas fa-box"></i></div>
                                            @endif
                                        </div>
                                        @if($hasVariants)
                                        <button type="button" 
                                            @click="expandedGroups.includes({{ $product->id }}) ? expandedGroups = expandedGroups.filter(id => id !== {{ $product->id }}) : expandedGroups.push({{ $product->id }})"
                                            class="absolute -bottom-1 -right-1 w-5 h-5 bg-white rounded-lg shadow-sm border border-slate-100 flex items-center justify-center text-[#a91b43] hover:bg-[#a91b43] hover:text-white transition-all cursor-pointer">
                                            <i class="fas fa-plus text-[8px]" :class="expandedGroups.includes({{ $product->id }}) ? 'rotate-45' : ''"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $product->name }}</div>
                                        <div class="text-[10px] font-bold text-slate-400 mt-0.5">{{ $product->sku ?? 'NO-SKU' }} • <span class="text-[#a91b43]/60 italic">{{ $product->category->name ?? 'Collection' }}</span></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-6 px-4 text-center">
                                @if($hasVariants)
                                    <div id="visual-qty-{{ $product->id }}" class="w-24 mx-auto py-2.5 rounded-xl bg-slate-50 text-slate-400 text-sm font-black border border-slate-100 select-none cursor-not-allowed">
                                        {{ $product->stock_quantity }}
                                    </div>
                                    <input type="hidden" name="stock[{{ $product->id }}][quantity]" value="{{ $product->stock_quantity }}">
                                @else
                                    <input type="number" name="stock[{{ $product->id }}][quantity]" value="{{ $product->stock_quantity }}" 
                                        class="w-24 mx-auto py-2.5 text-center rounded-xl bg-slate-50 border border-slate-100 text-sm font-black text-slate-700 focus:bg-white focus:border-[#a91b43] outline-none transition-all shadow-sm">
                                @endif
                                <input type="hidden" name="stock[{{ $product->id }}][reserved_stock]" value="{{ $product->reserved_stock }}">
                                <input type="hidden" name="stock[{{ $product->id }}][offer_collection]" value="{{ $product->offer_collection }}">
                                <input type="hidden" name="stock[{{ $product->id }}][restock_date]" value="{{ $product->restock_date ? $product->restock_date->format('Y-m-d') : '' }}">
                                <input type="hidden" id="restock-stock-{{ $product->id }}" value="0">
                            </td>

                            <td class="py-6 px-4 text-center">
                                <input type="number" name="stock[{{ $product->id }}][low_stock_threshold]" value="{{ $product->low_stock_threshold }}" 
                                    class="w-20 mx-auto py-2 text-center rounded-xl bg-orange-50/50 border border-orange-100 text-xs font-black text-orange-600 focus:bg-white focus:border-orange-400 outline-none transition-all">
                            </td>

                            <td class="py-6 px-8 text-right">
                                <div class="flex flex-col items-end gap-1.5">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                                        @if($product->stock_status == 'instock') bg-emerald-50 text-emerald-600 border-emerald-100
                                        @elseif($product->stock_status == 'lowstock') bg-amber-50 text-amber-600 border-amber-100
                                        @else bg-rose-50 text-rose-600 border-rose-100 @endif">
                                        {{ $product->stock_status }}
                                    </span>
                                    <a href="{{ route('admin.stock.logs', $product->id) }}" class="text-[9px] font-black text-[#a91b43] uppercase tracking-widest hover:underline">History</a>
                                </div>
                            </td>
                        </tr>

                        <!-- Variants -->
                        @if($hasVariants)
                        @foreach($product->product_variants as $variant)
                        <tr x-show="expandedGroups.includes({{ $product->id }})" x-transition class="bg-slate-50/20 variant-of-{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                            <td class="py-3"></td>
                            <td class="py-3 px-4 pl-12 relative border-b border-slate-50">
                                <div class="absolute left-6 top-0 bottom-0 w-[1px] bg-slate-100"></div>
                                <div class="absolute left-6 top-1/2 w-4 h-[1px] bg-slate-100"></div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 overflow-hidden shadow-sm">
                                        @if($variant->image) <img src="{{ asset('uploads/'.$variant->image) }}" class="w-full h-full object-cover"> @else <div class="w-full h-full flex items-center justify-center text-slate-200"><i class="fas fa-tag text-[8px]"></i></div> @endif
                                    </div>
                                    <div class="text-[11px] font-black text-indigo-600 uppercase tracking-tighter">{{ implode(' | ', $variant->attribute_values ?? []) }}</div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-center border-b border-slate-50">
                                <input type="number" name="variants[{{ $variant->id }}][quantity]" value="{{ $variant->stock_quantity }}" 
                                    class="w-20 py-2 text-center text-xs font-black bg-white border border-slate-100 rounded-xl shadow-sm focus:border-indigo-400 outline-none">
                                <input type="hidden" id="restock-variants-{{ $variant->id }}" value="0">
                            </td>
                            <td class="border-b border-slate-50"></td>
                            <td class="py-3 px-8 text-right border-b border-slate-50"><span class="text-[8px] font-black text-slate-300 uppercase tracking-widest">Variant</span></td>
                        </tr>
                        @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($products->hasPages())
            <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Page {{ $products->currentPage() }} of {{ $products->lastPage() }}</p>
                    <div class="custom-pagination">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<style>
    .custom-pagination nav svg { height: 1.25rem; width: 1.25rem; }
    .custom-pagination nav span[aria-current="page"] span { background: #a91b43 !important; border-color: #a91b43 !important; color: white !important; border-radius: 8px; font-weight: 900; }
    .custom-pagination nav a, .custom-pagination nav span { border-radius: 8px; margin: 0 1px; font-weight: 700; border: 1px solid #f1f5f9; }
</style>
@endsection
