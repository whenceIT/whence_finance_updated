@extends('layouts.master')

@section('title')
    Cash Balance &amp; Mobile Money Audit Results
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">

        {{-- ── Page header ──────────────────────────────────────────────── --}}
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-money" style="margin-right:6px;"></i>
                    Cash Balance &amp; Mobile Money Audit Results
                </h3>
                <div class="box-tools pull-right">
                    <span class="label label-default" style="font-size:12px;padding:5px 10px;">
                        {{ $submissions->total() }} {{ $submissions->total() === 1 ? 'submission' : 'submissions' }}
                    </span>
                </div>
            </div>

            <div class="box-body">

                {{-- ── Summary stat boxes ──────────────────────────────── --}}
                <div class="row" style="margin-bottom:20px;">
                    <div class="col-sm-3">
                        <div class="small-box bg-aqua">
                            <div class="inner">
                                <h3>{{ $totalCount }}</h3>
                                <p>Total Submissions</p>
                            </div>
                            <div class="icon"><i class="fa fa-file-text-o"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>K {{ number_format($totalCash, 2) }}</h3>
                                <p>Total Cash Reported</p>
                            </div>
                            <div class="icon"><i class="fa fa-money"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>K {{ number_format($totalPettyCash, 2) }}</h3>
                                <p>Total Petty Cash</p>
                            </div>
                            <div class="icon"><i class="fa fa-briefcase"></i></div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="small-box bg-purple" style="background:#8e44ad!important;">
                            <div class="inner">
                                <h3>{{ $uniqueOffices }}</h3>
                                <p>Offices Responded</p>
                            </div>
                            <div class="icon"><i class="fa fa-building"></i></div>
                        </div>
                    </div>
                </div>

                {{-- ── Filters ──────────────────────────────────────────── --}}
                <form method="GET" action="{{ route('risk.cash-audit.results') }}" id="caFiltersForm">
                    <div class="row" style="margin-bottom:16px;">
                        <div class="col-sm-3">
                            <select name="office_id" class="form-control input-sm" onchange="this.form.submit()">
                                <option value="">All Offices</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <input type="text" name="search" class="form-control input-sm"
                                   placeholder="Search branch / DM / mobile…"
                                   value="{{ request('search') }}"
                                   onchange="this.form.submit()">
                        </div>
                        <div class="col-sm-2">
                            <input type="date" name="from" class="form-control input-sm"
                                   value="{{ request('from') }}"
                                   onchange="this.form.submit()">
                        </div>
                        <div class="col-sm-2">
                            <input type="date" name="to" class="form-control input-sm"
                                   value="{{ request('to') }}"
                                   onchange="this.form.submit()">
                        </div>
                        <div class="col-sm-2">
                            <a href="{{ route('risk.cash-audit.results') }}" class="btn btn-default btn-sm btn-block">
                                <i class="fa fa-times"></i> Clear
                            </a>
                        </div>
                    </div>
                </form>

                {{-- ── Results table ────────────────────────────────────── --}}
                @if($submissions->isEmpty())
                    <div class="text-center text-muted" style="padding:40px 0;">
                        <i class="fa fa-inbox fa-3x" style="margin-bottom:12px;display:block;opacity:.4;"></i>
                        No submissions found{{ request()->hasAny(['office_id','search','from','to']) ? ' for the selected filters' : '' }}.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-striped" style="font-size:13px;">
                            <thead>
                                <tr style="background:#1a3a6b;color:#fff;">
                                    <th style="width:40px;">#</th>
                                    <th>Office</th>
                                    <th>Branch Name</th>
                                    <th>District Manager</th>
                                    <th>Cash Count Date/Time</th>
                                    <th class="text-right">Cash Total</th>
                                    <th class="text-right">Petty Cash</th>
                                    <th>Petty via Wallet</th>
                                    <th>USSD Reference</th>
                                    <th>Mobile Number</th>
                                    <th>SIM Holder</th>
                                    <th>DM Using SIM</th>
                                    <th>Submitted By</th>
                                    <th>Submitted At</th>
                                    <th style="width:36px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($submissions as $i => $s)
                                <tr>
                                    <td>{{ $submissions->firstItem() + $i }}</td>
                                    <td>{{ $s->office->name ?? '—' }}</td>
                                    <td>{{ $s->branch_name }}</td>
                                    <td>{{ $s->district_manager_name }}</td>
                                    <td>
                                        @if($s->cash_count_datetime)
                                            {{ \Carbon\Carbon::parse($s->cash_count_datetime)->format('d M Y, H:i') }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <strong>K {{ number_format($s->cash_total, 2) }}</strong>
                                    </td>
                                    <td class="text-right">K {{ number_format($s->petty_total, 2) }}</td>
                                    <td class="text-center">
                                        @if($s->petty_via_mobile_wallet)
                                            <span class="label label-info">Mobile Wallet</span>
                                        @else
                                            <span class="label label-default">Cash</span>
                                        @endif
                                    </td>
                                    <td><code>{{ $s->mobile_ussd_reference }}</code></td>
                                    <td>{{ $s->mobile_number }}</td>
                                    <td>{{ $s->sim_registered_name }}</td>
                                    <td>{{ $s->dm_using_sim }}</td>
                                    <td>
                                        @if($s->user)
                                            {{ $s->user->first_name }} {{ $s->user->last_name }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">
                                        {{ $s->created_at->format('d M Y') }}<br>
                                        <small class="text-muted">{{ $s->created_at->format('H:i') }}</small>
                                    </td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-xs btn-default"
                                                title="View denomination breakdown"
                                                onclick="caShowBreakdown({{ $s->id }})">
                                            <i class="fa fa-list"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div style="margin-top:12px;">
                        {{ $submissions->links() }}
                    </div>
                @endif

            </div>{{-- /box-body --}}
        </div>{{-- /box --}}

    </div>
</div>

{{-- ── Denomination breakdown modal ─────────────────────────────────────── --}}
<div class="modal fade" id="caBreakdownModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header" style="background:linear-gradient(135deg,#1a3a6b,#0d2247);border:none;">
                <button type="button" class="close" data-dismiss="modal"
                        style="color:#fff;opacity:.8;">&times;</button>
                <h4 class="modal-title text-white">
                    <i class="fa fa-list"></i> Denomination Breakdown
                </h4>
            </div>

            <div class="modal-body" id="caBreakdownBody">
                <div class="text-center text-muted" style="padding:20px;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>

{{-- ── Embed all submission denomination data for the breakdown modal ───── --}}
@php
    $caBreakdownData = $submissions->map(function($s) {
        return [
            'id'            => $s->id,
            'branch_name'   => $s->branch_name,
            'cash'  => [
                5   => $s->cash_5,
                10  => $s->cash_10,
                20  => $s->cash_20,
                50  => $s->cash_50,
                100 => $s->cash_100,
                200 => $s->cash_200,
                500 => $s->cash_500,
            ],
            'cash_total'  => $s->cash_total,
            'petty' => [
                5   => $s->petty_5,
                10  => $s->petty_10,
                20  => $s->petty_20,
                50  => $s->petty_50,
                100 => $s->petty_100,
                200 => $s->petty_200,
                500 => $s->petty_500,
            ],
            'petty_total'            => $s->petty_total,
            'petty_via_mobile_wallet'=> $s->petty_via_mobile_wallet,
        ];
    })->keyBy('id');
@endphp

<script>
var CA_BREAKDOWN = @json($caBreakdownData);
var DENOMS = [5, 10, 20, 50, 100, 200, 500];

function caShowBreakdown(id) {
    var s = CA_BREAKDOWN[id];
    if (!s) return;

    function denomRow(denom, qty, type) {
        if (!qty || qty == 0) return '';
        var value = (parseFloat(qty) * denom).toLocaleString('en-US', {minimumFractionDigits: 2});
        return '<tr>' +
               '<td>K' + denom + '</td>' +
               '<td class="text-right">' + parseFloat(qty).toLocaleString() + '</td>' +
               '<td class="text-right"><strong>K ' + value + '</strong></td>' +
               '</tr>';
    }

    function denomTable(data, total, viaWallet) {
        if (viaWallet) {
            return '<div class="alert alert-info" style="margin:0;font-size:13px;">' +
                   '<i class="fa fa-mobile"></i> Petty cash held via mobile wallet — see mobile money details.' +
                   '</div>';
        }
        var rows = '';
        DENOMS.forEach(function(d) { rows += denomRow(d, data[d], 'cash'); });
        if (!rows) rows = '<tr><td colspan="3" class="text-muted text-center">No denominations entered</td></tr>';
        return '<table class="table table-condensed table-bordered" style="margin:0;">' +
               '<thead><tr><th>Denom</th><th class="text-right">Qty</th><th class="text-right">Value</th></tr></thead>' +
               '<tbody>' + rows + '</tbody>' +
               '<tfoot><tr style="background:#f0fdf4;">' +
               '<td colspan="2"><strong>Total</strong></td>' +
               '<td class="text-right"><strong>K ' + parseFloat(total).toLocaleString('en-US', {minimumFractionDigits: 2}) + '</strong></td>' +
               '</tr></tfoot></table>';
    }

    var html =
        '<h5 style="margin-top:0;font-weight:700;color:#1a3a6b;border-bottom:2px solid #e2e8f0;padding-bottom:8px;">' +
        '<i class="fa fa-money"></i> Physical Cash — ' + s.branch_name + '</h5>' +
        denomTable(s.cash, s.cash_total, false) +

        '<h5 style="margin-top:18px;font-weight:700;color:#1a3a6b;border-bottom:2px solid #e2e8f0;padding-bottom:8px;">' +
        '<i class="fa fa-briefcase"></i> Petty Cash</h5>' +
        denomTable(s.petty, s.petty_total, s.petty_via_mobile_wallet);

    $('#caBreakdownBody').html(html);
    $('#caBreakdownModal').modal('show');
}
</script>

@endsection
