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
        $perPage = $request->get('per_page', 10);
        $attribute = null;
        
        $query = AttributeValue::query();

        if ($attributeId) {
            $attribute = Attribute::findOrFail($attributeId);
            $query->where('attribute_id', $attributeId);
        } else {
            $query->with('attribute');
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('status', '=', $status);
        }

        if (!$attributeId) {
            $query->orderBy('attribute_id');
        }

        $attributeValues = $query->orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();

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
            'swatch_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['swatch_value'] = $request->input('swatch_value');

        if ($request->hasFile('swatch_image')) {
            $data['swatch_value'] = $this->storeSwatchImage($request->file('swatch_image'));
        }

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
            'swatch_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'display_order' => 'required|integer',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $data['swatch_value'] = $request->input('swatch_value');

        if ($request->hasFile('swatch_image')) {
            if ($attributeValue->swatch_value && !$this->isHexColor($attributeValue->swatch_value)) {
                $oldPath = public_path('uploads/' . $attributeValue->swatch_value);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }
            $data['swatch_value'] = $this->storeSwatchImage($request->file('swatch_image'));
        }

        $attributeValue->update($data);

        return redirect()->route('admin.attribute-values.index', ['attribute_id' => $request->attribute_id])
            ->with('success', 'Attribute value updated successfully.');
    }

    public function destroy(AttributeValue $attributeValue)
    {
        $attributeId = $attributeValue->attribute_id;
        if ($attributeValue->swatch_value && !$this->isHexColor($attributeValue->swatch_value)) {
            $oldPath = public_path('uploads/' . $attributeValue->swatch_value);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }
        $attributeValue->delete();
        return redirect()->route('admin.attribute-values.index', ['attribute_id' => $attributeId])
            ->with('success', 'Attribute value deleted successfully.');
    }

    private function storeSwatchImage($image): string
    {
        $imageName = time() . '_' . Str::random(8) . '.' . $image->extension();
        $uploadPath = public_path('uploads/attribute-values');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        $image->move($uploadPath, $imageName);

        return 'attribute-values/' . $imageName;
    }

    private function isHexColor(?string $value): bool
    {
        if (!$value) {
            return false;
        }

        return (bool) preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $value);
    }
}
