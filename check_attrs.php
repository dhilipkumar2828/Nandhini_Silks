<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$attrs = \App\Models\Attribute::with('values')->get();
foreach ($attrs as $a) {
    echo "ID:{$a->id} group:{$a->group} name:{$a->name}\n";
    foreach ($a->values as $v) {
        echo "  value_id:{$v->id} name:{$v->name} swatch:{$v->swatch_value}\n";
    }
}
