<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::orderBy('id', 'desc')->paginate(20);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::where('status', '=', 1)->orderBy('name')->get();
        $categories = Category::where('status', '=', 1)->orderBy('name')->get();
        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['code'] = strtoupper(trim($data['code']));
        $data['applicable_on'] = $request->input('applicable_on', 'all');
        $data['applicable_products'] = array_values($request->input('applicable_products', []));
        $data['applicable_categories'] = array_values($request->input('applicable_categories', []));
        $data['first_order_only'] = $request->boolean('first_order_only');
        $data['status'] = $request->boolean('status');

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon created successfully.');
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::where('status', '=', 1)->orderBy('name')->get();
        $categories = Category::where('status', '=', 1)->orderBy('name')->get();
        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $data = $this->validateData($request, $coupon->id);
        $data['code'] = strtoupper(trim($data['code']));
        $data['applicable_on'] = $request->input('applicable_on', 'all');
        $data['applicable_products'] = array_values($request->input('applicable_products', []));
        $data['applicable_categories'] = array_values($request->input('applicable_categories', []));
        $data['first_order_only'] = $request->boolean('first_order_only');
        $data['status'] = $request->boolean('status');

        $coupon->update($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted successfully.');
    }

    private function validateData(Request $request, ?int $couponId = null): array
    {
        $uniqueRule = 'unique:coupons,code';
        if ($couponId) {
            $uniqueRule .= ',' . $couponId;
        }

        $isFreeShipping = $request->input('type') === 'free_shipping';

        return $request->validate([
            'code'                   => ['required', 'string', 'max:50', $uniqueRule],
            'type'                   => 'required|in:percentage,fixed,free_shipping',
            'discount_value'         => $isFreeShipping ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'min_order_amount'       => 'nullable|numeric|min:0',
            'max_discount'           => 'nullable|numeric|min:0',
            'applicable_on'          => 'nullable|in:all,category,product',
            'applicable_products'    => 'nullable|array',
            'applicable_categories'  => 'nullable|array',
            'usage_limit'            => 'nullable|integer|min:1',
            'per_user_limit'         => 'nullable|integer|min:1',
            'valid_from'             => 'nullable|date',
            'expires_at'             => 'nullable|date|after_or_equal:valid_from',
            'first_order_only'       => 'nullable|boolean',
            'status'                 => 'nullable|boolean',
        ]);
    }
}
