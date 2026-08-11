@extends('layouts.master')

@section('content')

<section class="content-header">

    <h1>
        Payroll Loan Portfolio Dashboard
    </h1>

</section>


<section class="content">


<!-- ================= DATE FILTER ================= -->

<div class="box box-primary">

    <div class="box-header with-border">

        <h3 class="box-title">
            Filter Period
        </h3>

    </div>


    <div class="box-body">

        <form method="GET" action="{{ url('payroll/dashboard') }}">

            <div class="row">

                <div class="col-md-4">

                    <label>
                        Start Date
                    </label>

                    <input
                        type="date"
                        name="start_date"
                        class="form-control"
                        value="{{ $start_date }}"
                    >

                </div>


                <div class="col-md-4">

                    <label>
                        End Date
                    </label>

                    <input
                        type="date"
                        name="end_date"
                        class="form-control"
                        value="{{ $end_date }}"
                    >

                </div>


                <div class="col-md-4">

                    <label>
                        &nbsp;
                    </label>

                    <br>

                    <button
                        type="submit"
                        class="btn btn-primary btn-block"
                    >
                        <i class="fa fa-search"></i>
                        Load Report
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<!-- ================= NATIONAL SUMMARY ================= -->

<div class="row">


    <!-- NUMBER OF LOANS -->

    <div class="col-lg-3 col-xs-6">

        <div class="small-box bg-aqua">

            <div class="inner">

                <h3>
                    {{ number_format($data['national']['number_of_loans'] ?? 0) }}
                </h3>

                <p>
                    Payroll Loans
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-money"></i>
            </div>

        </div>

    </div>


    <!-- TOTAL PORTFOLIO -->

    <div class="col-lg-3 col-xs-6">

        <div class="small-box bg-yellow">

            <div class="inner">

                <h3>
                    K {{ number_format($data['national']['total_loan_portfolion'] ?? 0, 2) }}
                </h3>

                <p>
                    Total Loan Portfolio
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-bar-chart"></i>
            </div>

        </div>

    </div>


    <!-- EXPECTED COLLECTIONS -->

    <div class="col-lg-3 col-xs-6">

        <div class="small-box bg-green">

            <div class="inner">

                <h3>
                    K {{ number_format($data['national']['expected_collections'] ?? 0, 2) }}
                </h3>

                <p>
                    Expected Collections
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-calculator"></i>
            </div>

        </div>

    </div>


    <!-- TOTAL COLLECTIONS -->

    <div class="col-lg-3 col-xs-6">

        <div class="small-box bg-red">

            <div class="inner">

                <h3>
                    K {{ number_format($data['national']['total_collections'] ?? 0, 2) }}
                </h3>

                <p>
                    Total Collections
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-money"></i>
            </div>

        </div>

    </div>


</div>


<!-- ================= ADDITIONAL NATIONAL SUMMARY ================= -->

<div class="row">


    <!-- GIVEN OUT -->

    <div class="col-lg-4 col-xs-6">

        <div class="small-box bg-blue">

            <div class="inner">

                <h3>
                    K {{ number_format($consultantData['national']['given_out'] ?? 0, 2) }}
                </h3>

                <p>
                    Total Given Out
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-sign-out"></i>
            </div>

        </div>

    </div>


    <!-- EXPECTED INTEREST -->

    <div class="col-lg-4 col-xs-6">

        <div class="small-box bg-purple">

            <div class="inner">

                <h3>
                    K {{ number_format($consultantData['national']['expected_interest'] ?? 0, 2) }}
                </h3>

                <p>
                    Expected Interest
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-percent"></i>
            </div>

        </div>

    </div>


    <!-- UNCOLLECTED -->

    <div class="col-lg-4 col-xs-6">

        <div class="small-box bg-orange">

            <div class="inner">

                <h3>
                    K {{ number_format($consultantData['national']['total_uncollected'] ?? 0, 2) }}
                </h3>

                <p>
                    Total Uncollected
                </p>

            </div>

            <div class="icon">
                <i class="fa fa-warning"></i>
            </div>

        </div>

    </div>


</div>



<!-- ================= LOAN CONSULTANTS ================= -->

