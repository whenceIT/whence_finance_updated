<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['activations', 'audit_trail', 'persistences', 'reminders', 'throttle'];

foreach ($tables as $table) {
    echo "Fixing table: $table...\n";
    try {
        // First check if there is an 'id' column
        $columns = DB::select("SHOW COLUMNS FROM $table LIKE 'id'");
        if (count($columns) > 0) {
            // Modify ID to be auto-increment
            // We need to know the type. Based on my previous check, they are int(10) unsigned NOT NULL.
            DB::statement("ALTER TABLE $table MODIFY COLUMN id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
            echo "Successfully added AUTO_INCREMENT to $table.id\n";
        } else {
            echo "No 'id' column found in $table\n";
        }
    } catch (\Exception $e) {
        echo "Error fixing $table: " . $e->getMessage() . "\n";
    }
}

// Special check for role_users
echo "Checking role_users...\n";
try {
    $res = DB::select("SHOW CREATE TABLE role_users");
    $create = (array) $res[0];
    $sql = array_values($create)[1];
    echo "role_users SQL: $sql\n";
    if (strpos($sql, 'id') !== false && strpos($sql, 'AUTO_INCREMENT') === false) {
        DB::statement("ALTER TABLE role_users MODIFY COLUMN id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
        echo "Successfully added AUTO_INCREMENT to role_users.id\n";
    }
} catch (\Exception $e) {
    echo "Error checking role_users: " . $e->getMessage() . "\n";
}
