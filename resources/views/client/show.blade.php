@extends('layouts.master')
@section('title')
    @if($client->client_type=="individual")
        {{$client->first_name}} {{$client->middle_name}} {{$client->last_name}} #{{$client->account_no}}
    @else
        {{$client->full_name}} #{{$client->account_no}}
    @endif
@endsection
@section('content')
    <div class="row">
        <div class="col-md-3">

            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                    @if(!empty($client->picture))
                        <img class="profile-user-img img-responsive img-circle"
                             src="{{asset('uploads/'.$client->picture)}}"
                             alt="User profile picture">
                             
                    @else
                        <img class="profile-user-img img-responsive img-circle" src="{{asset('uploads/user.png')}}"
                             alt="User profile picture">
                    @endif
                    <h3 class="profile-username text-center">
                        @if($client->client_type=="individual")
                            {{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}
                        @else
                            {{$client->full_name}}
                        @endif
                    </h3>
                    @if($client->client_type=="individual")
                        <p class="text-muted text-center">{{trans_choice('general.individual',1)}}</p>
                    @else
                        <p class="text-muted text-center">{{trans_choice('general.business',1)}}</p>
                    @endif
                    <p class="text-center">
                    <a href="#" class="btn btn-primary btn-sm"
                               data-toggle="modal" data-target="#upload_picture_modal"
                               title="{{trans_choice('general.upload',1)}} {{trans_choice('general.picture',1)}}"><b><i
                                            class="fa fa-camera"></i>
                                </b></a>
                    @if (Sentinel::hasAccess('clients.update'))
                            <a href="{{url('client/'.$client->id.'/edit')}}"
                               class="btn btn-primary btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.edit',1)}}"><b><i
                                            class="fa fa-edit"></i>
                                </b></a>
                            <a href="{{url('communication/client/'.$client->id.'/sms')}}"
                               class="btn btn-success btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.sms',1)}}"><b><i
                                            class="fa fa-envelope"></i>
                                </b></a>
                            <a href="{{url('communication/client/'.$client->id.'/email')}}"
                               class="btn btn-success btn-sm"
                               data-toggle="tooltip" title="{{trans_choice('general.email',1)}}"><b><i
                                            class="fa fa-envelope"></i>
                                </b></a>
                        </p>
                    @endif
                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{trans_choice('general.branch',1)}}</b>
                            @if(!empty($client->office))
                                <a class="pull-right">{{$client->office->name}}</a>
                            @endif
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.staff',1)}}</b>
                            @if(!empty($client->staff))
                                <a class="pull-right">{{$client->staff->first_name}} {{$client->staff->last_name}}</a>
                            @endif
                        </li>
                        @if($client->client_type=="individual")
                            <li class="list-group-item">
                                <b>{{trans_choice('general.gender',1)}}</b>
                                @if($client->gender=="male")
                                    <a class="pull-right">{{trans_choice('general.male',1)}}</a>
                                @endif
                                @if($client->gender=="female")
                                    <a class="pull-right">{{trans_choice('general.female',1)}}</a>
                                @endif
                                @if($client->gender=="other")
                                    <a class="pull-right">{{trans_choice('general.other',1)}}</a>
                                @endif
                                @if($client->gender=="unspecified")
                                    <a class="pull-right">{{trans_choice('general.unspecified',1)}}</a>
                                @endif
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.dob',1)}}</b>
                                <a class="pull-right"> {{ Carbon\Carbon::parse($client->dob)->format('d/m/Y')}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.marital_status',1)}}</b>
                                @if($client->marital_status=="married")
                                    <a class="pull-right">{{trans_choice('general.married',1)}}</a>
                                @endif
                                @if($client->marital_status=="single")
                                    <a class="pull-right">{{trans_choice('general.single',1)}}</a>
                                @endif
                                @if($client->marital_status=="divorced")
                                    <a class="pull-right">{{trans_choice('general.divorced',1)}}</a>
                                @endif
                                @if($client->marital_status=="widowed")
                                    <a class="pull-right">{{trans_choice('general.widowed',1)}}</a>
                                @endif
                                @if($client->marital_status=="unspecified")
                                    <a class="pull-right">{{trans_choice('general.unspecified',1)}}</a>
                                @endif
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.working_place',1)}}</b>
                                <a class="pull-right">{{$client->working_place}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.working_position',1)}}</b>
                                <a class="pull-right">{{$client->working_position}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.salary',1)}}</b>
                                <a class="pull-right">{{$client->salary}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.nrc_number',1)}}</b>
                                <a class="pull-right">{{$client->nrc_number}}</a>
                            </li>
                            @else
                            <li class="list-group-item">
                                <b>{{trans_choice('general.incorporation_number',1)}}</b>
                                <a class="pull-right">{{$client->incorporation_number}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.key_contact_person',1)}}</b>
                                <a class="pull-right"> {{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.nrc_number',1)}}</b>
                                <a class="pull-right"> {{$client->nrc_number}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.number_of_shareholders',1)}}</b>
                                <a class="pull-right"> {{$client->number_of_shareholders}}</a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.company_registration_date',1)}}</b>
                                <a class="pull-right"> {{ Carbon\Carbon::parse($client->company_registration_date)->format('d/m/Y') }} </a>
                            </li>
                            <li class="list-group-item">
                                <b>{{trans_choice('general.type_of_business',1)}}</b>
                                <a class="pull-right"> {{$client->type_of_business}}</a>
                            </li>
                            @endif
                        <li class="list-group-item">
                            <b>{{trans_choice('general.mobile',1)}}</b>
                            <a class="pull-right">{{$client->mobile}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.phone',1)}}</b>
                            <a class="pull-right">{{$client->phone}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.email',1)}}</b>
                            <a class="pull-right">{{$client->email}}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.registration',1)}} {{trans_choice('general.date',1)}}</b>
                            <a class="pull-right">  {{ Carbon\Carbon::parse($client->joined_date)->format('d/m/Y') }}</a>
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.status',1)}}</b>
                            @if($client->status=="pending")
                                <a class="pull-right">{{trans_choice('general.pending',1)}}</a>
                            @endif
                            @if($client->status=="active")
                                <a class="pull-right">{{trans_choice('general.active',1)}}</a>
                            @endif
                            @if($client->status=="inactive")
                                <a class="pull-right">{{trans_choice('general.inactive',1)}}</a>
                            @endif
                            @if($client->status=="declined")
                                <a class="pull-right">{{trans_choice('general.declined',1)}}</a>
                            @endif
                        </li>
                        <li class="list-group-item">
                            <b>{{trans_choice('general.blacklisted',1)}}</b>
                            @if($client->blacklisted==1)
                                <a class="pull-right">{{trans_choice('general.yes',1)}}</a>
                            @else
                                <a class="pull-right">{{trans_choice('general.no',1)}}</a>
                            @endif
                        </li>
                        @foreach(\App\Models\CustomFieldMeta::where('category', 'clients')->where('parent_id', $client->id)->get() as $key)
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
                        @if($client->status=="pending" && Sentinel::hasAccess('clients.approve'))
                            <a class="btn btn-sm btn-success" data-toggle="modal"
                               data-target="#approve_client_modal">{{trans_choice('general.approve',1)}}</a>
                            <a class="btn btn-sm btn-danger" data-toggle="modal"
                               data-target="#decline_client_modal">{{trans_choice('general.decline',1)}}</a>
                        @endif
                        @if($client->status=="active" && Sentinel::hasAccess('clients.close'))
                            <a class="btn btn-sm btn-success" data-toggle="modal"
                               data-target="#close_client_modal">{{trans_choice('general.close',1)}}</a>
                            <a class="btn btn-sm btn-warning" data-toggle="modal"
                               data-target="#inactive_client_modal">{{trans_choice('general.inactive',1)}}</a>
                        @endif
                        @if($client->status=="inactive" && Sentinel::hasAccess('clients.approve'))
                            <a href="{{url('client/'.$client->id.'/active')}}"
                               class="btn btn-sm btn-info confirm">{{trans_choice('general.active',1)}}</a>
                        @endif
                        @if($client->status=="declined" && Sentinel::hasAccess('clients.approve'))
                            <a class="btn btn-sm btn-success" data-toggle="modal"
                               data-target="#approve_client_modal">{{trans_choice('general.approve',1)}}</a>
                        @endif
                        @if( Sentinel::hasAccess('clients.transfer.client'))
                            <a class="btn btn-sm btn-warning" data-toggle="modal"
                               data-target="#transfer_client_modal">{{trans_choice('general.transfer',1)}}</a>
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
                    <p>{!! $client->street !!}</p>
                    <strong><i class="fa fa-map-marker margin-r-5"></i> {{trans_choice('general.address',1)}}</strong>
                    <p>{!! $client->address !!}</p>
                    <hr>
                    <strong><i class="fa fa-file-text-o margin-r-5"></i> {{trans_choice('general.description',1)}}
                    </strong>

                    <p>{!! $client->notes !!}</p>
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
                    @if (Sentinel::hasAccess('clients.identification.view'))
                        <li><a href="#client_identification"
                               data-toggle="tab">{{trans_choice('general.client',1)}} {{trans_choice('general.identification',1)}}</a>
                        </li>
                    @endif
                    @if (Sentinel::hasAccess('clients.documents.view'))
                        <li><a href="#documents" data-toggle="tab">{{trans_choice('general.document',2)}}</a></li>
                    @endif
                    @if (Sentinel::hasAccess('clients.next_of_kin.view'))
                        <li><a href="#next_of_kin" data-toggle="tab">{{trans_choice('general.next_of_kin',2)}}</a></li>
                    @endif
                    @if (Sentinel::hasAccess('clients.notes.view'))
                        <li><a href="#notes" data-toggle="tab">{{trans_choice('general.note',2)}}</a></li>
                    @endif
                    @if (Sentinel::hasAccess('clients.notes.view'))
                        <li><a href="#login_details"
                               data-toggle="tab">{{trans_choice('general.login',1)}} {{trans_choice('general.detail',2)}}</a>
                        </li>
                    @endif
                    @if (Sentinel::hasAccess('clients.blacklist.view'))
                        <li><a href="#blacklist"
                               data-toggle="tab">{{trans_choice('general.blacklist',1)}}</a>
                        </li>
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
                            @foreach(DB::table('loans as l')->leftJoin("loan_repayment_schedules as lr", "l.id", '=', "lr.loan_id")->leftJoin("loan_products as lp", "l.loan_product_id", '=', "lp.id")->select(DB::raw('(COALESCE(SUM(lr.principal),0)+COALESCE(SUM(lr.interest),0)+COALESCE(SUM(lr.fees),0)+COALESCE(SUM(lr.penalty),0)-COALESCE(SUM(lr.principal_waived),0)-COALESCE(SUM(lr.principal_written_off),0)-COALESCE(SUM(lr.principal_paid),0)-COALESCE(SUM(lr.interest_waived),0)-COALESCE(SUM(lr.interest_written_off),0)-COALESCE(SUM(lr.interest_paid),0)-COALESCE(SUM(lr.fees_waived),0)-COALESCE(SUM(lr.fees_written_off),0)-COALESCE(SUM(lr.fees_paid),0)-COALESCE(SUM(lr.penalty_written_off),0)-COALESCE(SUM(lr.penalty_paid),0)) as balance, l.id,l.loan_product_id,l.status,lp.name'))->where('l.client_id', $client->id)->groupBy("l.id")->get() as $key)
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
                                        @if($key->status=="withdrawn")
                                            {{trans_choice('general.withdrawn',1)}}
                                        @endif
                                        @if($key->status=="approved")
                                            {{trans_choice('general.approved',1)}}
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
                    @if (Sentinel::hasAccess('clients.identification.view'))
                        <div class="tab-pane" id="client_identification">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (Sentinel::hasAccess('clients.identification.create'))
                                        <a href="#add_identification_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.identification',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.type',1) }}</th>
                                            <th>{{ trans('general.id') }}</th>
                                            <th>{{ trans('general.description') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($client->identifications as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->type))
                                                        {{$key->type->name}}
                                                    @endif
                                                </td>
                                                <td>{{ $key->name }}</td>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>
                                                    <a class="" href="{{asset('uploads/'.$key->attachment)}}"
                                                       target="_blank"><i class="fa fa-download"></i> </a>
                                                    @if (Sentinel::hasAccess('clients.identification.delete'))
                                                        <a class="confirm"
                                                           href="{{url('client/identification/'.$key->id.'/delete')}}"><i
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
                    @if (Sentinel::hasAccess('clients.documents.view'))
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (Sentinel::hasAccess('clients.documents.create'))
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
                                        @foreach(\App\Models\Document::where('record_id',$client->id)->where('type','client')->get() as $key)
                                            <tr>
                                                <td>{{ $key->name }}</td>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>
                                                    <a class="" href="{{asset('uploads/'.$key->location)}}"
                                                       target="_blank"><i class="fa fa-download"></i> </a>
                                                    @if (Sentinel::hasAccess('clients.documents.delete'))
                                                        <a class="confirm"
                                                           href="{{url('client/document/'.$key->id.'/delete')}}"><i
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
                    @if (Sentinel::hasAccess('clients.next_of_kin.view'))
                        <div class="tab-pane" id="next_of_kin">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (Sentinel::hasAccess('clients.next_of_kin.create'))
                                        <a href="#add_next_of_kin_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.next_of_kin',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.name',1) }}</th>
                                            <th>{{ trans_choice('general.relationship',1) }}</th>
                                            <th>{{ trans_choice('general.mobile',1) }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($client->next_of_kin as $key)
                                            <tr>
                                                <td>{{ $key->first_name }} {{ $key->middle_name }} {{ $key->last_name }}</td>
                                                <td>
                                                    @if(!empty($key->relationship))
                                                        {{$key->relationship->name}}
                                                    @endif
                                                </td>

                                                <td>{{ $key->mobile }}</td>
                                                <td>
                                                    @if (Sentinel::hasAccess('clients.next_of_kin.view'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#view_next_of_kin"><i class="fa fa-eye"></i>
                                                        </a>
                                                    @endif
                                                    @if (Sentinel::hasAccess('clients.next_of_kin.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_next_of_kin"><i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if (Sentinel::hasAccess('clients.next_of_kin.delete'))
                                                        <a class="confirm"
                                                           href="{{url('client/next_of_kin/'.$key->id.'/delete')}}"><i
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
                    @if (Sentinel::hasAccess('clients.notes.view'))
                        <div class="tab-pane" id="notes">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (Sentinel::hasAccess('clients.notes.create'))
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
                                        @foreach(\App\Models\Note::where('reference_id',$client->id)->where('type','client')->get() as $key)
                                            <tr>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>{!!   $key->created_at !!}</td>
                                                <td>
                                                    @if(!empty($key->created_by))
                                                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Sentinel::hasAccess('clients.notes.view'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#view_note"><i class="fa fa-eye"></i> </a>
                                                    @endif
                                                    @if (Sentinel::hasAccess('clients.notes.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_note"><i class="fa fa-edit"></i> </a>
                                                    @endif
                                                    @if (Sentinel::hasAccess('clients.notes.delete'))
                                                        <a class="confirm"
                                                           href="{{url('client/note/'.$key->id.'/delete')}}"><i
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
                    @if (Sentinel::hasAccess('clients.notes.view'))
                        <div class="tab-pane" id="login_details">
                            <div class="row">
                                <div class="col-md-12">
                                    @if (Sentinel::hasAccess('clients.notes.create'))
                                        <a href="#add_user_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.user',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.name',1) }}</th>
                                            <th>{{ trans('general.last_login') }}</th>
                                            <th>{{ trans('general.created_by') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\ClientUser::where('client_id',$client->id)->with('user')->get() as $key)
                                            @if(!empty($key->user))
                                                <tr>
                                                    <td>{{ $key->user->first_name }} {{ $key->user->last_name }}</td>
                                                    <td>{{ $key->user->last_login }} </td>
                                                    <td>
                                                        @if(!empty($key->created_by))
                                                            {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (Sentinel::hasAccess('clients.notes.view'))
                                                            <a href="{{url('user/'.$key->user_id.'/show')}}"
                                                               data-toggle="tooltip"
                                                               data-title="View"><i class="fa fa-eye"></i> </a>
                                                        @endif
                                                        @if (Sentinel::hasAccess('clients.notes.update'))
                                                            <a href="{{url('user/'.$key->user_id.'/edit')}}"
                                                               data-toggle="tooltip"
                                                               data-title="Edit"><i class="fa fa-edit"></i> </a>
                                                        @endif
                                                        @if (Sentinel::hasAccess('clients.notes.delete'))
                                                            <a class="confirm"
                                                               href="{{url('client/'.$key->id.'/delete_user')}}"><i
                                                                        class="fa fa-trash"></i> </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @else

                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal fade" id="add_user_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                                            &times;
                                        </button>
                                        <h4 class="modal-title"> {{trans_choice('general.add',1)}} {{trans_choice('general.user',1)}}</h4>
                                    </div>
                                    <form method="post"
                                          action="{{url('client/'.$client->id.'/add_user')}}"
                                          class="form-horizontal" id="add_user_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="existing_user"
                                                       class="control-label col-md-3">{{trans_choice('general.existing_user',1)}}
                                                </label>
                                                <div class="col-md-9">
                                                    <select name="existing_user" class="form-control "
                                                            id="existing_user"
                                                            required>
                                                        <option></option>
                                                        <option value="0">{{trans_choice('general.no',1)}}</option>
                                                        <option value="1">{{trans_choice('general.yes',1)}}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group" id="existing_user_div" style="display: none">
                                                <label for="user_user_id"
                                                       class="control-label col-md-3">
                                                    {{trans_choice('general.user',1)}}
                                                </label>
                                                <div class="col-md-9">
                                                    <select name="user_id" class="form-control select2"
                                                            id="user_user_id" required>
                                                        <option></option>
                                                        @foreach(\App\Models\User::all() as $key)
                                                            @if(Sentinel::findUserById($key->id)->inRole('client'))
                                                                <option value="{{$key->id}}">{{$key->first_name}} {{$key->last_name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="" style="display: none;" id="new_user_div">
                                                <div class="form-group">
                                                    <label for="user_first_name"
                                                           class="control-label col-md-3">{{trans_choice('general.first_name',1)}}</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="first_name" class="form-control"
                                                               placeholder="{{trans_choice('general.first_name',1)}}"
                                                               value="{{$client->first_name}}"
                                                               id="user_first_name">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="user_last_name"
                                                           class="control-label col-md-3">{{trans_choice('general.last_name',1)}}</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="last_name" class="form-control"
                                                               placeholder="{{trans_choice('general.last_name',1)}}"
                                                               value="{{$client->last_name}}"
                                                               id="user_last_name">
                                                    </div>
                                                </div>
                                                <div class="form-group ">
                                                    <label for="user_email"
                                                           class="control-label col-md-3">{{trans_choice('general.email',1)}}</label>
                                                    <div class="col-md-9">
                                                        <input type="email" name="email" class="form-control"
                                                               placeholder="{{trans_choice('general.email',1)}}"
                                                               value="{{$client->email}}"
                                                               id="user_email">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="user_password"
                                                           class="control-label col-md-3">{{trans_choice('general.password',1)}}</label>
                                                    <div class="col-md-9">
                                                        <input type="text" name="password" class="form-control"
                                                               placeholder="{{trans_choice('general.password',1)}}"
                                                               value=""
                                                               id="user_password">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="send_login_details"
                                                           class="control-label col-md-3">{{trans_choice('general.send_login_details',1)}}
                                                    </label>
                                                    <div class="col-md-9">
                                                        <select name="send_login_details" class="form-control "
                                                                id="send_login_details"
                                                                required>
                                                            <option value="0">{{trans_choice('general.no',1)}}</option>
                                                            <option value="1">{{trans_choice('general.yes',1)}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                    data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                                            <button type="submit"
                                                    class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                        </div>
                    @endif
                    @if (Sentinel::hasAccess('clients.blacklist.view'))
                        <div class="tab-pane" id="blacklist">
                            <h4>Blacklist History</h4>
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans('general.created_by') }}</th>
                                            <th>{{ trans_choice('general.reason',1) }}</th>
                                            <th>{{ trans_choice('general.note',2) }}</th>
                                            <th>{{ trans_choice('general.date',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\BlacklistHistory::where('client_id',$client->id)->with(['office','created_by'])->orderBy('created_at','desc')->get() as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->created_by))
                                                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                    @endif
                                                </td>
                                                <td>{{ $key->reason->name??'' }} </td>
                                                <td>{{ $key->description }} </td>
                                                <td>{{ $key->date }} </td>
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
    <div class="modal fade" id="add_identification_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.identification',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/identification/store')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="add_identification_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="client_identification_type_id"
                                   class="control-label col-md-3">{{trans_choice('general.type',1)}}</label>
                            <div class="col-md-9">
                                <select name="client_identification_type_id" class="select2 form-control"
                                        id="client_identification_type_id" required>
                                    <option></option>
                                    @foreach(\App\Models\ClientIdentificationType::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identification_name"
                                   class="control-label col-md-3">{{trans_choice('general.unique',1)}}#</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control"
                                       value="{{old('name')}}"
                                       required id="identification_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identification_notes"
                                   class="control-label col-md-3">{{trans_choice('general.description',2)}}</label>
                            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="identification_notes" rows="3">{{old('notes')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="identification_attachment"
                                   class="control-label col-md-3">{{trans_choice('general.attachment',1)}}</label>
                            <div class="col-md-9">
                                <input type="file" name="attachment" class="form-control"
                                       id="identification_attachment">
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
                <form method="post" action="{{url('client/'.$client->id.'/document/store')}}"
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
    <div class="modal fade" id="add_next_of_kin_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.next_of_kin',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/next_of_kin/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_next_of_kin_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="next_of_kin_first_name"
                                   class="control-label col-md-3">{{trans_choice('general.first_name',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="first_name" class="form-control"
                                       value="{{old('first_name')}}"
                                       required id="next_of_kin_first_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_middle_name"
                                   class="control-label col-md-3">{{trans_choice('general.middle_name',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="middle_name" class="form-control"
                                       value="{{old('middle_name')}}"
                                       id="next_of_kin_middle_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_last_name"
                                   class="control-label col-md-3">{{trans_choice('general.last_name',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="last_name" class="form-control"
                                       value="{{old('last_name')}}"
                                       required id="next_of_kin_last_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_mobile"
                                   class="control-label col-md-3">{{trans_choice('general.mobile',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="mobile" class="form-control"
                                       value="{{old('mobile')}}"
                                       id="next_of_kin_mobile">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="client_relationship_id"
                                   class="control-label col-md-3">{{trans_choice('general.relationship',1)}}</label>
                            <div class="col-md-9">
                                <select name="client_relationship_id" class="select2 form-control"
                                        id="client_relationship_id" required>
                                    <option></option>
                                    @foreach(\App\Models\ClientRelationship::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_gender"
                                   class="control-label col-md-3">{{trans_choice('general.gender',1)}}</label>
                            <div class="col-md-9">
                                <select name="gender" class="form-control" id="next_of_kin_gender">
                                    <option value="male">{{trans('general.male')}}</option>
                                    <option value="female">{{trans('general.female')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="address"
                                   class="control-label col-md-3">{{trans_choice('general.address',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="address" class="form-control"
                                       value=""
                                       id="address">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="next_of_kin_notes" rows="3">{{old('notes')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="next_of_kin_picture"
                                   class="control-label col-md-3">{{trans_choice('general.picture',1)}}</label>
                            <div class="col-md-9">
                                <input type="file" name="picture" class="form-control"
                                       id="next_of_kin_picture">
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
    <div class="modal fade" id="view_next_of_kin">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_next_of_kin">
        <div class="modal-dialog">
            <div class="modal-content">
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
                <form method="post" action="{{url('client/'.$client->id.'/note/store')}}"
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
                <form method="post" action="{{url('client/'.$client->id.'/picture')}}"
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
    <div class="modal fade" id="approve_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.approve',1)}} {{trans_choice('general.client',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/approve')}}"
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
    <div class="modal fade" id="decline_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.decline',1)}} {{trans_choice('general.client',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/decline')}}"
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
    <div class="modal fade" id="close_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.close',1)}} {{trans_choice('general.client',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/close')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="close_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="closed_date"
                                   class="control-label col-md-3">{{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="closed_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="closed_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="closed_reason"
                                   class="control-label col-md-3">{{trans_choice('general.reason',1)}}</label>
                            <div class="col-md-9">
                        <textarea name="closed_reason" class="form-control"
                                  id="closed_reason" rows="3" required>{{old('closed_reason')}}</textarea>
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
    <div class="modal fade" id="inactive_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.inactive',1)}} {{trans_choice('general.client',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/inactive')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="inactive_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="inactive_date"
                                   class="control-label col-md-3">{{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="inactive_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="inactive_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inactive_reason"
                                   class="control-label col-md-3">{{trans_choice('general.reason',1)}}</label>
                            <div class="col-md-9">
                        <textarea name="inactive_reason" class="form-control"
                                  id="inactive_reason" rows="3" required>{{old('inactive_reason')}}</textarea>
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
    <div class="modal fade" id="transfer_client_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.transfer',1)}} {{trans_choice('general.client',1)}}</h4>
                </div>
                <form method="post" action="{{url('client/'.$client->id.'/transfer')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="transfer_client_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="office_id"
                                   class="control-label col-md-3">{{trans_choice('general.branch',1)}}</label>
                            <div class="col-md-9">
                                <select name="office_id" class="form-control select2" id="office_id" required>
                                    <option></option>
                                    @foreach(\App\Models\Office::whereNotIn('id',[$client->office_id])->get() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
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
@endsection
@section('footer-scripts')
    <script>
        $('#view_next_of_kin').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('client/next_of_kin') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_next_of_kin').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('client/next_of_kin') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        })
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
        });
        $("#add_document_form").validate();
        $("#add_identification_form").validate();
        $("#add_next_of_kin_form").validate();
        $("#add_note_form").validate();
        $("#inactive_client_form").validate();
        $("#close_client_form").validate();
        $("#transfer_client_form").validate();
        $("#add_user_form").validate();
        $('#existing_user').change(function (e) {
            if ($('#existing_user').val() == '1') {
                $('#existing_user_div').show();
                $('#new_user_div').hide();
                $('#user_user_id').attr("required", "required");
                $('#user_first_name').removeAttr("required");
                $('#user_last_name').removeAttr("required");
                $('#user_email').removeAttr("required");
                $('#user_password').removeAttr("required");
            } else {
                $('#new_user_div').show();
                $('#existing_user_div').hide();
                $('#user_user_id').removeAttr("required");
                $('#user_first_name').attr("required", "required");
                $('#user_last_name').attr("required", "required");
                $('#user_email').attr("required", "required");
                $('#user_password').attr("required", "required");
            }
        })
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
