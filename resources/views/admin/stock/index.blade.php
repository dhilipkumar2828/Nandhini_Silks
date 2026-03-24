@extends('admin.layouts.admin')

@section('title', 'Stock Management')

@section('content')
<div class="space-y-6" x-data="{ 
    expandedGroups: [], 
    quickRestock(id, isVariant = false, hasVariants = false) {
        console.log('Restocking:', id, 'isVariant:', isVariant, 'hasVariants:', hasVariants);
        const prefix = isVariant ? 'variants' : 'stock';
        let qtyToRestock = document.querySelector(`input[name='${prefix}[${id}][restock_quantity]']`);
        
        if (!qtyToRestock || !qtyToRestock.value || parseInt(qtyToRestock.value) <= 0) {
            console.warn('Invalid restock value');
            return;
        }

        const restockVal = parseInt(qtyToRestock.value);

        if (!isVariant && hasVariants) {
            console.log('Parent restock triggered for product with variants');
            if (!this.expandedGroups.includes(id)) this.expandedGroups.push(id);
            
            // Redirect to first variant
            const firstVarRow = document.querySelector(`.variant-of-${id}`);
            if (firstVarRow) {
                const vid = firstVarRow.getAttribute('data-variant-id');
                const vQtyInput = document.querySelector(`input[name='variants[${vid}][quantity]']`);
                if (vQtyInput) {
                    vQtyInput.value = (parseInt(vQtyInput.value) || 0) + restockVal;
                    qtyToRestock.value = '';
                    this.refreshParentStock(id);
                    if (window.toastr) toastr.success(`Added ${restockVal} to variant stock`);
                    return;
                }
            }
        }
        
        const currentQtyInput = document.querySelector(`input[name='${prefix}[${id}][quantity]']`);
        const visualQtyEl = document.querySelector(`#visual-qty-${id}`);
        
        if (currentQtyInput) {
            currentQtyInput.value = (parseInt(currentQtyInput.value) || 0) + restockVal;
            qtyToRestock.value = '';
            if (visualQtyEl) visualQtyEl.innerText = currentQtyInput.value;
            
            // Animation
            const parent = currentQtyInput.parentElement;
            parent.classList.add('ring-4', 'ring-[#a91b43]/30', 'scale-110');
            setTimeout(() => parent.classList.remove('ring-4', 'ring-[#a91b43]/30', 'scale-110'), 600);
            if (window.toastr) toastr.success(`Added ${restockVal} units`);
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
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-100 shadow-sm relative z-10">
        <div>
            <h2 class="text-xl font-black text-slate-800 tracking-tight">Inventory Control</h2>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Manage stock levels and variants precisely</p>
        </div>
        <div class="flex items-center gap-3">
             <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-500 hover:bg-slate-50 transition-all font-bold">Cancel</a>
            <button type="submit" form="bulkStockForm" class="bg-[#a91b43] text-white px-8 py-2.5 rounded-xl text-xs font-black uppercase tracking-widest hover:bg-[#940437] shadow-lg shadow-pink-900/20 transition-all active:scale-[0.98] flex items-center">
                <i class="fas fa-save mr-2"></i> Update Inventory
            </button>
        </div>
    </div>

    <!-- Main Inventory Card -->
    <form id="bulkStockForm" action="{{ route('admin.stock.update-bulk') }}" method="POST">
        @csrf
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden min-h-[400px]">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-slate-50/50 text-slate-400 text-[10px] font-black uppercase tracking-[0.15em] border-b border-slate-100">
                            <th class="py-4 px-6 w-[35%]">Product / Variants</th>
                            <th class="py-4 px-4 text-center w-32">Current Qty</th>
                            <th class="py-4 px-4 text-center w-32">Threshold</th>
                            <th class="py-4 px-4 text-center w-40">Restock Plan</th>
                            <th class="py-4 px-4">Supplier</th>
                            <th class="py-4 px-6 text-right w-40">Status</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        @foreach($products as $product)
                        @php $hasVariants = $product->product_variants->count() > 0; @endphp
                        <tr class="group hover:bg-slate-50/30 transition-all border-b border-slate-50">
                            <!-- Product Name & Variant Trigger -->
                            <td class="py-5 px-6">
                                <div class="flex items-center gap-4">
                                    <div class="flex flex-col items-center gap-1">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-[#a91b43]/5 group-hover:text-[#a91b43] transition-colors overflow-hidden">
                                            @if($product->primary_image)
                                                <img src="{{ asset('uploads/'.$product->primary_image) }}" class="w-full h-full object-cover">
                                            @else
                                                <i class="fas fa-box text-lg"></i>
                                            @endif
                                        </div>
                                        @if($hasVariants)
                                        <button type="button" 
                                            @click="expandedGroups.includes({{ $product->id }}) ? expandedGroups = expandedGroups.filter(id => id !== {{ $product->id }}) : expandedGroups.push({{ $product->id }})"
                                            class="p-1 hover:bg-[#a91b43]/10 rounded-md transition-colors">
                                            <i class="fas fa-chevron-down text-[8px] text-[#a91b43] transition-transform duration-300" :class="expandedGroups.includes({{ $product->id }}) ? 'rotate-180' : ''"></i>
                                        </button>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-black text-slate-800 text-sm leading-tight group-hover:text-[#a91b43] transition-colors">{{ $product->name }}</div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[9px] font-black bg-indigo-50 text-indigo-600 px-1.5 py-0.5 rounded uppercase tracking-tighter">{{ $product->sku ?? 'NO-SKU' }}</span>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter px-2 border-l border-slate-100">{{ $product->category->name ?? 'Collection' }}</span>
                                            @if($hasVariants)
                                            <span class="text-[8px] font-black text-indigo-400 uppercase tracking-widest bg-indigo-50/50 px-1.5 py-0.5 rounded ml-2">{{ $product->product_variants->count() }} Var</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Product Qty Aligned -->
                            <td class="py-5 px-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    @if($hasVariants)
                                        <div id="visual-qty-{{ $product->id }}" class="w-20 text-center bg-slate-50 border border-slate-200 px-2 py-2 rounded-xl text-sm font-black text-slate-400 cursor-not-allowed select-none shadow-sm" title="Automatically calculated from variants">
                                            {{ $product->stock_quantity }}
                                        </div>
                                        <input type="hidden" name="stock[{{ $product->id }}][quantity]" value="{{ $product->stock_quantity }}">
                                    @else
                                        <input type="number" name="stock[{{ $product->id }}][quantity]" value="{{ $product->stock_quantity }}" 
                                            class="w-20 text-center {{ $product->stock_quantity < 0 ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-slate-50 border-slate-100 text-slate-800' }} px-2 py-2 rounded-xl text-sm font-black focus:border-[#a91b43] focus:bg-white focus:ring-4 focus:ring-[#a91b43]/5 outline-none transition-all shadow-sm">
                                    @endif
                                    <div class="flex gap-2 mt-1">
                                        <span class="text-[8px] font-bold text-slate-400">Res: {{ $product->reserved_stock }}</span>
                                        <span class="text-[8px] font-black {{ $product->available_stock <= $product->low_stock_threshold ? 'text-rose-500' : 'text-emerald-500' }}">Avail: {{ $product->available_stock }}</span>
                                    </div>
                                    <input type="hidden" name="stock[{{ $product->id }}][reserved_stock]" value="{{ $product->reserved_stock }}">
                                </div>
                            </td>

                            <!-- Threshold Aligned -->
                            <td class="py-5 px-4 text-center">
                                <input type="number" name="stock[{{ $product->id }}][low_stock_threshold]" value="{{ $product->low_stock_threshold }}" 
                                    class="w-16 text-center bg-amber-50/40 border border-amber-100 px-1 py-1.5 rounded-lg text-xs font-black text-amber-700 outline-none focus:border-amber-400 transition-all">
                            </td>

                            <!-- Restock Aligned -->
                            <td class="py-5 px-4 text-center">
                                <div class="flex flex-col items-center gap-1.5">
                                    <div class="flex items-center gap-1">
                                        <input type="number" name="stock[{{ $product->id }}][restock_quantity]" value="{{ $product->restock_quantity }}" 
                                            class="w-16 text-center bg-slate-50 border border-slate-100 px-1 py-1.5 rounded-lg text-[11px] font-bold text-slate-600 outline-none focus:border-[#a91b43] transition-all" placeholder="Qty">
                                        <button type="button" @click="quickRestock({{ $product->id }}, false, {{ $hasVariants ? 'true' : 'false' }})" class="w-6 h-6 flex items-center justify-center rounded-lg bg-[#a91b43]/5 text-[#a91b43] border border-[#a91b43]/10 hover:bg-[#a91b43] hover:text-white transition-all" title="Add to stock now">
                                            <i class="fas fa-plus text-[8px]"></i>
                                        </button>
                                    </div>
                                    <input type="date" name="stock[{{ $product->id }}][restock_date]" value="{{ $product->restock_date ? $product->restock_date->format('Y-m-d') : '' }}" 
                                        class="w-28 text-center bg-slate-50 border border-slate-100 px-1 py-1.5 rounded-lg text-[9px] font-bold text-slate-500 outline-none focus:border-[#a91b43] transition-all">
                                </div>
                            </td>

                            <!-- Supplier Aligned -->
                            <td class="py-5 px-4">
                                <div class="relative group/sup">
                                    <i class="fas fa-truck-field text-[10px] text-slate-300 absolute left-2 top-1/2 -translate-y-1/2 group-focus-within/sup:text-[#a91b43] transition-colors"></i>
                                    <input type="text" name="stock[{{ $product->id }}][supplier]" value="{{ $product->supplier }}" 
                                        class="w-full pl-7 bg-transparent border-b border-transparent hover:border-slate-100 focus:border-[#a91b43] py-2 text-[10px] font-medium text-slate-500 outline-none transition-all placeholder:text-slate-200" placeholder="Supplier...">
                                </div>
                            </td>

                            <!-- Status Aligned -->
                            <td class="py-5 px-6 text-right">
                                <div class="flex flex-col items-end gap-2">
                                    <span class="px-2.5 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm
                                        @if($product->stock_status == 'instock') bg-emerald-50 text-emerald-600 border border-emerald-100
                                        @elseif($product->stock_status == 'lowstock') bg-amber-50 text-amber-600 border border-amber-100
                                        @else bg-rose-50 text-rose-600 border border-rose-100 @endif">
                                        {{ $product->stock_status }}
                                    </span>
                                    <a href="{{ route('admin.stock.logs', $product->id) }}" class="text-[9px] font-black text-[#a91b43] uppercase tracking-tighter hover:underline flex items-center gap-1.5">
                                        <i class="fas fa-history text-[8px]"></i> Logs
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- VARIANTS INSIDE -->
                        @if($hasVariants)
                        @foreach($product->product_variants as $index => $variant)
                        <tr x-show="expandedGroups.includes({{ $product->id }})" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-indigo-50/[0.03] variant-of-{{ $product->id }}" data-variant-id="{{ $variant->id }}">
                            <!-- Variant Info -->
                            <td class="py-4 px-6 pl-16 relative border-b border-slate-50">
                                <div class="absolute left-10 top-0 bottom-0 w-[1px] bg-slate-100"></div>
                                <div class="absolute left-10 top-1/2 w-4 h-[1px] bg-slate-100"></div>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white border border-slate-200 flex items-center justify-center overflow-hidden shadow-sm">
                                        @if($variant->image)
                                            <img src="{{ asset('uploads/'.$variant->image) }}" class="w-full h-full object-cover">
                                        @else
                                            <i class="fas fa-tags text-[10px] text-indigo-300"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-[10px] font-black text-indigo-600 uppercase tracking-wide">
                                            {{ implode(' • ', $variant->attribute_values ?? []) }}
                                        </div>
                                        <div class="text-[8px] font-bold text-slate-400 mt-0.5">{{ $variant->sku }}</div>
                                    </div>
                                </div>
                            </td>

                            <!-- Variant Stock -->
                            <td class="py-4 px-4 text-center border-b border-slate-50">
                                <div class="relative inline-block">
                                    <input type="number" name="variants[{{ $variant->id }}][quantity]" value="{{ $variant->stock_quantity }}" 
                                        class="w-20 text-center {{ $variant->stock_quantity <= 0 ? 'bg-rose-50 border-rose-100 text-rose-600' : 'bg-white border-slate-200 text-slate-700' }} px-2 py-2 rounded-xl text-xs font-black focus:border-indigo-400 outline-none shadow-sm transition-all focus:ring-4 focus:ring-indigo-100/50">
                                    <span class="absolute -top-2 -right-2 text-[6px] font-black bg-indigo-500 text-white px-1 py-0.5 rounded-full uppercase">Var</span>
                                </div>
                            </td>

                            <!-- Spacer -->
                            <td class="py-4 px-4 text-center opacity-20 border-b border-slate-50">
                                <div class="w-16 mx-auto h-[1px] bg-slate-400"></div>
                            </td>

                            <!-- Variant Restock -->
                            <td class="py-4 px-4 text-center border-b border-slate-50">
                                <div class="flex items-center justify-center gap-1">
                                    <input type="number" name="variants[{{ $variant->id }}][restock_quantity]" 
                                        class="w-16 text-center bg-white border-b border-indigo-100 px-1 py-1.5 rounded text-[11px] font-bold text-slate-500 outline-none focus:border-indigo-400 transition-all" placeholder="Qty">
                                    <button type="button" @click="quickRestock({{ $variant->id }}, true); refreshParentStock({{ $product->id }})" class="w-6 h-6 flex items-center justify-center rounded-lg bg-indigo-50 text-indigo-500 border border-indigo-100 hover:bg-indigo-500 hover:text-white transition-all" title="Add to variant stock now">
                                        <i class="fas fa-plus text-[8px]"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Label -->
                            <td colspan="2" class="py-4 px-6 text-right border-b border-slate-50">
                                <div class="flex items-center justify-end gap-2 text-[9px] font-bold text-slate-300 uppercase tracking-[0.2em] select-none">
                                    <i class="fas fa-fingerprint opacity-50"></i> Variant Unit MGT
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer Pagination -->
            @if($products->hasPages())
            <div class="px-6 py-5 bg-slate-50/50 border-t border-slate-100">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest order-2 sm:order-1">
                        Displaying inventory items {{ $products->firstItem() }}-{{ $products->lastItem() }}
                    </p>
                    <div class="custom-pagination order-1 sm:order-2">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>

<style>
    /* Premium Pagination */
    .custom-pagination nav svg { height: 1.25rem; width: 1.25rem; }
    .custom-pagination nav span[aria-current="page"] span { background-color: #a91b43 !important; border-color: #a91b43 !important; border-radius: 8px; }
    .custom-pagination nav a:hover { color: #a91b43 !important; border-radius: 8px; }
    
    /* Input aesthetics */
    input[type=number]::-webkit-inner-spin-button, input[type=number]::-webkit-outer-spin-button { opacity: 0.5; cursor: pointer; }
    
    /* Fixed Table helper */
    table { border-collapse: separate !important; }
    th { position: sticky; top: 0; z-index: 10; background: #fdfdfd; }
</style>
@endsection
