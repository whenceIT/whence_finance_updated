
@extends('layouts.master')

@section('content')

<div class="container-fluid">

    <div class="row">
        <div class="col-md-12">

            <div class="card card-primary">

                <div class="card-header">
                    <h3 class="card-title">Departmental Payroll</h3>
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Payroll cycle selector --}}
                    <form method="GET"
                          action="{{ route('departmental-wage-bills.index') }}"
                          class="form-inline mb-4">

                        <div class="form-group mr-2">
                            <label for="cycle_start" class="mr-2">
                                Payroll Cycle
                            </label>

                            <input
                                type="date"
                                name="cycle_start"
                                id="cycle_start"
                                class="form-control"
                                value="{{ $cycleStart }}"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary">
                            View Cycle
                        </button>

                    </form>

                    <div class="alert alert-info">
                        <strong>Payroll period:</strong>
                        {{ \Carbon\Carbon::parse($cycleStart)->format('d M Y') }}
                        to
                        {{ \Carbon\Carbon::parse($cycleStart)->addMonthNoOverflow()->subDay()->format('d M Y') }}

                        <br>

                        <small>
                            PAYE, NAPSA, NHIMA and advance deductions are calculated automatically.
                            All amounts are in Zambian Kwacha.
                        </small>
                    </div>

                    <div class="table-responsive">

                        <table class="table table-bordered table-striped table-sm">

                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Basic Salary</th>
                                    <th>Allowances</th>
                                    <th>Gross Pay</th>
                                    <th>PAYE</th>
                                    <th>NAPSA</th>
                                    <th>NHIMA</th>
                                    <th>Advance Deductions</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Reason</th>
                                    <th>Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($employees as $employee)

                                    @php
                                        $wageBill = $wageBills->get($employee->id);

                                        $canEdit = !$wageBill || $wageBill->status === 'draft';

                                        $formId = 'departmental-payroll-form-' . $employee->id;

                                        /*
                                         * For new and draft records, use the current
                                         * pending advance instalments.
                                         *
                                         * For submitted/approved records, display the
                                         * advance amount saved with that payroll record.
                                         */
                                        if ($wageBill && $wageBill->status !== 'draft') {
                                            $advanceAmount = (float) $wageBill->advance_deductions;
                                        } else {
                                            $advanceAmount = (float) ($advanceDeductions->get($employee->id, 0));
                                        }
                                    @endphp

                                    <tr
                                        id="payroll-row-{{ $employee->id }}"
                                        data-advance="{{ $advanceAmount }}"
                                    >

                                        <td style="min-width: 180px;">

                                            <strong>
                                                {{ $employee->first_name }}
                                                {{ $employee->last_name }}
                                            </strong>

                                            <br>

                                            <small class="text-muted">
                                                ID: {{ $employee->id }}
                                            </small>

                                            <form
                                                id="{{ $formId }}"
                                                method="POST"
                                                action="{{ route('departmental-wage-bills.store') }}"
                                            >
                                                @csrf

                                                <input
                                                    type="hidden"
                                                    name="user_id"
                                                    value="{{ $employee->id }}"
                                                >

                                                <input
                                                    type="hidden"
                                                    name="cycle_start"
                                                    value="{{ $cycleStart }}"
                                                >
                                            </form>

                                        </td>

                                        <td style="min-width: 120px;">

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="basic_salary"
                                                form="{{ $formId }}"
                                                class="form-control payroll-input basic-salary"
                                                value="{{ $wageBill->basic_salary ?? '' }}"
                                                data-row="{{ $employee->id }}"
                                                {{ !$canEdit ? 'disabled' : '' }}
                                                required
                                            >

                                        </td>

                                        <td style="min-width: 120px;">

                                            <input
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="allowances"
                                                form="{{ $formId }}"
                                                class="form-control payroll-input allowances"
                                                value="{{ $wageBill->allowances ?? 0 }}"
                                                data-row="{{ $employee->id }}"
                                                {{ !$canEdit ? 'disabled' : '' }}
                                            >

                                        </td>

                                        <td>
                                            <span id="gross-{{ $employee->id }}">
                                                {{ number_format($wageBill->gross_pay ?? 0, 2) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span id="paye-{{ $employee->id }}">
                                                {{ number_format($wageBill->paye ?? 0, 2) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span id="napsa-{{ $employee->id }}">
                                                {{ number_format($wageBill->napsa ?? 0, 2) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span id="nhima-{{ $employee->id }}">
                                                {{ number_format($wageBill->nhima ?? 0, 2) }}
                                            </span>
                                        </td>

                                        <td>
                                            <strong>
                                                K{{ number_format($advanceAmount, 2) }}
                                            </strong>
                                        </td>

                                        <td>
                                            <strong id="net-{{ $employee->id }}">
                                                {{ number_format($wageBill->net_pay ?? 0, 2) }}
                                            </strong>
                                        </td>

                                        <td>

                                            @if($wageBill)

                                                @if($wageBill->status === 'draft')
                                                    <span class="badge badge-secondary">
                                                        Draft
                                                    </span>

                                                @elseif($wageBill->status === 'submitted')
                                                    <span class="badge badge-info">
                                                        Submitted
                                                    </span>

                                                @elseif($wageBill->status === 'approved')
                                                    <span class="badge badge-success">
                                                        Approved
                                                    </span>

                                                @elseif($wageBill->status === 'returned')
                                                    <span class="badge badge-warning">
                                                        Returned
                                                    </span>

                                                @else
                                                    <span class="badge badge-secondary">
                                                        {{ ucfirst($wageBill->status) }}
                                                    </span>
                                                @endif

                                            @else

                                                <span class="badge badge-light">
                                                    Not entered
                                                </span>

                                            @endif

                                        </td>

                                        <td style="min-width: 180px;">

                                            <textarea
                                                name="reason"
                                                form="{{ $formId }}"
                                                class="form-control"
                                                rows="2"
                                                placeholder="Reason for creating/updating this record"
                                                {{ !$canEdit ? 'disabled' : '' }}
                                                required
                                            ></textarea>

                                        </td>

                                        <td>

                                            @if($canEdit)

                                                <button
                                                    type="submit"
                                                    form="{{ $formId }}"
                                                    class="btn btn-sm btn-primary"
                                                >
                                                    Save
                                                </button>

                                            @else

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-secondary"
                                                    disabled
                                                >
                                                    Locked
                                                </button>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="12" class="text-center">
                                            No departmental employees found.
                                        </td>
                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection

<script>
document.addEventListener('DOMContentLoaded', function () {

    function money(value) {
        return Number(value || 0).toLocaleString('en-ZM', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // PAYE - 2026 Zambian monthly tax bands
    function calculatePaye(grossPay) {
        if (grossPay <= 5100) {
            return 0;
        }

        if (grossPay <= 7100) {
            return (grossPay - 5100) * 0.20;
        }

        if (grossPay <= 9200) {
            return 400 + ((grossPay - 7100) * 0.30);
        }

        return 1030 + ((grossPay - 9200) * 0.37);
    }

    // NAPSA - 5%, capped at K37,236 earnings
    function calculateNapsa(grossPay) {
        const contributionBase = Math.min(grossPay, 37236);
        return contributionBase * 0.05;
    }

    // NHIMA - 1% of gross pay
    function calculateNhima(basicSalary, grossPay) {
        return grossPay * 0.01;
    }

    function calculateRow(employeeId) {

        const basicInput = document.querySelector(
            '.basic-salary[data-row="' + employeeId + '"]'
        );

        const allowancesInput = document.querySelector(
            '.allowances[data-row="' + employeeId + '"]'
        );

        const row = document.getElementById(
            'payroll-row-' + employeeId
        );

        if (!basicInput || !allowancesInput || !row) {
            return;
        }

        const basicSalary = Math.max(
            0,
            parseFloat(basicInput.value) || 0
        );

        const allowances = Math.max(
            0,
            parseFloat(allowancesInput.value) || 0
        );

        const advanceDeductions = Math.max(
            0,
            parseFloat(row.dataset.advance) || 0
        );

        const grossPay = basicSalary + allowances;

        const paye = calculatePaye(grossPay);
        const napsa = calculateNapsa(grossPay);
        const nhima = calculateNhima(basicSalary, grossPay);

        const netPay =
            grossPay -
            paye -
            napsa -
            nhima -
            advanceDeductions;

        const grossElement = document.getElementById('gross-' + employeeId);
        const payeElement = document.getElementById('paye-' + employeeId);
        const napsaElement = document.getElementById('napsa-' + employeeId);
        const nhimaElement = document.getElementById('nhima-' + employeeId);
        const netElement = document.getElementById('net-' + employeeId);

        if (grossElement) grossElement.textContent = money(grossPay);
        if (payeElement) payeElement.textContent = money(paye);
        if (napsaElement) napsaElement.textContent = money(napsa);
        if (nhimaElement) nhimaElement.textContent = money(nhima);
        if (netElement) netElement.textContent = money(netPay);
    }

    // Recalculate whenever basic salary or allowances change
    document.querySelectorAll('.basic-salary, .allowances').forEach(function (input) {
        input.addEventListener('input', function () {
            calculateRow(this.dataset.row);
        });
    });

    // Calculate all rows when the page loads
    document.querySelectorAll('.basic-salary').forEach(function (input) {
        calculateRow(input.dataset.row);
    });

});
</script>
