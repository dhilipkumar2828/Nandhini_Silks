@extends('admin.layouts.admin')

@section('title', 'Advertisements')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-bold text-slate-800">Ads List</h2>
        <a href="{{ route('admin.ads.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Ad
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Image</th>
                    <th class="pb-3 font-bold">Details</th>
                    <th class="pb-3 font-bold">Behavior</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($ads as $ad)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2">
                        <img src="{{ asset('uploads/' . $ad->image) }}" class="w-16 h-16 rounded-lg object-cover shadow-sm border border-slate-100">
                    </td>
                    <td class="py-2.5">
                        <div class="font-bold text-slate-800 text-sm">{{ $ad->title ?? 'Untitled Ad' }}</div>
                        <div class="text-[10px] text-slate-400 truncate max-w-[200px]">{{ $ad->link ?? 'No Link' }}</div>
                    </td>
                    <td class="py-2.5">
                         <span class="text-[10px] font-bold text-slate-500">
                            {{ $ad->open_new_tab ? 'New Tab' : 'Same Page' }}
                         </span>
                    </td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $ad->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $ad->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.ads.edit', $ad->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $ad->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $ad->id }}" action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="hidden">
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
