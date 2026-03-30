<?php
use Razorpay\Api\Api;
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Manually read .env exactly as the controller does
$envPath = base_path('.env');
$parsed = [];
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (str_contains($line, '=')) {
            [$k, $v] = explode('=', $line, 2);
            $key = trim($k);
            $val = trim($v);
            if (str_starts_with($val, '"') && str_ends_with($val, '"')) $val = trim($val, '"');
            elseif (str_starts_with($val, "'") && str_ends_with($val, "'")) $val = trim($val, "'");
            $parsed[$key] = $val;
        }
    }
}

$key = $parsed['RAZORPAY_KEY'] ?? config('services.razorpay.key');
$secret = $parsed['RAZORPAY_SECRET'] ?? config('services.razorpay.secret');

echo "--- RAZORPAY DEBUG SCRPT ---\n";
echo "Found .env at: " . $envPath . (file_exists($envPath) ? " (EXISTS)\n" : " (MISSING!)\n");
echo "Read Key Prefix: " . ($key ? substr($key, 0, 9) . "..." : "NULL") . "\n";
echo "Secret Length: " . ($secret ? strlen($secret) : 0) . "\n";

if (!$key || !$secret) {
    echo "ERROR: Credentials missing. Ensure RAZORPAY_KEY and RAZORPAY_SECRET are in .env on the server.\n";
    exit;
}

try {
    $api = new Api($key, $secret);
    // Try to fetch something harmless like orders list (limit 1)
    echo "Testing connection to Razorpay API...\n";
    $api->order->all(['count' => 1]);
    echo "SUCCESS: Connection verified. Keys are valid! ✅\n";
} catch (\Exception $e) {
    echo "FAILURE: Razorpay says: " . $e->getMessage() . " ❌\n";
    echo "HINT: This usually means Key or Secret is incorrect, or generated for a different mode (Test/Live).\n";
}
