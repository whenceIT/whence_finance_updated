@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.campaign',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.campaign',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('communication/store')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               required id="name">
                    </div>

                </div>
                <div class="form-group">
                    <label for="type"
                           class="control-label col-md-3">{{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="type" class="form-control select2" id="type" required>
                            <option></option>
                            <option value="sms">{{trans_choice('general.sms',1)}}</option>
                            <option value="email">{{trans_choice('general.email',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="recurrence_type"
                           class="control-label col-md-3">{{trans_choice('general.campaign',1)}} {{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="recurrence_type" class="form-control select2" id="recurrence_type" required>
                            <option></option>
                            <option value="none">{{trans_choice('general.direct',1)}}</option>
                            <!--<option value="schedule">{{trans_choice('general.schedule',1)}}</option>-->
                        </select>
                    </div>
                </div>
                <div id="recurrence_div" style="display: none;">
                    <div class="form-group">
                        <label for="report_start_date"
                               class="control-label col-md-3">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
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

                        <label for="recur_interval"
                               class="control-label col-md-3">{{trans_choice('general.every',1)}}</label>
                        <div class="col-md-3">
                            <input type="number" name="recur_interval" class="form-control"
                                   value="{{old('recur_interval')}}"
                                   required id="recur_interval">
                        </div>
                        <div class="col-md-2">
                            <select name="recur_frequency" class="form-control "
                                    id="recur_frequency">
                                <option value="days">{{trans_choice('general.day',1)}}</option>
                                <option value="weeks">{{trans_choice('general.week',1)}}</option>
                                <option value="months">{{trans_choice('general.month',1)}}</option>
                                <option value="years">{{trans_choice('general.year',1)}}</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group" id="">
                    <label for="recipients_category"
                           class="control-label col-md-3">{{trans_choice('general.recipient',2)}}</label>
                    <div class="col-md-3">
                        <select name="recipients_category" class="form-control select2"
                                id="recipients_category">
                            <option></option>
                            <option value="active_clients">{{trans_choice('general.active',1)}} {{trans_choice('general.client',2)}}</option>
                            <option value="prospective_clients">{{trans_choice('general.prospective',1)}} {{trans_choice('general.client',2)}}</option>
                            <option value="active_loans">{{trans_choice('general.active',1)}} {{trans_choice('general.loan',1)}}  {{trans_choice('general.client',2)}}</option>
                            <option value="overdue_loans">{{trans_choice('general.overdue',1)}} {{trans_choice('general.loan',2)}}  </option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="report_attachment_div" style="display: none;">
                    <label for="report_attachment"
                           class="control-label col-md-3">{{trans_choice('general.report',1)}} {{trans_choice('general.attachment',1)}}</label>
                    <div class="col-md-3">
                        <select name="report_attachment" class="form-control select2"
                                id="report_attachment">
                            <option></option>
                            <option value="loan_schedule">{{trans_choice('general.loan',1)}} {{trans_choice('general.schedule',1)}}</option>
                            <option value="loan_statement">{{trans_choice('general.loan',1)}} {{trans_choice('general.statement',1)}}</option>
                            <option value="savings_statement">{{trans_choice('general.savings',1)}} {{trans_choice('general.statement',1)}}</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="office_id_div">
                    <label for="office_id"
                           class="control-label col-md-3">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0">{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" id="loan_officer_id_div">
                    <label for="loan_officer_id"
                           class="control-label col-md-3">
                        {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="loan_officer_id" class="form-control select2" id="loan_officer_id" required>
                            <option value="0">{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group" id="from_day_div" style="display: none">
                    <label for="from_day"
                           class="control-label col-md-3">{{trans_choice('general.from',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="from_day" class="form-control"
                               value="1"
                               id="from_day">
                    </div>

                </div>
                <div class="form-group" id="to_day_div" style="display: none">
                    <label for="to_day"
                           class="control-label col-md-3">{{trans_choice('general.to',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="to_day" class="form-control"
                               value="2"
                               id="to_day">
                    </div>

                </div>
                <div class="form-group" id="email_subject_div" style="display: none">
                    <label for="email_subject"
                           class="control-label col-md-3">{{trans_choice('general.email',1)}} {{trans_choice('general.subject',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="email_subject" class="form-control"
                               id="email_subject">
                    </div>
                </div>
                <div class="form-group">
                    <label for="message"
                           class="control-label col-md-3">{{trans_choice('general.message',1)}}</label>
                    <div class="col-md-9">
                        <textarea name="message" class="form-control tinymce"
                                  id="message">{{old('message')}}</textarea>
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
        $("#recurrence_type").change(function (e) {
            if ($("#recurrence_type").val() == "none") {
                $("#recurrence_div").hide();
                $("#recur_frequency").removeAttr("required");
                $("#recur_interval").removeAttr("required");
                $("#report_start_date").removeAttr("required");
                $("#report_start_time").removeAttr("required");
            }
            if ($("#recurrence_type").val() == "schedule") {
                $("#recurrence_div").show();
                $("#recur_frequency").attr("required", "required");
                $("#recur_interval").attr("required", "required");
                $("#report_start_date").attr("required", "required");
                $("#report_start_time").attr("required", "required");
            }
        });
        $("#type").change(function (e) {
            if ($("#type").val() == "sms") {
                $("#report_attachment_div").hide();
                $("#email_subject_div").hide();
                $("#email_subject").removeAttr("required");
                tinymce.get("message").remove();
                //$("#from_day_div").hide();
                //$("#to_day_div").hide();
                //$("#from_day").removeAttr("required");
                //$("#to_day").removeAttr("required");
            }
            if ($("#type").val() == "email") {
                $("#report_attachment_div").show();
                $("#email_subject_div").show();
                $("#email_subject").attr("required","required");
                $("#message").addClass("tinymce");
                tinymce.init({
                    selector: '#message'
                });
                //$("#from_day_div").show();
                //$("#from_day").attr("required","required");
                //$("#to_day").attr("required","required");
            }
        });
        $("#recipients_category").change(function (e) {
            if ($("#recipients_category").val() == "overdue_loans") {

            } else {

            }

        });
        $(".form-horizontal").validate({
            rules: {
                field: {
                    required: true,
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