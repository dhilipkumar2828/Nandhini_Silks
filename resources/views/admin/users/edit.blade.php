@extends('admin.layouts.admin')

@section('title', 'Edit User')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Edit User</h2>
            <p class="text-xs text-slate-400">Update profile and manage addresses</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.show', $user->id) }}" class="px-4 py-2 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">View</a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">Back</a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.users.update', $user->id) }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Full Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                    pattern="[A-Za-z\s]+" title="Only alphabets and spaces are allowed"
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                @error('name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" 
                    pattern="[0-9]{10}" maxlength="10" minlength="10" title="Phone number must be exactly 10 digits"
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                @error('phone') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Role</label>
                <input type="text" name="role" value="{{ old('role', $user->role) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Account Status</label>
                <select name="account_status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    <option value="Active" {{ strtolower(old('account_status', $user->account_status)) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ strtolower(old('account_status', $user->account_status)) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Gender</label>
                <select name="gender" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    <option value="">Select</option>
                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">DOB</label>
                <input type="date" name="dob" value="{{ old('dob', optional($user->dob)->format('Y-m-d')) }}" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Profile Picture</label>
                <input type="file" name="profile_picture" class="w-full bg-white border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-700">
                @if($user->profile_picture)
                    <div class="text-[10px] text-slate-400">Current: {{ $user->profile_picture }}</div>
                @endif
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">New Password</label>
                <input type="password" name="password" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="Leave empty to keep current">
            </div>
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-[#a91b43] text-white px-10 py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] shadow-xl shadow-pink-900/10 transition-all active:scale-95">
                Save Changes
            </button>
        </div>
    </form>

    {{-- <div class="mt-10">
        <h3 class="text-sm font-bold text-slate-800 mb-3">Addresses</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($user->addresses as $address)
                <div class="p-4 rounded-xl border border-slate-100 bg-white">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-700">{{ $address->label ?? 'Address' }}</span>
                        <div class="flex items-center gap-3">
                            @if($address->is_default)
                                <span class="text-[10px] font-bold uppercase text-emerald-700 bg-emerald-100/50 px-2 py-1 rounded">DEFAULT</span>
                            @endif
                            <button type="button" class="flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition-colors edit-address-btn" data-address="{{ json_encode($address) }}">
                                <i class="fas fa-pen text-[10px]"></i> Edit
                            </button>
                            <form method="POST" action="{{ route('admin.users.addresses.destroy', [$user->id, $address->id]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center gap-1 text-xs font-semibold text-rose-500 hover:text-rose-700 transition-colors" onclick="return confirm('Delete this address?')">
                                    <i class="fas fa-trash text-[10px]"></i> Remove
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="text-xs text-slate-600 leading-relaxed font-medium">
                        <div class="font-bold text-slate-800">{{ $address->recipient_name }}</div>
                        <div class="text-[10px] text-slate-500 mb-1 tracking-tighter">{{ $address->recipient_phone }}</div>
                        {{ $address->address1 }}<br>
                        {{ $address->city }}, {{ $address->state }} {{ $address->zip }}<br>
                        {{ $address->country }}<br>
                        @if($address->landmark)<span class="text-slate-400">Landmark: {{ $address->landmark }}</span>@endif
                    </div>
                </div>
            @empty
                <div class="text-xs text-slate-400 italic">No addresses saved.</div>
            @endforelse
        </div>

        <div class="card-glass p-5 rounded-2xl mt-6">
            <h4 class="text-sm font-bold text-slate-800 mb-4" id="address-form-title">Add Address</h4>
            <form id="address-form" method="POST" action="{{ route('admin.users.addresses.store', $user->id) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="_method" id="address-method" value="POST">
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Recipient Full Name <span class="text-rose-500">*</span></label>
                        <input type="text" name="recipient_name" required 
                            pattern="[A-Za-z\s]+" title="Only alphabets and spaces are allowed"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="e.g. John Doe">
                        @error('recipient_name') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Recipient Phone Number <span class="text-rose-500">*</span></label>
                        <input type="text" name="recipient_phone" required 
                            pattern="[0-9]{10}" maxlength="10" minlength="10" title="Phone number must be exactly 10 digits"
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="e.g. 9876543210">
                        @error('recipient_phone') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Address Label</label>
                    <input type="text" name="label" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="Home / Office">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Landmark</label>
                    <input type="text" name="landmark" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="Near temple">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Address Line 1 <span class="text-rose-500">*</span></label>
                    <input type="text" name="address1" required class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="Street name, Apartment">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 mb-1">Address Line 2 (Optional)</label>
                    <input type="text" name="address2" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800" placeholder="Suite, unit, etc.">
                </div>
               
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">City <span class="text-rose-500">*</span></label>
                    <input type="text" name="city" required 
                        pattern="[A-Za-z\s]+" title="Only alphabets and spaces are allowed"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    @error('city') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">State <span class="text-rose-500">*</span></label>
                    <div class="relative">
                        <select name="state" required 
                            class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800 appearance-none cursor-pointer">
                            <option value="">Select State</option>
                            @php
                                $states = [
                                    "Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", 
                                    "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli and Daman and Diu", "Delhi", "Goa", 
                                    "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", 
                                    "Kerala", "Ladakh", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", 
                                    "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Puducherry", "Punjab", "Rajasthan", 
                                    "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
                                ];
                            @endphp
                            @foreach($states as $stateName)
                                <option value="{{ $stateName }}" {{ old('state') == $stateName ? 'selected' : '' }}>{{ $stateName }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-[10px]"></i>
                        </div>
                    </div>
                    @error('state') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">ZIP</label>
                    <input type="text" name="zip" 
                        pattern="[0-9]{6}" maxlength="6" minlength="6" title="ZIP code must be exactly 6 digits"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    @error('zip') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Country <span class="text-rose-500">*</span></label>
                    <input type="text" name="country" value="India" 
                        pattern="[A-Za-z\s]+" title="Only alphabets and spaces are allowed"
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                    @error('country') <p class="text-rose-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex items-center gap-2 md:col-span-2">
                    <input type="checkbox" name="is_default" value="1" class="accent-[#a91b43]">
                    <label class="text-xs font-bold text-slate-700">Set as default address</label>
                </div>
                <div class="md:col-span-2 flex justify-end gap-2">
                    <button type="button" id="cancel-edit-btn" class="hidden bg-slate-100 text-slate-500 px-6 py-2 rounded-lg text-xs font-bold transition-all">Cancel</button>
                    <button type="submit" id="address-submit-btn" class="bg-indigo-600 text-white px-8 py-2 rounded-lg text-xs font-bold hover:bg-indigo-700 transition-all">Add Address</button>
                </div>
            </form>
        </div>
    </div> --}}
</div>
@push('scripts')
<script>
    // Function to restrict input to alphabets and spaces only
    function restrictToAlphabets(event) {
        const input = event.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        
        // Remove everything except letters and spaces
        const newValue = input.value.replace(/[^A-Za-z\s]/g, '');
        
        if (input.value !== newValue) {
            input.value = newValue;
            // Restore cursor position
            input.setSelectionRange(start, end);
        }
    }

    // Function to restrict input to numbers only
    function restrictToNumbers(event) {
        const input = event.target;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const newValue = input.value.replace(/[^0-9]/g, '');
        if (input.value !== newValue) {
            input.value = newValue;
            input.setSelectionRange(start, end);
        }
    }

    // Attach listener to relevant fields
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('address-form');
        const formTitle = document.getElementById('address-form-title');
        const submitBtn = document.getElementById('address-submit-btn');
        const methodInput = document.getElementById('address-method');
        const cancelBtn = document.getElementById('cancel-edit-btn');
        const userId = "{{ $user->id }}";
        const storeUrl = "{{ route('admin.users.addresses.store', $user->id) }}";

        // Fields to populate
        const fields = [
            'recipient_name', 'recipient_phone', 'label', 'landmark',
            'address1', 'address2', 'city', 'state', 'zip', 'country'
        ];

        document.querySelectorAll('.edit-address-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const address = JSON.parse(this.dataset.address);
                
                // Populate fields
                fields.forEach(field => {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) input.value = address[field] || '';
                });

                // Set checkbox
                const defaultCheckbox = form.querySelector('[name="is_default"]');
                if (defaultCheckbox) defaultCheckbox.checked = address.is_default;

                // Update form state for Edit
                formTitle.textContent = 'Edit Address';
                submitBtn.textContent = 'Update Address';
                submitBtn.classList.replace('bg-indigo-600', 'bg-[#a91b43]');
                methodInput.value = 'PUT';
                form.action = `/admin/users/${userId}/addresses/${address.id}`;
                cancelBtn.classList.remove('hidden');

                // Smooth scroll to form
                form.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });

        cancelBtn.addEventListener('click', function() {
            // Reset form
            form.reset();
            formTitle.textContent = 'Add Address';
            submitBtn.textContent = 'Add Address';
            submitBtn.classList.replace('bg-[#a91b43]', 'bg-indigo-600');
            methodInput.value = 'POST';
            form.action = storeUrl;
            this.classList.add('hidden');
        });

        const numericFields = [
            'input[name="phone"]',
            'input[name="recipient_phone"]',
            'input[name="zip"]'
        ];

        numericFields.forEach(selector => {
            const el = document.querySelector(selector);
            if (el) el.addEventListener('input', restrictToNumbers);
        });
    });
</script>
@endpush
@endsection
