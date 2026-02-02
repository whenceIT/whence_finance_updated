@extends('layouts.master')

@section('title', 'Thank You')

@section('content')
<div class="box box-success">
    <div class="box-header with-border">
        <h3 class="box-title">Thank You!</h3>
    </div>
    
    <div class="box-body text-center">
        <div style="font-size: 80px; color: #00a04a; margin-bottom: 20px;">
            <i class="fa fa-check-circle"></i>
        </div>
        
        <h2 style="margin-bottom: 20px;">Survey Submitted Successfully</h2>
        
        <p style="font-size: 18px; margin-bottom: 30px;">
            Thank you for taking the time to complete our survey. Your feedback is valuable to us and will help us improve our services.
        </p>
        
        <a href="{{ url('dashboard') }}" class="btn btn-primary btn-lg">
            <i class="fa fa-home"></i> Return to Dashboard
        </a>
    </div>
</div>
@endsection
