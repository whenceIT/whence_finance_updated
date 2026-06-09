@php
$blockSkipSettings = \App\Helpers\StatsHelper::getBranchSkipSettings($officeIdParam ?? null);
$blockSkipExemptions = [
    [
        'title' => 'Administration Department Fee Deposit',
        'description' => 'Allowed to skip payment to Administration Department fee deposit',
        'enabled' => $blockSkipSettings['admin'],
        'color' => '#17a2b8'
    ],
    [
        'title' => 'Building & Infrastructure Fee Deposit',
        'description' => 'Allowed to skip payment to Building & Infrastructure fee deposits',
        'enabled' => $blockSkipSettings['building'],
        'color' => '#17a2b8'
    ],
    [
        'title' => 'Statutory Payments Deposit',
        'description' => 'Allowed to skip payment to Statutory payments deposits',
        'enabled' => $blockSkipSettings['statutory'],
        'color' => '#17a2b8'
    ],
    [
        'title' => 'Setup Cost Debt Payment',
        'description' => 'Allowed to skip payment towards K5,000 minimum debt for setup cost',
        'enabled' => $blockSkipSettings['set_up_debt'],
        'color' => '#17a2b8'
    ]
];

$allowedExemptions = array_filter($blockSkipExemptions, function($e) { return $e['enabled']; });
@endphp

@if(!empty($allowedExemptions))
<div class="card" style="margin: 20px 0 10px 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
    <div class="card-header" style="background: #e7f1ff; border-bottom: 1px solid #cce5ff; padding: 12px 16px;">
        <i class="fa fa-unlock" style="color: #007bff;"></i> 
        <span style="font-weight: 600; color: #0056b3; margin-left: 8px;">Allowed Monthly Deposits Required</span>
    </div>
    <div class="card-body" style="padding: 16px;">
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($allowedExemptions as $exemption)
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 6px; background: {{ $exemption['color'] }}15; border-left: 3px solid {{ $exemption['color'] }};">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; color: #343a40; font-size: 14px; margin-bottom: 3px;">{{ $exemption['title'] }}</div>
                        <div style="color: #6c757d; font-size: 12px;">{{ $exemption['description'] }}</div>
                    </div>
                    <div style="text-align: center; min-width: 100px;">
                        <span style="background: {{ $exemption['color'] }}; color: white; padding: 3px 10px; border-radius: 10px; font-size: 11px; font-weight: 600;">
                            ALLOWED
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

