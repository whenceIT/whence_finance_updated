@extends('layouts.master')
@section('title') Reports @endsection
@section('content')
<section class="content">
<div class="box"><div class="box-body">
<p>Total cases: {{ $total }}</p>
<p>Total charges raised: K{{ number_format($raised,2) }}</p>
<p>Total paid: K{{ number_format($paid,2) }}</p>
<p>Outstanding: K{{ number_format($outstanding,2) }}</p>
<table class="table table-bordered">
<tr><th>Branch</th><th>Raised</th><th>Paid</th></tr>
@foreach($byBranch as $b)
<tr><td>{{ optional(\App\Models\Office::find($b->branch_id))->name }}</td><td>{{ number_format($b->raised,2) }}</td><td>{{ number_format($b->paid,2) }}</td></tr>
@endforeach
</table>
</div></div>
</section>
@endsection

