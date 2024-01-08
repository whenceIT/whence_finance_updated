@extends('layouts.master')
@section('title')
    {{$group->name}} #{{$group->account_no}}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    <h3 class="profile-username text-center">
                        {{$group->name}}
                    </h3>
                    @if($group->client_type=="individual")
                        <p class="text-muted text-center">#{{$group->account_no}}</p>
                    @else
                        <p class="text-muted text-center">#{{$group->account_no}}</p>
                    @endif
                    @if(Sentinel::hasAccess('groups.update'))
                        <p class="text-center">
                            <a href="{{url('group/'.$group->id.'/edit')}}"
                               class="btn btn-primary btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.edit',1)}}"><b><i
                                            class="fa fa-edit"></i>
                                </b></a>
                            <a href="{{url('communication/group/'.$group->id.'/sms')}}"
                               class="btn btn-success btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.sms',1)}}"><b><i
                                            class="fa fa-envelope"></i>
                                </b></a>
                            <a href="{{url('communication/group/'.$group->id.'/email')}}"
                               class="btn btn-success btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.email',1)}}"><b><i
                                            class="fa fa-envelope"></i>
                                </b></a>
                        </p>
                    @endif
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{trans_choice('general.external_id',1)}}</b>
                            <a class="pull-right">{{$group->external_id}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.branch',1)}}</b>
                            @if(!empty($group->office))
                                <a class="pull-right">{{$group->office->name}}</a>
                            @endif
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.staff',1)}}</b>
                            @if(!empty($group->staff))
                                <a class="pull-right">{{$group->staff->first_name}} {{$group->staff->last_name}}</a>
                            @endif
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.mobile',1)}}</b>
                            <a class="pull-right">{{$group->mobile}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.phone',1)}}</b>
                            <a class="pull-right">{{$group->phone}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.email',1)}}</b>
                            <a class="pull-right">{{$group->email}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</b>
                            <a class="pull-right">{{$group->joined_date}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.status',1)}}</b>
                            @if($group->status=="pending")
                                <a class="pull-right">{{trans_choice('general.pending',1)}}</a>
                            @endif
                            @if($group->status=="active")
                                <a class="pull-right">{{trans_choice('general.active',1)}}</a>
                            @endif
                            @if($group->status=="inactive")
                                <a class="pull-right">{{trans_choice('general.inactive',1)}}</a>
                            @endif
                            @if($group->status=="declined")
                                <a class="pull-right">{{trans_choice('general.declined',1)}}</a>
                            @endif
                        </li>
                        @foreach(\App\Models\CustomFieldMeta::where('category', 'groups')->where('parent_id', $group->id)->get() as $key)
                            <li class="list-group-item">
                                
                                @if(!empty($key->custom_field))
                                    <b>{{$key->custom_field->name}}:</b>
                                @endif
                                <a class="pull-right">
                                    @if($key->custom_field->field_type=="checkbox")
                                        @foreach(unserialize($key->name) as $v=>$k)
                                            {{$k}}<br>
                                        @endforeach
                                    @else
                                        {{$key->name}}
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <p class="text-center">
                        @if($group->status=="pending" && Sentinel::hasAccess('groups.approve'))
                            <a class="btn btn-sm btn-success" data-toggle="modal"
                               data-target="#approve_group_modal">{{trans_choice('general.approve',1)}}</a>
                            <a class="btn btn-sm btn-danger" data-toggle="modal"
                               data-target="#decline_group_modal">{{trans_choice('general.decline',1)}}</a>
                        @endif
                        @if($group->status=="active" && Sentinel::hasAccess('groups.approve'))
                            <a class="btn btn-sm btn-success">{{trans_choice('general.close',1)}}</a>
                            <a class="btn btn-sm btn-warning">{{trans_choice('general.inactive',1)}}</a>
                        @endif
                        @if($group->status=="inactive" && Sentinel::hasAccess('groups.approve'))
                            <a class="btn btn-sm btn-info">{{trans_choice('general.active',1)}}</a>
                        @endif
                        @if($group->status=="declined" && Sentinel::hasAccess('groups.approve'))
                            <a class="btn btn-sm btn-success" data-toggle="modal"
                               data-target="#approve_group_modal">{{trans_choice('general.approve',1)}}</a>
                        @endif
                    </p>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->

            <!-- About Me Box -->
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{trans_choice('general.extra',1)}} {{trans_choice('general.detail',2)}}</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <strong><i class="fa fa-map-marker margin-r-5"></i> {{trans_choice('general.street',1)}}</strong>
                    <p>{!! $group->street !!}</p>
                    <strong><i class="fa fa-map-marker margin-r-5"></i> {{trans_choice('general.address',1)}}</strong>
                    <p>{!! $group->address !!}</p>
                    <hr>
                    <strong><i class="fa fa-file-text-o margin-r-5"></i> {{trans_choice('general.description',1)}}
                    </strong>

                    <p>{!! $group->notes !!}</p>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#accounts" data-toggle="tab">{{trans_choice('general.account',2)}}</a>
                    </li>
                    <li><a href="#clients"
                           data-toggle="tab">{{trans_choice('general.client',2)}}</a>
                    </li>
                    @if(Sentinel::hasAccess('groups.documents.view'))
                        <li><a href="#documents" data-toggle="tab">{{trans_choice('general.document',2)}}</a></li>
                    @endif
                    @if(Sentinel::hasAccess('groups.notes.view'))
                        <li><a href="#notes" data-toggle="tab">{{trans_choice('general.note',2)}}</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="accounts">
                        <h4>{{ trans_choice('general.loan',2) }}</h4>
                        <table class="table table-hover table-striped" id="">
                            <thead>
                            <tr>

                                <th>{{ trans('general.id') }}</th>
                                <th>{{ trans_choice('general.product',1) }}</th>
                                <th>{{ trans('general.outstanding') }}</th>
                                <th>{{ trans_choice('general.status',1) }}</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(DB::table('loans as l')->leftJoin("loan_repayment_schedules as lr", "l.id", '=', "lr.loan_id")->join("loan_products as lp", "l.loan_product_id", '=', "lp.id")->select(DB::raw('(COALESCE(SUM(lr.principal),0)+COALESCE(SUM(lr.interest),0)+COALESCE(SUM(lr.fees),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) as balance, l.id,l.loan_product_id,l.status,lp.name'))->where('l.group_id', $group->id)->groupBy("l.id")->get() as $key)
                                <tr>
                                    <td>{{ $key->id }}</td>
                                    <td>
                                        {{$key->name}}
                                    </td>
                                    <td>{{ number_format($key->balance,2) }}</td>
                                    <td>
                                        @if($key->status=="disbursed")
                                            {{trans_choice('general.disbursed',1)}}
                                        @endif
                                        @if($key->status=="pending")
                                            {{trans_choice('general.pending',1)}}
                                        @endif
                                        @if($key->status=="approved")
                                            {{trans_choice('general.approved',1)}}
                                        @endif
                                        @if($key->status=="withdrawn")
                                            {{trans_choice('general.withdrawn',1)}}
                                        @endif
                                        @if($key->status=="closed")
                                            {{trans_choice('general.closed',1)}}
                                        @endif
                                        @if($key->status=="written_off")
                                            {{trans_choice('general.written_off',1)}}
                                        @endif
                                        @if($key->status=="rescheduled")
                                            {{trans_choice('general.rescheduled',1)}}
                                        @endif
                                    </td>
                                    <td>
                                        <a class="" href="{{url('loan/'.$key->id.'/show')}}"
                                        ><i class="fa fa-eye"></i> </a>

                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="clients">
                        <div class="row">
                            <div class="col-md-12">
                                @if(Sentinel::hasAccess('groups.client.create'))
                                    <a href="#add_client_modal"
                                       data-toggle="modal" class="btn btn-info pull-right"><i
                                                class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.client',1)}}
                                    </a>
                                @endif
                            </div>
                            <div class="col-md-12 table-responsive">
                                <table class="table table-hover table-striped" id="">
                                    <thead>
                                    <tr>
                                        <th>{{ trans_choice('general.name',1) }}</th>
                                        <th>{{ trans('general.id') }}</th>
                                        <th>{{ trans('general.status') }}</th>
                                        <th>{{ trans_choice('general.action',1) }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($group->clients as $key)
                                        <tr>
                                            @if(!empty($key->client))
                                                <td>
                                                    @if($key->client->client_type=="individual")
                                                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                                    @else
                                                        {{$key->client->full_name}}
                                                    @endif
                                                </td>
                                                <td>{{$key->client->account_no}}</td>
                                                <td>
                                                    @if($key->client->status=="pending")
                                                        {{trans_choice('general.pending',1)}}
                                                    @endif
                                                    @if($key->client->status=="active")
                                                        {{trans_choice('general.active',1)}}
                                                    @endif
                                                    @if($key->client->status=="inactive")
                                                        {{trans_choice('general.inactive',1)}}
                                                    @endif
                                                    @if($key->client->status=="declined")
                                                        {{trans_choice('general.declined',1)}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a class="" href="{{url('client/'.$key->client_id.'/show')}}"><i
                                                                class="fa fa-eye"></i> </a>
                                                    @if(Sentinel::hasAccess('groups.client.delete'))
                                                        <a class="confirm"
                                                           href="{{url('group/client/'.$key->id.'/delete')}}"><i
                                                                    class="fa fa-trash"></i> </a>
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if(Sentinel::hasAccess('groups.documents.view'))
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('groups.documents.create'))
                                        <a href="#add_document_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.document',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.name',1) }}</th>
                                            <th>{{ trans('general.description') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\Document::where('record_id',$group->id)->where('type','group')->get() as $key)
                                            <tr>
                                                <td>{{ $key->name }}</td>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>
                                                    @if(Sentinel::hasAccess('groups.documents.view'))
                                                        <a class="" href="{{asset('uploads/'.$key->location)}}"
                                                           target="_blank"><i class="fa fa-download"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('groups.documents.delete'))
                                                        <a class="confirm"
                                                           href="{{url('group/document/'.$key->id.'/delete')}}"><i
                                                                    class="fa fa-trash"></i> </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Sentinel::hasAccess('groups.notes.view'))
                        <div class="tab-pane" id="notes">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('groups.notes.create'))
                                        <a href="#add_note_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.note',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.note',1) }}</th>
                                            <th>{{ trans('general.date') }}</th>
                                            <th>{{ trans('general.created_by') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\Note::where('reference_id',$group->id)->where('type','group')->get() as $key)
                                            <tr>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>{!!   $key->created_at !!}</td>
                                                <td>
                                                    @if(!empty($key->created_by))
                                                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(Sentinel::hasAccess('groups.notes.view'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#view_note"><i class="fa fa-eye"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('groups.notes.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_note"><i class="fa fa-edit"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('groups.notes.delete'))
                                                        <a class="confirm"
                                                           href="{{url('group/note/'.$key->id.'/delete')}}"><i
                                                                    class="fa fa-trash"></i> </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="add_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.identification',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/client/store')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="add_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="client_id"
                                   class="control-label col-md-3">{{trans_choice('general.client',1)}}</label>
                            <div class="col-md-9">
                                <select name="client_id" class="select2 form-control"
                                        id="client_id" required>
                                    <option></option>
                                    @foreach(\App\Models\Client::where('status','active')->get() as $key)
                                        <option value="{{$key->id}}">
                                            @if($key->client_type=="individual")
                                                {{$key->first_name}} {{$key->middle_name}} {{$key->last_name}}
                                                ({{$key->account_no}})
                                            @else
                                                {{$key->full_name}} ({{$key->account_no}}
                                                )
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add_document_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.document',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/document/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_document_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="document_name"
                                   class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control"
                                       value="{{old('name')}}"
                                       required id="document_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="document_notes"
                                   class="control-label col-md-3">{{trans_choice('general.description',2)}}</label>
                            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="document_notes" rows="3">{{old('notes')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="document_attachment"
                                   class="control-label col-md-3">{{trans_choice('general.attachment',1)}}</label>
                            <div class="col-md-9">
                                <input type="file" name="attachment" class="form-control" required
                                       id="document_attachment">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="add_note_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.note',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/note/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_note_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="note_notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',1)}}</label>
                            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="note_notes" rows="3" required>{{old('notes')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="view_note">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_note">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="upload_picture_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.upload',1)}} {{trans_choice('general.picture',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/picture')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="upload_picture_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="upload_picture"
                                   class="control-label col-md-3">{{trans_choice('general.picture',1)}}</label>
                            <div class="col-md-9">
                                <input type="file" name="picture" class="form-control" required
                                       id="upload_picture">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="approve_group_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.approve',1)}} {{trans_choice('general.group',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/approve')}}"
                      class="form-horizontal" id="approve_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="activated_date"
                                   class="control-label col-md-3">{{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="activated_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="activated_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="decline_group_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.decline',1)}} {{trans_choice('general.group',1)}}</h4>
                </div>
                <form method="post" action="{{url('group/'.$group->id.'/decline')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="decline_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="declined_date"
                                   class="control-label col-md-3">{{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="declined_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="declined_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="declined_reason"
                                   class="control-label col-md-3">{{trans_choice('general.reason',1)}}</label>
                            <div class="col-md-9">
                        <textarea name="declined_reason" class="form-control"
                                  id="declined_reason" rows="3" required>{{old('declined_reason')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('footer-scripts')
    <script>

        $('#view_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('client/note') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('client/note') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        })
        $("#add_document_form").validate();
        $("#add_client_form").validate();
        $("#add_note_form").validate();
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });
    </script>
@endsection
