@extends('layouts.master')
@section('title')
    System fail safe's
@endsection
@section('content')
<div>
<a href="{{ url('loan/adjust_next_repayment')}}" style="margin: 10px;">
   <span class="label label-primary" style="font-size: 15px;">Adjust repayment dates</span>
</a>
</div>
@endsection
