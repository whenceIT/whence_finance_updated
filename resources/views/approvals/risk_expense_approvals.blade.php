@extends('layouts.master')

@section('title', 'Risk Manager Expense Approvals')

@include('components.kilo-alert')



@section('content')

<div class="row">

    <div class="col-md-12">

        <div class="box box-warning">

            <div class="box-header with-border">

                <h3 class="box-title">

                    <i class="fa fa-shield"></i> Risk Manager Expense Approvals

                </h3>

                <span class="label label-warning pull-right">

                    {{ $expenses->total() }} Awaiting Review

                </span>

            </div>



            <div class="box-body">

                <p class="text-muted">

                    These expenses have already passed Administration approval.

                    Approving an expense below initiates its payment through WithinHere.

                    A payment request marked Pending is not yet a confirmed payment.

                </p>



                @forelse($expenses as $expense)

                    @php

                        $documents = [];

                        if (!empty($expense->files)) {

                            $decoded = json_decode($expense->files, true);

                            $documents = is_array($decoded) ? $decoded : [$expense->files];

                        }

                    @endphp



                    <div class="panel panel-default">

                        <div class="panel-heading clearfix">

                            <strong>Expense #{{ $expense->id }} — {{ $expense->name }}</strong>

                            <span class="label label-warning pull-right">Pending Risk Approval</span>

                        </div>



                        <div class="panel-body">

                            <div class="row">

                                <div class="col-sm-3">

                                    <strong>Amount</strong><br>

                                    K{{ number_format((float) $expense->amount, 2) }}

                                </div>

                                <div class="col-sm-3">

                                    <strong>Expense Date</strong><br>

                                    {{ $expense->date }}

                                </div>

                                <div class="col-sm-3">

                                    <strong>Branch</strong><br>

                                    {{ optional($expense->office)->name ?? 'N/A' }}

                                </div>

                                <div class="col-sm-3">

                                    <strong>Expense Type</strong><br>

                                    {{ optional($expense->type)->name ?? 'N/A' }}

                                </div>

                            </div>



                            <hr>



                            <div class="row">

                                <div class="col-sm-4">

                                    <strong>Submitted By</strong><br>

                                    {{ optional($expense->created_by)->first_name }}

                                    {{ optional($expense->created_by)->last_name }}

                                </div>

                                <div class="col-sm-4">

                                    <strong>Supplier</strong><br>

                                    {{ $expense->supplier_name ?: 'N/A' }}

                                </div>

                                <div class="col-sm-4">

                                    <strong>Payment Method</strong><br>

                                    {{ $expense->deposit_method ?: 'N/A' }}

                                </div>

                            </div>



                            @if($expense->expense_subcategory || $expense->invoice_number || $expense->invoice_date || $expense->invoice_amount)

                                <hr>

                                <div class="row">

                                    <div class="col-sm-3">

                                        <strong>Subcategory</strong><br>

                                        {{ $expense->expense_subcategory ?: 'N/A' }}

                                    </div>

                                    <div class="col-sm-3">

                                        <strong>Invoice Number</strong><br>

                                        {{ $expense->invoice_number ?: 'N/A' }}

                                    </div>

                                    <div class="col-sm-3">

                                        <strong>Invoice Date</strong><br>

                                        {{ $expense->invoice_date ?: 'N/A' }}

                                    </div>

                                    <div class="col-sm-3">

                                        <strong>Invoice Amount</strong><br>

                                        {{ $expense->invoice_amount !== null ? 'K' . number_format((float) $expense->invoice_amount, 2) : 'N/A' }}

                                    </div>

                                </div>

                            @endif



                            <hr>

                            <strong>Payment Destination</strong><br>

                            @if($expense->deposit_method === 'mobile_money')

                                Phone: {{ $expense->payment_phone ?: 'Not provided' }}<br>

                                Operator: {{ $expense->payment_operator ?: 'Not provided' }}

                            @elseif($expense->deposit_method === 'bank')

                                Bank ID: {{ $expense->payment_bank_id ?: 'Not provided' }}<br>

                                Account Number: {{ $expense->payment_account_number ?: 'Not provided' }}

                            @else

                                <span class="text-muted">No recognised payment destination recorded.</span>

                            @endif



                            <hr>

                            <strong>Narration / Explanation</strong>

                            <p>{{ $expense->notes ?: 'No narration provided.' }}</p>



                            <strong>Administration Notes</strong>

                            <p>{{ $expense->admin_approval_notes ?: 'No notes provided.' }}</p>



                            <strong>Supporting Documents</strong>

                            @if(count($documents))

                                <ul>

                                    @foreach($documents as $document)

                                        <li>

                                            <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($document) }}"

                                               target="\_blank" rel="noopener">

                                                View document {{ $loop->iteration }}

                                            </a>

                                        </li>

                                    @endforeach

                                </ul>

                            @else

                                <p class="text-muted">

                                    No document attached. Review the expense explanation and supporting details.

                                </p>

                            @endif



                            <hr>

                            <div class="text-right">

                                <button type="button"

                                        class="btn btn-success risk-review-approve"

                                        data-id="{{ $expense->id }}"

                                        data-name="{{ e($expense->name) }}">

                                    <i class="fa fa-check"></i> Approve & Initiate Payment

                                </button>



                                <button type="button"

                                        class="btn btn-danger risk-review-decline"

                                        data-id="{{ $expense->id }}"

                                        data-name="{{ e($expense->name) }}">

                                    <i class="fa fa-times"></i> Decline

                                </button>

                            </div>

                        </div>

                    </div>

                @empty

                    <div class="alert alert-info">

                        There are currently no expenses awaiting Risk Manager approval.

                    </div>

                @endforelse



                <div class="text-center">

                    {{ $expenses->links() }}

                </div>

            </div>

        </div>

    </div>

