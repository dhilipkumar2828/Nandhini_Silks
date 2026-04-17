@extends('admin.layouts.admin')

@section('title', 'Team Management')

@section('content')
<div x-data="{ showModal: false }" class="space-y-8">
    <!-- Header Card -->
    <div class="card-glass rounded-3xl p-8 flex flex-col md:flex-row md:items-center justify-between gradient-bg text-white border-none shadow-xl shadow-[#a91b43]/20">
        <div>
            <h2 class="text-2xl font-black uppercase tracking-tight">Administrators</h2>
            <p class="text-white/80 text-[10px] font-bold uppercase tracking-[0.2em] mt-1">Configure system access and user roles</p>
        </div>
        <div class="mt-6 md:mt-0">
            <button @click="showModal = true" class="px-8 py-3 bg-white text-[#a91b43] rounded-2xl font-black text-[11px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl">
                <i class="fas fa-plus-circle mr-2"></i>
                Add Administrator
            </button>
        </div>
    </div>

    <!-- Admin List -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($admins as $adm)
        <div class="card-glass rounded-3xl p-6 hover:translate-y-[-4px] transition-all group">
            <div class="flex items-start justify-between">
                <div class="w-14 h-14 rounded-2xl overflow-hidden flex items-center justify-center border border-slate-100 shadow-sm transition-colors uppercase">
                    @if($adm->profile_photo)
                        <img src="{{ asset($adm->profile_photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-50 flex items-center justify-center text-xl font-black text-slate-300 group-hover:bg-[#a91b43]/5 group-hover:text-[#a91b43]/30">
                            {{ substr($adm->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                <div class="px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[9px] font-black uppercase tracking-widest border border-emerald-100/50">
                    Active
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="text-base font-black text-slate-900 capitalize">{{ $adm->name }}</h3>
                <p class="text-[11px] font-bold text-slate-400 mt-0.5">{{ $adm->email }}</p>
                <p class="text-[10px] font-black text-[#a91b43] uppercase tracking-widest mt-2 bg-[#a91b43]/5 inline-block px-2 py-0.5 rounded-lg">{{ $adm->role }}</p>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-50 space-y-3">
                <div class="flex items-center space-x-3 text-slate-500">
                    <i class="fas fa-phone text-[10px] w-4"></i>
                    <span class="text-xs font-bold">{{ $adm->phone_number ?? 'No Phone' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Joined Date</p>
                        <p class="text-xs font-black text-slate-700">{{ $adm->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Add Admin Modal -->
    <div x-show="showModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center px-4 bg-slate-950/40 backdrop-blur-sm" style="display: none;">
        
        <div @click.away="showModal = false" class="card-glass w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden">
            <div class="px-8 py-6 border-b border-slate-50 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">New Administrator</h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">Create a new staff account</p>
                </div>
                <button @click="showModal = false" class="w-9 h-9 rounded-xl bg-white flex items-center justify-center text-slate-400 hover:text-rose-600 shadow-sm border border-slate-100 transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            
            <form action="{{ route('admin.manage-admins.store') }}" method="POST" class="p-8 space-y-5">
                @csrf
                <div class="grid grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Full Name</label>
                        <input type="text" name="name" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                            placeholder="Full Name">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">System Role</label>
                        <select name="role" required
                            class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700">
                            <option value="Admin">Admin</option>
                            <option value="Super Admin">Super Admin</option>
                            <option value="Manager">Manager</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Email Address</label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                        placeholder="email@example.com">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Phone Number</label>
                    <input type="text" name="phone_number"
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                        placeholder="+91 00000 00000">
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Secure Password</label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-100 focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none font-bold text-xs text-slate-700"
                        placeholder="••••••••">
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-4 bg-[#a91b43] text-white rounded-2xl font-black text-[11px] uppercase tracking-widest shadow-xl shadow-[#a91b43]/20 hover:scale-[1.01] active:scale-[0.99] transition-all">
                        Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
