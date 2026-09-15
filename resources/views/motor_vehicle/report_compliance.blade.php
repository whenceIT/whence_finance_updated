@extends('layouts.master')
@section('title')PEP & Sanctions Screening Report@endsection

@section('content')

<style>
    @media print {
        .no-print { display: none !important; }
        .report-card { page-break-inside: avoid; break-inside: avoid; }
    }
    .report-section { page-break-inside: avoid; break-inside: avoid; margin-bottom: 24px; }
    .report-header {
        border-bottom: 3px solid #000c3c;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
    .report-title { font-size: 18px; font-weight: 700; color: #000c3c; margin: 0; }
    .report-meta { font-size: 12px; color: #6b7280; margin-top: 4px; }
    .report-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .report-table th { width: 35%; background: #f8fafc; font-weight: 600; color: #334159; vertical-align: top; padding: 8px 12px; border: 1px solid #e2e8f0; }
    .report-table td { padding: 8px 12px; border: 1px solid #e2e8f0; vertical-align: top; }
    .report-table .label-cell { background: #f1f5f9; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
    .status-cleared { background: #d1fae5; color: #065f46; }
    .status-flagged { background: #fee2e2; color: #991b1b; }
    .status-pending { background: #fef3c7; color: #92400e; }
</style>

<section class="content">

    <div class="no-print" style="margin-bottom: 16px;">
        <a href="{{ url('vehicles/'.$vehicle->id) }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back to Vehicle Details
        </a>
        <a href="{{ url('vehicles/'.$vehicle->id.'/report') }}" class="btn btn-info btn-sm">
            <i class="fa fa-file-pdf-o"></i> Full Executive Report
        </a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">
            <i class="fa fa-print"></i> Print Compliance Report
        </button>
    </div>

    <div class="report-card" style="background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:24px; margin-bottom:24px;">
        <div class="report-header">
            <h2 class="report-title"><i class="fa fa-shield-alt"></i> PEP & SANCTIONS SCREENING REPORT</h2>
            <div class="report-meta">
                Generated: {{ \Carbon\Carbon::now()->format('d M Y, h:i A') }} |
                Vehicle: {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->registration_number ?? 'N/A' }}) |
                Client: {{ $loan->client->first_name ?? '' }} {{ $loan->client->last_name ?? '' }}
            </div>
        </div>
    </div>

    @include('motor_vehicle.partials.report-section-compliance', ['loan' => $loan])

</section>

@endsection
