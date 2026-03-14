@extends('admin.layouts.admin')

@section('title', 'Banners')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-slate-800">Banner List</h2>
        <a href="{{ route('admin.banners.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Banner
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Preview (D/M)</th>
                    <th class="pb-3 font-bold">Details</th>
                    <th class="pb-3 font-bold">Order</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($banners as $banner)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2">
                        <div class="flex space-x-2">
                            <img src="{{ asset('uploads/' . $banner->image_desktop) }}" class="w-16 h-8 rounded-lg object-cover shadow-sm border border-slate-100" title="Desktop">
                            @if($banner->image_mobile)
                                <img src="{{ asset('uploads/' . $banner->image_mobile) }}" class="w-6 h-10 rounded-md object-cover shadow-sm border border-slate-100" title="Mobile">
                            @endif
                        </div>
                    </td>
                    <td class="py-2.5">
                        <div class="font-bold text-slate-800 text-sm">{{ $banner->title ?? 'No Title' }}</div>
                        <div class="text-[10px] text-slate-400 tracking-tight truncate max-w-[200px]">{{ $banner->link ?? 'No Link' }}</div>
                    </td>
                    <td class="py-2.5">
                        <span class="text-xs font-bold text-slate-500">{{ $banner->display_order }}</span>
                    </td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $banner->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $banner->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.banners.edit', $banner->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $banner->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $banner->id }}" action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="hidden">
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
            text: "This action cannot be undone!",
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
