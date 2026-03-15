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
            <img src="{{ public_path('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
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
                    @if($loan_transaction->loan->client_type=="client")
                        @if($loan_transaction->loan->client->client_type=="individual")
                            <h2>{{$loan_transaction->loan->client->first_name}} {{$loan_transaction->loan->client->middle_name}} {{$loan_transaction->loan->client->last_name}}</h2>
                        @endif
                        @if($loan_transaction->loan->client->client_type=="business")
                            <h2>{{$loan_transaction->loan->client->full_name}}</h2>
                        @endif

                    @endif
                    @if($loan_transaction->loan->client_type=="group")
                        <h2>{{$loan_transaction->loan->group->name}}</h2>
                    @endif
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.loan',1)}} #</span></h2></td>
                <td class="text-right"><h2>{{$loan_transaction->loan->id}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</span></h2>
                </td>
                <td class="text-right">
                    <h2>
                        @if($loan_transaction->transaction_type=='disbursement')
                            {{trans_choice('general.disbursement',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='disbursement_repayment')
                            {{trans_choice('general.disbursement',1)}} {{trans_choice('general.repayment',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='specified_due_date')
                            {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='installment_fee')
                            {{trans_choice('general.installment_fee',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='overdue_installment_fee')
                            {{trans_choice('general.overdue_installment_fee',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='loan_rescheduling_fee')
                            {{trans_choice('general.loan_rescheduling_fee',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='overdue_maturity')
                            {{trans_choice('general.overdue_maturity',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='disbursement_fee')
                            {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='interest')
                            {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='repayment')
                            {{trans_choice('general.repayment',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='penalty')
                            {{trans_choice('general.penalty',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='interest_waiver')
                            {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='charge_waiver')
                            {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                        @endif
                        @if($loan_transaction->transaction_type=='write_off')
                            {{trans_choice('general.write_off',1)}}
                        @endif
                        @if($loan_transaction->transaction_type=='write_off_recovery')
                            {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
                        @endif

                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span> {{trans_choice('general.date',1)}}:</span></h2></td>
                <td class="text-right"><h2>{{$loan_transaction->date}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.amount',1)}}</span></h2></td>
                <td class="text-right">
                    <h2>
                        @if($loan_transaction->credit>$loan_transaction->debit)
                            {{number_format($loan_transaction->credit,2)}}
                        @else
                            {{number_format($loan_transaction->debit,2)}}
                        @endif
                    </h2>
                </td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.balance',1)}}</span></h2></td>
                <td class="text-right">
                    <h2>{{number_format(\App\Helpers\GeneralHelper::loan_total_balance($loan_transaction->loan->id),2)}}</h2>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</b></td>
            </tr>
            @if(!empty($loan_transaction->payment_detail))
                @if(!empty($loan_transaction->payment_detail->type))
                    <tr>
                        <td><h2><span>{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}</span>
                            </h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->type->name}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($loan_transaction->payment_detail->account_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.account',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->account_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($loan_transaction->payment_detail->cheque_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.cheque',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->cheque_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($loan_transaction->payment_detail->routing_code))
                    <tr>
                        <td><h2><span>{{trans_choice('general.routing_code',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->routing_code}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($loan_transaction->payment_detail->receipt_number))
                    <tr>
                        <td><h2><span>{{trans_choice('general.receipt',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->receipt_number}}
                            </h2>
                        </td>
                    </tr>
                @endif
                @if(!empty($loan_transaction->payment_detail->bank))
                    <tr>
                        <td><h2><span>{{trans_choice('general.bank',1)}}#</span></h2></td>
                        <td class="text-right">
                            <h2>
                                {{$loan_transaction->payment_detail->bank}}
                            </h2>
                        </td>
                    </tr>
                @endif
            @endif
            @foreach(\App\Models\CustomFieldMeta::where('category', 'repayments')->where('parent_id', $loan_transaction->id)->get() as $key)
                <tr>
                    @if(!empty($key->custom_field))
                        <td><h2><span>{{$key->custom_field->name}}:</span></h2></td>
                    @endif
                    <td  class="text-right">
                        @if($key->custom_field->field_type=="checkbox")
                            @foreach(unserialize($key->name) as $v=>$k)
                                {{$k}}<br>
                            @endforeach
                        @else
                            {{$key->name}}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td><h2><span>{{trans_choice('general.transaction',1)}} {{trans_choice('general.id',1)}} </span></h2></td>
                <td class="text-right"><h2>{{$loan_transaction->id}}</h2></td>
            </tr>
            <tr>
                <td><h2><span>{{trans_choice('general.collected_by',1)}}:</span></h2></td>
                <td class="text-right">
                    @if(!empty($loan_transaction->created_by))
                        <h2>{{$loan_transaction->created_by->first_name}} {{$loan_transaction->created_by->last_name}}</h2>
                    @endif
                </td>
            </tr>
        </table>
        <p></p>
        <hr>
    </div>
</div>

