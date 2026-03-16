@extends('layouts.master')
@section('title')
    {{ trans_choice('general.closed',1) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.report',1) }}
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            {{ trans_choice('general.closed',1) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.report',1) }}
        </h3>
    </div>

    <div class="box-body hidden-print">
        <form method="post" action="{{Request::url()}}" class="form-horizontal">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="start_date" class="control-label col-md-2">{{ trans_choice('general.start',1) }} {{ trans_choice('general.date',1) }}</label>
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                </div>
                <label for="end_date" class="control-label col-md-2">{{ trans_choice('general.end',1) }} {{ trans_choice('general.date',1) }}</label>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">{{ trans_choice('general.search',1) }}</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(!empty($data) && count($data) > 0)
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
            font-size: 10px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .style-1 {
            background-color: #339933;
            color: white;
            font-size: 14pt;
            font-weight: bold;
            padding-left: 10pt;
            font-family: Arial;
        }

        .style-2 {
            font-size: 8pt;
            font-family: Arial;
            font-weight: bold;
            color: black;
        }

        .style-3 {
            font-size: 8pt;
            font-family: "Arial Narrow";
            text-align: right;
            color: black;
        }
    </style>

    <div class="box box-primary">
        <div class="panel-body table-responsive">
            <table class="table">
                <tbody>
                <tr style="height: 25pt">
                    <td colspan="12" class="style-1">{{ trans_choice('general.closed',1) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.report',1) }}</td>
                </tr>
                <tr style="height: 15pt">
                    <td class="style-2">{{ trans_choice('general.from',1) }}:</td>
                    <td class="style-3">{{ $start_date }}</td>
                    <td colspan="2" class="style-2">{{ trans_choice('general.report',1) }} {{ trans_choice('general.run',1) }} {{ trans_choice('general.date',1) }}:</td>
                    <td colspan="3" class="style-3">{{ date("Y-m-d H:i:s") }}</td>
                    <td colspan="5"></td>
                </tr>
                <tr style="height: 15pt">
                    <td class="style-2">{{ trans_choice('general.to',1) }}:</td>
                    <td class="style-3">{{ $end_date }}</td>
                    <td colspan="10"></td>
                </tr>

                <tr>
                    <th>{{ trans_choice('general.loan',1) }} {{ trans_choice('general.officer',1) }}</th>
                    <th>{{ trans_choice('general.office',1) }}</th>
                    <th>{{ trans_choice('general.client',1) }}</th>
                    <th>{{ trans_choice('general.phone',1) }}</th>
                    <th>{{ trans_choice('general.loan',1) }} {{ trans_choice('general.id',1) }}</th>
                    <th>{{ trans_choice('general.product',1) }}</th>
                    <th>{{ trans_choice('general.closed',1) }} {{ trans_choice('general.date',1) }}</th>
                    <th>{{ trans_choice('general.principal',1) }}</th>
                    <th>{{ trans_choice('general.paid',1) }}</th>
                    <th>{{ trans_choice('general.balance',1) }}</th>
                </tr>

                @php
                    $total_principal = 0;
                    $total_paid = 0;
                    $total_balance = 0;
                @endphp

                @foreach($data as $key)
                    @php
                        $principal = $key->repayment_schedules->sum('principal');
                        $paid = $key->repayment_schedules->sum('principal_paid') + $key->repayment_schedules->sum('interest_paid') + $key->repayment_schedules->sum('fees_paid') + $key->repayment_schedules->sum('penalty_paid');
                        $waived = $key->repayment_schedules->sum('principal_waived') + $key->repayment_schedules->sum('interest_waived') + $key->repayment_schedules->sum('fees_waived') + $key->repayment_schedules->sum('penalty_waived');
                        $written_off = $key->repayment_schedules->sum('principal_written_off') + $key->repayment_schedules->sum('interest_written_off') + $key->repayment_schedules->sum('fees_written_off') + $key->repayment_schedules->sum('penalty_written_off');
                        $total_due = $principal + $key->repayment_schedules->sum('interest') + $key->repayment_schedules->sum('fees') + $key->repayment_schedules->sum('penalty');
                        $balance = $total_due - ($paid + $waived + $written_off);

                        $total_principal += $principal;
                        $total_paid += $paid;
                        $total_balance += $balance;
                    @endphp

                    <tr>
                        <td>{{ $key->loan_officer->first_name ?? '' }} {{ $key->loan_officer->last_name ?? '' }}</td>
                        <td>{{ $key->office->name ?? '' }}</td>
                        <td>
                            @if($key->client_type == 'client' && $key->client)
                                {{ $key->client->first_name }} {{ $key->client->middle_name }} {{ $key->client->last_name }}
                            @elseif($key->client_type == 'group' && $key->group)
                                {{ $key->group->name }}
                            @endif
                        </td>
                        <td>
                            {{ $key->client->mobile ?? $key->group->mobile ?? '' }}
                        </td>
                        <td>{{ $key->id }}</td>
                        <td>{{ $key->loan_product->name ?? '' }}</td>
                        <td>{{ $key->updated_at ? $key->updated_at->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ number_format($principal, 2) }}</td>
                        <td>{{ number_format($paid, 2) }}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>

                <tfoot>
                <tr>
                    <td colspan="7"><strong>{{ trans_choice('general.total',1) }}</strong></td>
                    <td><strong>{{ number_format($total_principal, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_paid, 2) }}</strong></td>
                    <td><strong>{{ number_format($total_balance, 2) }}</strong></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif
@endsection
