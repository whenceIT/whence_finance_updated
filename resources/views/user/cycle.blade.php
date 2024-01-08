@extends('layouts.master')
@section('title')
    My Cycle
@endsection
@section('content')
<div>
    <form method="post" action="{{url('user/addcycle')}}" class="form-horizontal" enctype="multipart/form-data">
    {{csrf_field()}}
<div class="form-group" id="cycle_end_date_div" style="display: flex;
    align-items: center;
    justify-content: center;">
                    <label for="cycle_end_date"
                           class="control-label col-md-2"> Your cycle ends on
                    </label>
                    <div class="col-md-2">
                        <input type="number" name="cycle_end_date" class="form-control"
                               min='1'                               max="31"
                               required id="cycle_end_date">
                    </div>
                    <label for="cycle_end_date"
                           class="control-label col-md-2 text-left"> 
                      of every month
                    </label>
                </div>
                

                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right" ">{{trans_choice('general.save',1)}}</button>
                </div>
                </form>
</div>           
@endsection
@section('footer-scripts')
   
@endsection
