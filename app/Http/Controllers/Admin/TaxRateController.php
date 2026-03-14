<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxRate;
use App\Models\TaxClass;

class TaxRateController extends Controller
{
    public function index()
    {
        $taxRates = TaxRate::with('taxClass')->get();
        return view('admin.tax-rates.index', compact('taxRates'));
    }

    public function create()
    {
        $taxClasses = TaxClass::all();
        return view('admin.tax-rates.create', compact('taxClasses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tax_class_id' => 'required|exists:tax_classes,id',
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:2',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'rate' => 'required|numeric|min:0',
            'priority' => 'required|integer',
            'is_compound' => 'required|boolean',
            'applies_to_shipping' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        TaxRate::create($request->all());

        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate created successfully.');
    }

    public function edit(TaxRate $taxRate)
    {
        $taxClasses = TaxClass::all();
        return view('admin.tax-rates.edit', compact('taxRate', 'taxClasses'));
    }

    public function update(Request $request, TaxRate $taxRate)
    {
        $request->validate([
            'tax_class_id' => 'required|exists:tax_classes,id',
            'name' => 'required|string|max:255',
            'country' => 'nullable|string|max:2',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'rate' => 'required|numeric|min:0',
            'priority' => 'required|integer',
            'is_compound' => 'required|boolean',
            'applies_to_shipping' => 'required|boolean',
            'status' => 'required|boolean',
        ]);

        $taxRate->update($request->all());

        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate deleted successfully.');
    }
}
