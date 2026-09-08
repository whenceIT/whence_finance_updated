@extends('layouts.master')
@section('title')
    Disposal Register
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Disposal Register (Defaulted Motor Vehicle Loans)</h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="callout callout-info" style="margin-bottom: 20px;">
                        <h4><i class="fa fa-car"></i> Disposal Summary</h4>
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Total Defaulted/Disbursed:</strong><br>
                                <span class="badge bg-blue">{{ $loans->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Total Value:</strong><br>
                                K{{ number_format($loans->sum('principal'), 2) }}
                            </div>
                            <div class="col-md-3">
                                <strong>Defaulted:</strong><br>
                                <span class="badge bg-red">{{ $loans->where('defaulted', 'yes')->count() }}</span> loans
                            </div>
                            <div class="col-md-3">
                                <strong>Disbursed (Overdue):</strong><br>
                                <span class="badge bg-yellow">{{ $loans->where('defaulted', '!=', 'yes')->count() }}</span> loans
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
            <table class="table table-bordered table-hover table-striped" id="data-table">
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
                        <th>1st Repayment</th>
                        <th>Time Taken to Sale</th>
                        <th>Status</th>
                        <th>Onboarding Progress</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($loans as $loan)
                    @php
                        $status = $statuses[$loan->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null, 'ownership_completed' => null];
                        $firstRepayment = $loan->first_repayment_date ? \Carbon\Carbon::parse($loan->first_repayment_date) : null;
                        $timeTaken = $firstRepayment ? \Carbon\Carbon::now()->diffInDays($firstRepayment) : null;
                        $timeTakenLabel = $timeTaken ? $timeTaken . ' days' : 'N/A';
                        if ($timeTaken >= 30) {
                            $months = floor($timeTaken / 30);
                            $days = $timeTaken % 30;
                            $timeTakenLabel = $months . ' month' . ($months > 1 ? 's' : '') . ($days > 0 ? ' ' . $days . ' day' . ($days > 1 ? 's' : '') : '');
                        }
                    @endphp
                    <tr>
                        <td>{{ $loan->id }}</td>
                        <td>
                            @if(!empty($loan->originatingBranch))
                                {{$loan->originatingBranch->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->originatingBranch->district))
                                {{$loan->originatingBranch->district->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->originatingBranch->province))
                                {{$loan->originatingBranch->province->name}}
                            @endif
                        </td>
                        <td>
                            @if($loan->client_type=="client")
                                @if(!empty($loan->client))
                                    @if($loan->client->client_type=="individual")
                                        {{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}
                                    @else
                                        {{$loan->client->full_name}}
                                    @endif
                                @endif
                            @endif
                            @if($loan->client_type=="group")
                                {{$loan->group->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->loanConsultant))
                                {{$loan->loanConsultant->first_name}} {{$loan->loanConsultant->last_name}}
                                <br><small class="text-muted">ID: {{$loan->loanConsultant->id}}</small>
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->created_by))
                                {{$loan->created_by->first_name}} {{$loan->created_by->last_name}}
                            @endif
                        </td>
                        <td>
                            @php $latestInspection = $loan->vehicle->inspections->sortByDesc('inspection_date')->first(); @endphp
                            @if(!empty($latestInspection))
                                {{ $latestInspection->inspector }}
                            @endif
                        </td>
                        <td>
                            @php $latestValuation = $loan->vehicle->valuations->sortByDesc('valuation_date')->first(); @endphp
                            @if(!empty($latestValuation) && !empty($latestValuation->valuator))
                                {{ $latestValuation->valuator->first_name }} {{ $latestValuation->valuator->last_name }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($loan->vehicle->custody) && !empty($loan->vehicle->custody->receiver))
                                {{ $loan->vehicle->custody->receiver->first_name }} {{ $loan->vehicle->custody->receiver->last_name }}
                            @endif
                        </td>
                        <td>{{ number_format($loan->principal, $loan->decimals) }}</td>
                        <td>
                            @if($firstRepayment)
                                {{ $firstRepayment->format('Y-m-d') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-red">{{ $timeTakenLabel }}</span>
                        </td>
                        <td>
                            @if($loan->defaulted == 'yes')
                                <span class="label label-danger">Defaulted</span>
                            @else
                                <span class="label label-warning">{{ ucfirst($loan->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <x-onboarding-progress :status="$status" :loan="$loan" />
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('loans.view'))
                                        <li>
                                            <a href="{{ url('loan/'.$loan->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
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
            "order": [[11, "desc"]],
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
