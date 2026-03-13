@extends('layouts.master')

@section('title')
    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email }} — Specialist
@endsection

@section('content')
@php
    $categories     = \App\Models\RecoveryCase::CATEGORIES;
    $specialistName = trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email;
@endphp

<div class="row">
    <div class="col-sm-8">
        <h4 style="margin:0 0 4px">
            <i class="fa fa-user"></i> {{ $specialistName }}
        </h4>
        <small class="text-muted">{{ $user->email }}</small>
    </div>
    <div class="col-sm-4 text-right">
        <a href="{{ url('recovery/specialist/data') }}" class="btn btn-default btn-sm">
            <i class="fa fa-arrow-left"></i> Back
        </a>
    </div>
</div>
<br>

<div class="row">
<div class="col-md-8">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-folder-open"></i> Assigned Cases</h3>
        </div>
        <div class="box-body no-padding">
            <table class="table table-hover table-striped" style="margin-bottom:0">
                <thead>
                    <tr>
                        <th>Case #</th>
                        <th>Client</th>
                        <th>Outstanding</th>
                        <th>Recovered</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cases as $case)
                <tr>
                    <td><small>{{ $case->case_number }}</small></td>
                    <td>{{ ($case->client->client_type ?? '') === 'business' ? ($case->client->full_name ?? '—') : (trim(($case->client->first_name ?? '') . ' ' . ($case->client->last_name ?? '')) ?: '—') }}</td>
                    <td>K {{ number_format($case->loan_outstanding_amount, 2) }}</td>
                    <td>K {{ number_format($case->amount_recovered, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted" style="padding:24px">
                        No cases assigned.
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="col-md-4">
    <div class="box box-warning">
        <div class="box-header with-border">
            <h3 class="box-title"><i class="fa fa-bullseye"></i> Set Target</h3>
        </div>
        <div class="box-body">
            <form method="POST" action="{{ url('recovery/specialist/' . $user->id . '/target/store') }}">
                @csrf
                <div class="form-group">
                    <label>Month</label>
                    <select name="month" class="form-control input-sm">
                        <option value="{{ now()->month }}">This Month</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Target Amount (K)</label>
                    <input type="number" name="target_amount" class="form-control input-sm"
                           step="0.01" min="0">
                </div>
                <button type="submit" class="btn btn-warning btn-block">
                    <i class="fa fa-save"></i> Save Target
                </button>
            </form>
        </div>
    </div>
</div>
</div>

@endsection
