@extends('admin.layouts.admin')

@section('title', 'User Details')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-slate-800">User Profile</h2>
            <p class="text-xs text-slate-400">Full account details and addresses</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="px-4 py-2 rounded-lg text-xs font-bold bg-[#a91b43] text-white">Edit User</a>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg text-xs font-bold bg-slate-100 text-slate-600">Back</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="card-glass p-5 rounded-2xl">
            <div class="flex items-center gap-4">
                @php
                    $avatar = null;
                    if (!empty($user->profile_picture)) {
                        $profilePicture = ltrim((string) $user->profile_picture, '/');
                        if (str_starts_with($profilePicture, 'http://') || str_starts_with($profilePicture, 'https://')) {
                            $avatar = $profilePicture;
                        } elseif (str_starts_with($profilePicture, 'uploads/')) {
                            $avatar = asset($profilePicture);
                        } else {
                            $avatar = asset('uploads/' . $profilePicture);
                        }
                    }
                @endphp
                @if($avatar)
                    <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-14 h-14 rounded-full object-cover border border-slate-200">
                @else
                    <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-bold text-lg">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="text-lg font-bold text-slate-800">{{ $user->name }}</div>
                    <div class="text-xs text-slate-400">{{ $user->email }}</div>
                    <div class="text-xs text-slate-400">{{ $user->phone ?? '-' }}</div>
                </div>
            </div>

            <div class="mt-5 space-y-2 text-xs text-slate-600">
                <div class="flex justify-between"><span>Role</span><span class="font-bold uppercase">{{ $user->role }}</span></div>
                <div class="flex justify-between"><span>Status</span><span class="font-bold {{ $user->account_status === 'active' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $user->account_status }}</span></div>
                <div class="flex justify-between"><span>Gender</span><span class="font-bold">{{ $user->gender ?? '-' }}</span></div>
                <div class="flex justify-between"><span>DOB</span><span class="font-bold">{{ $user->dob ? $user->dob->format('d M Y') : '-' }}</span></div>
                <div class="flex justify-between"><span>Registered</span><span class="font-bold">{{ $user->created_at ? $user->created_at->format('d M Y, h:i A') : '-' }}</span></div>
                <div class="flex justify-between"><span>Last Login</span><span class="font-bold">{{ $user->last_login_at ? $user->last_login_at->format('d M Y, h:i A') : '-' }}</span></div>
            </div>
        </div>

        <div class="card-glass p-5 rounded-2xl lg:col-span-2">
            <h3 class="text-sm font-bold text-slate-800 mb-3">Order Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Total Orders</div>
                    <div class="text-xl font-black text-slate-800 mt-1">{{ $user->orders->count() }}</div>
                </div>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="text-[10px] text-slate-400 uppercase font-bold">Total Spent</div>
                    <div class="text-xl font-black text-slate-800 mt-1">&#8377;{{ number_format($user->orders->sum('grand_total'), 2) }}</div>
                </div>
            </div>

            <!-- <div class="mt-6">
                <h3 class="text-sm font-bold text-slate-800 mb-3">Addresses</h3>
                @if($user->addresses->count())
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($user->addresses as $address)
                            <div class="p-4 rounded-xl border border-slate-100 bg-white">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-700">{{ $address->label ?? 'Address' }}</span>
                                    @if($address->is_default)
                                        <span class="text-[9px] font-bold uppercase text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Default</span>
                                    @endif
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
                        @endforeach
                    </div>
                @else
                    <div class="text-xs text-slate-400 italic">No addresses saved.</div>
                @endif
            </div> -->
        </div>
    </div>
</div>
@endsection
