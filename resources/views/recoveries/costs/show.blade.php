@extends('layouts.master')

@section('title')
    Recovery Cost #{{ $cost->id }}
@endsection

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">

        {{-- Action bar --}}
        <div style="margin-bottom:14px;display:flex;justify-content:space-between;align-items:center">
            <a href="{{ url('recovery/costs') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to List
            </a>
            <div>
                <a href="{{ url('recovery/costs/' . $cost->id . '/edit') }}" class="btn btn-warning btn-sm">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <form action="{{ url('recovery/costs/' . $cost->id . '/delete') }}"
                      method="POST" style="display:inline"
                      onsubmit="return confirm('Permanently delete this cost record?')">
                    {{ csrf_field() }}
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        {{-- Main card --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-money"></i>
                    {{ $cost->name }}
                </h3>
                <div class="box-tools">
                    <span class="label label-danger" style="font-size:14px">
                        K {{ number_format($cost->amount, 2) }}
                    </span>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-condensed">
                    <tbody>
                        <tr>
                            <th style="width:35%">Name</th>
                            <td>{{ $cost->name }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $cost->description ?: '—' }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><strong class="text-danger">K {{ number_format($cost->amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Cost Date</th>
                            <td>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : '—' }}</td>
                        </tr>
                        <tr>
                            <th>Office / Department</th>
                            <td>
                                @if($cost->office)
                                    {{ $cost->office->name }}
                                    @if($cost->office->district)
                                        <small class="text-muted">— {{ $cost->office->district->name ?? '' }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Recovery Case</th>
                            <td>
                                @if($cost->recoveryCase)
                                    <a href="{{ url('recovery/case/' . $cost->case_id . '/show') }}">
                                        {{ $cost->recoveryCase->case_number }}
                                    </a>
                                    @if($cost->recoveryCase->client)
                                        &nbsp;—&nbsp;
                                        {{ $cost->recoveryCase->client->first_name }}
                                        {{ $cost->recoveryCase->client->last_name }}
                                    @endif
                                @else
                                    <span class="text-muted">Not linked to a case</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Recorded By</th>
                            <td>
                                @if($cost->recordedBy)
                                    {{ $cost->recordedBy->first_name }} {{ $cost->recordedBy->last_name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Recorded At</th>
                            <td>{{ $cost->created_at ? $cost->created_at->format('d M Y, H:i') : '—' }}</td>
                        </tr>
                        @if($cost->updated_at && $cost->updated_at->ne($cost->created_at))
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $cost->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Linked case snapshot --}}
        @if($cost->recoveryCase)
            <div class="box box-default">
                <div class="box-header with-border">
                    <h4 class="box-title"><i class="fa fa-folder-open"></i> Linked Case Snapshot</h4>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Case #</strong><br>{{ $cost->recoveryCase->case_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Category</strong><br>
                                <span class="label label-default">
                                    {{ $cost->recoveryCase->category_label }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Status</strong><br>
                                <span class="label label-{{ $cost->recoveryCase->status_color }}">
                                    {{ ucwords(str_replace('_', ' ', $cost->recoveryCase->status)) }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Outstanding</strong><br>
                                K {{ number_format($cost->recoveryCase->loan_outstanding_amount, 2) }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Recovered</strong><br>
                                K {{ number_format($cost->recoveryCase->amount_recovered, 2) }}
                            </p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Total Case Costs</strong><br>
                                K {{ number_format(
                                    $cost->recoveryCase->recovery_costs +
                                    $cost->recoveryCase->legal_costs_incurred +
                                    $cost->recoveryCase->skip_trace_costs,
                                    2
                                ) }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ url('recovery/case/' . $cost->case_id . '/show') }}"
                       class="btn btn-default btn-sm" target="_blank">
                        <i class="fa fa-external-link"></i> View Full Case
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

@endsection
