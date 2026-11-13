
@extends('layouts.master')

@section('content')

<div class="container-fluid">

    <div class="row mb-3">
        <div class="col-md-12">
            <h3>My Payroll</h3>
            <p class="text-muted">
                View your payroll history and download your payslips.
            </p>
        </div>
    </div>

    {{-- Latest Payroll Summary --}}
    @if($latestPayroll)

        <div class="row">

            <div class="col-md-6">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3>
                            K{{ number_format($latestPayroll->net_pay, 2) }}
                        </h3>
                        <p>Latest Net Pay</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3>
                            {{ \Carbon\Carbon::parse($latestPayroll->cycle_start)->format('d M Y') }}
                        </h3>
                        <p>Latest Payroll Cycle Start</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>

        </div>

        {{-- Latest Payroll Breakdown --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Latest Payroll Breakdown</h3>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">
                        <h5 class="text-success">Earnings</h5>

                        <table class="table table-sm">
                            <tr>
                                <td>Performance-Based Salary</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->predicted_salary, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>Allowances</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->allowances, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-danger">Deductions</h5>

                        <table class="table table-sm">
                            <tr>
                                <td>PAYE</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->paye, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>NAPSA</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->napsa, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>NHIMA</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->nhima, 2) }}
                                </td>
                            </tr>

                            <tr>
                                <td>Salary Advance</td>
                                <td class="text-right">
                                    K{{ number_format($latestPayroll->advance_deductions, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>

                </div>

                <hr>

                <div class="row">
                    <div class="col-md-8">
                        <h4>Net Pay</h4>
                    </div>

                    <div class="col-md-4 text-right">
                        <h3 class="text-success">
                            K{{ number_format($latestPayroll->net_pay, 2) }}
                        </h3>
                    </div>
                </div>

                <div class="text-right">
                    <a href="{{ route('wage-bills.payslip', $latestPayroll->id) }}"
                       class="btn btn-primary">
                        <i class="fas fa-file-pdf"></i>
                        Download Latest Payslip
                    </a>
                </div>

            </div>
        </div>

    @else

        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i>
            You do not have any approved payroll records yet.
        </div>

    @endif


    {{-- Payroll History --}}
    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Payroll History</h3>
        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-hover">

                <thead>
                    <tr>
                        <th>Payroll Cycle</th>
                        <th>Branch</th>
                        <th>Predicted Salary</th>
                        <th>Allowances</th>
                        <th>Advance Deductions</th>
                        <th>Net Pay</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($payrolls as $payroll)

                        @php
                            $cycleStart = \Carbon\Carbon::parse($payroll->cycle_start);
                            $cycleEnd = $cycleStart->copy()->addMonth()->day(24);
                        @endphp

                        <tr>
                            <td>
                                {{ $cycleStart->format('d M Y') }}
                                -
                                {{ $cycleEnd->format('d M Y') }}
                            </td>

                            <td>
                                {{ $payroll->branch->name ?? 'N/A' }}
                            </td>

                            <td>
                                K{{ number_format($payroll->predicted_salary, 2) }}
                            </td>

                            <td>
                                K{{ number_format($payroll->allowances, 2) }}
                            </td>

                            <td>
                                K{{ number_format($payroll->advance_deductions, 2) }}
                            </td>

                            <td>
                                <strong class="text-success">
                                    K{{ number_format($payroll->net_pay, 2) }}
                                </strong>
                            </td>

                            <td>
                                <a href="{{ route('wage-bills.payslip', $payroll->id) }}"
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                    Payslip
                                </a>
                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td colspan="7" class="text-center">
                                No approved payroll history found.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

            <div class="mt-3">
                {{ $payrolls->links() }}
            </div>

        </div>
    </div>

</div>

@endsection
