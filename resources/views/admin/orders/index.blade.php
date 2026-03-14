@extends('admin.layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Order Management</h2>
            <p class="text-xs text-slate-400">View and manage customer orders</p>
        </div>
        
        <div class="flex flex-wrap gap-2">
            @php
                $currentStatus = request('status', 'all');
                $statuses = [
                    'all' => 'All Orders',
                    'paid' => 'Paid',
                    'unpaid' => 'Unpaid',
                    'processing' => 'Processing',
                    'dispatched' => 'Dispatched'
                ];
            @endphp
            @foreach($statuses as $key => $label)
                <a href="{{ route('admin.orders.index', ['status' => $key]) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $currentStatus == $key ? 'bg-[#a91b43] text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Order ID</th>
                    <th class="pb-3 font-bold">Customer</th>
                    <th class="pb-3 font-bold">Total</th>
                    <th class="pb-3 font-bold">Payment</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold">Date</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($orders as $order)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2">
                        <span class="font-bold text-[#a91b43]">#{{ $order->id }}</span>
                    </td>
                    <td class="py-3">
                        <div class="font-bold text-slate-800">{{ $order->customer_name }}</div>
                        <div class="text-[10px] text-slate-400">{{ $order->customer_email }}</div>
                        <div class="text-[10px] text-slate-400">{{ $order->customer_phone }}</div>
                    </td>
                    <td class="py-3">
                        <div class="font-bold text-slate-800">₹{{ number_format($order->grand_total, 2) }}</div>
                        <div class="text-[10px] text-slate-400">{{ $order->payment_method }}</div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter 
                            @if($order->payment_status == 'paid') bg-emerald-50 text-emerald-600 
                            @elseif($order->payment_status == 'failed') bg-rose-50 text-rose-600
                            @else bg-amber-50 text-amber-600 @endif">
                            {{ $order->payment_status }}
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter 
                            @if($order->order_status == 'delivered') bg-emerald-50 text-emerald-600 
                            @elseif($order->order_status == 'cancelled') bg-rose-50 text-rose-600
                            @elseif($order->order_status == 'dispatched') bg-blue-50 text-blue-600
                            @else bg-amber-50 text-amber-600 @endif">
                            {{ $order->order_status }}
                        </span>
                    </td>
                    <td class="py-3 text-[10px] text-slate-500 font-bold">
                        {{ $order->created_at->format('d M Y, h:i A') }}
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all" title="View Details">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('admin.orders.edit', $order->id) }}" class="p-1.5 text-amber-400 hover:bg-amber-50 rounded-md transition-all" title="Update Status">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" class="p-1.5 text-emerald-400 hover:bg-emerald-50 rounded-md transition-all" title="Download Invoice">
                                <i class="fas fa-file-invoice text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $order->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
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
