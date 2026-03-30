@extends('admin.layouts.admin')

@section('title', 'Create Testimonial')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-glass p-6 rounded-2xl">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.testimonials.index') }}" class="mr-4 text-slate-400 hover:text-slate-600 transition-all">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h2 class="text-lg font-bold text-slate-800">Add New Testimonial</h2>
        </div>

        <form id="testimonialForm" action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Customer Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] focus:ring-2 focus:ring-pink-50 transition-all text-slate-800"
                        placeholder="John Doe">
                </div>

                {{-- <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Customer Photo</label>
                    <input type="file" name="photo" 
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div> --}}

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Rating <span class="text-rose-500">*</span></label>
                    <select name="rating" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ old('rating') == $i ? 'selected' : '' }}>{{ $i }} Stars</option>
                        @endfor
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Submitted Date <span class="text-rose-500">*</span></label>
                    <input type="date" name="submitted_at" value="{{ old('submitted_at', date('Y-m-d')) }}" required
                        class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Display on Homepage? <span class="text-rose-500">*</span></label>
                    <select name="display_homepage" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('display_homepage') == '1' ? 'selected' : '' }}>Show</option>
                        <option value="0" {{ old('display_homepage') == '0' ? 'selected' : '' }}>Hide</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Status <span class="text-rose-500">*</span></label>
                    <select name="status" class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700">Review Content <span class="text-rose-500">*</span></label>
                <textarea name="review" rows="4" required
                    class="w-full bg-slate-50 border border-slate-200 px-3 py-2 rounded-lg text-sm outline-none focus:border-[#a91b43] transition-all text-slate-800"
                    placeholder="Write the customer testimonial here...">{{ old('review') }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.testimonials.index') }}" class="px-6 py-2 rounded-lg text-sm text-slate-600 hover:bg-slate-50 transition-all font-semibold">Cancel</a>
                <button type="submit" class="bg-[#a91b43] text-white px-8 py-2 rounded-lg text-sm hover:bg-[#940437] shadow-lg shadow-pink-900/10 transition-all font-semibold active:scale-[0.98]">
                    Save Testimonial
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $("#testimonialForm").validate({
            rules: {
                name: "required",
                rating: "required",
                review: "required",
                submitted_at: "required"
            },
            messages: {
                name: "Please enter customer name",
                rating: "Please select rating",
                review: "Please enter review content",
                submitted_at: "Please select date"
            }
        });
    });
</script>
@endpush
