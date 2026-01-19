@extends('layouts.master')
@section('title')
    {{ $user->first_name }} {{ $user->last_name }} - {{ trans_choice('general.user', 1) }} Details
@endsection

@section('content')
    <style>
        .profile-user-img {
            margin: 0 auto;
            width: 100px;
            padding: 3px;
            border: 3px solid #d2d6de;
        }
        .info-label {
            font-weight: bold;
            color: #555;
            width: 40%;
        }
        .detail-card {
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border-radius: 4px;
        }
        .status-badge {
            font-size: 0.9em;
            padding: 5px 10px;
        }
        .action-buttons {
            margin-bottom: 15px;
        }
    </style>

    <div class="row">
        <!-- Content Header / Action Buttons -->
        <div class="col-md-12 action-buttons">
            <div class="pull-right">
                <a href="{{ url('user/' . $user->id . '/edit') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-edit"></i> {{ trans('general.edit') }}
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column: Profile Summary -->
        <div class="col-md-3">
            <div class="box box-primary detail-card">
                <div class="box-body box-profile">
                    @if(!empty($user->picture))
                        <img class="profile-user-img img-responsive img-circle" src="{{ asset('uploads/'.$user->picture) }}" alt="User profile picture">
                    @else
                        <img class="profile-user-img img-responsive img-circle" src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR99-ZMZeEtYlFVdT-HN3Hz0f_i64Zf76D67g&s" alt="User profile picture">
                    @endif

                    <h3 class="profile-username text-center">{{ $user->first_name }} {{ $user->last_name }}</h3>

                    <p class="text-muted text-center">
                        @foreach($user->roles as $role)
                            <span class="label label-info">{{ $role->name }}</span>
                        @endforeach
                    </p>

                    <ul class="list-group list-group-unbordered">
                        <li class="list-group-item">
                            <b>{{ trans('general.status') }}</b> 
                            <span class="pull-right">
                                @if($user->status == 'Active')
                                    <span class="label label-success status-badge">Active</span>
                                @else
                                    <span class="label label-default status-badge">Inactive</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item text-center" style="border-bottom: none;">
                            <form action="{{ route('user.toggleStatus', $user->id) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-xs {{ $user->status == 'Active' ? 'btn-danger' : 'btn-success' }} btn-block" style="margin-top: 10px;">
                                    {{ $user->status == 'Active' ? 'Deactivate Account' : 'Activate Account' }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- System Info Card -->
            <div class="box box-primary detail-card">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="fa fa-cog"></i> {{ trans('general.system') }}</h3>
                </div>
                <div class="box-body">
                    <strong><i class="fa fa-calendar margin-r-5"></i> {{ trans('general.created_at') }}</strong>
                    <p class="text-muted">{{ $user->created_at }}</p>
                    <hr style="margin: 10px 0;">
                    
                    <strong><i class="fa fa-refresh margin-r-5"></i> {{ trans('general.updated_at') }}</strong>
                    <p class="text-muted">{{ $user->updated_at }}</p>
                    <hr style="margin: 10px 0;">

                    <strong><i class="fa fa-sign-in margin-r-5"></i> {{ trans('general.last_login') }}</strong>
                    <p class="text-muted">{{ $user->last_login ?: 'Never' }}</p>
                </div>
            </div>
        </div>

        <!-- Right Column: Detailed Information -->
        <div class="col-md-9">
            <div class="nav-tabs-custom detail-card">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#details" data-toggle="tab">{{ trans('general.details') }}</a></li>
                    <li><a href="#notes_tab" data-toggle="tab">{{ trans_choice('general.note', 2) }}</a></li>
                    @if(\App\Models\CustomFieldMeta::where('category', 'users')->where('parent_id', $user->id)->count() > 0)
                        <li><a href="#custom_fields_tab" data-toggle="tab">Additional Info</a></li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="details">
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="page-header"><i class="fa fa-user"></i> Personal Information</h4>
                                <table class="table no-border">
                                    <tr>
                                        <td class="info-label">{{ trans('general.gender') }}:</td>
                                        <td>{{ ucfirst($user->gender) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">NRC ID:</td>
                                        <td><span class="text-primary" style="font-weight: bold;">{{ $user->nrc_id ?: 'N/A' }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">{{ trans_choice('general.email', 1) }}:</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">{{ trans('general.phone') }}:</td>
                                        <td>{{ $user->phone }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h4 class="page-header"><i class="fa fa-building"></i> Organization & Location</h4>
                                <table class="table no-border">
                                    <tr>
                                        <td class="info-label">Province:</td>
                                        <td>{{ $user->province ? $user->province->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">Office/Branch:</td>
                                        <td>{{ $user->office ? $user->office->name : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="info-label">{{ trans('general.address') }}:</td>
                                        <td>{!! $user->address ?: 'N/A' !!}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="notes_tab">
                        <div class="post">
                            <p>{!! $user->notes ?: 'No notes available.' !!}</p>
                        </div>
                    </div>

                    <div class="tab-pane" id="custom_fields_tab">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Field Name</th>
                                    <th>Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\CustomFieldMeta::where('category', 'users')->where('parent_id', $user->id)->get() as $key)
                                    <tr>
                                        <td>
                                            @if(!empty($key->custom_field))
                                                {{$key->custom_field->name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->custom_field) && $key->custom_field->field_type=="checkbox")
                                                @foreach(unserialize($key->name) as $v=>$k)
                                                    {{$k}}<br>
                                                @endforeach
                                            @else
                                                {{$key->name}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        // Any specific JS for this page can go here
    </script>
@endsection
