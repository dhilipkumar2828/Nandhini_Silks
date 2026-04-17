@extends('admin.layouts.admin')

@section('title', 'Inquiry Details')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.inquiries.index') }}" class="flex items-center text-slate-500 hover:text-[#a91b43] transition-colors font-bold text-xs uppercase tracking-widest">
            <i class="fas fa-arrow-left mr-2"></i> Back to Inquiries
        </a>
        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] border 
            @if($inquiry->status == 'responded') bg-emerald-50 text-emerald-600 border-emerald-100
            @else bg-amber-50 text-amber-600 border-amber-100 @endif">
            {{ $inquiry->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Inquiry Content -->
        <div class="md:col-span-2 space-y-6">
            <div class="card-glass p-8 rounded-3xl relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-[#a91b43]/[0.02] rounded-full -mr-16 -mt-16"></div>
                
                <div class="relative">
                    <div class="flex items-center space-x-4 mb-8">
                        <div class="w-12 h-12 rounded-2xl gradient-bg flex items-center justify-center text-white font-black text-lg shadow-lg">
                            {{ substr($inquiry->name, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-black text-slate-900 leading-tight">{{ $inquiry->name }}</h2>
                            <p class="text-xs font-bold text-[#a91b43] mt-0.5">{{ $inquiry->email }}</p>
                        </div>
                        <div class="ml-auto text-right">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $inquiry->created_at->format('d M Y') }}</p>
                            <p class="text-[9px] font-bold text-slate-300 uppercase tracking-tighter mt-0.5">{{ $inquiry->created_at->diffForHumans() }}</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Customer Message</label>
                        <div class="p-6 bg-slate-50/50 rounded-2xl border border-slate-100 text-slate-700 leading-relaxed text-sm italic font-medium">
                            "{{ $inquiry->message }}"
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Actions -->
        <div class="space-y-6">
            <div class="card-glass p-6 rounded-3xl">
                <h3 class="text-sm font-black text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-bolt text-[#a91b43] mr-2"></i> Fast Actions
                </h3>

                <form action="{{ route('admin.inquiries.update', $inquiry->id) }}" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Update Status</label>
                        <select name="status" class="w-full bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none appearance-none cursor-pointer shadow-sm">
                            <option value="pending" {{ $inquiry->status == 'pending' ? 'selected' : '' }}>Mark as Pending</option>
                            <option value="responded" {{ $inquiry->status == 'responded' ? 'selected' : '' }}>Mark as Responded</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Customer Response / Admin Note</label>
                        <textarea name="admin_note" rows="6" placeholder="Write your response to the customer here. This will be sent as an email when status is set to 'Responded'."
                                  class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#a91b43]/20 focus:border-[#a91b43] transition-all outline-none shadow-sm">{{ $inquiry->admin_note }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-3 gradient-bg text-white rounded-xl text-xs font-black uppercase tracking-[0.2em] shadow-lg shadow-[#a91b43]/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                        Update Inquiry
                    </button>
                </form>
            </div>

            <div class="card-glass p-6 rounded-3xl bg-slate-900 text-white border-none shadow-xl">
                <h3 class="text-sm font-black mb-4 flex items-center">
                    <i class="fas fa-envelope-open-text text-[#fbb624] mr-2"></i> Quick Reply
                </h3>
                <p class="text-[11px] text-slate-400 font-bold mb-4 leading-relaxed">Copy the customer's email and reply directly from your mail client.</p>
                <button onclick="copyEmail()" class="w-full py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border border-white/10">
                    <span id="copyText">Copy Email Address</span>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyEmail() {
        const email = "{{ $inquiry->email }}";
        navigator.clipboard.writeText(email).then(() => {
            const btnText = document.getElementById('copyText');
            btnText.innerText = 'Copied!';
            btnText.parentElement.classList.replace('bg-white/10', 'bg-emerald-500/20');
            btnText.parentElement.classList.replace('hover:bg-white/20', 'hover:bg-emerald-500/30');
            
            setTimeout(() => {
                btnText.innerText = 'Copy Email Address';
                btnText.parentElement.classList.replace('bg-emerald-500/20', 'bg-white/10');
                btnText.parentElement.classList.replace('hover:bg-emerald-500/30', 'hover:bg-white/20');
            }, 2000);
            
            toastr.success('Email address copied to clipboard');
        });
    }
</script>
@endpush
