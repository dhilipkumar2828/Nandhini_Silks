@extends('admin.layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="card-glass p-0 rounded-3xl overflow-hidden shadow-2xl border-white/40">
    <div class="p-6">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-6">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Order Portfolio</h2>
                <p class="text-sm text-slate-400 font-medium mt-1">Manage your boutique's orders with precision.</p>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 w-full lg:w-auto items-center">
                <form action="{{ route('admin.orders.index') }}" method="GET" class="relative w-full sm:w-80 group">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, Name, Phone..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                           class="w-full pl-11 pr-4 py-3 text-sm font-semibold bg-white border-2 border-slate-100 rounded-2xl focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 transition-all outline-none shadow-sm group-hover:border-slate-200">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 text-base group-hover:text-rose-500 transition-colors"></i>
                    @if(request('search'))
                        <a href="{{ route('admin.orders.index', ['status' => request('status')]) }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 hover:text-rose-500">
                            <i class="fas fa-times-circle text-lg"></i>
                        </a>
                    @endif
                </form>

                <form method="GET" action="{{ route('admin.orders.index') }}" class="hidden sm:block">
                    <select name="per_page" onchange="this.form.submit()" class="bg-white border-2 border-slate-100 rounded-2xl px-4 py-3 text-sm font-bold text-slate-500 focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 cursor-pointer shadow-sm">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 / page</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 / page</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 / page</option>
                    </select>
                </form>
            </div>
        </div>

        <!-- Status Tabs -->
        @php
            $currentStatus = request('status', 'all');
            $statusConfigs = [
                'all' => ['label' => 'All Orders', 'color' => 'rose'],
                'order placed' => ['label' => 'New Order', 'color' => 'amber'],
                'processing' => ['label' => 'Processing', 'color' => 'orange'],
                'ready to ship' => ['label' => 'Ready to Ship', 'color' => 'indigo'],
                'shipped' => ['label' => 'Shipped', 'color' => 'blue'],
                'out for delivery' => ['label' => 'Out Delivery', 'color' => 'emerald'],
                'delivered' => ['label' => 'Delivered', 'color' => 'teal'],
                'cancelled' => ['label' => 'Cancelled', 'color' => 'slate'],
            ];
        @endphp

        <div class="flex flex-nowrap overflow-x-auto pb-4 mb-2 gap-2 scrollbar-none no-scrollbar">
            @foreach($statusConfigs as $key => $config)
                @php
                    $isActive = $currentStatus == $key;
                    $colorClass = $isActive 
                        ? "bg-{$config['color']}-500 text-white shadow-lg shadow-{$config['color']}-500/20 border-{$config['color']}-500" 
                        : "bg-white text-slate-500 border-slate-100 hover:bg-slate-50 hover:border-slate-200";
                    $countColorClass = $isActive 
                        ? "bg-white/20 text-white" 
                        : "bg-slate-100 text-slate-400";
                @endphp
                <a href="{{ route('admin.orders.index', ['status' => $key, 'search' => request('search')]) }}" 
                   class="flex items-center whitespace-nowrap px-5 py-3 rounded-2xl border-2 font-bold text-sm transition-all duration-300 {{ $colorClass }}">
                    <span>{{ $config['label'] }}</span>
                    <span class="ml-2 px-2 py-0.5 rounded-lg text-[10px] font-black {{ $countColorClass }}">
                        {{ $counts[$key] ?? 0 }}
                    </span>
                </a>
            @endforeach
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
                            @if($order->order_status == 'delivered') bg-teal-100 text-teal-600 border-teal-200
                            @elseif($order->order_status == 'cancelled') bg-rose-100 text-rose-600 border-rose-200
                            @elseif($order->order_status == 'shipped') bg-blue-100 text-blue-600 border-blue-200
                            @elseif($order->order_status == 'ready to ship') bg-indigo-100 text-indigo-600 border-indigo-200
                            @elseif($order->order_status == 'processing') bg-orange-100 text-orange-600 border-orange-200
                            @elseif($order->order_status == 'out for delivery') bg-emerald-100 text-emerald-600 border-emerald-200
                            @else bg-amber-100 text-amber-600 border-amber-200 @endif">
                            {{ $order->order_status == 'dispatched' ? 'Shipped' : ucwords($order->order_status) }}
                        </span>
                    </td>
                    <td class="py-4 text-right pr-4">
                        <div class="flex justify-end items-center space-x-3">
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               class="group flex items-center justify-center w-10 h-10 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-2xl transition-all duration-500 shadow-sm border border-rose-100/50" 
                               title="Manage Workflow">
                                <i class="fas fa-cog text-sm group-hover:rotate-180 transition-transform duration-700"></i>
                            </a>
                            <a href="{{ route('admin.orders.invoice', $order->id) }}" 
                               class="flex items-center justify-center w-10 h-10 text-slate-400 bg-slate-50/50 hover:bg-slate-800 hover:text-white rounded-2xl transition-all duration-300 shadow-sm border border-slate-100/50" 
                               title="Invoice">
                                <i class="fas fa-file-invoice text-sm"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $order->id }}')" 
                                    class="flex items-center justify-center w-10 h-10 text-slate-300 hover:bg-rose-50 hover:text-rose-500 rounded-2xl transition-all duration-300" 
                                    title="Delete Portfolio Item">
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
