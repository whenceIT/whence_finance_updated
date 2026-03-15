@extends('layouts.master')

<?php


 $loanTransaction = \App\Models\LoanTransaction::where('loan_id', $loan->id)->orderBy('created_at', 'desc');
$loan_allocation = \App\Helpers\GeneralHelper::loan_items($loan->id);



$repaymentAmount = $loan->principal+$loan_allocation['interest'];
$balance = $repaymentAmount;

?>


@section('title')
    {{ trans_choice('general.loan',1) }} Statment
@endsection
@section('content')



    <div class="row">
             <table class="table table-striped table-bordered">
                                    <tbody>
   @if($loan->client_type=="client")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}</a>

                                                @else
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->full_name}}</a>
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif

                                        <tr>
                                            <th class="table-bold-loan">Principal</th>

                                            <td>
                                                {{number_format($loan->principal,2)}}
                                            </td>
                                        </tr>
                                             <tr>
                                            <th class="table-bold-loan">Interest</th>

                                            <td>
                                                {{number_format($loan_allocation['interest'],2)}}
                                            </td>
                                        </tr>


                                    </tbody>
                                </table>


        <div class="col-md-12">
            <div class="nav-tabs-custom">
          
                <div class="tab-content">
               
          <div class="row">
                                
                                    <div class="col-md-12 table-responsive">
                                        <table class="pretty displayschedule" 
                                               style="margin-top: 20px;">

                                             <thead>
                                            <tr>
                                                <th>Date</th>
                                                <th>Repayment Amount</th>
                                                <th>Loan Balance</th>
                                            </tr>
                                            </thead>

                                            <tbody>
                                            @foreach($loanTransaction->get() as $transaction)

                                                <?php 
                                                if ($transaction->transaction_type !== 'repayment') {
                                                    continue;
                                                }
                                                $balance = $balance-$transaction->credit;
                                                ?>

                                                <tr>
                                                    <td>{{ $transaction->date }}</td>
                                                    <td>{{ $transaction->credit }}</td>
                                                    <td>{{ $balance}}</td>
                                                </tr>
                                            @endforeach

                                        </table>
                                    </div>

                                    </div>
               
                
                 
             
               
                </div>
            </div>
        </div>
    </div>



@endsection
@section('footer-scripts')
    <script>
        if ($("#is_client").val() == 1) {
            $("#clients_div").show();
            $("#new_client_div").hide();
            $("#guarantor_client_id").attr('required', 'required');
            $("#guarantor_first_name").removeAttr('required');
            $("#guarantor_last_name").removeAttr('required');
            $("#guarantor_mobile").removeAttr('required');
        } else if ($("#is_client").val() == 0) {
            $("#clients_div").hide();
            $("#new_client_div").show();
            $("#guarantor_client_id").removeAttr('required');
            $("#guarantor_first_name").attr('required', 'required');
            $("#guarantor_last_name").attr('required', 'required');
            $("#guarantor_mobile").attr('required', 'required');
        } else {
            $("#clients_div").hide();
            $("#clients_div").hide();
        }
        $("#is_client").change(function (e) {
            if ($("#is_client").val() == 1) {
                $("#clients_div").show();
                $("#new_client_div").hide();
                $("#guarantor_client_id").attr('required', 'required');
                $("#guarantor_first_name").removeAttr('required');
                $("#guarantor_last_name").removeAttr('required');
                $("#guarantor_mobile").removeAttr('required');
            } else if ($("#is_client").val() == 0) {
                $("#clients_div").hide();
                $("#new_client_div").show();
                $("#guarantor_client_id").removeAttr('required');
                $("#guarantor_first_name").attr('required', 'required');
                $("#guarantor_last_name").attr('required', 'required');
                $("#guarantor_mobile").attr('required', 'required');
            } else {
                $("#clients_div").hide();
                $("#clients_div").hide();
            }
        });
        $('#view_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/note') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/note') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        })
        $('#view_collateral').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/collateral') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_collateral').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/collateral') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#view_guarantor').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/guarantor') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_guarantor').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/guarantor') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $("#add_document_form").validate();
        $("#add_collateral_form").validate();
        $("#add_guarantor_form").validate();
        $("#add_note_form").validate();
        $("#approve_loan_form").validate();
        $("#decline_loan_form").validate();
        $("#add_charge_form").validate();
        $("#waive_interest_form").validate();
        $("#write_off_loan_form").validate();
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
        $('#repayments-data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[1, "asc"]],
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
