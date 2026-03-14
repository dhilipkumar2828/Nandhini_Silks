@extends('admin.layouts.admin')

@section('title', 'Child Categories')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-slate-800">Child Category List</h2>
        <a href="{{ route('admin.child-categories.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Image</th>
                    <th class="pb-3 font-bold">Details</th>
                    <th class="pb-3 font-bold">Category Hierarchy</th>
                    <th class="pb-3 font-bold">Order</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($childCategories as $childCategory)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2">
                        @if($childCategory->image)
                            <img src="{{ asset('uploads/' . $childCategory->image) }}" class="w-10 h-10 rounded-lg object-cover shadow-sm">
                        @else
                            <div class="w-10 h-10 bg-slate-50 rounded-lg flex items-center justify-center text-slate-300">
                                <i class="fas fa-image text-xs"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-2.5">
                        <div class="font-bold text-slate-800 text-sm">{{ $childCategory->name }}</div>
                        <div class="text-[10px] text-slate-400 tracking-tight">{{ $childCategory->slug }}</div>
                    </td>
                    <td class="py-2.5">
                        <div class="flex flex-col space-y-1">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                                {{ $childCategory->category->name }}
                            </span>
                            <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md w-fit">
                                <i class="fas fa-chevron-right mr-1 text-[8px]"></i> {{ $childCategory->subCategory->name }}
                            </span>
                        </div>
                    </td>
                    <td class="py-2.5 text-xs text-slate-500 font-bold">{{ $childCategory->display_order }}</td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $childCategory->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $childCategory->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.child-categories.edit', $childCategory->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $childCategory->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $childCategory->id }}" action="{{ route('admin.child-categories.destroy', $childCategory->id) }}" method="POST" class="hidden">
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
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "All related products will be affected!",
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
