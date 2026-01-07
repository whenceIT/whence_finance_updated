<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['activations', 'audit_trail', 'persistences', 'reminders', 'throttle'];

foreach ($tables as $table) {
    echo "Checking keys for table: $table...\n";
    try {
        $res = DB::select("SHOW KEYS FROM $table");
        print_r($res);
        $create = DB::select("SHOW CREATE TABLE $table")[0];
        print_r($create);
    } catch (\Exception $e) {
        echo "Error checking $table: " . $e->getMessage() . "\n";
    }
}
