@extends('admin.layouts.admin')

@section('title', 'Products')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-bold text-slate-800">Product List</h2>
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center">
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                </select>
            </form>
        </div>
        <a href="{{ route('admin.products.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add Product
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">S.No</th>
                    <th class="pb-3 px-2 font-bold">Image</th>
                    <th class="pb-3 font-bold">Details</th>
                    <th class="pb-3 font-bold">Inventory</th>
                    <th class="pb-3 font-bold">Pricing</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($products as $product)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2 text-xs font-bold text-slate-500">
                        {{ $products->firstItem() + $loop->index }}
                    </td>
                    <td class="py-2.5 px-2">
                        @php $images = is_array($product->images) ? $product->images : json_decode($product->images, true); @endphp
                        @if($images && count($images) > 0)
                            <img src="{{ asset('uploads/' . $images[0]) }}" class="w-10 h-10 rounded-lg object-cover shadow-sm">
                        @else
                            <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-300">
                                <i class="fas fa-box text-xs"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-2.5">
                        <div class="font-bold text-slate-800 text-sm">{{ $product->name }}</div>
                        <div class="flex items-center space-x-2">
                            <span class="text-[9px] font-black text-slate-400 tracking-widest uppercase">SKU: {{ $product->sku ?? 'N/A' }}</span>
                            <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50 px-1.5 rounded">{{ $product->category->name }}</span>
                        </div>
                    </td>
                    <td class="py-2.5">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-slate-700">{{ $product->stock_quantity }} in stock</span>
                            <span class="text-[10px] uppercase font-bold {{ $product->stock_status == 'instock' ? 'text-emerald-500' : 'text-rose-500' }}">
                                {{ strtoupper($product->stock_status) }}
                            </span>
                        </div>
                    </td>
                    <td class="py-2.5">
                        <div class="flex flex-col">
                            <span class="text-sm font-black text-slate-800">₹{{ number_format($product->regular_price, 2) }}</span>
                            @if($product->sale_price)
                                <span class="text-[10px] text-rose-500 font-bold">Sale: ₹{{ number_format($product->sale_price, 2) }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $product->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $product->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $product->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $product->id }}" action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="hidden">
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
    <div class="mt-6">
        {{ $products->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently remove the product and its data!",
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
