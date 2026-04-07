@extends('layouts.master')
@section('title')
Company Payroll
@endsection

@section('content')

<div class="box box-primary">

    <div class="box-header with-border d-flex justify-content-between align-items-center">
        <h3 class="box-title">Company Payroll</h3>
    </div>

    <div class="box-body">

        {{-- ✅ Filters: Branch + Month + Excel Button --}}
        <form method="get" class="form-inline mb-3 d-flex align-items-center flex-wrap" id="payrollFilterForm">
            <div class="form-group mr-3">
                <label class="mr-2">Branch:</label>
                <select name="office_id" class="form-control" id="branchFilter">
                    <option value="">All Branches</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" 
                            {{ request('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group mr-3">
                <label class="mr-2">Month:</label>
                <input type="month" name="month" value="{{ request('month') ?? date('Y-m') }}" class="form-control" id="monthFilter">
            </div>

            <button type="submit" class="btn btn-primary mr-2">
                Apply Filter
            </button>

            <button type="submit" formaction="{{ url('payroll/export') }}" class="btn btn-success">
                Download Excel
            </button>
        </form>

        {{-- Optional: auto-submit when branch or month changes --}}
        <script>
            document.getElementById('branchFilter').addEventListener('change', function(){
                document.getElementById('payrollFilterForm').submit();
            });
            document.getElementById('monthFilter').addEventListener('change', function(){
                document.getElementById('payrollFilterForm').submit();
            });
        </script>

        {{-- ✅ Payroll Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr style="background:#007bff; color:white;">
                        <th>Staff</th>
                        @foreach($payroll_fields as $payroll_field)
                            <th>{{$payroll_field->name}}</th>
                        @endforeach
                        <th>Net Pay</th>
                        <th>Date</th>
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
                        @php $month = date("F Y", strtotime($payroll->payroll_date)); @endphp

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
                                if($payroll_field && $payroll_field->type == 'addition') $additions += $info->value;
                                if($payroll_field && $payroll_field->type == 'deduction') $deductions += $info->value;
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

@endsection
@section('footer-scripts')
@endsection