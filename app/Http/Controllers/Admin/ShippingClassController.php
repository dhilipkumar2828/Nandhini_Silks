<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingClass;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShippingClassController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = ShippingClass::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('is_active', '=', $status);
        }

        $shippingClasses = $query->orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.shipping-classes.index', compact('shippingClasses'));
    }

    public function create()
    {
        return view('admin.shipping-classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        
        ShippingClass::create($data);

        return redirect()->route('admin.shipping-classes.index')->with('success', 'Shipping Class created successfully.');
    }

    public function show(ShippingClass $shippingClass)
    {
        return view('admin.shipping-classes.show', compact('shippingClass'));
    }

    public function edit(ShippingClass $shippingClass)
    {
        return view('admin.shipping-classes.edit', compact('shippingClass'));
    }

    public function update(Request $request, ShippingClass $shippingClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'display_order' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $shippingClass->update($data);

        return redirect()->route('admin.shipping-classes.index')->with('success', 'Shipping Class updated successfully.');
    }

    public function destroy(ShippingClass $shippingClass)
    {
        $shippingClass->delete();
        return redirect()->route('admin.shipping-classes.index')->with('success', 'Shipping Class deleted successfully.');
    }
}
