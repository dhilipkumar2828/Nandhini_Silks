<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxSetting;

class TaxSettingController extends Controller
{
    public function index()
    {
        $taxSettings = TaxSetting::all();
        return view('admin.tax-settings.index', compact('taxSettings'));
    }

    public function create()
    {
        return view('admin.tax-settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|boolean',
        ]);

        TaxSetting::create($request->all());

        return redirect()->route('admin.tax-settings.index')->with('success', 'Tax setting created successfully.');
    }

    public function edit(TaxSetting $taxSetting)
    {
        return view('admin.tax-settings.edit', compact('taxSetting'));
    }

    public function update(Request $request, TaxSetting $taxSetting)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|boolean',
        ]);

        $taxSetting->update($request->all());

        return redirect()->route('admin.tax-settings.index')->with('success', 'Tax setting updated successfully.');
    }

    public function destroy(TaxSetting $taxSetting)
    {
        $taxSetting->delete();
        return redirect()->route('admin.tax-settings.index')->with('success', 'Tax setting deleted successfully.');
    }
}
