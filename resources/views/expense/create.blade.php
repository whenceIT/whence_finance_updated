@extends('layouts.master')
@section('title')
    {{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('expense/store')}}" class="form-horizontal" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.branch',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option></option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="expense_type_id"
                           class="control-label col-md-2">{{trans_choice('general.type',1)}}</label>
                    <div class="col-md-3">
                        <select name="expense_type_id" class="form-control select2" id="expense_type_id"
                                required>
                            <option></option>
                            @foreach(\App\Models\ExpenseType::all() as $key)
                                <option value="{{$key->id}}">{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{old('name')}}"
                               id="name">
                    </div>
                </div>
                <div class="form-group">
                    <label for="date"
                           class="control-label col-md-2">{{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date('Y-m-d')}}"
                               id="date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="amount"
                           class="control-label col-md-2">{{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-3">
                        <input type="number" name="amount" class="form-control"
                               value="{{old('amount')}}" required
                               id="amount">
                    </div>
                </div>
                <div class="form-group">
                    <label for="recurring"
                           class="control-label col-md-2">{{trans_choice('general.recurring',1)}}</label>
                    <div class="col-md-3">
                        <select name="recurring" class="form-control select2" id="recurring"
                                required>
                            <option value="0">{{trans_choice('general.no',1)}}</option>
                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                        </select>
                    </div>
                </div>
                <div id="recur">
                    <div class="form-group">
                        <label for="recur_frequency"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.frequency',1)}}</label>
                        <div class="col-md-3">
                            <input type="number" name="recur_frequency" class="form-control"
                                   value="{{old('recur_frequency')}}"
                                   id="recur_frequency">
                        </div>
                        <label for="recur_type"
                               class="control-label col-md-2">{{trans_choice('general.recur',1)}} {{trans_choice('general.type',1)}}</label>
                        <div class="col-md-3">
                            <select name="recur_type" class="form-control select2" id="recur_type">
                                <option value="day">{{trans_choice('general.day',1)}}</option>
                                <option value="week">{{trans_choice('general.week',1)}}</option>
                                <option value="month">{{trans_choice('general.month',1)}}</option>
                                <option value="year">{{trans_choice('general.year',1)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="recur_start_date"
                               class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_start_date" class="form-control date-picker"
                                   value=""
                                   id="recur_start_date">
                        </div>
                        <label for="recur_end_date"
                               class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="recur_end_date" class="form-control date-picker"
                                   value=""
                                   id="recur_end_date">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3">{{old('notes')}}</textarea>
                    </div>
                </div>
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','expenses')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif></textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required @endif>
                                @endif
                                @if($key->field_type=="select")
                                    <select class="form-control touchspin" name="custom_field_{{$key->id}}"
                                            @if($key->required==1) required @endif>
                                        @if($key->required!=1)
                                            <option value=""></option>
                                        @else
                                            <option value="" disabled selected>Select...</option>
                                        @endif
                                        @foreach(explode(',',$key->select_values) as $v)
                                            <option>{{$v}}</option>
                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                <input type="radio" name="custom_field_{{$key->id}}" id="{{$key->id}}"
                                                       value="{{$v}}"
                                                       @if($key->required==1) required @endif>
                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
                                    @foreach(explode(',',$key->checkbox_values) as $v)
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                       id="{{$key->id}}"
                                                       value="{{$v}}"
                                                       @if($key->required==1) required @endif>
                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
    </div>
    <!-- /.box -->
@endsection
@section('footer-scripts')
    <script>

        $(document).ready(function (e) {
            $(".form-horizontal").validate();
            if ($('#recurring').val() == '1') {
                $('#recur').show();
                $('#recur_frequency').attr('required', 'required');
                $('#recur_start_date').attr('required', 'required');
                $('#recur_type').attr('required', 'required');
            } else {
                $('#recur').hide();
                $('#recur_frequency').removeAttr('required');
                $('#recur_start_date').removeAttr('required');
                $('#recur_type').removeAttr('required');
            }
            $('#recurring').change(function () {
                if ($('#recurring').val() == '1') {
                    $('#recur').show();
                    $('#recur_frequency').attr('required', 'required');
                    $('#recur_type').attr('required', 'required');
                    $('#recur_start_date').attr('required', 'required');
                } else {
                    $('#recur').hide();
                    $('#recur_frequency').removeAttr('required');
                    $('#recur_start_date').removeAttr('required');
                    $('#recur_type').removeAttr('required');
                }
            })
        })

    </script>
@endsection

