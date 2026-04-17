<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$cat = App\Models\Category::where('slug','mens')->first();
if ($cat) {
    echo "cat_id={$cat->id}\n";
    echo "products=" . App\Models\Product::where('category_id', $cat->id)->count() . "\n";
} else {
    echo "no cat\n";
}
?>
