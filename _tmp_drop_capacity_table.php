<?php
// Temporary helper: remove the no-longer-needed branch_position_capacities table.
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

if (Schema::hasTable('branch_position_capacities')) {
    echo 'rows before drop: ' . DB::table('branch_position_capacities')->count() . PHP_EOL;
    Schema::drop('branch_position_capacities');
}

$deleted = DB::table('migrations')
    ->where('migration', '2026_09_21_000001_create_branch_position_capacities_table')
    ->delete();

echo 'table exists after drop: ' . (Schema::hasTable('branch_position_capacities') ? 'yes' : 'no') . PHP_EOL;
echo 'migration records removed: ' . $deleted . PHP_EOL;
echo 'remaining 2026_09_21 migrations: ' . json_encode(
    DB::table('migrations')->where('migration', 'like', '2026_09_21%')->pluck('migration')
) . PHP_EOL;
