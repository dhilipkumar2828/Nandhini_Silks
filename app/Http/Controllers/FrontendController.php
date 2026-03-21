<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

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

    public function category($slug)
    {
        $category = Category::where('slug', '=', $slug)->first();
        if (!$category) {
            if ($slug === 'sarees') {
                return $this->shop();
            }
            abort(404);
        }
        $products = Product::where('category_id', '=', $category->id)->where('status', '=', 1)->paginate(12);
        $filterData = $this->getFilterData();
        
        // Map slug to specific views if needed, or use a default
        $view = 'frontend.' . $slug;
        if (!view()->exists($view)) {
            $view = 'frontend.sarees'; // Fallback
        }
        
        return view($view, compact('category', 'products', 'filterData'));
    }

    public function productShow($slug)
    {
        $product = Product::with('category')->where('slug', '=', $slug)->where('status', '=', 1)->firstOrFail();
        $relatedProducts = Product::where('category_id', '=', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', '=', 1)
            ->limit(4)
            ->get();

        $attributeGroups = $this->buildAttributeGroups($product);
            
        return view('frontend.product-detail', compact('product', 'relatedProducts', 'attributeGroups'));
    }

    public function about() { return view('frontend.about'); }
    public function contact() { return view('frontend.contact'); }
    public function cart() { return view('frontend.cart'); }
    public function checkout() { return view('frontend.checkout'); }
    public function wishlist() { return view('frontend.wishlist'); }
    public function search() { return view('frontend.search'); }
    
    // Policy Pages
    public function privacyPolicy() { return view('frontend.privacy-policy'); }
    public function termsConditions() { return view('frontend.terms-conditions'); }
    public function cancellation() { return view('frontend.cancellation'); }
    public function exchangePolicy() { return view('frontend.exchange-policy'); }
    public function shippingPolicy() { return view('frontend.shipping-policy'); }
    public function fabricCare() { return view('frontend.fabric-care'); }

    // Account Pages (Assuming guest for now as requested)
    public function userLogin() { return view('frontend.login'); }
    public function myAccount() { return view('frontend.my-account'); }
    public function myAddresses() 
    { 
        $addresses = auth()->check() ? auth()->user()->addresses : collect();
        return view('frontend.my-addresses', compact('addresses')); 
    }
    public function myOrders() { return view('frontend.my-orders'); }
    public function myProfile() { return view('frontend.my-profile'); }
    public function myReviews() { return view('frontend.my-reviews'); }
    public function orderDetail() { return view('frontend.order-detail'); }

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
            $selectedIds = array_map('intval', $normalizedAttributes[$attribute->id] ?? []);
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