</div>




<style>
    .risk-expense-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.60);
        z-index: 20000;
        align-items: center;
        justify-content: center;
        padding: 15px;
    }
    .risk-expense-modal {
        background: #fff;
        border-radius: 10px;
        width: 100%;
        max-width: 500px;
        padding: 22px;
        box-shadow: 0 15px 50px rgba(0,0,0,.25);
    }
    .risk-expense-modal textarea { resize: vertical; }
</style>

<div class="risk-expense-modal-backdrop" id="riskExpenseApprovalModal">
    <div class="risk-expense-modal">
        <h4 style="margin-top:0;">Confirm Risk Approval</h4>
        <p id="riskExpenseApproveName" class="text-muted"></p>
        <div class="alert alert-warning">
            This action will submit a payment request to WithinHere.
            A pending response does not confirm that the recipient has received the money.
            Do not submit the request again if it takes time.
        </div>
        <label for="riskExpenseApprovalNotes">Approval notes (optional)</label>
        <textarea id="riskExpenseApprovalNotes" class="form-control" rows="3"></textarea>
        <input type="hidden" id="riskExpenseApproveId">
        <div id="riskExpenseApprovalError" class="text-danger" style="margin-top:8px;"></div>
        <div class="text-right" style="margin-top:18px;">
            <button type="button" class="btn btn-default" id="riskExpenseCancelApprove">Cancel</button>
            <button type="button" class="btn btn-success" id="riskExpenseConfirmApprove">
                Confirm & Initiate Payment
            </button>
        </div>
    </div>
</div>

<div class="risk-expense-modal-backdrop" id="riskExpenseDeclineModal">
    <div class="risk-expense-modal">
        <h4 style="margin-top:0;">Decline Expense</h4>
        <p id="riskExpenseDeclineName" class="text-muted"></p>
        <label for="riskExpenseDeclineNotes">Reason for declining <span class="text-danger">*</span></label>
        <textarea id="riskExpenseDeclineNotes" class="form-control" rows="4"
                  placeholder="Explain why this expense is being declined."></textarea>
        <input type="hidden" id="riskExpenseDeclineId">
        <div id="riskExpenseDeclineError" class="text-danger" style="margin-top:8px;"></div>
        <div class="text-right" style="margin-top:18px;">
            <button type="button" class="btn btn-default" id="riskExpenseCancelDecline">Cancel</button>
            <button type="button" class="btn btn-danger" id="riskExpenseConfirmDecline">Confirm Decline</button>
        </div>
    </div>
</div>

@endsection


