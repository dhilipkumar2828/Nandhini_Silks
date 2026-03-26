@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-6 pb-6">
    <!-- Hero Welcome Section -->
    <div class="card-glass p-6 rounded-[1.5rem] relative overflow-hidden bg-gradient-to-br from-[#a91b43] to-[#800d2e] text-white">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="space-y-3 text-center md:text-left">
                <h2 class="text-xl md:text-2xl font-extrabold tracking-tight">Welcome, {{ Auth::guard('admin')->user()->name }} 👋</h2>
                <p class="text-pink-100/80 text-sm max-w-xl">Overall system performance and registered user overview.</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-2">
                    <a href="{{ route('admin.users.index') }}" class="bg-white text-[#a91b43] px-5 py-2 rounded-lg text-xs font-bold hover:scale-105 transition-all shadow-xl shadow-black/10">
                        <i class="fas fa-users mr-1.5"></i> Manage Users
                    </a>
                    <a href="{{ route('admin.categories.create') }}" class="bg-white/20 backdrop-blur-md text-white border border-white/30 px-5 py-2 rounded-lg text-xs font-bold hover:bg-white/30 transition-all">
                        <i class="fas fa-plus mr-1.5"></i> Category
                    </a>
                </div>
            </div>
            <div class="hidden lg:block">
                <div class="w-32 h-32 bg-white/10 rounded-full flex items-center justify-center animate-pulse">
                    <i class="fas fa-chart-line text-4xl text-pink-200/50"></i>
                </div>
            </div>
        </div>
        <!-- Decorative blobs -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -left-12 w-64 h-64 bg-pink-500/20 rounded-full blur-3xl"></div>
    </div>

    <!-- Analytics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $stats = [
                ['label' => 'Total Sales', 'value' => '₹' . number_format($totalSales, 0), 'icon' => 'fa-indian-rupee-sign', 'color' => 'pink'],
                ['label' => 'Total Orders', 'value' => number_format($totalOrders), 'icon' => 'fa-shopping-cart', 'color' => 'amber'],
                ['label' => 'Registered Users', 'value' => number_format($totalUsers), 'icon' => 'fa-users', 'color' => 'indigo'],
                ['label' => 'Total Products', 'value' => number_format($totalProducts), 'icon' => 'fa-box-open', 'color' => 'rose'],
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="card-glass p-5 rounded-[1.25rem] hover:translate-y-[-3px] transition-all duration-300">
            <div class="flex items-start justify-between">
                <div class="p-2 rounded-xl bg-{{ $stat['color'] }}-50 text-{{ $stat['color'] }}-600">
                    <i class="fas {{ $stat['icon'] }} text-lg"></i>
                </div>
                <div class="text-right">
                </div>
            </div>
            <div class="mt-4">
                <p class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">{{ $stat['label'] }}</p>
                <h3 class="text-xl font-black text-slate-800 tracking-tight mt-0.5">{{ $stat['value'] }}</h3>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Analytics Section -->
    <!-- Latest Orders -->
    <div class="card-glass rounded-[1.5rem] overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center bg-white">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Latest Orders</h3>
                <p class="text-slate-400 text-[11px]">Recent transactions from your store</p>
            </div>
            <a href="{{ route('admin.orders.index') }}" class="text-[#a91b43] text-xs font-bold hover:underline">View All</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest w-16">S.No</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Order ID</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Total</th>
                        <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 text-xs">
                    @foreach($latestOrders as $order)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 font-bold text-slate-500 w-16">{{ $loop->iteration }}</td>
                        <td class="px-6 py-4 font-bold text-slate-700">#{{ $order->order_number }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-800">{{ $order->customer_name }}</div>
                            <div class="text-[10px] text-slate-400">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-6 py-4 font-black text-slate-900">₹{{ number_format($order->grand_total, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            @php $status = $order->order_status_badge; @endphp
                            <span class="px-2 py-1 rounded-lg font-bold text-[10px] {{ $status['class'] }} text-center block w-fit mx-auto">{{ $status['label'] }}</span>
                        </td>
                    </tr>
                    @endforeach
                    @if($latestOrders->isEmpty())
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400 font-bold">No orders found</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
