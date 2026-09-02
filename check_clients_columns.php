<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select('SHOW COLUMNS FROM clients');
$columnNames = array_column($columns, 'Field');

$checkColumns = ['nrc_number', 'tpin', 'address_line1', 'address_line2', 'city', 
    'employer', 'employer_address', 'business_name', 'business_type', 'annual_income',
    'phone_primary', 'phone_secondary', 'email_primary', 'next_of_kin_name', 
    'next_of_kin_relationship', 'next_of_kin_phone', 'next_of_kin_address',
    'guarantor_name', 'guarantor_nrc', 'guarantor_phone', 'guarantor_address',
    'guarantor_employer', 'guarantor_relationship', 'verification_status', 'verified_by_id'];

foreach ($checkColumns as $col) {
    echo $col . ': ' . (in_array($col, $columnNames) ? 'EXISTS' : 'MISSING') . "\n";
}
