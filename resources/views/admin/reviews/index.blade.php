@extends('admin.layouts.admin')

@section('title', 'Product Reviews')

@section('content')
<div class="space-y-6">
    <!-- Header with Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card-glass p-6 rounded-2xl flex items-center justify-between">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Reviews</p>
                <p class="text-2xl font-black text-slate-800">{{ \App\Models\ProductReview::count() }}</p>
            </div>
            <div class="w-12 h-12 bg-rose-50 text-[#a91b43] rounded-xl flex items-center justify-center">
                <i class="fas fa-comments text-xl"></i>
            </div>
        </div>
        <div class="card-glass p-6 rounded-2xl flex items-center justify-between text-yellow-600">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pending Approval</p>
                <p class="text-2xl font-black">{{ \App\Models\ProductReview::where('status', 0)->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-clock text-xl"></i>
            </div>
        </div>
        <div class="card-glass p-6 rounded-2xl flex items-center justify-between text-emerald-600">
            <div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Avg. Rating</p>
                <p class="text-2xl font-black">{{ round(\App\Models\ProductReview::avg('stars') ?? 5, 1) }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                <i class="fas fa-star text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Filter / Search Row -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
        <form action="{{ route('admin.reviews.index') }}" method="GET" class="relative w-full flex flex-col md:flex-row items-center gap-3">
            <div class="relative w-full md:w-96 flex-shrink-0">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ Request::get('search') }}" placeholder="Search reviews..." 
                    class="w-full bg-white border border-slate-200 rounded-xl pl-12 pr-4 py-3 text-sm focus:border-[#a91b43] focus:ring-4 focus:ring-[#a91b43]/5 transition-all outline-none">
            </div>

            <div class="w-full md:w-48">
                <select name="status" onchange="this.form.submit()" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm focus:border-[#a91b43] outline-none transition-all">
                    <option value="">All Status</option>
                    <option value="1" {{ Request::get('status') == '1' ? 'selected' : '' }}>Published</option>
                    <option value="0" {{ Request::get('status') == '0' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            
            <div class="flex items-center gap-2 ml-auto">
                @if(Request::filled('search') || Request::filled('status'))
                <a href="{{ route('admin.reviews.index') }}" class="px-4 py-3 bg-slate-100 text-slate-500 rounded-xl hover:bg-slate-200 transition-colors text-sm font-bold flex items-center justify-center whitespace-nowrap">
                    Clear Filters
                </a>
                @endif
                
                <button type="submit" class="px-6 py-3 bg-[#a91b43] text-white rounded-xl hover:bg-[#940437] transition-colors text-sm font-bold shadow-lg shadow-pink-900/10 active:scale-[0.98]">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card-glass rounded-2xl overflow-hidden border-none shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">S.No</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider text-center">Rating</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Review</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-[11px] font-black text-slate-400 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($reviews as $review)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-xs font-bold text-slate-500">
                            {{ $reviews->firstItem() + $loop->index }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-slate-50 border border-slate-100 flex-shrink-0">
                                    <img src="{{ $review->product->primary_image ? asset('uploads/' . $review->product->primary_image) : asset('images/pro.png') }}" class="w-full h-full object-cover">
                                </div>
                                <div class="max-w-[200px]">
                                    <p class="text-xs font-black text-slate-800 truncate" title="{{ optional($review->product)->name ?? 'Deleted Product' }}">{{ optional($review->product)->name ?? 'Deleted Product' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">SKU: {{ optional($review->product)->sku ?: 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-[#a91b43]/10 text-[#a91b43] flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr(optional($review->user)->name ?? 'D', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-800">{{ optional($review->user)->name ?? 'Deleted User' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">{{ optional($review->user)->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center text-yellow-400 text-[10px] gap-0.5">
                                @for($i=1; $i<=5; $i++)
                                <i class="{{ $i <= $review->stars ? 'fas' : 'far' }} fa-star"></i>
                                @endfor
                            </div>
                            <span class="text-[10px] font-bold text-slate-400 mt-1 block">{{ $review->stars }} Stars</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="max-w-[300px]">
                                <p class="text-xs text-slate-600 line-clamp-2" title="{{ $review->review }}">
                                    {{ $review->review }}
                                </p>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($review->status == 1)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                <span class="w-1 h-1 rounded-full bg-emerald-600 mr-2"></span>
                                PUBLISHED
                            </span>
                            @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-yellow-50 text-yellow-600 border border-yellow-100">
                                <span class="w-1 h-1 rounded-full bg-yellow-600 mr-2"></span>
                                PENDING
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-xs font-bold text-slate-800">{{ $review->created_at->format('M d, Y') }}</p>
                            <p class="text-[10px] font-medium text-slate-400">{{ $review->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                @if($review->status == 0)
                                <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="1">
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Approve Review">
                                        <i class="fas fa-check text-xs"></i>
                                    </button>
                                </form>
                                @else
                                <form action="{{ route('admin.reviews.status', $review->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="0">
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-yellow-50 text-yellow-600 hover:bg-yellow-600 hover:text-white transition-all flex items-center justify-center shadow-sm" title="Mark as Pending">
                                        <i class="fas fa-pause text-xs"></i>
                                    </button>
                                </form>
                                @endif

                                <button type="button" onclick="confirmDelete('{{ route('admin.reviews.destroy', $review->id) }}')" 
                                        class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all flex items-center justify-center shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-comments text-slate-200 text-3xl"></i>
                                </div>
                                <h3 class="text-slate-800 font-black text-lg">No reviews yet</h3>
                                <p class="text-slate-400 text-sm max-w-[250px]">When customers review your products, they will appear here for your management.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($reviews->hasPages())
        <div class="px-6 py-4 border-t border-slate-50 bg-slate-50/30">
            {{ $reviews->links() }}
        </div>
        @endif
    </div>
</div>

<form id="deleteForm" method="POST" class="hidden">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
    function confirmDelete(url) {
        Swal.fire({
            title: 'Delete Review?',
            text: "This action cannot be undone and will remove the customer's feedback.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#a91b43',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Delete',
            borderRadius: '1rem'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteForm');
                form.action = url;
                form.submit();
            }
        })
    }
</script>
@endpush
@endsection
