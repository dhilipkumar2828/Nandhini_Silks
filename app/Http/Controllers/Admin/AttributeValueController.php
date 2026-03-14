<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Support\Str;

class AttributeValueController extends Controller
{
    public function index(Request $request)
    {
        $attributeId = $request->query('attribute_id');
        $attribute = null;
        
        if ($attributeId) {
            $attribute = Attribute::findOrFail($attributeId);
            $attributeValues = AttributeValue::where('attribute_id', $attributeId)->orderBy('display_order', 'asc')->get();
        } else {
            $attributeValues = AttributeValue::with('attribute')->orderBy('attribute_id')->orderBy('display_order', 'asc')->get();
        }

        return view('admin.attribute-values.index', compact('attributeValues', 'attribute'));
    }

    public function create(Request $request)
    {
        $attributes = Attribute::all();
        $selectedAttributeId = $request->query('attribute_id');
        return view('admin.attribute-values.create', compact('attributes', 'selectedAttributeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'name' => 'required|string|max:255',
            'swatch_value' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        AttributeValue::create($data);

        return redirect()->route('admin.attribute-values.index', ['attribute_id' => $request->attribute_id])
            ->with('success', 'Attribute value created successfully.');
    }

    public function edit(AttributeValue $attributeValue)
    {
        $attributes = Attribute::all();
        return view('admin.attribute-values.edit', compact('attributeValue', 'attributes'));
    }

    public function update(Request $request, AttributeValue $attributeValue)
    {
        $request->validate([
            'attribute_id' => 'required|exists:attributes,id',
            'name' => 'required|string|max:255',
            'swatch_value' => 'nullable|string|max:255',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $attributeValue->update($data);

        return redirect()->route('admin.attribute-values.index', ['attribute_id' => $request->attribute_id])
            ->with('success', 'Attribute value updated successfully.');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeId = $attributeValue->attribute_id;
        $attributeValue->delete();
        return redirect()->route('admin.attribute-values.index', ['attribute_id' => $attributeId])
            ->with('success', 'Attribute value deleted successfully.');
    }
}
