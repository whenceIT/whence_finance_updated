@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.product',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.product',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('loan/product/'.$loan_product->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The product name is a unique identifier for the lending product."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{$loan_product->name}}"
                               required id="name">
                    </div>
                    <label for="short_name"
                           class="control-label col-md-2">{{trans_choice('general.short_name',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="The short name is a unique identifier for the lending product."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="short_name" class="form-control"
                               value="{{$loan_product->short_name}}"
                               required id="short_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="control-label col-md-2">{{trans_choice('general.description',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The description is used to provide additional information regarding the purpose and characteristics of the loan product."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="description" class="form-control"
                               value="{{$loan_product->description}}"
                               required id="description">
                    </div>
                    <label for="fund_id"
                           class="control-label col-md-2">{{trans_choice('general.fund',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="Loan products may be assigned to a fund set up by your financial institution. If available, the fund field can be used for tracking and reporting on groups of loans."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="fund_id" class="form-control select2" id="fund_id">
                            <option></option>
                            @foreach(\App\Models\Fund::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($loan_product->fund_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="currency_id"
                           class="control-label col-md-2">{{trans_choice('general.currency',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The currency in which the loan will be disbursed."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="currency_id" class="form-control select2" id="currency_id" disabled>
                            <option></option>
                            @foreach(\App\Models\Currency::where('active',1)->get() as $key)
                                <option value="{{$key->id}}"
                                        @if($loan_product->currency_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="decimals"
                           class="control-label col-md-2">{{trans_choice('general.decimal',2)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The number of decimal places to be used to track and report on loans."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="decimals" class="form-control"
                               value="{{$loan_product->decimals}}"
                               required id="decimals">
                    </div>
                </div>
                <div class="form-group">
                    <label for="principal"
                           class="control-label col-md-2">{{trans_choice('general.principal',2)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="These fields are used to define the minimum, default, and maximum principal allowed for the loan product."></i>
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="default_principal" class="form-control" min="0"
                               placeholder="{{trans_choice('general.default',1)}}"
                               value="{{$loan_product->default_principal}}"
                               required id="default_principal">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="minimum_principal" class="form-control" min="0"
                               placeholder="{{trans_choice('general.minimum',1)}}"
                               value="{{$loan_product->minimum_principal}}"
                               required id="minimum_principal">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="maximum_principal" class="form-control" min="0"
                               placeholder="{{trans_choice('general.maximum',1)}}"
                               value="{{$loan_product->maximum_principal}}"
                               required id="maximum_principal">
                    </div>
                </div>
                <div class="form-group">
                    <label for="principal"
                           class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="These fields are used to define the minimum, default, and maximum principal allowed for the loan product."></i>
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="default_loan_term" class="form-control" min="0"
                               placeholder="{{trans_choice('general.default',1)}}"
                               value="{{$loan_product->default_loan_term}}"
                               required id="default_loan_term">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="minimum_loan_term" class="form-control" min="0"
                               placeholder="{{trans_choice('general.minimum',1)}}"
                               value="{{$loan_product->minimum_loan_term}}"
                               required id="minimum_loan_term">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="maximum_loan_term" class="form-control" min="0"
                               placeholder="{{trans_choice('general.maximum',1)}}"
                               value="{{$loan_product->maximum_loan_term}}"
                               required id="maximum_loan_term">
                    </div>
                </div>
                <div class="form-group">
                    <label for="repayment_frequency"
                           class="control-label col-md-2">{{trans_choice('general.repayment',1)}}
                        <br> {{trans_choice('general.frequency',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The frequency of loan instalments due on the loan. To select weekly, enter ‘1’ and select “Weeks” from the dropdown. For fortnightly, enter ’2’ and select “Weeks” from the dropdown."></i>
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="repayment_frequency" class="form-control" min="0"
                               placeholder="" value="{{$loan_product->repayment_frequency}}"
                               required id="repayment_frequency">
                    </div>
                    <div class="col-md-3">
                        <select name="repayment_frequency_type" class="form-control " id="repayment_frequency_type"
                                required>
                            <option value="days"
                                    @if($loan_product->repayment_frequency_type=="days") selected @endif>{{trans_choice('general.day',2)}}</option>
                            <option value="weeks"
                                    @if($loan_product->repayment_frequency_type=="weeks") selected @endif>{{trans_choice('general.week',2)}}</option>
                            <option value="months"
                                    @if($loan_product->repayment_frequency_type=="months") selected @endif>{{trans_choice('general.month',2)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="interest_rate_type"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The default interest rate is the amount automatically shown to users when creating a new loan. By setting a minimum and maximum rate, you give users flexibility in determining the interest charged. No user will be able to disburse loans with an interest outside of the minimum and maximum rates."></i>
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="default_interest_rate" class="form-control" min="0"
                               placeholder="{{trans_choice('general.default',1)}}"
                               value="{{$loan_product->default_interest_rate}}"
                               required id="default_interest_rate">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="minimum_interest_rate" class="form-control" min="0"
                               placeholder="{{trans_choice('general.minimum',1)}}"
                               value="{{$loan_product->minimum_interest_rate}}"
                               required id="minimum_interest_rate">
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="maximum_interest_rate" class="form-control" min="0"
                               placeholder="{{trans_choice('general.maximum',1)}}"
                               value="{{$loan_product->maximum_interest_rate}}"
                               required id="maximum_interest_rate">
                    </div>
                    <div class="col-md-3">
                        <select name="interest_rate_type" class="form-control " id="interest_rate_type"
                                required>
                            <option value="month"
                                    @if($loan_product->interest_rate_type=="month") selected @endif>{{trans_choice('general.per',2)}} {{trans_choice('general.month',1)}}</option>
                            <option value="year"
                                    @if($loan_product->interest_rate_type=="year") selected @endif>{{trans_choice('general.per',2)}} {{trans_choice('general.year',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="grace_on_principal"
                           class="control-label col-md-2">{{trans_choice('general.grace_on_principal',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Enter the number of instalments you wish you grace period on principal payments to be "></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="grace_on_principal" class="form-control" min="0"
                               value="{{$loan_product->grace_on_principal}}"
                               required id="grace_on_principal">
                    </div>
                    <label for="grace_on_interest_payment"
                           class="control-label col-md-2">{{trans_choice('general.grace_on_interest_payment',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Enter the number of instalments you wish the grace period on Interest payments to be"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="grace_on_interest_payment" class="form-control" min="0"
                               value="{{$loan_product->grace_on_interest_payment}}"
                               required id="grace_on_interest_payment">
                    </div>
                </div>
                <div class="form-group">
                    <label for="grace_on_interest_charged"
                           class="control-label col-md-2">{{trans_choice('general.grace_on_interest_charged',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Enter the number of instalments you do not wish to charge or calculate any interest for "></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="grace_on_interest_charged" class="form-control" min="0"
                               value="{{$loan_product->grace_on_interest_charged}}"
                               required id="grace_on_interest_charged">
                    </div>
                    <label for="interest_method"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.method',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Flat Interest loans charge equal interest amounts on each loan instalment, based on the original loan principal. Declining Balance Loans calculate the interest amount on each instalment based upon the outstanding balance of the loan (therefore the interest amount charged each instalment reduces as the loan is repaid). "></i>
                    </label>
                    <div class="col-md-3">
                        <select name="interest_method" class="form-control " id="interest_method"
                                required>
                            <option value="flat"
                                    @if($loan_product->interest_method=="flat") selected @endif>{{trans_choice('general.flat',1)}}</option>
                            <option value="declining_balance"
                                    @if($loan_product->interest_method=="declining_balance") selected @endif>{{trans_choice('general.declining_balance',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="armotization_method"
                           class="control-label col-md-2">{{trans_choice('general.armotization',1)}} {{trans_choice('general.method',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Amortization Type only applies to Declining Balance loans Selecting ‘Equal Instalments’ ensures that the principal of the loan instalment remains the same through the loan term (with the interest and instalment size therefore declining). Selecting ‘Equal Principal Payments’ ensures the principal is adjusted to ensure equal instalment sizes. "></i>
                    </label>
                    <div class="col-md-3">
                        <select name="armotization_method" class="form-control " id="armotization_method"
                                required>
                            <option value="equal_installment"
                                    @if($loan_product->armotization_method=="equal_installment") selected @endif>{{trans_choice('general.equal_installment',1)}}</option>
                            <option value="equal_principal"
                                    @if($loan_product->armotization_method=="equal_principal") selected @endif>{{trans_choice('general.declining_balance',1)}}</option>
                        </select>
                    </div>
                    <label for="year_days"
                           class="control-label col-md-2">{{trans_choice('general.day',2)}} {{trans_choice('general.in',1)}} {{trans_choice('general.year',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="This determines whether the daily interest rate is calculated by dividing the ‘Nominal Annual Interest Rate’ by 360, or 365."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="year_days" class="form-control " id="year_days"
                                required>
                            <option value="actual"
                                    @if($loan_product->year_days=="actual") selected @endif>{{trans_choice('general.actual',1)}}</option>
                            <option value="360" @if($loan_product->year_days=="360") selected @endif>
                                360 {{trans_choice('general.day',2)}}</option>
                            <option value="364" @if($loan_product->year_days=="364") selected @endif>
                                364 {{trans_choice('general.day',2)}}</option>
                            <option value="365" @if($loan_product->year_days=="365") selected @endif>
                                365 {{trans_choice('general.day',2)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="month_days"
                           class="control-label col-md-2">{{trans_choice('general.day',2)}} {{trans_choice('general.in',1)}} {{trans_choice('general.month',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Specifies the number of days in a Month, used to calculate partial periods for monthly loans"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="month_days" class="form-control " id="month_days"
                                required>
                            <option value="actual"
                                    @if($loan_product->month_days=="actual") selected @endif>{{trans_choice('general.actual',1)}}</option>
                            <option value="30" @if($loan_product->month_days=="30") selected @endif>
                                30 {{trans_choice('general.day',2)}}</option>
                        </select>
                    </div>
                    <label for="loan_transaction_strategy"
                           class="control-label col-md-2">{{trans_choice('general.repayment',1)}} {{trans_choice('general.strategy',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The Transaction Processing Strategy determines the order incoming payments are allocated between Interest, Principal, Penalties and Fees. the default setting is ‘Interest, Principal, Penalties, Fees’, which ensures that clients with an outstanding Penalty are still able to repay their full loan instalment."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="loan_transaction_strategy" class="form-control " id="loan_transaction_strategy"
                                required>
                            <option value="penalty_fees_interest_principal"
                                    @if($loan_product->loan_transaction_strategy=="penalty_fees_interest_principal") selected @endif>{{trans_choice('general.penalty_fees_interest_principal',1)}}</option>
                            <option value="principal_interest_penalty_fees"
                                    @if($loan_product->loan_transaction_strategy=="principal_interest_penalty_fees") selected @endif>{{trans_choice('general.principal_interest_penalty_fees',1)}}</option>
                            <option value="interest_principal_penalty_fees"
                                    @if($loan_product->loan_transaction_strategy=="interest_principal_penalty_fees") selected @endif>{{trans_choice('general.interest_principal_penalty_fees',1)}}</option>
                        </select>
                    </div>

                </div>
                <div class="form-group">
                    <label for="lock_guarantee"
                           class="control-label col-md-2">{{trans_choice('general.lock',1)}} {{trans_choice('general.guarantee',1)}} {{trans_choice('general.fund',2)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="If you select yes, a guarantor will not be able to withdraw funds linked as a guarantee to the loan until the loan is repaid"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="lock_guarantee" class="form-control " id="lock_guarantee"
                                required>
                            <option value="1"
                                    @if($loan_product->lock_guarantee=="1") selected @endif>{{trans_choice('general.yes',1)}}</option>
                            <option value="0"
                                    @if($loan_product->lock_guarantee=="0") selected @endif>{{trans_choice('general.no',1)}}</option>
                        </select>
                    </div>
                    <label for="allow_additional_charges"
                           class="control-label col-md-2">{{trans_choice('general.allow_additional_charges',1)}}</label>
                    <div class="col-md-3">
                        <select name="allow_additional_charges" class="form-control select2"
                                id="allow_additional_charges" required>
                            <option value="1"
                                    @if($loan_product->allow_additional_charges=="1") selected @endif>{{trans_choice('general.yes',1)}}</option>
                            <option value="0"
                                    @if($loan_product->allow_additional_charges=="0") selected @endif>{{trans_choice('general.no',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="arrears_grace_days"
                           class="control-label col-md-2">{{trans_choice('general.arrears_grace_days',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="A loan is in arrears once the number of days entered into this field is exceeded. If this field is blank, the loan will be in arrears the day after a scheduled payment is missed."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="arrears_grace_days" class="form-control" min="0"
                               value="{{$loan_product->arrears_grace_days}}"
                               required id="arrears_grace_days">
                    </div>
                </div>
                <h3>{{trans_choice('general.charge',2)}}</h3>
                <hr>
                <div class="form-group">
                    <label for="charges_dropdown"
                           class="control-label col-md-2">{{trans_choice('general.charge',1)}}</label>
                    <div class="col-md-3">
                        <select name="charges_dropdown" class="form-control select2" id="charges_dropdown">
                            <option></option>
                            @foreach(\App\Models\Charge::where('currency_id', $loan_product->currency_id)->where('active', 1)->where('product', "loan")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="add_charge"
                                class="btn btn-info">{{trans_choice('general.add',1)}}</button>
                    </div>
                </div>
                <div class="row" id="charges_div">
                    <div class="col-md-12">
                        <div style="display: none;" id="saved_charges">
                            @foreach($loan_product->charges as $key)
                                <input type="hidden" name="charges[]" id="charge{{$key->charge_id}}" value="{{$key->charge_id}}">
                            @endforeach
                        </div>
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>{{trans_choice('general.name',1)}}</th>
                                <th>{{trans_choice('general.amount',1)}}</th>
                                <th>{{trans_choice('general.collected',1)}} {{trans_choice('general.on',1)}}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody id="charges_table">
                            @foreach($loan_product->charges as $key)
                                @if(!empty($key->charge))
                                    <tr id="row{{$key->charge->id}}">
                                        <td>{{ $key->charge->name }}</td>
                                        <td>
                                            {{$key->charge->amount}}
                                            @if($key->charge->charge_option=="flat")
                                                {{trans_choice('general.flat',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="installment_principal_due")
                                                % {{trans_choice('general.installment_principal_due',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="installment_principal_interest_due")
                                                % {{trans_choice('general.installment_principal_interest_due',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="installment_interest_due")
                                                % {{trans_choice('general.installment_interest_due',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="total_due")
                                                % {{trans_choice('general.total_due',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="original_principal")
                                                % {{trans_choice('general.original_principal',1)}}
                                            @endif
                                            @if($key->charge->charge_option=="percentage")
                                                % {{trans_choice('general.percentage',1)}} {{trans_choice('general.of',1)}} {{trans_choice('general.amount',1)}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($key->charge->charge_type=='disbursement')
                                                {{trans_choice('general.disbursement',1)}}
                                            @endif
                                            @if($key->charge->charge_type=='specified_due_date')
                                                {{trans_choice('general.specified_due_date',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='installment_fee')
                                                {{trans_choice('general.installment_fee',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='overdue_installment_fee')
                                                {{trans_choice('general.overdue_installment_fee',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='loan_rescheduling_fee')
                                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='overdue_maturity')
                                                {{trans_choice('general.overdue_maturity',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='savings_activation')
                                                {{trans_choice('general.savings_activation',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='withdrawal_fee')
                                                {{trans_choice('general.withdrawal_fee',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='monthly_fee')
                                                {{trans_choice('general.monthly_fee',2)}}
                                            @endif
                                            @if($key->charge->charge_type=='annual_fee')
                                                {{trans_choice('general.annual_fee',2)}}
                                            @endif
                                        </td>
                                        <td><button type="button" class="btn btn-danger btn-xs" data-id="{{$key->charge->id}}" onclick="delete_charge(this)"><i class="fa fa-trash"></i></button></td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <h3>{{trans_choice('general.accounting',1)}}</h3>
                <hr>
                <div class="form-group">
                    <label for="accounting_rule"
                           class="control-label col-md-2">{{trans_choice('general.accounting_rule',1)}}</label>
                    <div class="col-md-3">
                        <select name="accounting_rule" class="form-control select2"
                                id="accounting_rule" required>
                            <option value="none"
                                    @if($loan_product->accounting_rule=="none") selected @endif>{{trans_choice('general.none',1)}}</option>
                            <option value="cash"
                                    @if($loan_product->accounting_rule=="cash") selected @endif>{{trans_choice('general.cash',1)}}</option>
                            <option value="accrual_periodic"
                                    @if($loan_product->accounting_rule=="accrual_periodic") selected @endif>{{trans_choice('general.accrual_periodic',1)}}</option>
                            <option value="accrual_upfront"
                                    @if($loan_product->accounting_rule=="accrual_upfront") selected @endif>{{trans_choice('general.accrual_upfront',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="accounting">
                    <h4>{{trans_choice('general.asset',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_fund_source_id"
                               class="control-label col-md-2">{{trans_choice('general.fund',1)}} {{trans_choice('general.source',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="an Asset account(typically Bank or cash) that is debited during repayments/payments an credited using disbursals.."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_fund_source_id" class="form-control select2"
                                    id="gl_account_fund_source_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_fund_source_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_loan_portfolio_id"
                               class="control-label col-md-2">{{trans_choice('general.loan',1)}} {{trans_choice('general.portfolio',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="an Asset account that is debited during disbursement and credited during principal repayment/writeoff."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_loan_portfolio_id" class="form-control select2"
                                    id="gl_account_loan_portfolio_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_loan_portfolio_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="assets_accruals">
                        <div class="form-group">
                            <label for="gl_account_receivable_interest_id"
                                   class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.receivable',1)}}
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                   data-title="an Asset account that is used to accrue interest"></i>
                            </label>
                            <div class="col-md-3">
                                <select name="gl_account_receivable_interest_id" class="form-control select2"
                                        id="gl_account_receivable_interest_id" required>
                                    <option></option>
                                    @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_product->gl_account_receivable_interest_id==$key->id) selected @endif>{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <label for="gl_account_receivable_fee_id"
                                   class="control-label col-md-2">{{trans_choice('general.fee',2)}} {{trans_choice('general.receivable',1)}}
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                   data-title="an Asset account that is used to accrue fees"></i>
                            </label>
                            <div class="col-md-3">
                                <select name="gl_account_receivable_fee_id" class="form-control select2"
                                        id="gl_account_receivable_fee_id" required>
                                    <option></option>
                                    @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_product->gl_account_receivable_fee_id==$key->id) selected @endif>{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="gl_account_receivable_penalty_id"
                                   class="control-label col-md-2">{{trans_choice('general.penalty',2)}} {{trans_choice('general.receivable',1)}}
                                <i class="fa fa-question-circle" data-toggle="tooltip"
                                   data-title="an Asset account that is used to accrue penalties"></i>
                            </label>
                            <div class="col-md-3">
                                <select name="gl_account_receivable_penalty_id" class="form-control select2"
                                        id="gl_account_receivable_penalty_id" required>
                                    <option></option>
                                    @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                        <option value="{{$key->id}}"
                                                @if($loan_product->gl_account_receivable_penalty_id==$key->id) selected @endif>{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.liability',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_loan_over_payments_id"
                               class="control-label col-md-2">{{trans_choice('general.overpayment',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="an Liability account that is credited on overpayments and credited when refunds are made to client."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_loan_over_payments_id" class="form-control select2"
                                    id="gl_account_loan_over_payments_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"liability")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_loan_over_payments_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_suspended_income_id"
                               class="control-label col-md-2">{{trans_choice('general.suspended',2)}} {{trans_choice('general.income',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="When you write off a loan, the outstanding balance is booked into this account"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_suspended_income_id" class="form-control select2"
                                    id="gl_account_suspended_income_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"liability")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_suspended_income_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.income',1)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_income_interest_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',1)}} {{trans_choice('general.interest',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="an Income account that is credited during interest payment."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_interest_id" class="form-control select2"
                                    id="gl_account_income_interest_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_income_interest_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_income_fee_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',2)}} {{trans_choice('general.fee',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="When you write off a loan, the outstanding balance is booked into this account"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_fee_id" class="form-control select2"
                                    id="gl_account_income_fee_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_income_fee_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gl_account_income_penalty_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',1)}} {{trans_choice('general.penalty',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="An Income account, which is credited when a penalty is paid by account holder on this account"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_penalty_id" class="form-control select2"
                                    id="gl_account_income_penalty_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_income_penalty_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_income_recovery_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',2)}} {{trans_choice('general.recovery',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="an Income account that is credited during Recovery Repayment."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_recovery_id" class="form-control select2"
                                    id="gl_account_income_recovery_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_income_recovery_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.expense',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_loans_written_off_id"
                               class="control-label col-md-2">{{trans_choice('general.loss',2)}} {{trans_choice('general.written',1)}} {{trans_choice('general.off',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="An expense account that is debited on principal writeoff (also debited in the events of interest, fee and penalty written-off in case of accrual based accounting.)"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_loans_written_off_id" class="form-control select2"
                                    id="gl_account_loans_written_off_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"expense")->get() as $key)
                                    <option value="{{$key->id}}"
                                            @if($loan_product->gl_account_loans_written_off_id==$key->id) selected @endif>{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#currency_id').change(function (e) {
            var id = $('#currency_id').val();
            var url = "{!!  url('loan/product')  !!}/" + id + "/get_currency_charges";
            var items = "";
            items += "<option></option>";
            $.getJSON(url, function (data) {
                $.each(data, function (index, item) {
                    items += "<option value='" + item.id + "'>" + item.name + "</option>";
                });
                $("#charges_dropdown").html(items);
            });
        });
        $('#add_charge').click(function (e) {
            if ($('#charges_dropdown').val() == "") {
                alert("Please select an item")
            } else {
                //try to build table
                var id = $('#charges_dropdown').val();
                $.ajax({
                    type: 'GET',
                    url: "{{url('loan/product/')}}" + "/" + id + "/get_charge_detail",
                    dataType: "json",
                    success: function (data) {
                        $('#charges_table').append('<tr id="row' + id + '"><td>' + data.name + '</td><td>' + data.amount +' '+data.charge_option+ '</td><td>' + data.collected_on + '</td><td><button type="button" class="btn btn-danger btn-xs" data-id="' + id + '" onclick="delete_charge(this)"><i class="fa fa-trash"></i></button></td></tr>');
                        $('#saved_charges').append('<input name="charges[]" id="charge' + id + '" value="' + id + '">');
                    },
                    error: function (data) {
                        swal({
                            title: 'Error',
                            text: 'An Error occurred, please try again',
                            type: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ok',
                            timer: 2000
                        })
                    }
                });
            }
        });
        function delete_charge(e) {
            swal({
                title: 'Are you sure?',
                text: '',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ok',
                cancelButtonText: 'Cancel'
            }).then(function () {
                $('#charge' + $(e).attr("data-id")).remove();
                $('#row' + $(e).attr("data-id")).remove();

            })
        }
        if ($('#accounting_rule').val() == "none") {
            //disable all accounting
            $('#accounting').hide();
            $('#gl_account_fund_source_id').removeAttr("required");
            $('#gl_account_loan_portfolio_id').removeAttr("required");
            $('#gl_account_receivable_interest_id').removeAttr("required");
            $('#gl_account_receivable_fee_id').removeAttr("required");
            $('#gl_account_receivable_penalty_id').removeAttr("required");
            $('#gl_account_loan_over_payments_id').removeAttr("required");
            $('#gl_account_suspended_income_id').removeAttr("required");
            $('#gl_account_income_interest_id').removeAttr("required");
            $('#gl_account_income_fee_id').removeAttr("required");
            $('#gl_account_income_penalty_id').removeAttr("required");
            $('#gl_account_income_recovery_id').removeAttr("required");
            $('#gl_account_loans_written_off_id').removeAttr("required");
        }
        $('#accounting_rule').change(function (e) {
            if ($('#accounting_rule').val() == "none") {
                //disable all accounting
                $('#accounting').hide();
                $('#gl_account_fund_source_id').removeAttr("required");
                $('#gl_account_loan_portfolio_id').removeAttr("required");
                $('#gl_account_receivable_interest_id').removeAttr("required");
                $('#gl_account_receivable_fee_id').removeAttr("required");
                $('#gl_account_receivable_penalty_id').removeAttr("required");
                $('#gl_account_loan_over_payments_id').removeAttr("required");
                $('#gl_account_suspended_income_id').removeAttr("required");
                $('#gl_account_income_interest_id').removeAttr("required");
                $('#gl_account_income_fee_id').removeAttr("required");
                $('#gl_account_income_penalty_id').removeAttr("required");
                $('#gl_account_income_recovery_id').removeAttr("required");
                $('#gl_account_loans_written_off_id').removeAttr("required");
            }
            if ($('#accounting_rule').val() == "cash") {
                //disable all accounting
                $('#accounting').show();
                $('#assets_accruals').hide();
                $('#gl_account_fund_source_id').attr("required", "required");
                $('#gl_account_loan_portfolio_id').attr("required", "required");
                $('#gl_account_receivable_interest_id').removeAttr("required");
                $('#gl_account_receivable_fee_id').removeAttr("required");
                $('#gl_account_receivable_penalty_id').removeAttr("required");
                $('#gl_account_loan_over_payments_id').attr("required", "required");
                $('#gl_account_suspended_income_id').attr("required", "required");
                $('#gl_account_income_interest_id').attr("required", "required");
                $('#gl_account_income_fee_id').attr("required", "required");
                $('#gl_account_income_penalty_id').attr("required", "required");
                $('#gl_account_income_recovery_id').attr("required", "required");
                $('#gl_account_loans_written_off_id').attr("required", "required");
            }
            if ($('#accounting_rule').val() == "accrual_periodic" || $('#accounting_rule').val() == "accrual_upfront") {
                //disable all accounting
                $('#accounting').show();
                $('#assets_accruals').show();
                $('#gl_account_fund_source_id').attr("required", "required");
                $('#gl_account_loan_portfolio_id').attr("required", "required");
                $('#gl_account_receivable_interest_id').attr("required", "required");
                $('#gl_account_receivable_fee_id').attr("required", "required");
                $('#gl_account_receivable_penalty_id').attr("required", "required");
                $('#gl_account_loan_over_payments_id').attr("required", "required");
                $('#gl_account_suspended_income_id').attr("required", "required");
                $('#gl_account_income_interest_id').attr("required", "required");
                $('#gl_account_income_fee_id').attr("required", "required");
                $('#gl_account_income_penalty_id').attr("required", "required");
                $('#gl_account_income_recovery_id').attr("required", "required");
                $('#gl_account_loans_written_off_id').attr("required", "required");
            }
        });
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }
            }, highlight: function (element) {
                $(element).closest('.form-group div').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group div').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection