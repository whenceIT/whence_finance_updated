@extends('layouts.master')

@section('title')
    Wage Bill Overview
@endsection

@section('content')

@php
    $cycleEnd = $selectedCycle
        ? \Carbon\Carbon::parse($selectedCycle)->addMonthNoOverflow()->day(24)->format('d M Y')
        : null;

    $cycleStartLabel = $selectedCycle
        ? \Carbon\Carbon::parse($selectedCycle)->format('d M Y')
        : null;
@endphp

<style>
    .payroll-summary-card {
        border-radius: 5px;
        min-height: 105px;
        padding: 15px;
        margin-bottom: 15px;
        background: #fff;
        border: 1px solid #e5e5e5;
    }

    .payroll-summary-card .summary-label {
        color: #777;
        font-size: 12px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .payroll-summary-card .summary-value {
        font-size: 22px;
        font-weight: 600;
        color: #333;
    }

    .payroll-summary-card .summary-icon {
        float: right;
        font-size: 25px;
        color: #b7b7b7;
    }

    .payroll-table th,
    .payroll-table td {
        white-space: nowrap;
        vertical-align: middle !important;
    }

    .payroll-table th {
        background: #f7f7f7;
    }

    .payroll-total-row {
        background: #f2f6fa;
        font-weight: bold;
    }

    .payroll-filter-box {
        padding: 15px;
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .net-pay-cell {
        font-weight: bold;
        color: #18794e;
    }
</style>

<section class="content">

    {{-- Page description --}}
    <div class="row">
        <div class="col-md-12">
            <p class="text-muted">
                Review employee salary predictions, statutory deductions,
                allowances and final net pay for each payroll cycle.
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="payroll-filter-box">
        <form method="GET" action="{{ route('wage-bills.index') }}">
            <div class="row">

                @if($isAdminOrRisk)
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="branch_id">Branch</label>

                            <select name="branch_id"
                                    id="branch_id"
                                    class="form-control">

                                <option value="">All Branches</option>

                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ (string) $selectedBranchId === (string) $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach

                            </select>
                        </div>
                    </div>
                @else
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Branch</label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ $offices->first()->name }}"
                                   readonly>
                        </div>
                    </div>
                @endif

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="cycle_start">Payroll Cycle</label>

                        <select name="cycle_start"
                                id="cycle_start"
                                class="form-control">

                            @forelse($cycles as $cycle)
                                @php
                                    $cycleValue = \Carbon\Carbon::parse($cycle)->format('Y-m-d');
                                    $cycleLabel = \Carbon\Carbon::parse($cycle)->format('d M Y')
                                        . ' - '
                                        . \Carbon\Carbon::parse($cycle)->addMonthNoOverflow()->day(24)->format('d M Y');
                                @endphp

                                <option value="{{ $cycleValue }}"
                                    {{ $selectedCycle === $cycleValue ? 'selected' : '' }}>
                                    {{ $cycleLabel }}
                                </option>
                            @empty
                                <option value="">No payroll cycles available</option>
                            @endforelse

                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group" style="margin-top: 25px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-filter"></i> Apply Filters
                        </button>

                        <a href="{{ route('wage-bills.index') }}"
                           class="btn btn-default">
                            Reset
                        </a>
                    </div>
                </div>

            </div>
        </form>

        
                @if(Sentinel::getUser()->roles()->where('roles.id', 1)->exists())
    <form method="GET"
          action="{{ route('wage-bills.export-all-approved') }}"
          class="d-inline">

        <input type="hidden"
               name="cycle_start"
               value="{{ $selectedCycle }}">

        <button type="submit" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            Export Wage Bill
        </button>
    </form>
@endif


@if(Sentinel::getUser()->roles()->where('roles.id', 1)->exists())
    <form method="GET"
          action="{{ route('wage-bills.export-paye') }}"
          class="d-inline">

        <input type="hidden"
               name="cycle_start"
               value="{{ $selectedCycle }}">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-file-excel"></i>
            Download PAYE Report
        </button>
    </form>
@endif


@if(Sentinel::getUser()->roles()->where('roles.id', 1)->exists())
    <form method="GET"
          action="{{ route('wage-bills.export-napsa') }}"
          class="d-inline">

        <input type="hidden"
               name="cycle_start"
               value="{{ $selectedCycle }}">

        <button type="submit" class="btn btn-success">
            <i class="fas fa-file-excel"></i>
            Download NAPSA Report
        </button>
    </form>
@endif



