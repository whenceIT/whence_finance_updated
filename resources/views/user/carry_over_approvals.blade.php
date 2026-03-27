@extends('layouts.master')
@section('title')
    Carry Over Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Carry Over Approvals</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                  
                    <th>Loan Officer</th>
                    <th>Office</th>
                    <th>Carry Amount</th>
                    <th>Cycle Start Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                
                    <tr>
                        <td>
                        @if(!empty($key->created_by))
                            {{$key->created_by->first_name}}  {{$key->created_by->last_name}} 
                        @else
                            {{$key->full_name ?? 'N/A'}}
                        @endif
                        </td>
                        <td>{{$key->office->name ?? 'N/A'}}</td>
                        <td>{{number_format($key->amount ?? 0,2)}}</td>
                        <td>{{$key->cycle_date ?? 'N/A'}}</td>
                        <td>{{$key->status ?? 'Unknown'}}</td>
                        <td>


           <a href="#"
   class="approve-carry-over"
   data-url="{{ url('user/'.$key->id.'/approve_carry_over') }}">
    <span class="label label-success">Approve</span>
</a>

                            <a href="{{ url('user/'.$key->id.'/decline_carry_over')}}"  onclick="return confirm('Are you sure?')">
                            <span class="label label-danger style="color:red" >Decline</span>
                            </a>
</td>
         
                    




                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="approveCarryOverModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">Confirm Approval</h4>
            </div>

            <div class="modal-body">
                <p>
                    By clicking <strong>Approve</strong>, you confirm that the carry over amount has been reviewed
                    and is correct and accurate.
                </p>

                <p>
                    You acknowledge that this approval may affect system targets and related outcomes, and that
                    <strong>you will be held responsible</strong> for approving this carry over.
                </p>

                <p class="text-danger">
                    Please ensure all details are correct before proceeding.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>

                <a href="#" id="confirmApproveCarryOver" class="btn btn-danger">
                    Approve
                </a>
            </div>

        </div>
    </div>
</div>


@endsection
@section('footer-scripts')
    <script>


let approveUrl = '';

$('.approve-carry-over').on('click', function (e) {
    e.preventDefault();
    approveUrl = $(this).data('url');
    $('#confirmApproveCarryOver').attr('href', approveUrl);
    $('#approveCarryOverModal').modal('show');
});


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
