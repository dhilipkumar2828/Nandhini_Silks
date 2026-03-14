@extends('admin.layouts.admin')

@section('title', 'Edit Testimonial')

@section('content')
<div class="card-glass p-6 rounded-2xl max-w-2xl mx-auto">
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.testimonials.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-bold text-slate-800">Edit Testimonial</h2>
    </div>

    <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Customer Name</label>
                    <input type="text" name="name" value="{{ $testimonial->name }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Current Photo</label>
                    <div class="flex items-center space-x-3 mb-2">
                        @if($testimonial->photo)
                            <img src="{{ asset('uploads/' . $testimonial->photo) }}" class="w-10 h-10 rounded-full object-cover border border-slate-100 shadow-sm">
                        @endif
                        <input type="file" name="photo" class="flex-1 px-4 py-2 rounded-xl border border-slate-100 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Review Content</label>
                <textarea name="review" rows="4" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm" required>{{ $testimonial->review }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Rating (1-5)</label>
                    <select name="rating" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ $testimonial->rating == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Submitted At</label>
                    <input type="date" name="submitted_at" value="{{ $testimonial->submitted_at ? $testimonial->submitted_at->format('Y-m-d') : '' }}" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Display on Homepage?</label>
                    <select name="display_homepage" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                        <option value="1" {{ $testimonial->display_homepage ? 'selected' : '' }}>Show</option>
                        <option value="0" {{ !$testimonial->display_homepage ? 'selected' : '' }}>Hide</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-widest mb-1">Status</label>
                    <select name="status" class="w-full px-4 py-2 rounded-xl border border-slate-100 text-sm appearance-none bg-white">
                        <option value="1" {{ $testimonial->status ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ !$testimonial->status ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full bg-[#a91b43] text-white py-2.5 rounded-xl text-sm font-bold hover:bg-[#940437] transition-all">
                    Update Testimonial
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
