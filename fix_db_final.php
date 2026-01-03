<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['audit_trail', 'persistences', 'reminders', 'throttle'];

foreach ($tables as $table) {
    echo "Processing $table...\n";
    try {
        // Check for duplicates in 'id'
        $dupes = DB::select("SELECT id, COUNT(*) as c FROM $table GROUP BY id HAVING c > 1");
        if (count($dupes) > 0) {
            echo "Found duplicate IDs in $table. Clearing and re-indexing...\n";
            // If data is not critical to keep exact ID 0, we can re-assign IDs or just truncate if it's junk data.
            // audit_trail might be important. persistences/reminders/throttle can probably be truncated or re-indexed.

            // Safer way: add a temporary column, populate it, drop old id, rename new id.
            // Or just: SET @row_number = 0; UPDATE table_name SET id = (@row_number:=@row_number + 1);
            DB::statement("SET @row_number = 0");
            DB::statement("UPDATE $table SET id = (@row_number:=@row_number + 1)");
        }

        echo "Adding Primary Key and Auto Increment to $table...\n";
        DB::statement("ALTER TABLE $table ADD PRIMARY KEY (id)");
        DB::statement("ALTER TABLE $table MODIFY COLUMN id INT(10) UNSIGNED NOT NULL AUTO_INCREMENT");
        echo "Successfully updated $table\n";

    } catch (\Exception $e) {
        echo "Error on $table: " . $e->getMessage() . "\n";
    }
}
