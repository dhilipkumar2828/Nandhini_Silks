<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

use App\Models\Banner;
use App\Models\Testimonial;

class FrontendController extends Controller
{
    public function index()
    {
        $banners = Banner::where('status', '=', 1, 'and')->orderBy('display_order', 'asc')->get();
        $testimonials = Testimonial::where('status', '=', 1, 'and')->where('display_homepage', '=', true, 'and')->latest()->get();
        $featuredProducts = Product::where('is_featured', '=', true, 'and')->get();
        
        return view('frontend.index', compact('banners', 'testimonials', 'featuredProducts'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', '=', $slug, 'and')->firstOrFail();
        $products = Product::where('category_id', '=', $category->id, 'and')->paginate(12);
        
        // Map slug to specific views if needed, or use a default
        $view = 'frontend.' . $slug;
        if (!view()->exists($view)) {
            $view = 'frontend.sarees'; // Fallback
        }
        
        return view($view, compact('category', 'products'));
    }

    public function productShow($slug)
    {
        $product = Product::with('category')->where('slug', '=', $slug, 'and')->firstOrFail();
        $relatedProducts = Product::where('category_id', '=', $product->category_id, 'and')
            ->where('id', '!=', $product->id, 'and')
            ->limit(4)
            ->get();
            
        return view('frontend.product-detail', compact('product', 'relatedProducts'));
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
    public function myAddresses() { return view('frontend.my-addresses'); }
    public function myOrders() { return view('frontend.my-orders'); }
    public function myProfile() { return view('frontend.my-profile'); }
    public function myReviews() { return view('frontend.my-reviews'); }
    public function orderConfirmation() { return view('frontend.order-confirmation'); }
    public function orderDetail() { return view('frontend.order-detail'); }
}
