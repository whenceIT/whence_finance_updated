@extends('layouts.master')
@section('title', 'Administration Expense Approvals')

@section('content')
<style>
    .expense-approval-header { display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; }
    .expense-approval-card { border:1px solid #e7eaf0; border-radius:8px; padding:15px; margin-bottom:15px; background:#fff; }
    .expense-meta { color:#6b7280; font-size:12px; }
    .expense-amount { font-size:20px; font-weight:700; color:#172554; }
    .approval-stage { display:inline-block; padding:5px 9px; border-radius:20px; background:#fff7ed; color:#9a3412; font-size:11px; font-weight:700; }
    .document-link { display:inline-block; margin:3px 6px 3px 0; }
    .detail-label { color:#6b7280; font-size:11px; text-transform:uppercase; letter-spacing:.04em; }
    .detail-value { font-weight:600; overflow-wrap:anywhere; }
    .approval-modal-backdrop { display:none; position:fixed; inset:0; background:rgba(15,23,42,.55); z-index:1050; align-items:center; justify-content:center; padding:15px; }
    .approval-modal { background:#fff; border-radius:10px; width:100%; max-width:480px; padding:22px; box-shadow:0 15px 50px rgba(0,0,0,.2); }
    .approval-modal textarea { resize:vertical; }
</style>

<div class="box box-primary">
    <div class="box-header with-border">
        <div class="expense-approval-header">
            <div>
                <h3 class="box-title"><i class="fa fa-clipboard-check"></i> Administration Expense Approvals</h3>
                <p class="text-muted" style="margin:6px 0 0;">Review branch expenses before they are forwarded to the Risk Manager.</p>
            </div>
            <span class="approval-stage"><i class="fa fa-clock-o"></i> Awaiting Administration</span>
        </div>
    </div>

    <div class="box-body">
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            Approving an expense sends it to the Risk Manager. <strong>It does not initiate payment.</strong>
            Payment can only be initiated after the Risk Manager's approval.
        </div>

        @forelse($expenses as $expense)
            @php
                $documentPaths = [];
                if (!empty($expense->files)) {
                    $decodedFiles = json_decode($expense->files, true);
                    if (is_array($decodedFiles)) {
                        $documentPaths = $decodedFiles;
                    } else {
                        $documentPaths = [$expense->files];
                    }
                }
            @endphp

            <div class="expense-approval-card" id="expense-{{ $expense->id }}">
                <div class="row">
                    <div class="col-sm-8">
                        <div class="expense-meta">
                            EXPENSE #{{ $expense->id }} &nbsp;·&nbsp; {{ $expense->date }}
                            &nbsp;·&nbsp; Submitted {{ optional($expense->created_at)->diffForHumans() }}
                        </div>
                        <h4 style="margin:7px 0 4px;">{{ $expense->name }}</h4>
                        <div class="text-muted">
                            {{ $expense->type->name ?? 'Uncategorised' }}
                            @if(!empty($expense->expense_subcategory))
                                / {{ $expense->expense_subcategory }}
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-4 text-right">
                        <div class="expense-amount">K{{ number_format($expense->amount, 2) }}</div>
                        <span class="label label-warning">Pending Administration</span>
                    </div>
                </div>

                <hr style="margin:12px 0;">

                <div class="row">
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Branch</div>
                        <div class="detail-value">{{ $expense->office->name ?? 'N/A' }}</div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Submitted by</div>
                        <div class="detail-value">
                            {{ $expense->created_by->first_name ?? '' }} {{ $expense->created_by->last_name ?? '' }}
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Beneficiary / Supplier</div>
                        <div class="detail-value">{{ $expense->supplier_name ?: 'Not specified' }}</div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Payment method</div>
                        <div class="detail-value">{{ $expense->deposit_method ?: 'Not specified' }}</div>
                    </div>
                </div>

                <div class="row" style="margin-top:12px;">
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Invoice number</div>
                        <div class="detail-value">{{ $expense->invoice_number ?: 'N/A' }}</div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Invoice date</div>
                        <div class="detail-value">{{ $expense->invoice_date ?: 'N/A' }}</div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Invoice amount</div>
                        <div class="detail-value">
                            {{ $expense->invoice_amount !== null ? 'K'.number_format($expense->invoice_amount, 2) : 'N/A' }}
                        </div>
                    </div>
                    <div class="col-sm-3 col-xs-6">
                        <div class="detail-label">Payment destination</div>
                        <div class="detail-value">
                            @if($expense->payment_phone)
                                {{ $expense->payment_phone }}
                            @elseif($expense->payment_account_number)
                                {{ $expense->payment_account_number }}
                            @else
                                Not specified
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($expense->notes))
                    <div style="margin-top:12px;">
                        <div class="detail-label">Narration / explanation</div>
                        <div>{{ $expense->notes }}</div>
                    </div>
                @endif

                <div style="margin-top:12px;">
                    <div class="detail-label">Supporting documents</div>
                    @if(count($documentPaths))
                        @foreach($documentPaths as $documentPath)
                            <a class="btn btn-default btn-xs document-link" href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($documentPath) }}" target="_blank" rel="noopener">
                                <i class="fa fa-file-o"></i> {{ basename($documentPath) }}
                            </a>
                        @endforeach
                    @else
                        <span class="text-muted">No document attached. Check the explanation provided for expenses that do not have invoices.</span>
                    @endif
                </div>

                <div class="text-right" style="margin-top:15px;">
                    <button type="button" class="btn btn-success btn-sm review-expense"
                            data-id="{{ $expense->id }}" data-action="approve">
                        <i class="fa fa-check"></i> Approve & Forward to Risk
                    </button>
                    <button type="button" class="btn btn-danger btn-sm review-expense"
                            data-id="{{ $expense->id }}" data-action="decline">
                        <i class="fa fa-times"></i> Decline
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center" style="padding:45px 15px;">
                <i class="fa fa-check-circle-o fa-3x text-muted"></i>
                <h4>No expenses awaiting Administration approval</h4>
                <p class="text-muted">New branch expense submissions will appear here.</p>
            </div>
        @endforelse

        <div class="text-center">{{ $expenses->links() }}</div>
    </div>
</div>

<div class="approval-modal-backdrop" id="expenseApprovalModal">
    <div class="approval-modal">
        <h4 id="approvalModalTitle" style="margin-top:0;">Review expense</h4>
        <p id="approvalModalDescription" class="text-muted"></p>
        <label for="approval_notes">Review note <span id="approvalRequiredText"></span></label>
        <textarea id="approval_notes" class="form-control" rows="4" maxlength="5000"
                  placeholder="Add a note. A reason is required when declining."></textarea>
        <div id="approvalModalError" class="text-danger" style="margin-top:8px;"></div>
        <div class="text-right" style="margin-top:18px;">
            <button type="button" class="btn btn-default" id="cancelApproval">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmApproval">Continue</button>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
$(function () {
    var selectedExpenseId = null;
    var selectedAction = null;

    $('.review-expense').on('click', function () {
        selectedExpenseId = $(this).data('id');
        selectedAction = $(this).data('action');

        $('#approval_notes').val('');
        $('#approvalModalError').text('');

        if (selectedAction === 'approve') {
            $('#approvalModalTitle').text('Approve expense');
            $('#approvalModalDescription').text('This expense will move to the Risk Manager for the next approval stage. No payment will be initiated.');
            $('#approvalRequiredText').text('(optional)');
            $('#confirmApproval').removeClass('btn-danger').addClass('btn-success').text('Approve & Forward');
        } else {
            $('#approvalModalTitle').text('Decline expense');
            $('#approvalModalDescription').text('Please provide the reason for declining this expense.');
            $('#approvalRequiredText').text('(required)');
            $('#confirmApproval').removeClass('btn-success').addClass('btn-danger').text('Decline Expense');
        }

        $('#expenseApprovalModal').css('display', 'flex');
    });

    $('#cancelApproval').on('click', function () {
        $('#expenseApprovalModal').hide();
    });

    $('#confirmApproval').on('click', function () {
        var notes = $('#approval_notes').val().trim();

        if (selectedAction === 'decline' && !notes) {
            $('#approvalModalError').text('A decline reason is required.');
            return;
        }

        var url = selectedAction === 'approve'
            ? '{{ url("expense/approvals/expense-approvals") }}/' + selectedExpenseId + '/approve'
            : '{{ url("expense/approvals/expense-approvals") }}/' + selectedExpenseId + '/decline';

        var button = $(this);
        button.prop('disabled', true).text('Processing...');

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                approval_notes: notes
            },
            success: function (response) {
                if (response.success) {
                    if (window.KiloAlert && window.KiloAlert.success) {
                        window.KiloAlert.success(response.message);
                    } else {
                        alert(response.message);
                    }
                    setTimeout(function () { location.reload(); }, 900);
                } else {
                    $('#approvalModalError').text(response.message || 'Unable to process this expense.');
                    button.prop('disabled', false);
                }
            },
            error: function (xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'An error occurred while processing the expense.';
                $('#approvalModalError').text(message);
                button.prop('disabled', false);
            },
            complete: function () {
                button.prop('disabled', false);
                if (selectedAction === 'approve') {
                    button.text('Approve & Forward');
                } else {
                    button.text('Decline Expense');
                }
            }
        });
    });
});
</script>
@endsection
