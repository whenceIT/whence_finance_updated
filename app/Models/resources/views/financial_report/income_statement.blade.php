@extends('layouts.master')

@section('title')
    {{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}
@endsection

@section('content')
<style type="text/css">
    .style-0 { empty-cells: show; table-layout: fixed; width: 875pt }
    .style-1 { color:#fff;padding-left:10pt;font-size:14pt;font-family:"Arial";font-weight:bold;text-align:left;background:#339933 }
    .style-10{ color:#000;font-size:10pt;font-family:"Arial";font-weight:bold;font-style:italic;text-align:left;background:#ccc }
    .style-11{ color:#000;padding-right:5pt;font-size:10pt;font-family:"Arial";font-weight:bold;font-style:italic;text-align:right;background:#ccc }
    .style-12{ border-top:1pt solid #000 }
    .style-13{ color:#000;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:right }
    .style-14{ color:#000;padding-right:5pt;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:right }
    .style-15{ color:#000;padding-right:5pt;font-size:9pt;font-family:"Arial";text-align:right }
    .style-16{ color:#2f2c35;font-size:9pt;font-family:"Arial";text-align:left }
    .style-2 { color:#000;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:left }
    .style-3 { color:#000;font-size:10pt;font-family:"Arial";text-align:left }
    .style-4 { color:#000;padding-right:5pt;font-size:10pt;font-family:"Arial";text-align:right }
    .style-5 { color:#2f2c35;font-size:10pt;font-family:"Arial";text-align:right }
    .style-6 { color:#fff;padding-left:5pt;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:left;background:#999 }
    .style-7 { color:#fff;padding-left:5pt;padding-right:5pt;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:left;background:#999 }
    .style-8 { color:#fff;padding-left:5pt;padding-right:5pt;font-size:10pt;font-family:"Arial";font-weight:bold;text-align:center;background:#999 }
    .style-9 { color:#000;font-size:13pt;font-family:"Arial";font-weight:bold;text-align:center }
</style>

<div class="box box-primary">
    <div class="box-body hidden-print">
        <form method="post" action="{{ Request::url() }}" class="form-horizontal" enctype="multipart/form-data">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="end_date" class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                <div class="col-md-3">
                    <input type="text" name="end_date" class="form-control date-picker" value="{{ $end_date }}" required id="end_date">
                </div>
            </div>

            <div class="form-group">
                <label for="office_id" class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                <div class="col-md-3">
                    <select name="office_id" class="form-control select2" id="office_id" required>
                        <option></option>
                        <?php $branch = Sentinel::getUser()->office_id; ?>
                        @if(Sentinel::hasAccess('offices.view'))
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{ $key->id }}" {{ (isset($office_id) && $office_id == $key->id)?'selected':'' }}>
                                    {{ $key->name }}
                                </option>
                            @endforeach
                        @else
                            @foreach(\App\Models\Office::where('id',$branch)->get() as $key)
                                <option value="{{ $key->id }}" {{ (isset($office_id) && $office_id == $key->id)?'selected':'' }}>
                                    {{ $key->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-2"></label>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!</button>
                    <a href="{{ Request::url() }}" class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>
                    <div class="btn-group">
                        <button type="button" class="btn bg-blue dropdown-toggle legitRipple" data-toggle="dropdown">
                            {{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                            <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            {{-- add export options here if needed --}}
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@php
    use Carbon\Carbon;
@endphp

@if(!empty($end_date))
    @php
        // Resolve office and date boundaries
        $office     = \App\Models\Office::find($office_id);
        $officeOpen = $office ? Carbon::parse($office->opening_date)->startOfDay() : Carbon::minValue();
        $end        = Carbon::parse($end_date)->endOfDay();
        $mtdStart   = $end->copy()->startOfMonth();
        $ytdStart   = $end->copy()->startOfYear();

        // Totals
        $total_income = 0;
        $total_expenses = 0;
        $op_total_income = 0;
        $op_total_expenses = 0;
    @endphp

    <div class="box box-primary">
        <div class="box-body table-responsive">
            <section class="invoice">
                <div class="row">
                    <div class="col-xs-12">
                        <h2 class="page-header">
                            {{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}.
                            <small class="pull-right">As at {{ $end->toDateString() }}</small>
                        </h2>

                        <div class="row invoice-info">
                            <div class="col-sm-4 invoice-col">
                                Office:
                                <address>
                                    <strong>
                                        @if($office_id!=0 && $office) {{ $office->name }} @endif
                                    </strong><br>
                                </address>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="row">
                    <div class="col-xs-12 table-responsive">
                        <table cellspacing="0" cellpadding="0" class="table table-striped">
                            <tbody>
                            <tr style="height:20pt">
                                <td colspan="2" class="style-6">{{trans_choice('general.gl_code',1)}}</td>
                                <td colspan="3" class="style-7">{{trans_choice('general.account',1)}}</td>
                                <td colspan="6" class="style-7">{{trans_choice('general.office',1)}}</td>
                                <td colspan="5" class="style-8">Month to Date</td>
                                <td colspan="5" class="style-8">Year to Date</td>
                            </tr>

                            <tr style="height:20pt">
                                <td></td>
                                <td colspan="16" class="style-9">{{trans_choice('general.income',1)}}</td>
                            </tr>

                            {{-- INCOME ACCOUNTS --}}
                            @foreach(\App\Models\GlAccount::where('account_type','income')->orderBy('gl_code','asc')->get() as $key)
                                @php
                                    // Reset per-account accumulators
                                    $balance = 0; $xbalance = 0; $op_balance = 0;
                                    $op_dr = 0; $op_cr = 0;   // opening components if you store them per row

                                    $dr = 0; $cr = 0;         // YTD
                                    $xdr = 0; $xcr = 0;       // MTD

                                    // ----- YTD (start of year to end_date) -----
                                    $journalsYTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$ytdStart, $end])
                                        ->get();

                                    foreach ($journalsYTD as $j) {
                                        $cr    += $j->credit;
                                        $dr    += $j->debit;
                                        $op_dr += $j->op_balance_dr;
                                        $op_cr += $j->op_balance_cr;
                                    }

                                    // Optional: include year_end entries that occurred within YTD
                                    $yeYTDcr = 0; $yeYTDr = 0;
                                    $yearEndYTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('transaction_type','year_end')
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$ytdStart, $end])
                                        ->get();
                                    foreach ($yearEndYTD as $j) {
                                        $yeYTDcr += $j->credit;
                                        $yeYTDr  += $j->debit;
                                        $op_dr   += $j->op_balance_dr;
                                        $op_cr   += $j->op_balance_cr;
                                    }

                                    // ----- MTD (start of month to end_date) -----
                                    $journalsMTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$mtdStart, $end])
                                        ->get();

                                    foreach ($journalsMTD as $j) {
                                        $xcr += $j->credit;
                                        $xdr += $j->debit;
                                    }

                                    // Optional: include year_end entries that occurred within MTD
                                    $yeMTDcr = 0; $yeMTDdr = 0;
                                    $yearEndMTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('transaction_type','year_end')
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$mtdStart, $end])
                                        ->get();
                                    foreach ($yearEndMTD as $j) {
                                        $yeMTDcr += $j->credit;
                                        $yeMTDdr += $j->debit;
                                    }

                                    // Income natural sign: credit - debit
                                    $balance    = ($cr - $dr) + ($yeYTDcr - $yeYTDr);     // YTD
                                    $xbalance   = ($xcr - $xdr) + ($yeMTDcr - $yeMTDdr);  // MTD
                                    $op_balance = ($op_cr - $op_dr) + $balance;           // Opening + YTD

                                    $total_income    += $xbalance;
                                    $op_total_income += $op_balance;
                                @endphp

                                <tr style="height:15pt">
                                    <td colspan="2" class="style-3">{{ $key->gl_code }}</td>
                                    <td colspan="3" class="style-3">{{ $key->name }}</td>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="style-4">{{ number_format($xbalance,2) }}</td>
                                    <td colspan="4" class="style-4">{{ number_format($op_balance,2) }}</td>
                                </tr>
                            @endforeach

                            <tr style="height:1pt"><td class="style-12" colspan="16"></td></tr>
                            <tr style="height:1pt">
                                <td colspan="11" class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.income',1)}}</td>
                                <td colspan="2" class="style-14">{{ number_format($total_income,2) }}</td>
                                <td colspan="5" class="style-14">{{ number_format($op_total_income,2) }}</td>
                            </tr>

                            <tr style="height:0pt"><td class="style-12" colspan="16"></td></tr>

                            <tr style="height:20pt">
                                <td colspan="17" class="style-9">{{trans_choice('general.expense',2)}}</td>
                            </tr>

                            {{-- EXPENSE ACCOUNTS --}}
                            @foreach(\App\Models\GlAccount::where('account_type','expense')->orderBy('gl_code','asc')->get() as $key)
                                @php
                                    $xbalance = 0; $balance = 0; $op_balance = 0;
                                    $op_dr = 0; $op_cr = 0;

                                    $dr = 0; $cr = 0;     // YTD
                                    $xdr = 0; $xcr = 0;   // MTD

                                    // ----- YTD -----
                                    $journalsYTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$ytdStart, $end])
                                        ->get();

                                    foreach ($journalsYTD as $j) {
                                        $cr    += $j->credit;
                                        $dr    += $j->debit;
                                        $op_dr += $j->op_balance_dr;
                                        $op_cr += $j->op_balance_cr;
                                    }

                                    // Year-end entries within YTD
                                    $yexcr = 0; $yexdr = 0;
                                    $yearexYTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('transaction_type','year_end')
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$ytdStart, $end])
                                        ->get();

                                    foreach ($yearexYTD as $yexjournal) {
                                        $yexcr += $yexjournal->credit;
                                        $yexdr += $yexjournal->debit;
                                        $op_dr += $yexjournal->op_balance_dr; // fixed
                                        $op_cr += $yexjournal->op_balance_cr; // fixed
                                    }

                                    // ----- MTD -----
                                    $journalsMTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$mtdStart, $end])
                                        ->get();

                                    foreach ($journalsMTD as $j) {
                                        $xcr += $j->credit;
                                        $xdr += $j->debit;
                                    }

                                    // Year-end entries within MTD
                                    $yemxcr = 0; $yemxdr = 0;
                                    $yearEndMTD = \App\Models\GlJournalEntry::where('gl_account_id',$key->id)
                                        ->where('transaction_type','year_end')
                                        ->where('reversed',0)
                                        ->when($office_id, function ($q) use ($office_id) {
                                            if ($office_id != 0) $q->where('office_id',$office_id);
                                        })
                                        ->whereBetween('date', [$mtdStart, $end])
                                        ->get();

                                    foreach ($yearEndMTD as $j) {
                                        $yemxcr += $j->credit;
                                        $yemxdr += $j->debit;
                                    }

                                    // Expense natural sign: debit - credit
                                    $balance    = ($dr - $cr) + ($yexdr - $yexcr);     // YTD
                                    $xbalance   = ($xdr - $xcr) + ($yemxdr - $yemxcr); // MTD
                                    $op_balance = ($op_dr - $op_cr) + $balance;

                                    $total_expenses    += $xbalance;
                                    $op_total_expenses += $op_balance;
                                @endphp

                                <tr style="height:15pt">
                                    <td colspan="2" class="style-3">{{ $key->gl_code }}</td>
                                    <td colspan="3" class="style-3">{{ $key->name }}</td>
                                    <td colspan="6"></td>
                                    <td colspan="2" class="style-4">{{ number_format($xbalance,2) }}</td>
                                    <td colspan="4" class="style-4">{{ number_format($op_balance,2) }}</td>
                                </tr>
                            @endforeach

                            <tr style="height:1pt"><td class="style-12" colspan="16"></td></tr>
                            <tr style="height:1pt"><td class="style-12" colspan="16"></td></tr>

                            <tr style="height:20pt">
                                <td colspan="11" class="style-13">
                                    {{trans_choice('general.total',1)}} {{trans_choice('general.expense',2)}} :
                                </td>
                                <td colspan="2" class="style-14">{{ number_format($total_expenses,2) }}</td>
                                <td colspan="5" class="style-14">{{ number_format($op_total_expenses,2) }}</td>
                            </tr>

                            <tr style="height:1pt"><td class="style-12" colspan="16"></td></tr>

                            <tr style="height:18pt">
                                <td colspan="11" class="style-13">Profit/Loss :</td>
                                <td colspan="2" class="style-14">{{ number_format($total_income - $total_expenses,2) }}</td>
                                <td colspan="5" class="style-14">{{ number_format($op_total_income - $op_total_expenses,2) }}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </section>
        </div>
    </div>
@endif
@endsection

@section('footer-scripts')
@endsection
