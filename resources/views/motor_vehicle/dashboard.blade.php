@extends('layouts.master')

@section('content')


<section class="content-header">

    <h1>
        Motor Vehicle Loan Portfolio Dashboard

        <a href="{{ url('/vehicles/analytics_dashboard') }}" 
           class="btn btn-primary pull-right">
            <i class="fa fa-bar-chart"></i>
            Analytics Dashboard
        </a>

    </h1>

</section>


<section class="content">


<div class="box box-primary">

    <div class="box-header with-border">

        <h3 class="box-title">
            Filter Period
        </h3>

    </div>


    <div class="box-body">


        <form method="GET" action="{{ url('vehicles/dashboard') }}">


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

<section class="content">

<!-- ================= INSURANCE REMINDERS ================= -->

<div class="box box-danger">

    <div class="box-header with-border">

        <h3 class="box-title">
            <i class="fa fa-bell"></i>
            Insurance Expiry Reminders
        </h3>

    </div>

    <div class="box-body">

        @if($insuranceReminders->count())

            <table class="table table-bordered table-hover">

                <thead>

                    <tr>
                        <th>Registration</th>
                        <th>Owner</th>
                        <th>Insurer</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th></th>
                    </tr>

                </thead>

                <tbody>

                @foreach($insuranceReminders as $insurance)

                    @php
                        $daysRemaining = \Carbon\Carbon::today()->diffInDays(
                            \Carbon\Carbon::parse($insurance->expiry_date),
                            false
                        );
                    @endphp

                    <tr>

                        <td>{{ optional($insurance->vehicle)->registration_number }}</td>

                        <td>
                            {{ optional(optional($insurance->vehicle)->client)->first_name }}
                            {{ optional(optional($insurance->vehicle)->client)->last_name }}
                        </td>

                        <td>{{ $insurance->insurer_name }}</td>

                        <td>{{ $insurance->expiry_date }}</td>

                        <td>

                            @if($daysRemaining < 0)

                                <span class="label label-danger">
                                    Expired {{ abs($daysRemaining) }} day(s) ago
                                </span>

                            @elseif($daysRemaining == 0)

                                <span class="label label-danger">
                                    Expires Today
                                </span>

                            @elseif($daysRemaining <= 7)

                                <span class="label label-warning">
                                    Expires in {{ $daysRemaining }} day(s)
                                </span>

                            @else

                                <span class="label label-info">
                                    Expires in {{ $daysRemaining }} day(s)
                                </span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ url('vehicles/'.$insurance->vehicle_id) }}"
                               class="btn btn-xs btn-primary">

                                View Vehicle

                            </a>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        @else

            <div class="alert alert-success">
                No insurance policies require attention.
            </div>

        @endif

    </div>

</div>


<!-- ================= NATIONAL SUMMARY ================= -->

<div class="row">


<div class="col-lg-3 col-xs-6">

<div class="small-box bg-aqua" style="cursor: pointer;" data-endpoint="loans">

<div class="inner">

<h3>
{{ number_format($data['national']['number_of_loans']) }}
</h3>

<p>
Motor Vehicle Loans
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Total count of active Motor Vehicle Loans (MVL) on the book."></span>
</p>

</div>

<div class="icon">
<i class="fa fa-money"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-blue" style="cursor: pointer;" data-endpoint="vehicles">

<div class="inner">

<h3>
{{ number_format($data['national']['number_of_vehicles']) }}
</h3>

<p>
Vehicles
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Total number of vehicles registered under Motor Vehicle Loans (MVL)."></span>
</p>

</div>


<div class="icon">
<i class="fa fa-car"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-yellow" style="cursor: pointer;" data-endpoint="portfolio">

<div class="inner">

<h3>
K {{ number_format($data['national']['total_loan_portfolion'],2) }}
</h3>


<p>
Total Portfolio Value
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Sum of outstanding principal balances across all Motor Vehicle Loans (MVL)."></span>
</p>

</div>


<div class="icon">
<i class="fa fa-bar-chart"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-green" style="cursor: pointer;" data-endpoint="collections">

<div class="inner">

<h3>
K {{ number_format($data['national']['total_collections'],2) }}
</h3>


<p>
Total Collections
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Sum of repayments received against Motor Vehicle Loans (MVL) to date."></span>
</p>

</div>


<div class="icon">
<i class="fa fa-money"></i>
</div>


</div>

</div>


</div>


