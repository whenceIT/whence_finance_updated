@extends('layouts.master')
@section('title')
  My Payroll Information
@endsection
@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"> Payroll Information</h3>

        <div class="box-tools pull-right">

        </div>
    </div>
    @if(empty($information))
    <form method="post" action="{{url('payroll/add_payroll_information')}}" class="form" enctype="multipart/form-data">
    {{csrf_field()}}
<div class="box-body">
    
    <div class="form-group">
            <label for="SSN"
                   class="">Social Security Number</label>
            <input type="text" name="SSN" class="form-control"
                   placeholder="Social Security Number"
                   value="{{old('SSN')}}"
                   required id="SSN">
    </div>




    <div class="form-group">
            <label for="name"
                   class="">TPIN</label>
            <input type="text" name="TPIN" class="form-control"
                   placeholder="TPIN"
                   value="{{old('TPIN')}}"
                   required id="TPIN">
    </div>



    <div class="form-group" id="">
        <label for="type"
               class="">Payment Type
        </label>
            <select name="payment_type" class="form-control " id="payment_type"
                    required>
                <option></option>
                <option value="mobile money">Mobile Money</option>
                <option value="bank">Bank</option>
            </select>
    </div>



    <div class="form-group">
            <label for="name"
                   class="">Bank Account/Mobile Money #</label>
            <input type="text" name="account_number" class="form-control"
                   placeholder="Account Number"
                   value="{{old('account_number')}}"
                   required id="account_number">
    </div>
</div>

<div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
</form>
    @else
  
    <form method="post" action="{{url('payroll/'.$information->id.'/edit_payroll_information')}}" class="form" enctype="multipart/form-data">
    {{csrf_field()}}
<div class="box-body">
    
    <div class="form-group">
            <label for="SSN"
                   class="">Social Security Number</label>
            <input type="text" name="SSN" class="form-control"
                   placeholder="Social Security Number"
                   value="{{$information->SSN}}"
                   required id="SSN">
    </div>




    <div class="form-group">
            <label for="name"
                   class="">TPIN</label>
            <input type="text" name="TPIN" class="form-control"
                   placeholder="TPIN"
                   value="{{$information->TPIN}}"
                   required id="TPIN">
    </div>



    <div class="form-group" id="">
        <label for="type"
               class="">Payment Type
        </label>
            <select name="payment_type" class="form-control " id="payment_type"
                    required>
                <option></option>
                <option value="mobile money">Mobile Money</option>
                <option value="bank">Bank</option>
            </select>
    </div>



    <div class="form-group">
            <label for="name"
                   class="">Bank Account/Mobile Money #</label>
            <input type="text" name="account_number" class="form-control"
                   placeholder="Account Number"
                   value="{{$information->account_number}}"
                   required id="account_number">
    </div>
</div>

<div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
</form>

    @endif

</div>
@endsection
@section('footer-scripts')
    <script src="{{ asset('assets/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
    <script>
        $(".form").validate({
            rules: {
                field: {
                    required: true,
                    step: 10
                }, password: {
                    required: true,
                    minlength: 6,
                },
                repeat_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            }, highlight: function (element) {
                $(element).closest('.form-group').addClass('has-error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error');
            },
            errorElement: 'span',
            errorClass: 'help-block',
            errorPlacement: function (error, element) {
                if (element.parent('.input-group').length) {
                    error.insertAfter(element.parent());
                } else {
                    error.insertAfter(element);
                }
            }
        });
    </script>
@endsection