@section('footer-scripts')
<script>
$(function () {
    var riskSelectedExpenseId = null;

    // Open the custom approval modal (no Bootstrap modal dependency).
    $(document).on('click', '.risk-review-approve', function () {
        riskSelectedExpenseId = $(this).attr('data-id');

        $('#riskExpenseApproveId').val(riskSelectedExpenseId);
        $('#riskExpenseApproveName').text(
            'You are about to approve and initiate payment for: ' + ($(this).attr('data-name') || 'this expense')
        );
        $('#riskExpenseApprovalNotes').val('');
        $('#riskExpenseApprovalError').text('');
        $('#riskExpenseConfirmApprove').prop('disabled', false).text('Confirm & Initiate Payment');

        $('#riskExpenseApprovalModal').css('display', 'flex');
    });

    // Open the custom decline modal.
    $(document).on('click', '.risk-review-decline', function () {
        riskSelectedExpenseId = $(this).attr('data-id');

        $('#riskExpenseDeclineId').val(riskSelectedExpenseId);
        $('#riskExpenseDeclineName').text(
            'You are about to decline: ' + ($(this).attr('data-name') || 'this expense')
        );
        $('#riskExpenseDeclineNotes').val('');
        $('#riskExpenseDeclineError').text('');
        $('#riskExpenseConfirmDecline').prop('disabled', false).text('Confirm Decline');

        $('#riskExpenseDeclineModal').css('display', 'flex');
    });

    // Close custom modals.
    $('#riskExpenseCancelApprove').on('click', function () {
        $('#riskExpenseApprovalModal').hide();
    });

    $('#riskExpenseCancelDecline').on('click', function () {
        $('#riskExpenseDeclineModal').hide();
    });

    // Close when the user clicks the dark backdrop, not the modal itself.
    $('#riskExpenseApprovalModal, #riskExpenseDeclineModal').on('click', function (event) {
        if (event.target === this) {
            $(this).hide();
        }
    });

    // Approve and initiate payment.
    $('#riskExpenseConfirmApprove').on('click', function () {
        var id = $('#riskExpenseApproveId').val();
        var button = $(this);

        if (!id) {
            $('#riskExpenseApprovalError').text('The expense ID is missing. Please close this window and refresh the page.');
            return;
        }

        button.prop('disabled', true).text('Submitting payment request...');
        $('#riskExpenseApprovalError').text('');

        $.ajax({
            url: '{{ url("expense/approvals/risk-expense-approvals") }}/' + encodeURIComponent(id) + '/approve',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                approval_notes: $('#riskExpenseApprovalNotes').val()
            },
            success: function (response) {
                if (response && response.success) {
                    $('#riskExpenseApprovalModal').hide();

                    if (window.KiloAlert && typeof window.KiloAlert.success === 'function') {
                        window.KiloAlert.success(response.message || 'The expense approval request was processed.');
                    } else {
                        alert(response.message || 'The expense approval request was processed.');
                    }

                    setTimeout(function () { window.location.reload(); }, 900);
                } else {
                    $('#riskExpenseApprovalError').text(
                        (response && response.message) ? response.message : 'The server did not confirm the request.'
                    );
                }
            },
            error: function (xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'The request failed. Check the payment status before attempting another payment request.';
                $('#riskExpenseApprovalError').text(message);
            },
            complete: function () {
                button.prop('disabled', false).text('Confirm & Initiate Payment');
            }
        });
    });

    // Decline expense.
    $('#riskExpenseConfirmDecline').on('click', function () {
        var id = $('#riskExpenseDeclineId').val();
        var reason = $.trim($('#riskExpenseDeclineNotes').val());
        var button = $(this);

        if (!id) {
            $('#riskExpenseDeclineError').text('The expense ID is missing. Please close this window and refresh the page.');
            return;
        }

        if (!reason) {
            $('#riskExpenseDeclineError').text('Please provide a reason for declining this expense.');
            return;
        }

        button.prop('disabled', true).text('Declining...');
        $('#riskExpenseDeclineError').text('');

        $.ajax({
            url: '{{ url("expense/approvals/risk-expense-approvals") }}/' + encodeURIComponent(id) + '/decline',
            method: 'POST',
            dataType: 'json',
            data: {
                _token: '{{ csrf_token() }}',
                approval_notes: reason
            },
            success: function (response) {
                if (response && response.success) {
                    $('#riskExpenseDeclineModal').hide();

                    if (window.KiloAlert && typeof window.KiloAlert.success === 'function') {
                        window.KiloAlert.success(response.message || 'The expense was declined.');
                    } else {
                        alert(response.message || 'The expense was declined.');
                    }

                    setTimeout(function () { window.location.reload(); }, 900);
                } else {
                    $('#riskExpenseDeclineError').text(
                        (response && response.message) ? response.message : 'The server did not confirm the decline.'
                    );
                }
            },
            error: function (xhr) {
                var message = xhr.responseJSON && xhr.responseJSON.message
                    ? xhr.responseJSON.message
                    : 'The expense could not be declined.';
                $('#riskExpenseDeclineError').text(message);
            },
            complete: function () {
                button.prop('disabled', false).text('Confirm Decline');
            }
        });
    });
});
</script>
@endsection
