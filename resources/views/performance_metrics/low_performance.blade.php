@extends('layouts.master')

@section('title')
    Low Performance
@endsection

<style>
    .fixed-width-name {
        width: 150px;
    }
    .fixed-width-branch {
        width: 150px;
    }
    .fixed-width-target {
        width: 100px;
    }
    .table-container {
        overflow-x: auto;
    }
    .container {
        max-width: 1200px; 
        margin: 0 auto; 
    }

    .table-responsive {
        overflow-x: auto; 
    }

    #data-table {
        width: 100%; 
    }
</style>

@section('content')

<div class="box box-danger">
    <div class="box-header with-border">
    <div style="display: flex; align-items: center; justify-content: center; padding-bottom: 10px; ">
                <a href="{{ route('performance_metrics.targets') }}" style="margin: 10px;">
                <span class="label label-success" style="font-size: 15px;">Targets</span>
                </a>

                <a href="{{ route('performance_metrics.uncollected')}}" style="margin: 10px;">
                <span class="label label-primary" style="font-size: 15px;">Staff Uncollected amounts</span>
                </a>

                <a href="{{ route('performance_metrics.low_performance')}}" style="margin: 10px;">
                <span class="label label-danger" style="font-size: 15px;">Low Performance</span>
                </a>

                <a href="{{ route('performance_metrics.defaulted')}}" style="margin: 10px;">
                <span class="label label-warning" style="font-size: 15px;">Staff Defaulted Loans</span>
                </a>
            </div>
            <p style="text-align: center;">LC's failing to disburse more than 20, 000 in current cycle.
            </p>

            
            <div class="d-flex justify-content-center align-items-center mb-3" style="margin-bottom: -50px">
                <form method="GET" action="{{ route('performance_metrics.low_performance') }}" class="form-inline">
                    <div class="form-group">
                        <label for="office_id">Filter by Branch:</label>
                        <select name="office_id" id="office_id" class="form-control">
                            <option value="">All Branches</option>
                            @if($user_role == 1)
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            @elseif($user_role == 4)
                                @foreach($offices->where('id', $user_branch) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            @elseif($user_role == 6)
                                @foreach($offices->where('province_id', $user_province) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>
            </div>

            
            <div class="box-body table-responsive">
                <table id="data-table" class="table table-bordered table-striped table-hover no-footer">
                    <thead>
                        <tr>
                            <th>Consultant Name</th>
                            <th>Amount Disbursed this Cycle</th>
                            <th>{{ trans_choice('general.action', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($filteredLoans as $item)
                        <tr>
                            <td>{{ $item['user']->first_name }} {{ $item['user']->last_name }}</td>
                            <td>{{ number_format($item['total_loans_cycle']) }}</td>
                            <td class="text-center">    
                                <div class="btn-group">
                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown" aria-expanded="false">
                                        <i class="fa fa-navicon"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        <li>
                                            <a href="{{url('user/'.$item['user']->id.'/staff_info')}}">
                                                <i class="fa fa-search"></i> {{ trans_choice('general.detail',2) }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="3">No records found.</td>
                            </tr>
                        @endforelse
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
        $(document).ready(function() {
            $('#data-table').DataTable({
                dom: 'frtip',
                "paging": true,
                "lengthChange": false,
                "displayLength": 20,
                "searching": true,
                "ordering": true,
                "autoWidth": false,
                
                "language": {
                    "search": "{{ trans('general.search') }}",
                    "paginate": {
                        "first": "{{ trans('general.first') }}",
                        "last": "{{ trans('general.last') }}",
                        "next": "{{ trans('general.next') }}",
                        "previous": "{{ trans('general.previous') }}"
                    }
                },
                responsive: false
            });
        });
    </script>

@endsection


