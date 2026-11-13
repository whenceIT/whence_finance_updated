@extends('layouts.master')

@section('title')
    Adjust Employee Payroll
@endsection

@section('content')

<style>
    .salary-panel {
        background: #fff;
        border: 1px solid #e5e5e5;
        border-radius: 4px;
        padding: 18px;
        margin-bottom: 20px;
    }

    .salary-panel h4 {
        margin-top: 0;
        font-weight: 600;
    }

    .system-value {
        color: #777;
        font-size: 12px;
        margin-top: 5px;
    }

    .calculated-net {
        background: #f1f8f4;
        border: 1px solid #cde8d6;
        padding: 18px;
        border-radius: 4px;
        margin-bottom: 20px;
    }

    .calculated-net h2 {
        color: #18794e;
        font-weight: bold;
        margin: 5px 0;
    }

    .audit-table th,
    .audit-table td {
        vertical-align: middle !important;
    }
</style>

<section class="content">

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following:</strong>
            <ul style="margin-bottom: 0;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Employee information --}}
    <div class="box box-primary">
        <div class="box-body">
            <div class="row">

                <div class="col-md-4">
                    <strong>Employee</strong>
                    <h4>
                        {{ optional($wageBill->employee)->full_name ?? 'Unknown Employee' }}
                    </h4>
                </div>

                <div class="col-md-4">
                    <strong>Branch</strong>
                    <h4>
                        {{ optional($wageBill->branch)->name ?? 'Unknown Branch' }}
                    </h4>
                </div>

                <div class="col-md-4">
                    <strong>Payroll Cycle</strong>
                    <h4>
                        {{ \Carbon\Carbon::parse($wageBill->cycle_start)->format('d M Y') }}
                        -
                        {{ \Carbon\Carbon::parse($wageBill->cycle_start)->addMonthNoOverflow()->day(24)->format('d M Y') }}
                    </h4>
                </div>

            </div>
        </div>
    </div>

    {{-- System predicted salary --}}
    <div class="salary-panel">
        <h4>
            <i class="fa fa-calculator"></i>
            System Salary Prediction
        </h4>

        <div class="row">

            <div class="col-md-4">
                <label>Predicted Net Salary</label>
                <h3>
                    K{{ number_format($wageBill->predicted_salary, 2) }}
                </h3>
                <small class="text-muted">
                    This value comes from the salary predictor and cannot be edited.
                </small>
            </div>

            <div class="col-md-4">
                <label>System Gross Salary</label>
                <h3>
                    K{{ number_format($wageBill->system_gross_pay, 2) }}
                </h3>
            </div>

            <div class="col-md-4">
                <label>System Net Salary</label>
                <h3>
                    K{{ number_format($wageBill->system_net_pay, 2) }}
                </h3>
            </div>

        </div>
    </div>

    {{-- Adjustment form --}}
    <form method="POST"
          action="{{ route('wage-bills.update', $wageBill->id) }}">

        @csrf
        @method('PUT')

        <div class="box box-primary">

            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-edit"></i>
                    Payroll Adjustments
                </h3>
            </div>

            <div class="box-body">

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    You can adjust the editable payroll figures below.
                    Any changes will be recorded with your name, date and reason.
                    Predicted salary cannot be changed.
                </div>

                <div class="row">

                    {{-- Gross pay --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Gross Pay (K)</label>

                            <input type="number"
                                   name="gross_pay"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('gross_pay', $wageBill->gross_pay) }}">

                            <div class="system-value">
                                System value:
                                K{{ number_format($wageBill->system_gross_pay, 2) }}

                                @if(!empty($wageBill->manual_overrides['gross_pay']))
                                    <span class="label label-warning">Manually adjusted</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- PAYE --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>PAYE (K)</label>

                            <input type="number"
                                   name="paye"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('paye', $wageBill->paye) }}">

                            <div class="system-value">
                                System value:
                                K{{ number_format($wageBill->system_paye, 2) }}

                                @if(!empty($wageBill->manual_overrides['paye']))
                                    <span class="label label-warning">Manually adjusted</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- NAPSA --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NAPSA (K)</label>

                            <input type="number"
                                   name="napsa"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('napsa', $wageBill->napsa) }}">

                            <div class="system-value">
                                System value:
                                K{{ number_format($wageBill->system_napsa, 2) }}

                                @if(!empty($wageBill->manual_overrides['napsa']))
                                    <span class="label label-warning">Manually adjusted</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- NHIMA --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NHIMA (K)</label>

                            <input type="number"
                                   name="nhima"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('nhima', $wageBill->nhima) }}">

                            <div class="system-value">
                                System value:
                                K{{ number_format($wageBill->system_nhima, 2) }}

                                @if(!empty($wageBill->manual_overrides['nhima']))
                                    <span class="label label-warning">Manually adjusted</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Allowances --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Allowances (K)</label>

                            <input type="number"
                                   name="allowances"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('allowances', $wageBill->allowances) }}">

                            <div class="system-value">
                                Manually entered allowance
                            </div>
                        </div>
                    </div>

                    {{-- Advance deductions --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Advance Deductions (K)</label>

                            <input type="number"
                                   name="advance_deductions"
                                   class="form-control"
                                   min="0"
                                   step="0.01"
                                   required
                                   value="{{ old('advance_deductions', $wageBill->advance_deductions) }}">

                            <div class="system-value">
                                Manually entered deduction
                            </div>
                        </div>
                    </div>

                </div>

                <hr>

                {{-- Net pay calculation --}}
                <div class="calculated-net">
                    <div class="row">

                        <div class="col-md-7">
                            <h4>Calculated Net Pay</h4>

                            <p class="text-muted">
                                Predicted Salary + Allowances - Advance Deductions
                            </p>
                        </div>

                        <div class="col-md-5 text-right">
                            <h2 id="calculatedNetPay">
                                K{{ number_format($wageBill->net_pay, 2) }}
                            </h2>
                        </div>

                    </div>
                </div>

                {{-- Reason --}}
                <div class="form-group">
                    <label for="reason">
                        Reason for Adjustment <span class="text-danger">*</span>
                    </label>

                    <textarea name="reason"
                              id="reason"
                              class="form-control"
                              rows="3"
                              minlength="5"
                              maxlength="2000"
                              required
                              placeholder="Explain why these payroll adjustments are necessary...">{{ old('reason') }}</textarea>

                    <small class="text-muted">
                        This reason will be stored in the payroll audit history.
                    </small>
                </div>

            </div>

            <div class="box-footer">

                <a href="{{ route('wage-bills.index', [
                    'branch_id' => $wageBill->branch_id,
                    'cycle_start' => \Carbon\Carbon::parse($wageBill->cycle_start)->format('Y-m-d')
                ]) }}"
                   class="btn btn-default">
                    <i class="fa fa-arrow-left"></i>
                    Back to Wage Bills
                </a>

                <button type="submit" class="btn btn-primary pull-right">
                    <i class="fa fa-save"></i>
                    Save Adjustments
                </button>

            </div>

        </div>

    </form>

    {{-- Audit history --}}
    <div class="box box-default">

        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-history"></i>
                Adjustment History
            </h3>
        </div>

        <div class="box-body">

            @if($wageBill->auditLogs->count())

                <div class="table-responsive">
                    <table class="table table-bordered table-striped audit-table">

                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Changed By</th>
                                <th>Field</th>
                                <th>Previous Value</th>
                                <th>New Value</th>
                                <th>Reason</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($wageBill->auditLogs->sortByDesc('created_at') as $log)
                                <tr>
                                    <td>
                                        {{ optional($log->created_at)->format('d M Y H:i') }}
                                    </td>

                                    <td>
                                        {{ optional($log->editor)->full_name ?? 'Unknown User' }}
                                    </td>

                                    <td>
                                        {{ ucwords(str_replace('_', ' ', $log->field_name)) }}
                                    </td>

                                    <td>
                                        K{{ number_format((float) $log->old_value, 2) }}
                                    </td>

                                    <td>
                                        K{{ number_format((float) $log->new_value, 2) }}
                                    </td>

                                    <td>
                                        {{ $log->reason }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

            @else

                <p class="text-muted text-center">
                    No payroll adjustments have been made to this employee's record.
                </p>

            @endif

        </div>
    </div>

</section>

@endsection

@section('footer-scripts')
<script>
    $(document).ready(function () {

        const predictedSalary = Number(
            @json((float) $wageBill->predicted_salary)
        );

        function calculateNetPay() {

            const allowances = Number(
                $('input[name="allowances"]').val()
            ) || 0;

            const advanceDeductions = Number(
                $('input[name="advance_deductions"]').val()
            ) || 0;

            const netPay =
                predictedSalary + allowances - advanceDeductions;

            $('#calculatedNetPay').text(
                'K' + netPay.toLocaleString('en-ZM', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                })
            );
        }

        $('input[name="allowances"], input[name="advance_deductions"]')
            .on('input', calculateNetPay);

    });
</script>
@endsection