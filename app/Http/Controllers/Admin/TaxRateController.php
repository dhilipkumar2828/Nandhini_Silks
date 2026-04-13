<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxRate;
use App\Models\TaxClass;

class TaxRateController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $taxRates = TaxRate::with('taxClass')->latest()->paginate($perPage)->withQueryString();
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
            'tax_class_id' => 'required|exists:tax_classes,id|unique:tax_rates,tax_class_id',
            'name' => 'required|string|max:255|unique:tax_rates,name',
            'country' => 'nullable|string|max:2',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'rate' => 'required|numeric|min:0',
            'priority' => 'required|integer',
            'is_compound' => 'required',
            'applies_to_shipping' => 'required',
            'status' => 'required',
        ], [
            'tax_class_id.unique' => 'This Tax Class already has a tax rate assigned. Each class can only have one rate.'
        ]);

        $data = $request->all();
        $data['name'] = trim($data['name']);

        if (TaxRate::where('name', $data['name'])->exists()) {
            return redirect()->back()->withErrors(['name' => 'This Tax Rate name already exists.'])->withInput();
        }

        // Extra safety check for tax_class_id
        if (TaxRate::where('tax_class_id', $data['tax_class_id'])->exists()) {
            return redirect()->back()->withErrors(['tax_class_id' => 'This Tax Class already has a tax rate assigned.'])->withInput();
        }

        TaxRate::create($data);

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
            'tax_class_id' => 'required|exists:tax_classes,id|unique:tax_rates,tax_class_id,' . $taxRate->id,
            'name' => 'required|string|max:255|unique:tax_rates,name,' . $taxRate->id,
            'country' => 'nullable|string|max:2',
            'state' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'rate' => 'required|numeric|min:0',
            'priority' => 'required|integer',
            'is_compound' => 'required',
            'applies_to_shipping' => 'required',
            'status' => 'required',
        ], [
            'tax_class_id.unique' => 'This Tax Class already has a tax rate assigned. Each class can only have one rate.'
        ]);

        $data = $request->all();
        $data['name'] = trim($data['name']);

        if (TaxRate::where('name', $data['name'])->where('id', '!=', $taxRate->id)->exists()) {
            return redirect()->back()->withErrors(['name' => 'This Tax Rate name already exists.'])->withInput();
        }

        if (TaxRate::where('tax_class_id', $data['tax_class_id'])->where('id', '!=', $taxRate->id)->exists()) {
            return redirect()->back()->withErrors(['tax_class_id' => 'This Tax Class already has a tax rate assigned.'])->withInput();
        }

        $taxRate->update($data);

        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate updated successfully.');
    }

    public function destroy(TaxRate $taxRate)
    {
        $taxRate->delete();
        return redirect()->route('admin.tax-rates.index')->with('success', 'Tax rate deleted successfully.');
    }
}
