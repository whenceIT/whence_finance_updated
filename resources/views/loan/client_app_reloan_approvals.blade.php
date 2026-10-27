@extends('layouts.master')
@section('title')
   Pending Client App Reloans
@endsection
@section('content')

  <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Client App Reloans</h3>
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
                        <?php
                           $todaysDate = date('Y-m-d');
                        ?>
                     
                        <td>
                        
                          <a href="{{ url('loan/'.$key->loan_id.'/'.$key->id.'/approve_client_app_reloan') }}" onclick="return confirm('Are you sure?')" >
                                        <span class="label label-success" >Approve</span>
                                                            </a>
                                      
<a href="#"
   class="label label-danger"
   style="color: white; cursor: pointer;"
   data-toggle="modal"
   data-target="#declineReloanModal"
   data-url="{{ url('loan/'.$key->id.'/decline_client_app_reloan') }}"
   data-amount="{{ number_format($key->credit, 2) }}"
   onclick="setDeclineDetails(this); return false;">
    Decline
</a>

                                          
                                    </td> 
                    </tr>
                @endforeach
                </tbody>
            </table>

            
<div class="modal fade" id="declineReloanModal" tabindex="-1" role="dialog" aria-labelledby="declineReloanTitle">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; border: none;">

            <div class="modal-header" style="background: #fff3cd; border-bottom: 1px solid #ffeeba; padding: 20px;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

                <h4 class="modal-title" id="declineReloanTitle" style="color: #856404; font-weight: bold;">
                    <i class="fa fa-exclamation-triangle"></i>
                    Confirm Reloan Decline
                </h4>
            </div>

            <div class="modal-body" style="padding: 25px;">
                <p style="font-size: 15px; color: #333;">
                    Are you sure you want to decline this client app reloan?
                </p>

                <div style="background: #fff3cd; border-left: 4px solid #f0ad4e; padding: 15px; border-radius: 5px; margin: 20px 0;">
                    <strong style="color: #856404;">
                        <i class="fa fa-exclamation-circle"></i> Important Notice
                    </strong>

                    <p style="margin: 8px 0 0; color: #664d03;">
                        If you proceed, the amount already paid by the client will be added to their loan as a <strong>part payment</strong>.
                    </p>

                    <p style="margin: 12px 0 0; color: #664d03;">
                        Amount to be processed: <strong id="declineReloanAmount">K0.00</strong>
                    </p>
                </div>

                <p style="color: #666; font-size: 13px; margin-bottom: 0;">
                    Please ensure you understand the consequences before proceeding.
                </p>
            </div>

            <div class="modal-footer" style="padding: 15px 25px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Cancel
                </button>

                <a href="#" id="confirmDeclineReloan" class="btn btn-danger">
                    <i class="fa fa-times"></i> Yes, Decline Reloan
                </a>
            </div>

        </div>
    </div>
</div>

            <button onclick="log_console()">
        View Message
    </button>
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


        
function setDeclineDetails(element) {
    var url = $(element).data('url');
    var amount = $(element).data('amount');

    $('#declineReloanAmount').text('K' + amount);
    $('#confirmDeclineReloan').attr('href', url);
}

$('#declineReloanModal').on('hidden.bs.modal', function () {
    $('#confirmDeclineReloan').attr('href', '#');
});




    </script>
@endsection
