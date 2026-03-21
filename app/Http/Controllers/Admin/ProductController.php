<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\ChildCategory;
use App\Models\Product;
use App\Models\Attribute;
use App\Models\TaxClass;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $products = Product::with(['category', 'subCategory', 'childCategory'])->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('status', '=', 1)->get();
        $attributes = Attribute::with(['values' => function ($query) {
            $query->where('status', '=', true)->orderBy('display_order', 'asc');
        }])->where('status', '=', true)->orderBy('group')->orderBy('name')->get();
        $taxClasses = TaxClass::where('status', '=', 1)->get();
        $products = Product::where('status', '=', 1)->orderBy('name')->get(['id', 'name']);

        return view('admin.products.create', compact('categories', 'attributes', 'taxClasses', 'products'));
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
            'status' => 'required',
            'attributes' => 'nullable|array',
            'tax_class' => 'nullable|string',
            'related_products' => 'nullable|array',
            'tags' => 'nullable|string',
        ]);

        $data = $request->except(['color_images', 'images']);
        $data['slug'] = Str::slug($request->name);
        $data['price'] = $request->sale_price ?: $request->regular_price;
        $data['attributes'] = $this->sanitizeAttributes($request->input('attributes', []));
        $data['related_products'] = $request->input('related_products', []);
        
        // Handle Tags (stored as string in DB but model has array cast, let's store as array)
        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        }

        // Handle General Multiple Images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('uploads/products'), $imageName);
                $images[] = 'products/' . $imageName;
            }
            $data['images'] = $images;
        }

        // Handle Color-specific Images
        if ($request->hasFile('color_images')) {
            $colorImages = [];
            foreach ($request->file('color_images') as $colorValueId => $files) {
                $colorImages[$colorValueId] = [];
                foreach ($files as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/products'), $imageName);
                    $colorImages[$colorValueId][] = 'products/' . $imageName;
                }
            }
            $data['color_images'] = $colorImages;
        }

        Product::create($data);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('status', '=', 1)->get();
        $subCategories = SubCategory::where('category_id', '=', $product->category_id)->get();
        $childCategories = ChildCategory::where('sub_category_id', '=', $product->sub_category_id)->get();
        $attributes = Attribute::with(['values' => function ($query) {
            $query->where('status', '=', true)->orderBy('display_order', 'asc');
        }])->where('status', '=', true)->orderBy('group')->orderBy('name')->get();
        $taxClasses = TaxClass::where('status', '=', 1)->get();
        $products = Product::where('status', '=', 1)->where('id', '!=', $product->id)->orderBy('name')->get(['id', 'name']);
        
        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'childCategories', 'attributes', 'taxClasses', 'products'));
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
            'status' => 'required',
            'attributes' => 'nullable|array',
            'tax_class' => 'nullable|string',
            'related_products' => 'nullable|array',
            'tags' => 'nullable|string',
        ]);

        $data = $request->except(['color_images', 'images']);
        $data['slug'] = Str::slug($request->name);
        $data['price'] = $request->sale_price ?: $request->regular_price;
        $data['attributes'] = $this->sanitizeAttributes($request->input('attributes', []));
        $data['related_products'] = $request->input('related_products', []);

        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $data['tags'] = [];
        }

        // Handle General Images
        if ($request->hasFile('images')) {
            if ($product->images) {
                foreach ($product->images as $oldImage) {
                    if (file_exists(public_path('uploads/' . $oldImage))) unlink(public_path('uploads/' . $oldImage));
                }
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('uploads/products'), $imageName);
                $images[] = 'products/' . $imageName;
            }
            $data['images'] = $images;
        }

        // Handle Color-specific Images
        if ($request->hasFile('color_images')) {
            $existing = $product->color_images ?? [];
            foreach ($request->file('color_images') as $colorValueId => $files) {
                if (!empty($existing[$colorValueId])) {
                    foreach ($existing[$colorValueId] as $old) {
                        if (file_exists(public_path('uploads/' . $old))) unlink(public_path('uploads/' . $old));
                    }
                }
                $existing[$colorValueId] = [];
                foreach ($files as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/products'), $imageName);
                    $existing[$colorValueId][] = 'products/' . $imageName;
                }
            }
            $data['color_images'] = $existing;
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
        $subCategories = SubCategory::where('category_id', '=', $category_id)->where('status', '=', 1)->get();
        return response()->json($subCategories);
    }

    public function getChildCategories($sub_category_id)
    {
        $childCategories = ChildCategory::where('sub_category_id', '=', $sub_category_id)->where('status', '=', 1)->get();
        return response()->json($childCategories);
    }

    private function sanitizeAttributes(array $attributes): array
    {
        $clean = [];

        foreach ($attributes as $attributeId => $values) {
            $valueIds = array_values(array_unique(array_filter(array_map('intval', (array) $values))));
            if (!empty($valueIds)) {
                $clean[(int) $attributeId] = $valueIds;
            }
        }

        return $clean;
    }
}
