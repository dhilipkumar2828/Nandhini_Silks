<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'subCategory', 'childCategory'])->orderBy('id', 'desc')->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', '=', 1, 'and')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Handle Dynamic Multiple Images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.uniqid().'.'.$image->extension();
                $image->move(public_path('uploads/products'), $imageName);
                $images[] = 'products/'.$imageName;
            }
            $data['images'] = $images;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', '=', 1, 'and')->get();
        $subCategories = SubCategory::where('category_id', '=', $product->category_id, 'and')->get();
        $childCategories = ChildCategory::where('sub_category_id', '=', $product->sub_category_id, 'and')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'childCategories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required|boolean',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        // Handle Images
        if ($request->hasFile('images')) {
            // Delete old images
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    if (file_exists(public_path('uploads/' . $oldImage))) {
                        unlink(public_path('uploads/' . $oldImage));
                    }
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time().'_'.uniqid().'.'.$image->extension();
                $image->move(public_path('uploads/products'), $imageName);
                $images[] = 'products/'.$imageName;
            }
            $data['images'] = $images;
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->images) {
            foreach ($product->images as $image) {
                if (file_exists(public_path('uploads/' . $image))) {
                    unlink(public_path('uploads/' . $image));
                }
            }
        }
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully.');
    }

    public function getSubCategories($category_id)
    {
        $subCategories = SubCategory::where('category_id', '=', $category_id, 'and')->where('status', '=', 1, 'and')->get();
        return response()->json($subCategories);
    }

    public function getChildCategories($sub_category_id)
    {
        $childCategories = ChildCategory::where('sub_category_id', '=', $sub_category_id, 'and')->where('status', '=', 1, 'and')->get();
        return response()->json($childCategories);
    }
}
