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
            <!-- <p>Payment Type:
            @if(!empty($loan_transaction->payment_detail))
                @if(!empty($loan_transaction->payment_detail->type))
                                {{$loan_transaction->payment_detail->type->name}}
                @endif
            @endif
            </p> -->
            <p>Collected By:
            @if(!empty($loan_transaction->created_by))
                {{$loan_transaction->created_by->first_name}} {{$loan_transaction->created_by->last_name}}
            @endif
            </p>
            <p>Date:  {{date("jS M, Y", strtotime($loan_transaction->date))}}</p>
        </div>

        <td class="pull-right">
        <p>Loan #: {{$loan_transaction->loan->id}}</p>
        <p>Transaction #: {{$loan_transaction->id}}</p>
        @if($loan_transaction->payment_apply_to !== 'full_payment')
        <p>Due date: {{date("jS M, Y",strtotime($due_date))}}</p>
        @else
        <p>Due date: -</p>
        @endif
        </td>
        </td>
  
    </tr>

    <tr style="background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;">

        <td >
            <p>Payment type</p>
        </td>

        <td >
            <p>Balance b/f</p>
        </td>

        <td>
            <p>Amount paid</p>
        </td>

        <td>
            <p>Outstanding</p>
        </td>
    </tr>

    <tr>

        <td>
            <p>
            {{$loan_transaction->payment_apply_to}}
            </p>
        </td>
        <?php 
            if($loan_transaction->credit>$loan_transaction->debit){
                $amount = $loan_transaction->credit;
            }else{
                $amount = $loan_transaction->debit;
            }
            ?>
        <td>
            <p>
            @if($loan_transaction->payment_apply_to == 'reloan_payment')
            {{number_format(($current_balance/1.4) + $loan_transaction->credit)}}
            @else
            {{number_format($current_balance + $loan_transaction->credit)}}
            @endif
            </p>
        </td>



        <td>
            <p>
            @if($loan_transaction->credit>$loan_transaction->debit)
                            {{number_format($loan_transaction->credit,2)}}
                        @else
                            {{number_format($loan_transaction->debit,2)}}
                        @endif
            </p>

        </td>
        <td>
            <p>
       
         
           {{number_format($current_balance,2)}}

            </p>
        </td>


    </tr>
</table>
<div style="border-top: 2px solid #eee;"></div>
</div>


