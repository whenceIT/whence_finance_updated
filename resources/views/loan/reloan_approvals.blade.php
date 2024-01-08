@extends('layouts.master')
@section('title')
    Pending Reloan Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Reloan Approvals</h3>
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
                    <th>Date</th>
                    <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                
                <?php
                $client_identification = $key->loan->client_id;
              //  $client = \App\Models\Client::where('id',$client_identification)->get();
                $client = \App\Models\Client::find($client_identification);
                ?>
                    <tr>
                        <td><a href="{{ url('loan/'.$key->loan_id.'/show') }}" data-toggle="tooltip" title="Click to view">{{ $key->loan_id }}</a></td>
                        <td>
                        @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                        @if(!empty($key->created_by))
                                {{$key->created_by->first_name}}  {{$key->created_by->last_name}} 
                            @endif
                        </td>
                        <td>{{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}</td>
                        <td>{{number_format($key->credit,2)}}</td>
                        <td>{{$key->date}}</td>
                        <td>
                           
                        <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('clients.create'))
                    <a href="{{ url('client/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add',1) }} {{ trans_choice('general.client',1) }}
                    </a>
                @endif
            </div>

            <a href="{{ url('loan/'.$key->loan_id.'/'.$key->id.'/create_reloan') }}" onclick="return confirm('Are you sure?')" >
                            <span class="label label-success" >Approve</span>
                                                </a>
                            <a href="{{ url('loan/'.$key->id.'/delete_pending_transaction')}}"  onclick="return confirm('Are you sure?')">
                            <span class="label label-danger style="color:red" >Decline</span>
                            </a>
                              
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <button onclick="log_console()">
        View Message
    </button>
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


        // function log_console() {
        //     console.log
        //         ("GeeksforGeeks is a portal for geeks.");
        // }
    </script>
@endsection
