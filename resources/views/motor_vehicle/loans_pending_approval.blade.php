@extends('layouts.master')
@section('title')
    Motor Vehicle Loans Pending Approval
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Motor Vehicle Loans Pending</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.loan',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info" style="margin-bottom: 20px;">
                        <h4><i class="fa fa-car"></i> Vehicle & Loan Summary</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total Pending:</strong><br>
                                <span class="badge bg-blue">{{ $data->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Total Amount:</strong><br>
                                KSh {{ number_format($data->sum('principal'), 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Pending Approval:</strong><br>
                                <span class="badge bg-yellow">{{ $data->where('status', 'pending')->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Approved:</strong><br>
                                <span class="badge bg-green">{{ $data->where('status', 'approved')->count() }}</span> loans
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Loans List</h3>
        </div>
        <div class="box-body">

            <form method="GET" class="form-horizontal">

                <div class="row">

                    <div class="col-md-3">
                        <select name="office" class="form-control">
                            <option value="">All Branches</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}"
                                    {{ request('office')==$office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="district" class="form-control">
                            <option value="">All Districts</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}"
                                    {{ request('district')==$district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="province" class="form-control">
                            <option value="">All Provinces</option>
                            @foreach($provinces as $province)
                                <option value="{{ $province->id }}"
                                    {{ request('province')==$province->id ? 'selected' : '' }}>
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <select name="staff" class="form-control">
                            <option value="">All Staff</option>
                            @foreach($staff as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('staff')==$user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div class="row" style="margin-top: 10px;">
                    <div class="col-md-12">
                        <button class="btn btn-success">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ url()->current() }}"
                           class="btn btn-default">
                            Reset
                        </a>
                    </div>
                </div>

            </form>

        </div>

        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th>{{ trans_choice('general.account',1) }}#</th>
                        <th>{{ trans_choice('general.branch',1) }}</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>{{ trans_choice('general.client',1) }}</th>
                        <th>Loan Consultant</th>
                        <th>Received By</th>
                        <th>Inspector</th>
                        <th>Valuator</th>
                        <th>Custodian</th>
                        <th>{{ trans_choice('general.proposed',1) }} {{ trans_choice('general.amount',1) }}</th>
                        <th>{{ trans_choice('general.created_at',1) }}</th>
                        <th>Approval</th>
                        <th>Onboarding Progress</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                <tr>
                <td>{{ $key->id }}</td>
                <td>
                    @if(!empty($key->originatingBranch))
                        {{$key->originatingBranch->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->originatingBranch->district))
                        {{$key->originatingBranch->district->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->originatingBranch->province))
                        {{$key->originatingBranch->province->name}}
                    @endif
                </td>
                <td>
                    @if($key->client_type=="client")
                        @if(!empty($key->client))
                            @if($key->client->client_type=="individual")
                                {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                            @else
                                {{$key->client->full_name}}
                            @endif
                        @endif
                    @endif
                    @if($key->client_type=="group")
                        {{$key->group->name}}
                    @endif
                </td>
                <td>
                    @if(!empty($key->loanConsultant))
                        {{$key->loanConsultant->first_name}} {{$key->loanConsultant->last_name}}
                        <br><small class="text-muted">ID: {{$key->loanConsultant->id}}</small>
                    @endif
                </td>
                <td>
                    @if(!empty($key->created_by))
                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                    @endif
                </td>
                <td>
                    @php
                        $latestInspection = null;
                        if (!empty($key->vehicle) && $key->vehicle->relationLoaded('inspections')) {
                            $latestInspection = $key->vehicle->inspections->sortByDesc('inspection_date')->first();
                        }
                    @endphp
                    @if(!empty($latestInspection))
                        {{ $latestInspection->inspector }}
                    @endif
                </td>
                <td>
                    @php
                        $latestValuation = null;
                        if (!empty($key->vehicle) && $key->vehicle->relationLoaded('valuations')) {
                            $latestValuation = $key->vehicle->valuations->sortByDesc('valuation_date')->first();
                        }
                    @endphp
                    @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                        {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                    @endif
                </td>
                <td>
                    @if(!empty($key->vehicle) && !empty($key->vehicle->custody) && !empty($key->vehicle->custody->receiver))
                        {{ $key->vehicle->custody->receiver->first_name }} {{ $key->vehicle->custody->receiver->last_name }}
                    @endif
                </td>
                <td>{{ number_format($key->principal, $key->decimals) }}</td>
                <td>{{ $key->created_date }}</td>
                <td>
                {{$key->status}}
            </td>
            <td>
                <x-onboarding-progress :status="$statuses[$key->id]" :loan="$key" />
            </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                aria-expanded="false"><i
                                    class="fa fa-navicon"></i></button>
                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                            @if(Sentinel::hasAccess('loans.view'))
                                <li>
                                    <a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                class="fa fa-search"></i>
                                        {{ trans_choice('general.detail',2) }}</a>
                                </li>
                            @endif
                            @if($key->status=="pending")
                                @if(Sentinel::hasAccess('loans.update'))
                                    <li>
                                        <a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                    class="fa fa-edit"></i>
                                            {{ trans('general.edit') }}</a>
                                    </li>
                                @endif
                                @if(Sentinel::hasAccess('loans.delete'))
                                    <li>
                                        <a href="{{ url('loan/'.$key->id.'/delete') }}"
                                           class="delete"><i
                                                    class="fa fa-trash"></i>
                                            {{ trans('general.delete') }}</a>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[10, "desc"]],
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