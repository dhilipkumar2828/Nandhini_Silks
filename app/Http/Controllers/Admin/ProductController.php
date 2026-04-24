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
use App\Models\OfferCollection;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $query = Product::with(['category', 'subCategory', 'childCategory']);

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $status = $request->status == 'active' ? 1 : 0;
            $query->where('status', '=', $status);
        }

        $products = $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
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
        $offerCollections = OfferCollection::where('status', 'active')->get();

        return view('admin.products.create', compact('categories', 'attributes', 'taxClasses', 'products', 'offerCollections'));
    }

    public function store(Request $request)
    {
        $isVariant = $request->input('is_variant') == '1';

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'name' => 'required|string|max:255',
            'full_description' => 'nullable|string',
            'specification' => 'nullable|string',
            'offer_collection' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|unique:products,sku',
            'barcode' => 'nullable|string',
            'isbn' => 'nullable|string',
            'regular_price' => $isVariant ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => $isVariant ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'required',
            'display_order' => 'nullable|integer',
            'show_offer_on_homepage' => 'nullable|boolean',
            'is_free_delivery' => 'nullable|boolean',
            'attributes' => 'nullable|array',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'related_products' => 'nullable|array',
            'tags' => 'nullable|string',
            'weight' => 'required|numeric|min:0.1|max:10',
            'v_weight.*' => 'nullable|numeric|min:0.1|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'color_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'v_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'slug.unique' => 'This Product Slug is already in use. Please choose a different one.',
            'weight.max' => 'Weight cannot exceed 10 KG. Use format like 0.10 (100g).',
            'weight.min' => 'Minimum weight should be 0.10 KG.',
            'v_weight.*.max' => 'Variant weight cannot exceed 10 KG.',
            'v_weight.*.min' => 'Minimum variant weight should be 0.10 KG.',
        ]);

        $isVariant = $request->input('is_variant') == '1';
        $data = $request->except(['color_images', 'images']);
        $data['show_offer_on_homepage'] = $request->has('show_offer_on_homepage') ? 1 : 0;
        
        // If it's a variant product and regular_price is empty, try to get from first variant
        if($isVariant && empty($request->regular_price) && $request->has('v_price')) {
            $vPrices = $request->input('v_price');
            $data['regular_price'] = is_array($vPrices) ? reset($vPrices) : $vPrices;
        }

        $data['price'] = (floatval($request->sale_price) > 0) ? $request->sale_price : ($data['regular_price'] ?? $request->regular_price);
        $data['sale_price'] = (floatval($request->sale_price) > 0) ? $request->sale_price : null;
        $vStocks = $request->input('v_stock');
        $data['stock_quantity'] = $isVariant && empty($request->stock_quantity) ? ($vStocks ? (is_array($vStocks) ? reset($vStocks) : 0) : 0) : $request->stock_quantity;
        
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

        // Sync Offer Collections
        if ($request->has('offer_collections')) {
            $product->offerCollections()->sync($request->offer_collections);
        }

        // Handle Variant Matrix Storage
        // Handle Variant Matrix
        if ($request->input('is_variant') == '1' && $request->has('variant_combinations')) {
            $combs = $request->input('variant_combinations');
            $prices = $request->input('v_price');
            $stocks = $request->input('v_stock');
            $skus = $request->input('v_sku');
            $variantImagesFiles = $request->file('v_images') ?? [];

            $usedSkus = [];
            $variantFound = false;
            $firstVariantState = [];

            $totalStockSum = 0;
            foreach ($combs as $index => $idsString) {
                if(empty($idsString)) continue;
                
                // ... logic for variantData ...
                $vStockValue = $stocks[$idsString] ?? ($stocks[$index] ?? 0);
                $totalStockSum += (int)$vStockValue;

                $variantData = [
                    'product_id' => $product->id,
                    'combination' => $combJson,
                    'attribute_values' => $attrValuesJson,
                    'price' => $prices[$idsString] ?? ($prices[$index] ?? ($product->regular_price ?? $product->price)),
                    'sale_price' => $request->v_sale_price[$idsString] ?? ($request->v_sale_price[$index] ?? $product->sale_price),
                    'stock_quantity' => $vStockValue,
                    'low_stock_threshold' => $request->v_low_stock[$idsString] ?? ($request->v_low_stock[$index] ?? null),
                    'weight' => $request->v_weight[$idsString] ?? ($request->v_weight[$index] ?? null),
                    'sku' => $variantSku,
                    'status' => 'active'
                ];
                
                // Handle Multiple Variant Images
                $vUploadedImages = [];
                $rowFiles = $variantImagesFiles[$idsString] ?? ($variantImagesFiles[$index] ?? null);

                if ($rowFiles) {
                    foreach ($rowFiles as $file) {
                        $imgName = time() . '_v_' . $index . '_' . uniqid() . '.' . $file->extension();
                        $file->move(public_path('uploads/products/variants'), $imgName);
                        $vUploadedImages[] = 'products/variants/' . $imgName;
                    }
                    $variantData['images'] = $vUploadedImages;
                    $variantData['image'] = $vUploadedImages[0] ?? null; 
                }

                $newVariant = \App\Models\ProductVariant::create($variantData);

                // Collect first variant's data for master product sync (except stock which we sum)
                if (!$variantFound) {
                    $firstVariantState = [
                        'primary_image' => $newVariant->image,
                        'image_path' => $newVariant->image,
                        'images' => $newVariant->images,
                        'regular_price' => $newVariant->price,
                        'price' => $newVariant->sale_price ?: $newVariant->price,
                        'sale_price' => $newVariant->sale_price,
                        'weight' => $newVariant->weight,
                        'sku' => $newVariant->sku ?: $product->sku
                    ];
                    $variantFound = true;
                }
            }
            
            if ($variantFound) {
                $firstVariantState['stock_quantity'] = $totalStockSum;
                $product->update($firstVariantState);
            }
            
            $attributeMap = [];
            foreach ($combs as $idsString) {
                if(empty($idsString)) continue;
                $idsArray = explode(',', $idsString);
                foreach ($idsArray as $valId) {
                    $val = \App\Models\AttributeValue::find($valId);
                    if ($val) {
                        $attributeMap[$val->attribute_id][] = (int)$valId;
                    }
                }
            }
            foreach ($attributeMap as $attrId => $vIds) {
                $attributeMap[$attrId] = array_values(array_unique($vIds));
            }
            $product->update(['attributes' => $attributeMap]);
        }

        return redirect()->route('admin.products.success')->with(['success' => 'Product published successfully.', 'action' => 'published']);
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
        $offerCollections = OfferCollection::where('status', 'active')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'subCategories', 'childCategories', 'attributes', 'taxClasses', 'products', 'offerCollections'));
    }

    public function update(Request $request, Product $product)
    {
        $isVariant = $request->input('is_variant') == '1';

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'child_category_id' => 'nullable|exists:child_categories,id',
            'name' => 'required|string|max:255',
            'full_description' => 'nullable|string',
            'specification' => 'nullable|string',
            'offer_collection' => 'nullable|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'sku' => 'nullable|string|unique:products,sku,' . $product->id,
            'barcode' => 'nullable|string',
            'isbn' => 'nullable|string',
            'regular_price' => $isVariant ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'stock_quantity' => $isVariant ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'status' => 'required',
            'display_order' => 'nullable|integer',
            'show_offer_on_homepage' => 'nullable|boolean',
            'is_free_delivery' => 'nullable|boolean',
            'attributes' => 'nullable|array',
            'tax_class_id' => 'nullable|exists:tax_classes,id',
            'related_products' => 'nullable|array',
            'tags' => 'nullable|string',
            'weight' => $isVariant ? 'nullable' : 'required|numeric|min:0.1|max:10',
            'v_weight.*' => 'nullable|numeric|min:0.1|max:10',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'color_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'v_images.*.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'slug.unique' => 'This Product Slug is already in use. Please choose a different one.',
            'weight.max' => 'Weight cannot exceed 10 KG. Use format like 0.10 (100g).',
            'weight.min' => 'Minimum weight should be 0.10 KG.',
            'v_weight.*.max' => 'Variant weight cannot exceed 10 KG.',
            'v_weight.*.min' => 'Minimum variant weight should be 0.10 KG.',
        ]);

        $isVariant = $request->input('is_variant') == '1';
        $data = $request->except(['color_images', 'images']);
        $data['show_offer_on_homepage'] = $request->has('show_offer_on_homepage') ? 1 : 0;
        
        // If it's a variant product and regular_price is empty, try to get from first variant
        if($isVariant && empty($request->regular_price) && $request->has('v_price')) {
            $vPrices = $request->input('v_price');
            $data['regular_price'] = is_array($vPrices) ? reset($vPrices) : $vPrices;
        }

        $data['price'] = (floatval($request->sale_price) > 0) ? $request->sale_price : ($data['regular_price'] ?? $request->regular_price);
        $data['sale_price'] = (floatval($request->sale_price) > 0) ? $request->sale_price : null;
        $vStocks = $request->input('v_stock');
        $vWeights = $request->input('v_weight');
        $data['stock_quantity'] = $isVariant && empty($request->stock_quantity) ? ($vStocks ? (is_array($vStocks) ? reset($vStocks) : 0) : 0) : $request->stock_quantity;
        $data['weight'] = $isVariant && empty($request->weight) ? ($vWeights ? (is_array($vWeights) ? reset($vWeights) : 0.10) : 0.10) : ($request->weight ?? 0.10);

        $data['attributes'] = $this->sanitizeAttributes($request->input('attributes', []));
        $data['related_products'] = $request->input('related_products', []);

        if ($request->filled('tags')) {
            $data['tags'] = array_map('trim', explode(',', $request->tags));
        } else {
            $data['tags'] = [];
        }

        // Handle General Images
        $existingGeneralImages = $request->input('existing_images', []);
        
        // Delete images from disk that were removed in the UI
        if ($product->images) {
            foreach ($product->images as $oldImage) {
                if (!in_array($oldImage, $existingGeneralImages)) {
                    $path = public_path('uploads/' . $oldImage);
                    if (file_exists($path)) unlink($path);
                }
            }
        }
        
        $images = $existingGeneralImages;
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $imageName = time() . '_' . uniqid() . '.' . $image->extension();
                $image->move(public_path('uploads/products'), $imageName);
                $images[] = 'products/' . $imageName;
            }
        }
        $data['images'] = $images;
        $primaryIndex = $request->input('primary_image_index', 0);
        $data['primary_image'] = $images[$primaryIndex] ?? ($images[0] ?? null);

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
        if ($request->input('is_variant') == '1' && $request->has('variant_combinations')) {
            $product->product_variants()->delete(); 
            $combinations = $request->variant_combinations;
            $prices = $request->v_price;
            $stocks = $request->v_stock;
            $skus = $request->v_sku;
            $variantImagesFiles = $request->file('v_images') ?? [];

            $variantFound = false;
            $usedSkus = [];
            $firstVariantState = [];

            $totalStockSum = 0;
            foreach ($combinations as $index => $idsString) {
                if(empty($idsString)) continue;
                
                // ... logic for variantData ...
                $vStockValue = $stocks[$idsString] ?? ($stocks[$index] ?? 0);
                $totalStockSum += (int)$vStockValue;

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
                $baseSku = !empty($skus[$idsString]) ? $skus[$idsString] : (!empty($skus[$index]) ? $skus[$index] : ($product->sku ? $product->sku . '-' . $index : 'PRD-' . $product->id . '-' . $index));
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
                    'price' => $prices[$idsString] ?? ($prices[$index] ?? ($product->regular_price ?? $product->price)),
                    'sale_price' => (floatval($request->v_sale_price[$idsString] ?? ($request->v_sale_price[$index] ?? $product->sale_price))) > 0 ? ($request->v_sale_price[$idsString] ?? ($request->v_sale_price[$index] ?? $product->sale_price)) : null,
                    'stock_quantity' => $vStockValue,
                    'low_stock_threshold' => $request->v_low_stock[$idsString] ?? ($request->v_low_stock[$index] ?? null),
                    'weight' => $request->v_weight[$idsString] ?? ($request->v_weight[$index] ?? null),
                    'sku' => $variantSku,
                    'status' => 'active'
                ];

                // Handle Multiple Variant Images
                $vUploadedImages = [];
                $vExistingArray = [];
                
                // Get existing images from hidden input
                if ($request->has('v_existing_images')) {
                    $val = $request->v_existing_images[$idsString] ?? ($request->v_existing_images[$index] ?? null);
                    if ($val) {
                        try {
                            $vExistingArray = json_decode($val, true);
                            if (!is_array($vExistingArray)) {
                                $vExistingArray = !empty($val) ? [$val] : [];
                            }
                        } catch (\Exception $e) {
                            $vExistingArray = !empty($val) ? [$val] : [];
                        }
                    }
                }

                $rowFiles = $variantImagesFiles[$idsString] ?? ($variantImagesFiles[$index] ?? null);

                if ($rowFiles) {
                    foreach ($rowFiles as $file) {
                        $imgName = time() . '_v_' . $index . '_' . uniqid() . '.' . $file->extension();
                        $file->move(public_path('uploads/products/variants'), $imgName);
                        $vUploadedImages[] = 'products/variants/' . $imgName;
                    }
                    // MERGE new uploads with existing ones
                    $variantData['images'] = array_merge($vExistingArray, $vUploadedImages);
                    $variantData['image'] = $variantData['images'][0] ?? null; 
                } else {
                    $variantData['images'] = $vExistingArray;
                    $variantData['image'] = $vExistingArray[0] ?? null;
                }

                $newVariant = $product->product_variants()->create($variantData);

                // Collect first variant's data for master product sync (except stock which we sum)
                if (!$variantFound) {
                    $firstVariantState = [
                        'primary_image' => $newVariant->image,
                        'image_path' => $newVariant->image,
                        'regular_price' => $newVariant->price,
                        'price' => (floatval($newVariant->sale_price) > 0) ? $newVariant->sale_price : $newVariant->price,
                        'sale_price' => (floatval($newVariant->sale_price) > 0) ? $newVariant->sale_price : null,
                        'sku' => $newVariant->sku ?: $product->sku
                    ];
                    $variantFound = true;
                }
            }
            
            // Merge variant state into the final data array so it's not overwritten
            if ($variantFound) {
                $firstVariantState['stock_quantity'] = $totalStockSum;
                $data = array_merge($data, $firstVariantState);
            }

            // Sync top-level attributes for frontend display/filters
            $attributeMap = [];
            foreach ($request->variant_combinations as $idsString) {
                if(empty($idsString)) continue;
                $idsArray = explode(',', $idsString);
                foreach ($idsArray as $valId) {
                    $val = \App\Models\AttributeValue::find($valId);
                    if ($val) {
                        $attributeMap[$val->attribute_id][] = (int)$valId;
                    }
                }
            }
            foreach ($attributeMap as $attrId => $vIds) {
                $attributeMap[$attrId] = array_values(array_unique($vIds));
            }
            $data['attributes'] = $attributeMap; 
        } else {
            // Delete all variants if switch is OFF
            $product->product_variants()->delete();
        }

        $product->update($data);

        // Sync Offer Collections
        if ($request->has('offer_collections')) {
            $product->offerCollections()->sync($request->offer_collections);
        } else {
            $product->offerCollections()->detach();
        }

        return redirect()->route('admin.products.success')->with(['success' => 'Product updated successfully.', 'action' => 'updated']);
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

    public function getSubCategoryDetails($id)
    {
        $subCategory = SubCategory::find($id);
        return response()->json($subCategory);
    }

    public function getChildCategories($sub_category_id)
    {
        $childCategories = ChildCategory::where('sub_category_id', '=', $sub_category_id)->where('status', '=', 1)->get();
        return response()->json($childCategories);
    }

    public function checkUniqueness(Request $request)
    {
        $field = $request->field;
        $value = $request->value;
        $excludeId = $request->exclude;

        $query = Product::where($field, '=', $value);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return response()->json(['exists' => $query->exists()]);
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
