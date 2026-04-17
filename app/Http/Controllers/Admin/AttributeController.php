<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attribute;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Attribute::withCount('values');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('group', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('status', '=', $status);
        }

        $attributes = $query->orderBy('group', 'asc')->orderBy('name', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.attributes.index', compact('attributes'));
    }

    public function create()
    {
        return view('admin.attributes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string|max:255',
            'name' => 'required|string|max:255|unique:attributes,name',
            'slug' => 'required|string|max:255|unique:attributes,slug',
            'status' => 'required|boolean',
        ], [
            'name.unique' => 'This Attribute Name is already in use.',
            'slug.unique' => 'This Attribute Slug is already in use. Please choose a different one.',
        ]);

        $data = $request->all();

        Attribute::create($data);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute created successfully.');
    }

    public function edit(Attribute $attribute)
    {
        return view('admin.attributes.edit', compact('attribute'));
    }

    public function update(Request $request, Attribute $attribute)
    {
        $request->validate([
            'group' => 'nullable|string|max:255',
            'name' => 'required|string|max:255|unique:attributes,name,' . $attribute->id,
            'slug' => 'required|string|max:255|unique:attributes,slug,' . $attribute->id,
            'status' => 'required|boolean',
        ], [
            'name.unique' => 'This Attribute Name is already in use.',
            'slug.unique' => 'This Attribute Slug is already in use. Please choose a different one.',
        ]);

        $data = $request->all();

        $attribute->update($data);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully.');
    }

    public function checkSlug(Request $request)
    {
        $slug = Str::slug($request->name);
        if ($request->filled('slug')) {
            $slug = Str::slug($request->slug);
        }

        $query = Attribute::where('slug', '=', $slug);
        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        $exists = $query->exists();
        return response()->json([
            'exists' => $exists,
            'slug' => $slug
        ]);
    }

    public function checkName(Request $request)
    {
        $name = $request->name;
        $query = Attribute::where('name', '=', $name);
        if ($request->filled('id')) {
            $query->where('id', '!=', $request->id);
        }

        $exists = $query->exists();
        return response()->json([
            'exists' => $exists
        ]);
    }
}