<div class="row" style="margin-top: 20px;">
<div class="col-lg-3 col-xs-6">
<div class="small-box bg-navy" style="cursor: pointer;" data-endpoint="defaulted">
<div class="inner">
<h3>{{ number_format($defaultedMVLCount) }}</h3>
<p>
MVLs in Default (Count)
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Total count of Motor Vehicle Loans (MVL) that have been in default (Past their due date).">
</span>
</p>
</div>
<div class="icon">
<i class="fa fa-exclamation-triangle"></i>
</div>
</div>
</div>


<div class="col-lg-3 col-xs-6">
<div class="small-box bg-red" style="cursor: pointer;" data-endpoint="defaulted">
<div class="inner">
<h3>K {{ number_format($defaultedMVL, 2) }}</h3>
<p>
MVLs in Default
<span class="fa fa-info-circle" style="color: #fff; cursor: help;" data-toggle="tooltip" data-placement="top" title="Total outstanding balance (from loan_transactions: debit minus credit) of Motor Vehicle Loans (MVL) that have been in default (Past their due date)">
</span>
</p>
</div>
<div class="icon">
<i class="fa fa-ban"></i>
</div>
</div>
</div>
</div>


<!-- ================= LOAN CONSULTANTS ================= -->

<style>
    .consultant-section .main-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.1);
        border: none;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 14px;
        width: 100%;
    }
    .consultant-section .main-table thead {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: #fff;
    }
    .consultant-section .main-table thead th {
        padding: 14px 16px;
        border: none;
        font-size: 13px;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .consultant-section .main-table thead th:nth-child(4),
    .consultant-section .main-table thead th:nth-child(5) { text-align: center; }
    .consultant-section .main-table thead th:nth-child(n+6) { text-align: right; }
    .consultant-section .main-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e8e8e8;
        cursor: pointer;
    }
    .consultant-section .main-table tbody tr:hover {
        background: #f0f7ff;
        box-shadow: inset 3px 0 0 #2a5298;
    }
    .consultant-section .main-table tbody td {
        padding: 12px 16px;
        font-size: 14px;
        vertical-align: middle;
    }
    .consultant-section .main-table .col-highlight { color: #2a5298; font-weight: 600; }
    .consultant-section .main-table .col-positive { color: #2e7d32; font-weight: 600; }
    .consultant-section .main-table .col-negative { color: #c62828; font-weight: 600; }
    .consultant-section .main-table .col-muted { color: #555; }

    .consultant-section .detail-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        border: 1px solid #e0e0e0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13px;
        width: 100%;
    }
    .consultant-section .detail-table thead {
        background: linear-gradient(135deg, #43a047 0%, #66bb6a 100%);
        color: #fff;
    }
    .consultant-section .detail-table thead th {
        padding: 10px 12px;
        border: none;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.2px;
    }
    .consultant-section .detail-table tbody tr {
        transition: background 0.15s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .consultant-section .detail-table tbody tr:hover {
        background: #f5fff5;
    }
    .consultant-section .detail-table tbody td {
        padding: 10px 12px;
        font-size: 13px;
        vertical-align: middle;
    }
    .consultant-section .detail-table .col-number { text-align: right; font-weight: 600; color: #333; }
    .consultant-section .detail-table .col-status { font-weight: 600; padding: 3px 8px; border-radius: 4px; font-size: 12px; }
    .consultant-section .detail-table .col-status.disbursed { background: #e8f5e9; color: #2e7d32; }
    .consultant-section .detail-table .col-status.pending { background: #fff3e0; color: #e65100; }
    .consultant-section .detail-table .col-status.overdue { background: #ffebee; color: #c62828; }
    .consultant-section .detail-table .col-due { color: #1565c0; font-weight: 500; white-space: nowrap; }
    .consultant-section .detail-table .col-default { color: #c62828; font-weight: 600; text-align: center; }
    .consultant-section .detail-table .btn-expand { transition: all 0.2s ease; }
    .consultant-section .detail-table .btn-expand:hover { transform: scale(1.05); }

    .consultant-section .vehicle-table {
        border-collapse: separate;
        border-spacing: 0;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 6px rgba(0,0,0,0.08);
        border: 1px solid #e0e0e0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        font-size: 13px;
        width: 100%;
    }
    .consultant-section .vehicle-table thead {
        background: linear-gradient(135deg, #fb8c00 0%, #ffa726 100%);
        color: #fff;
    }
    .consultant-section .vehicle-table thead th {
        padding: 10px 12px;
        border: none;
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.2px;
    }
    .consultant-section .vehicle-table tbody tr {
        transition: background 0.15s ease;
        border-bottom: 1px solid #f0f0f0;
    }
    .consultant-section .vehicle-table tbody tr:hover {
        background: #fff8e1;
    }
    .consultant-section .vehicle-table tbody td {
        padding: 10px 12px;
        font-size: 13px;
        vertical-align: middle;
    }
    .consultant-section .vehicle-table img {
        border-radius: 4px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .consultant-section .vehicle-table img:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
</style>

<div class="box box-primary consultant-section">

    <div class="box-header bg-blue">

        <h3 class="box-title text-white">
            <i class="fa fa-users"></i>
            Loan Consultant Performance
        </h3>

    </div>


    <div class="box-body table-responsive bg-blue" style="padding: 20px; background: linear-gradient(135deg, #e8f0fe 0%, #d4e4fc 100%);">


        <table class="table table-bordered table-hover" style="border-collapse: separate; border-spacing: 0; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.1); border: none; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px;">


            <thead style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: #fff;">

                <tr style="font-weight: 600; letter-spacing: 0.3px;">

                    <th style="padding: 14px 16px; text-align: left; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Loan Consultant
                    </th>

                    <th style="padding: 14px 16px; text-align: left; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Branch
                    </th>

                    <th style="padding: 14px 16px; text-align: left; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Province
                    </th>

                    <th style="padding: 14px 16px; text-align: center; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Loans
                    </th>

                    <th style="padding: 14px 16px; text-align: center; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Vehicles
                    </th>

                    <th style="padding: 14px 16px; text-align: right; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Given Out
                    </th>

                    <th style="padding: 14px 16px; text-align: right; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Expected Collections
                    </th>

                    <th style="padding: 14px 16px; text-align: right; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Expected Interest
                    </th>

                    <th style="padding: 14px 16px; text-align: right; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Collections
                    </th>

                    <th style="padding: 14px 16px; text-align: right; border: none; font-size: 13px; text-transform: uppercase; font-weight: 700;">
                        Uncollected
                    </th>

                </tr>

            </thead>


            <tbody style="background: #fff;">


                @foreach($consultantData['consultants'] as $index => $consultant)


                    <tr
                        style="cursor:pointer; transition: all 0.2s ease; border-bottom: 1px solid #e8e8e8;"
                        onmouseover="this.style.background='#f0f7ff'; this.style.boxShadow='inset 3px 0 0 #2a5298';"
                        onmouseout="this.style.background=''; this.style.boxShadow='';"
                        data-toggle="collapse"
                        data-target="#consultant{{$index}}"
                        class="bg-info"
                    >


                        <td style="padding: 12px 16px; font-size: 14px;">

                            <i class="fa fa-plus-circle" style="color: #2a5298; margin-right: 6px;"></i>

                            <strong>
                                {{ $consultant['consultant_name'] }}
                            </strong>

                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; color: #555;">
                            {{ $consultant['branch_name'] }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; color: #555;">
                            {{ $consultant['province_name'] }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: center; font-weight: 600; color: #2a5298;">
                            {{ number_format($consultant['number_of_loans']) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: center; font-weight: 600; color: #2a5298;">
                            {{ number_format($consultant['number_of_vehicles']) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: right; font-weight: 600; color: #333;">
                            K {{ number_format($consultant['given_out'] ?? 0, 2) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: right; color: #555;">
                            K {{ number_format($consultant['expected_collections'] ?? 0, 2) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: right; color: #555;">
                            K {{ number_format($consultant['expected_interest'] ?? 0, 2) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: right; font-weight: 600; color: #2e7d32;">
                            K {{ number_format($consultant['total_collections'] ?? 0, 2) }}
                        </td>


                        <td style="padding: 12px 16px; font-size: 14px; text-align: right; font-weight: 600; color: #c62828;">
                            K {{ number_format($consultant['total_uncollected'] ?? 0, 2) }}
                        </td>


                    </tr>



                    <!-- ================= CONSULTANT DETAILS ================= -->

                    <tr id="consultant{{$index}}" class="collapse">

                        <td colspan="10">


                            <div class="box box-success">


                                <div class="box-header">

                                    <h4>

                                        <i class="fa fa-user"></i>

                                        {{ $consultant['consultant_name'] }}

                                        - Loans & Vehicles

                                    </h4>

                                </div>


                                <div class="box-body table-responsive">


                                    <table class="detail-table">


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

                                                <th>
                                                    Vehicles
                                                </th>

                                            </tr>

                                        </thead>


                                        <tbody>


                                            @foreach($consultant['loans_list'] as $loan)


                                                <tr>

                                                    <td>
                                                        {{ $loan['loan_id'] }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['referrer_name'] ?? '' }}
                                                    </td>


                                                    <td>
                                                        {{ $loan['client_name'] ?? '' }}
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
                                                        @if(!empty($loan['due_date']))
                                                            {{ \Carbon\Carbon::parse($loan['due_date'])->format('d M Y') }}
                                                        @endif
                                                    </td>


                                                    <td>
                                                        {{ $loan['days_in_default'] ?? 0 }}
                                                    </td>


                                                    <td>

                                                        @if(!empty($loan['vehicles']))

                                                            <button
                                                                class="btn btn-warning btn-xs"
                                                                data-toggle="collapse"
                                                                data-target="#vehicle{{$index}}{{$loan['loan_id']}}"
                                                            >

                                                                <i class="fa fa-car"></i>

                                                                {{ count($loan['vehicles']) }}

                                                            </button>

                                                        @else

                                                            0

                                                        @endif

                                                    </td>


                                                </tr>


                                                <!-- ================= VEHICLES FOR LOAN ================= -->

                                                @if(!empty($loan['vehicles']))

                                                    <tr
                                                        id="vehicle{{$index}}{{$loan['loan_id']}}"
                                                        class="collapse"
                                                    >

                                                        <td colspan="13">


                                                            <table class="vehicle-table">


                                                                <thead>

                                                                     <tr>

                                                                         <th>
                                                                             Image
                                                                         </th>

                                                                         <th>
                                                                             Registration
                                                                         </th>

                                                                         <th>
                                                                             Model
                                                                         </th>

                                                                         <th>
                                                                             Market Value
                                                                         </th>

                                                                         <th>
                                                                             Loan ID
                                                                         </th>

                                                                     </tr>

                                                                </thead>


                                                                <tbody>


                                                                    @foreach($loan['vehicles'] as $vehicle)


                                                                         <tr>

                                                                             <td>
                                                                                 @if(!empty($vehicle['photos']) && count($vehicle['photos']) > 0)
                                                                                     <img src="{{ $vehicle['photos'][0]['photo_url'] }}"
                                                                                          class="vehicle-photo-thumb"
                                                                                          data-photos='@json(collect($vehicle['photos'])->pluck("photo_url"))'
                                                                                          style="height: 50px; width: auto; object-fit: cover; border-radius: 4px; cursor: pointer;"
                                                                                          alt="Vehicle photo">
                                                                                 @else
                                                                                     <span class="text-muted">No photo</span>
                                                                                 @endif
                                                                             </td>

                                                                             <td>
                                                                                 {{ $vehicle['registration_number'] ?? '' }}
                                                                             </td>


                                                                            <td>
                                                                                {{ $vehicle['model'] ?? '' }}
                                                                            </td>


                                                                            <td>
                                                                                K {{ number_format($vehicle['market_value'] ?? 0, 2) }}
                                                                            </td>


                                                                            <td>
                                                                                {{ $vehicle['loan_id'] ?? '' }}
                                                                            </td>

                                                                        </tr>


                                                                    @endforeach


                                                                </tbody>


                                                            </table>


                                                        </td>

                                                    </tr>

                                                @endif


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

<!-- ================= PROVINCES ================= -->

<div class="box box-primary">

<div class="box-header bg-blue">

<h3 class="box-title text-white">
<i class="fa fa-map-marker"></i>
Province Performance
</h3>

</div>


<div class="box-body table-responsive bg-blue">


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
Vehicles
</th>

<th>
Vehicle Value
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



</tr>

</thead>


<tbody>


@foreach($data['provinces'] as $index=>$province)


<tr 
style="cursor:pointer"
data-toggle="collapse"
data-target="#province{{$index}}"
class="bg-info"
>


<td>

<i class="fa fa-plus-circle"></i>

<strong>
{{ $province['province_name'] }}
</strong>

</td>


<td>
{{ number_format($province['number_of_loans']) }}
</td>


<td>
{{ number_format($province['number_of_vehicles']) }}
</td>


<td>
K {{ number_format($province['total_vehicle_value'],2) }}
</td>

<td>
K {{ number_format($province['expected_collections'],2) }}
</td>

<td>
K {{ number_format($province['expected_interest'],2) }}
</td>


<td>
K {{ number_format($province['total_collections'],2) }}
</td>


</tr>



<tr id="province{{$index}}" class="collapse">


<td colspan="5">


<div class="box box-success">


<div class="box-header">

<h4>
<i class="fa fa-building"></i>

Branches

</h4>


</div>


<div class="box-body">


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
Vehicles
</th>

<th>
Expected Collections
</th>


<th>
Expected Interest
</th>


<th>
Value
</th>

<th>
Collections
</th>

</tr>

</thead>



<tbody>


@foreach($province['branches'] as $b=>$branch)


<tr

style="cursor:pointer"

data-toggle="collapse"

data-target="#branch{{$index}}{{$b}}"

>


<td>

<i class="fa fa-plus-circle text-green"></i>

<strong>
{{ $branch['branch_name'] }}
</strong>

</td>


<td>
{{ number_format($branch['number_of_loans']) }}
</td>

<td>
{{ number_format($branch['number_of_vehicles']) }}
</td>


<td>
K{{ number_format($branch['expected_collections']) }}
</td>


<td>
K{{ number_format($branch['expected_interest']) }}
</td>


<td>

K {{ number_format($branch['total_vehicle_value'],2) }}

</td>


<td>

K {{ number_format($branch['total_collections'],2) }}

</td>


</tr>



<tr id="branch{{$index}}{{$b}}" class="collapse">


<td colspan="5">



<div class="box box-warning">


<div class="box-header">

<h4>

<i class="fa fa-user"></i>

Loan Consultants

</h4>

</div>



<div class="box-body">


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
Vehicles
</th>



<th>
Vehicle Value
</th>

<th>
Collections
</th>

<th>
Expected Collections
</th>


<th>
Expected Interest
</th>

</tr>


</thead>


<tbody>


@foreach($branch['consultants'] as $c=>$consultant)


<tr>

<td>

<strong>
{{ $consultant['consultant_name'] }}
</strong>

</td>



<td>
{{ number_format($consultant['number_of_loans']) }}
</td>


<td>
{{ number_format($consultant['number_of_vehicles']) }}
</td>


<td>
K {{ number_format($consultant['total_vehicle_value'],2) }}
</td>


<td>
K {{ number_format($consultant['total_collections'],2) }}
</td>

<td>
K {{ number_format($consultant['expected_collections'],2) }}
</td>

<td>
K {{ number_format($consultant['expected_interest'],2) }}
</td>


</tr>


<tr>

<td colspan="5">


<button 
class="btn btn-success btn-sm"
data-toggle="collapse"
data-target="#loans{{$index}}{{$b}}{{$c}}">

<i class="fa fa-money"></i>
Loans

</button>


<button 
class="btn btn-warning btn-sm"
data-toggle="collapse"
data-target="#vehicles{{$index}}{{$b}}{{$c}}">

<i class="fa fa-car"></i>
Vehicles

</button>


<button 
class="btn btn-danger btn-sm"
data-toggle="collapse"
data-target="#collections{{$index}}{{$b}}{{$c}}">

<i class="fa fa-list"></i>
Collections

</button>


</td>

</tr>

<tr id="loans{{$index}}{{$b}}{{$c}}" class="collapse">

<td colspan="5">


<div class="box box-success">


<div class="box-body table-responsive">


<table class="table table-bordered table-striped">


<thead>

<tr>

<th>ID</th>
<th>
    Referrer Name
</th>
<th>Client</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
<th>Due Date</th>
<th>Days in Default </th>

</tr>

</thead>


<tbody>


@foreach($consultant['loans_list'] as $loan)


<tr>

<td>
{{ $loan['id'] }}
</td>


<td>
{{ $loan['referrer_name'] }}
</td>


<td>
{{ $loan['client_id'] }}
</td>


<td>
K {{ number_format($loan['principal'] ?? 0,2) }}
</td>


<td>
{{ $loan['status'] }}
</td>


<td>
{{ $loan['created_at'] }}
</td>

<td>
    @if(!empty($loan['due_date']))
        {{ \Carbon\Carbon::parse($loan['due_date'])->format('d M Y') }}
    @endif
</td>

<td>
   {{$loan['days_in_default']}}
</td>


</tr>


@endforeach


</tbody>


</table>


</div>


</div>


</td>


</tr>


<tr id="vehicles{{$index}}{{$b}}{{$c}}" class="collapse">

<td colspan="5">


<div class="box box-warning">


<div class="box-body table-responsive">


<table class="table table-bordered">


<thead>

<tr>

<th>Image</th>

<th>Registration</th>

<th>Model</th>

<th>Market Value</th>

<th>Loan ID</th>

</tr>

</thead>


<tbody>


@foreach($consultant['vehicles_list'] as $vehicle)


<tr>


<td>

@if(!empty($vehicle['photos']) && count($vehicle['photos']) > 0)

<img src="{{ $vehicle['photos'][0]['photo_url'] }}"

class="vehicle-photo-thumb"

data-photos='@json(collect($vehicle['photos'])->pluck("photo_url"))'

style="height: 50px; width: auto; object-fit: cover; border-radius: 4px; cursor: pointer;"

alt="Vehicle photo">

@else

<span class="text-muted">No photo</span>

@endif

</td>


<td>

{{ $vehicle['registration_number'] ?? '' }}

</td>


<td>

{{ $vehicle['model'] ?? '' }}

</td>


<td>

K {{ number_format($vehicle['market_value'] ?? 0,2) }}

</td>


<td>

{{ $vehicle['loan_id'] }}

</td>


</tr>


@endforeach


</tbody>


</table>


</div>


</div>


</td>

</tr>


<tr id="collections{{$index}}{{$b}}{{$c}}" class="collapse">

<td colspan="5">


<table class="table table-bordered table-striped">


<thead>

<tr>

<th>Date</th>
<th>Loan ID</th>
<th>Type</th>
<th>Applied To</th>
<th>Amount</th>

</tr>

</thead>


<tbody>


@foreach($consultant['collections_list'] as $transaction)


<tr>

<td>
{{ $transaction['date'] }}
</td>


<td>
{{ $transaction['loan_id'] }}
</td>


<td>
{{ $transaction['transaction_type'] }}
</td>


<td>
{{ $transaction['payment_apply_to'] }}
</td>


<td>
K {{ number_format($transaction['credit'] ?? 0,2) }}
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






<!-- ================= NATIONAL LOAN LIST ================= -->


<!-- ================= VEHICLES ================= -->




</section>




<div class="modal fade" id="vehiclePhotoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="width: auto; max-width: 90%;">
        <div class="modal-content" style="background: transparent; box-shadow: none; border: none;">
            <div class="modal-body" style="padding: 0; position: relative;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: -30px; right: 0; color: #fff; font-size: 30px; z-index: 10;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <img id="vehicleModalImage" src="" alt="Vehicle photo" style="width: 100%; max-height: 75vh; object-fit: contain; display: block; margin: 0 auto; border-radius: 8px;">
                <button type="button" class="btn btn-default btn-lg" id="vehicleModalPrev" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); opacity: 0.8;">
                    <i class="fa fa-chevron-left"></i>
                </button>
                <button type="button" class="btn btn-default btn-lg" id="vehicleModalNext" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); opacity: 0.8;">
                    <i class="fa fa-chevron-right"></i>
                </button>
                <div id="vehicleModalThumbs" style="display: flex; justify-content: center; gap: 8px; margin-top: 12px; overflow-x: auto; padding: 8px 0;"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    const modal = document.getElementById("vehiclePhotoModal");
    const modalImg = document.getElementById("vehicleModalImage");
    const prevBtn = document.getElementById("vehicleModalPrev");
    const nextBtn = document.getElementById("vehicleModalNext");
    const thumbsContainer = document.getElementById("vehicleModalThumbs");
    let photos = [];
    let currentIndex = 0;

    function updateImage(index) {
        if (!photos.length) return;
        currentIndex = (index + photos.length) % photos.length;
        modalImg.style.transition = "opacity 0.25s ease";
        modalImg.style.opacity = "0";
        setTimeout(() => {
            modalImg.src = photos[currentIndex];
            modalImg.onload = () => {
                modalImg.style.opacity = "1";
            };
        }, 250);
        updateThumbs();
    }

    function updateThumbs() {
        thumbsContainer.innerHTML = "";
        photos.forEach((url, idx) => {
            const thumb = document.createElement("img");
            thumb.src = url;
            thumb.style.height = "50px";
            thumb.style.width = "auto";
            thumb.style.objectFit = "cover";
            thumb.style.borderRadius = "4px";
            thumb.style.cursor = "pointer";
            thumb.style.opacity = idx === currentIndex ? "1" : "0.5";
            thumb.style.transition = "opacity 0.2s";
            thumb.onclick = () => updateImage(idx);
            thumbsContainer.appendChild(thumb);
        });
    }

    prevBtn.onclick = () => updateImage(currentIndex - 1);
    nextBtn.onclick = () => updateImage(currentIndex + 1);

    document.querySelectorAll(".vehicle-photo-thumb").forEach(img => {
        img.addEventListener("click", function() {
            try {
                photos = JSON.parse(this.getAttribute("data-photos") || "[]");
            } catch (e) {
                photos = [];
            }
            if (!photos.length) return;
            currentIndex = 0;
            updateImage(0);
            $(modal).modal("show");
        });
    });

    document.addEventListener("keydown", function(e) {
        if (!$(modal).data("bs.modal")?.isShown) return;
        if (e.key === "ArrowLeft") updateImage(currentIndex - 1);
        if (e.key === "ArrowRight") updateImage(currentIndex + 1);
    });
})();
</script>

@endsection

<!-- MVL Records Bottom Sheet -->
<div class="bottom-sheet-overlay" id="mvlRecordsOverlay" style="display: none;">
    <div class="bottom-sheet" id="mvlRecordsSheet" style="max-height: 85vh; display: none;">
        <button class="bottom-sheet-close" id="closeMvlSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title" id="mvlSheetTitle">Records</h3>
            <div id="mvlShimmerContainer">
                <div class="shimmer-row">
                    <div class="shimmer-cell" style="width: 80px;"></div>
                    <div class="shimmer-cell"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                    <div class="shimmer-cell" style="width: 100px;"></div>
                    <div class="shimmer-cell" style="width: 70px;"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                </div>
                <div class="shimmer-row">
                    <div class="shimmer-cell" style="width: 80px;"></div>
                    <div class="shimmer-cell"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                    <div class="shimmer-cell" style="width: 100px;"></div>
                    <div class="shimmer-cell" style="width: 70px;"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                </div>
                <div class="shimmer-row">
                    <div class="shimmer-cell" style="width: 80px;"></div>
                    <div class="shimmer-cell"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                    <div class="shimmer-cell" style="width: 100px;"></div>
                    <div class="shimmer-cell" style="width: 70px;"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                </div>
                <div class="shimmer-row">
                    <div class="shimmer-cell" style="width: 80px;"></div>
                    <div class="shimmer-cell"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                    <div class="shimmer-cell" style="width: 100px;"></div>
                    <div class="shimmer-cell" style="width: 70px;"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                </div>
                <div class="shimmer-row">
                    <div class="shimmer-cell" style="width: 80px;"></div>
                    <div class="shimmer-cell"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                    <div class="shimmer-cell" style="width: 100px;"></div>
                    <div class="shimmer-cell" style="width: 70px;"></div>
                    <div class="shimmer-cell" style="width: 120px;"></div>
                </div>
            </div>
            <table class="table table-bordered table-striped" id="mvlRecordsTable" style="display:none;">
                <thead id="mvlRecordsHead">
                </thead>
                <tbody id="mvlRecordsBody">
                </tbody>
            </table>
            <div id="mvlPagination" class="text-center" style="margin-top: 15px;"></div>
            <p id="mvlNoRecords" class="text-center" style="display:none; color: #888; margin-top: 20px;">No records found</p>
        </div>
    </div>
</div>

<style>
    .shimmer-row {
        display: flex;
        gap: 10px;
        margin-bottom: 10px;
    }
    .shimmer-cell {
        flex: 1;
        height: 18px;
        background: #e0e0e0;
        border-radius: 4px;
        animation: shimmer 1.5s infinite;
    }
    @keyframes shimmer {
        0% { opacity: 0.4; }
        50% { opacity: 1; }
        100% { opacity: 0.4; }
    }
</style>

@section('footer-scripts')
<script>
$(function() {
    $("[data-toggle='tooltip']").tooltip();

    const endpointTitles = {
        'loans': 'Motor Vehicle Loans',
        'vehicles': 'Vehicles',
        'portfolio': 'Total Portfolio Value',
        'collections': 'Total Collections',
        'defaulted': 'Motor Vehicle Loans in Default'
    };

    $(document).on('click', '.small-box[data-endpoint]', function() {
        var endpoint = $(this).data('endpoint');
        var title = endpointTitles[endpoint] || 'Records';
        openMvlSheet(endpoint, title);
    });

    function openMvlSheet(endpoint, title) {
        $('#mvlSheetTitle').text(title);
        $('#mvlRecordsTable').hide();
        $('#mvlNoRecords').hide();
        $('#mvlPagination').hide();
        $('#mvlShimmerContainer').show();
        $('#mvlRecordsBody').empty();
        $('#mvlRecordsHead').empty();

        var columns = ['Loan', 'Client', 'Loan Consultant', 'Registration', 'Balance'];
        if (endpoint === 'portfolio') columns.push('Portfolio');
        if (endpoint === 'collections') columns.push('Collected');
        columns.push('Due Date', 'Status');
        if (endpoint === 'defaulted') columns.push('Time in Default');
        columns.push('Actions');

        var thead = '<tr>';
        for (var i = 0; i < columns.length; i++) {
            thead += '<th>' + columns[i] + '</th>';
        }
        thead += '</tr>';
        $('#mvlRecordsHead').html(thead);

        var currentPage = 1;

        var cellBuilders = {
            'Loan': function(r) { return r.loan_id || r.id; },
            'Client': function(r) { return r.client_name || 'N/A'; },
            'Loan Consultant': function(r) { return r.loan_officer_name || 'N/A'; },
            'Registration': function(r) { return r.registration_number || 'N/A'; },
            'Balance': function(ep, r) {
                return (r.balance || 0).toLocaleString();
            },
            'Portfolio': function(r) { return (r.debit || 0).toLocaleString(); },
            'Collected': function(r) { return (r.credit || 0).toLocaleString(); },
            'Due Date': function(r) { return r.due_date ? moment(r.due_date).fromNow() : 'N/A'; },
            'Status': function(r) { return r.status || 'N/A'; },
            'Time in Default': function(r) { return r.due_date ? moment(r.due_date).fromNow() : 'N/A'; },
            'Actions': function(r) {
                return '<a href="/motor-vehicle-loans/' + r.loan_id + '" class="btn btn-xs btn-primary" target="_blank">Show Loan</a> <a href="/vehicles/' + (r.vehicle_id || r.id) + '" class="btn btn-xs btn-info" target="_blank">Show Vehicle</a>';
            }
        };

        function fetchRecords(page) {
            $.ajax({
                url: '/api/mvl/' + endpoint,
                type: 'GET',
                data: { page: page },
                success: function(response) {
                    $('#mvlShimmerContainer').hide();

                    if (response.success && response.records.length > 0) {
                        $('#mvlRecordsTable').show();
                        var tbody = $('#mvlRecordsBody');
                        tbody.empty();

                        $.each(response.records, function(i, record) {
                            var row = '<tr>';
                            for (var j = 0; j < columns.length; j++) {
                                var col = columns[j];
                                if (col === 'Balance') {
                                    row += '<td>' + cellBuilders[col](endpoint, record) + '</td>';
                                } else {
                                    row += '<td>' + cellBuilders[col](record) + '</td>';
                                }
                            }
                            row += '</tr>';
                            tbody.append(row);
                        });

                        var pagination = response.pagination;
                        if (pagination.last_page > 1) {
                            var pager = '';
                            for (var i = 1; i <= pagination.last_page; i++) {
                                pager += '<button class="btn btn-sm ' + (i === pagination.current_page ? 'btn-primary' : 'btn-default') + '" data-page="' + i + '" style="margin: 2px;">' + i + '</button>';
                            }
                            $('#mvlPagination').html(pager).show();
                        } else {
                            $('#mvlPagination').hide();
                        }
                    } else {
                        $('#mvlNoRecords').show();
                        $('#mvlPagination').hide();
                    }
                },
                error: function() {
                    $('#mvlShimmerContainer').hide();
                    $('#mvlNoRecords').text('Failed to load records. Please try again.').show();
                }
            });
        }

        fetchRecords(1);

        $(document).off('click', '#mvlPagination button[data-page]');
        $(document).on('click', '#mvlPagination button[data-page]', function() {
            var page = $(this).data('page');
            fetchRecords(page);
        });

        $('#mvlRecordsOverlay').css('display', 'flex').addClass('active');
        $('#mvlRecordsSheet').css('display', 'block').addClass('active');
        document.body.style.overflow = 'hidden';
    }

    $('#closeMvlSheet').on('click', function() {
        $('#mvlRecordsOverlay').css('display', 'none').removeClass('active');
        $('#mvlRecordsSheet').css('display', 'none').removeClass('active');
        document.body.style.overflow = '';
    });

    $('#mvlRecordsOverlay').on('click', function(e) {
        if (e.target === this) {
            $('#mvlRecordsOverlay').css('display', 'none').removeClass('active');
            $('#mvlRecordsSheet').css('display', 'none').removeClass('active');
            document.body.style.overflow = '';
        }
    });
});
</script>
@endsection
