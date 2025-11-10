@extends('layouts.master')
@section('title')
    Pending Top Up Approvals
@endsection
@section('content')
 <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Top Up Approvals </h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Loan</th>
                    <th>Branch</th>
                    <th>Loan Officer</th>
                    <th>Client</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                
                <?php
if (isset($key->loan) && $key->loan !== null) {
    $client_identification = $key->loan->client_id;
    $client = \App\Models\Client::find($client_identification);
} else {
    $client_identification = null;
    $client = null;
}

$loan_officer = \App\Models\User::find($key->created_by);
?>

                    <tr>
                            @if($key->status == 'pending')
                        <td><a href="{{ url('loan/'.$key->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">{{ $key->loan_id }}</a></td>
                        <td>
                        @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                        @if(!empty($loan_officer->first_name))
                                {{$loan_officer->first_name}}
                            @endif

                               @if(!empty($loan_officer->last_name))
                                {{$loan_officer->last_name}} 
                            @endif
                        </td>
                        @if(!empty($client->first_name))
                        <td>{{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}</td>
                        @endif
                        <td>{{number_format($key->amount,2)}}</td>
                        <td>{{$key->status}}</td>
                        <td>{{$key->date}}</td>
                        <?php
                           $todaysDate = date('Y-m-d');
                        ?>

<td>
<a href="{{ url('loan/'.$key->loan_id.'/'.$key->id.'/approve_top_up') }}" onclick="return confirm('Are you sure?')" >
                            <span class="label label-success" >Approve</span>
                                                </a>
                            <a href="{{ url('loan/'.$key->id.'/decline_top_up')}}"  onclick="return confirm('Are you sure?')">
                            <span class="label label-danger style="color:red" >Decline</span>
                            </a>
</td>
                    



@endif
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
            "order": [[5, "desc"]],
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
