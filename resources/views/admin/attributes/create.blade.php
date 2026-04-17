@extends('admin.layouts.admin')

@section('title', 'Create Attribute')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.attributes.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Add New Attribute</h2>
        </div>

        <form id="attributeForm" action="{{ route('admin.attributes.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Attribute Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="attributeName" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800"
                        placeholder="e.g. Color, Fabric, Size">
                    @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Attribute Slug (auto-generated) <span class="text-rose-500">*</span></label>
                    <input type="text" name="slug" id="attributeSlug" value="{{ old('slug') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                        placeholder="attribute-slug">
                    @error('slug') <p class="text-rose-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <div class="flex bg-slate-100 p-1 rounded-xl w-fit">
                        <label class="relative flex-1">
                            <input type="radio" name="status" value="1" class="sr-only peer" {{ old('status', '1') == '1' ? 'checked' : '' }}>
                            <div class="px-4 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all peer-checked:bg-white peer-checked:text-emerald-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                                Active
                            </div>
                        </label>
                        <label class="relative flex-1">
                            <input type="radio" name="status" value="0" class="sr-only peer" {{ old('status') == '0' ? 'checked' : '' }}>
                            <div class="px-4 py-1.5 rounded-lg text-xs font-bold cursor-pointer transition-all peer-checked:bg-white peer-checked:text-rose-600 peer-checked:shadow-sm text-slate-500 hover:text-slate-700">
                                Inactive
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.attributes.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Attribute
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const nameInput = document.getElementById('attributeName');
        const slugInput = document.getElementById('attributeSlug');
        let timer;

        nameInput.addEventListener('input', function() {
            slugInput.value = slugify(this.value);
            checkSlug(slugInput.value);
            checkName(this.value);
        });

        slugInput.addEventListener('input', function() {
            this.value = slugify(this.value);
            checkSlug(this.value);
        });

        function checkName(name) {
            clearTimeout(timer);
            if (!name) return;
            
            timer = setTimeout(() => {
                $.get('{{ route("admin.attributes.check-name") }}', { name: name }, function(data) {
                    const errorId = 'name-error';
                    let errorMsg = document.getElementById(errorId);
                    
                    if (data.exists) {
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.id = errorId;
                            errorMsg.className = 'text-rose-500 text-[10px] mt-1 font-bold';
                            nameInput.parentNode.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'This attribute name is already taken!';
                        nameInput.classList.add('border-rose-500');
                    } else {
                        if (errorMsg) errorMsg.remove();
                        nameInput.classList.remove('border-rose-500');
                    }
                });
            }, 500);
        }

        function checkSlug(slug) {
            clearTimeout(timer);
            if (!slug) return;
            
            timer = setTimeout(() => {
                $.get('{{ route("admin.attributes.check-slug") }}', { slug: slug }, function(data) {
                    const errorId = 'slug-error';
                    let errorMsg = document.getElementById(errorId);
                    
                    if (data.exists) {
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.id = errorId;
                            errorMsg.className = 'text-rose-500 text-[10px] mt-1 font-bold';
                            slugInput.parentNode.appendChild(errorMsg);
                        }
                        errorMsg.textContent = 'This slug is already taken!';
                        slugInput.classList.add('border-rose-500');
                    } else {
                        if (errorMsg) errorMsg.remove();
                        slugInput.classList.remove('border-rose-500');
                    }
                });
            }, 500);
        }

        $("#attributeForm").validate({
            rules: {
                group: "required",
                name: "required",
                status: "required"
            },
            messages: {
                group: "Please enter attribute group",
                name: "Please enter attribute name",
                status: "Please select status"
            }
        });
    });
</script>
@endpush
