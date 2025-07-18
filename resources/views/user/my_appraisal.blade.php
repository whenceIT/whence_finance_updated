@extends('layouts.auth')
@section('title')
    My Appraisal
@endsection

@section('content')
<?php
$year = date('Y');
$month = date('m');
$todaysDate = $month % 3;
?>
<div>
<div class="header">
    <div class="headerContainer">
    <img src="{{asset('images/icons/icon-72x72.png') }}"/>
    <h3>Whence Financial Services</h3>
    <p style="font-weight: bold; font-size: 16px; text-decoration: underline;">{{$form->form_name}}<p>
    </div>
</div>

@if(session('message'))
<div class="main">
<div class="alert alert-success">
<i class="fa fa-check-circle-o" aria-hidden="true"></i>
  Your Appraisal has been successfully submitted</div>
</div>
@endif


<div class="main">

    <div class="infobox">

  
  <form id="regForm" method="post"  action="{{url('user/'.$form->id.'/submit_appraisal')}}"
  enctype="multipart/form-data">
  {{csrf_field()}}
   @foreach($sections as $section)
<div class="tab" style="display: none; ">
<h3>{{$section->section_name}}</h3>

@foreach($questions as $question)
@if($question->section_id == $section->id)
<label for="fname">{{$question->question}} | 
  @if($question->unit == '[1-5]')
  Provide ratings on a scale of 1 (Poor) to 5 (Excellent)
  @elseif($question->unit == '[I,S,D]')
  ☐ Improving ☐ Stable ☐ Declining
  @else
  {{$question->unit}}
  @endif
</label>
@if($question->unit == 'p_r')
@foreach($peers as $peer)
<div class="form-group" id="">
<?php

$key = array_search('{{$peer}}', $peers)
?>
<p>{{$peer->first_name}} {{$peer->last_name}}</p>
<select name="{{$peer->id}}{{$question->id}}" id="{{$peer->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach

@elseif($question->unit == 'sb_r')
@foreach($managers as $manager)
<div class="form-group" id="">
<?php
$key = array_search('{{$manager}}', $managers)
?>
<p>{{$manager->first_name}} {{$manager->last_name}}</p>
<select name="{{$manager->id}}{{$question->id}}" id="{{$manager->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach


@elseif($question->unit == 'rr_r')
@foreach($recoveries_reps as $manager)
<div class="form-group" id="">
<?php
$key = array_search('{{$manager}}', $recoveries_reps)
?>
<p>{{$manager->first_name}} {{$manager->last_name}}</p>
<select name="{{$manager->id}}{{$question->id}}" id="{{$manager->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach

@elseif($question->unit == 'rh_r')
@foreach($recoveries_head as $manager)
<div class="form-group" id="">
<?php
$key = array_search('{{$manager}}', $recoveries_head)
?>
<p>{{$manager->first_name}} {{$manager->last_name}}</p>
<select name="{{$manager->id}}{{$question->id}}" id="{{$manager->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach


@elseif($question->unit == 'ma_r')
@foreach($manager_admin as $manager)
<div class="form-group" id="">
<?php
$key = array_search('{{$manager}}', $manager_admin)
?>
<p>{{$manager->first_name}} {{$manager->last_name}}</p>
<select name="{{$manager->id}}{{$question->id}}" id="{{$manager->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach

@elseif($question->unit == 'p_r_dm')
@foreach($dm_peers as $manager)
<div class="form-group" id="">
<?php
$key = array_search('{{$manager}}', $dm_peers)
?>
<p>{{$manager->first_name}} {{$manager->last_name}}</p>
<select name="{{$manager->id}}{{$question->id}}" id="{{$manager->id}}{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
</div>
@endforeach

@elseif($question->unit == '[1-5]')
<select name="{{$question->id}}"  id="{{$question->id}}"
                            required>
                        <option></option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>

