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
            <td colspan="6" valign="middle"
                class="style-1"> {{trans_choice('general.savings',1)}}  {{trans_choice('general.account',2)}}</td>
        </tr>
        <tr style="height: 15pt">
            <td valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
            <td valign="middle" class="style-3">{{$start_date}}</td>
            <td colspan="2" valign="middle"
                class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                :
            </td>
            <td colspan="2" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
        </tr>
        <tr style="height: 45pt">
            <td valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
            <td valign="middle" class="style-3">{{$end_date}}</td>
            <td colspan="7"></td>
        </tr>
        <tr class="">
            <td>{{trans_choice('general.id',1)}}</td>
            <td>{{trans_choice('general.client',1)}}</td>
            <td>{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}} </td>
            <td>{{trans_choice('general.product',1)}}</td>
            <td>{{trans_choice('general.created',1)}}</td>
            <td>{{trans_choice('general.balance',1)}}</td>
        </tr>
        <?php
        $total_balance = 0;

        ?>
        @foreach($data as $key)
            <?php

            $decimals = $key->decimals;

            $balance = \App\Helpers\GeneralHelper::savings_account_balance($key->id);
            $total_balance = $total_balance + $balance;


            ?>
            <tr>
                <td>{{$key->id}}</td>
                <td>
                    @if($key->client_type=="client")
                        @if(!empty($key->client))
                            @if($key->client->client_type=="individual")
                                {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                            @endif
                            @if($key->client->client_type=="business")
                                {{$key->client->full_name}}
                            @endif
                        @endif
                    @endif
                    @if($key->client_type=="group")
                        @if(!empty($key->group))
                            {{$key->group->name}}
                        @endif
                    @endif
                </td>
                <td>
                    @if(!empty($key->field_officer))
                        {{$key->field_officer->first_name}}  {{$key->field_officer->last_name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->savings_product))
                        {{$key->savings_product->name}}
                    @endif
                </td>
                <td>{{$key->created_at}}</td>
                <td>{{number_format($balance,$decimals)}}</td>

            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr>
            <td colspan="5"></td>
            <td><b>{{number_format($total_balance,2)}}</b></td>
        </tr>
        </tfoot>
    </table>
</div>