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
                <input type="hidden" id="pltEditId" value="">
                <input type="hidden" id="pltUserProvince" value="{{ $officeProvince ? $officeProvince->id : '' }}">
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltType">Type</label>
                        <select id="pltType" disabled>
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
                        <label for="pltTransactionDate">Transaction Date</label>
                        <input type="date" id="pltTransactionDate" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group">
                        <label for="pltReferenceNumber">Reference Number</label>
                        <input type="text" id="pltReferenceNumber" placeholder="Optional reference">
                    </div>
                    <div class="plt-form-group">
                        <label for="pltRecordedAt">Recorded At</label>
                        <input disabled type="datetime-local" id="pltRecordedAt" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltDescription">Description</label>
                        <textarea id="pltDescription" placeholder="Enter description"></textarea>
                    </div>
                </div>
                <div class="plt-form-row">
                    <div class="plt-form-group plt-form-full">
                        <label for="pltFile">Upload Proof of Payment (Optional)</label>
                        <input type="file" id="pltFile" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    </div>
                </div>
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
    var currentType = 'income';

    function resetModal(type) {
        currentType = type;
        modal.find('#pltType').val(type);
        modal.find('#pltEditId').val('');
        modal.find('#pltTitle').val('');
        modal.find('#pltAmount').val('');
        modal.find('#pltPaymentMethod').val('');
        modal.find('#pltTransactionDate').val(new Date().toISOString().split('T')[0]);
        modal.find('#pltReferenceNumber').val('');
        modal.find('#pltRecordedAt').val(new Date().toISOString().slice(0, 16));
        modal.find('#pltDescription').val('');
        modal.find('#pltFile').val('');
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
        formData.append('payment_method', $('#pltPaymentMethod').val());
        formData.append('transaction_date', $('#pltTransactionDate').val());
        formData.append('reference_number', $('#pltReferenceNumber').val());
        formData.append('recorded_at', $('#pltRecordedAt').val());
        
        var fileInput = $('#pltFile')[0];
        if (fileInput.files && fileInput.files[0]) {
            formData.append('file', fileInput.files[0]);
        }

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