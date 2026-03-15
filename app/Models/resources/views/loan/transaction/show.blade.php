@extends('layouts.master')
@section('title')
    {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.detail',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.detail',2) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
                @if(Sentinel::hasAccess('users.create'))

                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <div class="col-md-6">
                <table class="table  table-bordered table-hover table-striped" id="">
                    <tr>
                        <td>{{trans_choice('general.id',1)}}</td>
                        <td>{{$loan_transaction->id}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.type',1)}}</td>
                        <td>
                            @if($loan_transaction->transaction_type=='disbursement')
                                {{trans_choice('general.disbursement',1)}}
                            @endif
                            @if($loan_transaction->transaction_type=='disbursement_repayment')
                                {{trans_choice('general.disbursement',1)}}  {{trans_choice('general.repayment',1)}}
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
                            @if($loan_transaction->transaction_type=='write_off_recovery')
                                {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
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
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{$loan_transaction->date}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.amount',1)}}</td>
                        <td>
                            @if($loan_transaction->credit>$loan_transaction->debit)
                                {{number_format($loan_transaction->credit,2)}}
                            @else
                                {{number_format($loan_transaction->debit,2)}}
                            @endif
                        </td>
                    </tr>
                    @if(!empty($loan_transaction->receipt))
                        <tr>
                            <td>{{trans_choice('general.receipt',1)}}</td>
                            <td>
                                {{$loan_transaction->receipt}}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td>{{trans_choice('general.note',2)}}</td>
                        <td>
                            {{$loan_transaction->notes}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</b></td>
                    </tr>
                    @if(!empty($loan_transaction->payment_detail))
                        @if(!empty($loan_transaction->payment_detail->type))
                            <tr>
                                <td>{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}</td>
                                <td>
                                    {{$loan_transaction->payment_detail->type->name}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->account_number))
                            <tr>
                                <td>{{trans_choice('general.account',1)}}#</td>
                                <td>
                                    {{$loan_transaction->payment_detail->account_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->cheque_number))
                            <tr>
                                <td>{{trans_choice('general.cheque',1)}}#</td>
                                <td>
                                    {{$loan_transaction->payment_detail->cheque_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->routing_code))
                            <tr>
                                <td>{{trans_choice('general.routing_code',1)}}#</td>
                                <td>
                                    {{$loan_transaction->payment_detail->routing_code}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->receipt_number))
                            <tr>
                                <td>{{trans_choice('general.receipt',1)}}#</td>
                                <td>
                                    {{$loan_transaction->payment_detail->receipt_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->bank))
                            <tr>
                                <td>{{trans_choice('general.bank',1)}}#</td>
                                <td>
                                    {{$loan_transaction->payment_detail->bank}}
                                </td>
                            </tr>
                        @endif
                    @endif
                    @foreach(\App\Models\CustomFieldMeta::where('category', 'repayments')->where('parent_id', $loan_transaction->id)->get() as $key)
                        <tr>
                            @if(!empty($key->custom_field))
                                <td>{{$key->custom_field->name}}:</td>
                            @endif
                            <td>
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
                </table>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>

    </script>
@endsection
