<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrations = [
    '2026_08_31_010002_create_motor_vehicle_loan_status_history_table',
    '2026_08_31_030002_create_vehicle_ownership_records_table',
    '2026_08_31_030003_add_enhanced_fields_to_vehicle_valuations_table',
    '2026_08_31_030004_add_enhanced_fields_to_vehicle_inspections_table',
    '2026_08_31_030005_add_enhanced_fields_to_vehicle_insurance_table',
    '2026_08_31_030006_add_enhanced_fields_to_vehicle_custody_table',
    '2026_08_31_030007_create_vehicle_movements_table',
    '2026_08_31_030008_create_vehicle_roll_calls_table',
    '2026_08_31_030009_add_reloan_fields_to_motor_vehicle_loans_table',
];

$batch = DB::table('migrations')->max('batch') + 1;
foreach ($migrations as $migration) {
    DB::table('migrations')->insert(['migration' => $migration, 'batch' => $batch]);
}
echo 'Marked ' . count($migrations) . ' migrations as run' . PHP_EOL;
