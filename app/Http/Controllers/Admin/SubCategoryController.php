<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $subCategories = SubCategory::with('category')->orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.sub_categories.index', compact('subCategories'));
    }

    public function create()
    {
        $categories = Category::where('status', '=', 1)->get();
        return view('admin.sub_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required',
            'display_order' => 'required|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . Str::random(8) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/sub-categories'), $imageName);
            $data['image'] = 'sub-categories/'.$imageName;
        }

        SubCategory::create($data);

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub Category created successfully.');
    }

    public function edit(SubCategory $subCategory)
    {
        $categories = Category::where('status', '=', 1)->get();
        return view('admin.sub_categories.edit', compact('subCategory', 'categories'));
    }

    public function update(Request $request, SubCategory $subCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'description' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'status' => 'required',
            'display_order' => 'required|integer',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        if ($request->hasFile('image')) {
            if ($subCategory->image && file_exists(public_path('uploads/' . $subCategory->image))) {
                unlink(public_path('uploads/' . $subCategory->image));
            }
            $imageName = time() . '_' . Str::random(8) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/sub-categories'), $imageName);
            $data['image'] = 'sub-categories/'.$imageName;
        }

        $subCategory->update($data);

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub Category updated successfully.');
    }

    public function destroy(SubCategory $subCategory)
    {
        if ($subCategory->image && file_exists(public_path('uploads/' . $subCategory->image))) {
            unlink(public_path('uploads/' . $subCategory->image));
        }
        $subCategory->delete();

        return redirect()->route('admin.sub-categories.index')->with('success', 'Sub Category deleted successfully.');
    }
}

