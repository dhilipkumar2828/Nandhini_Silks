@extends('admin.layouts.admin')

@section('title', 'Testimonials')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center space-x-4">
            <h2 class="text-lg font-bold text-slate-800">Testimonials List</h2>
            <form method="GET" action="{{ route('admin.testimonials.index') }}" class="flex items-center">
                <select name="per_page" onchange="this.form.submit()" class="bg-slate-50 border-none rounded-lg px-2 py-1 text-[10px] font-bold text-slate-500 focus:ring-0 cursor-pointer">
                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 rows</option>
                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 rows</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 rows</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 rows</option>
                </select>
            </form>
        </div>
        <a href="{{ route('admin.testimonials.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Testimonial
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">S.No</th>
                    <th class="pb-3 font-bold">Client</th>
                    <th class="pb-3 font-bold">Review</th>
                    <th class="pb-3 font-bold">Rating</th>
                    <th class="pb-3 font-bold">Homepage</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($testimonials as $item)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2 text-xs font-bold text-slate-500">
                        {{ $testimonials->firstItem() + $loop->index }}
                    </td>
                    <td class="py-2.5 px-2">
                        <div class="flex items-center">
                            @if($item->photo)
                                <img src="{{ asset('uploads/' . $item->photo) }}" class="w-8 h-8 rounded-full object-cover mr-2 border border-slate-100 shadow-sm">
                            @else
                                <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 mr-2 text-[10px]">
                                    <i class="fas fa-user"></i>
                                </div>
                            @endif
                            <div>
                                <div class="font-bold text-slate-800 text-[11px]">{{ $item->name }}</div>
                                <div class="text-[9px] text-slate-400">{{ $item->submitted_at ? $item->submitted_at->format('M d, Y') : 'No Date' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-2.5">
                        <div class="text-xs text-slate-500 italic truncate max-w-xs" title="{{ $item->review }}">"{{ $item->review }}"</div>
                    </td>
                    <td class="py-2.5">
                        <div class="flex text-amber-400 text-[10px]">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="{{ $i <= $item->rating ? 'fas' : 'far' }} fa-star"></i>
                            @endfor
                        </div>
                    </td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $item->display_homepage ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-50 text-slate-400' }}">
                            {{ $item->display_homepage ? 'Yes' : 'No' }}
                        </span>
                    </td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $item->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $item->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.testimonials.edit', $item->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $item->id }}" action="{{ route('admin.testimonials.destroy', $item->id) }}" method="POST" class="hidden">
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
        {{ $testimonials->links() }}
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
