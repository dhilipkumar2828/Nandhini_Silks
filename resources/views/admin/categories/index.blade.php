@extends('admin.layouts.admin')

@section('title', 'Categories')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-bold text-slate-800">Category List</h2>
            <form method="GET" action="{{ route('admin.categories.index') }}" class="flex items-center">
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
            <form action="{{ route('admin.categories.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.categories.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
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
                            <option value="{{ route('admin.categories.index', ['status' => $key, 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.categories.create') }}" class="bg-[#a91b43] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all whitespace-nowrap shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Add New
            </a>
        </div>
    </div>


    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold text-center w-16">S.No</th>
                    <th class="pb-3 px-2 font-bold text-center w-20">Image</th>
                    <th class="pb-3 font-bold text-center w-40">Category</th>
                    <th class="pb-3 font-bold text-center w-16">Order</th>
                    <th class="pb-3 font-bold text-center w-16">Status</th>
                    <th class="pb-3 font-bold text-center w-16">In Menu</th>
                    <th class="pb-3 font-bold text-right pr-4 w-16">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($categories as $category)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2 text-xs font-bold text-slate-500 text-center">
                        {{ $categories->firstItem() + $loop->index }}
                    </td>
                    <td class="py-2.5 px-2 text-center">
                        @if($category->image)
                            <img src="{{ asset('uploads/' . $category->image) }}" class="w-10 h-10 rounded-lg object-cover shadow-sm mx-auto">
                        @else
                            <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-300 mx-auto">
                                <i class="fas fa-image text-xs"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-2.5 text-center w-16">
                        <div class="font-bold text-slate-800 text-sm">{{ $category->name }}</div>
                        <div class="text-[10px] text-slate-400 tracking-tight">{{ $category->slug }}</div>
                    </td>
                    <td class="py-2.5 text-xs text-slate-500 font-bold text-center">{{ $category->display_order }}</td>
                    <td class="py-2.5 text-center">
                        <span class="px-2 py-0.5 rounded-md text-[7px] font-bold uppercase tracking-tighter {{ $category->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-center">
                        <span class="px-2 py-0.5 rounded-md text-[8px] font-bold uppercase tracking-tighter {{ $category->show_in_menu ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-600' }}">
                            {{ $category->show_in_menu ? 'Shown' : 'Hidden' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right pr-4">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.categories.edit', $category->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $category->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="hidden">
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
        {{ $categories->links() }}
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
