@extends('layouts.master')
@php
use Illuminate\Support\Str;
@endphp
@section('title')
Client App Dashboard
@endsection

@section('content')

<section class="content-header">
    <h1>
        Client App Dashboard
        <small>Client Mobile Application Analytics</small>
    </h1>
</section>

<section class="content">

<div class="row">

    <!-- Row 1 -->

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3>{{ number_format($client_app_users->count()) }}</h3>
                <p>Total Users</p>
            </div>
            <div class="icon">
                <i class="fa fa-users"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-green">
            <div class="inner">
                <h3>{{ number_format($client_app_loan_applications->count()) }}</h3>
                <p>Loan Applications</p>
            </div>
            <div class="icon">
                <i class="fa fa-file-text-o"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-yellow">
            <div class="inner">
               <h3>K{{ number_format($client_app_loan_applications->sum('amount'), 2) }}</h3>
                <p>Money Applied For</p>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>

</div>

<div class="row">

    <!-- Row 2 -->

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-red">
            <div class="inner">
             <h3>
    K{{ number_format($client_app_loan_applications->where('status', 'approved')->sum('amount'), 2) }}
</h3>
                <p>Total Disbursed</p>
            </div>
            <div class="icon">
                <i class="fa fa-bank"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-purple">
            <div class="inner">
                <h3>K{{ number_format($summary['payments_received'],2) }}</h3>
                <p>Payments Received</p>
            </div>
            <div class="icon">
                <i class="fa fa-credit-card"></i>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-sm-6">
        <div class="small-box bg-navy">
            <div class="inner">
                <h3>K{{ number_format($summary['fees'],2) }}</h3>
                <p>Transaction Fees</p>
            </div>
            <div class="icon">
                <i class="fa fa-exchange"></i>
            </div>
        </div>
    </div>

</div>


<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-mobile"></i>
            Client App Loan Applications Breakdown
        </h3>
    </div>

   


    <div class="box-body table-responsive no-padding">

        <table class="table table-bordered table-hover">

            <thead style="background:#f4f4f4;">
            <tr>
                <th width="50"></th>
                <th>Name</th>
                <th>Applications</th>
            </tr>
            </thead>

            <tbody>
@foreach($provinceClientCounts as $province)

<tr class="bg-light-blue-gradient">

    <td class="text-center">
        <button
            class="btn btn-xs btn-primary toggle-btn"
            data-target="#province-{{ $province['id'] }}">
            <i class="fa fa-plus"></i>
        </button>
    </td>

    <td>
        <strong>{{ $province['province'] }}</strong>
    </td>

    <td>
        <span class="badge bg-blue">
            {{ number_format($province['client_count']) }}
        </span>
    </td>

</tr>


<tr id="province-{{ $province['id'] }}" style="display:none;">

<td colspan="3">

<table class="table table-bordered table-striped">

<tbody>

@foreach($province['offices'] as $office)

<tr>

<td class="text-center">

<button
    class="btn btn-xs btn-warning toggle-btn"
    data-target="#branch-{{ $office['id'] }}">

    <i class="fa fa-plus"></i>

</button>

</td>


<td>
<strong>{{ $office['name'] }}</strong>
</td>


<td>
<span class="badge bg-green">
{{ number_format($office['client_count']) }}
</span>
</td>


</tr>


<tr id="branch-{{ $office['id'] }}" style="display:none;">

<td colspan="3">


<table class="table table-hover table-bordered">

<tbody>


@foreach($office['loan_consultants'] as $consultant)


<tr>

<td class="text-center">

<button
class="btn btn-xs btn-info toggle-btn"
data-target="#consultant-{{ $consultant['id'] }}">

<i class="fa fa-plus"></i>

</button>

</td>


<td>
<strong>{{ $consultant['name'] }}</strong>
</td>


<td>

<span class="badge bg-aqua">
{{ number_format($consultant['client_count']) }}
</span>

</td>


</tr>



<tr id="consultant-{{ $consultant['id'] }}" style="display:none;">

<td colspan="3">


<table class="table table-bordered table-condensed">

<thead>

<tr>
<th>ID</th>
<th>Amount</th>
<th>Status</th>
<th>Date</th>
</tr>

</thead>


<tbody>


@forelse($consultant['loan_applications'] as $application)


<tr>

<td>
{{ $application['id'] }}
</td>


<td>
{{ number_format($application['amount'],2) }}
</td>


<td>
<span class="label label-primary">
{{ ucfirst($application['status']) }}
</span>
</td>


<td>
{{ $application['created_at'] }}
</td>


</tr>


@empty

<tr>
<td colspan="4" class="text-center text-muted">
No applications.
</td>
</tr>

@endforelse


</tbody>


</table>


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


</td>

</tr>


@endforeach
            </tbody>

        </table>

    </div>
</div>

<script>

$(document).on('click','.toggle-btn',function(){

    var target=$(this).data('target');

    $(target).toggle();

    $(this).find('i').toggleClass('fa-plus fa-minus');

});

</script>


</section>

@endsection