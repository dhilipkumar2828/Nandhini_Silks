<?php
$p = \App\Models\Product::where('slug', 'testing')->first();
if ($p) {
    // Also attach some dummy attributes if Red doesn't exist
    $c = $p->color_images ?? [];
    $c[1] = ['products/pro1.png', 'products/pro2.png'];
    $c[2] = ['products/pro3.png']; // Suppose 2 is another color
    $p->color_images = $c;
    
    // Add images for the main gallery too
    $imgs = $p->images ?? [];
    if (!in_array('products/pro1.png', $imgs)) {
        $imgs[] = 'products/pro1.png';
        $imgs[] = 'products/pro2.png';
        $imgs[] = 'products/pro3.png';
    }
    $p->images = $imgs;

    $p->save();
    echo "Done updating testing product.\n";
} else {
    echo "Product not found.\n";
}
