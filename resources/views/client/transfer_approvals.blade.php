@extends('layouts.master')
@section('title')
    Pending Client Transfer Approvals
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Client Transfer Approvals</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Current Office</th>
                    <th>Proposed New Office</th>
                    <th>Initiated By</th>
                    <th>Date</th>
                    <th>Action</th>

                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)

                    <tr>
                        <td>{{$key->id}}</td>
                           <td>{{$key->client->first_name}} {{$key->client->last_name}}</td>
                         <td>{{$key->oldOffice->name}}</td>
                       
                        <td>{{$key->newOffice->name}}</td>
                       <td>{{$key->doneBy->first_name}} {{$key->doneBy->last_name}}</td>
                     
                   
                        <td>{{$key->date}}</td>
                        <td>

                            <a href="{{ url('client/'.$key->id.'/approve_transfer') }}" onclick="return confirm('Are you sure?')" >
                            <span class="label label-success" >Approve</span>
                                                </a>

                            <a href="{{ url('client/'.$key->id.'/delete_transfer')}}"  onclick="return confirm('Are you sure?')">
                            <span class="label label-danger style="color:red" >Decline</span>
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