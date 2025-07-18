@extends('layouts.auth')
@section('title')
Appraisal Forms
@endsection
@section('content')
<div>
@foreach($forms as $form)
@if($form != null)
    <a href="{{url('user/'.$form->id.'/my_appraisal')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$form->form_name}}</span>
</div>
</div>
</div>
</a>
@endif
    @endforeach
</div>
@endsection
@section('footer-scripts')
@endsection
