
<h3>Payroll Report {{date("M Y",strtotime($date))}}</h3>
<table id="view-repayments" class="table table-bordered table-condensed table-striped table-hover no-footer">
            <thead style="background-color: blue;">
                    <tr style="background-color: blue;" role="row">
                        <th style="font-weight: bold; width:250px; background-color:#1f4e78; color: white;">
                           Staff
                        </th>
                        <th style="font-weight: bold; width:100px ; background-color:#1f4e78; color: white;">
                            Gross Pay
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                           NAPSA
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                            NHIMA
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                            PAYE
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                            Allowances
                        </th>
                        <th style="font-weight: bold; width:200px; background-color:#1f4e78; color: white;">
                            Advance Deductions
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                           Charges
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                            Net Pay
                        </th>
                        <th style="font-weight: bold; width:100px; background-color:#1f4e78; color: white;">
                            Date
                        </th>
                    </tr>
                    </thead>
                    <?php
                    
                    $payroll_new = [];
                    ?>
                    <tbody>
                    @foreach($payroll_list as $payroll)
                    <?php 
                    $basic_pay = 0;
                    $allowances = 0;
                    $salary_deductions = 0;
                    $charges = 0;
                    $NAPSA = 0;
                    $NHIMA = 0;
                    $gross_pay = 0;
                    $net_pay = 0;

                    $basic_pay = $payroll->basic_pay;
                    $allowances = $payroll->allowances;
                    $salary_deductions = $payroll->salary_deductions;
                    $charges = $payroll->charges;
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

                    $user =  \App\Models\User::where('id',$payroll->user_id)->first();
                    ?>
                    <tr>
                        <td>{{$user->first_name}} {{$user->last_name}}</td>
                        <td>{{number_format($payroll->basic_pay,2)}}</td>
                        <td>{{number_format($NAPSA,2)}}</td>
                        <td>{{number_format($NHIMA,2)}}</td>
                        <td>{{number_format($PAYE,2)}}</td>
                        <td>{{number_format($payroll->allowances,2)}}</td>
                        <td>{{number_format($payroll->salary_deductions)}}</td>
                        <td>{{number_format($payroll->charges,2)}}</td>
                        <td>{{number_format($net_pay,2)}}</td>
                        <td>{{date("M, Y",strtotime($payroll->created_at))}}</td>
                    </tr>

                    @endforeach
                    </tbody>
                 
            </table>