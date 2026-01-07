<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['users', 'role_users', 'activations', 'audit_trail', 'persistences', 'reminders', 'throttle'];

foreach ($tables as $table) {
    try {
        $res = DB::select("SHOW CREATE TABLE $table");
        $create = (array) $res[0];
        $sql = array_values($create)[1];
        $has_ai = (strpos($sql, 'AUTO_INCREMENT') !== false);
        echo "Table $table: " . ($has_ai ? "HAS AUTO_INCREMENT" : "NO AUTO_INCREMENT") . "\n";
        if (!$has_ai) {
            echo "SQL: $sql\n";
        }
    } catch (\Exception $e) {
        echo "Table $table: Error - " . $e->getMessage() . "\n";
    }
}
