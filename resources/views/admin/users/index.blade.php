@extends('admin.layouts.admin')

@section('title', 'Users')

@section('content')
<div class="card-glass p-6 rounded-2xl">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div class="flex items-center space-x-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">User Management</h2>
                <p class="text-[10px] text-slate-400">View customer profiles, status, and spending</p>
            </div>
            <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center">
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
            <form action="{{ route('admin.users.index') }}" method="GET" class="relative w-full sm:w-64">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('per_page'))
                    <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                @endif
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, email, phone..." oninput="clearTimeout(this.timer); this.timer = setTimeout(() => { this.form.submit(); }, 500);" 
                       class="w-full pl-10 pr-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                @if(request('search'))
                    <a href="{{ route('admin.users.index', ['status' => request('status'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-rose-500">
                        <i class="fas fa-times text-xs"></i>
                    </a>
                @endif
            </form>

            <div class="w-full sm:w-40">
                @php
                    $currentStatus = request('status', 'all');
                    $statuses = [
                        'all' => 'All Status',
                        'Active' => 'Active',
                        'Inactive' => 'Inactive'
                    ];
                @endphp
                <div class="relative">
                    <select onchange="window.location.href=this.value" 
                            class="appearance-none bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] block w-full px-4 py-2 transition-all outline-none cursor-pointer shadow-sm">
                        @foreach($statuses as $key => $label)
                            <option value="{{ route('admin.users.index', ['status' => $key, 'search' => request('search'), 'per_page' => request('per_page')]) }}" {{ $currentStatus == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-[10px]"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="text-slate-400 text-[10px] font-bold uppercase tracking-widest border-b border-slate-100">
                    <th class="pb-3 px-2 font-bold text-center w-12">S.No</th>
                    <th class="pb-3 font-bold px-4  w-32">User</th>
                    <th class="pb-3 font-bold text-center w-32">Status</th>
                    <th class="pb-3 font-bold px-4 w-32">Registration</th>
                    <th class="pb-3 font-bold text-center px-4 w-32">Orders</th>
                    <th class="pb-3 font-bold px-4 w-32">Total Spent</th>
                    <th class="pb-3 font-bold text-right pr-6 w-32">Actions</th>
                </tr>
            </thead>
            <tbody class="text-sm">
                @forelse($users as $user)
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                        <td class="py-3 px-2 text-xs font-bold text-slate-500 text-center">
                            {{ $users->firstItem() + $loop->index }}
                        </td>
                        <td class="py-3 px-4">
                            <div class="flex items-center gap-3">
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
                                    <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-9 h-9 rounded-full object-cover border border-slate-200 shadow-sm">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center font-black text-[10px] border border-slate-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <div class="font-bold text-slate-800 text-sm capitalize">{{ $user->name }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium">{{ $user->email }}</div>
                                    <div class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $user->phone ?? 'No Phone' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ strtolower($user->account_status) === 'active' ? 'bg-emerald-100 text-emerald-600 border-emerald-200' : 'bg-rose-100 text-rose-600 border-rose-200' }}">
                                {{ $user->account_status }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-[10px] text-slate-500 font-black uppercase tracking-tighter">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</div>
                            <div class="text-[9px] text-slate-300 font-bold uppercase">{{ $user->role }}</div>
                        </td>
                        <td class="py-3 text-center">
                            <span class="w-7 h-7 inline-flex items-center justify-center bg-slate-50 text-slate-700 rounded-lg border border-slate-100 font-black text-xs shadow-sm">
                                {{ $user->orders_count ?? 0 }}
                            </span>
                        </td>
                        <td class="py-3 px-4">
                            <div class="text-[11px] text-slate-800 font-black">₹{{ number_format($user->orders_sum_grand_total ?? 0, 2) }}</div>
                            @if($user->last_login_at)
                                <div class="text-[8px] text-slate-400 font-bold uppercase">Last Login: {{ $user->last_login_at->format('d/m/y') }}</div>
                            @endif
                        </td>
                        <td class="py-3 text-right pr-6">
                            <div class="flex justify-end items-center space-x-2">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="flex items-center justify-center w-8 h-8 text-indigo-500 bg-indigo-50/50 hover:bg-indigo-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-indigo-100" title="View Profile">
                                    <i class="fas fa-eye text-[10px]"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="flex items-center justify-center w-8 h-8 text-amber-500 bg-amber-50/50 hover:bg-amber-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-amber-100" title="Edit User">
                                    <i class="fas fa-edit text-[10px]"></i>
                                </a>
                                @if($user->id !== auth()->id())
                                    <button type="button" 
                                            class="flex items-center justify-center w-8 h-8 text-rose-500 bg-rose-50/50 hover:bg-rose-500 hover:text-white rounded-lg transition-all duration-300 shadow-sm border border-rose-100 delete-user-btn" 
                                            data-id="{{ $user->id }}" 
                                            data-name="{{ $user->name }}"
                                            title="Delete User">
                                        <i class="fas fa-trash text-[10px]"></i>
                                    </button>
                                    <form id="delete-form-{{ $user->id }}" action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center text-slate-400 italic">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
        $('.delete-user-btn').click(function() {
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            
            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to delete user "${userName}". This action cannot be undone!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#a91b43',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                    cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${userId}`).submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
