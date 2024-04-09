@extends('layouts.master')
@section('title')
  Edit details
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
        <form method="post" action="{{url('user/update_my_details')}}" class="form-horizontal"
              enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                @if($client->client_type=="business")
                    <div id="business_name_div">
                        <div class="form-group">
                            <label for="full_name"
                                   class="control-label col-md-2">{{trans_choice('general.name',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="full_name" class="form-control"
                                       value="{{$client->full_name}}"
                                       required id="full_name">
                            </div>
                            <label for="incorporation_number"
                                   class="control-label col-md-2">{{trans_choice('general.incorporation_number',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="incorporation_number" class="form-control"
                                       value="{{$client->incorporation_number}}" id="incorporation_number">
                            </div>
                        </div>
                    </div>
                @endif
                @if($client->client_type=="individual")
                    <div id="individual_name_div">
                        <div class="form-group">
                            <label for="first_name"
                                   class="control-label col-md-2">{{trans_choice('general.first_name',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="first_name" class="form-control"
                                       value="{{$client->first_name}}"
                                       required id="first_name">
                            </div>

                        </div>
                        <div class="form-group">
                            <label for="middle_name"
                                   class="control-label col-md-2">{{trans_choice('general.middle_name',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="middle_name" class="form-control"
                                       value="{{$client->middle_name}}" id="middle_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="last_name"
                                   class="control-label col-md-2">{{trans_choice('general.last_name',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="last_name" class="form-control"
                                       value="{{$client->last_name}}"
                                       required id="last_name">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="mobile"
                           class="control-label col-md-2">{{trans_choice('general.mobile',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="mobile" class="form-control"
                               value="{{$client->mobile}}"
                               required id="mobile">
                               <input type="hidden" name="client_type" class="form-control" value="{{$client->client_type}}" id="client_type">
                    </div>
                    <label for="phone"
                           class="control-label col-md-2">{{trans_choice('general.phone',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="phone" class="form-control"
                               value="{{$client->phone}}"
                               id="phone">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email"
                           class="control-label col-md-2">{{trans_choice('general.email',1)}}</label>
                    <div class="col-md-3">
                        <input type="email" name="email" class="form-control"
                               value="{{$client->email}}"
                               id="email">
                    </div>
                </div>
                @if($client->client_type=="individual")
                    <div id="individual_extra_details">
                        <div class="form-group">
                            <label for="dob"
                                   class="control-label col-md-2">{{trans_choice('general.dob',1)}}</label>
                            <div class="col-md-3">
                                <input type="text" name="dob" class="form-control date-picker"
                                value="{{$client->dob}}"
                                       id="dob">
                            
                            </div>
                            <label for="gender"
                                   class="control-label col-md-2">{{trans_choice('general.gender',1)}}</label>
                            <div class="col-md-3">
                                <select name="gender" class="form-control" id="gender">
                                    <option></option>
                                    <option value="male"
                                            @if($client->gender=="male") selected @endif>{{trans('general.male')}}</option>
                                    <option value="female"
                                            @if($client->gender=="female") selected @endif>{{trans('general.female')}}</option>
                                    <option value="other"
                                            @if($client->gender=="other") selected @endif>{{trans('general.other')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="marital_status"
                                   class="control-label col-md-2">{{trans_choice('general.marital_status',1)}}</label>
                            <div class="col-md-3">
                                <select name="marital_status" class="form-control" id="marital_status">
                                    <option></option>
                                    <option value="married"
                                            @if($client->marital_status=="married") selected @endif>{{trans('general.married')}}</option>
                                    <option value="single"
                                            @if($client->marital_status=="single") selected @endif>{{trans('general.single')}}</option>
                                    <option value="divorced"
                                            @if($client->marital_status=="divorced") selected @endif>{{trans('general.divorced')}}</option>
                                    <option value="widowed"
                                            @if($client->marital_status=="widowed") selected @endif>{{trans('general.widowed')}}</option>
                                    <option value="unspecified"
                                            @if($client->marital_status=="unspecified") selected @endif>{{trans('general.unspecified')}}</option>
                                </select>
                            </div>
                        </div>
          
                @endif
                <div class="form-group">
                    <label for="street"
                           class="control-label col-md-2">{{trans_choice('general.street',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="street" class="form-control"
                               value="{{$client->street}}"
                               id="street">
                    </div>
                    <label for="address"
                           class="control-label col-md-2">{{trans_choice('general.address',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="address" class="form-control"
                               value="{{$client->address}}"
                               id="address">
                    </div>

                </div>
                <div class="form-group">
                    <label for="nrc_number"
                           class="control-label col-md-2">NRC Number</label>
                    <div class="col-md-3">
                        <input type="varchar" name="nrc_number" class="form-control"
                               value="{{$client->nrc_number}}"
                               id="nrc_number">
                    </div>
                    <label for="joined_date"
                           class="control-label col-md-2">{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="joined_date" class="form-control date-picker"
                               value="{{$client->joined_date}}" required
                               id="joined_date">
                    </div>

                </div>

                <div class="form-group">
                    <label for="street"
                           class="control-label col-md-2">Working Place</label>
                    <div class="col-md-3">
                        <input type="text" name="working_place" class="form-control"
                               value="{{$client->working_place}}"
                               id="street">
                    </div>
                    </div>
                    
                    <div class="form-group">
                    <label for="street"
                           class="control-label col-md-2">Occupation</label>
                    <div class="col-md-3">
                        <input type="text" name="working_position" class="form-control"
                               value="{{$client->working_position}}"
                               id="street">
                    </div>
                    <label for="salary"
                           class="control-label col-md-2">Salary</label>
                    <div class="col-md-3">
                        <input type="text" name="salary" class="form-control"
                               value="{{$client->salary}}"
                               id="salary">
                    </div>
                    </div>
                @if($client->status=="active")
                    <div class="form-group">
                        <label for="activated_date"
                               class="control-label col-md-2">{{trans_choice('general.activation',1)}} {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-3">
                            <input type="text" name="activated_date" class="form-control date-picker"
                                   value="{{$client->activated_date}}" required
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
                                  id="notes" rows="3">{{$client->notes}}</textarea>
                    </div>
                </div>
                @if(\App\Models\Setting::where('setting_key','enable_custom_fields')->first()->setting_value==1)
                    @foreach(\App\Models\CustomField::where('category','clients')->get() as $key)
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-2">{{$key->name}}</label>
                            <div class="col-md-8">
                                @if($key->field_type=="number")
                                    <input type="number" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textfield")
                                    <input type="text" class="form-control" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="date")
                                    <input type="text" class="form-control date-picker" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name}} @endif">
                                @endif
                                @if($key->field_type=="textarea")
                                    <textarea class="form-control" name="custom_field_{{$key->id}}"
                                              @if($key->required==1) required @endif>@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name}} @endif</textarea>
                                @endif
                                @if($key->field_type=="decimal")
                                    <input type="text" class="form-control touchspin" name="custom_field_{{$key->id}}"
                                           @if($key->required==1) required
                                           @endif value="@if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first())){{\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name}} @endif">
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
                                            @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()))
                                                @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name==$v)
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
                                                @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()))
                                                    @if(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()->name==$v)
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
                                    @if(!empty(\App\Models\CustomFieldMeta::where('custom_field_id',$key->id)->where('parent_id',$client->id)->where('category','clients')->first()))
                                        <?php $c = unserialize(\App\Models\CustomFieldMeta::where('custom_field_id',
                                            $key->id)->where('parent_id', $client->id)->where('category',
                                            'clients')->first()->name); ?>

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
    @if($client->client_type=="individual")
        <script>
            $("#first_name").attr("required", "required");
            $("#last_name").attr("required", "required");
            $("#dob").attr("required", "required");
            $("#gender").attr("required", "required");
            $("#marital_status").attr("required", "required");
            $("#full_name").removeAttr("required");
        </script>
    @endif
    @if($client->client_type=="business")
        <script>
            $("#first_name").removeAttr("required");
            $("#last_name").removeAttr("required");
            $("#dob").removeAttr("required");
            $("#gender").removeAttr("required");
            $("#marital_status").removeAttr("required");
            $("#full_name").attr("required", "required");
        </script>
    @endif
    <script>

        $(".form-horizontal").validate();
    </script>
@endsection