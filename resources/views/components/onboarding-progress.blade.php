@props([
    'status' => null,
    'loan' => null,
    'printMode' => false,
])

@php
    $status = $status ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null];
    $vehicleId = $loan ? ($loan->vehicle_id ?? optional($loan->vehicle)->id ?? '') : '';
@endphp

<style>
    .onboarding-progress a {
        cursor: pointer;
        transition: transform 0.2s;
        display: inline-block;
    }
    .onboarding-progress a:hover {
        transform: scale(1.2);
    }
    .onboarding-progress a:hover i {
        filter: brightness(0.8);
    }
</style>

<div class="onboarding-progress" style="display: flex; align-items: center; gap: 8px;">
    <div style="text-align: center;">
        @if($status['kyc_completed'] !== null)
            <a href="{{ $printMode ? route('vehicles.report.kyc', $vehicleId) : route('clients.edit-kyc', [$loan->client_id, $loan->id]) }}" style="text-decoration: none;" title="Go to KYC">
                @if($status['kyc_completed'] === true)
                    <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                @else
                    <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                @endif
            </a>
        @else
            <span style="color: #777; font-size: 12px;">N/A</span>
        @endif
        <div style="font-size: 10px; color: #666;">{{ $printMode ? 'Print KYC' : 'KYC' }}</div>
    </div>
    <div style="width: 1px; height: 25px; background: #ccc;"></div>
    <div style="text-align: center;">
        @if($status['compliance_screening_completed'] !== null)
            <a href="{{ $printMode ? route('vehicles.report.compliance', $vehicleId) : route('motor-vehicle-loans.compliance-screening', $loan->id) }}" style="text-decoration: none;" title="Go to Compliance Screening">
                @if($status['compliance_screening_completed'] === true)
                    <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                @else
                    <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                @endif
            </a>
        @else
            <span style="color: #777; font-size: 12px;">N/A</span>
        @endif
        <div style="font-size: 10px; color: #666;">{{ $printMode ? 'Print Compliance' : 'Compliance' }}</div>
    </div>
    <div style="width: 1px; height: 25px; background: #ccc;"></div>
    <div style="text-align: center;">
        @if($status['ownership_completed'] !== null)
            <a href="{{ $printMode ? route('vehicles.report.ownership', $vehicleId) : route('vehicles.ownership-verification.show', $loan->vehicle_id ?? ($loan->vehicle->id ?? '')) }}" style="text-decoration: none;" title="Go to Vehicle Ownership">
                @if($status['ownership_completed'] === true)
                    <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                @else
                    <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                @endif
            </a>
        @else
            <span style="color: #777; font-size: 12px;">N/A</span>
        @endif
        <div style="font-size: 10px; color: #666;">{{ $printMode ? 'Print Ownership' : 'Ownership' }}</div>
    </div>
</div>
