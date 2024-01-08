<style>


    .text-left {
        text-align: left;
    }

    .text-right {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    .text-justify {
        text-align: justify;
    }

    .pull-right {
        float: right !important;
    }

    .row {
        clear: both;
        margin-right: -15px;
        margin-left: -15px;
    }

    .col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
    }

    .col-md-1, .col-md-2, .col-md-3, .col-md-4, .col-md-5, .col-md-6, .col-md-7, .col-md-8, .col-md-9, .col-md-10, .col-md-11, .col-md-12 {
        float: left;
    }

    .col-md-12 {
        width: 100%;
    }

    .col-md-11 {
        width: 91.66666667%;
    }

    .col-md-10 {
        width: 83.33333333%;
    }

    .col-md-9 {
        width: 75%;
    }

    .col-md-8 {
        width: 66.66666667%;
    }

    .col-md-7 {
        width: 58.33333333%;
    }

    .col-md-6 {
        width: 45%;
    }

    .col-md-5 {
        width: 41.66666667%;
    }

    .col-md-4 {
        width: 33.33333333%;
    }

    .col-md-3 {
        width: 25%;
    }

    .col-md-2 {
        width: 16.66666667%;
    }

    .col-md-1 {
        width: 8.33333333%;
    }

    .col-md-pull-12 {
        right: 100%;
    }

    .col-md-pull-11 {
        right: 91.66666667%;
    }

    .col-md-pull-10 {
        right: 83.33333333%;
    }

    .col-md-pull-9 {
        right: 75%;
    }

    .col-md-pull-8 {
        right: 66.66666667%;
    }

    .col-md-pull-7 {
        right: 58.33333333%;
    }

    .col-md-pull-6 {
        right: 50%;
    }

    .col-md-pull-5 {
        right: 41.66666667%;
    }

    .col-md-pull-4 {
        right: 33.33333333%;
    }

    .col-md-pull-3 {
        right: 25%;
    }

    .col-md-pull-2 {
        right: 16.66666667%;
    }

    .col-md-pull-1 {
        right: 8.33333333%;
    }

    .col-md-pull-0 {
        right: auto;
    }

    .col-md-push-12 {
        left: 100%;
    }

    .col-md-push-11 {
        left: 91.66666667%;
    }

    .col-md-push-10 {
        left: 83.33333333%;
    }

    .col-md-push-9 {
        left: 75%;
    }

    .col-md-push-8 {
        left: 66.66666667%;
    }

    .col-md-push-7 {
        left: 58.33333333%;
    }

    .col-md-push-6 {
        left: 50%;
    }

    .col-md-push-5 {
        left: 41.66666667%;
    }

    .col-md-push-4 {
        left: 33.33333333%;
    }

    .col-md-push-3 {
        left: 25%;
    }

    .col-md-push-2 {
        left: 16.66666667%;
    }

    .col-md-push-1 {
        left: 8.33333333%;
    }

    .col-md-push-0 {
        left: auto;
    }

    .col-md-offset-12 {
        margin-left: 100%;
    }

    .col-md-offset-11 {
        margin-left: 91.66666667%;
    }

    .col-md-offset-10 {
        margin-left: 83.33333333%;
    }

    .col-md-offset-9 {
        margin-left: 75%;
    }

    .col-md-offset-8 {
        margin-left: 66.66666667%;
    }

    .col-md-offset-7 {
        margin-left: 58.33333333%;
    }

    .col-md-offset-6 {
        margin-left: 50%;
    }

    .col-md-offset-5 {
        margin-left: 41.66666667%;
    }

    .col-md-offset-4 {
        margin-left: 33.33333333%;
    }

    .col-md-offset-3 {
        margin-left: 25%;
    }

    .col-md-offset-2 {
        margin-left: 16.66666667%;
    }

    .col-md-offset-1 {
        margin-left: 8.33333333%;
    }

    .col-md-offset-0 {
        margin-left: 0;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
    }

    .table > thead > tr > th,
    .table > tbody > tr > th,
    .table > tfoot > tr > th,
    .table > thead > tr > td,
    .table > tbody > tr > td,
    .table > tfoot > tr > td {
        padding: 8px;
        line-height: 1.42857143;
        vertical-align: top;

    }

    .table > thead > tr > th {
        vertical-align: bottom;

    }

    .lead .table > tfoot > tr > td {
        border-top: none;
    }

    .lead .table > thead > tr > th {
        border-bottom: none;
    }

    .table > caption + thead > tr:first-child > th,
    .table > colgroup + thead > tr:first-child > th,
    .table > thead:first-child > tr:first-child > th,
    .table > caption + thead > tr:first-child > td,
    .table > colgroup + thead > tr:first-child > td,
    .table > thead:first-child > tr:first-child > td {
        border-top: 0;
    }

    .table > tbody + tbody {

    }

    .table .table {
        background-color: #fff;
    }

    .table-condensed > thead > tr > th,
    .table-condensed > tbody > tr > th,
    .table-condensed > tfoot > tr > th,
    .table-condensed > thead > tr > td,
    .table-condensed > tbody > tr > td,
    .table-condensed > tfoot > tr > td {
        padding: 5px;
    }

    .table-bordered {
        border: 1px solid #ddd;
        border-collapse: collapse;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > tbody > tr > th,
    .table-bordered > tfoot > tr > th,
    .table-bordered > thead > tr > td,
    .table-bordered > tbody > tr > td,
    .table-bordered > tfoot > tr > td {
        border: 1px solid #ddd;
    }

    .table-bordered > thead > tr > th,
    .table-bordered > thead > tr > td {
        border-bottom-width: 2px;
    }

    .table-striped > tbody > tr:nth-of-type(odd) {
        background-color: #f9f9f9;
    }

    .table > thead > tr > td.active,
    .table > tbody > tr > td.active,
    .table > tfoot > tr > td.active,
    .table > thead > tr > th.active,
    .table > tbody > tr > th.active,
    .table > tfoot > tr > th.active,
    .table > thead > tr.active > td,
    .table > tbody > tr.active > td,
    .table > tfoot > tr.active > td,
    .table > thead > tr.active > th,
    .table > tbody > tr.active > th,
    .table > tfoot > tr.active > th {
        background-color: #f5f5f5;
    }

    .table-hover > tbody > tr > td.active:hover,
    .table-hover > tbody > tr > th.active:hover,
    .table-hover > tbody > tr.active:hover > td,
    .table-hover > tbody > tr:hover > .active,
    .table-hover > tbody > tr.active:hover > th {
        background-color: #e8e8e8;
    }

    .table > thead > tr > td.success,
    .table > tbody > tr > td.success,
    .table > tfoot > tr > td.success,
    .table > thead > tr > th.success,
    .table > tbody > tr > th.success,
    .table > tfoot > tr > th.success,
    .table > thead > tr.success > td,
    .table > tbody > tr.success > td,
    .table > tfoot > tr.success > td,
    .table > thead > tr.success > th,
    .table > tbody > tr.success > th,
    .table > tfoot > tr.success > th {
        background-color: #dff0d8;
    }

    .table-hover > tbody > tr > td.success:hover,
    .table-hover > tbody > tr > th.success:hover,
    .table-hover > tbody > tr.success:hover > td,
    .table-hover > tbody > tr:hover > .success,
    .table-hover > tbody > tr.success:hover > th {
        background-color: #d0e9c6;
    }

    .table > thead > tr > td.info,
    .table > tbody > tr > td.info,
    .table > tfoot > tr > td.info,
    .table > thead > tr > th.info,
    .table > tbody > tr > th.info,
    .table > tfoot > tr > th.info,
    .table > thead > tr.info > td,
    .table > tbody > tr.info > td,
    .table > tfoot > tr.info > td,
    .table > thead > tr.info > th,
    .table > tbody > tr.info > th,
    .table > tfoot > tr.info > th {
        background-color: #d9edf7;
    }

    .table-hover > tbody > tr > td.info:hover,
    .table-hover > tbody > tr > th.info:hover,
    .table-hover > tbody > tr.info:hover > td,
    .table-hover > tbody > tr:hover > .info,
    .table-hover > tbody > tr.info:hover > th {
        background-color: #c4e3f3;
    }

    .table > thead > tr > td.warning,
    .table > tbody > tr > td.warning,
    .table > tfoot > tr > td.warning,
    .table > thead > tr > th.warning,
    .table > tbody > tr > th.warning,
    .table > tfoot > tr > th.warning,
    .table > thead > tr.warning > td,
    .table > tbody > tr.warning > td,
    .table > tfoot > tr.warning > td,
    .table > thead > tr.warning > th,
    .table > tbody > tr.warning > th,
    .table > tfoot > tr.warning > th {
        background-color: #fcf8e3;
    }

    .table-hover > tbody > tr > td.warning:hover,
    .table-hover > tbody > tr > th.warning:hover,
    .table-hover > tbody > tr.warning:hover > td,
    .table-hover > tbody > tr:hover > .warning,
    .table-hover > tbody > tr.warning:hover > th {
        background-color: #faf2cc;
    }

    .table > thead > tr > td.danger,
    .table > tbody > tr > td.danger,
    .table > tfoot > tr > td.danger,
    .table > thead > tr > th.danger,
    .table > tbody > tr > th.danger,
    .table > tfoot > tr > th.danger,
    .table > thead > tr.danger > td,
    .table > tbody > tr.danger > td,
    .table > tfoot > tr.danger > td,
    .table > thead > tr.danger > th,
    .table > tbody > tr.danger > th,
    .table > tfoot > tr.danger > th {
        background-color: #f2dede;
    }

    .table-hover > tbody > tr > td.danger:hover,
    .table-hover > tbody > tr > th.danger:hover,
    .table-hover > tbody > tr.danger:hover > td,
    .table-hover > tbody > tr:hover > .danger,
    .table-hover > tbody > tr.danger:hover > th {
        background-color: #ebcccc;
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
        border-collapse: collapse;
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
</style>


<div>
    <h3 class="text-center">
        @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
            <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                 class="img-responsive" width="150"/>

        @endif
    </h3>
    <h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b>
    </h3>

    <h3 class="text-center">
        <b>    <span class="">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    {{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    {{$loan->client->full_name}}
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>{{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}</b>
    </h3>
    <hr>
    <div class="row">
      
 
    </div>

    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px; clear: both">
    <table id="repayments-data-table"
                                                   class="table  table-condensed table-hover">
                                                <thead>
                                                    
                                                <tr>
                                                   
                                                    <th>
                                                        {{trans_choice('general.date',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                                                    </th>
                                                    <th>
                                                    {{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}
                                                    </th>

                                                    <th>
                                                        {{trans_choice('general.debit',1)}} [ZMK]
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.credit',1)}} [ZMK]
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.balance',1)}}  [ZMK]
                                                    </th>
                                                   
                                                </tr>
                                                </thead>
                                                <tbody>
                                           


                                                <?php
                                                $total_dr = 0;
                                                $total_cr = 0;
                                                $balance = 0;
                                                $dr = 0;
                                                $cr = 0;
                                                ?>
                                                @foreach(\App\Models\LoanTransaction::where('loan_id',$loan->id)->whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->get() as $key)
                                                    <?php
                                                   
                                                    $cr = $cr + $key->credit;
                                                    $dr = $dr + $key->debit;
                                                    $total_dr = $total_dr + $key->debit;
                                                    $total_cr = $total_cr + $key->credit;
                                                    $balance = $balance + ($key->debit - $key->credit);
                                                    
                                                    ?>
                                                    <tr>
                                                        
                                                        <td>{{ Carbon\Carbon::parse($key->date)->format('d/m/Y') }}</td>
                                                        <td>{{ Carbon\Carbon::parse($key->created_at)->format('d/m/Y') }}</td>
                                                        <td>
                                                            @if($key->transaction_type=='disbursement')
                                                                {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='specified_due_date_fee')
                                                                {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='installment_fee')
                                                                {{trans_choice('general.installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_installment_fee')
                                                                {{trans_choice('general.overdue_installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='loan_rescheduling_fee')
                                                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_maturity')
                                                                {{trans_choice('general.overdue_maturity',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='disbursement_fee')
                                                                {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment')
                                                                {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='penalty')
                                                                {{trans_choice('general.penalty',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest_waiver')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='waiver')
                                                                {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='')
                                                                {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='charge_waiver')
                                                                {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='write_off')
                                                                {{trans_choice('general.write_off',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment_disbursement')
                                                                {{trans_choice('general.repayment',1)}} {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='adjusted_interest')
                                                                Reloan Interest
                                                            @endif
                                                            @if($key->transaction_type=='write_off_recovery')
                                                                {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->reversed==1)
                                                                @if($key->reversal_type=="user")
                                                                    <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif
                                                                @if($key->reversal_type=="system")
                                                                    <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif

                                                            @endif
                                                         
                                                        </td>
                                                        <td>{{number_format($key->debit,2)}}</td>
                                                        <td>{{number_format($key->credit,2)}}</td>
                                                        <td>{{number_format($balance,2)}}</td>
                                                        
                                                       
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                                <tr >
                    
                    <th></th>
                    <th align="left" class="style-3"><b>TOTALS</b></th>
                    <th align="left"></th>
                
                    <th align="right" style="text-align: left" class="style-3"><b>{{ number_format($total_dr,2) }}</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b>{{ number_format($total_cr,2) }}</b></th>
                    <th align="right" style="text-align: left" class="style-3"><b></b></th>
                  </tr>
                    <tr style="height: 2pt">
                        <td class="style-8" colspan="8"></td>
                    </tr>
                                            </table>
    </div>
</div>
<script>
    window.onload = function () {
        window.print();
    }
</script>