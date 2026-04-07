@extends('layouts.master')
@section('title')
My Payslip
@endsection
@section('content')

<div style="padding-top: 10px;">

<div class="box box-primary">
   <div class="box-header with-border">
      <div class="box-tools pull-right"></div>
    </div>

    <div class="box-body">

        <div class="table-responsive">
            <table class="table table-bordered table-condensed table-striped table-hover">

                <thead>
                    <tr>
                        <th>Staff</th>
                        @foreach($payroll_fields as $payroll_field)
                            <th>{{$payroll_field->name}}</th>
                        @endforeach
                        <th>Net Pay</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                @php 
                    use App\Models\PayrollMeta;
                    use App\Models\PayrollTemplateMeta;

                    $currentMonth = null;
                @endphp

                @foreach($payroll_list->sortByDesc('payroll_date') as $payroll)

                    @php
                        $month = date("F Y", strtotime($payroll->payroll_date));
                    @endphp

                    {{-- ✅ Month Header --}}
                    @if($currentMonth != $month)
                        <tr style="background:#f4f6f9;">
                            <td colspan="100%">
                                <strong>{{ $month }}</strong>
                            </td>
                        </tr>
                        @php $currentMonth = $month; @endphp
                    @endif

                    @php
                        $payroll_info = PayrollMeta::where('payroll_id',$payroll->id)->get();

                        $additions = 0;
                        $deductions = 0;

                        foreach($payroll_info as $info){
                            $payroll_field = PayrollTemplateMeta::where('id',$info->payroll_template_meta_id)->first();

                            if($payroll_field && $payroll_field->type == 'addition'){
                                $additions += $info->value;
                            }elseif($payroll_field && $payroll_field->type == 'deduction'){
                                $deductions += $info->value;
                            }
                        }

                        $net_pay = $additions - $deductions;
                    @endphp

                    <tr>
                        <td>{{$payroll->employee_name}}</td>

                        @foreach($payroll_info as $info)
                            <td>{{number_format($info->value ?? 0,2)}}</td>
                        @endforeach

                        <td>{{number_format($net_pay,2)}}</td>
                        <td>{{date("M, Y", strtotime($payroll->payroll_date))}}</td>
                        <td>{{$payroll->status}}</td>

                        <td>
                            <a class="btn bg-blue"
                               href="{{url('payroll/'.$payroll->id.'/payslip')}}">
                                Generate payslip
                            </a>
                        </td>
                    </tr>

                @endforeach

                </tbody>

            </table>
        </div>

    </div>
</div>

</div>

@endsection

@section('footer-scripts')
@endsection
