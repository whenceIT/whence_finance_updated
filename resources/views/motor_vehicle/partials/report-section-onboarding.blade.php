@php
    $kycCompleted = false;
    $complianceCompleted = false;
    $ownershipCompleted = false;
    if ($loan && $loan->loan_product_id == 0 && $loan->client) {
        $client = $loan->client;
        $kycFields = ['nrc_number', 'phone_primary', 'email_primary', 'city', 'address_line1'];
        $kycCompleted = true;
        foreach ($kycFields as $field) {
            if (!isset($client->$field) || $client->$field === null || trim($client->$field) === '') {
                $kycCompleted = false;
                break;
            }
        }
        $complianceCompleted = $loan->relationLoaded('complianceScreenings')
            && $loan->complianceScreenings->whereIn('status', ['cleared', 'flagged', 'requires_review'])->isNotEmpty();
        $ownershipCompleted = $vehicle->relationLoaded('ownershipRecords')
            && $vehicle->ownershipRecords->isNotEmpty();
    }
@endphp

<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-clipboard-check"></i> Onboarding Progress Summary</h3>
    </div>
    <div class="row">
        <div class="col-md-4">
            <table class="report-table" style="margin-bottom: 0;">
                <tr>
                    <th class="label-cell">KYC Verification</th>
                    <td>
                        @if($kycCompleted)
                            <span class="status-badge status-cleared">Completed</span>
                        @else
                            <span class="status-badge status-pending">Incomplete</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-4">
            <table class="report-table" style="margin-bottom: 0;">
                <tr>
                    <th class="label-cell">PEP & Sanctions Screening</th>
                    <td>
                        @if($complianceCompleted)
                            <span class="status-badge status-cleared">Completed</span>
                        @else
                            <span class="status-badge status-pending">Incomplete</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-4">
            <table class="report-table" style="margin-bottom: 0;">
                <tr>
                    <th class="label-cell">Ownership Verification</th>
                    <td>
                        @if($ownershipCompleted)
                            <span class="status-badge status-cleared">Completed</span>
                        @else
                            <span class="status-badge status-pending">Incomplete</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
