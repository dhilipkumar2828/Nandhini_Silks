@extends('admin.layouts.admin')

@section('title', 'Profile Settings')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Overview -->
        <div class="lg:col-span-1">
            <div class="card-glass rounded-3xl p-6 text-center sticky top-6">
                <div class="relative inline-block mb-4">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden shadow-xl mx-auto border-2 border-slate-50 relative group">
                        @if($admin->profile_photo)
                            <img src="{{ asset($admin->profile_photo) }}" alt="{{ $admin->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full gradient-bg flex items-center justify-center text-3xl font-black text-white">
                                {{ substr($admin->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>
                
                <h2 class="text-lg font-black text-slate-900 capitalize">{{ $admin->name }}</h2>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $admin->role }}</p>

                <!-- Change Photo Button -->
                <div class="mt-4">
                    <form action="{{ route('admin.profile.photo') }}" method="POST" enctype="multipart/form-data" id="photoForm">
                        @csrf
                        <input type="file" name="profile_photo" id="profile_photo" class="hidden" onchange="document.getElementById('photoForm').submit()">
                        <button type="button" onclick="document.getElementById('profile_photo').click()" 
                            class="px-4 py-2 bg-slate-50 border border-slate-100 text-[#a91b43] rounded-xl font-bold text-[10px] uppercase tracking-widest hover:bg-[#a91b43] hover:text-white transition-all shadow-sm">
                            <i class="fas fa-camera mr-1"></i> Change Photo
                        </button>
                    </form>
                </div>
                
                <div class="mt-8 pt-8 border-t border-slate-50 space-y-4">
                    <div class="flex items-center justify-between text-left p-3 rounded-2xl bg-slate-50/50">
                        <div class="flex items-center space-x-5 text-left w-full overflow-hidden">
                            <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm border border-slate-100 flex-shrink-0">
                                <i class="fas fa-envelope text-[10px]"></i>
                            </div>
                            <div class="overflow-hidden">
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Email Address</p>
                                <p class="text-xs font-black text-slate-700 truncate lowercase">{{ $admin->email }}</p>
                            </div>
                        </div>
                    </div>
                    @if($admin->phone_number)
                    <div class="flex items-center justify-between text-left p-3 rounded-2xl bg-slate-50/50">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-xl bg-white flex items-center justify-center text-slate-400 shadow-sm border border-slate-100 flex-shrink-0">
                                <i class="fas fa-phone text-[10px]"></i>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Phone Number</p>
                                <p class="text-xs font-black text-slate-700 capitalize">{{ $admin->phone_number }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                </div>
            </div>
        </div>

        <!-- Edit Forms -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Basic Info -->
            <div class="card-glass rounded-3xl overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Edit Profile</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Manage your personal details</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-[#a91b43]/5 flex items-center justify-center text-[#a91b43]">
                        <i class="fas fa-user-edit text-xs"></i>
                    </div>
                </div>
                <form action="{{ route('admin.profile.update') }}" method="POST" class="px-8 py-4 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                placeholder="Enter name">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                placeholder="Enter email">
                        </div>
                        <div class="space-y-1.5 md:col-span-2">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                            <input type="text" name="phone_number" value="{{ old('phone_number', $admin->phone_number) }}"
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                placeholder="e.g. +91 9876543210">
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-[#a91b43] text-white rounded-xl font-bold text-[11px] uppercase tracking-widest hover:shadow-lg transition-all">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change -->
            <div class="card-glass rounded-3xl overflow-hidden">
                <div class="px-8 py-5 border-b border-slate-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Security</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Update your account password</p>
                    </div>
                    <div class="w-8 h-8 rounded-xl bg-slate-50 flex items-center justify-center text-slate-400">
                        <i class="fas fa-lock text-xs"></i>
                    </div>
                </div>
                <form action="{{ route('admin.profile.password') }}" method="POST" class="px-8 py-4 space-y-6">
                    @csrf
                    <div class="space-y-6">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                placeholder="••••••••">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">New Password</label>
                                <input type="password" name="password" required
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                    placeholder="••••••••">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Confirm Password</label>
                                <input type="password" name="password_confirmation" required
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                                    placeholder="••••••••">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 bg-slate-900 text-white rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-slate-800 transition-all">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
