@extends('layouts.master')
@section('title', 'Advance Details')
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Advance Details</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 40%;">Advance ID</th>
                            <td>{{ $advance->id }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $advance->first_name . ' ' . $advance->last_name }}</td>
                        </tr>
                        <tr>
                            <th>Advance Amount</th>
                            <td>{{ $advance->amount }}</td>
                        </tr>
                        <tr>
                            <th>Installments</th>
                            <td>{{ $advance->installments }}</td>
                        </tr>
                        <tr>
                            <th>Amount per Installment</th>
                            <td>{{ $advance->installment_amount }}</td>
                        </tr>
                        <tr>
                            <th>Date Approved</th>
                            <td>{{ $advance->date_approved }}</td>
                        </tr>  
                        <tr>      
                            <th>Approved by</th>
                            <td>{{ $advance->approved_by_id }}</td>
                        </tr>
                        <tr>
                            <th>Purpose</th>
                            <td>{{ $advance->purpose }}</td>
                        </tr>
                        <tr>
                            <th>Next Repayment Date</th>
                            <td>{{ $advance->expected_repayment_dates }}</td>
                        </tr>
                        <tr>
                            <th>Amount Left</th>
                            <td>{{ $advance->remaining_amount }}</td>
                        </tr>  
                    </table>
                </div>
            </div>
        </div>
        <!----->
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Notes</h3>
                </div>
                <div class="box-body">
                    {!! $advance->notes !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            $('.table').DataTable({
                "paging": false,
                "searching": false,
                "info": false,
            });
        });
    </script>
@endsection
