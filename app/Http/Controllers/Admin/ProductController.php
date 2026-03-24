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
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'isbn' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required',
            'display_order' => 'nullable|integer',
            'attributes' => 'nullable|array',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
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
            $primaryIndex = $request->input('primary_image_index', 0);
            $data['primary_image'] = $images[$primaryIndex] ?? $images[0];
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

        $product = Product::create($data);

        // Handle Variant Matrix Storage
        // Handle Variant Matrix
        if ($request->has('variant_combinations')) {
            $combs = $request->input('variant_combinations');
            $prices = $request->input('v_price');
            $stocks = $request->input('v_stock');
            $skus = $request->input('v_sku');
            $variantImagesFiles = $request->file('v_images');

            $usedSkus = [];

            foreach ($combs as $index => $idsString) {
                if(empty($idsString)) continue;
                $idsArray = explode(',', $idsString);
                $combJson = [];
                $attrValuesJson = [];
                foreach ($idsArray as $valId) {
                    $val = \App\Models\AttributeValue::with('attribute')->find($valId);
                    if ($val) {
                        $combJson[$val->attribute_id] = [$val->id];
                        $attrValuesJson[Str::slug($val->attribute->name)] = $val->name;
                    }
                }

                // Auto-deduplicate SKU
                $baseSku = !empty($skus[$index]) ? $skus[$index] : ($product->sku ? $product->sku . '-' . $index : 'PRD-' . $product->id . '-' . $index);
                $variantSku = $baseSku;
                $skuCounter = 1;
                while(in_array($variantSku, $usedSkus) || \App\Models\ProductVariant::where('sku', $variantSku)->exists()) {
                    $variantSku = $baseSku . '-' . $skuCounter;
                    $skuCounter++;
                }
                $usedSkus[] = $variantSku;

                $variantData = [
                    'product_id' => $product->id,
                    'combination' => $combJson,
                    'attribute_values' => $attrValuesJson,
                    'price' => $prices[$index] ?? $product->price,
                    'sale_price' => $prices[$index] ?? $product->price,
                    'stock_quantity' => $stocks[$index] ?? 0,
                    'sku' => $variantSku,
                    'status' => 'active'
                ];

                // Handle Multiple Variant Images
                $vUploadedImages = [];
                if (isset($variantImagesFiles[$index])) {
                    foreach ($variantImagesFiles[$index] as $file) {
                        $imgName = time() . '_v_' . $index . '_' . uniqid() . '.' . $file->extension();
                        $file->move(public_path('uploads/products/variants'), $imgName);
                        $vUploadedImages[] = 'products/variants/' . $imgName;
                    }
                    $variantData['images'] = $vUploadedImages;
                    $variantData['image'] = $vUploadedImages[0] ?? null; // For legacy
                }

                \App\Models\ProductVariant::create($variantData);
            }
        }

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
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'isbn' => 'nullable|string',
            'regular_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'status' => 'required',
            'display_order' => 'nullable|integer',
            'attributes' => 'nullable|array',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
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
            // Delete old images if new ones uploaded
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
            $primaryIndex = $request->input('primary_image_index', 0);
            $data['primary_image'] = $images[$primaryIndex] ?? $images[0];
        }

        // Handle Color-specific Images (Merge with existing)
        $colorImages = $product->color_images ?? [];
        if(!is_array($colorImages)) {
            $colorImages = json_decode($colorImages, true) ?? [];
        }

        if ($request->hasFile('color_images')) {
            foreach ($request->file('color_images') as $colorValueId => $files) {
                // If new files for this color are uploaded, we append them or replace?
                // The user said "Update", usually means append or replace for that color.
                // Let's replace only for that specific color if images are sent.
                $colorImages[$colorValueId] = []; 
                foreach ($files as $file) {
                    $imageName = time() . '_' . uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/products'), $imageName);
                    $colorImages[$colorValueId][] = 'products/' . $imageName;
                }
            }
        }
        $data['color_images'] = $colorImages;

        // Handle Variant Matrix
        if ($request->has('variant_combinations')) {
            $product->product_variants()->delete(); 
            $combinations = $request->variant_combinations;
            $prices = $request->v_price;
            $stocks = $request->v_stock;
            $skus = $request->v_sku;
            $variantImagesFiles = $request->file('v_images');
            $existingImages = $request->v_existing_image;

            $usedSkus = [];

            foreach ($combinations as $index => $idsString) {
                if(empty($idsString)) continue;
                $idsArray = explode(',', $idsString);
                $combJson = [];
                $attrValuesJson = [];
                foreach ($idsArray as $valId) {
                    $val = \App\Models\AttributeValue::with('attribute')->find($valId);
                    if ($val) {
                        $combJson[$val->attribute_id] = [$val->id];
                        $attrValuesJson[Str::slug($val->attribute->name)] = $val->name;
                    }
                }

                // Auto-deduplicate SKU
                $baseSku = !empty($skus[$index]) ? $skus[$index] : ($product->sku ? $product->sku . '-' . $index : 'PRD-' . $product->id . '-' . $index);
                $variantSku = $baseSku;
                $skuCounter = 1;
                while(in_array($variantSku, $usedSkus) || \App\Models\ProductVariant::where('sku', $variantSku)->exists()) {
                    $variantSku = $baseSku . '-' . $skuCounter;
                    $skuCounter++;
                }
                $usedSkus[] = $variantSku;

                $variantData = [
                    'product_id' => $product->id,
                    'combination' => $combJson,
                    'attribute_values' => $attrValuesJson,
                    'price' => $prices[$index] ?? $product->price,
                    'sale_price' => $prices[$index] ?? $product->price,
                    'stock_quantity' => $stocks[$index] ?? 0,
                    'sku' => $variantSku,
                    'status' => 'active'
                ];

                // Handle Multiple Variant Images
                $vUploadedImages = [];
                $vExistingArray = [];
                
                // Get existing images from hidden input if present
                if ($request->has('v_existing_images') && isset($request->v_existing_images[$index])) {
                    $val = $request->v_existing_images[$index];
                    if ($val) {
                        $vExistingArray = json_decode($val, true) ?? [];
                        if (!is_array($vExistingArray) && !empty($val)) $vExistingArray = [$val];
                    }
                }

                if (isset($variantImagesFiles[$index])) {
                    foreach ($variantImagesFiles[$index] as $file) {
                        $imgName = time() . '_v_' . $index . '_' . uniqid() . '.' . $file->extension();
                        $file->move(public_path('uploads/products/variants'), $imgName);
                        $vUploadedImages[] = 'products/variants/' . $imgName;
                    }
                    $variantData['images'] = $vUploadedImages;
                    $variantData['image'] = $vUploadedImages[0] ?? null; 
                } else {
                    $variantData['images'] = $vExistingArray;
                    $variantData['image'] = $vExistingArray[0] ?? null;
                }

                $product->product_variants()->create($variantData);
            }
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
