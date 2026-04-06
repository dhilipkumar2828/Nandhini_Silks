@extends('admin.layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Order Management</h2>
                <p class="text-xs text-slate-400">View and manage orders</p>
            </div>
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex items-center pt-2 md:pt-0">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                </select>
            </form>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('admin.orders.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search orders..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.orders.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </form>

            <div class="w-full sm:w-48">
                @php
                    $currentStatus = request('status', 'all');
                    $statuses = [
                        'all' => 'All Orders',
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'order placed' => 'Order Placed',
                        'shipped' => 'Shipped',
                        'out for delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled'
                    ];
                @endphp
                <div class="relative">
                    <select onchange="window.location.href=this.value" 
                            class="appearance-none bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] block w-full px-4 py-2 transition-all outline-none cursor-pointer shadow-sm">
                        @foreach($statuses as $key => $label)
                            <option value="{{ route('admin.orders.index', ['status' => $key, 'search' => request('search')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">S.No</th>
                    <th class="pb-3 font-bold">Order ID</th>
                    <th class="pb-3 font-bold">Customer</th>
                    <th class="pb-3 font-bold">Total</th>
                    <th class="pb-3 font-bold">Payment</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($orders as $order)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 text-xs font-bold text-slate-500">
                        {{ $orders->firstItem() + $loop->index }}
                    </td>
                    <td class="py-3">
                        <span class="font-black text-[#a91b43] text-xs">#{{ $order->order_number }}</span>
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter mt-0.5">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                    </td>
                    <td class="py-3">
                        <div class="font-bold text-slate-800">{{ $order->customer_name }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $order->customer_email }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="py-3">
                        <div class="font-black text-slate-800">₹{{ number_format($order->grand_total, 2) }}</div>
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest">{{ $order->payment_method }}</div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border 
                            @if($order->payment_status == 'paid') bg-emerald-50 text-emerald-600 border-emerald-100
                            @elseif($order->payment_status == 'failed') bg-rose-50 text-rose-600 border-rose-100
                            @else bg-amber-50 text-amber-600 border-amber-100 @endif">
                            {{ $order->payment_status }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border
                            @if($order->order_status == 'delivered') bg-emerald-100 text-emerald-600 border-emerald-200
                            @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-600 border-rose-200
                            @elseif($order->order_status == 'shipped' || $order->order_status == 'dispatched') bg-blue-100 text-blue-600 border-blue-200
                            @else bg-amber-100 text-amber-600 border-amber-200 @endif">
                            {{ $order->order_status == 'dispatched' ? 'Shipped' : ucwords($order->order_status) }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end items-center space-x-2">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="flex items-center justify-center w-8 h-8 text-indigo-500 bg-indigo-50/50 hover:bg-indigo-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-indigo-100" title="View Details">
                                <i class="fas fa-eye text-[10px]"></i>
                            </a>
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="flex items-center justify-center w-8 h-8 text-amber-500 bg-amber-50/50 hover:bg-amber-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-amber-100" title="Update Status">
                                <i class="fas fa-edit text-[10px]"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="flex items-center justify-center w-8 h-8 text-emerald-500 bg-emerald-50/50 hover:bg-emerald-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-emerald-100" title="Invoice">
                                <i class="fas fa-file-invoice text-[10px]"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $order->id }}')" class="flex items-center justify-center w-8 h-8 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-rose-100" title="Delete">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                            <form id="delete-form-{{ $order->id }}" action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-10 text-center text-slate-400 italic">No orders found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $orders->appends(request()->query())->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
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
