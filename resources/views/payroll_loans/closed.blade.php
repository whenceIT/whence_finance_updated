@extends('layouts.master')

@section('title')
    Closed Payroll Loans
@endsection

@section('content')
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <h3 class="box-title">
                        <i class="fa fa-lock"></i> Closed Payroll Loans
                    </h3>
                    <div class="box-tools pull-right">
                        <span class="label label-default">{{ $loans->count() }} Closed</span>
                        <a href="{{ url('payrollloans/dashboard') }}" class="btn btn-xs btn-default" style="margin-left:8px;">
                            <i class="fa fa-tachometer"></i> Dashboard
                        </a>
                        <a href="{{ url('loan/create') }}" class="btn btn-xs btn-primary" style="margin-left:4px;">
                            <i class="fa fa-plus"></i> Add Loan
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    @include('payroll_loans._partials.loans_table')
                </div>
            </div>
        </div>
    </div>

</section>
@endsection
