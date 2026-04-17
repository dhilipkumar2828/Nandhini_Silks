@extends('admin.layouts.admin')

@section('title', 'Coupons')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Coupons</h2>
                <p class="text-[10px] text-slate-400 font-medium">Manage discount codes and rules</p>
            </div>
            <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex items-center">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : (!request('per_page') ? 'selected' : '') }}>20 rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                </select>
            </form>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('admin.coupons.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search coupons..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.coupons.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
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
                            <option value="{{ route('admin.coupons.index', ['status' => $key, 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.coupons.create') }}" class="bg-[#a91b43] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all whitespace-nowrap shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Add Coupon
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold text-center">S.No</th>
                    <th class="pb-3 px-2">Code</th>
                    <th class="pb-3">Type</th>
                    <th class="pb-3">Discount</th>
                    <th class="pb-3">Min/Max</th>
                    <th class="pb-3">Usage</th>
                    <th class="pb-3">Validity</th>
                    <th class="pb-3 text-center">Status</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($coupons as $coupon)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 text-xs font-bold text-slate-500 text-center">
                        {{ $coupons->firstItem() + $loop->index }}
                    </td>
                    <td class="py-3 px-2 font-black text-slate-800 uppercase tracking-wider text-xs">{{ $coupon->code }}</td>
                    <td class="py-3 text-slate-500 text-[10px] font-black uppercase tracking-tighter">{{ $coupon->type }}</td>
                    <td class="py-3 text-slate-700 font-black text-[11px]">
                        @if($coupon->type === 'percentage')
                            {{ rtrim(rtrim(number_format($coupon->discount_value, 2), '0'), '.') }}%
                        @else
                            &#8377;{{ number_format($coupon->discount_value, 2) }}
                        @endif
                    </td>
                    <td class="py-3 text-slate-400 text-[9px] font-bold uppercase">
                        @if($coupon->min_order_amount)
                            M.O: &#8377;{{ number_format($coupon->min_order_amount, 0) }}
                        @else
                            -
                        @endif
                        <br>
                        @if($coupon->max_discount)
                            M.D: &#8377;{{ number_format($coupon->max_discount, 0) }}
                        @else
                            -
                        @endif
                    </td>
                    <td class="py-3 text-slate-500 text-[10px] font-bold">
                        <span class="text-indigo-600 font-black">{{ $coupon->times_used }}</span> / <span class="text-slate-400">{{ $coupon->usage_limit ?? '∞' }}</span>
                    </td>
                    <td class="py-3 text-slate-400 text-[9px] font-bold uppercase tracking-tighter">
                        @if($coupon->valid_from)
                            {{ $coupon->valid_from->format('d M Y') }}
                        @else
                            Open
                        @endif
                        <span class="text-slate-200 mx-1">/</span>
                        @if($coupon->expires_at)
                            {{ $coupon->expires_at->format('d M Y') }}
                        @else
                            Forever
                        @endif
                    </td>
                    <td class="py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $coupon->status ? 'bg-emerald-100 text-emerald-600 border border-emerald-200' : 'bg-rose-100 text-rose-600 border border-rose-200' }}">
                            {{ $coupon->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end items-center space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="flex items-center justify-center w-8 h-8 text-indigo-500 bg-indigo-50/50 hover:bg-indigo-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-indigo-100" title="Edit">
                                <i class="fas fa-edit text-[10px]"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $coupon->id }}')" class="flex items-center justify-center w-8 h-8 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-rose-100" title="Delete">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                            <form id="delete-form-{{ $coupon->id }}" action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="hidden">
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
        {{ $coupons->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Delete Coupon?',
            text: 'This will remove the coupon permanently.',
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
