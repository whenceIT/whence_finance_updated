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
    <p style="font-weight:bold">Payroll Loan Application Form</p>
    </div>
</div>



<div  class="main">
  <div class="infobox">
 

  <form id="regForm" action="{{url('create_payroll_loan_application')}}">
  <!-- One "tab" for each step in the form: -->
  <div class="tab" style="display: none; ">
    <p style="text-decoration: underline; text-align:center">Personal information</p>
    <label for="fname">First Name</label>
    <input type="text" name="client_name" 
                                   required id="client_name" placeholder="client name">

                                  
                          
                                   <label for="fname">NRC</label>
<input type="text" name="nrc" 
                                   required id="nrc" placeholder="nrc number">
                                   <label for="fname">Date of Birth</label>
<input type="date" name="dob" 
                               placeholder="DOB"
                               id="dob" >

                               <label for="fname">Gender</label>
<select name="gender"  id="gender" >
                                <option></option>
                                <option value="male">{{trans('general.male')}}</option>
                                <option value="female">{{trans('general.female')}}</option>
                                <option value="other">{{trans('general.other')}}</option>
                            </select>

                            <label for="fname">Email</label>
<input type="email" name="email"
                               id="email" placeholder="email">

                               <label for="fname">Phone</label>
<input type="text" name="phone"  id="phone" placeholder="phone">


<label for="fname">Home Address</label>
<input type="text" name="address"  id="address" placeholder="home address">
  </div>


  <div class="tab" style="display: none;">
    <p style="text-decoration: underline; text-align:center">Employment information</p>
    <label for="fname">Employer Name</label>
    <input type="text" name="employer_name" 
                                   required id="employer_name" placeholder="employer name">

<label for="fname">Man Number</label>
<input type="text" name="man_number" 
                                   required id="man_number" placeholder="man number">

                                   <label for="fname">Job Position</label>                                   
<input type="text" name="position" 
                                   required id="position" placeholder="job position">

<label for="fname">Length of Service</label>                                   
<select name="length_of_service" id="length_of_service" >
                                <option></option>
                                <option value="less than 1 year">< 1 year</option>
                                <option value="1-3 years">1-3 years</option>
                                <option value="3-5 years">3-5 years</option>
                                <option value="greater than 5 years">> 5 years</option>
                            </select>

                            <label for="fname">Monthly Service</label>
<input type="text" name="monthly_service" 
                            required id="monthly_service" placeholder="monthly service">
                            <label for="fname">Work Address</label>                          
<input type="text" name="work_address" 
                            required id="work_address" placeholder="work address">

                            <label for="fname">Work Phone Number</label>
<input type="text" name="work_phone_number" 
                            required id="work_phone_number" placeholder="work phone number">
  </div>



  <div class="tab" style="display: none;">
    <p style="text-decoration: underline; text-align:center">Loan Details</p>
    <label for="fname">Loan Amount</label>
<input type="text" name="loan_amount" 
                                   required id="loan_amount" placeholder="loan amount">
                                   <label for="fname">Loan Term</label>
<select name="loan_term" id="loan_term" >
                                <option></option>
                                <option value="9 months">9 months</option>
                                <option value="12 months">12 months</option>
                                <option value="18 months">18 months</option>
                                <option value="24 months">24 months</option>
                            </select>
                            <label for="fname">Loan Purpose</label>
<input type="text" name="loan_purpose" 
                                   required id="loan_purpose" placeholder="loan purpose">
  </div>


  <div class="tab" style="display: none;">
  <p style="text-decoration: underline; text-align:center">Banking information</p>
  <label for="fname">Bank Name</label>
    <input type="text" name="bank_name" 
                                   required id="bank_name" placeholder="Bank name" >
                                   <label for="fname">Account Number</label>
<input type="text" name="ac_number" 
                                   required id="ac_number" placeholder="Bank account Number" >
                                   <label for="fname">Bank Short Code</label>
<input type="text" name="short_code" 
                                   required id="short_code" placeholder="Bank short code" >
                                   <label for="fname">Branch Name</label>
<input type="text" name="branch_name" 
                                   required id="branch_name" placeholder="Branch Name" >
                                   <label for="fname">Branch Code</label>
<input type="text" name="branch_code" 
                                   required id="branch_code" placeholder="Branch Code">
  </div>

  
<div class="tab" style="display: none;">
<p style="text-decoration: underline; text-align:center">Upload documents</p>
<label for="fname">NRC</label>
<input type="file" name="nrc_file" required
id="nrc_file">

<label for="fname">Payslip</label>
<input type="file" name="payslip" required
id="payslip">

<label for="fname">Bank Statement</label>
<input type="file" name="bank_statement" required
id="bank_statement">

</div>

  <div style="overflow:auto;">
    <div style="float:right;">
      <button type="button" id="prevBtn" class="button" onclick="nextPrev(-1)">Previous</button>
      <button type="button" id="nextBtn" class="button" onclick="nextPrev(1)">Next</button>
      <button type="submit" class="submitButton" id="submitBtn">Submit</button>
    </div>
  </div>
  <!-- Circles which indicates the steps of the form: -->
  <div style="text-align:center;margin-top:40px;">
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
    <span class="step"></span>
  </div>


</form>

  </div>







</div>

</div>

@endsection
@section('footer-scripts')

<style>

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
    document.getElementById("nextBtn").style.display = "none";
  } else {
    document.getElementById("nextBtn").innerHTML = "Next";
    document.getElementById("submitBtn").style.display = "none";
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
