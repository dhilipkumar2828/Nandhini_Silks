<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ChildCategoryController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $childCategories = ChildCategory::with(['category', 'subCategory'])->orderBy('display_order', 'asc')->paginate($perPage)->withQueryString();
        return view('admin.child_categories.index', compact('childCategories'));
    }

    public function create()
    {
        $categories = Category::where('status', '=', 1)->get();
        return view('admin.child_categories.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
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
            $request->image->move(public_path('uploads/child-categories'), $imageName);
            $data['image'] = 'child-categories/'.$imageName;
        }

        ChildCategory::create($data);

        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category created successfully.');
    }

    public function edit(ChildCategory $childCategory)
    {
        $categories = Category::where('status', '=', 1)->get();
        $subCategories = SubCategory::where('category_id', '=', $childCategory->category_id)->get();
        return view('admin.child_categories.edit', compact('childCategory', 'categories', 'subCategories'));
    }

    public function update(Request $request, ChildCategory $childCategory)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
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
            if ($childCategory->image && file_exists(public_path('uploads/' . $childCategory->image))) {
                unlink(public_path('uploads/' . $childCategory->image));
            }
            $imageName = time() . '_' . Str::random(8) . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/child-categories'), $imageName);
            $data['image'] = 'child-categories/'.$imageName;
        }

        $childCategory->update($data);

        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category updated successfully.');
    }

    public function destroy(ChildCategory $childCategory)
    {
        if ($childCategory->image && file_exists(public_path('uploads/' . $childCategory->image))) {
            unlink(public_path('uploads/' . $childCategory->image));
        }
        $childCategory->delete();

        return redirect()->route('admin.child-categories.index')->with('success', 'Child Category deleted successfully.');
    }

    public function getSubCategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', '=', $category_id)->where('status', '=', 1)->get();
        return response()->json($subCategories);
    }
}

