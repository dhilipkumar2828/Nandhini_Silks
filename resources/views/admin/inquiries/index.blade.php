@extends('admin.layouts.admin')

@section('title', 'Inquiries')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">Inquiry Management</h2>
                <p class="text-xs text-slate-400">View and manage customer inquiries</p>
            </div>
            <form method="GET" action="{{ route('admin.inquiries.index') }}" class="flex items-center pt-2 md:pt-0">
                @if(request('search'))<input type="hidden" name="search" value="{{ request('search') }}">@endif
                @if(request('status'))<input type="hidden" name="status" value="{{ request('status') }}">@endif
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                </select>
            </form>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto items-center">
            <form action="{{ route('admin.inquiries.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search inquiries..."
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            </form>

            <div class="w-full sm:w-48">
                @php
                    $currentStatus = request('status', 'all');
                    $statuses = [
                        'all' => 'All Status',
                        'pending' => 'Pending',
                        'responded' => 'Responded',
                    ];
                @endphp
                <div class="relative">
                    <select onchange="window.location.href=this.value" 
                            class="appearance-none bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] block w-full px-4 py-2 transition-all outline-none cursor-pointer shadow-sm">
                        @foreach($statuses as $key => $label)
                            <option value="{{ route('admin.inquiries.index', ['status' => $key, 'search' => request('search')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
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
                    <th class="pb-3 font-bold">Date</th>
                    <th class="pb-3 font-bold">Customer</th>
                    <th class="pb-3 font-bold">Message Snippet</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($inquiries as $inquiry)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 text-xs font-bold text-slate-500">
                        {{ $inquiries->firstItem() + $loop->index }}
                    </td>
                    <td class="py-3">
                        <div class="text-[11px] text-slate-600 font-bold uppercase tracking-tighter">{{ $inquiry->created_at->format('d M Y') }}</div>
                        <div class="text-[9px] text-slate-400 font-medium tracking-tighter">{{ $inquiry->created_at->format('h:i A') }}</div>
                    </td>
                    <td class="py-3">
                        <div class="font-bold text-slate-800">{{ $inquiry->name }}</div>
                        <div class="text-[10px] text-slate-400 font-medium">{{ $inquiry->email }}</div>
                    </td>
                    <td class="py-3">
                        <div class="text-xs text-slate-600 truncate max-w-[200px]">{{ $inquiry->message }}</div>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest border 
                            @if($inquiry->status == 'responded') bg-emerald-50 text-emerald-600 border-emerald-100
                            @else bg-amber-50 text-amber-600 border-amber-100 @endif">
                            {{ $inquiry->status }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end items-center space-x-2">
                            <a href="{{ route('admin.inquiries.show', $inquiry->id) }}" class="flex items-center justify-center w-8 h-8 text-indigo-500 bg-indigo-50/50 hover:bg-indigo-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-indigo-100" title="View Details">
                                <i class="fas fa-eye text-[10px]"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $inquiry->id }}')" class="flex items-center justify-center w-8 h-8 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-rose-100" title="Delete">
                                <i class="fas fa-trash-alt text-[10px]"></i>
                            </button>
                            <form id="delete-form-{{ $inquiry->id }}" action="{{ route('admin.inquiries.destroy', $inquiry->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-400 italic">No inquiries found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="mt-4">
        {{ $inquiries->appends(request()->query())->links() }}
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
