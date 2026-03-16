@extends('admin.layouts.admin')

@section('title', 'Attribute Values')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
        <div class="flex items-center">
            @if($attribute)
                <a href="{{ route('admin.attributes.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h2 class="text-lg font-bold text-slate-800">Values for: {{ $attribute->name }}</h2>
            @else
                <h2 class="text-lg font-bold text-slate-800">All Attribute Values</h2>
            @endif
        </div>
        <a href="{{ route('admin.attribute-values.create', ['attribute_id' => $attribute ? $attribute->id : '']) }}" class="bg-[#a91b43] text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-[#940437] transition-all">
            <i class="fas fa-plus mr-1.5"></i> Add New Value
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold">Attribute</th>
                    <th class="pb-3 font-bold">Value Name</th>
                    <th class="pb-3 font-bold">Swatch</th>
                    <th class="pb-3 font-bold">Order</th>
                    <th class="pb-3 font-bold">Status</th>
                    <th class="pb-3 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($attributeValues as $value)
                <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                    <td class="py-2.5 px-2 text-xs font-bold text-slate-400">{{ $value->attribute->name }}</td>
                    <td class="py-2.5">
                        <div class="font-bold text-slate-800 text-sm">{{ $value->name }}</div>
                        <div class="text-[10px] text-slate-400 tracking-tight">{{ $value->slug }}</div>
                    </td>
                    <td class="py-2.5">
                        @if($value->swatch_value)
                            <div class="flex items-center">
                                <span class="w-4 h-4 rounded-full border border-slate-100 mr-2" style="background-color: {{ $value->swatch_value }}"></span>
                                <span class="text-[10px] text-slate-500">{{ $value->swatch_value }}</span>
                            </div>
                        @else
                            <span class="text-[10px] text-slate-300">None</span>
                        @endif
                    </td>
                    <td class="py-2.5 text-xs text-slate-500 font-bold">{{ $value->display_order }}</td>
                    <td class="py-2.5">
                        <span class="px-2 py-0.5 rounded-md text-[9px] font-bold uppercase tracking-tighter {{ $value->status ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                            {{ $value->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="py-2.5 text-right">
                        <div class="flex justify-end space-x-1">
                            <a href="{{ route('admin.attribute-values.edit', $value->id) }}" class="p-1.5 text-indigo-400 hover:bg-indigo-50 rounded-md transition-all">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button type="button" onclick="confirmDelete('{{ $value->id }}')" class="p-1.5 text-rose-400 hover:bg-rose-50 rounded-md transition-all">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $value->id }}" action="{{ route('admin.attribute-values.destroy', $value->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-10 text-center text-slate-400 text-xs">No values found. Click "Add New Value" to get started.</td>
                </tr>
                @endforelse
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
