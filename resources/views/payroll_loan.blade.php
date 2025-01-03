@extends('layouts.auth')
@section('title')
    Payroll Loan
    @endsection



@section('content')
<div>

<div class="header">
    <div class="headerContainer">
    <img src="{{asset('images/icons/icon-72x72.png') }}"/>
    <h3>Whence Financial Services</h3>
    </div>
</div>

@if(session('message'))
<div class="main">
<div class="alert alert-success">
<i class="fa fa-check-circle-o" aria-hidden="true"></i>
  You application has been sent successfully</div>
</div>
@endif

<div class="main">
     <div class="infobox">
    <!-- <p style="font-weight: bold;">Payroll Loan Application Form</p>
   <p style="text-decoration: underline;">Requirements</p>
   <ul class="a">
  <li>NRC</li>
  <li>Payslip</li>
  <li>Must be a <span style="font-weight: bold; text-decoration: underline;">civil servant</span> under PMEC</li>
  </ul> -->
  
  <!-- <a class="startButton" href="{{url('payroll_loan/create-step-one')}}">Start Application</a> -->
  <p style="font-weight: bold;">Sorry! We are temporarily closed for maintenance</p>

</div>

</div>


@endsection
@section('footer-scripts')
<style>
    ul.a {
  list-style-type: circle;
}

    .header {
  overflow: hidden;
  padding: 10px 10px;

}
.headerContainer{
    flex-direction: column;
    display: flex;
    justify-content: center;
    align-items: center;
    

}

.main{
    display: flex;
    justify-content: center;
    align-items: center;
}

.infobox{
    flex-direction: column;
    align-items: center;
    display: flex;
    justify-content: center;
    border-width: thin;
    width: 80%;
    border-style: solid;
    padding: 20px;
    background-color: #f2f2f2;
}

.startButton{
    background-color: #04AA6D; /* Green */
  border: none;
  color: white;
  padding: 15px 32px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 4px 2px;
  cursor: pointer;

}

</style>
@endsection
