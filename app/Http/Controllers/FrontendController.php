<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\Order;
use App\Models\ProductReview;

use App\Models\Banner;
use App\Models\Testimonial;
use App\Models\Ad;
use App\Models\OfferCollection;
use App\Mail\ReturnRequestAdminMail;
use Illuminate\Support\Facades\Mail;

use App\Services\ShiprocketService;

class FrontendController extends Controller
{
    public function trackOrder(Request $request, ShiprocketService $shiprocket)
    {
        $orderId = $request->get('id');
        
        if (!$orderId) {
            return redirect()->route('home')->with('error', 'Please enter a valid tracking ID or Order Number.');
        }

        // Find the order by number or shipment id
        $order = Order::where('order_number', $orderId)
            ->orWhere('shiprocket_shipment_id', $orderId)
            ->orWhere('tracking_number', $orderId)
            ->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found. Please check your Order Number.');
        }

        $trackingData = null;
        if ($order->shiprocket_shipment_id) {
            $trackingData = $shiprocket->trackOrder($order->shiprocket_shipment_id);
            
            // Save tracking activities to DB if available
            if (isset($trackingData['tracking_data']['shipment_track_activities'])) {
                $order->update([
                    'shipment_track_activities' => $trackingData['tracking_data']['shipment_track_activities']
                ]);
            }
        }

