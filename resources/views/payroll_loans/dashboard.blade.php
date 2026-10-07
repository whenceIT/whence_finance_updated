@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>Payroll Loan Portfolio Dashboard</h1>
</section>

<section class="content">

{{-- ===================== DATE FILTER ===================== --}}
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Filter Period</h3>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ url('payroll/dashboard') }}">
            <div class="row">
                <div class="col-md-4">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $start_date }}">
                </div>
                <div class="col-md-4">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $end_date }}">
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fa fa-search"></i> Load Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger">{{ $errors->first('error') }}</div>
@endif

{{-- ===================== STAT CARDS — ROW 1 ===================== --}}
<div class="row">

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ number_format($data['national']['number_of_loans']) }}</h3>
                <p>Payroll Loans</p>
            </div>
            <div class="icon"><i class="fa fa-file-text-o"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['total_given_out'], 2) }}</h3>
                <p>Total Loan Portfolio</p>
            </div>
            <div class="icon"><i class="fa fa-bar-chart"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['expected_collections'], 2) }}</h3>
                <p>Expected Collections</p>
            </div>
            <div class="icon"><i class="fa fa-calculator"></i></div>
        </div>
    </div>

    <div class="col-lg-3 col-xs-6">
        <div class="small-box bg-red">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['total_collections'], 2) }}</h3>
                <p>Total Collections</p>
            </div>
            <div class="icon"><i class="fa fa-money"></i></div>
        </div>
    </div>

</div>

{{-- ===================== STAT CARDS — ROW 2 ===================== --}}
<div class="row">

    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-blue">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['total_given_out'], 2) }}</h3>
                <p>Total Given Out</p>
            </div>
            <div class="icon"><i class="fa fa-sign-out"></i></div>
        </div>
    </div>

    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['expected_interest'], 2) }}</h3>
                <p>Expected Interest</p>
            </div>
            <div class="icon"><i class="fa fa-percent"></i></div>
        </div>
    </div>

    <div class="col-lg-4 col-xs-6">
        <div class="small-box bg-orange">
            <div class="inner">
                <h3>K&nbsp;{{ number_format($data['national']['total_uncollected'], 2) }}</h3>
                <p>Total Uncollected</p>
            </div>
            <div class="icon"><i class="fa fa-warning"></i></div>
        </div>
    </div>

</div>

