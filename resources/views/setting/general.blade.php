@extends('layouts.master')
@section('title')
    {{ trans_choice('general.setting',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <form method="post" action="{{ url('setting/general/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-header with-border">
                <h3 class="box-title">{{ trans_choice('general.setting',2) }}</h3>

                <div class="box-tools">
                    <button type="submit" class="btn btn-info">{{ trans('general.save') }}</button>
                </div>
            </div>
            <div class="box-body">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li><a href="#general" data-toggle="tab">{{ trans('general.general') }}</a></li>
                        <li><a href="#email_templates"
                               data-toggle="tab">{{ trans_choice('general.email',1) }} {{ trans_choice('general.template',2) }}</a>
                        </li>
                        <li><a href="#sms_templates"
                               data-toggle="tab">{{ trans_choice('general.sms',1) }} {{ trans_choice('general.template',2) }}</a>
                        </li>
                        <li class="active"><a href="#system"
                                              data-toggle="tab">{{ trans_choice('general.system',1) }}</a>
                        </li>
                        <li><a href="#update" data-toggle="tab">{{ trans_choice('general.update',2) }}</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane" id="general">
                            <div class="form-group">
                                <label for="company_name"
                                       class="col-sm-2 control-label">{{trans_choice('general.name',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="company_name" class="form-control"
                                           placeholder="{{trans_choice('general.company_name',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}"
                                           required id="company_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_email"
                                       class="col-sm-2 control-label">{{trans_choice('general.email',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="company_email" class="form-control"
                                           placeholder="{{trans_choice('general.company_email',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','company_email')->first()->setting_value}}"
                                           required id="company_email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_website"
                                       class="col-sm-2 control-label">{{trans_choice('general.website',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="company_website" class="form-control"
                                           placeholder="{{trans_choice('general.company_website',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','company_website')->first()->setting_value}}"
                                           id="company_website">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_country"
                                       class="col-sm-2 control-label">{{trans_choice('general.country',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="company_country" class="form-control select2" id="company_country"
                                            required>
                                        @foreach(\App\Models\Country::all() as $country)
                                            <option value="{{$country->id}}"
                                                    @if(\App\Models\Setting::where('setting_key','company_country')->first()->setting_value==$country->id) selected @endif>{{$country->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_currency"
                                       class="col-sm-2 control-label">{{trans_choice('general.currency',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="company_currency" class="form-control select2" id="company_currency"
                                            required>
                                        @foreach(\App\Models\Currency::all() as $key)
                                            <option value="{{$key->id}}"
                                                    @if(\App\Models\Setting::where('setting_key','company_currency')->first()->setting_value==$key->id) selected @endif>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="company_logo"
                                       class="col-sm-2 control-label">{{trans_choice('general.logo',1)}}</label>
                                <div class="col-sm-10">
                                    @if(!empty(\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value))
                                        <img src="{{ url(asset('uploads/'.\App\Models\Setting::where('setting_key','company_logo')->first()->setting_value)) }}"
                                             class="img-responsive"/>
                                    @endif
                                    <input type="file" name="company_logo" class="form-control" id="company_logo">
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-info">{{ trans('general.save') }}</button>
                                </div>
                            </div>
                        </div>
                        <!-- /.tab-pane -->

                        <div class="tab-pane" id="email_templates">
                            <p>Universal tags to use: <span class="label label-info">{name}</span> <span
                                        class="label label-info">{activationLink}</span> <span class="label label-info">{transactionId}</span>
                                <span class="label label-info">{amount}</span>
                            </p>

                            <div class="form-group">
                                <label for="password_reset_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.password_reset_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="password_reset_subject" class="form-control"
                                           placeholder="{{trans_choice('general.password_reset_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','password_reset_subject')->first()->setting_value}}"
                                           required id="password_reset_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password_reset_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.password_reset_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="password_reset_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.password_reset_template',1)}}"
                                              id="password_reset_template">{{\App\Models\Setting::where('setting_key','password_reset_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_received_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.payment_received_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_received_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.payment_received_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','payment_received_email_subject')->first()->setting_value}}"
                                           required id="payment_received_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_received_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.payment_received_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="payment_received_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.payment_received_email_template',1)}}"
                                              id="payment_received_email_template">{{\App\Models\Setting::where('setting_key','payment_received_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.payment_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="payment_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.payment_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','payment_email_subject')->first()->setting_value}}"
                                           required id="payment_email_subject">
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="payment_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.payment_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="payment_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.payment_email_template',1)}}"
                                              id="payment_email_template">{{\App\Models\Setting::where('setting_key','payment_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="client_statement_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.client_statement_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="client_statement_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.client_statement_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','client_statement_email_subject')->first()->setting_value}}"
                                           required id="client_statement_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="client_statement_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.client_statement_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="client_statement_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.client_statement_email_template',1)}}"
                                              id="client_statement_email_template">{{\App\Models\Setting::where('setting_key','client_statement_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_statement_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_statement_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_statement_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_statement_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_statement_email_subject')->first()->setting_value}}"
                                           required id="loan_statement_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_statement_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_statement_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_statement_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_statement_email_template',1)}}"
                                              id="loan_statement_email_template">{{\App\Models\Setting::where('setting_key','loan_statement_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_schedule_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_schedule_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_schedule_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_schedule_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_schedule_email_subject')->first()->setting_value}}"
                                           required id="loan_schedule_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_schedule_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_schedule_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_schedule_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_schedule_email_template',1)}}"
                                              id="loan_schedule_email_template">{{\App\Models\Setting::where('setting_key','loan_schedule_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_overdue_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_overdue_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_overdue_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_overdue_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_overdue_email_subject')->first()->setting_value}}"
                                           required id="loan_overdue_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_overdue_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_overdue_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_overdue_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_overdue_email_template',1)}}"
                                              id="loan_overdue_email_template">{{\App\Models\Setting::where('setting_key','loan_overdue_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="missed_payment_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.missed_payment_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="missed_payment_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.missed_payment_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','missed_payment_email_subject')->first()->setting_value}}"
                                           required id="missed_payment_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="missed_payment_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.missed_payment_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="missed_payment_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.missed_payment_email_template',1)}}"
                                              id="missed_payment_email_template">{{\App\Models\Setting::where('setting_key','missed_payment_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_payment_reminder_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_payment_reminder_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_payment_reminder_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_payment_reminder_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_payment_reminder_subject')->first()->setting_value}}"
                                           required id="loan_payment_reminder_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_payment_reminder_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_payment_reminder_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_payment_reminder_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_payment_reminder_email_template',1)}}"
                                              id="loan_payment_reminder_email_template">{{\App\Models\Setting::where('setting_key','loan_payment_reminder_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="loan_approved_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_approved_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_approved_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_approved_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_approved_email_subject')->first()->setting_value}}"
                                           required id="loan_approved_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_approved_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_approved_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_approved_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_approved_email_template',1)}}"
                                              id="loan_approved_email_template">{{\App\Models\Setting::where('setting_key','loan_approved_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="loan_disbursed_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_disbursed_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="loan_disbursed_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.loan_disbursed_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','loan_disbursed_email_subject')->first()->setting_value}}"
                                           required id="loan_disbursed_email_subject">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_disbursed_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_disbursed_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_disbursed_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.loan_disbursed_email_template',1)}}"
                                              id="loan_disbursed_email_template">{{\App\Models\Setting::where('setting_key','loan_disbursed_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="savings_statement_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.savings_statement_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="savings_statement_email_template" class="form-control"
                                           placeholder="{{trans_choice('general.savings_statement_email_template',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','savings_statement_email_template')->first()->setting_value}}"
                                           required id="savings_statement_email_template">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="savings_transaction_email_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.savings_transaction_email_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="savings_transaction_email_template" class="form-control tinymce"
                                              placeholder="{{trans_choice('general.savings_transaction_email_template',1)}}"
                                              id="savings_transaction_email_template">{{\App\Models\Setting::where('setting_key','savings_transaction_email_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="savings_transaction_email_subject"
                                       class="col-sm-2 control-label">{{trans_choice('general.savings_transaction_email_subject',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="savings_transaction_email_subject" class="form-control"
                                           placeholder="{{trans_choice('general.savings_transaction_email_subject',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','savings_transaction_email_subject')->first()->setting_value}}"
                                           required id="savings_transaction_email_subject">
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="sms_templates">
                            <p>Universal tags to use: <span class="label label-info">{name}</span> <span
                                        class="label label-info">{otp}</span>
                            </p>
                            <div class="form-group">
                                <label for="sms_enabled"
                                       class="col-sm-2 control-label">{{trans_choice('general.sms_enabled',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="sms_enabled" class="form-control" id="sms_enabled">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','sms_enabled')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','sms_enabled')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="active_sms"
                                       class="col-sm-2 control-label">{{trans_choice('general.active_sms',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="active_sms" class="form-control"
                                            id="active_sms">
                                        @foreach(\App\Models\SmsGateway::all() as $sms_gateway)
                                            <option value="{{$sms_gateway->id}}"
                                                    @if(\App\Models\Setting::where('setting_key','active_sms')->first()->setting_value==$sms_gateway->id) selected @endif>{{$sms_gateway->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payment_received_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.payment_received_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="payment_received_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.payment_received_sms_template',1)}}"
                                              id="payment_received_sms_template">{{\App\Models\Setting::where('setting_key','payment_received_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_overdue_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_overdue_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_overdue_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.loan_overdue_sms_template',1)}}"
                                              id="loan_overdue_sms_template">{{\App\Models\Setting::where('setting_key','loan_overdue_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_payment_reminder_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_payment_reminder_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_payment_reminder_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.loan_payment_reminder_sms_template',1)}}"
                                              id="loan_payment_reminder_sms_template">{{\App\Models\Setting::where('setting_key','loan_payment_reminder_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="missed_payment_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.missed_payment_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="missed_payment_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.missed_payment_sms_template',1)}}"
                                              id="missed_payment_sms_template">{{\App\Models\Setting::where('setting_key','missed_payment_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_approved_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_approved_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_approved_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.loan_approved_sms_template',1)}}"
                                              id="loan_approved_sms_template">{{\App\Models\Setting::where('setting_key','loan_approved_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_disbursed_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_disbursed_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="loan_disbursed_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.loan_disbursed_sms_template',1)}}"
                                              id="loan_disbursed_sms_template">{{\App\Models\Setting::where('setting_key','loan_disbursed_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="savings_transaction_sms_template"
                                       class="col-sm-2 control-label">{{trans_choice('general.savings_transaction_sms_template',1)}}</label>
                                <div class="col-sm-10">
                                    <textarea name="savings_transaction_sms_template" class="form-control "
                                              placeholder="{{trans_choice('general.savings_transaction_sms_template',1)}}"
                                              id="savings_transaction_sms_template">{{\App\Models\Setting::where('setting_key','savings_transaction_sms_template')->first()->setting_value}}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane active" id="system">
                            <div class="form-group">
                                <label for="enable_cron"
                                       class="col-sm-2 control-label">{{trans_choice('general.cron_enabled',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="enable_cron" class="form-control" id="enable_cron">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','enable_cron')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','enable_cron')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                    <small>Last
                                        Run:@if(!empty(\App\Models\Setting::where('setting_key','cron_last_run')->first()->setting_value)) {{\App\Models\Setting::where('setting_key','cron_last_run')->first()->setting_value}} @else
                                            Never @endif</small>
                                    <br>
                                    <small>Cron Url: {{url('cron')}}</small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="enable_custom_fields"
                                       class="col-sm-2 control-label">{{trans_choice('general.enable_custom_fields',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="enable_custom_fields" class="form-control" id="enable_custom_fields">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_payment_receipt_sms"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_payment_receipt_sms',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_payment_receipt_sms" class="form-control"
                                            id="auto_payment_receipt_sms">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_payment_receipt_sms')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_payment_receipt_sms')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_payment_receipt_email"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_payment_receipt_email',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_payment_receipt_email" class="form-control"
                                            id="auto_payment_receipt_email">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_payment_receipt_email')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_payment_receipt_email')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_approved_auto_email"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_approved_auto_email',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="loan_approved_auto_email" class="form-control"
                                            id="loan_approved_auto_email">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','loan_approved_auto_email')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','loan_approved_auto_email')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_approved_auto_sms"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_approved_auto_sms',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="loan_approved_auto_sms" class="form-control"
                                            id="loan_approved_auto_sms">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','loan_approved_auto_sms')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','loan_approved_auto_sms')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_disbursed_auto_email"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_disbursed_auto_email',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="loan_disbursed_auto_email" class="form-control"
                                            id="loan_disbursed_auto_email">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','loan_disbursed_auto_email')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','loan_disbursed_auto_email')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="loan_disbursed_auto_sms"
                                       class="col-sm-2 control-label">{{trans_choice('general.loan_disbursed_auto_sms',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="loan_disbursed_auto_sms" class="form-control"
                                            id="loan_disbursed_auto_sms">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','loan_disbursed_auto_sms')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','loan_disbursed_auto_sms')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_repayment_sms_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_repayment_sms_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_repayment_sms_reminder" class="form-control"
                                            id="auto_repayment_sms_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_repayment_sms_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_repayment_sms_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_repayment_email_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_repayment_email_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_repayment_email_reminder" class="form-control"
                                            id="auto_repayment_email_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_repayment_email_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_repayment_email_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_repayment_days"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_repayment_days',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="number" name="auto_repayment_days" class="form-control"
                                           placeholder="{{trans_choice('general.auto_repayment_days',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','auto_repayment_days')->first()->setting_value}}"
                                           required id="auto_repayment_days">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_overdue_repayment_sms_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_repayment_sms_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_overdue_repayment_sms_reminder" class="form-control"
                                            id="auto_overdue_repayment_sms_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_repayment_sms_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_repayment_sms_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="auto_overdue_repayment_email_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_repayment_email_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_overdue_repayment_email_reminder" class="form-control"
                                            id="auto_overdue_repayment_email_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_repayment_email_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_repayment_email_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_overdue_repayment_days"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_repayment_days',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="number" name="auto_overdue_repayment_days" class="form-control"
                                           placeholder="{{trans_choice('general.auto_overdue_repayment_days',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','auto_overdue_repayment_days')->first()->setting_value}}"
                                           required id="auto_overdue_repayment_days">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_overdue_loan_sms_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_loan_sms_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_overdue_loan_sms_reminder" class="form-control"
                                            id="auto_overdue_loan_sms_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_loan_sms_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_loan_sms_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_overdue_loan_email_reminder"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_loan_email_reminder',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="auto_overdue_loan_email_reminder" class="form-control"
                                            id="auto_overdue_loan_email_reminder">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_loan_email_reminder')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','auto_overdue_loan_email_reminder')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="auto_overdue_loan_days"
                                       class="col-sm-2 control-label">{{trans_choice('general.auto_overdue_loan_days',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="number" name="auto_overdue_loan_days" class="form-control"
                                           placeholder="{{trans_choice('general.auto_overdue_loan_days',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','auto_overdue_loan_days')->first()->setting_value}}"
                                           required id="auto_overdue_repayment_days">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="allow_self_registration"
                                       class="col-sm-2 control-label">{{trans_choice('general.allow_self_registration',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="allow_self_registration" class="form-control"
                                            id="allow_self_registration">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','allow_self_registration')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','allow_self_registration')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="allow_client_apply"
                                       class="col-sm-2 control-label">{{trans_choice('general.allow_client_apply',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="allow_client_apply" class="form-control"
                                            id="allow_client_apply">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','allow_client_apply')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','allow_client_apply')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="enable_google_recaptcha"
                                       class="col-sm-2 control-label">{{trans_choice('general.enable_google_recaptcha',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="enable_google_recaptcha" class="form-control"
                                            id="enable_google_recaptcha">
                                        <option value="1"
                                                @if(\App\Models\Setting::where('setting_key','enable_google_recaptcha')->first()->setting_value==1) selected @endif>{{trans('general.yes')}}</option>
                                        <option value="0"
                                                @if(\App\Models\Setting::where('setting_key','enable_google_recaptcha')->first()->setting_value==0) selected @endif>{{trans('general.no')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="google_recaptcha_site_key"
                                       class="col-sm-2 control-label">{{trans_choice('general.google_recaptcha_site_key',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="google_recaptcha_site_key" class="form-control"
                                           placeholder="{{trans_choice('general.google_recaptcha_site_key',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','google_recaptcha_site_key')->first()->setting_value}}"
                                           id="google_recaptcha_site_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="google_recaptcha_secret_key"
                                       class="col-sm-2 control-label">{{trans_choice('general.google_recaptcha_secret_key',1)}}</label>
                                <div class="col-sm-10">
                                    <input type="text" name="google_recaptcha_secret_key" class="form-control"
                                           placeholder="{{trans_choice('general.google_recaptcha_secret_key',1)}}"
                                           value="{{\App\Models\Setting::where('setting_key','google_recaptcha_secret_key')->first()->setting_value}}"
                                           id="google_recaptcha_secret_key">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payroll_gl_account_expense_id"
                                       class="col-sm-2 control-label">{{trans_choice('general.payroll',1)}} {{trans_choice('general.expense',1)}} {{trans_choice('general.account',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="payroll_gl_account_expense_id" class="form-control"
                                            id="payroll_gl_account_expense_id">
                                        <option></option>
                                        @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"expense")->get() as $key)
                                            <option value="{{$key->id}}"
                                                    @if(\App\Models\Setting::where('setting_key','payroll_gl_account_expense_id')->first()->setting_value==$key->id) selected @endif>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="payroll_gl_account_asset_id"
                                       class="col-sm-2 control-label">{{trans_choice('general.payroll',1)}} {{trans_choice('general.asset',1)}} {{trans_choice('general.account',1)}}</label>
                                <div class="col-sm-10">
                                    <select name="payroll_gl_account_asset_id" class="form-control"
                                            id="payroll_gl_account_asset_id">
                                        <option></option>
                                        @foreach(\App\Models\GlAccount::where('active',1)->where('account_type',"asset")->get() as $key)
                                            <option value="{{$key->id}}"
                                                    @if(\App\Models\Setting::where('setting_key','payroll_gl_account_asset_id')->first()->setting_value==$key->id) selected @endif>{{$key->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane" id="update">
                            <div class="form-group">
                                <div class="col-sm-4 text-right">Local Version:</div>

                                <div class="col-sm-4">
                                    <span class="label label-primary">{{\App\Models\Setting::where('setting_key','system_version')->first()->setting_value}}</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 text-right">Server Version:</div>

                                <div class="col-sm-4">
                                    <button class="btn btn-info btn-sm" type="button" id="checkUpdate">Check Version
                                    </button>
                                    <br>
                                    <span class="label label-primary" id="serverVersion"></span>
                                </div>
                            </div>
                            <div id="updateMessage"></div>
                        </div>
                    </div>
                    <!-- /.tab-content -->
                </div>
                <!-- /.nav-tabs-custom -->
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-info pull-right">{{ trans('general.save') }}</button>
            </div>
        </form>
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>
        $('#checkUpdate').click(function (e) {
            $.ajax({
                type: 'POST',
                url: '{{\App\Models\Setting::where('setting_key','update_url')->first()->setting_value}}',
                dataType: 'json',
                success: function (data) {
                    if ("{!! \App\Models\Setting::where('setting_key','system_version')->first()->setting_value !!}}" < data.version) {
                        swal({
                            title: '{{trans_choice('general.update_available',1)}}<br>' + data.version,
                            html: data.notes,
                            type: 'success',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: '{{trans_choice('general.download',1)}}',
                            cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                        }).then(function () {
                            //curl function to download update
                            //notify user that update is in progress, do not navigate from page
                            $('#updateMessage').html("<div class='alert alert-warning'>{{trans_choice('general.do_not_navigate_from_page',1)}}</div>");
                            window.location = "{{url('update/download?url=')}}" + data.url;
                        });
                        $('#serverVersion').html(data.version);
                    } else {
                        swal({
                            title: '{{trans_choice('general.no_update_available',1)}}',
                            text: '{{trans_choice('general.system_is_up_to_date',1)}}',
                            type: 'warning',
                            showCancelButton: false,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: '{{trans_choice('general.ok',1)}}',
                            cancelButtonText: '{{trans_choice('general.cancel',1)}}'
                        })
                    }
                }
                ,
                error: function (e) {
                    alert("There was an error connecting to the server")
                }
            });
        })
    </script>
@endsection
