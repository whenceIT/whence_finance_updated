<style>
.plt-modal { padding: 20px; }
.plt-modal-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid #eee; }
.plt-modal-title { margin: 0; font-size: 16px; font-weight: 600; color: #333; }
.plt-form-row { display: flex; gap: 16px; margin-bottom: 16px; flex-wrap: wrap; }
.plt-form-group { flex: 1 1 200px; display: flex; flex-direction: column; }
.plt-form-group label { font-size: 12px; font-weight: 600; margin-bottom: 6px; color: #555; }
.plt-form-group input, .plt-form-group select, .plt-form-group textarea { padding: 8px 10px; font-size: 13px; border: 1px solid #ccc; border-radius: 4px; width: 100%; }
.plt-form-group textarea { min-height: 80px; resize: vertical; }
.plt-form-full { flex: 1 1 100%; }
.plt-modal-footer { display: flex; justify-content: flex-end; gap: 8px; margin-top: 20px; padding-top: 12px; border-top: 1px solid #eee; }
.plt-btn { padding: 8px 16px; font-size: 13px; border: none; border-radius: 4px; cursor: pointer; }
.plt-btn-primary { background: #3c8dbc; color: #fff; }
.plt-btn-secondary { background: #95a5a6; color: #fff; }
</style>

@php
$userInfo = \App\Helpers\GeneralHelper::get_user_info();
$user = $userInfo->user;
$office = $userInfo->office;
$officeProvince = null;
if ($office) {
    if (is_object($office) && isset($office->province_id)) {
        $officeProvince = \App\Models\Province::find($office->province_id, ['name', 'id']);
    } elseif (is_int($office) || is_numeric($office)) {
        $officeObj = \App\Models\Office::find($office);
        if ($officeObj && $officeObj->province_id) {
            $officeProvince = \App\Models\Province::find($officeObj->province_id, ['name', 'id']);
        }
    }
}
@endphp
@include('components.kilo-alert')
<div class="modal fade" id="pltTransactionModal" tabindex="-1" role="dialog" aria-labelledby="pltTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 700px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="pltTransactionModalLabel">Record Transaction</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body plt-modal">
                <div class="plt-form-row" style="margin-bottom: 16px;">
                    <div class="plt-form-group" style="flex: 0 0 auto;">
                        <button type="button" class="plt-btn plt-btn-primary" id="pltDepositBtn" onclick="setTransactionType('deposit')" style="background: #27ae60; border-color: #27ae60;">
                            <i class="fa fa-arrow-down"></i> Deposit
                        </button>
                    </div>
                    <div class="plt-form-group" style="flex: 0 0 auto;">
                        <button type="button" class="plt-btn plt-btn-primary" id="pltWithdrawalBtn" onclick="setTransactionType('withdrawal')" style="background: #e74c3c; border-color: #e74c3c;">
                            <i class="fa fa-arrow-up"></i> Withdrawal
                        </button>
                    </div>
                </div>
                <input type="hidden" id="pltEditId" value="">
                <input type="hidden" id="pltUserProvince" value="{{ $officeProvince ? $officeProvince->id : '' }}">
                <input type="hidden" id="pltUserOffice" value="{{ is_object($office) ? $office->id : ($office ?: ($user->office_id ?? '')) }}">
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltType">Type</label>
                        <select id="pltType">
                            <option value="deposit">Deposit</option>
                            <option value="withdrawal">Withdrawal</option>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                    <div class="plt-form-group">
                        <label for="pltTitle">Title</label>
                        <input type="text" id="pltTitle" placeholder="Enter title">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltAmount">Amount (K)</label>
                        <input type="number" id="pltAmount" step="0.01" min="0" placeholder="0.00">
                    </div>
                </div>
                @if($officeProvince)
                <div class="plt-form-row" style="margin-bottom: 0;">
                    <div class="plt-form-group">
                        <label>Provincial Office</label>
                        <div style="padding: 8px 10px; background: #f8f9fa; border: 1px solid #ccc; border-radius: 4px; font-size: 13px; color: #555;">
                            {{ $officeProvince->name }} Provincial Headquarter 
                        </div>
                    </div>
                </div>
                @endif
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltPaymentMethod">Method of Payment</label>
                        <select id="pltPaymentMethod">
                            <option value="">Select Payment Method</option>
                            <option value="bank">Bank Transfer/ Deposit</option>
                            <option value="check">Check</option>
                            <option value="withinhere">Withinhere</option>
                            <option value="artel_money">Artel Money</option>
                            <option value="momo_money">MoMo Money</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="plt-form-group">
                        <label for="pltContribution">Contribution Type</label>
                        <select id="pltContribution">
                            <option value="">Select Contribution</option>
                            <option value="salary">Salary</option>
                            <option value="savings">Savings</option>
                            <option value="housing">Housing</option>
                            <option value="transport">Transport</option>
                            <option value="internet">Internet</option>
                            <option value="petty_cash">Petty Cash</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="plt-form-group">
                        <label for="pltTransactionDate">Transaction Date</label>
                        <input type="date" id="pltTransactionDate" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltReferenceNumber">Reference Number</label>
                        <input type="text" id="pltReferenceNumber" placeholder="Optional reference">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltDescription">Description</label>
                        <textarea id="pltDescription" placeholder="Enter description"></textarea>
                    </div>
                </div>
                <!-- <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltFile">Upload Proof of Payment (Optional)</label>
                        <input type="file" id="pltFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div> -->
            </div>
            <div class="modal-footer">
                <button type="button" class="plt-btn plt-btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="plt-btn plt-btn-primary" id="pltSaveTransaction">Save Transaction</button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var modal = $('#pltTransactionModal');
    var currentType = 'deposit';

    function resetModal(type) {
        currentType = type;
        modal.find('#pltType').val(type);
        modal.find('#pltEditId').val('');
        modal.find('#pltTitle').val('');
        modal.find('#pltAmount').val('');
        modal.find('#pltPaymentMethod').val('');
        modal.find('#pltContribution').val('');
        modal.find('#pltTransactionDate').val(new Date().toISOString().split('T')[0]);
        modal.find('#pltReferenceNumber').val('');
        modal.find('#pltDescription').val('');
        modal.find('#pltFile').val('');
        updateTypeButtons();
    }

    window.setTransactionType = function(type) {
        currentType = type;
        modal.find('#pltType').val(type);
        updateTypeButtons();
    };

    function updateTypeButtons() {
        var type = currentType;
        if (type === 'deposit') {
            $('#pltDepositBtn').css('background', '#27ae60').css('border-color', '#27ae60');
            $('#pltWithdrawalBtn').css('background', '#e74c3c').css('border-color', '#e74c3c');
        } else if (type === 'withdrawal') {
            $('#pltDepositBtn').css('background', '#e74c3c').css('border-color', '#e74c3c');
            $('#pltWithdrawalBtn').css('background', '#27ae60').css('border-color', '#27ae60');
        } else {
            $('#pltDepositBtn').css('background', '#3c8dbc').css('border-color', '#3c8dbc');
            $('#pltWithdrawalBtn').css('background', '#3c8dbc').css('border-color', '#3c8dbc');
        }
    }

    window.openTransactionModal = function(type) {
        resetModal(type);
        modal.modal('show');
    };

    $('#pltSaveTransaction').on('click', function() {
        var formData = new FormData();
        formData.append('title', $('#pltTitle').val());
        formData.append('description', $('#pltDescription').val());
        formData.append('amount', $('#pltAmount').val());
        formData.append('type', currentType);
        formData.append('province_id', $('#pltUserProvince').val());
        formData.append('office_id', $('#pltUserOffice').val());
        formData.append('payment_method', $('#pltPaymentMethod').val());
        formData.append('contribution', $('#pltContribution').val());
        formData.append('transaction_date', $('#pltTransactionDate').val());
        formData.append('reference_number', $('#pltReferenceNumber').val());
        formData.append('recorded_at', $('#pltRecordedAt').val());

        $.ajax({
            url: '{{ route("api.provincial-ledger.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    KiloAlert.success(response.message);
                    modal.modal('hide');
                    setTimeout(function() { location.reload(); }, 1500);
                } else {
                    KiloAlert.error(response.message || 'Failed to save transaction.');
                }
            },
            error: function(xhr) {
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'An error occurred.';
                KiloAlert.error(msg);
            }
        });
    });
})();
</script>