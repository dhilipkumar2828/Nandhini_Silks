@extends('admin.layouts.admin')

@section('title', 'Tax Classes')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Tax Classes</h2>
            <p class="text-xs text-slate-400 font-medium">Group tax rates for products</p>
        </div>
        <a href="{{ route('admin.tax-classes.create') }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Class
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2">Class Name</th>
                    <th class="pb-3">Description</th>
                    <th class="pb-3">Active Rates</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @foreach($taxClasses as $class)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-3 px-2 font-bold text-slate-800">{{ $class->name }}</td>
                    <td class="py-3 text-slate-500 text-xs">{{ Str::limit($class->description, 50) ?? 'N/A' }}</td>
                    <td class="py-3">
                        <span class="bg-indigo-50 text-indigo-600 px-2.5 py-0.5 rounded-full text-[10px] font-bold">
                            {{ $class->rates_count }} Rates
                        </span>
                    </td>
                    <td class="py-3">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $class->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $class->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-3 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.tax-classes.edit', $class->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $class->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $class->id }}" action="{{ route('admin.tax-classes.destroy', $class->id) }}" method="POST" class="hidden">
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
            title: 'Delete Tax Class?',
            text: "All associated rates will be deleted!",
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
