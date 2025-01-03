@extends('layouts.master')

@section('title')
    Defaulted Clients
@endsection

<style>
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

<?php 
$todaysDate = date('Y-m-d');
?>

<div class="box box-warning">
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

            <p style="text-align: center;">Defaulted loans under each LC.
                
            </p>
    
            
            <div class="d-flex justify-content-between align-items-center mb-3" style="margin-bottom: -50px">
                <form method="GET" action="{{ route('performance_metrics.defaulted') }}" class="form-inline">
                    <div class="form-group mr-2">
                        <label for="office_id" class="mr-1">Filter by Branch:</label>
                        <select name="office_id" id="office_id" class="form-control">
                            <option value="">All Branches</option>
                            @if($user_role == 1)
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 4)
                                @foreach($offices->where('id', $user_branch) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @elseif($user_role == 6)
                                @foreach($offices->where('province_id', $user_province) as $office)
                                    <option value="{{ $office->id }}" {{ $officeId == $office->id ? 'selected' : '' }}>{{ $office->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </form>    
            </div>

            <
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped table-hover" id="data-table">
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Branch</th>
                            <th>Approved Date</th>
                            <th>Loan Amount</th>
                            <th>Loan Officer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loanConsultants as $user)
                            <?php
                                $hasDefaulters = false;
                            ?>
                            @foreach($user->loan as $loan)
                                @if($loan->status == 'disbursed' && $loan->first_repayment_date < $todaysDate)
                                    <?php
                                        $hasDefaulters = true;
                                    ?>
                                    <tr>
                                        <td>{{ $loan->client->first_name }} {{ $loan->client->last_name }}</td> 
                                        <td>{{ $user->office ? $user->office->name : 'N/A' }}</td>
                                        <td>{{ $loan->approved_date }}</td>
                                        <td>{{ number_format($loan->principal, 2) }}</td>
                                        <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No defaulters found.</td>
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
                columnDefs: [
                { width: '20%', targets: 0 }, // Client Name
                { width: '25%', targets: 1 }, // Branch
                { width: '15%', targets: 2 }, // Approved Date
                { width: '15%', targets: 3 }, // Loan Amount
                { width: '25%', targets: 4 }  // Loan Officer
            ],
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

