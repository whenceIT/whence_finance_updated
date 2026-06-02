@php
$officeSettings = \App\Models\PlatformSetting::getBranchDepositSettings($officeIdParam ?? null);
$exemptions = [
    [
        'title' => 'Administration Department Fee Deposit',
        'description' => $officeSettings['admin'] 
            ? 'Obligated to make payment to Administration Department fee deposit'
            : 'Excluded from making payment to Administration Department fee deposit',
        'enabled' => $officeSettings['admin'],
        'color' => $officeSettings['admin'] ? '#28a745' : '#dc3545'
    ],
    [
        'title' => 'Building & Infrastructure Fee Deposit',
        'description' => $officeSettings['building'] 
            ? 'Obligated to make payment to Building & Infrastructure fee deposits'
            : 'Excluded from making payment to Building & Infrastructure fee deposits',
        'enabled' => $officeSettings['building'],
        'color' => $officeSettings['building'] ? '#28a745' : '#dc3545'
    ],
    [
        'title' => 'Statutory Payments Deposit',
        'description' => $officeSettings['statutory'] 
            ? 'Obligated to make payment to Statutory payments deposits'
            : 'Excluded from making payment to Statutory payments deposits',
        'enabled' => $officeSettings['statutory'],
        'color' => $officeSettings['statutory'] ? '#28a745' : '#dc3545'
    ],
    [
        'title' => 'Setup Cost Debt Payment',
        'description' => $officeSettings['set_up_debt'] 
            ? 'Obligated to make payment towards K5,000 minimum debt for setup cost'
            : 'Excluded from making payment towards setup cost debt',
        'enabled' => $officeSettings['set_up_debt'],
        'color' => $officeSettings['set_up_debt'] ? '#28a745' : '#dc3545'
    ]
];
@endphp

@include('components.office-exemptions', ['title' => 'Office Exemptions', 'settings' => $exemptions])

<script>
var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}
</script>