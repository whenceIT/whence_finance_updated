<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 8px !important;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .col {
        margin-bottom: -99999px;
        padding-bottom: 99999px;
    }

    .col-wrap {
        overflow: hidden;
    }

    table.pretty {
        width: 100%;
    }

    table.pretty th, table.pretty td {
        border: 1px solid gainsboro;
        padding: 0.2em;
    }

    table.pretty caption {
        font-style: italic;
        font-weight: bold;
        margin-left: inherit;
        margin-right: inherit;
    }

    table.pretty thead tr th {
        border-bottom: 2px solid;
        font-weight: bold;
        text-align: center;
    }

    table.pretty thead tr th.empty {
        border: 0 none;
    }

    table.pretty tfoot tr th {
        border-bottom: 2px solid;
        border-top: 2px solid;
        font-weight: bold;
        text-align: center;
    }

    table.pretty tbody tr th {
        text-align: center;
    }

    table.pretty tbody tr td {
        border-top: 1px solid;
        text-align: center;
    }

    table.pretty tbody tr.odd td {
        background: none repeat scroll 0 0 #EBF4FB;
    }

    table.pretty tbody tr.even td {
        background: none repeat scroll 0 0 #BCEEEE;
    }

    table.pretty thead tr th.highlightcol {
        border-color: #2E6E9E #2E6E9E gainsboro;
        border-style: solid;
        border-width: 2px 2px 1px;
    }

    table.pretty tfoot tr th.highlightcol {
        border-left: 2px solid #2E6E9E;
        border-right: 2px solid #2E6E9E;
    }

    table.pretty thead tr th.lefthighlightcol, table.pretty tbody tr td.lefthighlightcol, table.pretty tfoot tr th.lefthighlightcol {
        border-left: 2px solid #2E6E9E;
    }

    table.pretty thead tr th.righthighlightcol, table.pretty tbody tr td.righthighlightcol, table.pretty tfoot tr th.righthighlightcol {
        border-right: 2px solid #2E6E9E;
    }

    table.pretty thead tr th.lefthighlightcolheader, table.pretty tbody tr td.lefthighlightcolheader, table.pretty tfoot tr th.lefthighlightcolheader {
        border-left: 2px solid #2E6E9E;
    }

    table.pretty thead tr th.righthighlightcolheader, table.pretty tbody tr td.righthighlightcolheader, table.pretty tfoot tr th.righthighlightcolheader {
        border-right: 2px solid #2E6E9E;
    }

    .strikethrough {
        text-decoration: line-through;
        color: red;
    }

    .month, .year {
        margin: 2px;
    }

    caption, th {
        text-align: left;
    }

    .font-11 {
        font-size: 11px;
    }

    .style-0 {
        empty-cells: show;
        table-layout: fixed;
        width: 100%
    }

    .style-1 {
        color: white;
        padding-left: 10pt;
        font-size: 14pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;;
        background-color: #339933
    }

    .style-10 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        background-color: #f2f1f1
    }

    .style-11 {
        border-top: 2pt solid black
    }

    .style-12 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        border-bottom: 2pt solid black
    }

    .style-13 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        border-bottom: 2pt solid black
    }

    .style-14 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-bottom: 2pt solid black;
        background-color: #cccccc
    }

    .style-15 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-bottom: 2pt solid black;
        background-color: #f2f1f1
    }

    .style-16 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-17 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

    .style-18 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #f2f1f1
    }

    .style-19 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 2pt solid black
    }

    .style-2 {
        color: black;
        padding-right: 5pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-20 {
        color: black;
        padding-right: 2pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 2pt solid black
    }

    .style-21 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 2pt solid black;
        background-color: #cccccc
    }

    .style-22 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 2pt solid black;
        background-color: #f2f1f1
    }

    .style-23 {
        color: black;
        font-size: 10pt;
        font-family: serif;
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-24 {
        width: 50px;
        height: 50px
    }

    .style-3 {
        color: #2f2c35;
        padding-right: 5pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-4 {
        color: #2f2c35;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-5 {
        color: black;
        padding-right: 2pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-6 {
        color: black;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-7 {
        color: black;
        padding-right: 2pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-8 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-9 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        background-color: #cccccc
    }
</style>
<div>

    <table cellspacing="0" cellpadding="0" class="style-0">

        <tbody>
        <tr style="height: 25pt">
            <td colspan="29" valign="middle"
                class="style-1"> {{trans_choice('general.portfolio',1)}} {{trans_choice('general.report',1)}}</td>

        </tr>
        <tr style="height: 13pt">
            <td colspan="5" valign="middle" class="style-2"></td>
            <td></td>
            <td colspan="4" valign="middle" class="style-2"></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="3" valign="middle"
                class="style-3">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                :
            </td>
            <td colspan="3" valign="middle" class="style-4"> {{date("Y-m-d H:i:s")}}</td>
            <td colspan="10"></td>

        </tr>
        <tr style="height: 13pt">
            <td colspan="3" valign="middle"
                class="style-2"></td>
            <td colspan="2" valign="middle" class="style-5" id="outstanding_principal"></td>
            <td></td>
            <td colspan="2" valign="middle" class="style-2"></td>
            <td colspan="2" valign="middle" class="style-6"></td>
            <td colspan="3"></td>
            <td colspan="3" valign="middle"
                class="style-3">{{trans_choice('general.report',1)}} {{trans_choice('general.date',1)}}:
            </td>
            <td colspan="3" valign="middle" class="style-6">{{$end_date}}</td>
            <td colspan="10"></td>
        </tr>
        <tr style="height: 23pt">
            <td colspan="3" valign="middle" class="style-2">{{trans_choice('general.office',1)}} :</td>
            <td colspan="3" valign="middle" class="style-7">
                @if($office_id!=0)
                    {{\App\Models\Office::find($office_id)->name}}
                @endif
            </td>
            <td colspan="24"></td>
        </tr>
        <tr style="height: 13pt">
            <td></td>
            <td colspan="7" valign="middle" class="style-8">{{trans_choice('general.portfolio',1)}}</td>
            <td colspan="3" valign="middle"
                class="style-9">{{trans_choice('general.on_time',1)}} {{trans_choice('general.loan',2)}}</td>
            <td colspan="3" valign="middle" class="style-10">1-30 {{trans_choice('general.day',2)}}</td>
            <td colspan="3" valign="middle" class="style-9">31-60 {{trans_choice('general.day',2)}}</td>
            <td colspan="3" valign="middle" class="style-10">61-90 {{trans_choice('general.day',2)}}</td>
            <td colspan="3" valign="middle" class="style-9">91-180 {{trans_choice('general.day',2)}}</td>
            <td colspan="3" valign="middle" class="style-10">More than
                180 {{trans_choice('general.day',2)}}</td>
            <td colspan="3" valign="middle"
                class="style-9">{{trans_choice('general.total',2)}} {{trans_choice('general.par',1)}}</td>
        </tr>
        <tr style="height: 13pt">
            <td valign="middle" class="style-12">{{trans_choice('general.office',1)}} :</td>
            <td valign="middle" class="style-13">NOL</td>
            <td colspan="2" valign="middle" class="style-13">P</td>
            <td valign="middle" class="style-13">I</td>
            <td valign="middle" class="style-13">F</td>
            <td valign="middle" class="style-13">P</td>
            <td valign="middle" class="style-13">{{trans_choice('general.total',1)}}</td>
            <td valign="middle" class="style-14">#</td>
            <td valign="middle" class="style-14">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-14">%</td>
            <td valign="middle" class="style-15">#</td>
            <td valign="middle" class="style-15">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-15">%</td>
            <td valign="middle" class="style-14">#</td>
            <td valign="middle" class="style-14">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-14">%</td>
            <td valign="middle" class="style-15">#</td>
            <td valign="middle" class="style-15">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-15">%</td>
            <td valign="middle" class="style-14">#</td>
            <td valign="middle" class="style-14">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-14">%</td>
            <td valign="middle" class="style-15">#</td>
            <td valign="middle" class="style-15">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-15">%</td>
            <td valign="middle" class="style-14">#</td>
            <td valign="middle" class="style-14">{{trans_choice('general.amount',1)}}</td>
            <td valign="middle" class="style-14">%</td>

        </tr>
        <?php
        $total_loans = 0;
        $total_on_time_loans = 0;
        $total_on_time_amount = 0;
        $total_p_30_loans = 0;
        $total_p_30_amount = 0;
        $total_p_60_loans = 0;
        $total_p_60_amount = 0;
        $total_p_90_loans = 0;
        $total_p_90_amount = 0;
        $total_p_180_loans = 0;
        $total_p_180_amount = 0;
        $total_p_180_plus_loans = 0;
        $total_p_180_plus_amount = 0;
        $total_p_loans = 0;
        $total_p_amount = 0;
        $total_principal = 0;
        $total_interest = 0;
        $total_fees = 0;
        $total_penalty = 0;
        $total_amount = 0;
        ?>
        @foreach($data as $key)
            <?php
            $loans = \App\Models\Loan::where('status', 'disbursed')->where('office_id', $key->id)->when($loan_product_id, function ($query) use ($loan_product_id) {
                if ($loan_product_id != 0) {
                    $query->where('loan_product_id', '=', $loan_product_id);
                }
            })->with('loan_officer')->with('office')->with('loan_product')->with('client')->with('group')->with('repayment_schedules')->get();
            $total_loans = $total_loans + count($loans);
            $on_time_loans = 0;
            $on_time_amount = 0;
            $p_30_loans = 0;
            $p_30_amount = 0;
            $p_60_loans = 0;
            $p_60_amount = 0;
            $p_90_loans = 0;
            $p_90_amount = 0;
            $p_180_loans = 0;
            $p_180_amount = 0;
            $p_180_plus_loans = 0;
            $p_180_plus_amount = 0;
            $p_loans = 0;
            $p_amount = 0;

            $amount = 0;
            $outstanding = 0;
            $principal = 0;
            $interest = 0;
            $fees = 0;
            $penalty = 0;

            $amount = 0;
            foreach ($loans as $loan) {
                $amount_in_arrears = 0;
                $days_in_arrears = 0;
                $late_count = 0;
                $schedule_principal = 0;
                foreach ($loan->repayment_schedules as $schedule) {
                    if (strtotime($schedule->due_date) < strtotime($end_date)) {
                        $amount_in_arrears = $amount_in_arrears + ($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid);
                    }
                    $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid;
                    $schedule_principal = $schedule_principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid;
                    $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid;
                    $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid;
                    $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid;
                    if ($amount_in_arrears > 0) {
                        $late_count++;
                        if ($late_count == 1) {
                            $overdue_date = $schedule->due_date;
                        }
                    }
                }

                if ($amount_in_arrears > 0) {
                    $date1 = new DateTime($overdue_date);
                    $date2 = new DateTime($end_date);
                    $days_in_arrears = $date2->diff($date1)->format("%a");
                    if ($days_in_arrears <= 30) {
                        $p_30_loans = $p_30_loans + 1;
                        $p_30_amount = $p_30_amount + $amount_in_arrears;
                    }
                    if ($days_in_arrears > 30 && $days_in_arrears <= 60) {
                        $p_60_loans = $p_60_loans + 1;
                        $p_60_amount = $p_60_amount + $amount_in_arrears;
                    }
                    if ($days_in_arrears > 60 && $days_in_arrears <= 90) {
                        $p_90_loans = $p_90_loans + 1;
                        $p_90_amount = $p_90_amount + $amount_in_arrears;
                    }
                    if ($days_in_arrears > 90 && $days_in_arrears <= 180) {
                        $p_180_loans = $p_180_loans + 1;
                        $p_180_amount = $p_180_amount + $amount_in_arrears;
                    }
                    if ($days_in_arrears > 180) {
                        $p_180_plus_loans = $p_180_plus_loans + 1;
                        $p_180_plus_amount = $p_180_plus_amount + $amount_in_arrears;
                    }

                } else {
                    $on_time_loans = $on_time_loans + 1;
                    $on_time_amount = $on_time_amount + $schedule_principal;
                }

            }
            $p_amount = $p_30_amount + $p_60_amount + $p_90_amount + $p_180_amount + $p_180_plus_amount;
            $p_loans = $p_30_loans + $p_60_loans + $p_90_loans + $p_180_loans + $p_180_plus_loans;
            $total_on_time_loans = $total_on_time_loans + $on_time_loans;
            $total_on_time_amount = $total_on_time_amount + $on_time_amount;
            $total_p_30_loans = $total_p_30_loans + $p_30_loans;
            $total_p_30_amount = $total_p_30_amount + $p_30_amount;
            $total_p_60_loans = $total_p_60_loans + $p_60_loans;
            $total_p_60_amount = $total_p_60_amount + $p_60_amount;
            $total_p_90_loans = $total_p_90_loans + $p_90_loans;
            $total_p_90_amount = $total_p_90_amount + $p_90_amount;
            $total_p_180_loans = $total_p_180_loans + $p_180_loans;
            $total_p_180_amount = $total_p_180_amount + $p_180_amount;
            $total_p_180_plus_loans = $total_p_180_plus_loans + $p_180_plus_loans;
            $total_p_180_plus_amount = $total_p_180_plus_amount + $p_180_plus_amount;
            $total_p_loans = $total_p_loans + $p_loans;
            $total_p_amount = $total_p_amount + $p_amount;
            $amount = $penalty + $fees + $principal + $interest;
            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
            $total_fees = $total_fees + $fees;
            $total_penalty = $total_penalty + $penalty;
            $total_amount = $total_amount + $penalty + $fees + $principal + $interest;


            ?>
            @if(count($loans)>0)
                <tr style="height: 13pt">
                    <td valign="middle" class="style-16">{{$key->name}}</td>
                    <td valign="middle" class="style-5">{{count($loans)}}</td>
                    <td colspan="2" valign="middle" class="style-5">{{number_format($principal,2)}}</td>
                    <td valign="middle" class="style-5">{{number_format($interest,2)}}</td>
                    <td valign="middle" class="style-5">{{number_format($fees,2)}}</td>
                    <td valign="middle" class="style-5">{{number_format($penalty,2)}}</td>
                    <td valign="middle" class="style-5">{{number_format($amount,2)}}</td>
                    <td valign="middle" class="style-17">{{$on_time_loans}}</td>
                    <td valign="middle" class="style-17">{{number_format($on_time_amount,2)}}</td>
                    <td valign="middle" class="style-17">{{round($on_time_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-18">{{$p_30_loans}}</td>
                    <td valign="middle" class="style-18">{{number_format($p_30_amount,2)}}</td>
                    <td valign="middle" class="style-18">{{round($p_30_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-17">{{$p_60_loans}}</td>
                    <td valign="middle" class="style-17">{{number_format($p_60_amount,2)}}</td>
                    <td valign="middle" class="style-17">{{round($p_60_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-18">{{$p_90_loans}}</td>
                    <td valign="middle" class="style-18">{{number_format($p_90_amount,2)}}</td>
                    <td valign="middle" class="style-18">{{round($p_90_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-17">{{$p_180_loans}}</td>
                    <td valign="middle" class="style-17">{{number_format($p_180_amount,2)}}</td>
                    <td valign="middle" class="style-17">{{round($p_180_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-18">{{$p_180_plus_loans}}</td>
                    <td valign="middle" class="style-18">{{number_format($p_180_plus_amount,2)}}</td>
                    <td valign="middle" class="style-18">{{round($p_180_plus_amount*100/$amount,2)}}%</td>
                    <td valign="middle" class="style-17">{{$p_loans}}</td>
                    <td valign="middle" class="style-17">{{number_format($p_amount,2)}}</td>
                    <td valign="middle" class="style-17">{{round($p_amount*100/$amount,2)}}%</td>

                </tr>
            @endif
        @endforeach
        <tr style="height: 13pt">
            <td valign="middle" class="style-19">{{trans_choice('general.total',1)}}</td>
            <td valign="middle" class="style-20">{{$total_loans}}</td>
            <td colspan="2" valign="middle" class="style-20">{{number_format($total_principal,2)}}</td>
            <td valign="middle" class="style-20">{{number_format($total_interest,2)}}</td>
            <td valign="middle" class="style-20">{{number_format($total_fees,2)}}</td>
            <td valign="middle" class="style-20">{{number_format($total_penalty,2)}}</td>
            <td valign="middle" class="style-20">{{number_format($total_amount,2)}}</td>
            <td valign="middle" class="style-21">{{$total_on_time_loans}}</td>
            <td valign="middle" class="style-21">{{number_format($total_on_time_amount,2)}}</td>
            <td valign="middle" class="style-21">{{round($total_on_time_amount*100/$total_amount,2)}}%</td>
            <td valign="middle" class="style-22">{{$total_p_30_loans}}</td>
            <td valign="middle" class="style-22">{{number_format($total_p_30_amount,2)}}</td>
            <td valign="middle" class="style-22">{{round($total_p_30_amount*100/$total_amount,2)}}%</td>
            <td valign="middle" class="style-21">{{$total_p_60_loans}}</td>
            <td valign="middle" class="style-21">{{number_format($total_p_60_amount,2)}}</td>
            <td valign="middle" class="style-21">{{round($total_p_60_amount*100/$total_amount,2)}}%</td>
            <td valign="middle" class="style-22">{{$total_p_90_loans}}</td>
            <td valign="middle" class="style-22">{{number_format($total_p_90_amount,2)}}</td>
            <td valign="middle" class="style-22">{{round($total_p_90_amount*100/$total_amount,2)}}%</td>
            <td valign="middle" class="style-21">{{$total_p_180_loans}}</td>
            <td valign="middle" class="style-21">{{number_format($total_p_180_amount,2)}}</td>
            <td valign="middle" class="style-21">{{round($total_p_180_amount*100/$total_amount,2)}}%</td>
            <td valign="middle" class="style-22">{{$total_p_180_plus_loans}}</td>
            <td valign="middle" class="style-22">{{number_format($total_p_180_plus_amount,2)}}</td>
            <td valign="middle" class="style-22">{{round($total_p_180_plus_amount*100/$total_amount,2)}}%
            </td>
            <td valign="middle" class="style-21">{{$total_p_loans}}</td>
            <td valign="middle" class="style-21">{{number_format($total_p_amount,2)}}</td>
            <td valign="middle" class="style-21">{{round($total_p_amount*100/$total_amount,2)}}%</td>

        </tr>
        </tbody>
    </table>
</div>