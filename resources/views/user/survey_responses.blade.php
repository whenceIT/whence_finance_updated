@extends('layouts.master')
@section('title')
    Survey Responses
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Survey Responses</h3>
        <div class="box-tools pull-right">
            @if(Sentinel::hasAccess('surveys.export'))
                <button type="button" class="btn btn-success btn-sm" onclick="exportResponses()">
                    <i class="fa fa-download"></i> Export
                </button>
            @endif
        </div>
    </div>

    {{-- Filter Section --}}
    <div class="box-body hidden-print">
        <form method="get" action="{{Request::url()}}" class="form-horizontal" id="filterForm">
            <div class="form-group">
                <label for="survey_id" class="control-label col-md-2">Survey</label>
                <div class="col-md-3">
                    <select name="survey_id" class="form-control select2" id="survey_id">
                        <option value="">All Surveys</option>
                        @if(isset($surveys))
                            @foreach($surveys as $survey)
                                <option value="{{ $survey->id }}" {{ old('survey_id', request('survey_id')) == $survey->id ? 'selected' : '' }}>
                                    {{ $survey->title }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="office_id" class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                <div class="col-md-3">
                    @if($role->role_id == '1')
                        <select name="office_id" class="form-control select2" id="office_id">
                            <option value="0" {{ request('office_id') == '0' ? 'selected' : '' }}>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $office)
                                <option value="{{ $office->id }}" {{ old('office_id', request('office_id')) == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($role->role_id == '6')
                        <select name="office_id" class="form-control select2" id="office_id">
                            <option value="">All Offices</option>
                            @foreach(\App\Models\Office::where('province_id', $userProvince)->get() as $office)
                                <option value="{{ $office->id }}" {{ old('office_id', request('office_id')) == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($role->role_id == '4' || $role->role_id == '3')
                        <select name="office_id" class="form-control select2" id="office_id">
                            @foreach(\App\Models\Office::where('id', $userBranch)->get() as $office)
                                <option value="{{ $office->id }}" {{ old('office_id', request('office_id')) == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            <div class="form-group">
                <label for="role_id" class="control-label col-md-2">{{trans_choice('general.role',1)}}</label>
                <div class="col-md-3">
                    <select name="role_id" class="form-control select2" id="role_id">
                        <option value="">All Roles</option>
                        @foreach(DB::table('roles')->get() as $roleItem)
                            <option value="{{ $roleItem->id }}" {{ old('role_id', request('role_id')) == $roleItem->id ? 'selected' : '' }}>
                                {{ $roleItem->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="start_date" class="control-label col-md-2">Start Date</label>
                <div class="col-md-3">
                    <input type="text" name="start_date" class="form-control date-picker" 
                           value="{{ old('start_date', request('start_date')) }}" id="start_date">
                </div>
            </div>

            <div class="form-group">
                <label for="end_date" class="control-label col-md-2">End Date</label>
                <div class="col-md-3">
                    <input type="text" name="end_date" class="form-control date-picker" 
                           value="{{ old('end_date', request('end_date')) }}" id="end_date">
                </div>
            </div>

            <div class="form-group">
                <label for="status" class="control-label col-md-2">Status</label>
                <div class="col-md-3">
                    <select name="status" class="form-control" id="status">
                        <option value="">All Status</option>
                        <option value="completed" {{ old('status', request('status')) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="pending" {{ old('status', request('status')) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ old('status', request('status')) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="control-label col-md-2"></label>
                <div class="col-md-10">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="{{ url('user/survey_responses') }}" class="btn btn-default">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Statistics Cards --}}
    @if(isset($statistics))
    <div class="row" style="margin-bottom: 20px;">
        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $statistics['total'] ?? 0 }}</h3>
                    <p>Total Responses</p>
                </div>
                <div class="icon">
                    <i class="fa fa-file-text-o"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $statistics['completed'] ?? 0 }}</h3>
                    <p>Completed</p>
                </div>
                <div class="icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $statistics['pending'] ?? 0 }}</h3>
                    <p>Pending</p>
                </div>
                <div class="icon">
                    <i class="fa fa-clock-o"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-xs-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $statistics['in_progress'] ?? 0 }}</h3>
                    <p>In Progress</p>
                </div>
                <div class="icon">
                    <i class="fa fa-spinner"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Responses Table --}}
    <div class="box-body table-responsive">
        <table class="table table-bordered table-hover table-striped" id="responses-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ trans('general.name') }}</th>
                    <th>{{ trans_choice('general.office',1) }}</th>
                    <th>{{ trans_choice('general.role',1) }}</th>
                    <th>Survey</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Score</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($responses) && count($responses) > 0)
                    @foreach($responses as $response)
                        <tr>
                            <td>{{ $response->id }}</td>
                            <td>
                                @if($response->user)
                                    {{ $response->user->first_name }} {{ $response->user->last_name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($response->user && $response->user->office)
                                    {{ $response->user->office->name }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($response->user && $response->user->role)
                                    <span class="label label-info">{{ $response->user->role->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($response->survey)
                                    {{ $response->survey->title }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($response->submitted_at)
                                    {{ \Carbon\Carbon::parse($response->submitted_at)->format('d M, Y H:i') }}
                                @else
                                    <span class="text-muted">Not submitted</span>
                                @endif
                            </td>
                            <td>
                                @if($response->status == 'completed')
                                    <span class="label label-success">Completed</span>
                                @elseif($response->status == 'pending')
                                    <span class="label label-warning">Pending</span>
                                @elseif($response->status == 'in_progress')
                                    <span class="label label-info">In Progress</span>
                                @else
                                    <span class="label label-default">{{ ucfirst($response->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if($response->score !== null)
                                    <span class="badge bg-{{ $response->score >= 80 ? 'green' : ($response->score >= 60 ? 'yellow' : 'red') }}">
                                        {{ $response->score }}%
                                    </span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown">
                                        <i class="fa fa-navicon"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('surveys.view'))
                                            <li>
                                                <a href="{{ url('user/survey_response/' . $response->id . '/show') }}">
                                                    <i class="fa fa-eye"></i> View Details
                                                </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('surveys.export'))
                                            <li>
                                                <a href="{{ url('user/survey_response/' . $response->id . '/export') }}">
                                                    <i class="fa fa-download"></i> Export PDF
                                                </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('surveys.delete'))
                                            <li>
                                                <a href="{{ url('user/survey_response/' . $response->id . '/delete') }}" 
                                                   class="delete" onclick="return confirm('Are you sure you want to delete this response?');">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No survey responses found
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        // Initialize DataTable
        $('#responses-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[5, "desc"]],
            "columnDefs": [
                {"orderable": false, "targets": [8]}
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

        // Initialize Select2
        $('.select2').select2({
            width: '100%'
        });

        // Initialize Date Pickers
        $('.date-picker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    });

    // Export function
    function exportResponses() {
        const form = document.getElementById('filterForm');
        const formData = new FormData(form);
        const params = new URLSearchParams(formData).toString();
        
        window.location.href = "{{ url('user/survey_responses/export') }}?" + params;
    }
</script>
@endsection
