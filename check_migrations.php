<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = DB::select('SELECT * FROM migrations WHERE migration LIKE ?', ['%recreate_collateral%']);
foreach ($rows as $row) {
    echo "Migration: " . $row->migration . "\n";
    echo "Batch: " . $row->batch . "\n";
}
