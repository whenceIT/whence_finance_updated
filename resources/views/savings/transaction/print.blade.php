<style>
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 20px;
        display: table;
    }

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

    span {
        font-weight: bold;
    }
</style>


<div>
    <h3 class="text-center">
        @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
            <img src="{{ asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                 class="img-responsive" width="150"/>

        @endif
    </h3>
    <h3 class="text-center">
        <span>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</span></h3>

    <h3 class="text-center"><span> {{trans_choice('general.receipt',1)}}</span>
    </h3>
    <div style="margin-top:30px;margin-left: auto;margin-right: auto;text-transform: capitalize;font-size: 8px; clear: both; border-top:solid thin #ccc">
        <table class="table">
            <tr>
                <td><h2><span> {{trans_choice('general.client',1)}}</span></h2></td>
                <td class="text-right">
                    @if($savings_transaction->savings->client_type=="client")
                        @if($savings_transaction->savings->client->client_type=="individual")
                            <h2>{{$savings_transaction->savings->client->first_name}} {{$savings_transaction->savings->client->middle_name}} {{$savings_transaction->savings->client->last_name}}</h2>
                        @endif
                        @if($savings_transaction->savings->client->client_type=="business")
                            <h2>{{$savings_transaction->savings->client->full_name}}</h2>
                        @endif

                    @endif
                    @if($savings_transaction->savings->client_type=="group")
                        <h2>{{$savings_transaction->savings->group->name}}</h2>
                    @endif
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.savings',1)}} #</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->savings->id}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</span></h2>
                </td>
                <td class="text-right">
                    <h2>
                        @if($savings_transaction->transaction_type=='deposit')
                            {{trans_choice('general.deposit',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='withdrawal')
                            {{trans_choice('general.withdrawal',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='bank_fees')
                            {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                        @endif
                        @if($savings_transaction->transaction_type=='specified_due_date_fee')
                            {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                        @endif
                        @if($savings_transaction->transaction_type=='interest')
                            {{trans_choice('general.interest',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='dividend')
                            {{trans_choice('general.dividend',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='guarantee_restored')
                            {{trans_choice('general.guarantee_restored',2)}}
                        @endif
                        @if($savings_transaction->transaction_type=='fees_payment')
                            {{trans_choice('general.fee',2)}} {{trans_choice('general.payment',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='transfer_loan')
                            {{trans_choice('general.transfer',1)}} {{trans_choice('general.loan',1)}}
                        @endif
                        @if($savings_transaction->transaction_type=='transfer_savings')
                            {{trans_choice('general.transfer',1)}} {{trans_choice('general.savings',2)}}
                        @endif
                        @if($savings_transaction->reversed==1)
                            @if($savings_transaction->reversal_type=="user")
                                <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                        )</b></span>
                            @endif
                            @if($savings_transaction->reversal_type=="system")
                                <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                        )</b></span>
                            @endif
                        @endif

                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span> {{trans_choice('general.date',1)}}:</span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->date}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.amount',1)}}</span></h2></td>
                <td class="text-right">
                    <h2>
                        @if($savings_transaction->credit>$savings_transaction->debit)
                            {{number_format($savings_transaction->credit,2)}}
                        @else
                            {{number_format($savings_transaction->debit,2)}}
                        @endif
                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.balance',1)}}</span></h2></td>
                <td class="text-right">
                    <h2></h2>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</b></td>
            </tr>
            @if(!empty($savings_transaction->payment_detail))
                @if(!empty($savings_transaction->payment_detail->type))
                    <tr>
                        <td><h2><span>{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}</span>
                            </h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->type->name}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($savings_transaction->payment_detail->account_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.account',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->account_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($savings_transaction->payment_detail->cheque_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.cheque',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->cheque_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($savings_transaction->payment_detail->routing_code))
                    <tr>
                        <td><h2><span>{{trans_choice('general.routing_code',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->routing_code}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($savings_transaction->payment_detail->receipt_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.receipt',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->receipt_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($savings_transaction->payment_detail->bank))
                    <tr>
                        <td><h2><span>{{trans_choice('general.bank',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$savings_transaction->payment_detail->bank}}
                            </h2>
                        </td>
                    </tr>
                @endif
            @endif
            <tr>
                <td><h2><span>{{trans_choice('general.transaction',1)}} {{trans_choice('general.id',1)}} </span></h2></td>
                <td class="text-right"><h2>{{$savings_transaction->id}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.collected_by',1)}}:</span></h2></td>
                <td class="text-right">
                    @if(!empty($savings_transaction->created_by))
                        <h2>{{$savings_transaction->created_by->first_name}} {{$savings_transaction->created_by->last_name}}</h2>
                    @endif
                </td>
            </tr>
        </table>
        <p></p>
        <hr>
    </div>
</div>

<script>
    window.onload = function () {
        window.print();
    }
</script>