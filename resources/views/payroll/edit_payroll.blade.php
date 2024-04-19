@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.add',1)}} {{trans_choice('general.payroll',1)}}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>

        <body onload="sum()">
        <form method="post" action="{{url('payroll/'.$payroll_user->id.'/save_payroll')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <input type="hidden" name="template_id" value="{{$template->id}}">

            <div class="box-body">
                <!-- <div class="form-group">
                    <label for="user_id"
                           class="control-label col-md-3">
                        {{trans_choice('general.staff',1)}}
                      
                        <i class="fa fa-question-circle" data-toggle="tooltip"
                           data-title=""></i>
                    </label>
                    <div class="col-md-3">
                        <select name="user_id" class="form-control select2" id="user_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($payroll_user->user_id)->inRole('client'))
                                    <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}} </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div> -->

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
                                                           <p>{{$user->first_name}} {{$user->last_name}}</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach($top_left as $key)
                                                    <tr>
                                                        <td width="50%" class="cell_format">{{$key->name}}</td>
                                                        <td width="50%" class="cell_format">
                                                            <div class="margin text-bold">
                                                                <input type="text" name="{{$key->id}}" id="{{$key->name}}"
                                                                       class="form-control"
                                                                       required>
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
                                                                   class="form-control date-picker" value="{{date("Y-m-d",strtotime($payroll_user->created_at))}}"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="50%"
                                                        class="cell_format">{{trans_choice('general.business',1)}} {{trans_choice('general.name',1)}}</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="text" name="business_name"
                                                                   class="form-control"
                                                                   value="{{\App\Models\Setting::where('setting_key', 'company_name')->first()->setting_value}}"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @foreach($top_right as $key)
                                                    <tr>
                                                        <td width="50%" class="cell_format">{{$key->name}}</td>
                                                        <td width="50%" class="cell_format">
                                                            <div class="margin text-bold">
                                                                <input type="text" name="{{$key->id}}" id="{{$key->name}}"
                                                                       class="form-control"
                                                                       required>
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
                                              
                                                <tr>
                                                    <td width="50%" class="cell_format">Basic Pay</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="basic_pay"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_left"
                                                                   id='basic_pay'
                                                                   value="{{$payroll_user->basic_pay}}"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">Allowances</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="allowances"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_left"
                                                                   id="allowances"
                                                                   value="{{$payroll_user->allowances}}"
                                                                   >
                                                        </div>
                                                    </td>
                                                </tr>

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
                                                   <tr>
                                                    <td width="50%" class="cell_format">Advance deductions</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="advance_deductions"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_right"
                                                                   id="advance_deductions"
                                                                   value="{{$payroll_user->salary_deductions}}"
                                                                   >
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">Penalties/Charges</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="charges"
                                                                onkeyup="sum()"
                                                                   class="form-control bottom_right"
                                                                   id="charges"
                                                                   value="{{$payroll_user->charges}}"
                                                                   >
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">NAPSA</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="NAPSA"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_right"
                                                                   id="NAPSA"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">PAYE</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="PAYE"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_right"
                                                                   id="PAYE"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">NHIMA</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                            <input type="number" name="NHIMA"
                                                                   onkeyup="sum()"
                                                                   class="form-control bottom_right"
                                                                   id="NHIMA"
                                                                   required>
                                                        </div>
                                                    </td>
                                                </tr>

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
                                                            <input type="text" name="total_pay" class="form-control"
                                                                   id="total_pay"  readonly>
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
                                                                   class="form-control"
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
                                                            <input type="text" name="net_pay" class="form-control"
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
                                                <input type="text" name="payment_method" class="form-control"
                                                       id="payment_method">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="bank_name" class="form-control"
                                                       id="bank_name">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="account_number" class="form-control"
                                                       id="account_number">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="text" name="description" class="form-control"
                                                       id="description">
                                            </div>
                                        </td>
                                        <td width="20%" class="cell_format">
                                            <div class="margin text-bold">
                                                <input type="number" name="paid_amount" class="form-control"
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
                                                <textarea name="comments" class="form-control"></textarea>
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
                                <option value="0">{{trans_choice('general.no',1)}}</option>
                                <option value="1">{{trans_choice('general.yes',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div id="recur">
                        <div class="form-group">
                            <label for="recur_frequency"
                                   class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.frequency',1)}}</label>
                            <div class="col-md-3">
                                <input type="number" name="recur_frequency" class="form-control"
                                       value="{{old('recur_frequency')}}"
                                       id="recur_frequency">
                            </div>
                            <label for="recur_type"
                                   class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.type',1)}}</label>
                            <div class="col-md-3">
                                <select name="recur_type" class="form-control select2" id="recur_type">
                                    <option value="days">{{trans_choice('general.day',1)}}</option>
                                    <option value="weeks">{{trans_choice('general.week',1)}}</option>
                                    <option value="months">{{trans_choice('general.month',1)}}</option>
                                    <option value="years">{{trans_choice('general.year',1)}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="recur_start_date"
                                   class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="recur_start_date" class="form-control date-picker"
                                       value=""
                                       id="recur_start_date">
                            </div>
                            <label for="recur_end_date"
                                   class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="recur_end_date" class="form-control date-picker"
                                       value=""
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
        </body>
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

        document.getElementById("PAYE").readOnly = true;
        document.getElementById("NAPSA").readOnly = true;
        document.getElementById("NHIMA").readOnly = true;

        $(".form-horizontal").validate()
        var BasicPay = document.getElementById("Basic Pay").value;
       console.log(BasicPay)
       
       function sum(){
            var BasicPay = document.getElementById('basic_pay').value;
          //  var PerformanceAllowance = document.getElementById("Performance Allowance").value
             var Allowances = document.getElementById("allowances").value
             var SalaryAdvanceDeductions =  document.getElementById("advance_deductions").value
             var PenaltyDeductions = document.getElementById("charges").value
             var GrossPay = (Number(document.getElementById("basic_pay").value) + Number(document.getElementById("allowances").value )
             - Number(document.getElementById("advance_deductions").value) - Number(document.getElementById("charges").value))
            console.log(BasicPay)
            // console.log(GrossPay * 0.05)
             document.getElementById("NAPSA").value = 0.05 * BasicPay;
             document.getElementById('NHIMA').value = 0.01 * BasicPay;
             if(BasicPay <= 5100){
                 document.getElementById('PAYE').value = 0;
             }else if(BasicPay <= 7100){
                 document.getElementById('PAYE').value = (BasicPay - 5100)*0.20
             }else if(BasicPay <= 9200){
                 document.getElementById('PAYE').value = ((BasicPay - 7100)*0.30) + ((7100 - 5100)*0.20)
             }else{
                 document.getElementById('PAYE').value = (BasicPay - 9200)*0.37 + (9200 - 7100)*0.30 + (7100 - 5100)*0.20
             }

            //total_pay
       var TotalPay =  Number(BasicPay) + Number(Allowances)
      var  TotalDeductions = Number(SalaryAdvanceDeductions) + Number(PenaltyDeductions) + Number(document.getElementById('PAYE').value) + Number(document.getElementById("NAPSA").value) +  Number(document.getElementById('NHIMA').value)
       document.getElementById('total_pay').value = TotalPay;
       document.getElementById('total_deductions').value = TotalDeductions;
       document.getElementById('net_pay').value = Number(TotalPay) - Number(TotalDeductions);
       document.getElementById('paid_amount').value = Number(TotalPay) - Number(TotalDeductions);
        }

        $(".form-horizontal").validate();
    </script>
@endsection