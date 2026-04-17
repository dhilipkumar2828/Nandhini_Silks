<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Current APP_TIMEZONE: " . config('app.timezone') . "\n";
echo "Now (Server): " . now()->toDateTimeString() . "\n";
echo "Now (Explicit Kolkata): " . now('Asia/Kolkata')->toDateTimeString() . "\n";

$coupon = \App\Models\Coupon::latest()->first();
if ($coupon) {
    echo "Latest Coupon Code: " . $coupon->code . "\n";
    echo "Valid From (Stored): " . $coupon->valid_from . "\n";
     if ($coupon->valid_from && now()->lt($coupon->valid_from)) {
        echo "RESULT: Coupon is in the FUTURE relative to server time! ❌\n";
    } else {
        echo "RESULT: Coupon should be valid relative to server time. ✅\n";
    }
}
