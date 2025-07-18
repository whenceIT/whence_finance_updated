@extends('layouts.master')
@section('title')
Appraisal Form
@endsection

@section('content')

<div class="box box-primary">
<div class="box-header with-border">
        <h3 class="box-title">{{$form->form_name}}</h3>

</div>

<form method="post" class="form-horizontal" action="{{url('user/'.$form->id.'/add_section')}}"
enctype="multipart/form-data">
{{csrf_field()}}
<div class="form-group" id="">
                <label for="type"
                       class="control-label col-md-1">Section Name
                </label>
                <div class="col-md-5">
                <input type="text" name="section_name" class="form-control" 
                required id="section_name">
                </div>
            </div>

            <div class="form-group">
                <label for=""
                       class="control-label col-md-1"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                        Add Section
                    </button>
                </div>
            </div>
</form>







        <div class="box-body">
            @foreach($sections as $section)
            <div class="form-group">
                <h3>{{$section->section_name}}</h3>
            </div>


            @foreach($questions as $question)

            @if($question->section_id == $section->id)
         

<div class="form-group">
<p><span>1. </span> {{$question->question}} <span> {{$question->unit}} </span></p>
</div>






@endif

@endforeach


<form method="post" class="form-horizontal" action="{{url('user/'.$section->id.'/'.$form->id.'/add_question')}}"
enctype="multipart/form-data">
{{csrf_field()}}
<div class="form-group" id="">
                <label for="type"
                       class="control-label col-md-1">Question
                </label>
                <div class="col-md-5">
                <input type="text" name="question" class="form-control" 
                required id="question">
                </div>

                <label for="type"
                       class="control-label col-md-1">Unit
                </label>
                <div class="col-md-1">
                <select name="unit" class="form-control" id="unit"
                            required>
                        <option></option>
                        <option value="%">%</option>
                        <option value="[1-5]">[1-5]</option>
                        <option value="K">K</option>
                        <option value="Number">Number</option>
                        <option value="p_r">Peer Review</option>
                        <option value="sb_r">Subordinate Review</option>
                        <option value="p_r_dm">Peer Review DM</option>
                        <option value="rr_r">Recoveries Rep Review</option>
                        <option value="rh_r">Recoveries Head Review</option>
                        <option value="ma_r">Manager Admin</option>
                        <option value="text">Text</option>
                        <option value="[I,S,D]">ISD</option>
                        <option value="subop1">subop1</option>
                        <option value="subop2">subop2</option>
                        <option value="subop3">subop3</option>
                        <option value="subop4">subop4</option>
                        <option value="yes/no">yes/no</option>
                        <option value="info">info</option>
                    </select>
                </div>
            </div>

  

            <div class="form-group">
                <label for=""
                       class="control-label col-md-1"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                        Add Question
                    </button>
                </div>
            </div>
</form>


            @endforeach

        </div>
</div>

@endsection
