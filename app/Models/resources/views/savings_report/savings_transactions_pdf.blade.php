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
        color: white;
        padding-left: 10pt;
        font-size: 14pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
        background-color: #339933;
    }
    .style-2 {
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
        white-space: pre-wrap;
    }
    .style-3 {
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
        white-space: pre-wrap;
    }
</style>
<div>

    <table class="table  table-condensed table-hover">
        <tbody>
        <tr style="height: 25pt">
            <td colspan="11" valign="middle"
                class="style-1"> {{trans_choice('general.savings',1)}}  {{trans_choice('general.transaction',2)}}</td>
        </tr>
        <tr style="height: 15pt">
            <td valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
            <td valign="middle" class="style-3">{{$start_date}}</td>
            <td colspan="2" valign="middle"
                class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                :
            </td>
            <td colspan="3" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
            <td colspan="4"></td>
        </tr>
        <tr style="height: 45pt">
            <td valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
            <td valign="middle" class="style-3">{{$end_date}}</td>
            <td colspan="9"></td>
        </tr>
        <tr class="">
            <td>{{trans_choice('general.id',1)}}</td>
            <td>{{trans_choice('general.client',1)}}</td>
            <td>{{trans_choice('general.savings',1)}}#</td>
            <td>{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}} </td>
            <td>{{trans_choice('general.type',1)}}</td>
            <td>{{trans_choice('general.debit',1)}}</td>
            <td>{{trans_choice('general.credit',1)}}</td>
            <td>{{trans_choice('general.date',1)}}</td>
            <td>{{trans_choice('general.channel',1)}}</td>
        </tr>
        <?php
        $total_debit = 0;
        $total_credit = 0;
        $decimals = 0;
        ?>
        @foreach($data as $key)
            <?php
            if (!empty($key->savings)) {
                $decimals = $key->savings->decimals;
            } else {
                $decimals = 0;
            }

            $total_debit = $total_debit + $key->debit;
            $total_credit = $total_credit + $key->credit;


            ?>
            <tr>
                <td>{{$key->id}}</td>
                <td>
                    @if(!empty($key->savings))
                        @if($key->savings->client_type=="client")
                            @if(!empty($key->savings->client))
                                @if($key->savings->client->client_type=="individual")
                                    {{$key->savings->client->first_name}} {{$key->savings->client->middle_name}} {{$key->savings->client->last_name}}
                                @endif
                                @if($key->savings->client->client_type=="business")
                                    {{$key->savings->client->full_name}}
                                @endif
                            @endif
                        @endif
                        @if($key->savings->client_type=="group")
                            @if(!empty($key->savings->group))
                                {{$key->savings->group->name}}
                            @endif
                        @endif
                    @endif
                </td>
                <td>{{$key->savings->id}}</td>
                <td>
                    @if(!empty($key->savings))
                        @if(!empty($key->savings->field_officer))
                            {{$key->savings->field_officer->first_name}}  {{$key->savings->field_officer->last_name}}
                        @endif
                    @endif
                </td>
                <td>
                    @if($key->transaction_type=='deposit')
                        {{trans_choice('general.deposit',1)}}
                    @endif
                    @if($key->transaction_type=='withdrawal')
                        {{trans_choice('general.withdrawal',1)}}
                    @endif
                    @if($key->transaction_type=='bank_fees')
                        {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                    @endif
                    @if($key->transaction_type=='specified_due_date_fee')
                        {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                    @endif
                    @if($key->transaction_type=='interest')
                        {{trans_choice('general.interest',1)}}
                    @endif
                    @if($key->transaction_type=='dividend')
                        {{trans_choice('general.dividend',1)}}
                    @endif
                    @if($key->transaction_type=='guarantee_restored')
                        {{trans_choice('general.guarantee_restored',2)}}
                    @endif
                    @if($key->transaction_type=='fees_payment')
                        {{trans_choice('general.fee',2)}} {{trans_choice('general.payment',1)}}
                    @endif
                    @if($key->transaction_type=='transfer_loan')
                        {{trans_choice('general.transfer',1)}} {{trans_choice('general.loan',1)}}
                    @endif
                    @if($key->transaction_type=='transfer_savings')
                        {{trans_choice('general.transfer',1)}} {{trans_choice('general.savings',2)}}
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
                <td>{{number_format($key->debit,$decimals)}}</td>
                <td>{{number_format($key->credit,$decimals)}}</td>
                <td>{{$key->date}}</td>
                <td>
                    @if(!empty($key->payment_detail))
                        @if(!empty($key->payment_detail->type))
                            {{$key->payment_detail->type->name}}
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5"></td>
            <td><b>{{number_format($total_debit,$decimals)}}</b></td>
            <td><b>{{number_format($total_credit,$decimals)}}</b></td>
            <td colspan="2"></td>
        </tr>
        </tfoot>
    </table>
</div>