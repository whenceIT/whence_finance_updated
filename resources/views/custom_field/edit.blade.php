@extends('layouts.master')
@section('title')
    {{trans_choice('general.edit',1)}} {{trans_choice('general.custom_field',1)}}
@endsection
@section('content')
    <!-- Default box -->
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.edit',1)}} {{trans_choice('general.custom_field',1)}}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <form method="post" action="{{url('custom_field/'.$custom_field->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="category"
                               class="control-label col-md-3">{{trans_choice('general.category',1)}}</label>
                        <div class="col-md-9">
                            <select name="category" class="form-control select2" id="category" required>
                                <option value="clients"
                                        @if($custom_field->category=="clients") selected @endif>{{trans_choice('general.client',2)}}</option>
                                <option value="groups"
                                        @if($custom_field->category=="groups") selected @endif>{{trans_choice('general.group',2)}}</option>
                                <option value="loans"
                                        @if($custom_field->category=="loans") selected @endif>{{trans_choice('general.loan',2)}}</option>
                                <option value="savings"
                                        @if($custom_field->category=="savings") selected @endif>{{trans_choice('general.savings',2)}}</option>
                                <option value="offices"
                                        @if($custom_field->category=="offices") selected @endif>{{trans_choice('general.office',2)}}</option>
                                <option value="users"
                                        @if($custom_field->category=="users") selected @endif>{{trans_choice('general.user',2)}}</option>
                                <option value="expenses"
                                        @if($custom_field->category=="expenses") selected @endif>{{trans_choice('general.expense',2)}}</option>
                                <option value="other_income"
                                        @if($custom_field->category=="other_income") selected @endif>{{trans_choice('general.other_income',2)}}</option>
                                <option value="repayments" @if($custom_field->category=="repayments") selected @endif>{{trans_choice('general.repayment',2)}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name"
                               class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                        <div class="col-md-9">
                            <input type="text" name="name" class="form-control"
                                   value="{{$custom_field->name}}"
                                   required id="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-md-3">{{trans_choice('general.required',1)}} </label>
                        <div class="col-md-9">
                            <label><input type="radio" name="required" required value="0"
                                          @if($custom_field->required==0) checked @endif> {{trans_choice('general.no',1)}}
                            </label>
                            <label><input type="radio" name="required" required value="1"
                                          @if($custom_field->required==1) checked @endif> {{trans_choice('general.yes',1)}}
                            </label>
                        </div>
                    </div>
                    <h4>{{trans_choice('general.field',1)}} {{trans_choice('general.type',1)}} </h4>
                    <table class="table table-bordered">
                        <tbody>
                        <tr>
                            <th width="150">&nbsp;</th>
                            <th>{{trans_choice('general.description',1)}}</th>
                            <th>{{trans_choice('general.allowed_value',2)}}</th>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="textfield"
                                               required=""
                                               @if($custom_field->field_type=="textfield") checked @endif>
                                        <b>{{trans_choice('general.text_field',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td>{{trans_choice('general.text_field_description',1)}}</td>
                            <td>{{trans_choice('general.any_value',1)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="date"
                                               required=""
                                               @if($custom_field->field_type=="date") checked @endif>
                                        <b>{{trans_choice('general.date_field',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td>{{trans_choice('general.date_field_description',1)}}</td>
                            <td>{{trans_choice('general.only_date',1)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="number"
                                               required=""
                                               @if($custom_field->field_type=="number") checked @endif>
                                        <b>{{trans_choice('general.number_field',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td>{{trans_choice('general.number_field_description',1)}}</td>
                            <td>{{trans_choice('general.only_number',1)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="decimal"
                                               required="" @if($custom_field->field_type=="decimal") checked @endif>
                                        <b>{{trans_choice('general.decimal_field',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td>{{trans_choice('general.decimal_field_description',1)}}</td>
                            <td>{{trans_choice('general.only_decimal',1)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="textarea"
                                               required=""
                                               @if($custom_field->field_type=="textarea") checked @endif>
                                        <b>{{trans_choice('general.textarea',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td>{{trans_choice('general.textarea_description',1)}}</td>
                            <td>{{trans_choice('general.any_value',1)}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="radiobox"
                                               required=""
                                               @if($custom_field->field_type=="radiobox") checked @endif>
                                        <b>{{trans_choice('general.radio_box',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td colspan="2">
                                <p>{{trans_choice('general.separate_with_comma',1)}}</p>
                                <input type="text" name="radio_box_values"
                                       value="{{$custom_field->radio_box_values}}"
                                       class="form-control" id="radio_box_values" placeholder="Option 1, Option 2">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="checkbox"
                                               required=""
                                               @if($custom_field->field_type=="checkbox") checked @endif>
                                        <b>{{trans_choice('general.checkbox',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td colspan="2">
                                <p>{{trans_choice('general.separate_with_comma',1)}}</p>
                                <input type="text" name="checkbox_values" value="{{$custom_field->checkbox_values}}"
                                       class="form-control" id="checkbox_values" placeholder="Option 1, Option 2">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="field_type" id="inputType" value="select"
                                               required="" @if($custom_field->field_type=="select") checked @endif>
                                        <b>{{trans_choice('general.select',1)}}</b>

                                    </label>
                                </div>
                            </td>
                            <td colspan="2">
                                <p>{{trans_choice('general.separate_with_comma',1)}}</p>
                                <input type="text" name="select_values" value="{{$custom_field->select_values}}"
                                       class="form-control" id="select_values" placeholder="Option 1, Option 2">
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
            </div>
        </form>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
@endsection

