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

<div style="display: flex; justify-content: space-between;">
<div>
<img src="{{ public_path('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value) }}"
                 class="img-responsive" width="150"/>
        <h2>Whence Financial Services</h2>
</div>
<table class="table">
    <tr>
        <td colspan="2">
        <div>
            <p>Client Name:@if($loan_transaction->loan->client_type=="client")
                        @if($loan_transaction->loan->client->client_type=="individual")
                           {{$loan_transaction->loan->client->first_name}} {{$loan_transaction->loan->client->middle_name}} {{$loan_transaction->loan->client->last_name}}
                        @endif
                        @if($loan_transaction->loan->client->client_type=="business")
                        {{$loan_transaction->loan->client->full_name}}
                        @endif

                    @endif
                    @if($loan_transaction->loan->client_type=="group")
                        {{$loan_transaction->loan->group->name}}
                    @endif

        </p>
            <p>Payment Type:
            @if(!empty($loan_transaction->payment_detail))
                @if(!empty($loan_transaction->payment_detail->type))
                                {{$loan_transaction->payment_detail->type->name}}
                @endif
            @endif
            </p>
            <p>Collected By:
            @if(!empty($loan_transaction->created_by))
                {{$loan_transaction->created_by->first_name}} {{$loan_transaction->created_by->last_name}}
            @endif
            </p>
        </div>

        <td class="text-right">
        <p>Date:  {{date("jS M, Y", strtotime($loan_transaction->date))}}</p>
        <p>Loan #: {{$loan_transaction->loan->id}}</p>
        </td>
        </td>
  
    </tr>

    <tr style="background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;">
        <td>
            <p>Transaction #</p>
        </td>

        <td class="text-center">
            <p>Description</p>
        </td>

        <td class="text-right">
            <p>Amount</p>
        </td>
    </tr>

    <tr>
        <td>
            <p>
            {{$loan_transaction->loan->id}}
            </p>
        </td>

        <td class="text-center">
            <p>
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

            </p>
        </td>


        <td class="text-right">
            <p>
            @if($loan_transaction->credit>$loan_transaction->debit)
                            {{number_format($loan_transaction->credit,2)}}
                        @else
                            {{number_format($loan_transaction->debit,2)}}
                        @endif
            </p>

        </td>


    </tr>
</table>
<div style="border-top: 2px solid #eee;"></div>
</div>

<script>
    window.onload = function () {
        window.print();
    }
</script>