        return view('frontend.track-order-result', compact('order', 'trackingData'));
    }

    public function index()
    {
        $banners = Banner::where('status', '=', 1, 'and')->orderBy('display_order', 'asc')->get();
        $testimonials = Testimonial::where('status', '=', 1)->where('display_homepage', '=', true)->latest()->limit(6)->get();
        $featuredProducts = Product::where('is_featured', '=', true, 'and')->where('status', '=', 1, 'and')->latest()->limit(8)->get();
        $offerProducts = $featuredProducts->isNotEmpty()
            ? $featuredProducts
            : Product::where('status', '=', 1)->latest()->limit(8)->get();
        
        // Fetch categories for "Browse Our Categories"
        $categories = Category::with('subCategories')->where('status', '=', 1)->orderBy('display_order', 'asc')->get();
        
        // Fetch subcategories for "Saree Collections" (Top 5 for homepage)
        $subCategories = \App\Models\SubCategory::where('status', '=', 1)->orderBy('display_order', 'asc')->limit(8)->get();

        // Fetch advertisements for promo section
        $ads = Ad::where('status', '=', 1)->latest()->get();

        // Fetch Offer Collections for homepage sections
        $offerCollections = OfferCollection::with(['products' => function($q) {
            $q->where('status', 1)->where('show_offer_on_homepage', 1)->latest()->limit(12);
        }])
        ->where('status', 'active')
        ->whereHas('products', function($q) {
            $q->where('status', 1)->where('show_offer_on_homepage', 1);
        })
        ->get();

        return view('frontend.index', compact('banners', 'testimonials', 'featuredProducts', 'categories', 'subCategories', 'ads', 'offerCollections'));
    }

    public function shop()
    {
        $query = Product::where('status', '=', 1);

        // Filter by categories if selected (Multi-select)
        if (request('categories')) {
            $query->whereIn('category_id', (array)request('categories'));
        }

        // Apply shared sidebar filters
        if (request('min_price') !== null && request('min_price') !== '') {
            $query->where('price', '>=', (float) request('min_price'));
        }
        if (request('max_price') !== null && request('max_price') !== '') {
            $query->where('price', '<=', (float) request('max_price'));
        }

        if (request('attr')) {
            foreach (request('attr') as $attr_id => $values) {
                $query->where(function($q) use ($attr_id, $values) {
                    foreach ($values as $val_id) {
                        $q->orWhereJsonContains('attributes->' . $attr_id, (int) $val_id)
                          ->orWhereJsonContains('attributes->' . $attr_id, (string) $val_id);
                    }
                });
            }
        }

        if (request('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sort = request('sort', 'popularity');
        if ($sort == 'price_low')  $query->orderBy('price', 'asc');
        elseif ($sort == 'price_high') $query->orderBy('price', 'desc');
        elseif ($sort == 'newest')     $query->orderBy('created_at', 'desc');
        else                           $query->orderBy('id', 'desc');

        $products = $query->paginate(12)->appends(request()->query());
        $category = new Category(['name' => 'Shop']);
        $filterData = $this->getFilterData();

        return view('frontend.category_listing', compact('category', 'products', 'filterData'));
    }

    public function category($slug, $sub_slug = null, $child_slug = null)
    {
        $actualSlug = $child_slug ?: ($sub_slug ?: $slug);

        // Determine what type of page we are on and build the base query
        $browsingType = null; // 'category', 'sub_category', 'child_category'
        $browsingId   = null;
        $parentCategoryId = null;

        $category = Category::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
        $query = Product::where('status', '=', 1);

        if ($category) {
            $browsingType = 'category';
            $browsingId   = $category->id;
            $query->where('category_id', '=', $category->id);
            $subCategories = SubCategory::where('category_id', '=', $category->id)->where('status', '=', 1)->get();
            $category->setRelation('subCategories', $subCategories);
        } else {
            // Try SubCategory
            $subCategory = SubCategory::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
            if ($subCategory) {
                $browsingType = 'sub_category';
                $browsingId   = $subCategory->id;
                $parentCategoryId = $subCategory->category_id;
                $category = $subCategory;
                $query->where('sub_category_id', '=', $subCategory->id);
            } else {
                // Try ChildCategory
                $childCategory = ChildCategory::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
                if ($childCategory) {
                    $browsingType = 'child_category';
                    $browsingId   = $childCategory->id;
                    $parentCategoryId = $childCategory->sub_category_id ?? null;
                    $category = $childCategory;
                    $query->where('child_category_id', '=', $childCategory->id);
                } else {
                    return redirect()->route('home');
                }
            }
        }

        // -------------------------------------------------------
        // Apply Sidebar Filters
        // -------------------------------------------------------

        // Price range
        if (request('min_price') !== null && request('min_price') !== '') {
            $query->where('price', '>=', (float) request('min_price'));
        }
        if (request('max_price') !== null && request('max_price') !== '') {
            $query->where('price', '<=', (float) request('max_price'));
        }

        // Sub-category filter (shown in sidebar when browsing a top-level category)
        if (request('sub_categories') && $browsingType === 'category') {
            $query->whereIn('sub_category_id', request('sub_categories'));
        }

        // Attribute filters
        if (request('attr')) {
            foreach (request('attr') as $attr_id => $values) {
                $query->where(function($q) use ($attr_id, $values) {
                    foreach ($values as $val_id) {
                        $q->orWhereJsonContains('attributes->' . $attr_id, (int) $val_id)
                          ->orWhereJsonContains('attributes->' . $attr_id, (string) $val_id);
                    }
                });
            }
        }

        // In-stock filter
        if (request('in_stock')) {
            $query->where('stock_quantity', '>', 0);
        }

        // Sorting
        $sort = request('sort', 'popularity');
        if ($sort == 'price_low')  $query->orderBy('price', 'asc');
        elseif ($sort == 'price_high') $query->orderBy('price', 'desc');
        elseif ($sort == 'newest')     $query->orderBy('created_at', 'desc');
        else                           $query->orderBy('id', 'desc');

        $products = $query->paginate(12)->appends(request()->query());

        // Build filter data scoped to the current category context
        $filterData = $this->getFilterData($browsingType, $browsingId);

        return view('frontend.category_listing', compact('category', 'products', 'filterData', 'browsingType'));
    }

    public function productShow($slug)
    {
        $product = Product::with(['category', 'product_variants'])->where('slug', '=', $slug)->where('status', '=', 1)->first();
        if (!$product) return redirect()->route('home');
        
        // Handle Recently Viewed
        $viewedIds = session()->get('recently_viewed', []);
        if (($key = array_search($product->id, $viewedIds)) !== false) unset($viewedIds[$key]);
        array_unshift($viewedIds, $product->id);
        $viewedIds = array_slice($viewedIds, 0, 10);
        session()->put('recently_viewed', $viewedIds);
        
        $recentlyViewed = Product::whereIn('id', array_diff($viewedIds, [$product->id]))
            ->where('status', 1)->limit(4)->get();

        $relatedProductIds = $product->related_products;
        if (!empty($relatedProductIds) && is_array($relatedProductIds)) {
            $relatedProducts = Product::whereIn('id', $relatedProductIds)
                ->where('status', '=', 1)
                ->get();
        } else {
            $relatedProducts = Product::where('category_id', '=', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('status', '=', 1)
                ->limit(4)
                ->get();
        }

        $attributeGroups = $this->buildAttributeGroups($product);
        $inWishlist = in_array($product->id, session()->get('wishlist', []));
        $userReview = Auth::guard('web')->check()
            ? ProductReview::where('product_id', $product->id)->where('user_id', Auth::guard('web')->id())->latest()->first()
            : null;

        $hasPurchased = false;
        if (Auth::guard('web')->check()) {
            $hasPurchased = Order::where('user_id', Auth::guard('web')->id())
                ->whereNotIn('order_status', ['cancelled', 'failed'])
                ->whereHas('items', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->exists();

            // Allow editing if a review already exists
            if (!$hasPurchased && $userReview) {
                $hasPurchased = true;
            }
        }

        // Correctly check if product is in cart (handles both session and DB)
        $inCart = false;
        $cartVariantIds = [];
        $cartVariantQuantities = [];
        /** @var \App\Models\Product $product */
        $productId = $product->id;
        if (Auth::guard('web')->check()) {
            $cartItems = \App\Models\CartItem::where('user_id', Auth::guard('web')->id())
                ->where('product_id', $productId)
                ->get();
            
            foreach ($cartItems as $cItem) {
                if ($cItem->product_variant_id) {
                    $cartVariantIds[] = $cItem->product_variant_id;
                    $cartVariantQuantities[$cItem->product_variant_id] = $cItem->quantity;
                } else {
                    $cartVariantQuantities['base'] = $cItem->quantity;
                }
            }
            $inCart = $cartItems->isNotEmpty();
        } else {
            $cart = session()->get('cart', []);
            $cartItems = collect($cart)->where('product_id', $productId);
            
            foreach ($cartItems as $cItem) {
                if (isset($cItem['variant_id']) && $cItem['variant_id']) {
                    $cartVariantIds[] = $cItem['variant_id'];
                    $cartVariantQuantities[$cItem['variant_id']] = $cItem['quantity'];
                } else {
                    $cartVariantQuantities['base'] = $cItem['quantity'];
                }
            }
            $inCart = $cartItems->isNotEmpty();
        }

        return view('frontend.product-detail', compact('product', 'relatedProducts', 'recentlyViewed', 'attributeGroups', 'inWishlist', 'inCart', 'cartVariantIds', 'cartVariantQuantities', 'userReview', 'hasPurchased'));
    }

    public function about() { return view('frontend.about'); }
    public function contact() { return view('frontend.contact'); }
    public function contactSubmit(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => 'required|email|max:255',
            'message' => 'required|string'
        ], [
            'name.regex' => 'The name field must only contain alphabets.'
        ]);

        $inquiry = \App\Models\Inquiry::create([
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        $adminEmail = \App\Models\Setting::getAdminEmail();

        // 1. Send Email to Admin (Swapped to send first)
        try {
            \Illuminate\Support\Facades\Log::info("Sending admin inquiry email to: " . $adminEmail);
            \Illuminate\Support\Facades\Mail::to($adminEmail)->send(new \App\Mail\InquiryAdminMail($inquiry));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Inquiry Admin Email Error: ' . $e->getMessage());
        }

        // Small delay to avoid "Too many emails per second" (550 error)
        sleep(2);

        // 2. Send Email to Customer (with BCC to admin as fallback)
        try {
            \Illuminate\Support\Facades\Log::info("Sending customer inquiry email to: " . $request->email);
            \Illuminate\Support\Facades\Mail::to($request->email)
                ->bcc($adminEmail)
                ->send(new \App\Mail\InquiryCustomerMail($inquiry));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Inquiry Customer Email Error: ' . $e->getMessage());
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you for contacting us! We will get back to you soon.'
            ]);
        }

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
    public function cart() { return view('frontend.cart'); }
    public function checkout() { return view('frontend.checkout'); }
    public function wishlist() { return view('frontend.wishlist'); }
    // public function search() { return view('frontend.search'); }
    
    // Policy Pages
    public function privacyPolicy() { return view('frontend.privacy-policy'); }
    public function termsConditions() { return view('frontend.terms-conditions'); }
    public function cancellation() { return view('frontend.cancellation'); }
    public function exchangePolicy() { return view('frontend.exchange-policy'); }
    public function shippingPolicy() { return view('frontend.shipping-policy'); }
    public function fabricCare() { return view('frontend.fabric-care'); }

    // Account Pages (Assuming guest for now as requested)
    public function userLogin() { return view('frontend.login'); }
    public function myAccount() 
    { 
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }

        $user = Auth::guard('web')->user();
        $orderCount = $user->orders()->count();
        $wishlistCount = count(session('wishlist', []));
        $addressCount = $user->addresses()->count();
        $recentOrders = $user->orders()->latest()->paginate(3);

        return view('frontend.my-account', compact('orderCount', 'wishlistCount', 'addressCount', 'recentOrders')); 
    }
    public function myAddresses() 
    { 
        $addresses = Auth::guard('web')->check() ? Auth::guard('web')->user()->addresses : collect();
        return view('frontend.my-addresses', compact('addresses')); 
    }
    public function myOrders() 
    { 
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard('web')->user();
        $orders = $user->orders()->latest()->paginate(3);
        return view('frontend.my-orders', compact('orders')); 
    }
    public function myProfile() 
    { 
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard('web')->user();
        return view('frontend.my-profile', compact('user')); 
    }
    public function myReviews() 
    { 
        if (!Auth::guard('web')->check()) {
            return redirect()->route('login');
        }
        $user = Auth::guard('web')->user();
        $publishedReviews = \App\Models\ProductReview::with('product')
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->latest()
            ->paginate(3, ['*'], 'published_page');
        $pendingReviews = \App\Models\ProductReview::with('product')
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->latest()
            ->paginate(3, ['*'], 'pending_page');

        return view('frontend.my-reviews', compact('publishedReviews', 'pendingReviews')); 
    }
    public function orderDetail(Request $request)
    {
        $order = Order::with(['items', 'coupon'])
            ->where('id', $request->query('id'))
            ->where('user_id', Auth::guard('web')->id())
            ->first();

        if (!$order) {
            return redirect()->route('my-orders')->with('error', 'Order not found.');
        }

        return view('frontend.order-detail', compact('order'));
    }

    public function returnRequest(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', Auth::guard('web')->id())
            ->firstOrFail();

        // Only allow returns for delivered orders
        if ($order->order_status !== 'delivered') {
            return back()->with('error', 'Only delivered orders can be returned.');
        }

        // Check if already requested
        if ($order->return_status) {
            return back()->with('error', 'Return request already exists for this order.');
        }

        $order->update([
            'return_status' => 'requested',
            'return_reason' => $request->reason,
        ]);

        // Send Email to Admin
        try {
            $adminEmail = \App\Models\Setting::getAdminEmail();
            Mail::to($adminEmail)->send(new ReturnRequestAdminMail($order));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Return Request Admin Email Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Your return request has been submitted successfully. Our team will review it shortly.');
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::guard('web')->user();
        $data = $request->validate([
            'name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed|required_with:current_password',
        ]);

        if ($request->filled('new_password') || $request->filled('current_password')) {
            if (!Hash::check((string) $request->current_password, (string) $user->password)) {
                if ($request->ajax()) {
                    return response()->json([
                        'message' => 'Validation failed.',
                        'errors' => [
                            'current_password' => ['Current password does not match.'],
                        ],
                    ], 422);
                }

                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->gender = $data['gender'] ?? null;
        $user->dob = $data['dob'] ?? null;
        $user->save();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully.',
            ]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::guard('web')->user();

        if ($request->hasFile('photo')) {
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            
            // Delete old photo
            if ($user->profile_picture && file_exists(public_path('uploads/' . $user->profile_picture))) {
                @unlink(public_path('uploads/' . $user->profile_picture));
            }

            $image->move(public_path('uploads'), $imageName);
            $user->profile_picture = $imageName;
            $user->save();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile picture updated.',
                    'url' => asset('uploads/' . $imageName)
                ]);
            }
        }

        return back()->with('success', 'Profile picture updated.');
    }

    public function requestEmailChangeOtp(Request $request)
    {
        $user = Auth::guard('web')->user();

        $data = $request->validate([
            'new_email' => 'required|email|max:255|unique:users,email',
        ]);

        if (strcasecmp($data['new_email'], $user->email) === 0) {
            return back()->withErrors(['new_email' => 'New email must be different from your current email.']);
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $user->otp = $otp;
        $user->otp_expires_at = now()->addMinutes(10);
        $user->save();

        try {
            \Illuminate\Support\Facades\Mail::to($data['new_email'])->send(new \App\Mail\VerficationOTP($otp));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Email Change OTP Failure: ' . $e->getMessage());
            return back()->withErrors(['new_email' => 'Unable to send OTP now. Please try again.']);
        }

        $request->session()->put('pending_email_change_user_id', $user->id);
        $request->session()->put('pending_new_email', $data['new_email']);

        return back()
            ->with('success', 'OTP sent to your new email address.')
            ->with('email_change_otp_sent', true);
    }

    public function verifyEmailChangeOtp(Request $request)
    {
        $request->validate([
            'email_change_otp' => 'required|string|size:6',
        ]);

        $user = Auth::guard('web')->user();
        $pendingUserId = $request->session()->get('pending_email_change_user_id');
        $pendingNewEmail = $request->session()->get('pending_new_email');

        if (!$pendingUserId || !$pendingNewEmail || (int)$pendingUserId !== (int)$user->id) {
            return back()->withErrors(['email_change_otp' => 'Email change session expired. Please request OTP again.']);
        }

        if ($user->otp !== $request->email_change_otp || !$user->otp_expires_at || $user->otp_expires_at->isPast()) {
            return back()
                ->withErrors(['email_change_otp' => 'Invalid or expired OTP.'])
                ->with('email_change_otp_sent', true);
        }

        $user->email = $pendingNewEmail;
        $user->email_verified_at = now();
        $user->is_verified = true;
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        $request->session()->forget(['pending_email_change_user_id', 'pending_new_email']);

        return back()->with('success', 'Email updated and verified successfully.');
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('status', '=', 1)
            ->where(function($q) use ($query) {
                $q->where('name', 'like', "%$query%")
                  ->orWhere('description', 'like', "%$query%")
                  ->orWhere('sku', 'like', "%$query%");
            })
            ->paginate(12);

        $filterData = $this->getFilterData();

        return view('frontend.search', array_merge([
            'products' => $products,
            'searchQuery' => $query
        ], $filterData));
    }

    public function deleteReview($id)
    {
        $review = \App\Models\ProductReview::where('id', '=', $id)->where('user_id', '=', Auth::id())->firstOrFail();
        $review->delete();
        return back()->with('success', 'Review deleted successfully.');
    }

    public function updateReview(Request $request, $id)
    {
        $data = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10',
        ]);

        $review = \App\Models\ProductReview::where('id', '=', $id)->where('user_id', '=', Auth::id())->firstOrFail();
        $data['status'] = 0;
        $review->update($data);
        return back()->with('success', 'Review updated successfully.');
    }

    public function storeReview(Request $request, Product $product)
    {
        if (!Auth::guard('web')->check()) {
            return back()->with('error', 'Please login to submit a review.');
        }

        $hasPurchased = Order::where('user_id', Auth::guard('web')->id())
            ->whereNotIn('order_status', ['cancelled', 'failed'])
            ->whereHas('items', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->exists();

        $existingReview = ProductReview::where('product_id', $product->id)->where('user_id', Auth::guard('web')->id())->exists();

        if (!$hasPurchased && !$existingReview) {
            return back()->with('error', 'You can only review products you have purchased.');
        }

        $data = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10',
        ]);

        ProductReview::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'product_id' => $product->id,
            ],
            [
                'stars' => $data['stars'],
                'review' => $data['review'],
                'status' => 0,
            ]
        );

        return back()->with('success', 'Your review has been submitted.');
    }

    private function getFilterData(string $browsingType = null, int $browsingId = null): array
    {
        // Scope price range to the current category context
        $priceQuery = Product::where('status', '=', 1);
        $subCategories = collect();

        if ($browsingType === 'category' && $browsingId) {
            $priceQuery->where('category_id', $browsingId);
            // Provide sub-categories of the browsed category for the sidebar
            $subCategories = SubCategory::where('category_id', $browsingId)
                ->where('status', 1)
                ->orderBy('display_order', 'asc')
                ->get();
        } elseif ($browsingType === 'sub_category' && $browsingId) {
            $priceQuery->where('sub_category_id', $browsingId);
        } elseif ($browsingType === 'child_category' && $browsingId) {
            $priceQuery->where('child_category_id', $browsingId);
        }

        return [
            'categories'     => Category::where('status', '=', 1)->orderBy('display_order', 'asc')->get(),
            'sub_categories' => $subCategories,
            'attributes'     => Attribute::with(['values' => function($q) {
                $q->where('status', '=', 1)->orderBy('display_order', 'asc');
            }])->where('status', '=', 1)->orderBy('group')->get(),
            'max_price' => (clone $priceQuery)->max('price') ?? 50000,
            'min_price' => (clone $priceQuery)->min('price') ?? 0,
        ];
    }

    private function buildAttributeGroups(Product $product): array
    {
        $productAttributes = $product->attributes ?? [];
        
        // If attributes JSON is empty, try to derive from variants
        if (empty($productAttributes) && $product->product_variants->count() > 0) {
            foreach ($product->product_variants as $v) {
                if ($v->combination) {
                    foreach ($v->combination as $attrId => $valIds) {
                        foreach ((array)$valIds as $id) {
                            $productAttributes[$attrId][] = (int)$id;
                        }
                    }
                }
            }
            // Deduplicate
            foreach ($productAttributes as $attrId => $ids) {
                $productAttributes[$attrId] = array_values(array_unique($ids));
            }
        }

        if (empty($productAttributes) || !is_array($productAttributes)) {
            return [];
        }

        if (array_is_list($productAttributes)) {
            $valueIds = array_values(array_unique(array_filter(array_map('intval', $productAttributes))));
            if (empty($valueIds)) {
                return [];
            }

            $values = AttributeValue::with('attribute')
                ->whereIn('id', $valueIds)
                ->where('status', '=', 1)
                ->orderBy('display_order', 'asc')
                ->get();

            if ($values->isEmpty()) {
                return [];
            }

            $groups = [];
            $grouped = $values->groupBy('attribute_id');
            foreach ($grouped as $valueGroup) {
                $attribute = $valueGroup->first()->attribute;
                if (!$attribute || $attribute->status != 1) {
                    continue;
                }
                $groups[] = [
                    'attribute' => $attribute,
                    'values' => $valueGroup->sortBy('display_order')->values(),
                ];
            }

            usort($groups, function ($a, $b) {
                $groupCompare = strcmp($a['attribute']->group ?? '', $b['attribute']->group ?? '');
                if ($groupCompare !== 0) {
                    return $groupCompare;
                }
                return strcmp($a['attribute']->name ?? '', $b['attribute']->name ?? '');
            });

            return $groups;
        }

        $normalizedAttributes = [];
        foreach ($productAttributes as $attributeId => $values) {
            if (is_array($values)) {
                $normalizedAttributes[$attributeId] = $values;
            } elseif ($values !== null && $values !== '') {
                $normalizedAttributes[$attributeId] = [$values];
            }
        }

        if (empty($normalizedAttributes)) {
            return [];
        }

        $attributeIds = array_map('intval', array_keys($normalizedAttributes));
        $valueIds = [];
        foreach ($normalizedAttributes as $values) {
            foreach ((array) $values as $valueId) {
                $valueIds[] = (int) $valueId;
            }
        }

        $valueIds = array_values(array_unique(array_filter($valueIds)));
        if (empty($attributeIds) || empty($valueIds)) {
            return [];
        }

        $attributes = Attribute::with(['values' => function ($query) use ($valueIds) {
            $query->whereIn('id', $valueIds)->where('status', '=', 1)->orderBy('display_order', 'asc');
        }])->whereIn('id', $attributeIds)->where('status', '=', 1)->orderBy('group')->orderBy('name')->get();

        $groups = [];
        foreach ($attributes as $attribute) {
            $selectedIds = array_map('intval', $normalizedAttributes[$attribute->id] ?? $normalizedAttributes[(string)$attribute->id] ?? []);
            $values = $attribute->values->filter(function ($value) use ($selectedIds) {
                return in_array($value->id, $selectedIds, true);
            });
            if ($values->count() > 0) {
                $groups[] = [
                    'attribute' => $attribute,
                    'values' => $values,
                ];
            }
        }

        return $groups;
    }
}
