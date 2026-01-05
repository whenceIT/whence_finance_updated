<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = Illuminate\Support\Facades\DB::connection();

// Check if blacklist_reasons table exists
$exists = Illuminate\Support\Facades\Schema::hasTable('blacklist_reasons');
echo "Table 'blacklist_reasons' exists: " . ($exists ? 'Yes' : 'No') . "\n";

// Show the entry with id 0
$idZero = $db->table('migrations')->where('id', 0)->first();
echo "Entry with id 0:\n";
print_r($idZero);
