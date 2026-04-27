@extends('layouts.master')
@section('title')
Company PAYE
@endsection

@section('content')

<div class="box box-primary">

    <div class="box-body">

        {{-- ✅ Header --}}
        @php
            $displayMonth = \Carbon\Carbon::parse($month)->format('F Y');
            $month = \Carbon\Carbon::parse($month)->format('F');
            $year = \Carbon\Carbon::parse($month)->format('Y');
        @endphp

        <div class="text-center mb-4" style="border-bottom: 2px solid #eee; padding-bottom: 10px;">
            <h2 style="margin:0; font-weight:700; letter-spacing:0.5px;">
                Whence Financial Services
            </h2>

            <p style="margin:5px 0 0; font-size:16px; color:#555;">
                {{ $displayMonth }} — Monthly PAYE Register
            </p>
        </div>

        {{-- ✅ Filters --}}
        <form method="get"
              class="mb-4 p-3 d-flex align-items-center flex-wrap"
              style="background:#f9fafc; border:1px solid #eee; border-radius:8px;"
              id="payrollFilterForm">

            <div class="form-group mr-3">
                <label class="mr-2">Branch:</label>
                <select name="office_id" class="form-control">
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
                <input type="month"
                       name="month"
                       value="{{ request('month') ?? $month }}"
                       class="form-control">
            </div>

            <button type="submit" class="btn btn-primary mr-2">
                Apply Filter
            </button>

            <button type="submit"
                    formaction="{{ url('payroll/export_nhima') }}"
                    formmethod="GET"
                    class="btn btn-success">
                Download Excel
            </button>

            <a href="{{ url('payroll/company_payroll') }}" class="btn btn-default ml-2">
                Clear Filters
            </a>

        </form>

        {{-- ✅ Summary Cards --}}
        <div class="row mb-4">

            @php
                $cards = [
                    ['label' => 'Total Employees', 'value' => $totals['employees'], 'color' => 'bg-aqua'],
                    ['label' => 'Total Basic Pay', 'value' => number_format($totals['basic_pay'],2), 'color' => 'bg-green'],
                       ['label' => 'Total PAYE', 'value' => number_format($totals['paye'],2), 'color' => 'bg-purple'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="col-md-2">
                    <div class="small-box {{ $card['color'] }}" style="border-radius:10px;">
                        <div class="inner">
                            <h3 style="font-size:22px;">{{ $card['value'] }}</h3>
                            <p style="font-size:13px;">{{ $card['label'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach

        </div>

        {{-- ✅ Payroll Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">

                <thead>
                    <tr style="background:#1f2d3d; color:white; font-size:13px;">
                        <th>tpin</th>
                        <th>Employee Name</th>
                        <th>Employment Nature</th>
                        <th>Gross Emoluments</th>
                        <th>Chargeable Emoluments</th>
                        <th>Total Tax Credited</th>
                        <th>Tax Deducted</th>
                        <th>Tax Adjusted</th>
                       
                    </tr>
                </thead>

                <tbody>

                    @php
                        use App\Models\PayrollMeta;
                        use App\Models\PayrollTemplateMeta;
                        $currentMonth = null;
                    @endphp

                    @foreach($payroll_list->sortByDesc('payroll_date') as $payroll)

                        @php $monthLabel = date("F Y", strtotime($payroll->payroll_date)); @endphp

                        {{-- Month Header --}}
                        @if($currentMonth != $monthLabel)
                            <tr style="background:#f4f6f9;">
                                <td colspan="100%">
                                    <strong>{{ $monthLabel }}</strong>
                                </td>
                            </tr>
                            @php $currentMonth = $monthLabel; @endphp
                        @endif

                        @php

    $paye_info = PayrollMeta::where('payroll_id', $payroll->id)
    ->whereIn('payroll_template_meta_id', [5])
    ->first();

     $gross_info = PayrollMeta::where('payroll_id', $payroll->id)
    ->whereIn('payroll_template_meta_id', [1])
    ->first();

                            $additions = 0;
                            $deductions = 0;

                       
                        @endphp

                        <tr>
                            <td>{{ $payroll->user->nhima }}</td>
                             <td>{{ $payroll->employee_name }}</td>
                             <td>{{ $payroll->user->employment_type }}</td>
                            <td>{{ $paye_info->value }}</td>
                            <td>{{ $paye_info->value }}</td>
                            <td>0</td>
                            <td>{{$gross_info->value}}</td>
                             <td>0</td>
                   
                      
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