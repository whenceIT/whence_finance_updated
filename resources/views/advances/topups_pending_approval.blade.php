@extends('layouts.master')
@section('title')
Pending Top-Up Requests
@endsection

@section('content')

@if($cashBalance < 20000)

<div class="box box-danger">
    <div class="box-body text-center" style="padding: 30px 20px;">
        <i class="fa fa-exclamation-triangle text-danger"
           style="font-size: 48px; margin-bottom: 15px;"></i>

        <h4 style="font-weight: bold; margin-bottom: 10px;">
            Insufficient Cash Balance
        </h4>

        <p style="font-size: 16px; color: #555; margin-bottom: 0;">
            Your cash balance is below the required minimum of
            <strong>K20,000</strong>. You cannot approve any advance top-up at this time.
        </p>
    </div>
</div>

@else



<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Pending Advance Top-Up Requests</h3>
    </div>
    <div class="box-body table-responsive">
        @if ($advance_topups->isEmpty())
            <p>No pending TopUp advances found.</p>
        @else
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                    <tr>
                        <th>Advance ID</th>
			<th>Name</th>
			<th>Branch</th>
                        <th>Top-Up Amount</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($advance_topups as $topUp)
                        <tr>
                            <td>{{ $topUp->advance->id }}</td>
			    <td>{{ $topUp->first_name }} {{ $topUp->last_name }}</td>
<td>{{ $topUp->office->name}}</td>
                            <td>{{ $topUp->top_up_amount }}</td>
                            <td>{{ $topUp->top_up_date }}</td>
                            <td>
                                @if($topUp->status === 'pending')
                                    <div class="btn-group" role="group" aria-label="Basic example">
                                        <form action="{{ route('topups.approve', $topUp->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-xs" style="padding: 1px 2px; font-size: 12px;" onclick="return confirm('Are you sure you want to approve this TopUp?')">Approve</button>
                                        </form>
                                        
                                        <form action="{{ route('topups.decline', $topUp->id) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-xs" style="padding: 1px 2px; font-size: 12px;" onclick="return confirm('Are you sure you want to decline this TopUp?')">Decline</button>
                                        </form>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif    
    </div>
</div>

@endif
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
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
                {"orderable": false, "targets": [0, 2, 6, 7, 9, 10]},
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
    });
</script>
@endsection

