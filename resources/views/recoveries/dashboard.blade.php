@extends('layouts.master')

@section('title')
    Recovery Dashboard (Alt View)
@endsection

@section('content')

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-home"></i> Recovery Overview</h3>
    </div>
    <div class="box-body">
        <p>This is an alternative dashboard view. Go to <a href="{{ url('recovery/overview') }}">main recovery dashboard</a> for full analytical view.</p>
    </div>
</div>

@endsection
