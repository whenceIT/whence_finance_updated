@extends('layouts.master')

@section('title')
    Fund Movement Approvals
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Pending Fund Movement Approvals</h3>
        </div>

        <div class="box-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Branch</th>
                                <th>Added by</th>
                                <th>Type</th>
                                <th>From Account</th>
                                <th>Payee / Destination</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Status</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fund_movements as $movement)
                                <tr>
                                    <td>{{ $movement->transaction_date }}</td>
                                    <td>{{ optional($movement->office)->name }}</td>
                                    <td>{{($movement->user)->first_name}}
                                      {{($movement->user)->last_name}}
                                    </td>
                                    <td>
                                        <span class="label label-info">
                                            {{ ucwords(str_replace('_', ' ', $movement->movement_type)) }}
                                        </span>
                                    </td>
                                    <td>{{ ($movement->account)->name }}</td>
                                    <td>
                                        @if(!empty($movement->payee_name))
                                            {{ $movement->payee_name }}
                                        @elseif(!empty($movement->destination_account))
                                            {{ $movement->destination_account }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($movement->amount, 2) }}</strong></td>
                                    <td>{{ $movement->reference_no ?? '-' }}</td>
                                    <td>
                                        <span class="label label-warning">
                                            {{ ucfirst($movement->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ url('accounting/' . $movement->id . '/show_fund_movements') }}" class="btn btn-xs btn-default">
                                            View
                                        </a>

                                 
                                        <form action="{{ url('accounting/' . $movement->id . '/approve_fund') }}"
                                              method="POST"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Approve this fund movement?');">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-xs btn-success">
                                                Approve
                                            </button>
                                        </form>

                                        <form action="{{ url('accounting/' . $movement->id . '/reject_fund') }}"
                                              method="POST"
                                              style="display:inline-block;"
                                              onsubmit="return confirm('Reject this fund movement?');">
                                            {{ csrf_field() }}
                                            <button type="submit" class="btn btn-xs btn-danger">
                                                Reject
                                            </button>
                                        </form>
                                  


                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

        </div>
    </div>
@endsection