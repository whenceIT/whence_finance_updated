@extends('layouts.master')

@section('content')
<section class="content-header">
    <h1>Review Wage Bill</h1>
</section>

<section class="content">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Submission information --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Submission Information</h3>
        </div>

        <div class="box-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Branch</strong>
                    <p>{{ $submission->branch->name ?? '-' }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Payroll Cycle</strong>
                    <p>{{ \Carbon\Carbon::parse($submission->cycle_start)->format('d M Y') }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Submission Number</strong>
                    <p>#{{ $submission->submission_number }}</p>
                </div>

                <div class="col-md-3">
                    <strong>Submitted By</strong>
                    <p>
                        {{ $submission->submitter->first_name ?? '' }}
                        {{ $submission->submitter->last_name ?? '' }}
                    </p>
                </div>
            </div>

            <strong>Overall Status:</strong>

            <span class="label label-{{
                $submission->status == 'approved'
                    ? 'success'
                    : ($submission->status == 'returned' ? 'danger' : 'primary')
            }}">
                {{ ucfirst($submission->status) }}
            </span>
        </div>
    </div>

    {{-- Payroll totals --}}
    <div class="row">

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3>{{ number_format($totals['employees']) }}</h3>
                    <p>Employees</p>
                </div>
                <div class="icon"><i class="fa fa-users"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>K{{ number_format($totals['net_pay'], 2) }}</h3>
                    <p>Total Net Pay</p>
                </div>
                <div class="icon"><i class="fa fa-money"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>K{{ number_format($totals['allowances'], 2) }}</h3>
                    <p>Total Allowances</p>
                </div>
                <div class="icon"><i class="fa fa-plus"></i></div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>K{{ number_format($totals['advance_deductions'], 2) }}</h3>
                    <p>Advance Deductions</p>
                </div>
                <div class="icon"><i class="fa fa-minus"></i></div>
            </div>
        </div>

    </div>

    {{-- Manager Adjustments Review --}}
