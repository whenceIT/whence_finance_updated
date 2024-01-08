@extends('layouts.master')
@section('title')
    {{ trans_choice('general.add',1) }} {{ trans_choice('general.blacklist',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.add',1) }} {{ trans_choice('general.blacklist',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <form method="post" action="{{url('client/store_blacklist')}}" class="form" enctype="multipart/form-data">
            {{csrf_field()}}
            <div class="box-body">
                <div class="form-group">
                    <label for="client_id"
                           class="control-label">{{trans_choice('general.client',1)}}</label>
                    <select name="client_id" class="form-control select2" id="client_id" required>
                        <option></option>
                        @foreach($clients as $key)
                            <option value="{{$key->id}}">
                                @if($key->client_type=="individual")
                                    {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                    ({{$key->account_no}})({{$key->nrc_number}})
                                @else
                                    {{$key->full_name}} ({{$key->account_no}}
                                    )
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="blacklist_reason_id"
                           class="control-label">{{trans_choice('general.blacklist_reason',1)}}</label>
                    <select name="blacklist_reason_id" class="form-control select2" id="blacklist_reason_id" required>
                        <option></option>
                        @foreach($reasons as $key)
                            <option value="{{$key->id}}">
                                {{$key->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="date"
                           class="control-label">{{trans_choice('general.date',1)}}</label>

                    <input type="text" name="date" class="form-control date-picker" required
                           value="{{date("Y-m-d")}}"
                           id="date">
                </div>
                <div class="form-group">
                    <label for="description"
                           class="control-label">{{trans_choice('general.description',2)}}</label>

                    <textarea name="description" class="form-control"
                              id="description"
                              rows="3">{{old('description')}}</textarea>
                </div>


            </div>
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

    </script>
@endsection