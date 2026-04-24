@extends('layouts.master')
@section('title')
    Pending Transaction Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Transaction Approvals</h3>
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
                    <th>Balance</th>
                    <th>Date</th>
                    <th>Payment Type</th>
                    <th>Action</th>

                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)

                <?php
                $client_identification = $key->loan->client_id;
                //  $client = \App\Models\Client::where('id',$client_identification)->get();
                $client = \App\Models\Client::find($client_identification);
                $balance = \App\Helpers\GeneralHelper::new_loan_total_balance($key->loan_id);
                $payment_type = ($key->credit == $balance) ? 'Full' : (($key->credit == $balance / 2) ? 'Half' : 'Partial');
                $mismatch = ($key->payment_apply_to == 'full_payment' && $payment_type != 'Full') || ($key->payment_apply_to == 'partial_payment' && $payment_type == 'Full');
                ?>
                    <tr class="clickable-row{{ $mismatch ? ' red-flag' : '' }}" data-loan-id="{{ $key->loan_id }}" data-transaction-id="{{ $key->id }}" data-client-name="{{ $client->first_name ?? '' }} {{ $client->middle_name ?? '' }} {{ $client->last_name ?? '' }}" data-loan-officer="{{ $key->created_by->first_name ?? '' }} {{ $key->created_by->last_name ?? '' }}" data-branch="{{ $key->office->name ?? '' }}" data-amount="{{ number_format($key->credit, 2) }}" data-date="{{ $key->date }}" data-payment-apply-to="{{ $key->payment_apply_to }}" data-balance="{{ number_format($balance, 2) }}" data-payment-type="{{ $payment_type }}" data-loan-principal="{{ number_format($key->loan->principal ?? 0, 2) }}" data-loan-interest-rate="{{ $key->loan->interest_rate ?? 0 }}" data-loan-status="{{ $key->loan->status ?? '' }}" data-mismatch="{{ $mismatch ? 'Yes' : 'No' }}">
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
                         @if(!empty($client->first_name))
                        <td>{{$client->first_name}} {{$client->middle_name}} {{$client->last_name}}</td>
                        @endif
                        <td>{{number_format($key->credit,2)}}</td>
                        <td>{{ number_format($balance, 2) }}</td>
                        <td>{{$key->date}}</td>
                        <td>{{$key->payment_apply_to}} ({{ $payment_type }})</td>
                        <?php
                           $todaysDate = date('Y-m-d');
                        ?>
                        <td>

                            <a href="{{ url('loan/'.$key->loan_id.'/'.$key->id.'/create_transactiontt') }}" onclick="return confirm('Are you sure?')" >
                            <span class="label label-success" >Approve</span>
                                                </a>
                            <a href="{{ url('loan/'.$key->id.'/delete_pending_transaction_fp_pp')}}"  onclick="return confirm('Are you sure?')">
                            <span class="label label-danger style="color:red" >Decline</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .clickable-row { cursor: pointer; }
        .red-flag { background-color: #ffcccc; }
    </style>

    <div class="modal fade" id="transactionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Transaction Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger" id="mismatch-alert" style="display:none;">Mismatch detected: Payment Apply To does not match calculated payment type.</div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Loan Information</h5>
                            <p><strong>Loan ID:</strong> <span id="modal-loan-id"></span></p>
                            <p><strong>Principal:</strong> <span id="modal-loan-principal"></span></p>
                            <p><strong>Interest Rate:</strong> <span id="modal-loan-interest-rate"></span></p>
                            <p><strong>Status:</strong> <span id="modal-loan-status"></span></p>
                            <p><strong>Balance:</strong> <span id="modal-balance"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Transaction Information</h5>
                            <p><strong>Transaction ID:</strong> <span id="modal-transaction-id"></span></p>
                            <p><strong>Amount:</strong> <span id="modal-amount"></span></p>
                            <p><strong>Date:</strong> <span id="modal-date"></span></p>
                            <p><strong>Payment Apply To:</strong> <span id="modal-payment-apply-to"></span></p>
                            <p><strong>Payment Type:</strong> <span id="modal-payment-type"></span></p>
                            <p><strong>Mismatch:</strong> <span id="modal-mismatch"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Client Information</h5>
                            <p><strong>Name:</strong> <span id="modal-client-name"></span></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Staff Information</h5>
                            <p><strong>Loan Officer:</strong> <span id="modal-loan-officer"></span></p>
                            <p><strong>Branch:</strong> <span id="modal-branch"></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

      @if($HasPendingCarryOvers)
<div class="modal fade" id="managerPendingCarryOverModal"
     tabindex="-1"
     data-backdrop="static"
     data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-danger">
                <h4 class="modal-title">Pending Carry Overs</h4>
            </div>

            <div class="modal-body text-center">
                <p>
                    You have <strong>pending carry over requests</strong> awaiting your action.
                </p>

                <p>
                    Please clear all pending carry overs before continuing to use the system.
                </p>

                <p>
                    <a href="{{ url('user/carry_over_approvals') }}" class="btn btn-primary">
                        View Pending Carry Overs
                    </a>
                </p>
            </div>

        </div>
    </div>
</div>
@endif

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

        // function log_console() {
        //     console.log
        //         ("GeeksforGeeks is a portal for geeks.");
        // }
    </script>
@endsection
