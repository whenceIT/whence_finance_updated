@extends('layouts.master')
@section('title'){{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}} HERE</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('payroll/'.$payroll->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="template_id" value="{{$template->id}}">

            <div class="box-body">
                <div class="form-group">
                    <label for="user_id"
                           class="control-label col-md-3">
                        {{trans_choice('general.staff',1)}}
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title=""></i>
                    </label>
                    <div class="col-md-3">
                        <select name="user_id" class="form-control select2" id="user_id" required readonly="">
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($payroll->user_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>


                <div class="">
                    <table width="100%">
                        <tbody>
                        <tr>
                            <td style="padding-bottom:10px;">
                                <table width="100%" class="borderOk">
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top;" width="50%">

                                            <table width="100%" id="payslip_employee_header">
                                                <tbody>
                                                <tr>
                                                    <td width="50%"
                                                        class="cell_format">{{trans_choice('general.staff',1)}} {{trans_choice('general.name',1)}}</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="employee_name" class="form-control"
                                                                   value="{{$payroll->employee_name}}"
                                                                   id="employee_name" required>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach($top_left as $key)
                                                    <tr>
                                                        <td width="50%" class="cell_format">{{$key->name}}</td>
                                                        <td width="50%" class="cell_format">
                                                            <div class="margin text-bold">
                                                                <input type="text" name="{{$key->id}}"
                                                                       value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                       class="form-control">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </td>

                                        <td style="vertical-align: top" width="50%">

                                            <table width="100%" id="pay_period_and_salary">

                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin">
                                                            <b>{{trans_choice('general.payroll',1)}} {{trans_choice('general.date',1)}}</b>
                                                        </div>
                                                    </td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="date"
                                                                   class="form-control date-picker" value="{{$payroll->date}}"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%"
                                                        class="cell_format">Company Name</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="business_name"
                                                                   class="form-control"
                                                                   value="{{$payroll->business_name}}"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach($top_right as $key)
                                                    <tr>
                                                        <td width="50%" class="cell_format">{{$key->name}}</td>
                                                        <td width="50%" class="cell_format">
                                                            <div class="margin text-bold">
                                                                <input type="text" name="{{$key->id}}"
                                                                       value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                       class="form-control">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                            <!--Pay Period and Salary-->
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <table width="100%" class="borderOk">
                                    <tbody>
                                    <tr>
                                        <td style="vertical-align: top" width="50%" class="borderRight">

                                            <table width="100%" id="hours_and_earnings">
                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="bg-navy">
                                                        <b>{{trans_choice('general.description',1)}}</b></td>
                                                    <td width="50%" class="bg-navy">
                                                        <b>{{trans_choice('general.amount',1)}}</b></td>
                                                </tr>
                                                <?php
                                                $count = 0;
                                                foreach($bottom_left as $key){
                                                ?>
                                                <tr>
                                                    <td width="50%" class="cell_format">{{$key->name}}GCYC</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" onkeyup="refresh_totals()"
                                                                   name="{{$key->id}}"
                                                                   value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                   class="form-control" id="bottom_left{{$count}}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $count++;
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <!--Hours and Earnings-->
                                        </td>

                                        <td width="50%" valign="top">
                                            <table width="100%" id="pre_tax_deductions">
                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="bg-navy">
                                                        <b>{{trans_choice('general.description',1)}}</b></td>
                                                    <td width="50%" class="bg-navy">
                                                        <b>{{trans_choice('general.amount',1)}}</b></td>
                                                </tr>
                                                <?php
                                                $count = 0;
                                                foreach($bottom_right as $key){
                                                ?>
                                                <tr>
                                                    <td width="50%" class="cell_format">{{$key->name}}</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" onkeyup="refresh_totals()"
                                                                   name="{{$key->id}}"
                                                                   value="@if(\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()) {{\App\Models\PayrollMeta::where('payroll_template_meta_id',$key->id)->where('payroll_id',$payroll->id)->first()->value}} @endif"
                                                                   class="form-control" id="bottom_right{{$count}}">
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                $count++;
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                            <!--Pre-Tax Deductions-->
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%" class="bg-gray">
                                            <table width="100%" id="gross_pay">
                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin">
                                                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.pay',1)}}</b>
                                                        </div>
                                                    </td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="total_pay" class="form-control" value="{{\App\Helpers\GeneralHelper::single_payroll_total_pay($payroll->id)}}"
                                                                   id="total_pay" readonly>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>

                                        </td>
                                        <td width="50%" class="bg-gray">

                                            <table width="100%" id="gross_pay">
                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin">
                                                            <b>{{trans_choice('general.total',1)}} {{trans_choice('general.deduction',2)}}</b>
                                                        </div>
                                                    </td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="total_deductions"
                                                                   class="form-control" value="{{\App\Helpers\GeneralHelper::single_payroll_total_deductions($payroll->id)}}"
                                                                   id="total_deductions" readonly>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="50%">
                                            <br>
                                        </td>
                                        <td width="50%" class="bg-gray">
                                            <table width="100%" id="gross_pay">
                                                <tbody>
                                                <tr>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin">
                                                            <b>{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</b>
                                                        </div>
                                                    </td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="net_pay" class="form-control" value="{{\App\Helpers\GeneralHelper::single_payroll_pay($payroll->id)}}"
                                                                   id="net_pay" readonly>
                                                        </div>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-top:10px;">
                                <table width="100%" class="borderOk" id="net_pay_distribution">
                                    <tbody>
                                    <tr>
                                        <td colspan="5" class="bg-navy">
                                            <b>{{trans_choice('general.net_pay_distribution',1)}}</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="cell_format">
                                            <div class="margin">
                                                <b>{{trans_choice('general.payment',1)}} {{trans_choice('general.method',1)}}</b>
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin">
                                                <b>{{trans_choice('general.bank',1)}} {{trans_choice('general.name',1)}}</b>
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin">
                                                <b>{{trans_choice('general.account',1)}} {{trans_choice('general.number',1)}}</b>
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin">
                                                <b>{{trans_choice('general.description',1)}}</b>
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin">
                                                <b>{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}</b>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="payment_method" class="form-control" value="{{$payroll->payment_method}}"
                                                       id="payment_method">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="bank_name" class="form-control" value="{{$payroll->bank_name}}"
                                                       id="bank_name">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="account_number" class="form-control" value="{{$payroll->account_number}}"
                                                       id="account_number">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="description" class="form-control" value="{{$payroll->description}}"
                                                       id="description">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="number" name="paid_amount" class="form-control" value="{{$payroll->paid_amount}}"
                                                       id="paid_amount" required>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!--Net Pay Distribution-->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" class="borderOk" style="margin-top:10px" id="messages">
                                    <tbody>
                                    <tr>
                                        <td width="100%" class="cell_format">
                                            <div class="margin"><b>{{trans_choice('general.comment',2)}}</b></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="100%" class="cell_format">
                                            <div class="margin text-bold">
                                                <textarea name="comments" class="form-control">{{$payroll->comments}}</textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <!--Messages-->
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="form-group">
                        <label for="recurring"
                               class="control-label col-md-2">{{trans_choice('general.recurring',1)}}</label>
                        <div class="col-md-3">
                            <select name="recurring" class="form-control select2" id="recurring"
                                    required>
                                <option value="0" @if($payroll->recurring==0) selected @endif>{{trans_choice('general.no',1)}}</option>
                                <option value="1" @if($payroll->recurring==1) selected @endif>{{trans_choice('general.yes',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div id="recur">
                        <div class="form-group">
                            <label for="recur_frequency"
                                   class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.frequency',1)}}</label>
                            <div class="col-md-3">
                                <input type="number" name="recur_frequency" class="form-control"
                                       value="{{$payroll->recur_frequency}}"
                                       id="recur_frequency">
                            </div>
                            <label for="recur_type"
                                   class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.type',1)}}</label>
                            <div class="col-md-3">
                                <select name="recur_type" class="form-control select2" id="recur_type">
                                    <option value="days" @if($payroll->recur_type=="days") selected @endif>{{trans_choice('general.day',1)}}</option>
                                    <option value="weeks" @if($payroll->recur_type=="weeks") selected @endif>{{trans_choice('general.week',1)}}</option>
                                    <option value="months" @if($payroll->recur_type=="months") selected @endif>{{trans_choice('general.month',1)}}</option>
                                    <option value="years" @if($payroll->recur_type=="years") selected @endif>{{trans_choice('general.year',1)}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="recur_start_date"
                                   class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="recur_start_date" class="form-control date-picker"
                                       value="{{$payroll->recur_start_date}}"
                                       id="recur_start_date">
                            </div>
                            <label for="recur_end_date"
                                   class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="recur_end_date" class="form-control date-picker"
                                       value="{{$payroll->recur_end_date}}"
                                       id="recur_end_date">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
    </div>
    <!-- /.box -->
@endsection

@section('footer-scripts')

    <script>
        $('#user_id').change(function (e) {
            $.ajax({
                type: 'GET',
                url: '{!! url('payroll/getUser') !!}/' + $('#user_id').val(),
                success: function (data) {
                    $('#employee_name').val(data);
                }
            });
        })
        $(document).ready(function (e) {
            if ($('#recurring').val() == '1') {
                $('#recur').show();
                $('#recur_frequency').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recur_type').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recur_frequency').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recur_type').removeAttr('required');
            }
            $('#recurring').change(function () {
                if ($('#recurring').val() == '1') {
                    $('#recur').show();
                    $('#recur_frequency').attr('required', 'required');
                    $('#recur_type').attr('required', 'required');
                    $('#recur_start_date').attr('required', 'required');
                } else {
                    $('#recur').hide();
                    $('#recur_frequency').removeAttr('required');
                    $('#recur_start_date').removeAttr('required');
                    $('#recur_type').removeAttr('required');
                }
            })
        })
        function refresh_totals(e) {
            var totalPay = 0;
            var totalDeductions = 0;
            var totalPaid = 0;
            var netPay = 0;
            for (var i = 0; i < '{{count($bottom_left)}}'; i++) {
                var pay = document.getElementById("bottom_left" + i).value;
                if (pay == "")
                    pay = 0;
                totalPay = parseFloat(totalPay) + parseFloat(pay);
            }
            for (var i = 0; i < '{{count($bottom_right)}}'; i++) {
                var deduction = document.getElementById("bottom_right" + i).value;
                if (deduction == "")
                    deduction = 0;
                totalDeductions = parseFloat(totalDeductions) + parseFloat(deduction);
            }

            document.getElementById("total_pay").value = totalPay;
            document.getElementById("total_deductions").value = totalDeductions;
            document.getElementById("net_pay").value = totalPay - totalDeductions;
            document.getElementById("paid_amount").value = totalPay - totalDeductions;
        }
        $(".form-horizontal").validate();
    </script>
@endsection