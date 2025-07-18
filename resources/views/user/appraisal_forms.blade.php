@extends('layouts.master')
@section('title')
Appraisal Forms
@endsection
@section('content')
<div>



<form method="post" class="form-horizontal" action="{{url('user/create_form')}}"
enctype="multipart/form-data">
{{csrf_field()}}
<div class="form-group" id="">
                <label for="type"
                       class="control-label col-md-3">Form Name
                </label>
                <div class="col-md-5">
                <input type="text" name="form_name" class="form-control" 
                required id="form_name">
                </div>

                <label for="type"
                       class="control-label col-md-1">Role
                </label>
                <div class="col-md-1">
                <select name="role" class="form-control" id="role"
                            required>
                            <option></option>
                            @foreach(DB::table('roles')->get() as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <div class="form-group">
                <label for=""
                       class="control-label col-md-3"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary" id="next" ">add new form</button>
                </div>
            </div>
</form>






    @foreach($forms as $form)
    <a href="{{url('user/'.$form->id.'/appraisal_form')}}">
<div class="col-md-3 col-sm-6 col-xs-12">
<div class="info-box bg-purple">
<span class="info-box-icon"><i class="fa fa-file-text-o"></i></span>
<div class="info-box-content">
<span class="info-box-text">{{$form->form_name}}</span>
</div>
</div>
</div>
</a>
    @endforeach
</div>
@endsection
@section('footer-scripts')
@endsection
