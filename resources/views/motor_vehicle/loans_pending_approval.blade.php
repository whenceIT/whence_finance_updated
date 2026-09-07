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
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th>{{ trans_choice('general.account',1) }}#</th>
                        <th>{{ trans_choice('general.branch',1) }}</th>
                        <th>{{ trans_choice('general.client',1) }}</th>
                        <th>{{ trans_choice('general.proposed',1) }} {{ trans_choice('general.amount',1) }}</th>
                        <th>{{ trans_choice('general.created_at',1) }}</th>
                        <th>{{ trans_choice('general.product',1) }}</th>
                        <th>Onboarding Progress</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                <tr>
                <td>{{ $key->id }}</td>
                <td>
                    @if(!empty($key->office))
                        {{$key->office->name}}
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
                <td>{{ number_format($key->principal, $key->decimals) }}</td>
                <td>{{ $key->created_date }}</td>
                <td>
                @if(!empty($key->loan_product))
                    {{$key->loan_product->name}}
                @endif
			</td>
            <td>
                {{$key->status}}
            </td>
            <td>
            @php $status = $statuses[$key->id] ?? ['kyc_completed' => null, 'compliance_screening_completed' => null]; @endphp
                    <div class="onboarding-progress" style="display: flex; align-items: center; gap: 8px;">
                            <div style="text-align: center;">
                                @if($status['kyc_completed'] !== null)
                                    <a href="{{ route('clients.edit-kyc', [$key->client_id, $key->id]) }}" style="text-decoration: none;" title="Go to KYC">
                                        @if($status['kyc_completed'] === true)
                                            <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                                        @else
                                            <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                                        @endif
                                    </a>
                                @else
                                    <span style="color: #777; font-size: 12px;">N/A</span>
                                @endif
                                <div style="font-size: 10px; color: #666;">KYC</div>
                            </div>
                            <div style="width: 1px; height: 25px; background: #ccc;"></div>
                            <div style="text-align: center;">
                                @if($status['compliance_screening_completed'] !== null)
                                    <a href="{{ route('motor-vehicle-loans.compliance-screening', $key->id) }}" style="text-decoration: none;" title="Go to Compliance Screening">
                                        @if($status['compliance_screening_completed'] === true)
                                            <i class="fa fa-check-circle" style="color: #00a65a; font-size: 18px;"></i>
                                        @else
                                            <i class="fa fa-times-circle" style="color: #dd4b39; font-size: 18px;"></i>
                                        @endif
                                    </a>
                                @else
                                    <span style="color: #777; font-size: 12px;">N/A</span>
                                @endif
                                <div style="font-size: 10px; color: #666;">Compliance</div>
                            </div>
                        </div>
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
            "order": [[4, "desc"]],
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
    <style>
        .onboarding-progress a {
            cursor: pointer;
            transition: transform 0.2s;
            display: inline-block;
        }
        .onboarding-progress a:hover {
            transform: scale(1.2);
        }
        .onboarding-progress a:hover i {
            filter: brightness(0.8);
        }
    </style>
@endsection