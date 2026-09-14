@extends('layouts.master')
@section('title')Vehicle Executive Report@endsection

@section('content')

<style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff; }
        .report-card { page-break-inside: avoid; break-inside: avoid; }
    }
    .report-section {
        page-break-inside: avoid;
        break-inside: avoid;
        margin-bottom: 24px;
    }
    .report-header {
        border-bottom: 3px solid #000c3c;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
    .report-title {
        font-size: 18px;
        font-weight: 700;
        color: #000c3c;
        margin: 0;
    }
    .report-meta {
        font-size: 12px;
        color: #6b7280;
        margin-top: 4px;
    }
    .report-table th {
        width: 35%;
        background: #f8fafc;
        font-weight: 600;
        color: #334159;
        vertical-align: top;
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
    }
    .report-table td {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        vertical-align: top;
    }
    .report-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }
    .report-table .label-cell {
        background: #f1f5f9;
    }
    .status-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    .status-cleared { background: #d1fae5; color: #065f46; }
    .status-flagged { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-requires-review { background: #dbeafe; color: #1e40af; }
    .status-available { background: #dcfce7; color: #166534; }
    .status-in-custody { background: #fef3c7; color: #92400e; }
    .status-sold { background: #fee2e2; color: #991b1b; }
    .photo-thumb {
        height: 80px;
        width: 80px;
        object-fit: cover;
        border-radius: 6px;
        margin: 2px;
        border: 1px solid #e2e8f0;
    }
    .photo-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .kyc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 6px 20px;
    }
    .kyc-item {
        display: flex;
        border-bottom: 1px solid #f1f5f9;
        padding: 6px 0;
    }
    .kyc-label {
        font-weight: 600;
        color: #475569;
        min-width: 140px;
    }
    .kyc-value {
        color: #1e293b;
    }
</style>

<section class="content">

    <div class="no-print" style="margin-bottom: 16px;">
        <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back to Vehicle Details
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fa fa-print"></i> Print Report
        </button>
    </div>

    <div class="report-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 10px; padding: 24px; margin-bottom: 24px;">
        <div class="report-header">
            <h2 class="report-title"><i class="fa fa-file-pdf-o"></i> MOTOR VEHICLE EXECUTIVE REPORT</h2>
            <div class="report-meta">
                Generated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }} &nbsp;|&nbsp; Report ID: {{ 'VR'.str_pad($vehicle->id, 6, '0', STR_PAD_LEFT) }}
            </div>
        </div>

        <div class="row" style="margin-bottom: 20px;">
            @if($vehicle->photos && $vehicle->photos->isNotEmpty())
            <div class="col-md-2">
                <img src="{{ $vehicle->photos->first()->photo_url }}"
                     style="width: 100%; max-width: 100px; height: auto; border-radius: 8px; object-fit: cover; border: 2px solid #e2e8f0;">
            </div>
            @endif
            <div class="col-md-10">
                <h3 style="margin: 0 0 4px 0; font-size: 20px; color: #000c3c;">
                    {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->year ?? 'N/A' }})
                </h3>
                <p style="margin: 0; color: #64748b; font-size: 13px;">
                    Reg: <strong>{{ $vehicle->registration_number }}</strong> &nbsp;|&nbsp;
                    Engine: {{ $vehicle->engine_number ?? 'N/A' }} &nbsp;|&nbsp;
                    Chassis: {{ $vehicle->chassis_number ?? 'N/A' }}
                </p>
                <p style="margin: 4px 0 0 0; color: #64748b; font-size: 13px;">
                    @if($vehicle->status == 'sold')
                        <span class="status-badge status-sold">Sold</span>
                    @elseif($vehicle->status == 'in_custody' || $vehicle->custody)
                        <span class="status-badge status-in-custody">In Custody</span>
                    @else
                        <span class="status-badge status-available">Available</span>
                    @endif
                    @if($vehicle->loan)
                        <span class="status-badge" style="background:#ede9fe; color:#5b21b6;">Loan #{{ $vehicle->loan->id }}</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    @include('motor_vehicle.partials.report-section-onboarding', ['vehicle' => $vehicle, 'loan' => $loan])

    @include('motor_vehicle.partials.report-section-vehicle-info', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-insurance', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-custody', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-valuations', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-documents', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-photos', ['vehicle' => $vehicle])

    @include('motor_vehicle.partials.report-section-inspections', ['vehicle' => $vehicle])

    @if($loan)
        @include('motor_vehicle.partials.report-section-compliance', ['loan' => $loan])
        @include('motor_vehicle.partials.report-section-kyc', ['loan' => $loan])
        @include('motor_vehicle.partials.report-section-ownership', ['vehicle' => $vehicle])
    @endif

</section>

@endsection
