@extends('layouts.master')
@section('title')
   Client App Loan Applications
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Client App Loan Applications</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>Client ID</th>
                    <th>Client Name</th>
                    <th>Branch</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Payment ID</th>
                    <th>Loan Purpose</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)

            
                    <tr>
                        <td>
                            {{$key->client_id}}
                        </td>
                        <td>
                            {{$key->client_name}}
                        </td>
                        <td>
                        @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                            {{$key->amount}}
                        </td>
                        <td>
                              {{$key->payment_method}}
                        </td>
                        <td>
                            {{$key->payment_id}}
                        </td>
                        <td>
                            {{$key->loan_purpose}}
                        </td>
                            <td>{{$key->date}}</td>
                
                        <td>

 <a href="javascript:void(0)"
   class="label label-success approve-btn"
   data-client-id="{{ $key->client_id }}"
   onclick="return confirm('Are you sure?')">
   Approve
</a>
                       
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
  $('#managerPendingCarryOverModal').modal('show');
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

        $('#data-table').on('click', '.clickable-row', function() {
            $('#modal-loan-id').text($(this).data('loan-id'));
            $('#modal-transaction-id').text($(this).data('transaction-id'));
            $('#modal-client-name').text($(this).data('client-name'));
            $('#modal-loan-officer').text($(this).data('loan-officer'));
            $('#modal-branch').text($(this).data('branch'));
            $('#modal-amount').text($(this).data('amount'));
            $('#modal-date').text($(this).data('date'));
            $('#modal-payment-apply-to').text($(this).data('payment-apply-to'));
            $('#modal-balance').text($(this).data('balance'));
            $('#modal-payment-type').text($(this).data('payment-type'));
            $('#modal-loan-principal').text($(this).data('loan-principal'));
            $('#modal-loan-interest-rate').text($(this).data('loan-interest-rate'));
            $('#modal-loan-status').text($(this).data('loan-status'));
            $('#modal-mismatch').text($(this).data('mismatch'));
            if ($(this).data('mismatch') == 'Yes') {
                $('#mismatch-alert').show();
            } else {
                $('#mismatch-alert').hide();
            }
            $('#transactionModal').modal('show');
        });


        $(document).on('click', '.approve-btn', function (e) {
    e.preventDefault();

    if (!confirm('Are you sure?')) return;

    let client_id = $(this).data('client-id');
    let loan_product_id = 2; // fixed as requested

    if (client_id !== "" && loan_product_id !== "") {
        document.location = "{{ url('loan/create_client_loan') }}/" + client_id + "/" + loan_product_id;
    }
});

        // function log_console() {
        //     console.log
        //         ("GeeksforGeeks is a portal for geeks.");
        // }
    </script>
@endsection
