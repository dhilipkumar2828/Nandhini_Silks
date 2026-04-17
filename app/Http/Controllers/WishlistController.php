<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class WishlistController extends Controller    
{
    public function index()
    {
        $wishlist = session()->get('wishlist', []);
        $products = Product::whereIn('id', $wishlist)->get();
        return view('frontend.wishlist', compact('products'));
    }

    public function add(Request $request, Product $product)
    {
        $wishlist = session()->get('wishlist', []);
        
        if (!in_array($product->id, $wishlist)) {
            $wishlist[] = $product->id;
            session()->put('wishlist', $wishlist);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => count($wishlist),
                'message' => 'Added to wishlist'
            ]);
        }

        return redirect()->back()->with('success', 'Added to wishlist');
    }

    public function remove(Request $request, Product $product)
    {
        $wishlist = session()->get('wishlist', []);
        
        if (($key = array_search($product->id, $wishlist)) !== false) {
            unset($wishlist[$key]);
            session()->put('wishlist', array_values($wishlist));
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => count($wishlist),
                'message' => 'Removed from wishlist'
            ]);
        }

        return redirect()->back()->with('success', 'Removed from wishlist');
    }
}
