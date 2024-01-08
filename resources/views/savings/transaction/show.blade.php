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
                        <td>{{$savings_transaction->id}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.type',1)}}</td>
                        <td>
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
                        </td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.date',1)}}</td>
                        <td>{{$savings_transaction->date}}</td>
                    </tr>
                    <tr>
                        <td>{{trans_choice('general.amount',1)}}</td>
                        <td>
                            @if($savings_transaction->credit>$savings_transaction->debit)
                                {{number_format($savings_transaction->credit,2)}}
                            @else
                                {{number_format($savings_transaction->debit,2)}}
                            @endif
                        </td>
                    </tr>
                   
                    <tr>
                        <td>{{trans_choice('general.note',2)}}</td>
                        <td>
                            {{$savings_transaction->notes}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</b></td>
                    </tr>
                    @if(!empty($savings_transaction->payment_detail))
                        @if(!empty($savings_transaction->payment_detail->type))
                            <tr>
                                <td>{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}</td>
                                <td>
                                    {{$savings_transaction->payment_detail->type->name}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($savings_transaction->payment_detail->account_number))
                            <tr>
                                <td>{{trans_choice('general.account',1)}}#</td>
                                <td>
                                    {{$savings_transaction->payment_detail->account_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($savings_transaction->payment_detail->cheque_number))
                            <tr>
                                <td>{{trans_choice('general.cheque',1)}}#</td>
                                <td>
                                    {{$savings_transaction->payment_detail->cheque_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($savings_transaction->payment_detail->routing_code))
                            <tr>
                                <td>{{trans_choice('general.routing_code',1)}}#</td>
                                <td>
                                    {{$savings_transaction->payment_detail->routing_code}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($savings_transaction->payment_detail->receipt_number))
                            <tr>
                                <td>{{trans_choice('general.receipt',1)}}#</td>
                                <td>
                                    {{$savings_transaction->payment_detail->receipt_number}}
                                </td>
                            </tr>
                        @endif
                        @if(!empty($savings_transaction->payment_detail->bank))
                            <tr>
                                <td>{{trans_choice('general.bank',1)}}#</td>
                                <td>
                                    {{$savings_transaction->payment_detail->bank}}
                                </td>
                            </tr>
                        @endif
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>

    </script>
@endsection
