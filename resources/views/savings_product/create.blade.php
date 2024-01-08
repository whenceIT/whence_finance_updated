@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.product',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.product',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('savings/product/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The name of your savings product"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>
                    <label for="short_name"
                           class="control-label col-md-2">{{trans_choice('general.short_name',1)}}
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="In reports, collection sheet short code helps to display product names in less space"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="short_name" class="form-control"
                               value="{{old('short_name')}}"
                               required id="short_name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="description"
                           class="control-label col-md-2">{{trans_choice('general.description',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Enter a description of the product to make it easier to identify in the future"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="text" name="description" class="form-control"
                               value="{{old('description')}}"
                               required id="description">
                    </div>

                </div>
                <div class="form-group">
                    <label for="currency_id"
                           class="control-label col-md-2">{{trans_choice('general.currency',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="You can add new currencies by clicking on the ‘Currencies’ tab above. Unless you operate in multiple countries, you should normally only have one currency set up."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="currency_id" class="form-control select2" id="currency_id" required>
                            <option></option>
                            @foreach(\App\Models\Currency::where('active',1)->get() as $key)
                                <option value="{{$key->id}}"  @if(\App\Models\Setting::where('setting_key','company_currency')->first()->setting_value==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="decimals"
                           class="control-label col-md-2">{{trans_choice('general.decimal',2)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The number of decimal places you wish each savings transaction to be rounded to."></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="decimals" class="form-control"
                               value="2"
                               required id="decimals">
                    </div>
                </div>
                <div class="form-group">
                    <label for="interest_rate"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="The amount of interest the client earns on their savings each year"></i>
                    </label>
                    <div class="col-md-3">
                        <input type="number" name="interest_rate" class="form-control" min="0"
                               required id="interest_rate">
                    </div>
                </div>
                <div class="form-group">
                    <label for="interest_compounding_period"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}}
                        {{trans_choice('general.compounding',1)}} {{trans_choice('general.period',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="This determines whether the interest on the client’s account is calculated on a daily, weekly or quarterly basis. The default is on a daily basis."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="interest_compounding_period" class="form-control "
                                id="interest_compounding_period"
                                required>
                            <option value="daily">{{trans_choice('general.daily',1)}}</option>
                            <option value="monthly">{{trans_choice('general.monthly',1)}}</option>
                            <option value="quarterly">{{trans_choice('general.quarterly',1)}}</option>
                            <option value="biannual">{{trans_choice('general.biannual',1)}}</option>
                            <option value="annually">{{trans_choice('general.annually',1)}}</option>
                        </select>
                    </div>
                    <label for="interest_calculation_type"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}}
                        {{trans_choice('general.calculation',1)}} {{trans_choice('general.type',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title=",Select ‘Daily Balance’ if you wish the interest to calculated based on the daily end balance of the client’s account. Select ‘Average Daily Balance’ if you wish the Interest to be calculated on the average daily balance on the client’s savings account over the compounding period"></i>
                    </label>
                    <div class="col-md-3">
                        <select name="interest_calculation_type" class="form-control " id="interest_calculation_type"
                                required>
                            <option value="daily">{{trans_choice('general.daily',1)}} {{trans_choice('general.balance',1)}}</option>
                            <option value="average">{{trans_choice('general.average',1)}} {{trans_choice('general.daily',1)}} {{trans_choice('general.balance',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="interest_posting_period"
                           class="control-label col-md-2">{{trans_choice('general.interest',1)}}
                        {{trans_choice('general.posting',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="This determines how frequently interest earned by the client is posted to the client’s account. Default is monthly or quarterly."></i>
                    </label>
                    <div class="col-md-3">
                        <select name="interest_posting_period" class="form-control " id="interest_posting_period"
                                required>
                            <option value="monthly">{{trans_choice('general.monthly',1)}}</option>
                            <option value="quarterly">{{trans_choice('general.quarterly',1)}}</option>
                            <option value="biannual">{{trans_choice('general.biannual',1)}}</option>
                            <option value="annually">{{trans_choice('general.annually',1)}}</option>
                        </select>
                    </div>
                    <label for="minimum_balance"
                           class="control-label col-md-2">{{trans_choice('general.minimum',1)}} {{trans_choice('general.balance',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title="Set a 'minimum balance for interest calculation' to ensure that interest is only paid on savings accounts of this product if the balance on the account is above the minimum."></i>
                    </label>

                    <div class="col-md-3">
                        <input type="number" name="minimum_balance" class="form-control" value="0"
                               placeholder=""
                               required id="minimum_balance">
                    </div>

                </div>
                <div class="form-group">
                    <label for="allow_overdraft"
                           class="control-label col-md-2">{{trans_choice('general.allow',1)}} {{trans_choice('general.overdraft',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title=""></i>
                    </label>
                    <div class="col-md-3">
                        <select name="allow_overdraft" class="form-control " id="allow_overdraft"
                                required>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                            <option value="0" selected>{{trans_choice('general.no',1)}}</option>
                        </select>
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
                            @foreach(\App\Models\Charge::where('currency_id', \App\Models\Setting::where('setting_key','company_currency')->first()->setting_value)->where('active', 1)->where('charge_type', "savings")->get() as $key)
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
                            <option value="none">{{trans_choice('general.none',1)}}</option>
                            <option value="cash">{{trans_choice('general.cash',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="accounting">
                    <h4>{{trans_choice('general.asset',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_savings_reference_id"
                               class="control-label col-md-2">{{trans_choice('general.savings',1)}} {{trans_choice('general.reference',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="The Savings Reference refers to the pool where all client savings are actually stored (typically a bank account). This account is debited when a client deposits savings, and credited when a client withdraws."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_savings_reference_id" class="form-control select2"
                                    id="gl_account_savings_reference_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_overdraft_portfolio_id"
                               class="control-label col-md-2">{{trans_choice('general.overdraft',1)}} {{trans_choice('general.portfolio',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="This account tracks the balance of all overdrawn savings accounts. This account is debited when an account goes overdrawn."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_overdraft_portfolio_id" class="form-control select2"
                                    id="gl_account_overdraft_portfolio_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <h4>{{trans_choice('general.liability',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_savings_control_id"
                               class="control-label col-md-2">{{trans_choice('general.overpayment',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="The account where you monitor all of the client savings (such as Compulsory Savings). This account is credited when a client deposits savings, and debited when a client withdraws."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_savings_control_id" class="form-control select2"
                                    id="gl_account_savings_control_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"liability")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.income',1)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_income_fee_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',2)}} {{trans_choice('general.fee',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="Any income related to fees (related to a withdrawal fee for example) is booked into this account."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_fee_id" class="form-control select2"
                                    id="gl_account_income_fee_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_income_penalty_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',1)}} {{trans_choice('general.penalty',2)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="Any income from penalties is booked into this account."></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_penalty_id" class="form-control select2"
                                    id="gl_account_income_penalty_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="gl_account_income_interest_id"
                               class="control-label col-md-2">{{trans_choice('general.income',1)}} {{trans_choice('general.from',1)}} {{trans_choice('general.interest',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="Any interest income from overdrawn savings accounts is booked into this account"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_income_interest_id" class="form-control select2"
                                    id="gl_account_income_interest_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"income")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.expense',2)}}</h4>
                    <div class="form-group">
                        <label for="gl_account_interest_on_savings_id"
                               class="control-label col-md-2">{{trans_choice('general.interest',2)}} {{trans_choice('general.on',1)}} {{trans_choice('general.savings',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="This account is where any interest earned on a client’s savings account is posted"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_interest_on_savings_id" class="form-control select2"
                                    id="gl_account_interest_on_savings_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"expense")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="gl_account_savings_written_off_id"
                               class="control-label col-md-2">{{trans_choice('general.write_off',2)}} {{trans_choice('general.account',1)}}
                            <i class="fa fa-question-circle" data-toggle="tooltip"
                               data-title="When you write off a savings account overdraft, the written off funds are booked to this account"></i>
                        </label>
                        <div class="col-md-3">
                            <select name="gl_account_savings_written_off_id" class="form-control select2"
                                    id="gl_account_savings_written_off_id" required>
                                <option></option>
                                @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"expense")->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
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
            var url = "{!!  url('savings/product')  !!}/" + id + "/get_currency_charges";
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
                    url: "{{url('savings/product/')}}" + "/" + id + "/get_charge_detail",
                    dataType: "json",
                    success: function (data) {
                        $('#charges_table').append('<tr id="row' + id + '"><td>' + data.name + '</td><td>' + data.amount + '</td><td>' + data.collected_on + '</td><td><button type="button" class="btn btn-danger btn-xs" data-id="' + id + '" onclick="delete_charge(this)"><i class="fa fa-trash"></i></button></td></tr>');
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
            $('#gl_account_savings_reference_id').removeAttr("required");
            $('#gl_account_overdraft_portfolio_id').removeAttr("required");
            $('#gl_account_savings_control_id').removeAttr("required");
            $('#gl_account_interest_on_savings_id').removeAttr("required");
            $('#gl_account_savings_written_off_id').removeAttr("required");
            $('#gl_account_income_interest_id').removeAttr("required");
            $('#gl_account_income_fee_id').removeAttr("required");
            $('#gl_account_income_penalty_id').removeAttr("required");

        }
        $('#accounting_rule').change(function (e) {
            if ($('#accounting_rule').val() == "none") {
                //disable all accounting
                $('#accounting').hide();
                $('#gl_account_savings_reference_id').removeAttr("required");
                $('#gl_account_overdraft_portfolio_id').removeAttr("required");
                $('#gl_account_savings_control_id').removeAttr("required");
                $('#gl_account_interest_on_savings_id').removeAttr("required");
                $('#gl_account_savings_written_off_id').removeAttr("required");
                $('#gl_account_income_interest_id').removeAttr("required");
                $('#gl_account_income_fee_id').removeAttr("required");
                $('#gl_account_income_penalty_id').removeAttr("required");
            }
            if ($('#accounting_rule').val() == "cash") {
                //disable all accounting
                $('#accounting').show();
                $('#gl_account_savings_reference_id').attr("required", "required");
                $('#gl_account_overdraft_portfolio_id').attr("required", "required");
                $('#gl_account_savings_control_id').attr("required", "required");
                $('#gl_account_interest_on_savings_id').attr("required", "required");
                $('#gl_account_savings_written_off_id').attr("required", "required");
                $('#gl_account_income_interest_id').attr("required", "required");
                $('#gl_account_income_fee_id').attr("required", "required");
                $('#gl_account_income_penalty_id').attr("required", "required");
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