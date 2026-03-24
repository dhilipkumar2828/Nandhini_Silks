@extends('frontend.layouts.app')

@section('title', 'My Reviews | Nandhini Silks')

@push('styles')
<style>
    .review-tabs {
        display: flex;
        gap: 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid #eee;
    }

    .review-tab {
        padding-bottom: 15px;
        font-size: 15px;
        font-weight: 600;
        color: #999;
        cursor: pointer;
        position: relative;
    }

    .review-tab.active {
        color: var(--pink);
    }

    .review-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 2px;
        background: var(--pink);
    }

    .review-item-card {
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        display: flex;
        gap: 25px;
    }

    .review-product-img {
        width: 100px;
        height: 100px;
        border-radius: 10px;
        object-fit: cover;
    }

    .review-content-side {
        flex: 1;
    }

    .review-product-name {
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
    }

    .review-stars {
        color: #ffc107;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .review-text {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin-bottom: 15px;
    }

    .review-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
        color: #999;
    }

    .review-actions {
        display: flex;
        gap: 15px;
    }

    .review-action-btn {
        background: none;
        border: none;
        color: var(--pink);
        font-weight: 600;
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
    }

    .pending-review-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #fff5f7;
        padding: 20px;
        border-radius: 15px;
        border: 1px dashed var(--pink);
        margin-bottom: 15px;
    }

    .btn-review-now {
        background: var(--pink);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        cursor: pointer;
    }

    @media (max-width: 600px) {
        .review-item-card {
            flex-direction: column;
        }

        .review-product-img {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endpush

@section('content')
    <main class="account-page">
        <div class="page-shell">
            <div class="breadcrumb">
                <a href="{{ route('home') }}">Home</a> &nbsp; / &nbsp; <a href="{{ url('my-account') }}">My Account</a> &nbsp; / &nbsp; <span>My Reviews</span>
            </div>

            <div class="account-layout">
                <aside class="account-sidebar">
                    <div class="account-user-info">
                        <div class="account-avatar">
                            <img src="{{ Auth::user()->profile_picture ? asset('uploads/'.Auth::user()->profile_picture) : asset('images/user-avatar.svg') }}" alt="User Avatar">
                        </div>
                        <h2 class="account-user-name">{{ Auth::user()->name }}</h2>
                        <p class="account-user-email">{{ Auth::user()->email }}</p>
                    </div>

                    <ul class="account-nav">
                        <li class="account-nav-item"><a href="{{ url('my-account') }}" class="account-nav-link"><span>Dashboard</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-orders') }}" class="account-nav-link"><span>My Orders</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-profile') }}" class="account-nav-link"><span>My Profile</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-addresses') }}" class="account-nav-link"><span>Addresses</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('my-reviews') }}" class="account-nav-link active"><span>My Reviews</span></a></li>
                        <li class="account-nav-item"><a href="{{ url('wishlist') }}" class="account-nav-link"><span>Wishlist</span></a></li>
                        <form action="{{ route('logout') }}" method="POST" id="logout-form">@csrf</form>
                        <li class="account-nav-item"><a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" class="account-nav-link logout"><span>Logout</span></a></li>
                    </ul>
                </aside>

                <div class="account-content">
                    <div class="section-header" style="margin-bottom: 30px;">
                        <h1 class="section-title" style="font-size: 24px;">My Reviews</h1>
                    </div>

                    <div class="review-tabs">
                        <div class="review-tab active">Published Reviews ({{ $publishedReviews->count() }})</div>
                        <div class="review-tab">Pending Reviews ({{ $pendingReviews->count() }})</div>
                    </div>

                    <div id="publishedReviews">
                        @forelse($publishedReviews as $review)
                            <div class="review-item-card">
                                @php
                                    $productImage = 'images/pro.png';
                                    if ($review->product->images && is_array($review->product->images) && count($review->product->images) > 0) {
                                        $productImage = 'uploads/' . $review->product->images[0];
                                    } elseif ($review->product->image_path) {
                                        $productImage = 'images/' . $review->product->image_path;
                                    }
                                @endphp
                                <img src="{{ asset($productImage) }}" alt="{{ $review->product->name }}" class="review-product-img">
                                <div class="review-content-side">
                                    <h3 class="review-product-name">{{ $review->product->name }}</h3>
                                    <div class="review-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            {!! $i <= $review->stars ? '&#9733;' : '&#9734;' !!}
                                        @endfor
                                    </div>
                                    <p class="review-text">{{ $review->review }}</p>
                                    <div class="review-footer">
                                        <span>Reviewed on {{ $review->created_at->format('M d, Y') }}</span>
                                        <div class="review-actions">
                                            <button class="review-action-btn" onclick="openEditModal({{ json_encode($review) }})">Edit</button>
                                            <button type="button" class="review-action-btn" style="color: #999;" onclick="confirmDelete({{ $review->id }})">Delete</button>
                                            <form id="delete-form-{{ $review->id }}" action="{{ route('profile.review.delete', $review->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 40px; color: #999;">You haven't published any reviews yet.</div>
                        @endforelse
                    </div>

                    <div id="pendingReviews" style="display: none;">
                        @forelse($pendingReviews as $review)
                            <div class="pending-review-card">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    @php
                                        $productImage = 'images/pro.png';
                                        if ($review->product->images && is_array($review->product->images) && count($review->product->images) > 0) {
                                            $productImage = 'uploads/' . $review->product->images[0];
                                        } elseif ($review->product->image_path) {
                                            $productImage = 'images/' . $review->product->image_path;
                                        }
                                    @endphp
                                    <img src="{{ asset($productImage) }}" alt="" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover;">
                                    <div>
                                        <h4 style="margin-bottom: 4px;">{{ $review->product->name }}</h4>
                                        <p style="font-size: 12px; color: #777;">Added on {{ $review->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <button class="btn-review-now">Review Now</button>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 40px; color: #999;">No pending reviews.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Review Modal (Premium Design) -->
    <div id="editReviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(5px); z-index: 1000; justify-content: center; align-items: center; transition: all 0.3s ease;">
        <div style="background: #fff; padding: 40px; border-radius: 24px; width: 500px; max-width: 90%; box-shadow: 0 20px 40px rgba(0,0,0,0.1); transform: translateY(0); transition: 0.3s ease;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h3 style="font-size: 20px; font-weight: 800; color: #1a1a1a;">Edit Your Review</h3>
                <button onclick="closeEditModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
            </div>
            
            <form id="editReviewForm" method="POST">
                @csrf
                @method('PUT')
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #666; margin-bottom: 10px;">Your Rating</label>
                    <div style="display: flex; gap: 10px; background: #f8f8f8; padding: 10px; border-radius: 12px; border: 1px solid #eee;">
                        <select name="stars" id="editStars" style="width: 100%; border: none; background: transparent; font-weight: 600; color: #333; outline: none; cursor: pointer;">
                            <option value="5">⭐⭐⭐⭐⭐ (5/5)</option>
                            <option value="4">⭐⭐⭐⭐ (4/5)</option>
                            <option value="3">⭐⭐⭐ (3/5)</option>
                            <option value="2">⭐⭐ (2/5)</option>
                            <option value="1">⭐ (1/5)</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-size: 14px; font-weight: 700; color: #666; margin-bottom: 10px;">Detailed Review</label>
                    <textarea name="review" id="editReviewText" rows="6" required 
                        style="width: 100%; border: 1px solid #eee; border-radius: 12px; padding: 15px; font-size: 14px; color: #333; outline: none; transition: 0.3s; resize: none;"
                        onfocus="this.style.borderColor='var(--pink)'"
                        onblur="this.style.borderColor='#eee'"></textarea>
                </div>

                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn-save" style="flex: 1.5; height: 50px; border-radius: 12px; font-weight: 700; transition: 0.3s; box-shadow: 0 4px 15px rgba(234, 4, 126, 0.2);">Save Changes</button>
                    <button type="button" onclick="closeEditModal()" style="flex: 1; height: 50px; background: #f8f8f8; border: 1px solid #eee; border-radius: 12px; color: #666; font-weight: 700; cursor: pointer; transition: 0.3s;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.review-tab');
        const published = document.getElementById('publishedReviews');
        const pending = document.getElementById('pendingReviews');

        tabs.forEach((tab, index) => {
            tab.addEventListener('click', () => {
                tabs.forEach(item => item.classList.remove('active'));
                tab.classList.add('active');
                published.style.display = index === 0 ? 'block' : 'none';
                pending.style.display = index === 1 ? 'block' : 'none';
            });
        });
    });

    function openEditModal(review) {
        const modal = document.getElementById('editReviewModal');
        const form = document.getElementById('editReviewForm');
        
        document.getElementById('editStars').value = review.stars;
        document.getElementById('editReviewText').value = review.review;
        
        let url = "{{ route('profile.review.update', ':id') }}";
        form.action = url.replace(':id', review.id);
        
        modal.style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editReviewModal').style.display = 'none';
    }

    function confirmDelete(reviewId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: 'var(--pink)',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            borderRadius: '15px'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + reviewId).submit();
            }
        })
    }
</script>
@endpush