{{-- ===================== LOAN CONSULTANT PERFORMANCE ===================== --}}
<div class="box box-primary">
    <div class="box-header bg-blue">
        <h3 class="box-title text-white">
            <i class="fa fa-users"></i>&nbsp; Payroll Loan Consultant Performance
        </h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="bg-primary">
                <tr>
                    <th>Loan Consultant</th>
                    <th>Branch</th>
                    <th>Province</th>
                    <th class="text-center">Loans</th>
                    <th class="text-right">Expected Collections</th>
                    <th class="text-right">Expected Interest</th>
                    <th class="text-right">Collections</th>
                    <th class="text-right">Uncollected</th>
                </tr>
            </thead>
            <tbody>
                @forelse($consultants as $ci => $consultant)

                    {{-- Consultant summary row --}}
                    <tr class="bg-info" style="cursor:pointer"
                        data-toggle="collapse"
                        data-target="#cons{{ $ci }}">
                        <td>
                            <i class="fa fa-plus-circle"></i>
                            <strong>{{ $consultant['consultant_name'] }}</strong>
                        </td>
                        <td>{{ $consultant['branch_name'] }}</td>
                        <td>{{ $consultant['province_name'] }}</td>
                        <td class="text-center">{{ number_format($consultant['number_of_loans']) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($consultant['expected_collections'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($consultant['expected_interest'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($consultant['total_collections'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($consultant['total_uncollected'], 2) }}</td>
                    </tr>

                    {{-- Loan drill-down --}}
                    <tr id="cons{{ $ci }}" class="collapse">
                        <td colspan="8" style="padding:0">
                            <div class="box box-success" style="margin:8px;margin-bottom:4px">
                                <div class="box-header">
                                    <h4>
                                        <i class="fa fa-user"></i>
                                        {{ $consultant['consultant_name'] }} — Loans
                                    </h4>
                                </div>
                                <div class="box-body table-responsive" style="padding:0">
                                    <table class="table table-bordered table-striped" style="margin:0">
                                        <thead>
                                            <tr>
                                                <th>Loan #</th>
                                                <th>Client</th>
                                                <th class="text-right">Principal</th>
                                                <th class="text-right">Exp. Interest</th>
                                                <th class="text-right">Exp. Collections</th>
                                                <th class="text-right">Collections</th>
                                                <th class="text-right">Uncollected</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Disbursed</th>
                                                <th class="text-center">Due Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($consultant['loans_list'] ?? [] as $loan)
                                                <tr>
                                                    <td>{{ $loan['loan_id'] }}</td>
                                                    <td>{{ $loan['client_name'] }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($loan['principal'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($loan['expected_interest'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($loan['expected_collections'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($loan['total_collections'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($loan['total_uncollected'], 2) }}</td>
                                                    <td class="text-center">
                                                        <span class="label label-{{ $loan['status'] === 'disbursed' ? 'success' : 'default' }}">
                                                            {{ ucfirst($loan['status']) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $loan['date'] }}</td>
                                                    <td class="text-center">{{ $loan['due_date'] }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="10" class="text-center text-muted">No loans</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No consultant data for the selected period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===================== PROVINCE PERFORMANCE ===================== --}}
<div class="box box-primary">
    <div class="box-header bg-blue">
        <h3 class="box-title text-white">
            <i class="fa fa-map-marker"></i>&nbsp; Province Performance
        </h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="bg-primary">
                <tr>
                    <th>Province of Origin</th>
                    <th class="text-center">Loans</th>
                    <th class="text-right">Expected Collections</th>
                    <th class="text-right">Expected Interest</th>
                    <th class="text-right">Collections</th>
                    <th class="text-right">Uncollected</th>
                </tr>
            </thead>
            <tbody>
                @forelse($data['provinces'] ?? [] as $pi => $province)

                    {{-- Province row --}}
                    <tr class="bg-info" style="cursor:pointer"
                        data-toggle="collapse"
                        data-target="#prov{{ $pi }}">
                        <td>
                            <i class="fa fa-plus-circle"></i>
                            <strong>{{ $province['province_name'] }}</strong>
                        </td>
                        <td class="text-center">{{ number_format($province['number_of_loans']) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($province['expected_collections'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($province['expected_interest'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($province['total_collections'], 2) }}</td>
                        <td class="text-right">K&nbsp;{{ number_format($province['total_uncollected'], 2) }}</td>
                    </tr>

                    {{-- Branch drill-down --}}
                    <tr id="prov{{ $pi }}" class="collapse">
                        <td colspan="6" style="padding:0">
                            <div class="box box-success" style="margin:8px;margin-bottom:4px">
                                <div class="box-header">
                                    <h4><i class="fa fa-building"></i> Branches — {{ $province['province_name'] }}</h4>
                                </div>
                                <div class="box-body table-responsive" style="padding:0">
                                    <table class="table table-bordered table-hover" style="margin:0">
                                        <thead class="bg-green">
                                            <tr>
                                                <th>Branch</th>
                                                <th class="text-center">Loans</th>
                                                <th class="text-right">Expected Collections</th>
                                                <th class="text-right">Expected Interest</th>
                                                <th class="text-right">Collections</th>
                                                <th class="text-right">Uncollected</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($province['branches'] ?? [] as $bi => $branch)

                                                {{-- Branch row --}}
                                                <tr style="cursor:pointer"
                                                    data-toggle="collapse"
                                                    data-target="#branch{{ $pi }}{{ $bi }}">
                                                    <td>
                                                        <i class="fa fa-plus-circle text-green"></i>
                                                        <strong>{{ $branch['branch_name'] }}</strong>
                                                    </td>
                                                    <td class="text-center">{{ number_format($branch['number_of_loans']) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($branch['expected_collections'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($branch['expected_interest'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($branch['total_collections'], 2) }}</td>
                                                    <td class="text-right">K&nbsp;{{ number_format($branch['total_uncollected'], 2) }}</td>
                                                </tr>

                                                {{-- Consultant drill-down inside branch --}}
                                                <tr id="branch{{ $pi }}{{ $bi }}" class="collapse">
                                                    <td colspan="6" style="padding:0">
                                                        <div class="box box-warning" style="margin:8px;margin-bottom:4px">
                                                            <div class="box-header">
                                                                <h4><i class="fa fa-users"></i> Consultants — {{ $branch['branch_name'] }}</h4>
                                                            </div>
                                                            <div class="box-body table-responsive" style="padding:0">
                                                                <table class="table table-bordered" style="margin:0">
                                                                    <thead class="bg-yellow">
                                                                        <tr>
                                                                            <th>Consultant</th>
                                                                            <th class="text-center">Loans</th>
                                                                            <th class="text-right">Expected Collections</th>
                                                                            <th class="text-right">Expected Interest</th>
                                                                            <th class="text-right">Collections</th>
                                                                            <th class="text-right">Uncollected</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($branch['consultants'] ?? [] as $con)
                                                                            <tr>
                                                                                <td><strong>{{ $con['consultant_name'] }}</strong></td>
                                                                                <td class="text-center">{{ number_format($con['number_of_loans']) }}</td>
                                                                                <td class="text-right">K&nbsp;{{ number_format($con['expected_collections'], 2) }}</td>
                                                                                <td class="text-right">K&nbsp;{{ number_format($con['expected_interest'], 2) }}</td>
                                                                                <td class="text-right">K&nbsp;{{ number_format($con['total_collections'], 2) }}</td>
                                                                                <td class="text-right">K&nbsp;{{ number_format($con['total_uncollected'], 2) }}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="6" class="text-center text-muted">No consultants</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>

                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center text-muted">No branches</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No province data for the selected period</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</section>
@endsection
