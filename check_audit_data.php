<?php
// Quick script to check audit submission data
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get the latest audit submission
$submission = \App\Models\AuditSubmission::latest()->first();

if (!$submission) {
    echo "No audit submissions found.\n";
    exit;
}

echo "Audit Submission ID: {$submission->id}\n";
echo "Office: {$submission->office->name}\n";
echo "Audit Date: {$submission->audit_date}\n\n";

echo "Section 2 (Wallet) Data:\n";
for ($i = 1; $i <= 10; $i++) {
    $field = "s2_{$i}";
    $value = $submission->$field;
    echo "  s2_{$i}: " . ($value ?? 'NULL') . "\n";
}

echo "\nSection 3 (Loans) Data:\n";
for ($i = 1; $i <= 7; $i++) {
    $field = "s3_{$i}";
    $value = $submission->$field;
    echo "  s3_{$i}: " . ($value ?? 'NULL') . "\n";
}

echo "\nSection 4 (Collections) Data:\n";
for ($i = 1; $i <= 6; $i++) {
    $field = "s4_{$i}";
    $value = $submission->$field;
    echo "  s4_{$i}: " . ($value ?? 'NULL') . "\n";
}
