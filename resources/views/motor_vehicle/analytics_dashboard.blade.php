@extends('layouts.master')


@section('content')


<section class="content-header">

<h1>
<i class="fa fa-car"></i>
Motor Vehicle Loan Analytics

<small>
Portfolio Risk Dashboard
</small>

</h1>

</section>




<section class="content">



<!-- FILTER -->

<div class="box box-primary">

<div class="box-header with-border">

<h3 class="box-title">
<i class="fa fa-filter"></i>
Dashboard Filters
</h3>

</div>


<div class="box-body">


<form method="GET" class="form-inline">


<div class="form-group">

<label>
From
</label>

<input 
class="form-control"
type="date"
name="start_date"
value="{{$start}}">

</div>



&nbsp;


<div class="form-group">

<label>
To
</label>


<input 
class="form-control"
type="date"
name="end_date"
value="{{$end}}">


</div>



&nbsp;


<button class="btn btn-primary">

<i class="fa fa-search"></i>
Generate

</button>



</form>


</div>

</div>






<!-- KPI CARDS -->

<div class="row">


<div class="col-lg-3 col-xs-6">

<div class="small-box bg-aqua">

<div class="inner">

<h3>
{{number_format($data['summary']['total_loans'])}}
</h3>


<p>
Motor Vehicle Loans
</p>

</div>


<div class="icon">

<i class="fa fa-car"></i>

</div>


</div>

</div>





<div class="col-lg-3 col-xs-6">

<div class="small-box bg-green">

<div class="inner">


<h3>

K {{number_format($data['summary']['collections'])}}

</h3>


<p>
Collections Received
</p>


</div>


<div class="icon">

<i class="fa fa-money"></i>

</div>


</div>


</div>







<div class="col-lg-3 col-xs-6">


<div class="small-box bg-red">


<div class="inner">


<h3>

{{$data['summary']['default_loans']}}

</h3>


<p>
Loans In Default
</p>


</div>


<div class="icon">

<i class="fa fa-warning"></i>

</div>


</div>


</div>






<div class="col-lg-3 col-xs-6">


<div class="small-box bg-yellow">


<div class="inner">


<h3>

{{$data['summary']['default_rate']}}%

</h3>


<p>
Default Rate

</p>


</div>


<div class="icon">

<i class="fa fa-line-chart"></i>


</div>


</div>


</div>



</div>









<!-- PORTFOLIO HEALTH -->


<div class="box box-info">


<div class="box-header">

<h3 class="box-title">

<i class="fa fa-dashboard"></i>

Portfolio Health

</h3>


</div>


<div class="box-body">



<div class="progress-group">


<span class="progress-text">

Collection Performance

</span>


<span class="progress-number">

<b>
{{$data['summary']['collections']}}
</b>
/
{{$data['summary']['expected_collections']}}

</span>



<div class="progress sm">


<div 
class="progress-bar bg-green"

style="width:
{{($data['summary']['collections'] /
$data['summary']['expected_collections'])*100}}%">

</div>


</div>


</div>




<div class="progress-group">


<span class="progress-text">

Default Risk

</span>


<span class="progress-number">

<b>
{{$data['summary']['default_rate']}}%
</b>

</span>


<div class="progress sm">


<div 

class="progress-bar bg-red"

style="width:
{{$data['summary']['default_rate']}}%">

</div>


</div>


</div>



</div>


</div>







<!-- RISK TABLES -->

<div class="row">



<div class="col-md-6">


<div class="box box-danger">


<div class="box-header">


<h3 class="box-title">

<i class="fa fa-users"></i>

Highest Default Consultants

</h3>


</div>


<div class="box-body table-responsive">


<table class="table table-hover">


<tr>

<th>
Consultant
</th>


<th>
Defaults
</th>


<th>
Risk
</th>

</tr>



@foreach($data['default_by_consultant'] as $row)


<tr>


<td>
{{$row['name']}}
</td>


<td>

{{$row['defaults']}}

</td>



<td>


@if($row['default_rate'] > 30)

<span class="label label-danger">

{{$row['default_rate']}}%

</span>


@elseif($row['default_rate'] > 15)


<span class="label label-warning">

{{$row['default_rate']}}%

</span>


@else


<span class="label label-success">

{{$row['default_rate']}}%

</span>


@endif


</td>


</tr>


@endforeach


</table>


</div>


</div>


</div>








<div class="col-md-6">


<div class="box box-warning">


<div class="box-header">

<h3 class="box-title">

<i class="fa fa-car"></i>

Vehicle Default Risk

</h3>


</div>



<div class="box-body table-responsive">


<table class="table table-hover">


<tr>


<th>
Vehicle
</th>


<th>
Defaults
</th>


<th>
Rate
</th>


</tr>



@foreach($data['default_by_vehicle'] as $row)



<tr>


<td>

{{$row['name']}}

</td>



<td>

{{$row['defaults']}}

</td>



<td>


<span class="label label-danger">

{{$row['default_rate']}}%

</span>


</td>


</tr>


@endforeach


</table>



</div>


</div>



</div>


</div>









<!-- HIGH VALUE DEFAULTS -->


<div class="box box-danger">


<div class="box-header">


<h3 class="box-title">


<i class="fa fa-exclamation-triangle"></i>

Highest Risk Loans Requiring Attention


</h3>


</div>



<div class="box-body table-responsive">


<table class="table table-bordered table-striped">


<thead>

<tr>

<th>
Loan ID
</th>


<th>
Consultant
</th>


<th>
Branch
</th>


<th>
Outstanding
</th>


<th>
Days Default
</th>


<th>
Risk Level
</th>


</tr>

</thead>



<tbody>



@foreach($data['high_risk_loans'] as $loan)


<tr>


<td>
{{$loan['id']}}
</td>


<td>
{{$loan['consultant_name']}}
</td>


<td>
{{$loan['branch_name'] ?? '-'}}
</td>



<td>

K {{number_format($loan['balance'])}}

</td>



<td>

{{$loan['days_in_default']}}

</td>



<td>


@if($loan['days_in_default'] > 90)

<span class="label label-danger">
Critical
</span>


@elseif($loan['days_in_default'] > 30)

<span class="label label-warning">
High
</span>


@else

<span class="label label-info">
Watch
</span>


@endif


</td>


</tr>


@endforeach



</tbody>


</table>



</div>



</div>






</section>


@endsection