@elseif($question->unit == '[I,S,D]')
<select name="{{$question->id}}"  id="{{$question->id}}"
                            required>
                        <option></option>
                        <option value="Improving">Improving</option>
                        <option value="Stable">Stable</option>
                        <option value="Declining">Declining</option>
                    </select>  
                    
                    @elseif($question->unit == 'subop1')
                    <select name="{{$question->id}}"  id="{{$question->id}}"
                          >
                        <option></option>
                        <option value="Low customer demand">Low customer demand</option>
                        <option value="Loan Consultant underperformance">Loan Consultant underperformance</option>
                        <option value="Approval delays">Approval delays</option>
                        <option value="Other">Other</option>
                    </select>  
                    <label>Other (Specify)</label>    
                    <input type="text" name="{{$question->id}}other" 
id="{{$question->id}}other" placeholder="Enter (N/A) if not other">
@elseif($question->unit == 'subop2')
<select name="{{$question->id}}"  id="{{$question->id}}"
                          >
                        <option></option>
                        <option value="High default rate">High default rate</option>
                        <option value="Ineffective follow-ups">Ineffective follow-ups</option>
                        <option value="Failure to dispose-off seized collateral">Failure to dispose-off seized collateral</option>
                        <option value="Other">Other</option>
                    </select>  
                    <label>Other (Specify)</label>    
                    <input type="text" name="{{$question->id}}other" 
id="{{$question->id}}other" placeholder="Enter (N/A) if not other">

@elseif($question->unit == 'subop3')
<select name="{{$question->id}}"  id="{{$question->id}}"
                          >
                        <option></option>
                        <option value="Market challenges">Market challenges</option>
                        <option value="Poor branch management">Poor branch management</option>
                        <option value=" System inefficiencies"> System inefficiencies</option>
                        <option value="Other">Other</option>
                    </select>  
                    <label>Other (Specify)</label>    
                    <input type="text" name="{{$question->id}}other" 

id="{{$question->id}}other" placeholder="Enter (N/A) if not other">


@elseif($question->unit == 'subop4')
<select name="{{$question->id}}"  id="{{$question->id}}"
                          >
                        <option></option>
                        <option value="Inadequate follow-ups">Inadequate follow-ups</option>
                        <option value="Poor branch management">Poor branch management</option>
                        <option value=" System inefficiencies"> System inefficiencies</option>
                        <option value="Legal constraints">Legal constraints</option>
                        <option value="Other">Other</option>
                    </select>  
                    <label>Other (Specify)</label>    
                    <input type="text" name="{{$question->id}}other" 

id="{{$question->id}}other" placeholder="Enter (N/A) if not other">

@elseif($question->unit == 'yes/no')
<select name="{{$question->id}}"  id="{{$question->id}}"
                          >
                        <option></option>
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>  
@elseif($question->unit == 'info')    
<p>{{$question->question}}</p>
@else
<input type="text" name="{{$question->id}}" 
required id="{{$question->id}}" placeholder="{{$question->unit}}">
@endif

@endif

@endforeach
</div>
   @endforeach
   <div>
   <p style="font-weight: bold;" id="acknowledgeMsg">By clicking submit you acknowledge that you have reviewed you performance appraisal and understand the feedback provided.</p>
   </div>
   <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" id="prevBtn" class="button" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" class="button" onclick="nextPrev(1)">Next</button>
      <button type="submit" class="submitButton" id="submitBtn">Submit</button>
    </div>
  </div>

  <div style="text-align:center;margin-top:40px;">
  @foreach($sections as $section)
  <span class="step"></span>
  @endforeach
  </div>


  </form>



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
    border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}

input[type=text], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
input[type=email], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=date], select {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}


.button{
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

input[type="file"]::file-selector-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }


.submitButton{
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
<script>
  
var currentTab = 0; 
showTab(currentTab);



function showTab(n) {
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "block";
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
    document.getElementById("nextBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("submitBtn").style.display = "inline";
    document.getElementById("acknowledgeMsg").style.display = "inline";
    document.getElementById("nextBtn").style.display = "none";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
    document.getElementById("submitBtn").style.display = "none";
    document.getElementById("acknowledgeMsg").style.display = "none";
  }
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  z = x[currentTab].getElementsByTagName('select')
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (y[i].value == "") {
      // add an "invalid" class to the field:
      y[i].className += " invalid";
      // and set the current valid status to false
      valid = false;
    }
  }


  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class to the current step:
  x[n].className += " active";
}
</script>

@endsection
