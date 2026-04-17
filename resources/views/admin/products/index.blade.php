@extends('admin.layouts.admin')

@section('title', 'Products')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-bold text-slate-800">Product List</h2>
            <form method="GET" action="{{ route('admin.products.index') }}" class="flex items-center">
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
            <form action="{{ route('admin.products.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products or SKU..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.products.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
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
                            <option value="{{ route('admin.products.index', ['status' => $key, 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.products.create') }}" class="bg-[#a91b43] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all whitespace-nowrap shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Add Product
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">S.No</th>
                    <th class="pb-3 px-2 font-bold">Image</th>
                    <th class="pb-3 font-bold">Product Name</th>
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
                        <div class="font-bold text-slate-800 text-sm whitespace-normal break-words max-w-[250px]">{{ $product->name }}</div>
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <span class="text-[9px] font-black text-slate-400 tracking-widest uppercase">SKU: {{ $product->sku ?? 'N/A' }}</span>
                            <span class="text-[9px] font-bold text-indigo-500 bg-indigo-50 px-1.5 rounded">{{ $product->category->name ?? 'N/A' }}</span>
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
                        <div class="flex justify-end items-center space-x-2">
                            <a href="{{ route('admin.products.edit', $product->id) }}" class="flex items-center justify-center w-8 h-8 text-indigo-500 bg-indigo-50/50 hover:bg-indigo-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-indigo-100" title="Edit Product">
                                <i class="fas fa-edit text-[10px]"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $product->id }}')" class="flex items-center justify-center w-8 h-8 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-rose-100" title="Delete Product">
                                <i class="fas fa-trash-alt text-[10px]"></i>
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