@if(Sentinel::getUser()->roles()->where('roles.id', 1)->exists())
    <form method="GET"
          action="{{ route('wage-bills.export-access-bank') }}"
          class="d-inline">

        <input type="hidden"
               name="cycle_start"
               value="{{ $selectedCycle }}">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-university"></i>
            Download Access Bank Report
        </button>
    </form>
@endif


    </div>

    @if($selectedCycle)

        {{-- Cycle information --}}
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="fa fa-calendar"></i>
                    <strong>Payroll Cycle:</strong>
                    {{ $cycleStartLabel }} - {{ $cycleEnd }}
                </div>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="row">

            <div class="col-md-3 col-sm-6">
                <div class="payroll-summary-card">
                    <i class="fa fa-users summary-icon"></i>
                    <div class="summary-label">Employees</div>
                    <div class="summary-value">
                        {{ number_format($totals['employees']) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="payroll-summary-card">
                    <i class="fa fa-money summary-icon"></i>
                    <div class="summary-label">Predicted Salaries</div>
                    <div class="summary-value">
                        K{{ number_format($totals['predicted_salary'], 2) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="payroll-summary-card">
                    <i class="fa fa-plus-circle summary-icon"></i>
                    <div class="summary-label">Total Allowances</div>
                    <div class="summary-value">
                        K{{ number_format($totals['allowances'], 2) }}
                    </div>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="payroll-summary-card">
                    <i class="fa fa-bank summary-icon"></i>
                    <div class="summary-label">Total Advance Deductions</div>
                    <div class="summary-value">
                        K{{ number_format($totals['advance_deductions'], 2) }}
                    </div>
                </div>
            </div>

        </div>

        {{-- Net pay summary --}}
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-body">
                        <div class="row">

                            <div class="col-md-8">
                                <h4 style="margin-top: 5px;">
                                    <i class="fa fa-check-circle text-green"></i>
                                    Total Net Pay
                                </h4>

                                <p class="text-muted">
                                    Total amount payable to employees after
                                    advance deductions and allowances.
                                </p>
                            </div>

                            <div class="col-md-4 text-right">
                                <h2 class="text-green"
                                    style="font-weight: bold; margin-top: 10px;">
                                    K{{ number_format($totals['net_pay'], 2) }}
                                </h2>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>



{{-- ========================================================= --}}
{{-- PAYROLL PERIOD COMPARISON                                 --}}
{{-- ========================================================= --}}

@if($previousCycle)

    @php
        $previousCycleLabel = \Carbon\Carbon::parse($previousCycle)->format('d M Y');

        $grossChange = $comparisonMetrics['gross_pay']['change'];
        $grossPercentage = $comparisonMetrics['gross_pay']['percentage'];

        $changeClass = $grossChange > 0
            ? 'text-danger'
            : ($grossChange < 0 ? 'text-success' : 'text-muted');

        $changeIcon = $grossChange > 0
            ? 'fa-arrow-up'
            : ($grossChange < 0 ? 'fa-arrow-down' : 'fa-minus');
    @endphp

    <div class="box box-info">

        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-line-chart"></i>
                Payroll Period Comparison
            </h3>
        </div>

        <div class="box-body">

            <p class="text-muted">
                Comparing the selected payroll cycle against the
                previous available cycle beginning
                <strong>{{ $previousCycleLabel }}</strong>.
            </p>

            {{-- Overall movement --}}
            <div class="row">

                <div class="col-md-4">
                    <div class="payroll-summary-card">
                        <div class="summary-label">Gross Payroll Movement</div>

                        <div class="summary-value {{ $changeClass }}">
                            <i class="fa {{ $changeIcon }}"></i>
                            K{{ number_format(abs($grossChange), 2) }}
                        </div>

                        <small class="{{ $changeClass }}">
                            @if($grossPercentage !== null)
                                {{ number_format(abs($grossPercentage), 2) }}%
                            @else
                                No previous baseline
                            @endif

                            {{ $payrollDirection }}
                        </small>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="payroll-summary-card">
                        <div class="summary-label">Previous Gross Payroll</div>

                        <div class="summary-value">
                            K{{ number_format($previousTotals['gross_pay'], 2) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="payroll-summary-card">
                        <div class="summary-label">Current Gross Payroll</div>

                        <div class="summary-value">
                            K{{ number_format($totals['gross_pay'], 2) }}
                        </div>
                    </div>
                </div>

            </div>

            {{-- Comparison table --}}
            <h4>Financial Breakdown</h4>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">

                    <thead>
                        <tr>
                            <th>Payroll Component</th>
                            <th>Previous Cycle</th>
                            <th>Current Cycle</th>
                            <th>Change</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($comparisonMetrics as $metric)

                            @php
                                $change = $metric['change'];

                                $class = $change > 0
                                    ? 'text-danger'
                                    : ($change < 0 ? 'text-success' : 'text-muted');
                            @endphp

                            <tr>
                                <td>
                                    <strong>{{ $metric['label'] }}</strong>
                                </td>

                                <td>
                                    @if($metric['label'] === 'Number of Employees')
                                        {{ number_format($metric['previous']) }}
                                    @else
                                        K{{ number_format($metric['previous'], 2) }}
                                    @endif
                                </td>

                                <td>
                                    @if($metric['label'] === 'Number of Employees')
                                        {{ number_format($metric['current']) }}
                                    @else
                                        K{{ number_format($metric['current'], 2) }}
                                    @endif
                                </td>

                                <td class="{{ $class }}">
                                    <strong>
                                        {{ $change > 0 ? '+' : '' }}

                                        @if($metric['label'] === 'Number of Employees')
                                            {{ number_format($change) }}
                                        @else
                                            K{{ number_format($change, 2) }}
                                        @endif
                                    </strong>
                                </td>

                                <td class="{{ $class }}">
                                    @if($metric['percentage'] !== null)
                                        {{ number_format($metric['percentage'], 2) }}%
                                    @elseif($change > 0)
                                        New
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>

                        @endforeach

                    </tbody>

                </table>
            </div>

            <hr>

            {{-- Employee-level explanation --}}
            <h4>
                <i class="fa fa-users"></i>
                Employees Responsible for Payroll Changes
            </h4>

            <p class="text-muted">
                Employees are listed according to the size of their
                gross payroll movement between the two cycles.
            </p>

            @if($employeeChanges->isNotEmpty())

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">

                        <thead>
                            <tr>
                                <th>Employee</th>
                                <th>Change Type</th>
                                <th>Salary Change</th>
                                <th>Allowance Change</th>
                                <th>Gross Pay Change</th>
                                <th>Net Pay Change</th>
                                <th>Possible Causes</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($employeeChanges as $change)

                                @php
                                    $grossClass = $change['gross_change'] > 0
                                        ? 'text-danger'
                                        : ($change['gross_change'] < 0 ? 'text-success' : 'text-muted');
                                @endphp

                                <tr>
                                    <td>
                                        <strong>{{ $change['employee_name'] }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            ID: {{ $change['user_id'] }}
                                        </small>
                                    </td>

                                    <td>
                                        {{ $change['change_type'] }}
                                    </td>

                                    <td>
                                        K{{ number_format($change['salary_change'], 2) }}
                                    </td>

                                    <td>
                                        K{{ number_format($change['allowance_change'], 2) }}
                                    </td>

                                    <td class="{{ $grossClass }}">
                                        <strong>
                                            {{ $change['gross_change'] > 0 ? '+' : '' }}
                                            K{{ number_format($change['gross_change'], 2) }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $change['net_change'] > 0 ? '+' : '' }}
                                        K{{ number_format($change['net_change'], 2) }}
                                    </td>

                                    <td>
                                        @forelse($change['reasons'] as $reason)
                                            <span class="label label-default"
                                                  style="display:inline-block; margin:2px;">
                                                {{ $reason }}
                                            </span>
                                        @empty
                                            <span class="text-muted">No significant change</span>
                                        @endforelse
                                    </td>
                                </tr>

                            @endforeach

                        </tbody>

                    </table>
                </div>

            @else

                <div class="alert alert-success">
                    No individual employee payroll changes were detected
                    between these two cycles.
                </div>

            @endif

        </div>
    </div>

@else

    <div class="alert alert-warning">
        <i class="fa fa-info-circle"></i>
        There is no earlier payroll cycle available for comparison.
    </div>

@endif



        @if(
    $isBranchManager &&
    $wageBills->isNotEmpty() &&
    in_array($wageBills->first()->status, ['draft', 'returned'])
)
    <div class="box box-primary">
        <div class="box-body">
            <h4>Submit Wage Bill</h4>

            <p>
                Review all employee payroll figures and the total net pay
                before submitting this wage bill to Administration and Risk.
            </p>

            <form method="POST"
                  action="{{ route('wage-bills.submit', [
                      $wageBills->first()->branch_id,
                      $wageBills->first()->cycle_start->format('Y-m-d')
                  ]) }}">

                @csrf

                <div class="checkbox">
                    <label>
                        <input type="checkbox"
                               name="confirmation"
                               value="1"
                               required>

                        I confirm that I have reviewed this wage bill and
                        the payroll figures are correct.
                    </label>
                </div>

                <button type="submit" class="btn btn-success">
                    <i class="fa fa-paper-plane"></i>
                    Submit Wage Bill
                </button>
            </form>
        </div>
    </div>
@endif


@if(
    $isBranchManager &&
    $wageBills->isNotEmpty() &&
    $wageBills->first()->status === 'approved'
)
    <div class="box box-success">
        <div class="box-body">
            <div class="row">
                <div class="col-md-8">
                    <h4>
                        <i class="fa fa-check-circle text-success"></i>
                        Wage Bill Fully Approved
                    </h4>

                    <p>
                        Both Administration and Risk have approved this wage bill.
                        You can now download the final payroll report.
                    </p>
                </div>

                <div class="col-md-4 text-right">
                    <a href="{{ route('wage-bills.export-approved', [
                        $wageBills->first()->branch_id,
                        $wageBills->first()->cycle_start->format('Y-m-d')
                    ]) }}"
                       class="btn btn-success">
                        <i class="fa fa-file-excel-o"></i>
                        Download Excel Report
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

        {{-- Employee wage bill table --}}
        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-list"></i>
                    Employee Wage Bill
                </h3>

                <div class="box-tools pull-right">
                    <span class="label label-primary">
                        {{ number_format($totals['employees']) }} Employees
                    </span>
                </div>
            </div>

            <div class="box-body">

                @if($wageBills->count())

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover payroll-table">

                            <thead>
                                <tr>
                                    <th>#</th>
                                    @if($isAdminOrRisk && !$selectedBranchId)
                                        <th>Branch</th>
                                    @endif
                                    <th>Employee</th>
                                    <th>Predicted Salary</th>
                                    <th>Gross Pay</th>
                                    <th>PAYE</th>
                                    <th>NAPSA</th>
                                    <th>NHIMA</th>
                                    <th>Allowances</th>
                                    <th>Advance Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($wageBills as $index => $wageBill)

                                    <tr>
                                        <td>
                                            {{ $wageBills->firstItem() + $index }}
                                        </td>

                                        @if($isAdminOrRisk && !$selectedBranchId)
                                            <td>
                                                {{ optional($wageBill->branch)->name ?? 'Unknown Branch' }}
                                            </td>
                                        @endif

                                        <td>
                                            <strong>
                                                {{ optional($wageBill->employee)->full_name ?? 'Unknown Employee' }}
                                            </strong>
                                            <br>
                                            <small class="text-muted">
                                                ID: {{ $wageBill->user_id }}
                                            </small>
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->predicted_salary, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->gross_pay, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->paye, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->napsa, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->nhima, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->allowances, 2) }}
                                        </td>

                                        <td>
                                            K{{ number_format($wageBill->advance_deductions, 2) }}
                                        </td>

                                        <td class="net-pay-cell">
                                            K{{ number_format($wageBill->system_net_pay, 2) }}
                                        </td>

                                        <td>
                                            @if($wageBill->status === 'draft')
                                                <span class="label label-default">Draft</span>
                                            @elseif($wageBill->status === 'returned')
                                                <span class="label label-warning">Returned</span>
                                            @elseif($wageBill->status === 'submitted')
                                                <span class="label label-info">Submitted</span>
                                            @elseif($wageBill->status === 'approved')
                                                <span class="label label-success">Approved</span>
                                            @else
                                                <span class="label label-default">
                                                    {{ ucfirst($wageBill->status) }}
                                                </span>
                                            @endif
                                        </td>

                                        <td>
    @if($isBranchManager && in_array($wageBill->status, ['draft', 'returned']))
        <a href="{{ route('wage-bills.edit', $wageBill->id) }}"
           class="btn btn-xs btn-primary">
            <i class="fa fa-edit"></i> Adjust
        </a>
    @else
        <span class="text-muted">View only</span>
    @endif
</td>
                                    </tr>

                                @endforeach
                            </tbody>

                        </table>
                    </div>

                    <div class="text-right">
                        {{ $wageBills->links() }}
                    </div>

                @else

                    <div class="text-center" style="padding: 40px;">
                        <i class="fa fa-folder-open-o fa-3x text-muted"></i>

                        <h4>No Wage Bill Records Found</h4>

                        <p class="text-muted">
                            There are no payroll records for the selected
                            branch and cycle.
                        </p>
                    </div>

                @endif

            </div>
        </div>

    @else

        <div class="box box-default">
            <div class="box-body text-center" style="padding: 40px;">
                <i class="fa fa-calendar-times-o fa-3x text-muted"></i>
                <h4>No Payroll Cycles Available</h4>
                <p class="text-muted">
                    Wage bill records will appear here after the salary
                    predictor synchronization has created them.
                </p>
            </div>
        </div>

    @endif

</section>

@endsection