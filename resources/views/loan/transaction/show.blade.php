@extends('layouts.master')
@section('title')
    {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.detail',2) }}
@endsection
@section('content')
    <style>
        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #002c04;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .receipt-logo img {
            height: 50px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: 700;
            color: #002c04;
            margin: 0;
        }
        .receipt-subtitle {
            font-size: 14px;
            color: #666;
            margin: 0;
        }
        .receipt-info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .receipt-info-table th {
            text-align: left;
            padding: 8px 12px;
            background: #f8f9fa;
            font-weight: 600;
            font-size: 13px;
            color: #555;
            width: 20%;
            border-bottom: 1px solid #dee2e6;
        }
        .receipt-info-table td {
            padding: 8px 12px;
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
        }
        .receipt-info-table td strong {
            color: #002c04;
        }
        .receipt-amount-box {
            text-align: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 2px dashed #002c04;
        }
        .receipt-amount-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        .receipt-amount-value {
            font-size: 32px;
            font-weight: 700;
            color: #002c04;
        }
        .receipt-amount-balance {
            font-size: 20px;
            font-weight: 600;
            color: #d32f2f;
        }
        .receipt-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
        .receipt-actions .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn-print {
            background: #002c04;
            color: white;
        }
        .btn-pdf {
            background: #d32f2f;
            color: white;
        }
        .receipt-footer {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }
    </style>

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{ trans_choice('general.transaction',1) }}  {{ trans_choice('general.detail',2) }}
            </h3>
            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>
        <div class="box-body">
            <div class="receipt-container">
                <div class="receipt-header">
                    <div class="receipt-logo">
                        <img src="https://whencefinancesystem.com/images/w/logo.jpg" alt="Whence Financial Systems Logo">
                    </div>
                    <div>
                        <p class="receipt-title">Whence Financial Services</p>
                        <p class="receipt-subtitle">Loan Transaction Receipt</p>
                    </div>
                    <div style="text-align: right;">
                        <p style="margin: 0; font-size: 12px; color: #666;">Transaction #</p>
                        <p style="margin: 0; font-weight: bold; font-size: 16px; color: #002c04;">{{ $loan_transaction->id }}</p>
                    </div>
                </div>

                <table class="receipt-info-table">
                    <tr>
                        <th>Loan Number</th>
                        <td>{{ $loan->id ?? $loan_transaction->loan_id ?? 'N/A' }}</td>
                        <th>Transaction Type</th>
                        <td>
                            @if($loan_transaction->transaction_type=='disbursement')
                                {{ trans_choice('general.disbursement',1) }}
                            @elseif($loan_transaction->transaction_type=='disbursement_repayment')
                                {{ trans_choice('general.disbursement',1) }} {{ trans_choice('general.repayment',1) }}
                            @elseif($loan_transaction->transaction_type=='specified_due_date')
                                {{ trans_choice('general.specified_due_date',2) }} {{ trans_choice('general.fee',1) }}
                            @elseif($loan_transaction->transaction_type=='installment_fee')
                                {{ trans_choice('general.installment_fee',2) }}
                            @elseif($loan_transaction->transaction_type=='overdue_installment_fee')
                                {{ trans_choice('general.overdue_installment_fee',2) }}
                            @elseif($loan_transaction->transaction_type=='loan_rescheduling_fee')
                                {{ trans_choice('general.loan_rescheduling_fee',2) }}
                            @elseif($loan_transaction->transaction_type=='overdue_maturity')
                                {{ trans_choice('general.overdue_maturity',2) }}
                            @elseif($loan_transaction->transaction_type=='disbursement_fee')
                                {{ trans_choice('general.disbursement',1) }} {{ trans_choice('general.charge',2) }}
                            @elseif($loan_transaction->transaction_type=='interest')
                                {{ trans_choice('general.interest',1) }} {{ trans_choice('general.applied',2) }}
                            @elseif($loan_transaction->transaction_type=='repayment')
                                {{ trans_choice('general.repayment',1) }}
                            @elseif($loan_transaction->transaction_type=='write_off_recovery')
                                {{ trans_choice('general.recovery',1) }} {{ trans_choice('general.repayment',1) }}
                            @elseif($loan_transaction->transaction_type=='penalty')
                                {{ trans_choice('general.penalty',1) }}
                            @elseif($loan_transaction->transaction_type=='interest_waiver')
                                {{ trans_choice('general.interest',1) }} {{ trans_choice('general.waiver',2) }}
                            @elseif($loan_transaction->transaction_type=='charge_waiver')
                                {{ trans_choice('general.charge',1) }} {{ trans_choice('general.waiver',2) }}
                            @elseif($loan_transaction->transaction_type=='write_off')
                                {{ trans_choice('general.write_off',1) }}
                            @else
                                {{ $loan_transaction->transaction_type }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Date</th>
                        <td>{{ $loan_transaction->date }}</td>
                        <th>Receipt #</th>
                        <td>{{ $loan_transaction->receipt ?? 'N/A' }}</td>
                    </tr>
                    @if(isset($loan))
                    <tr>
                        <th>Client</th>
                        <td>
                            @if($loan->client_type == 'client')
                                @if($loan->client && $loan->client->client_type == 'individual')
                                    {{ $loan->client->first_name }} {{ $loan->client->middle_name }} {{ $loan->client->last_name }}
                                @elseif($loan->client && $loan->client->client_type == 'business')
                                    {{ $loan->client->full_name }}
                                @endif
                            @elseif($loan->client_type == 'group')
                                {{ $loan->group->name ?? 'N/A' }}
                            @endif
                        </td>
                        <th>Branch</th>
                        <td>{{ $loan->office->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Loan Officer</th>
                        <td>{{ $loan->loan_officer->name ?? 'N/A' }}</td>
                        <th>Principal</th>
                        <td>{{ number_format($loan->amount ?? 0, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Collected By</th>
                        <td>
                            @if(!empty($loan_transaction->created_by))
                                {{ $loan_transaction->created_by->first_name }} {{ $loan_transaction->created_by->last_name }}
                            @else
                                N/A
                            @endif
                        </td>
                        <th>Notes</th>
                        <td>{{ $loan_transaction->notes ?? 'N/A' }}</td>
                    </tr>
                    @if(!empty($loan_transaction->payment_detail))
                        @if(!empty($loan_transaction->payment_detail->type))
                            <tr>
                                <th>Payment Type</th>
                                <td>{{ $loan_transaction->payment_detail->type->name }}</td>
                                <th>Routing Code</th>
                                <td>{{ $loan_transaction->payment_detail->routing_code ?? 'N/A' }}</td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->account_number) || !empty($loan_transaction->payment_detail->cheque_number))
                            <tr>
                                <th>Account #</th>
                                <td>{{ $loan_transaction->payment_detail->account_number ?? 'N/A' }}</td>
                                <th>Cheque #</th>
                                <td>{{ $loan_transaction->payment_detail->cheque_number ?? 'N/A' }}</td>
                            </tr>
                        @endif
                        @if(!empty($loan_transaction->payment_detail->bank))
                            <tr>
                                <th>Bank</th>
                                <td>{{ $loan_transaction->payment_detail->bank }}</td>
                                <th>Receipt #</th>
                                <td>{{ $loan_transaction->payment_detail->receipt_number ?? 'N/A' }}</td>
                            </tr>
                        @endif
                    @endif
                </table>

                <div class="receipt-amount-box">
                    <div class="receipt-amount-label">{{ trans_choice('general.amount',1) }} {{ trans_choice('general.paid',2) }}</div>
                    <div class="receipt-amount-value">
                        {{ number_format(max($loan_transaction->credit, $loan_transaction->debit), 2) }}
                    </div>
                </div>

                @if(isset($current_balance))
                <div class="receipt-amount-box" style="background: #fff3f3; border-color: #d32f2f;">
                    <div class="receipt-amount-label" style="color: #666;">{{ trans_choice('general.balance',1) }} {{ trans_choice('general.remaining',2) }}</div>
                    <div class="receipt-amount-balance">
                        {{ number_format($current_balance, 2) }}
                    </div>
                </div>
                @endif

                <div class="receipt-actions">
                    <button onclick="window.print()" class="btn btn-print">
                        <i class="fa fa-print"></i> {{ trans_choice('general.print',1) }}
                    </button>
                    <a href="{{ url('loan/transaction/' . $loan_transaction->id . '/pdf') }}" target="_blank" class="btn btn-pdf">
                        <i class="fa fa-file-pdf-o"></i> {{ trans_choice('general.pdf',1) }}
                    </a>
                </div>

                <div class="receipt-footer">
                    <p style="margin: 0;">Whence Financial Services &mdash; This is a system-generated receipt. No signature required.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    @parent
    <script>

    </script>
@endsection
