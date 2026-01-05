<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$db = DB::connection();

echo "Step 1: Fixing migrations table structure...\n";

// Update the entry with id 0 to a new max ID + 1
$idZero = $db->table('migrations')->where('id', 0)->first();
if ($idZero) {
    $maxId = $db->table('migrations')->max('id');
    $newId = $maxId + 1;
    $db->table('migrations')->where('id', 0)->update(['id' => $newId]);
    echo "Updated migration entry '{$idZero->migration}' from id 0 to $newId.\n";
} else {
    echo "No entry with id 0 found.\n";
}

// Ensure AUTO_INCREMENT is set on the id column
try {
    $db->statement("ALTER TABLE migrations MODIFY COLUMN id INT UNSIGNED AUTO_INCREMENT");
    echo "Migrations table 'id' column modified to AUTO_INCREMENT.\n";
} catch (\Exception $e) {
    echo "Error modifying migrations table: " . $e->getMessage() . "\n";
}

echo "\nStep 2: Automating 'skip if table exists'...\n";

// List of pending migrations and their associated tables
// We'll broaden this to check all pending migrations.
$allMigrations = glob(base_path('database/migrations/*.php'));
$ranMigrations = $db->table('migrations')->pluck('migration')->toArray();

$pending = [];
foreach ($allMigrations as $path) {
    $name = basename($path, '.php');
    if (!in_array($name, $ranMigrations)) {
        $pending[] = $name;
    }
}

echo "Found " . count($pending) . " pending migrations.\n";

$batch = $db->table('migrations')->max('batch') + 1;

foreach ($pending as $migrationName) {
    // Attempt to guess the table name from the migration filename
    // Common format: YYYY_MM_DD_HHMMSS_create_TABLE_NAME_table
    $tableName = null;
    if (preg_match('/create_(.*)_table/', $migrationName, $matches)) {
        $tableName = $matches[1];
    } elseif (preg_match('/add_.*_to_(.*)_table/', $migrationName, $matches)) {
        // For 'add' migrations, we don't necessarily want to skip if the table exists, 
        // because the table IS supposed to exist. We only skip CREATE migrations if table exists.
        // However, if the user says "simply if table already exists skip it", 
        // they likely mean the CREATE table migrations that are failing.
    }

    if ($tableName && Schema::hasTable($tableName)) {
        echo "Table '$tableName' already exists. Skipping migration: $migrationName\n";
        $db->table('migrations')->insert([
            'migration' => $migrationName,
            'batch' => $batch
        ]);
    }
}

echo "\nFix completed.\n";
