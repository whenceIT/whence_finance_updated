@extends('layouts.master')
@section('title') Bulk Repayments Reconcile @endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Reconcile Bulk Repayments</h3>
        <div class="box-tools pull-right">
            <a class="btn btn-default btn-sm" href="{{ url('loan/repayment/bulk') }}">Upload New</a>
        </div>
    </div>

    <div class="box-body">

        <p>
            <b>File:</b> {{ $batch['filename'] ?? '' }} &nbsp; | &nbsp;
            <b>Date:</b> {{ $batch['refs']['date'] ?? '' }} &nbsp; | &nbsp;
            <b>Apply To:</b> {{ $batch['refs']['payment_apply_to'] ?? '' }} &nbsp; | &nbsp;
            <b>Payment Type:</b> {{ $batch['refs']['payment_type_id'] ?? '' }}
        </p>

        @php
            $total = count($batch['rows']);
            $selected = collect($batch['rows'])->where('_selected',1)->count();
            $validSelected = collect($batch['rows'])->where('_selected',1)->where('_valid',1)->count();
            $invalidSelected = $selected - $validSelected;
        @endphp

        <div class="alert alert-info">
            <b>Total Rows:</b> {{ $total }} |
            <b>Selected:</b> {{ $selected }} |
            <b>Valid Selected:</b> {{ $validSelected }} |
            <b>Invalid Selected:</b> {{ $invalidSelected }}
        </div>

        {{-- OPTIONAL: Add row (enable only if route/controller exists)
             If you don't have bulkRowAdd route/method, keep this commented out
        --}}
        {{--
        <div class="box box-default">
            <div class="box-header with-border"><b>Add Row</b></div>
            <div class="box-body">
                <form method="post" action="{{ url('loan/repayment/bulk/row/add') }}" class="form-inline">
                    {{ csrf_field() }}
                    <input class="form-control" name="first_name" placeholder="First name" style="width:140px;">
                    <input class="form-control" name="last_name" placeholder="Last name" style="width:140px;">
                    <input class="form-control" name="identification" placeholder="Identification" style="width:160px;">
                    <input class="form-control" name="external_id" placeholder="External ID" style="width:130px;">
                    <input class="form-control" name="loan_id" placeholder="Loan ID" style="width:100px;" required>
                    <input class="form-control" name="amount" placeholder="Amount" style="width:110px;" required>
                    <button class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
        --}}

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th width="70">Use</th>
                    <th>Client (CSV)</th>
                    <th>Client (System)</th>
                    <th>Identification</th>
                    <th>External ID</th>
                    <th width="90">Loan ID</th>
                    <th width="140">Original Principal</th>
                    <th width="120">Due Count (Period)</th>
                    <th width="150">Total Due (Period)</th>
                    <th width="120">Amount</th>
                    <th>Status</th>
                    <th width="110">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($batch['rows'] as $r)
                    @php
                        $muted = ($r['_selected'] ?? 0) == 0;
                        $sysName = trim(($r['_sys_first_name'] ?? '') . ' ' . ($r['_sys_last_name'] ?? ''));
                    @endphp

                    <tr class="@if($muted) text-muted @endif">
                        {{-- Use toggle (reliable, no checkbox missing value) --}}
                        <td>
                            <form method="post" action="{{ url('loan/repayment/bulk/row/update') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $r['_id'] }}">
                                <input type="hidden" name="selected" value="{{ ($r['_selected'] ?? 0) ? 0 : 1 }}">

                                {{-- keep existing values so update doesn't wipe them --}}
                                <input type="hidden" name="first_name" value="{{ $r['first_name'] ?? '' }}">
                                <input type="hidden" name="last_name" value="{{ $r['last_name'] ?? '' }}">
                                <input type="hidden" name="identification" value="{{ $r['identification'] ?? '' }}">
                                <input type="hidden" name="external_id" value="{{ $r['external_id'] ?? '' }}">
                                <input type="hidden" name="loan_id" value="{{ $r['loan_id'] ?? '' }}">
                                <input type="hidden" name="amount" value="{{ $r['amount'] ?? '' }}">

                                <button class="btn btn-xs {{ ($r['_selected'] ?? 0) ? 'btn-success' : 'btn-default' }}" type="submit">
                                    {{ ($r['_selected'] ?? 0) ? 'Yes' : 'No' }}
                                </button>
                            </form>
                        </td>

                        {{-- Editable row --}}
                        <td>
                            <form method="post" action="{{ url('loan/repayment/bulk/row/update') }}">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $r['_id'] }}">
                                <input type="hidden" name="selected" value="{{ ($r['_selected'] ?? 0) ? 1 : 0 }}">

                                <input class="form-control" name="first_name" value="{{ $r['first_name'] ?? '' }}" style="width:110px; display:inline-block;">
                                <input class="form-control" name="last_name" value="{{ $r['last_name'] ?? '' }}" style="width:110px; display:inline-block;">
                        </td>

                        <td>
                            @if($sysName !== '')
                                <span class="label label-info">{{ $sysName }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>

                        <td>
                            <input class="form-control" name="identification" value="{{ $r['identification'] ?? '' }}" style="width:170px;">
                        </td>

                        <td>
                            <input class="form-control" name="external_id" value="{{ $r['external_id'] ?? '' }}" style="width:140px;">
                        </td>

                        <td>
                            <input class="form-control" name="loan_id" value="{{ $r['loan_id'] ?? '' }}" style="width:95px;">
                        </td>

                        <td>
                            <span class="text-muted">
                                {{ number_format((float)($r['_loan_principal_original'] ?? 0), 2) }}
                            </span>
                        </td>

                        <td>
                            <span class="text-muted">{{ (int)($r['_period_due_count'] ?? 0) }}</span>
                        </td>

                        <td>
                            <span class="text-muted">
                                {{ number_format((float)($r['_period_due_total'] ?? 0), 2) }}
                            </span>
                            @if(!empty($r['_period_due_dates']) && is_array($r['_period_due_dates']))
                                <div style="font-size:11px;" class="text-muted">
                                    {{ implode(', ', $r['_period_due_dates']) }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <input class="form-control" name="amount" value="{{ $r['amount'] ?? '' }}" style="width:110px;">
                        </td>

                        <td>
                            @if(($r['_valid'] ?? 0) == 1)
                                <span class="label label-success">VALID</span>
                            @else
                                <span class="label label-danger">INVALID</span>
                                <div style="margin-top:6px;">
                                    @foreach(($r['_errors'] ?? []) as $e)
                                        <div class="text-danger" style="font-size:12px;">• {{ $e }}</div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($r['_warnings']))
                                <div style="margin-top:6px;">
                                    @foreach($r['_warnings'] as $w)
                                        <div class="text-warning" style="font-size:12px;">• {{ $w }}</div>
                                    @endforeach
                                </div>
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-xs btn-info" type="submit">Save</button>
                            </form>

                            {{-- Exclude: uses its own endpoint --}}
                            <form method="post" action="{{ url('loan/repayment/bulk/row/remove') }}" style="margin-top:6px;">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $r['_id'] }}">
                                <button class="btn btn-xs btn-warning" type="submit">Exclude</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </div>

    <div class="box-footer">
        <form method="post" action="{{ url('loan/repayment/bulk/store') }}">
            {{ csrf_field() }}
            <button class="btn btn-primary pull-right" type="submit">Post Batch</button>
        </form>
    </div>
</div>
@endsection
