<style>
    .borderOk {

        border-right: solid 1px #000000;
        border-left: solid 1px #000000;
        border-top: solid 1px #000000;
        border-bottom: solid 1px #000000;
    }

    table #hours_and_earnings td, table #tax_deductions td, table #pre_tax_deductions td, table #after_tax_deductions td, table #payslip_employee_header td, table #payslip_employer_header td, table #pay_period_and_salary td, table #summary td, table #net_pay_distribution td, table #messages td {
        padding: 2px;
    }

    .bg-navy {
        background-color: #001f3f;
        color: #fff;
    }

    .bg-gray {
        color: #000;
        background-color: #d2d6de;
    }

    .text-bold, .text-bold.table td, .text-bold.table th {
        font-weight: 700;
    }

    .margin {
        margin: 10px;
    }

    .text-center {
        text-align: center;
    }
</style>
<h3 class="text-center"><b>{{\App\Models\Setting::where('setting_key','company_name')->first()->setting_value}}</b></h3>

<h3 class="text-center"><b>{{trans_choice('general.payslip',1)}}</b></h3>
<table width="100%">
    <tbody>
    <tr style="margin: 20px">
        <td style="padding-bottom:10px;">
            <table width="100%" class="borderOk">
                <tbody>
                <tr>
                    <td style="vertical-align: top;" width="50%">

                        <table width="100%" id="payslip_employee_header">
                            <tbody>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin"> {{trans_choice('general.employee',1)}} {{trans_choice('general.name',1)}}
                                    </div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                    {{$user->first_name}} {{$user->last_name}}
                                    </div>
                                </td>
                            </tr>
                            @foreach($top_left as $key)
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            @if(!empty($key->payroll_template_meta))
                                                {{$key->payroll_template_meta->name}}
                                            @endif
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! $key->value !!}
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
                                    <div class="margin"><b>{{trans_choice('general.payroll',1)}} {{trans_choice('general.date',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                    {{date("d - M, Y",strtotime($payslip->created_at))}}
                                       
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td width="50%" class="cell_format">
                                    <div class="margin">Company Name</div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                        {!! \App\Models\Setting::where('setting_key', 'company_name')->first()->setting_value !!}
                                    </div>
                                </td>
                            </tr>
                            @foreach($top_right as $key)
                                <tr>
                                    <td width="50%" class="cell_format">
                                        <div class="margin">
                                            @if(!empty($key->payroll_template_meta))
                                                {{$key->payroll_template_meta->name}}
                                            @endif
                                        </div>
                                    </td>
                                    <td width="50%" class="cell_format">
                                        <div class="margin text-bold">
                                            {!! $key->value !!}
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
    <tr style="height: 20px">
        <td></td>
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
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.description',1)}}</b></td>
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.amount',1)}}</b></td>
                            </tr>
                        

                            <tr>
                                                    <td width="50%" class="cell_format">Basic Pay</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                           {{$payslip->basic_pay}}
                                                        </div>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td width="50%" class="cell_format">Allowances</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                           {{$payslip->allowances}}
                                                        </div>
                                                    </td>
                                                </tr>

                            </tbody>
                        </table>
                        <!--Hours and Earnings-->
                    </td>
                    <?php 
                    $basic_pay = 0;
                    $allowances = 0;
                    $salary_deductions = 0;
                    $charges = 0;
                    $NAPSA = 0;
                    $NHIMA = 0;
                    $gross_pay = 0;
                    $net_pay = 0;

                    $basic_pay = $payslip->basic_pay;
                    $allowances = $payslip->allowances;
                    $salary_deductions = $payslip->salary_deductions;
                    $charges = $payslip->charges;
                    $NAPSA = $basic_pay * 0.05;
                    $NHIMA = $basic_pay * 0.01;
                    $gross_pay = $basic_pay + $allowances - $salary_deductions - $charges;
                    if($basic_pay <= 5100){
                       $PAYE = 0;
                    }else if($basic_pay <= 7100){
                       $PAYE = ($basic_pay - 5100)*0.20;
                    }else if($basic_pay <= 9200){
                        $PAYE = (($basic_pay - 7100)*0.30) + ((7100 - 5100)*0.20);
                    }else{
                        $PAYE = ($basic_pay - 9200)*0.37 + (9200 - 7100)*0.30 + (7100 - 5100)*0.20;
                    }
                    $net_pay = $gross_pay - $NAPSA - $NHIMA - $PAYE;
                    $total_pay = $basic_pay + $allowances;
                    $total_deductions = $salary_deductions + $charges + $PAYE + $NAPSA + $NHIMA;

                    $user =  \App\Models\User::where('id',$payslip->user_id)->first();
                    ?>
                    <td width="50%" valign="top">
                        <table width="100%" id="pre_tax_deductions">
                            <tbody>
                            <tr>
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.description',1)}}</b></td>
                                <td width="50%" class="bg-navy"><b>{{trans_choice('general.amount',1)}}</b></td>
                            </tr>

                            <tr>
                                                    <td width="50%" class="cell_format">Advance deductions</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                          {{$payslip->salary_deductions}}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">Penalties/Charges</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                           {{$payslip->charges}}
                                                        </div>
                                                    </td>
                                                </tr>


                                                <tr>
                                                    <td width="50%" class="cell_format">NAPSA</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                         {{$NAPSA}}
                                                        </div>
                                                    </td>
                                                </tr>
                                                 

                                                <tr>
                                                    <td width="50%" class="cell_format">PAYE</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                           {{$PAYE}}
                                                        </div>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td width="50%" class="cell_format">NHIMA</td>
                                                    <td width="50%" class="cell_format">
                                                        <div class="margin text-bold">
                                                          {{$NHIMA}}
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
                                    <div class="margin"><b>{{trans_choice('general.total',1)}} {{trans_choice('general.pay',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                       {{$total_pay}}
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
                                    <div class="margin"><b>{{trans_choice('general.total',1)}} {{trans_choice('general.deduction',2)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                       {{$total_deductions}}
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
                                    <div class="margin"><b>{{trans_choice('general.net',1)}} {{trans_choice('general.pay',1)}}</b></div>
                                </td>
                                <td width="50%" class="cell_format">
                                    <div class="margin text-bold">
                                     {{$net_pay}}
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
    <tr style="height: 20px">
        <td></td>
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
                           
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                       
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                         
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                          
                        </div>
                    </td>
                    <td width="20%" class="cell_format">
                        <div class="margin text-bold">
                           {{$net_pay}}
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <table width="100%" class="borderOk" style="margin-top:10px;padding: 10px" id="messages">
                    <tbody>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin"><b>{{trans_choice('general.comment',2)}}</b></div>
                        </td>
                    </tr>
                    <tr>
                        <td width="100%" class="cell_format">
                            <div class="margin text-bold">
                             "Whence Financial Services strives for accuracy in all pay-related matters. If you notice any discrepancies, please let your immediate supervisor know right away."
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            <!--Net Pay Distribution-->
        </td>
    </tr>
  
