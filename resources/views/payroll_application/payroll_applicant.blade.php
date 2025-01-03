@extends('layouts.master')
@section('title')
{{ trans_choice('general.detail',2) }}
@endsection
@section('content')
<div class="box box-primary">
    <div class="box-body table-responsive">

    @if($applicant->status == 'pending')
    
    <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('loans.approve'))
                                        <a href="{{ url('loan/payroll_loan/'.$applicant->id.'/approve_applicant') }}" 
                                           class="btn btn-primary"><i
                                                    class="fa fa-check"></i>&nbsp;{{trans_choice('general.approve',1)}}
                                        </a>
                                        <a href="{{url('loan/payroll_loan/'.$applicant->id.'/decline_applicant')}}" 
                                           class="btn btn-primary"><i
                                                    class="fa fa-times"></i>&nbsp;{{trans_choice('general.decline',1)}}
                                        </a>
                                    @endif
                                </div>
                            </div>
    </div>
    @elseif($applicant->status == 'approved')

    <p style="color: green; text-decoration: underline; font-size:20px;">Approved</p>

    @else( == 'declined')

    <p style="color: red; text-decoration: underline; font-size:20px;">Declined</p>

    @endif
                        
        <div class="row m-t-20">



        <div class="col-sm-6 col-md-6">
            <h3>Personal Information</h3>
                                <table class="table table-striped table-bordered">
                                    <tbody>
                               
                                    <tr>
                                        <th class="table-bold-loan">Client Name</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->client_name}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">NRC</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->nrc}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Date of Birth</th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->dob}}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Gender</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->gender}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Email</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->email}}
                                        </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Phone</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->phone}}
                                        </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Home Address</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->home_address}}
                                        </span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>


                            
                            <div class="col-sm-6 col-md-6">
                            <h3>Employment Information</h3>
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">Employer Name</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->employer_name}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Man Number</th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->employee_id}}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Job Position</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->job_title}}</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Length of Service</th>
                                        <td>
                                            <span class="padded-td">{{$applicant->length_of_service}}</span>
                                        </td>
                                    </tr>


                                    <tr>
                                        <th class="table-bold-loan">Work address</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->work_address}}</span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Work Phone Number</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->work_phone}}</span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>



                                
                            <div class="col-sm-6 col-md-6">
                            <h3>Loan Details</h3>
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">Loan Amount</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->amount}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Loan Term</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->loan_term}}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                    <th class="table-bold-loan">Loan Purpose</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->purpose_of_loan}}</span>
                                        </td>
                                     
				    </tr>

 <tr>
                                    <th class="table-bold-loan">Monthly Repayment</th>
                                        <td>
                                            <span class="padded-td"> {{$applicant->deduction_amount}}</span>
                                        </td>
				    </tr>

    <tr>
                                    <th class="table-bold-loan">Service charge (5%)</th>
                                        <td>
                                            <span class="padded-td">{{($applicant->amount * 0.05)}}</span>
                                        </td>
                                    </tr>


                                    <tr>
                                    <th class="table-bold-loan">Amount to be given out</th>
                                        <td>
                                            <span class="padded-td"> {{($applicant->amount - ($applicant->amount * 0.05))}}</span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>


                            <div class="col-sm-6 col-md-6">
                            <h3>Bank Information</h3>
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">Bank Name</th>
                                        <td>
                                        <span class="padded-td">
                                        {{$applicant->bank_name}}
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Account Number</th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->bank_account}}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">Bank Short Code</th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->bank_short_code}}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Branch Name</th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->branch_name}}
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <th class="table-bold-loan">Branch Code </th>
                                        <td>
                                            <span class="padded-td">
                                            {{$applicant->branch_code}}
                                            </span>
                                        </td>
                                    </tr>



                                    </tbody>
                                </table>

                            </div>



	</div>

 <div class="row">

    <div style="padding: 20px;" >

    <a href="{{$applicant->nrc_file}}" download>

   <p style="font-size: 20px; text-decoration: underline;">
   <i class="fa fa-download"></i>
   NRC
   </p>

</a>
    </div>



    <div style="padding: 20px; " >

    <a href="{{$applicant->payslip_file}}" download>
        <p style="font-size: 20px; text-decoration: underline;">

        <i class="fa fa-download"></i>
        Payslip

        </p>
</a>
    </div>



  


                            </div>

    </div>
</div>
@endsection
