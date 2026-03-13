@extends('layouts.master')

@section('title')
    {{ $case->case_number ?? 'Case Detail' }}
@endsection

@push('scripts')
<script>
document.querySelectorAll('.nudge-ch').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.nudge-ch').forEach(b => {
            b.classList.remove('btn-warning', 'active');
            b.classList.add('btn-default');
        });
        this.classList.remove('btn-default');
        this.classList.add('btn-warning', 'active');
        document.getElementById('nudge-channel-input').value = this.dataset.channel;
    });
});
</script>
@endpush

@section('content')
@php
    $categories = \App\Models\RecoveryCase::CATEGORIES;

    $clientName = ($case->client->client_type ?? '') === 'business'
        ? ($case->client->full_name ?? '—')
        : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—');

    $loanRef = $case->loan->loan_id ?? ('Loan #' . $case->loan_id);
@endphp

{{-- KPI Boxes --}}
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-aqua"><i class="fa fa-bank"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Outstanding</span>
                <span class="info-box-number">K {{ number_format($case->loan_outstanding_amount, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Recovered</span>
                <span class="info-box-number">K {{ number_format($case->amount_recovered, 2) }}</span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $case->case_number }}</h3>
            </div>
            <div class="box-body">
                <p><strong>Client:</strong> {{ $clientName }}</p>
                <p><strong>Loan:</strong> {{ $loanRef }}</p>
                <p><strong>Status:</strong> {{ $case->status }}</p>
                <p><strong>Category:</strong> {{ $categories[$case->category] ?? $case->category }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <a href="{{ url('recovery/case/data') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Cases
        </a>
    </div>
</div>

@endsection
