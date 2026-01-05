<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = Illuminate\Support\Facades\DB::connection();
$results = $db->select('DESCRIBE migrations');
print_r($results);

$count = $db->table('migrations')->where('id', 0)->count();
echo "\nCount of migrations with id 0: $count\n";

$maxId = $db->table('migrations')->max('id');
echo "Max migration id: $maxId\n";
