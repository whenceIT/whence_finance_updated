@extends('layouts.master')
@section('title')
    {{ trans_choice('general.edit',1) }} {{ trans_choice('general.group',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.edit',1) }} {{ trans_choice('general.client',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('group/'.$group->id.'/update')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="staff_id"
                           class="control-label col-md-2">{{trans_choice('general.staff',1)}}</label>
                    <div class="col-md-3">
                        <select name="staff_id" class="form-control select2" id="staff_id" required>
                            <option></option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($group->staff_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name"
                           class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="name" class="form-control"
                               value="{{$group->name}}"
                               required id="name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="mobile"
                           class="control-label col-md-2">{{trans_choice('general.mobile',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="mobile" class="form-control"
                               value="{{$group->mobile}}"
                               required id="mobile">
                    </div>
                    <label for="phone"
                           class="control-label col-md-2">{{trans_choice('general.phone',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control"
                               value="{{$group->phone}}"
                               id="phone">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}}</label>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control"
                               value="{{$group->email}}"
                               id="email">
                    </div>
                </div>
                <div class="form-group">
                    <label for="street"
                           class="control-label col-md-2">{{trans_choice('general.street',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="street" class="form-control"
                               value="{{$group->street}}"
                               id="street">
                    </div>
                    <label for="address"
                           class="control-label col-md-2">{{trans_choice('general.address',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="address" class="form-control"
                               value="{{$group->address}}"
                               id="address">
                    </div>

                </div>
                <div class="form-group">
                    <label for="external_id"
                           class="control-label col-md-2">{{trans_choice('general.external_id',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="external_id" class="form-control"
                               value="{{$group->external_id}}"
                               id="external_id">
                    </div>
                    <label for="joined_date"
                           class="control-label col-md-2">{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="joined_date" class="form-control date-picker"
                               value="{{$group->joined_date}}" required
                               id="joined_date">
                    </div>

                </div>
                @if($group->status=="active")
                    <div class="form-group">
                        <label for="activated_date"
                               class="control-label col-md-2">{{trans_choice('general.activation',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="activated_date" class="form-control date-picker"
                                   value="{{$group->activated_date}}" required
                                   id="activated_date">
                        </div>

                    </div>
                @endif
                <div class="form-group">
                    <label for="notes"
                           class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                    <div class="col-md-8">
                        <textarea name="notes" class="form-control "
                                  placeholder=""
                                  id="notes" rows="3">{{$group->notes}}</textarea>
                    </div>


                </div>
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','groups')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name}} @endif</textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name}} @endif">
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
                                            @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()))
                                                @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name==$v)
                                                    <option selected>{{$v}}</option>
                                                @else
                                                    <option>{{$v}}</option>
                                                @endif
                                            @else
                                                <option>{{$v}}</option>
                                            @endif

                                        @endforeach
                                    </select>
                                @endif
                                @if($key->field_type=="radiobox")
                                    @foreach(explode(',',$key->radio_box_values) as $v)
                                        <div class="radio">
                                            <label>
                                                @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()))
                                                    @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()->name==$v)
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif checked>
                                                    @else
                                                        <input type="radio" name="custom_field_{{$key->id}}"
                                                               id="{{$key->id}}" value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                @else
                                                    <input type="radio" name="custom_field_{{$key->id}}"
                                                           id="{{$key->id}}" value="{{$v}}"
                                                           @if($key->required==1) required @endif>
                                                @endif

                                                <b>{{$v}}</b>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                                @if($key->field_type=="checkbox")
                                    @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$group->id)->where('category','groups')->first()))
                                        <?php $c = unserialize(\App\Models\CustomFieldMeta::where('custom_field_id',
                                            $key->id)->where('parent_id', $group->id)->where('category',
                                            'groups')->first()->name); ?>

                                        @foreach(explode(',',$key->checkbox_values) as $v)
                                            <div class="checkbox">
                                                <label>
                                                    @if(array_key_exists($v,$c))
                                                        @if($c[$v]==$v)
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif checked>
                                                        @else
                                                            <input type="checkbox"
                                                                   name="custom_field_{{$key->id}}[{{$v}}]"
                                                                   id="{{$key->id}}"
                                                                   value="{{$v}}"
                                                                   @if($key->required==1) required @endif>
                                                        @endif
                                                    @else
                                                        <input type="checkbox" name="custom_field_{{$key->id}}[{{$v}}]"
                                                               id="{{$key->id}}"
                                                               value="{{$v}}"
                                                               @if($key->required==1) required @endif>
                                                    @endif
                                                    <b>{{$v}}</b>
                                                </label>
                                            </div>
                                        @endforeach
                                    @else
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
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            <!-- /.box-body -->
            <div class="box-footer">
                <div class="heading-elements">
                    <button type="submit" class="btn btn-primary pull-right">{{trans_choice('general.save',1)}}</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('footer-scripts')

    <script>

        $(".form-horizontal").validate();
    </script>
@endsection