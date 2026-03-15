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
        <b>{{trans_choice('general.loan',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.schedule',1)}}</b>
    </h3>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <table class="table">
                @if($loan->client_type=="client")
                    <tr>
                        <th class="">{{trans_choice('general.client',1)}}</th>
                        <td>
                                        <span class="">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    {{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    {{$loan->client->full_name}}
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>
                        </td>
                    </tr>
                @endif
                @if($loan->client_type=="group")
                    <tr>
                        <th class="">{{trans_choice('general.group',1)}}</th>
                        <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->client))
                                                {{$loan->group->name}}
                                            @endif
                                        </span>
                        </td>
                    </tr>
                @endif
                <tr>
                    <td><span>{{trans_choice('general.loan',1)}} #</span></td>
                    <td>{{$loan->id}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.disbursed',1)}}</span></td>
                    <td>{{$loan->disbursement_date}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</span></td>
                    <td>{{$loan->expected_maturity_date}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.repayment',1)}}</span></td>
                    <td>
                        {{trans_choice('general.every',1)}} {{$loan->repayment_frequency}}
                        @if($loan->repayment_frequency_type=="days")
                            {{trans_choice('general.day',2)}}
                        @endif
                        @if($loan->repayment_frequency_type=="weeks")
                            {{trans_choice('general.week',2)}}
                        @endif
                        @if($loan->repayment_frequency_type=="months")
                            {{trans_choice('general.month',2)}}
                        @endif
                        @if($loan->repayment_frequency_type=="years")
                            {{trans_choice('general.year',2)}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.principal',1)}}</span></td>
                    <td>{{number_format($loan->principal,2)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.interest',1)}}%</span></td>
                    <td>
                        {{$loan->interest_rate}}
                        @if($loan->override_interest==0)
                            {{trans_choice('general.per',1)}}
                            @if($loan->interest_rate_type=="day")
                                {{trans_choice('general.day',1)}}
                            @endif
                            @if($loan->interest_rate_type=="week")
                                {{trans_choice('general.week',1)}}
                            @endif
                            @if($loan->interest_rate_type=="month")
                                {{trans_choice('general.month',1)}}
                            @endif
                            @if($loan->interest_rate_type=="year")
                                {{trans_choice('general.year',1)}}
                            @endif
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table">
                <?php
                $loan_allocation = \App\Helpers\GeneralHelper::loan_items($loan->id);
                $decimals = $loan->loan_product->decimals;
                ?>
                <tr>
                    <td><span>{{trans_choice('general.interest',1)}} </span></td>
                    <td>{{number_format($loan_allocation["interest"],$decimals)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.fee',2)}}</span></td>
                    <td>{{number_format($loan_allocation["fees"],$decimals)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.penalty',1)}}</span></td>
                    <td>{{number_format($loan_allocation["penalty"],$decimals)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.due',1)}}</span></td>
                    <td>{{number_format($loan_allocation["principal"]+$loan_allocation["interest"]+$loan_allocation["fees"]+$loan_allocation["penalty"],$decimals)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.paid',1)}}</span></td>
                    <td>{{number_format($loan_allocation["principal_paid"]+$loan_allocation["interest_paid"]+$loan_allocation["fees_paid"]+$loan_allocation["penalty_paid"],$decimals)}}</td>
                </tr>
                <tr>
                    <td><span>{{trans_choice('general.balance',1)}}</span></td>
                    <td>{{number_format(($loan_allocation["principal"]-$loan_allocation["principal_paid"]-$loan_allocation["principal_waived"]-$loan_allocation["principal_written_off"])+($loan_allocation["interest"]-$loan_allocation["interest_paid"]-$loan_allocation["interest_waived"]-$loan_allocation["interest_written_off"])+($loan_allocation["fees"]-$loan_allocation["fees_paid"]-$loan_allocation["fees_waived"]-$loan_allocation["fees_written_off"])+($loan_allocation["penalty"]-$loan_allocation["penalty_paid"]-$loan_allocation["penalty_waived"]-$loan_allocation["penalty_written_off"]),$decimals)}}</td>
                </tr>
            </table>
        </div>
    </div>

    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px; clear: both">
        <table class="pretty displayschedule" id="repaymentschedule"
               style="margin-top: 20px;">
            <colgroup span="3"></colgroup>
            <colgroup span="3">
                <col class="lefthighlightcol">
                <col>
                <col class="righthighlightcol">
            </colgroup>
            <colgroup span="3">
                <col class="lefthighlightcol">
                <col>
                <col class="righthighlightcol">
            </colgroup>
            <colgroup span="3"></colgroup>

            <thead>
            <tr>
                <th class="empty" scope="colgroup" colspan="4">&nbsp;</th>
                <th class="highlightcol" scope="colgroup"
                    colspan="3">{{trans_choice('general.loan',1)}} {{trans_choice('general.amount',1)}} {{trans_choice('general.and',1)}}
                    {{trans_choice('general.balance',1)}}
                </th>
                <th class="highlightcol" scope="colgroup"
                    colspan="3">{{trans_choice('general.total',1)}} {{trans_choice('general.cost',1)}} {{trans_choice('general.of',1)}} {{trans_choice('general.loan',1)}}
                </th>
                <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
            </tr>
            <tr>
                <th scope="col">#</th>
                <th scope="col">{{trans_choice('general.date',1)}}</th>
                <th scope="col">{{trans_choice('general.paid',1)}} {{trans_choice('general.by',1)}}</th>
                <th scope="col"></th>
                <th class="lefthighlightcolheader"
                    scope="col">{{trans_choice('general.disbursement',1)}}</th>
                <th scope="col">{{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}</th>
                <th class="righthighlightcolheader"
                    scope="col">{{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}}</th>

                <th class="lefthighlightcolheader"
                    scope="col">{{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}</th>
                <th scope="col">{{trans_choice('general.fee',2)}}</th>
                <th class="righthighlightcolheader"
                    scope="col">{{trans_choice('general.penalty',2)}}</th>

                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}</th>
                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.paid',1)}}</th>
                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.outstanding',1)}}</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $count = 1;
            $principal_balance = $loan->principal;
            $total_principal = 0;
            $total_interest = 0;
            $total_penalties = 0;
            $total_fees = 0;
            $total_due = 0;
            $total_paid = 0;
            $total_outstanding = 0;
            $disbursement_charges = \App\Models\LoanTransaction::where('loan_id', $loan->id)->where('transaction_type', 'disbursement_fee')->where('reversed', 0)->sum('debit');
            ?>
            <tr>
                <td scope="row"></td>
                <td>{{$loan->disbursement_date}}</td>

                <td><span style="color: #eb2442;"></span></td>
                <td>&nbsp;</td>
                <td class="lefthighlightcolheader">{{number_format($loan->principal,$loan->loan_product->decimals)}}</td>
                <td></td>
                <td class="righthighlightcolheader">{{number_format($loan->principal,$loan->loan_product->decimals)}}</td>

                <td class="lefthighlightcolheader">


                </td>
                <td>{{number_format($disbursement_charges,$loan->loan_product->decimals)}}</td>
                <td class="righthighlightcolheader"></td>

                <td></td>
                <td>{{number_format($disbursement_charges,$loan->loan_product->decimals)}}</td>
                <td></td>
            </tr>
            @foreach($loan->repayment_schedules as $key)
                <?php
                $principal_balance = $principal_balance - $key->principal - $key->principal_waived - $key->principal_written_off;
                $total_principal = $total_principal + $key->principal - $key->principal_waived - $key->principal_written_off;
                $total_interest = $total_interest + $key->interest - $key->interest_waived - $key->interest_written_off;
                $total_penalties = $total_penalties + $key->penalty - $key->penalty_waived - $key->penalty_written_off;
                $total_fees = $total_fees + $key->fees - $key->fees_waived - $key->fees_written_off;
                $total_due = $total_due + $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->penalties - $key->penalty_waived - $key->penalty_written_off + $key->fees - $key->fees_waived - $key->fees_written_off;
                $total_paid = $total_paid + $key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid;
                $total_outstanding = $total_outstanding + $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->penalty - $key->penalty_waived - $key->penalty_written_off + $key->fees - $key->fees_waived - $key->fees_written_off;
                ?>
                <tr>
                    <td scope="row">{{$count}}</td>
                    <td>{{$key->due_date}}</td>
                    <td>
                        @if(!empty($key->from_date))
                            @if(strtotime($key->due_date) >strtotime($key->from_date))
                                <span style="">{{$key->from_date}}</span>
                            @else
                                <span style="color: #eb2442;">{{$key->from_date}}</span>
                            @endif
                        @else

                        @endif
                    </td>
                    <td>
                        @if(!empty($key->from_date))
                            @if(strtotime($key->due_date) >strtotime($key->from_date))
                                <i class="fa fa-check" data-toggle="tooltip"
                                   title="{{trans_choice('general.timely',1)}}"></i>
                            @else
                                <i class="fa fa-question" data-toggle="tooltip"
                                   title="{{trans_choice('general.late',1)}}"></i>
                            @endif
                        @else
                            @if(strtotime(date("Y-m-d")) <strtotime($key->due_date))
                                &nbsp;
                            @else
                                <i class="fa fa-question" data-toggle="tooltip"
                                   title="{{trans_choice('general.late',1)}}"></i>
                            @endif
                        @endif
                    </td>
                    <td class="lefthighlightcolheader"></td>
                    <td>
                        @if( ($key->principal_waived +$key->principal_written_off)>0)
                            <span style="color: #eb2442;"><s>{{number_format($key->principal_waived +$key->principal_written_off,$loan->loan_product->decimals)}}</s></span>
                        @endif
                        {{number_format($key->principal- $key->principal_waived - $key->principal_written_off,$loan->loan_product->decimals)}}
                    </td>
                    <td class="righthighlightcolheader">{{number_format($principal_balance,$loan->loan_product->decimals)}}</td>

                    <td class="lefthighlightcolheader">
                        @if( ($key->interest_waived +$key->interest_written_off)>0)
                            <span style="color: #eb2442;"><s>{{number_format($key->interest_waived +$key->interest_written_off,$loan->loan_product->decimals)}}</s></span>
                        @endif
                        {{number_format($key->interest- $key->interest_waived - $key->interest_written_off,$loan->loan_product->decimals)}}
                    </td>
                    <td>
                        @if( ($key->fees_waived +$key->fees_written_off)>0)
                            <span style="color: #eb2442;"><s>{{number_format($key->fees_waived +$key->fees_written_off,$loan->loan_product->decimals)}}</s></span>
                        @endif
                        {{number_format($key->fees- $key->fees_waived - $key->fees_written_off,$loan->loan_product->decimals)}}
                    </td>
                    <td class="righthighlightcolheader">
                        @if( ($key->penalty_waived +$key->penalty_written_off)>0)
                            <span style="color: #eb2442;"><s>{{number_format($key->penalty_waived +$key->penalty_written_off,$loan->loan_product->decimals)}}</s></span>
                        @endif
                        {{number_format($key->penalty- $key->penalty_waived - $key->penalty_written_off,$loan->loan_product->decimals)}}
                    </td>

                    <td>{{number_format($key->principal- $key->principal_waived - $key->principal_written_off + $key->interest- $key->interest_waived - $key->interest_written_off + $key->penalty- $key->penalty_waived - $key->penalty_written_off + $key->fees- $key->fees_waived - $key->fees_written_off,$loan->loan_product->decimals)}}</td>
                    <td>{{number_format($key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid,$loan->loan_product->decimals)}}</td>
                    <td>{{number_format($key->principal- $key->principal_waived - $key->principal_written_off + $key->interest- $key->interest_waived - $key->interest_written_off + $key->penalty- $key->penalty_waived - $key->penalty_written_off + $key->fees- $key->fees_waived - $key->fees_written_off-($key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid),$loan->loan_product->decimals)}}</td>
                </tr>
                <?php
                $count++;
                ?>
            @endforeach
            </tbody>
            <tfoot class="ui-widget-header">
            <tr>
                <th colspan="2">{{trans_choice('general.total',1)}}</th>
                <th></th>
                <th></th>

                <th class="lefthighlightcolheader"> {{number_format($loan->principal,$loan->loan_product->decimals)}}</th>
                <th> {{number_format($total_principal,$loan->loan_product->decimals)}}</th>
                <th class="righthighlightcolheader">&nbsp;</th>

                <th class="lefthighlightcolheader">{{number_format($total_interest,$loan->loan_product->decimals)}}</th>
                <th>{{number_format($total_fees,$loan->loan_product->decimals)}}</th>
                <th class="righthighlightcolheader">{{number_format($total_penalties,$loan->loan_product->decimals)}}</th>

                <th>{{number_format($total_due,$loan->loan_product->decimals)}}</th>
                <th>{{number_format($total_paid,$loan->loan_product->decimals)}}</th>
                <th>{{number_format($total_outstanding-$total_paid,$loan->loan_product->decimals)}}</th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    window.onload = function () {
        window.print();
    }
</script>