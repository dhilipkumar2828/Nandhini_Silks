<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxClass;

class TaxClassController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $taxClasses = TaxClass::withCount('rates')->latest()->paginate($perPage)->withQueryString();
        return view('admin.tax-classes.index', compact('taxClasses'));
    }

    public function create()
    {
        return view('admin.tax-classes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        TaxClass::create($request->all());

        return redirect()->route('admin.tax-classes.index')->with('success', 'Tax class created successfully.');
    }

    public function edit(TaxClass $taxClass)
    {
        return view('admin.tax-classes.edit', compact('taxClass'));
    }

    public function update(Request $request, TaxClass $taxClass)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $taxClass->update($request->all());

        return redirect()->route('admin.tax-classes.index')->with('success', 'Tax class updated successfully.');
    }

    public function destroy(TaxClass $taxClass)
    {
        $taxClass->delete();
        return redirect()->route('admin.tax-classes.index')->with('success', 'Tax class deleted successfully.');
    }
}