<div class="box box-warning">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-edit"></i>
            Manager Adjustments Review
        </h3>
    </div>

    <div class="box-body">

        <div class="row">
            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-yellow">
                        <i class="fa fa-users"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Employees with Adjustments</span>
                        <span class="info-box-number">
                            {{ $adjustmentSummary['employees_adjusted'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-blue">
                        <i class="fa fa-history"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Recorded Changes</span>
                        <span class="info-box-number">
                            {{ $adjustmentSummary['total_adjustment_records'] }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="info-box">
                    <span class="info-box-icon bg-green">
                        <i class="fa fa-money"></i>
                    </span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Allowances</span>
                        <span class="info-box-number">
                            K{{ number_format($adjustmentSummary['total_allowances'], 2) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h4><strong>Current Values Compared with System Values</strong></h4>

        <p class="text-muted">
            This comparison highlights payroll figures that differ from the original
            system-generated values.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Field</th>
                        <th>System Value</th>
                        <th>Current Value</th>
                        <th>Difference</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $comparisonRows = $adjustments->where('type', 'comparison');
                    @endphp

                    @forelse($comparisonRows as $adjustment)
                        <tr>
                            <td>
                                {{ $adjustment['employee']->first_name ?? '' }}
                                {{ $adjustment['employee']->last_name ?? '' }}
                            </td>

                            <td>
                                {{ ucwords(str_replace('_', ' ', $adjustment['field'])) }}
                            </td>

                            <td>
                                K{{ number_format($adjustment['system_value'], 2) }}
                            </td>

                            <td>
                                <strong>
                                    K{{ number_format($adjustment['current_value'], 2) }}
                                </strong>
                            </td>

                            <td class="{{ $adjustment['difference'] >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $adjustment['difference'] >= 0 ? '+' : '' }}
                                K{{ number_format($adjustment['difference'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No differences from the original system values were found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <hr>

        <h4><strong>Manager Change History</strong></h4>

        <p class="text-muted">
            This shows each recorded edit, who made it and the reason provided.
        </p>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Field Changed</th>
                        <th>Previous Value</th>
                        <th>New Value</th>
                        <th>Reason</th>
                        <th>Edited By</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $auditRows = $adjustments
                            ->where('type', 'audit')
                            ->sortByDesc('created_at');
                    @endphp

                    @forelse($auditRows as $adjustment)
                        <tr>
                            <td>
                                {{ $adjustment['employee']->first_name ?? '' }}
                                {{ $adjustment['employee']->last_name ?? '' }}
                            </td>

                            <td>
                                {{ ucwords(str_replace('_', ' ', $adjustment['field'])) }}
                            </td>

                            <td>
                                K{{ number_format((float) $adjustment['old_value'], 2) }}
                            </td>

                            <td>
                                <strong>
                                    K{{ number_format((float) $adjustment['new_value'], 2) }}
                                </strong>
                            </td>

                            <td>{{ $adjustment['reason'] }}</td>

                            <td>
                                @if($adjustment['editor'])
                                    {{ $adjustment['editor']->first_name }}
                                    {{ $adjustment['editor']->last_name }}
                                @else
                                    Unknown User
                                @endif
                            </td>

                            <td>
                                {{ $adjustment['created_at']
                                    ? $adjustment['created_at']->format('d M Y H:i')
                                    : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">
                                No manual adjustment history is available for this wage bill.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

    {{-- Employee payroll details --}}
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Employee Payroll Details</h3>
        </div>

        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Predicted Salary</th>
                        <th>Gross Pay</th>
                        <th>PAYE</th>
                        <th>NAPSA</th>
                        <th>NHIMA</th>
                        <th>Allowances</th>
                        <th>Advance Deductions</th>
                        <th>Net Pay</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($wageBills as $wageBill)
                        <tr>
                            <td>
                                {{ $wageBill->employee->first_name ?? '' }}
                                {{ $wageBill->employee->last_name ?? '' }}
                            </td>

                            <td>K{{ number_format($wageBill->predicted_salary, 2) }}</td>
                            <td>K{{ number_format($wageBill->gross_pay, 2) }}</td>
                            <td>K{{ number_format($wageBill->paye, 2) }}</td>
                            <td>K{{ number_format($wageBill->napsa, 2) }}</td>
                            <td>K{{ number_format($wageBill->nhima, 2) }}</td>
                            <td>K{{ number_format($wageBill->allowances, 2) }}</td>
                            <td>K{{ number_format($wageBill->advance_deductions, 2) }}</td>
                            <td>
                                <strong>K{{ number_format($wageBill->net_pay, 2) }}</strong>
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot>
                    <tr>
                        <th>Total</th>
                        <th>K{{ number_format($totals['predicted_salary'], 2) }}</th>
                        <th>K{{ number_format($totals['gross_pay'], 2) }}</th>
                        <th>K{{ number_format($totals['paye'], 2) }}</th>
                        <th>K{{ number_format($totals['napsa'], 2) }}</th>
                        <th>K{{ number_format($totals['nhima'], 2) }}</th>
                        <th>K{{ number_format($totals['allowances'], 2) }}</th>
                        <th>K{{ number_format($totals['advance_deductions'], 2) }}</th>
                        <th>K{{ number_format($totals['net_pay'], 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Approval status and actions --}}
    <div class="row">

        @foreach(['administration' => 'Administration Approval', 'risk' => 'Risk Approval'] as $type => $title)

            @php
                $approval = $submission->approvals->firstWhere('approval_type', $type);
            @endphp

            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $title }}</h3>
                    </div>

                    <div class="box-body">

                        @if($approval)

                            <p>
                                <strong>Status:</strong>

                                <span class="label label-{{
                                    $approval->status == 'approved'
                                        ? 'success'
                                        : ($approval->status == 'returned' ? 'danger' : 'warning')
                                }}">
                                    {{ ucfirst($approval->status) }}
                                </span>
                            </p>

                            @if($approval->approver)
                                <p>
                                    <strong>Actioned By:</strong>
                                    {{ $approval->approver->first_name }}
                                    {{ $approval->approver->last_name }}
                                </p>
                            @endif

                            @if($approval->actioned_at)
                                <p>
                                    <strong>Action Date:</strong>
                                    {{ $approval->actioned_at->format('d M Y H:i') }}
                                </p>
                            @endif

                            @if($approval->comments)
                                <p>
                                    <strong>Comments:</strong><br>
                                    {{ $approval->comments }}
                                </p>
                            @endif

                            @if($submission->status == 'submitted' && $approval->status == 'pending')

                                <hr>

                                <form method="POST"
                                      action="{{ route('wage-bills.approve', [$submission->id, $type]) }}">

                                    @csrf

                                    <div class="form-group">
                                        <label>Approval Comments (Optional)</label>
                                        <textarea name="comments"
                                                  class="form-control"
                                                  rows="3"
                                                  maxlength="2000"
                                                  placeholder="Enter any approval comments..."></textarea>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-success btn-block"
                                            onclick="return confirm('Are you sure you want to approve this wage bill for {{ $type }}?')">
                                        <i class="fa fa-check"></i>
                                        Approve {{ ucfirst($type) }}
                                    </button>
                                </form>

                                <hr>

                                <form method="POST"
                                      action="{{ route('wage-bills.return', [$submission->id, $type]) }}">

                                    @csrf

                                    <div class="form-group">
                                        <label>Reason for Returning</label>
                                        <textarea name="comments"
                                                  class="form-control"
                                                  rows="3"
                                                  minlength="5"
                                                  maxlength="2000"
                                                  required
                                                  placeholder="Explain what needs to be corrected..."></textarea>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-danger btn-block"
                                            onclick="return confirm('Are you sure you want to return this wage bill?')">
                                        <i class="fa fa-undo"></i>
                                        Return to Branch Manager
                                    </button>
                                </form>

                            @endif

                        @else
                            <p class="text-muted">No approval record found.</p>
                        @endif

                    </div>
                </div>
            </div>

        @endforeach

    </div>

    <a href="{{ route('wage-bills.approvals') }}" class="btn btn-default">
        <i class="fa fa-arrow-left"></i> Back to Approvals
    </a>

</section>
@endsection