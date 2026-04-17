@extends('admin.layouts.admin')

@section('title', 'Product Operation Successful')

@section('content')
<div class="flex flex-col items-center justify-center min-h-[70vh]">
    <div class="bg-white p-12 rounded-3xl shadow-2xl border border-slate-100 flex flex-col items-center max-w-md w-full text-center relative overflow-hidden">
        <!-- Background Glow -->
        <div class="absolute -top-12 -right-12 w-40 h-40 bg-[#a91b43]/5 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-12 -left-12 w-40 h-40 bg-indigo-500/5 rounded-full blur-3xl"></div>

        <!-- Success Icon with Animation -->
        <div class="w-24 h-24 rounded-full bg-emerald-50 flex items-center justify-center mb-6 animate-bounce shadow-inner border border-emerald-100">
            <i class="fas fa-check-circle text-5xl text-emerald-500"></i>
        </div>

        <h2 class="text-3xl font-black text-slate-800 mb-2 leading-tight">Masterfully Done!</h2>
        <p class="text-slate-500 mb-8 font-medium">Your product has been successfully {{ session('action', 'published') }} to the catalog.</p>
        
        <!-- Progress Bar -->
        <div class="w-full h-1.5 bg-slate-100 rounded-full mb-8 overflow-hidden">
            <div id="redirectProgress" class="h-full bg-[#a91b43] transition-all duration-[3000ms] ease-linear" style="width: 0%"></div>
        </div>

        <div class="flex flex-col gap-3 w-full">
            <a href="{{ route('admin.products.index') }}" class="w-full bg-[#a91b43] text-white py-3.5 rounded-xl font-bold shadow-lg shadow-rose-200 hover:shadow-xl hover:-translate-y-0.5 transition-all text-center">
                <i class="fas fa-list mr-2"></i> Go to Product List
            </a>
            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-2">Auto-redirecting in 3 seconds...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Simple progress bar animation
        setTimeout(() => {
            $('#redirectProgress').css('width', '100%');
        }, 100);

        // Auto redirect
        setTimeout(() => {
            window.location.href = "{{ route('admin.products.index') }}";
        }, 3500);
    });
</script>
@endpush
@endsection
