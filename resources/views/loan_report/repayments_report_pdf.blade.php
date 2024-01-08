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

    <table class="">


        <tbody>
        <tr style="height: 25pt">
        <p>
            <td colspan="11" valign="middle"
                class="style-1"> {{trans_choice('general.loan',1)}}  {{trans_choice('general.repayment',2)}}</td>
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
            <td>{{trans_choice('general.loan',1)}}#</td>
            <td>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}} </td>
            <td>{{trans_choice('general.principal',1)}}</td>
            <td>{{trans_choice('general.interest',1)}}</td>
            <td>{{trans_choice('general.fee',2)}}</td>
            <td>{{trans_choice('general.penalty',2)}}</td>
            <td>{{trans_choice('general.total',1)}}</td>
            <td>{{trans_choice('general.date',1)}}</td>
            <td>{{trans_choice('general.channel',1)}}</td>
        </tr>
        <?php
        $total_principal = 0;
        $total_fees = 0;
        $total_interest = 0;
        $total_penalty = 0;
        $decimals = 0;
        ?>
        @foreach($data as $key)
            <?php
            if (!empty($key->loan)) {
                $decimals = $key->loan->decimals;
            } else {
                $decimals = 0;
            }
            $principal = $key->principal_derived;
            $interest = $key->interest_derived;
            $fees = $key->fees_derived;
            $penalty = $key->penalty_derived;
            $total_principal = $total_principal + $principal;
            $total_interest = $total_interest + $interest;
            $total_fees = $total_fees + $fees;
            $total_penalty = $total_penalty + $penalty;

            ?>
            <tr>
                <td>{{$key->id}}</td>
                <td>
                    @if(!empty($key->loan))
                        @if($key->loan->client_type=="client")
                            @if(!empty($key->loan->client))
                                @if($key->loan->client->client_type=="individual")
                                    {{$key->loan->client->first_name}} {{$key->loan->client->middle_name}} {{$key->loan->client->last_name}}
                                @endif
                                @if($key->loan->client->client_type=="business")
                                    {{$key->loan->client->full_name}}
                                @endif
                            @endif
                        @endif
                        @if($key->loan->client_type=="group")
                            @if(!empty($key->loan->group))
                                {{$key->loan->group->name}}
                            @endif
                        @endif
                    @endif
                </td>
                <td>{{$key->loan->id}}</td>
                <td>
                    @if(!empty($key->loan))
                        @if(!empty($key->loan->loan_officer))
                            {{$key->loan->loan_officer->first_name}}  {{$key->loan->loan_officer->last_name}}
                        @endif
                    @endif
                </td>
                <td>{{number_format($principal,$decimals)}}</td>
                <td>{{number_format($interest,$decimals)}}</td>
                <td>{{number_format($fees,$decimals)}}</td>
                <td>{{number_format($penalty,$decimals)}}</td>
                <td>{{number_format($principal+$interest+$fees+$penalty,$decimals)}}</td>
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
            <td colspan="4"></td>
            <td><b>{{number_format($total_principal,$decimals)}}</b></td>
            <td><b>{{number_format($total_interest,$decimals)}}</b></td>
            <td><b>{{number_format($total_fees,$decimals)}}</b></td>
            <td><b>{{number_format($total_penalty,$decimals)}}</b></td>
            <td><b>{{number_format($total_principal+$total_interest+$total_fees+$total_penalty,$decimals)}}</b></td>
            <td colspan="3"></td>
        </tr>
        </tfoot>
    </table>
</div>