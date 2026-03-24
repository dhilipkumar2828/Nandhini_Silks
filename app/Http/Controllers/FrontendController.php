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
use App\Models\Order;
use App\Models\ProductReview;

use App\Models\Banner;
use App\Models\Testimonial;

class FrontendController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', '=', 1)->orderBy('display_order', 'asc')->get();
        $testimonials = Testimonial::where('status', '=', 1)->where('display_homepage', '=', true)->latest()->get();
        $featuredProducts = Product::where('is_featured', '=', true)->where('status', '=', 1)->get();
        
        // Fetch categories for "Browse Our Categories"
        $categories = Category::where('status', '=', 1)->orderBy('display_order', 'asc')->get();
        
        // Fetch subcategories for "Saree Collections" (Top 5 for homepage)
        $subCategories = \App\Models\SubCategory::where('status', '=', 1)->orderBy('display_order', 'asc')->limit(8)->get();

        return view('frontend.index', compact('banners', 'testimonials', 'featuredProducts', 'categories', 'subCategories'));
    }

    public function shop()
    {
        $products = Product::where('status', '=', 1)->paginate(12);
        $category = new Category(['name' => 'Shop']);
        $filterData = $this->getFilterData();

        return view('frontend.sarees', compact('category', 'products', 'filterData'));
    }

    public function category($slug, $sub_slug = null, $child_slug = null)
    {
        $actualSlug = $child_slug ?: ($sub_slug ?: $slug);
        $category = Category::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
        $query = Product::where('status', '=', 1);

        if ($category) {
            $query->where('category_id', '=', $category->id);
            $subCategories = SubCategory::where('category_id', '=', $category->id)->where('status', '=', 1)->get();
            $category->setRelation('subCategories', $subCategories);
        } else {
            // Try SubCategory
            $subCategory = SubCategory::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
            if ($subCategory) {
                $category = $subCategory;
                $query->where('sub_category_id', '=', $subCategory->id);
            } else {
                // Try ChildCategory
                $childCategory = ChildCategory::where('slug', '=', $actualSlug)->where('status', '=', 1)->first();
                if ($childCategory) {
                    $category = $childCategory;
                    $query->where('child_category_id', '=', $childCategory->id);
                } else {
                    if ($slug === 'sarees') return $this->shop();
                    abort(404);
                }
            }
        }

        // Apply Filters
        if (request('min_price')) $query->where('price', '>=', request('min_price'));
        if (request('max_price')) $query->where('price', '<=', request('max_price'));
        if (request('categories')) $query->whereIn('category_id', request('categories'));
        if (request('attr')) {
            foreach (request('attr') as $attr_id => $values) {
                $query->where(function($q) use ($attr_id, $values) {
                    foreach ($values as $val_id) {
                        $q->orWhereJsonContains('attributes', ['value_id' => (string)$val_id])
                          ->orWhereJsonContains('attributes', ['value_id' => (int)$val_id]);
                    }
                });
            }
        }
        if (request('in_stock')) $query->where('stock_quantity', '>', 0);

        // Sorting
        $sort = request('sort', 'popularity');
        if ($sort == 'price_low') $query->orderBy('price', 'asc');
        elseif ($sort == 'price_high') $query->orderBy('price', 'desc');
        elseif ($sort == 'newest') $query->orderBy('created_at', 'desc');
        else $query->orderBy('id', 'desc');

        $products = $query->paginate(12)->appends(request()->query());
        $filterData = $this->getFilterData();
        
        $view = 'frontend.category_listing';
        return view($view, compact('category', 'products', 'filterData'));
    }

    public function productShow($slug)
    {
        $product = Product::with(['category', 'product_variants'])->where('slug', '=', $slug)->where('status', '=', 1)->firstOrFail();
        
        // Handle Recently Viewed
        $viewedIds = session()->get('recently_viewed', []);
        if (($key = array_search($product->id, $viewedIds)) !== false) unset($viewedIds[$key]);
        array_unshift($viewedIds, $product->id);
        $viewedIds = array_slice($viewedIds, 0, 10);
        session()->put('recently_viewed', $viewedIds);
        
        $recentlyViewed = Product::whereIn('id', array_diff($viewedIds, [$product->id]))
            ->where('status', 1)->limit(4)->get();

        $relatedProducts = Product::where('category_id', '=', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', '=', 1)
            ->limit(4)
            ->get();

        $attributeGroups = $this->buildAttributeGroups($product);
        $inWishlist = in_array($product->id, session()->get('wishlist', []));
            
        return view('frontend.product-detail', compact('product', 'relatedProducts', 'recentlyViewed', 'attributeGroups', 'inWishlist'));
    }

    public function about() { return view('frontend.about'); }
    public function contact() { return view('frontend.contact'); }
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
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $orderCount = $user->orders()->count();
        $wishlistCount = count(session('wishlist', []));
        $addressCount = $user->addresses()->count();
        $recentOrders = $user->orders()->latest()->limit(5)->get();

        return view('frontend.my-account', compact('orderCount', 'wishlistCount', 'addressCount', 'recentOrders')); 
    }
    public function myAddresses() 
    { 
        $addresses = auth()->check() ? auth()->user()->addresses : collect();
        return view('frontend.my-addresses', compact('addresses')); 
    }
    public function myOrders() { return view('frontend.my-orders'); }
    public function myProfile() 
    { 
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        return view('frontend.my-profile', compact('user')); 
    }
    public function myReviews() 
    { 
        if (!auth()->check()) {
            return redirect()->route('login');
        }
        $user = auth()->user();
        $publishedReviews = \App\Models\ProductReview::with('product')
            ->where('user_id', $user->id)
            ->where('status', 1)
            ->latest()
            ->get();
        $pendingReviews = \App\Models\ProductReview::with('product')
            ->where('user_id', $user->id)
            ->where('status', 0)
            ->latest()
            ->get();

        return view('frontend.my-reviews', compact('publishedReviews', 'pendingReviews')); 
    }
    public function orderDetail(Request $request)
    {
        $order = Order::with(['items', 'coupon'])
            ->where('id', $request->query('id'))
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return redirect()->route('my-orders')->with('error', 'Order not found.');
        }

        return view('frontend.order-detail', compact('order'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|string|in:Male,Female,Other',
            'dob' => 'nullable|date',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        if ($request->new_password) {
            if (!\Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password does not match.']);
            }
            $user->password = \Hash::make($request->new_password);
        }

        $user->name = $data['name'];
        $user->phone = $data['phone'];
        $user->gender = $data['gender'];
        $user->dob = $data['dob'];
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updateProfilePhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = auth()->user();

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
        $review->update($data);
        return back()->with('success', 'Review updated successfully.');
    }

    private function getFilterData(): array
    {
        return [
            'categories' => Category::where('status', '=', 1)->orderBy('display_order', 'asc')->get(),
            'attributes' => Attribute::with(['values' => function($q) {
                $q->where('status', '=', 1)->orderBy('display_order', 'asc');
            }])->where('status', '=', 1)->orderBy('group')->get(),
            'max_price' => Product::where('status', '=', 1)->max('price') ?? 50000,
            'min_price' => Product::where('status', '=', 1)->min('price') ?? 0,
        ];
    }

    private function buildAttributeGroups(Product $product): array
    {
        $productAttributes = $product->getAttribute('attributes') ?? [];
        if (is_string($productAttributes)) {
            $decoded = json_decode($productAttributes, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $productAttributes = $decoded;
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
