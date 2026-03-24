<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use App\Models\ShippingClass;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = ShippingRate::with('shippingClass');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('country', 'like', "%{$term}%")
                  ->orWhere('state', 'like', "%{$term}%")
                  ->orWhere('city', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('status', '=', $status);
        }

        $shippingRates = $query->orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.shipping-rates.index', compact('shippingRates'));
    }

    public function create()
    {
        $shippingClasses = ShippingClass::where('status', 1)->get();
        return view('admin.shipping-rates.create', compact('shippingClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_class_id' => 'required|exists:shipping_classes,id',
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        ShippingRate::create($request->all());

        return redirect()->route('admin.shipping-rates.index')->with('success', 'Shipping Rate created successfully.');
    }

    public function show(ShippingRate $shippingRate)
    {
        return view('admin.shipping-rates.show', compact('shippingRate'));
    }

    public function edit(ShippingRate $shippingRate)
    {
        $shippingClasses = ShippingClass::where('status', 1)->get();
        return view('admin.shipping-rates.edit', compact('shippingRate', 'shippingClasses'));
    }

    public function update(Request $request, ShippingRate $shippingRate)
    {
        $request->validate([
            'shipping_class_id' => 'required|exists:shipping_classes,id',
            'name' => 'required|string|max:255',
            'cost' => 'required|numeric|min:0',
            'status' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $shippingRate->update($request->all());

        return redirect()->route('admin.shipping-rates.index')->with('success', 'Shipping Rate updated successfully.');
    }

    public function destroy(ShippingRate $shippingRate)
    {
        $shippingRate->delete();
        return redirect()->route('admin.shipping-rates.index')->with('success', 'Shipping Rate deleted successfully.');
    }
}
