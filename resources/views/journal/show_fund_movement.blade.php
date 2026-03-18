@extends('layouts.master')

@section('title')
    Fund Movement Details
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Fund Movement Details</h3>

            <div class="pull-right">
                <span class="label 
                    @if($movement->status == 'approved') label-success 
                    @elseif($movement->status == 'rejected') label-danger 
                    @elseif($movement->status == 'submitted') label-warning 
                    @else label-default 
                    @endif">
                    {{ ucfirst($movement->status) }}
                </span>
            </div>
        </div>

        <div class="box-body">

            {{-- SECTION 1 --}}
            <h4 style="color:#3c8dbc;">1. Movement Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="25%">Movement Type</th>
                    <td>{{ ucwords(str_replace('_',' ', $movement->movement_type)) }}</td>

                    <th>Amount</th>
                    <td><strong>{{ number_format($movement->amount, 2) }}</strong></td>
                </tr>
                <tr>
                    <th>Transaction Date</th>
                    <td>{{ $movement->transaction_date }}</td>

                    <th>Reference</th>
                    <td>{{ $movement->reference_no ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Payment Method</th>
                    <td>{{ ucfirst(str_replace('_',' ', $movement->payment_method)) }}</td>

                    <th>Branch</th>
                    <td>{{ optional($movement->office)->name }}</td>
                </tr>
            </table>

            <hr>

            {{-- SECTION 2 --}}
            <h4 style="color:#3c8dbc;">2. Account Details</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="25%">From Account</th>
                    <td>{{ ($movement->account)->name }}</td>

                    <th>To Account</th>
                    <td>{{ $movement->destination_account ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Payee / Recipient</th>
                    <td>{{ $movement->payee_name ?? '-' }}</td>

                    <th>Expense Category</th>
                    <td>{{ $movement->expense_category ?? '-' }}</td>
                </tr>
            </table>

            <hr>

            {{-- SECTION 3 --}}
            <h4 style="color:#3c8dbc;">3. Description</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="25%">Title</th>
                    <td>{{ $movement->title ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Description</th>
                    <td>{{ $movement->description ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Internal Remarks</th>
                    <td>{{ $movement->remarks ?? '-' }}</td>
                </tr>
            </table>

            <hr>

            {{-- SECTION 4 --}}
            <!-- <h4 style="color:#3c8dbc;">4. Supporting Document</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="25%">Document</th>
                    <td>
                        @if($movement->attachment)
                            <a href="{{ asset($movement->attachment) }}" target="_blank" class="btn btn-xs btn-primary">
                                View Attachment
                            </a>
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Document Note</th>
                    <td>{{ $movement->document_note ?? '-' }}</td>
                </tr>
            </table> -->

            <hr>

            {{-- SECTION 5 --}}
            <h4 style="color:#3c8dbc;">5. Approval Information</h4>
            <table class="table table-bordered">
                <tr>
                    <th width="25%">Created By</th>
                  
                     <td>{{($movement->user)->first_name}}
                                      {{($movement->user)->last_name}}
                                    </td>

                    <th>Created At</th>
                    <td>{{ $movement->created_at }}</td>
                </tr>
             

                @if($movement->status == 'rejected')
                <tr>
                    <th>Rejection Reason</th>
                    <td colspan="3">{{ $movement->rejection_reason }}</td>
                </tr>
                @endif
            </table>

        </div>

        <div class="box-footer">
            <a href="{{ url('accounting/pending_fund_movements') }}" class="btn btn-default">Back</a>

             @if(Sentinel::hasAccess('settings'))
            @if($movement->status == 'submitted')
                <form action="{{ url('accounting/' . $movement->id . '/approve_fund') }}" method="POST" style="display:inline;">
                    {{ csrf_field() }}
                    <button class="btn btn-success">Approve</button>
                </form>

                <button class="btn btn-danger" data-toggle="modal" data-target="#rejectModal">
                    Reject
                </button>
            @endif
            @endif
        </div>
    </div>

    {{-- Reject Modal --}}
    <div class="modal fade" id="rejectModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ url('accounting/' . $movement->id . '/reject_fund') }}">
                    {{ csrf_field() }}

                    <div class="modal-header">
                        <h4 class="modal-title">Reject Fund Movement</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group">
                            <label>Reason</label>
                            <textarea name="rejection_reason" class="form-control" required></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-danger">Reject</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection