@extends('admin.layouts.admin')

@section('title', 'Attribute Values')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
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
            <form method="GET" action="{{ route('admin.attribute-values.index') }}" class="flex items-center pt-2 md:pt-0">
                @if(request('attribute_id'))<input type="hidden" name="attribute_id" value="{{ request('attribute_id') }}">@endif
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
            <form action="{{ route('admin.attribute-values.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('attribute_id'))
                    <input type="hidden" name="attribute_id" value="{{ request('attribute_id') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search values..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.attribute-values.index', ['attribute_id' => request('attribute_id'), 'status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
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
                            <option value="{{ route('admin.attribute-values.index', ['status' => $key, 'attribute_id' => request('attribute_id'), 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>

            <a href="{{ route('admin.attribute-values.create', ['attribute_id' => $attribute ? $attribute->id : '']) }}" class="bg-[#a91b43] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all whitespace-nowrap shadow-sm">
                <i class="fas fa-plus mr-1.5"></i> Add New Value
            </a>
        </div>
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
                        @php
                            $swatch = $value->swatch_value;
                            $isColor = $swatch && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $swatch);
                        @endphp
                        @if($swatch)
                            @if($isColor)
                                <div class="flex items-center">
                                    <span class="w-4 h-4 rounded-full border border-slate-100 mr-2" style="background-color: {{ $swatch }}"></span>
                                    <span class="text-[10px] text-slate-500">{{ $swatch }}</span>
                                </div>
                            @else
                                <div class="flex items-center">
                                    <img src="{{ asset('uploads/' . $swatch) }}" alt="Swatch" class="w-5 h-5 rounded-md border border-slate-100 mr-2 object-cover">
                                    <span class="text-[10px] text-slate-500">Image</span>
                                </div>
                            @endif
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
    
    <div class="mt-6">
        {{ $attributeValues->links() }}
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
