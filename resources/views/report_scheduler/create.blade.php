@extends('layouts.master')
@section('title')
    {{ trans_choice('general.schedule',1) }} {{ trans_choice('general.report',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.schedule',1) }} {{ trans_choice('general.report',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('report/report_scheduler/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="description"
                           class="control-label col-md-2">{{trans_choice('general.description',2)}}</label>
                    <div class="col-md-3">
                                                     <textarea name="description" class="form-control"
                                                               id="description"
                                                               rows="3">{{old('description')}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="report_start_date"
                           class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="report_start_date" class="form-control date-picker"
                               value="{{old('report_start_date')}}"
                               required id="report_start_date">
                    </div>
                    <div class="col-md-2">
                        <div class="input-group">
                            <input type="text" name="report_start_time" class="form-control time-picker"
                                   value="{{old('report_start_time')}}"
                                   required id="report_start_time">
                            <span class="input-group-addon"> <i class="fa fa-clock-o"> </i></span>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="recurrence_type"
                           class="control-label col-md-2">{{trans_choice('general.recurrence',1)}} {{trans_choice('general.type',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="recurrence_type" class="form-control select2" id="recurrence_type">
                            <option></option>
                            <option value="none">{{trans_choice('general.none',1)}}</option>
                            <option value="schedule">{{trans_choice('general.schedule',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="recurrence_div" style="display: none;">
                    <label for="recur_frequency"
                           class="control-label col-md-2">{{trans_choice('general.frequency',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="recur_frequency" class="form-control "
                                id="recur_frequency">
                            <option value="daily">{{trans_choice('general.daily',1)}}</option>
                            <option value="monthly">{{trans_choice('general.monthly',1)}}</option>
                            <option value="quarterly">{{trans_choice('general.quarterly',1)}}</option>
                            <option value="biannual">{{trans_choice('general.biannual',1)}}</option>
                            <option value="annually">{{trans_choice('general.annually',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_recipients"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}} {{trans_choice('general.recipient',2)}}</label>
                    <div class="col-md-3">
                        <textarea name="email_recipients" class="form-control"
                                  id="email_recipients" placeholder="email1@domain.com,email2@domain.com"
                                  rows="3" required>{{old('email_recipients')}}</textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_subject"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}} {{trans_choice('general.subject',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="email_subject" class="form-control"
                               value="{{old('email_subject')}}"
                               required id="email_subject">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email_message"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}} {{trans_choice('general.message',1)}}</label>
                    <div class="col-md-3">
                        <textarea name="email_message" class="form-control"
                                  id="email_message" placeholder=""
                                  rows="3" required>{{old('email_message')}}</textarea>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="email_attachment_file_format"
                           class="control-label col-md-2">{{trans_choice('general.email_attachment_file_format',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="email_attachment_file_format" class="form-control "
                                id="email_attachment_file_format"
                                required>
                            <option></option>
                            <option value="pdf">{{trans_choice('general.pdf',1)}}</option>
                            <option value="csv">{{trans_choice('general.csv',1)}}</option>
                            <option value="xlsx">{{trans_choice('general.xls',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="active"
                           class="control-label col-md-2">{{trans_choice('general.active',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="active" class="form-control "
                                id="active"
                                required>
                            <option></option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="report_category"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.category',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="report_category" class="form-control select2"
                                id="report_category"
                                required>
                            <option></option>
                            <option value="client_report">{{trans_choice('general.client',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="loan_report">{{trans_choice('general.loan',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="financial_report">{{trans_choice('general.financial',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="group_report"
                                    class="hidden">{{trans_choice('general.group',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="savings_report">{{trans_choice('general.savings',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="organisation_report">{{trans_choice('general.organisation',1)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="client_report_div" style="display: none;">
                    <label for="client_report_name"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.name',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="client_report_name" class="form-control select2"
                                id="client_report_name">
                            <option></option>
                            <option value="client_numbers_report">{{trans_choice('general.client',1)}} {{trans_choice('general.number',2)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="loan_report_div" style="display: none;">
                    <label for="loan_report_name"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.name',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="loan_report_name" class="form-control select2"
                                id="loan_report_name">
                            <option></option>
                            <option value="collection_sheet">{{trans_choice('general.collection',1)}} {{trans_choice('general.sheet',1)}}</option>
                            <option value="repayments_report">{{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}</option>
                            <option value="expected_repayments_report">{{trans_choice('general.expected',1)}} {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}</option>
                            <option value="arrears_report">{{trans_choice('general.arrears',1)}} {{trans_choice('general.report',1)}}</option>
                            <option value="disbursed_loans_report">{{trans_choice('general.disbursed',1)}} {{trans_choice('general.loan',2)}} {{trans_choice('general.report',1)}}</option>
                            <option value="loan_portfolio_report">{{trans_choice('general.loan',1)}} {{trans_choice('general.portfolio',1)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="financial_report_div" style="display: none;">
                    <label for="financial_report_name"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.name',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="financial_report_name" class="form-control select2"
                                id="financial_report_name">
                            <option></option>
                            <option value="balance_sheet">{{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}</option>
                            <option value="trial_balance">{{trans_choice('general.trial',2)}} {{trans_choice('general.balance',1)}}</option>
                            <option value="profit_and_loss">{{trans_choice('general.income',1)}} {{trans_choice('general.statement',1)}}</option>
                            <option value="provisioning">{{trans_choice('general.provisioning',1)}}</option>
                            <option value="journals_report">{{trans_choice('general.journal',2)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="organisation_report_div" style="display: none;">
                    <label for="organisation_report_name"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.name',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="organisation_report_name" class="form-control select2"
                                id="organisation_report_name">
                            <option></option>
                            <option value="products_summary">{{trans_choice('general.product',2)}} {{trans_choice('general.summary',1)}}</option>
                            <option value="audit_report">{{trans_choice('general.general',2)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="savings_report_div" style="display: none;">
                    <label for="savings_report_name"
                           class="control-label col-md-2">{{trans_choice('general.report',1)}} {{trans_choice('general.name',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="savings_report_name" class="form-control select2"
                                id="savings_report_name">
                            <option></option>
                            <option value="savings_transaction_report">{{trans_choice('general.savings',2)}} {{trans_choice('general.transaction',1)}}</option>
                            <option value="savings_balance_report">{{trans_choice('general.savings',2)}} {{trans_choice('general.balance',1)}} {{trans_choice('general.report',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="report_parameters_div" style="display: none;">
                    <div class="form-group" id="start_date_type_div" style="display: none;">
                        <label for="start_date_type"
                               class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}
                        </label>
                        <div class="col-md-3">
                            <select name="start_date_type" class="form-control select2"
                                    id="start_date_type">
                                <option></option>
                                <option value="date_picker">{{trans_choice('general.use',1)}} {{trans_choice('general.date',1)}} {{trans_choice('general.picker',1)}}</option>
                                <option value="today">{{trans_choice('general.use_today_date',2)}}</option>
                                <option value="yesterday">{{trans_choice('general.use_yesterday_date',2)}}</option>
                                <option value="tomorrow">{{trans_choice('general.use_tomorrow_date',2)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="start_date_div" style="display: none;">
                        <label for="start_date"
                               class="control-label col-md-2"></label>
                        <div class="col-md-3">
                            <input type="text" name="start_date" class="form-control date-picker"
                                   value=""
                                   id="start_date">
                        </div>
                    </div>
                    <div class="form-group" id="end_date_type_div" style="display: none;">
                        <label for="end_date_type"
                               class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}
                        </label>
                        <div class="col-md-3">
                            <select name="end_date_type" class="form-control select2"
                                    id="end_date_type">
                                <option></option>
                                <option value="date_picker">{{trans_choice('general.use',1)}} {{trans_choice('general.date',1)}} {{trans_choice('general.picker',1)}}</option>
                                <option value="today">{{trans_choice('general.use_today_date',2)}}</option>
                                <option value="yesterday">{{trans_choice('general.use_yesterday_date',2)}}</option>
                                <option value="tomorrow">{{trans_choice('general.use_tomorrow_date',2)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="end_date_div" style="display: none;">
                        <label for="end_date"
                               class="control-label col-md-2"></label>
                        <div class="col-md-3">
                            <input type="text" name="end_date" class="form-control date-picker"
                                   value=""
                                   id="end_date">
                        </div>
                    </div>
                    <div class="form-group" id="office_id_div" style="display: none;">
                        <label for="office_id"
                               class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                        <div class="col-md-3">
                            <select name="office_id" class="form-control select2" id="office_id">
                                <option value="0">{{trans_choice('general.all',1)}}</option>
                                @foreach(\App\Models\Office::all() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="loan_officer_div" style="display: none;">
                        <label for="loan_officer_id"
                               class="control-label col-md-2">
                            {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                        </label>
                        <div class="col-md-3">
                            <select name="loan_officer_id" class="form-control select2" id="loan_officer_id">
                                <option value="0">{{trans_choice('general.all',1)}}</option>
                                @foreach(\App\Models\User::all() as $key)
                                    @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                        <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="loan_status_div" style="display: none;">
                        <label for="loan_status"
                               class="control-label col-md-2">{{trans_choice('general.status',1)}}</label>
                        <div class="col-md-3">
                            <select name="loan_status" class="form-control select2" id="loan_status">
                                <option value="0">{{trans_choice('general.all',1)}}</option>
                                <option value="disbursed">{{trans_choice('general.active',1)}}</option>
                                <option value="closed">{{trans_choice('general.closed',1)}}</option>
                                <option value="written_off">{{trans_choice('general.written_off',1)}}</option>
                                <option value="rescheduled">{{trans_choice('general.rescheduled',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="loan_product_div" style="display: none;">
                        <label for="loan_product_id"
                               class="control-label col-md-2">{{trans_choice('general.product',1)}}</label>
                        <div class="col-md-3">
                            <select name="loan_product_id" class="form-control select2" id="loan_product_id">
                                <option value="0">{{trans_choice('general.all',1)}}</option>
                                @foreach(\App\Models\LoanProduct::all() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="gl_account_div" style="display: none;">
                        <label for="gl_account_id"
                               class="control-label col-md-2">{{trans_choice('general.account',1)}}</label>
                        <div class="col-md-3">
                            <select name="gl_account_id" class="form-control select2" id="gl_account_id">
                                <option value="0">{{trans_choice('general.all',1)}}</option>
                                @foreach(\App\Models\GlAccount::all() as $key)
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
                    <button type="submit"
                            class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')
    <script>

        $("#recurrence_type").change(function (e) {
            if ($("#recurrence_type").val() == "none") {
                $("#recurrence_div").hide();
                $("#recur_frequency").removeAttr("required");
            }
            if ($("#recurrence_type").val() == "schedule") {
                $("#recurrence_div").show();
                $("#recur_frequency").attr("required","required");
            }
        });
        $("#report_category").change(function (e) {
            if ($("#report_category").val() == "client_report") {
                $("#client_report_div").show();
                $("#loan_report_div").hide();
                $("#financial_report_div").hide();
                $("#group_report_div").hide();
                $("#savings_report_div").hide();
                $("#organisation_report_div").hide();
                $("#client_report_name").attr("required", "required");
                $("#organisation_report_name").removeAttr("required");
                $("#savings_report_name").removeAttr("required");
                $("#group_report_name").removeAttr("required");
                $("#financial_report_name").removeAttr("required");
                $("#loan_report_name").removeAttr("required");
            }
            if ($("#report_category").val() == "loan_report") {
                $("#loan_report_div").show();
                $("#loan_report_name").attr("required", "required");
                $("#client_report_div").hide();
                $("#financial_report_div").hide();
                $("#group_report_div").hide();
                $("#savings_report_div").hide();
                $("#organisation_report_div").hide();
                $("#organisation_report_name").removeAttr("required");
                $("#savings_report_name").removeAttr("required");
                $("#group_report_name").removeAttr("required");
                $("#financial_report_name").removeAttr("required");
                $("#client_report_name").removeAttr("required");
            }
            if ($("#report_category").val() == "financial_report") {
                $("#financial_report_div").show();
                $("#financial_report_name").attr("required", "required");
                $("#client_report_div").hide();
                $("#loan_report_div").hide();
                $("#group_report_div").hide();
                $("#savings_report_div").hide();
                $("#organisation_report_div").hide();
                $("#organisation_report_name").removeAttr("required");
                $("#savings_report_name").removeAttr("required");
                $("#group_report_name").removeAttr("required");
                $("#loan_report_name").removeAttr("required");
                $("#client_report_name").removeAttr("required");
            }
            if ($("#report_category").val() == "group_report") {
                $("#group_report_div").show();
                $("#group_report_name").attr("required", "required");
                $("#client_report_div").hide();
                $("#loan_report_div").hide();
                $("#financial_report_div").hide();
                $("#savings_report_div").hide();
                $("#organisation_report_div").hide();
                $("#organisation_report_name").removeAttr("required");
                $("#savings_report_name").removeAttr("required");
                $("#financial_report_name").removeAttr("required");
                $("#loan_report_name").removeAttr("required");
                $("#client_report_name").removeAttr("required");
            }
            if ($("#report_category").val() == "savings_report") {
                $("#savings_report_div").show();
                $("#savings_report_name").attr("required", "required");
                $("#client_report_div").hide();
                $("#loan_report_div").hide();
                $("#financial_report_div").hide();
                $("#group_report_div").hide();
                $("#organisation_report_div").hide();
                $("#organisation_report_name").removeAttr("required");
                $("#group_report_name").removeAttr("required");
                $("#financial_report_name").removeAttr("required");
                $("#loan_report_name").removeAttr("required");
                $("#client_report_name").removeAttr("required");
            }
            if ($("#report_category").val() == "organisation_report") {
                $("#organisation_report_div").show();
                $("#organisation_report_name").attr("required", "required");
                $("#client_report_div").hide();
                $("#loan_report_div").hide();
                $("#financial_report_div").hide();
                $("#group_report_div").hide();
                $("#savings_report_div").hide();
                $("#savings_report_name").removeAttr("required");
                $("#group_report_name").removeAttr("required");
                $("#financial_report_name").removeAttr("required");
                $("#loan_report_name").removeAttr("required");
                $("#client_report_name").removeAttr("required");
            }
        });
        $("#start_date_type").change(function (e) {
            if ($("#start_date_type").val() == "date_picker") {
                $("#start_date_div").show();
            } else {
                $("#start_date_div").hide();
            }
        });
        $("#end_date_type").change(function (e) {
            if ($("#end_date_type").val() == "date_picker") {
                $("#end_date_div").show();
            } else {
                $("#end_date_div").hide();
            }
        });
        $("#client_report_name").change(function (e) {
            if ($("#start_date_type").val() == "date_picker") {
                $("#start_date_div").show();
            } else {
                $("#start_date_div").hide();
            }
        });
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
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