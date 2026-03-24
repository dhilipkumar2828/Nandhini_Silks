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
            'group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Check for duplicate slug
        if (Attribute::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

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
            'group' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Check for duplicate slug (excluding current)
        if (Attribute::where('slug', $data['slug'])->where('id', '!=', $attribute->id)->exists()) {
            $data['slug'] = $data['slug'] . '-' . time();
        }

        $attribute->update($data);

        return redirect()->route('admin.attributes.index')->with('success', 'Attribute updated successfully.');
    }

    public function destroy(Attribute $attribute)
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Attribute deleted successfully.');
    }
}