<div class="box box-primary">

    <div class="box-header bg-blue">

        <h3 class="box-title text-white">

            <i class="fa fa-users"></i>

            Payroll Loan Consultant Performance

        </h3>

    </div>


    <div class="box-body table-responsive">


        <table class="table table-bordered table-hover">


            <thead class="bg-primary">

                <tr>

                    <th>
                        Loan Consultant
                    </th>

                    <th>
                        Branch
                    </th>

                    <th>
                        Province
                    </th>

                    <th>
                        Loans
                    </th>

                    <th>
                        Given Out
                    </th>

                    <th>
                        Expected Collections
                    </th>

                    <th>
                        Expected Interest
                    </th>

                    <th>
                        Collections
                    </th>

                    <th>
                        Uncollected
                    </th>

                </tr>

            </thead>


            <tbody>


                @foreach($consultants as $index => $consultant)


                    <tr
                        style="cursor:pointer"
                        data-toggle="collapse"
                        data-target="#consultant{{ $index }}"
                        class="bg-info"
                    >


                        <td>

                            <i class="fa fa-plus-circle"></i>

                            <strong>
                                {{ $consultant['consultant_name'] ?? 'Unknown' }}
                            </strong>

                        </td>


                        <td>
                            {{ $consultant['branch_name'] ?? 'Unknown' }}
                        </td>


                        <td>
                            {{ $consultant['province_name'] ?? 'Unknown' }}
                        </td>


                        <td>
                            {{ number_format($consultant['number_of_loans'] ?? 0) }}
                        </td>


                        <td>
                            K {{ number_format($consultant['given_out'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($consultant['expected_collections'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($consultant['expected_interest'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($consultant['total_collections'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($consultant['total_uncollected'] ?? 0, 2) }}
                        </td>


                    </tr>



                    <!-- ================= CONSULTANT DETAILS ================= -->

                    <tr
                        id="consultant{{ $index }}"
                        class="collapse"
                    >

                        <td colspan="9">


                            <div class="box box-success">


                                <div class="box-header">

                                    <h4>

                                        <i class="fa fa-user"></i>

                                        {{ $consultant['consultant_name'] ?? 'Unknown' }}

                                        - Payroll Loans

                                    </h4>

                                </div>


                                <div class="box-body table-responsive">


                                    <table class="table table-bordered table-striped">


                                        <thead>

                                            <tr>

                                                <th>
                                                    Loan ID
                                                </th>

                                                <th>
                                                    Referrer
                                                </th>

                                                <th>
                                                    Client
                                                </th>

                                                <th>
                                                    Given Out
                                                </th>

                                                <th>
                                                    Expected Interest
                                                </th>

                                                <th>
                                                    Expected Collections
                                                </th>

                                                <th>
                                                    Collections
                                                </th>

                                                <th>
                                                    Uncollected
                                                </th>

                                                <th>
                                                    Status
                                                </th>

                                                <th>
                                                    Date
                                                </th>

                                                <th>
                                                    Due Date
                                                </th>

                                                <th>
                                                    Days in Default
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>


                                            @foreach($consultant['loans_list'] ?? [] as $loan)


                                                <tr>

                                                    <td>
                                                        {{ $loan['loan_id'] ?? $loan['id'] ?? '' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['referrer_name'] ?? 'Unknown' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['client_name'] ?? 'Unknown' }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($loan['given_out'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($loan['expected_interest'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($loan['expected_collections'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($loan['total_collections'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($loan['total_uncollected'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['status'] ?? '' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['date'] ?? '' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['due_date'] ?? '' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['days_in_default'] ?? 0 }}
                                                    </td>


                                                </tr>


                                            @endforeach


                                        </tbody>


                                    </table>


                                </div>


                            </div>


                        </td>

                    </tr>


                @endforeach


            </tbody>


        </table>


    </div>

</div>



<!-- ================= PROVINCES ================= -->

<div class="box box-primary">

    <div class="box-header bg-blue">

        <h3 class="box-title text-white">

            <i class="fa fa-map-marker"></i>

            Province Performance

        </h3>

    </div>


    <div class="box-body table-responsive">


        <table class="table table-bordered table-hover">


            <thead class="bg-primary">

                <tr>

                    <th>
                        Province of Origin
                    </th>

                    <th>
                        Loans
                    </th>

                    <th>
                        Expected Collections
                    </th>

                    <th>
                        Expected Interest
                    </th>

                    <th>
                        Collections
                    </th>

                    <th>
                        Uncollected
                    </th>

                </tr>

            </thead>


            <tbody>


                @foreach($data['provinces'] ?? [] as $index => $province)


                    <tr
                        style="cursor:pointer"
                        data-toggle="collapse"
                        data-target="#province{{ $index }}"
                        class="bg-info"
                    >


                        <td>

                            <i class="fa fa-plus-circle"></i>

                            <strong>
                                {{ $province['province_name'] ?? 'Unknown' }}
                            </strong>

                        </td>


                        <td>
                            {{ number_format($province['number_of_loans'] ?? 0) }}
                        </td>


                        <td>
                            K {{ number_format($province['expected_collections'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($province['expected_interest'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($province['total_collections'] ?? 0, 2) }}
                        </td>


                        <td>
                            K {{ number_format($province['total_uncollected'] ?? 0, 2) }}
                        </td>


                    </tr>



                    <!-- ================= BRANCHES ================= -->

                    <tr
                        id="province{{ $index }}"
                        class="collapse"
                    >

                        <td colspan="6">


                            <div class="box box-success">


                                <div class="box-header">

                                    <h4>

                                        <i class="fa fa-building"></i>

                                        Branches

                                    </h4>

                                </div>


                                <div class="box-body table-responsive">


                                    <table class="table table-bordered table-hover">


                                        <thead class="bg-green">

                                            <tr>

                                                <th>
                                                    Branch of Origin
                                                </th>

                                                <th>
                                                    Loans
                                                </th>

                                                <th>
                                                    Expected Collections
                                                </th>

                                                <th>
                                                    Expected Interest
                                                </th>

                                                <th>
                                                    Collections
                                                </th>

                                                <th>
                                                    Uncollected
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>


                                            @foreach($province['branches'] ?? [] as $b => $branch)


                                                <tr
                                                    style="cursor:pointer"
                                                    data-toggle="collapse"
                                                    data-target="#branch{{ $index }}{{ $b }}"
                                                >


                                                    <td>

                                                        <i class="fa fa-plus-circle text-green"></i>

                                                        <strong>
                                                            {{ $branch['branch_name'] ?? 'Unknown' }}
                                                        </strong>

                                                    </td>


                                                    <td>
                                                        {{ number_format($branch['number_of_loans'] ?? 0) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($branch['expected_collections'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($branch['expected_interest'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($branch['total_collections'] ?? 0, 2) }}
                                                    </td>


                                                    <td>
                                                        K {{ number_format($branch['total_uncollected'] ?? 0, 2) }}
                                                    </td>


                                                </tr>



                                                <!-- ================= CONSULTANTS ================= -->

                                                <tr
                                                    id="branch{{ $index }}{{ $b }}"
                                                    class="collapse"
                                                >

                                                    <td colspan="6">


                                                        <div class="box box-warning">


                                                            <div class="box-header">

                                                                <h4>

                                                                    <i class="fa fa-users"></i>

                                                                    Loan Consultants

                                                                </h4>

                                                            </div>


                                                            <div class="box-body table-responsive">


                                                                <table class="table table-bordered">


                                                                    <thead class="bg-yellow">


                                                                        <tr>

                                                                            <th>
                                                                                Consultant
                                                                            </th>

                                                                            <th>
                                                                                Loans
                                                                            </th>

                                                                            <th>
                                                                                Given Out
                                                                            </th>

                                                                            <th>
                                                                                Expected Collections
                                                                            </th>

                                                                            <th>
                                                                                Expected Interest
                                                                            </th>

                                                                            <th>
                                                                                Collections
                                                                            </th>

                                                                            <th>
                                                                                Uncollected
                                                                            </th>

                                                                        </tr>


                                                                    </thead>


                                                                    <tbody>


                                                                    @foreach($branch['consultants'] ?? [] as $c => $consultant)


                                                                        <tr>


                                                                            <td>

                                                                                <strong>
                                                                                    {{ $consultant['consultant_name'] ?? 'Unknown' }}
                                                                                </strong>

                                                                            </td>


                                                                            <td>
                                                                                {{ number_format($consultant['number_of_loans'] ?? 0) }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($consultant['given_out'] ?? 0, 2) }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($consultant['expected_collections'] ?? 0, 2) }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($consultant['expected_interest'] ?? 0, 2) }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($consultant['total_collections'] ?? 0, 2) }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($consultant['total_uncollected'] ?? 0, 2) }}
                                                                            </td>


                                                                        </tr>



                                                                        <!-- ================= CONSULTANT BUTTONS ================= -->

                                                                        <tr>

                                                                            <td colspan="7">


                                                                                <button
                                                                                    class="btn btn-success btn-sm"
                                                                                    data-toggle="collapse"
                                                                                    data-target="#payrollLoans{{ $index }}{{ $b }}{{ $c }}"
                                                                                >

                                                                                    <i class="fa fa-money"></i>

                                                                                    Loans

                                                                                </button>


                                                                                <button
                                                                                    class="btn btn-danger btn-sm"
                                                                                    data-toggle="collapse"
                                                                                    data-target="#payrollCollections{{ $index }}{{ $b }}{{ $c }}"
                                                                                >

                                                                                    <i class="fa fa-list"></i>

                                                                                    Collections

                                                                                </button>


                                                                            </td>

                                                                        </tr>



                                                                        <!-- ================= LOANS ================= -->

                                                                        <tr
                                                                            id="payrollLoans{{ $index }}{{ $b }}{{ $c }}"
                                                                            class="collapse"
                                                                        >

                                                                            <td colspan="7">


                                                                                <div class="box box-success">


                                                                                    <div class="box-body table-responsive">


                                                                                        <table class="table table-bordered table-striped">


                                                                                            <thead>

                                                                                                <tr>

                                                                                                    <th>
                                                                                                        Loan ID
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Referrer Name
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Client
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Given Out
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Expected Interest
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Expected Collections
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Collections
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Uncollected
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Status
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Date
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Due Date
                                                                                                    </th>

                                                                                                    <th>
                                                                                                        Days in Default
                                                                                                    </th>

                                                                                                </tr>

                                                                                            </thead>


                                                                                            <tbody>


                                                                                            @foreach($consultant['loans_list'] ?? [] as $loan)


                                                                                                <tr>

                                                                                                    <td>
                                                                                                        {{ $loan['loan_id'] ?? $loan['id'] ?? '' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['referrer_name'] ?? 'Unknown' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['client_name'] ?? 'Unknown' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        K {{ number_format($loan['given_out'] ?? 0, 2) }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        K {{ number_format($loan['expected_interest'] ?? 0, 2) }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        K {{ number_format($loan['expected_collections'] ?? 0, 2) }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        K {{ number_format($loan['total_collections'] ?? 0, 2) }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        K {{ number_format($loan['total_uncollected'] ?? 0, 2) }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['status'] ?? '' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['date'] ?? '' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['due_date'] ?? '' }}
                                                                                                    </td>


                                                                                                    <td>
                                                                                                        {{ $loan['days_in_default'] ?? 0 }}
                                                                                                    </td>

                                                                                                </tr>


                                                                                            @endforeach


                                                                                            </tbody>


                                                                                        </table>


                                                                                    </div>


                                                                                </div>


                                                                            </td>

                                                                        </tr>



                                                                        <!-- ================= COLLECTIONS ================= -->

                                                                        <tr
                                                                            id="payrollCollections{{ $index }}{{ $b }}{{ $c }}"
                                                                            class="collapse"
                                                                        >

                                                                            <td colspan="7">


                                                                                <table class="table table-bordered table-striped">


                                                                                    <thead>

                                                                                        <tr>

                                                                                            <th>
                                                                                                Date
                                                                                            </th>

                                                                                            <th>
                                                                                                Loan ID
                                                                                            </th>

                                                                                            <th>
                                                                                                Type
                                                                                            </th>

                                                                                            <th>
                                                                                                Applied To
                                                                                            </th>

                                                                                            <th>
                                                                                                Amount
                                                                                            </th>

                                                                                        </tr>

                                                                                    </thead>


                                                                                    <tbody>


                                                                                    @foreach($consultant['collections_list'] ?? [] as $transaction)


                                                                                        <tr>

                                                                                            <td>
                                                                                                {{ $transaction['date'] ?? '' }}
                                                                                            </td>


                                                                                            <td>
                                                                                                {{ $transaction['loan_id'] ?? '' }}
                                                                                            </td>


                                                                                            <td>
                                                                                                {{ $transaction['transaction_type'] ?? '' }}
                                                                                            </td>


                                                                                            <td>
                                                                                                {{ $transaction['payment_apply_to'] ?? '' }}
                                                                                            </td>


                                                                                            <td>
                                                                                                K {{ number_format($transaction['credit'] ?? 0, 2) }}
                                                                                            </td>

                                                                                        </tr>


                                                                                    @endforeach


                                                                                    </tbody>


                                                                                </table>


                                                                            </td>

                                                                        </tr>


                                                                    @endforeach


                                                                    </tbody>


                                                                </table>


                                                            </div>


                                                        </div>


                                                    </td>

                                                </tr>


                                            @endforeach


                                        </tbody>


                                    </table>


                                </div>


                            </div>


                        </td>

                    </tr>


                @endforeach


            </tbody>


        </table>


    </div>

</div>


</section>

@endsection