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

<div class="small-box bg-aqua">

<div class="inner">

<h3>
{{ number_format($data['national']['number_of_loans']) }}
</h3>

<p>
Motor Vehicle Loans
</p>

</div>

<div class="icon">
<i class="fa fa-money"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-green">

<div class="inner">

<h3>
{{ number_format($data['national']['number_of_vehicles']) }}
</h3>

<p>
Vehicles
</p>

</div>


<div class="icon">
<i class="fa fa-car"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-yellow">

<div class="inner">

<h3>
K {{ number_format($data['national']['total_vehicle_value'],2) }}
</h3>


<p>
Vehicle Portfolio Value
</p>

</div>


<div class="icon">
<i class="fa fa-bar-chart"></i>
</div>

</div>

</div>



<div class="col-lg-3 col-xs-6">

<div class="small-box bg-red">

<div class="inner">

<h3>
K {{ number_format($data['national']['total_collections'],2) }}
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








<!-- ================= PROVINCES ================= -->

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
 {{$loan['due_date']}} 
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


@endsection