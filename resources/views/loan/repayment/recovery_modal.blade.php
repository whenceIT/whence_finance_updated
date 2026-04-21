<!-- Recovery Case Modal -->
<div id="recovery_modal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">×</button>
        <h4 class="modal-title"><i class="fa fa-money"></i> Debt Recovery Payment</h4>
      </div>
      <div class="modal-body info">
        <form method="post" id="recoveryForm"
              action="{{ url('loan/'.$loan->id.'/repayment/case/store') }}">
            {{csrf_field()}}
            <input type="hidden" name="is_recovery" value="1">

            <!-- Recovery Case Selection -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Recovery Case <span class="text-danger">*</span></label>
                        <select name="recovery_case_id" id="recovery_case_id" class="form-control select2" required>
                            <option selected value="">— Select Recovery Case —</option>

                            @if(isset($recoveryCases))
                                @foreach($recoveryCases as $index => $case)
                                    <option value="{{ $case->id }}"
                                        {{ $index === 0 ? 'selected' : '' }}
                                        data-case-number="{{ $case->case_number }}"
                                        data-outstanding="{{ $case->loan_outstanding_amount }}"
                                        data-amount-recovered="{{ $case->amount_recovered }}"
                                        data-recoveries-dept-pct="{{ $case->recoveries_dept_attribution_pct }}"
                                        data-origin-branch-pct="{{ $case->origin_branch_attribution_pct }}"
                                        data-supporting-branch-pct="{{ $case->supporting_branch_attribution_pct }}"
                                        data-origin-branch-id="{{ $case->origin_branch_id }}"
                                        data-supporting-branch-id="{{ $case->supporting_branch_id }}"
                                        data-specialist-id="{{ $case->assigned_specialist_id }}"
                                        data-client-name="{{ $case->client->first_name ?? '' }} {{ $case->client->last_name ?? '' }}">
                                        {{ $case->case_number }} - {{ $case->client->first_name ?? '' }} {{ $case->client->last_name ?? '' }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
            </div>

            <!-- Case Info Panel -->
            <div id="caseInfoPanel" class="alert alert-info" style="display:none;">
                <div class="row">
                    <div class="col-md-4">
                        <strong><i class="fa fa-user"></i> Client:</strong> <span id="displayClientName">-</span>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fa fa-money"></i> Outstanding Amount:</strong> <span id="displayOutstanding" class="text-danger">-</span>
                    </div>
                    <div class="col-md-4">
                        <strong><i class="fa fa-check-circle"></i> Amount Recovered:</strong> <span id="displayAmountRecovered" class="text-success">-</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Receipt Number</label>
                        <input type="text" name="receipt_number" id="recovery_receipt_number" class="form-control" placeholder="Auto-generated if empty">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Payment Date</label>
                        <input type="text" name="date" id="payment_date" class="form-control date-picker" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Amount <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="recovery_amount" class="form-control" step="0.01" required placeholder="Enter recovery amount">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Payment Method</label>
                        <select name="payment_type_id" id="payment_method" class="form-control select2">
                            <option value="">— Select Payment Method —</option>
                            <option value="cash">Cash</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="payroll_deduction">Payroll Deduction</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="referenceRow">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" id="paymentReferenceLabel">Payment Reference</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Cheque number, transaction ID, etc.">
                    </div>
                </div>
                <div class="col-md-6" id="bankRow" style="display:none;">
                    <div class="form-group">
                        <label class="control-label">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter bank name">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Is it Full Settlement?</label>
                        <select name="is_settlement" id="is_settlement" class="form-control">
                            <option value="">--empty--</option>
                            <option value="0">No</option>
                            <option value="1">Yes - Full Settlement</option>
                        </select>
                        <small class="text-muted" id="settlementHint">Select "Yes" if this payment fully settles the debt</small>
                    </div>
                </div>
            </div>

            <!-- Attribution Summary Panel -->
            <div id="attributionPanel" class="panel panel-default" style="display:none;">
                <div class="panel-heading">
                    <h4 class="panel-title"><i class="fa fa-calculator"></i> Attribution Breakdown</h4>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Outstanding Before</label>
                                <input type="hidden" name="outstanding_before" id="outstanding_before" value="0">
                                <p class="form-control-static text-danger" id="displayOutstandingBefore">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Outstanding After</label>
                                <input type="hidden" name="outstanding_after" id="outstanding_after" value="0">
                                <p class="form-control-static text-success" id="displayOutstandingAfter">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-building"></i> Recoveries Dept <span id="recoveriesDeptPctLabel">(0%)</span></label>
                                <input type="hidden" name="recoveries_dept_amount" id="recoveries_dept_amount" value="0">
                                <p class="form-control-static text-primary" id="displayRecoveriesDeptAmount">0.00</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-home"></i> Origin Branch <span id="originBranchPctLabel">(0%)</span></label>
                                <input type="hidden" name="origin_branch_amount" id="origin_branch_amount" value="0">
                                <p class="form-control-static text-info" id="displayOriginBranchAmount">0.00</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-md-offset-9">
                            <div class="form-group">
                                <label class="control-label"><i class="fa fa-handshake-o"></i> Supporting Branch <span id="supportingBranchPctLabel">(0%)</span></label>
                                <input type="hidden" name="supporting_branch_amount" id="supporting_branch_amount" value="0">
                                <p class="form-control-static text-warning" id="displaySupportingBranchAmount">0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Notes</label>
                        <textarea name="notes" id="recovery_notes" class="form-control" rows="2" placeholder="Any additional notes..."></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-default pull-left"
                        data-dismiss="modal">
                    {{ trans_choice('general.close',1) }}
                </button>
                <button type="submit"
                        class="btn btn-primary"  id='recoverySubmitBtn' disabled>{{ trans_choice('general.save',1) }}</button>
            </div>
        </form>
      </div>
      <div class="modal-footer"></div>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    function checkForm() {
        var caseSelected = $('#recovery_case_id').val() !== '';
        var amountFilled = $('#recovery_amount').val() !== '';
        var paymentMethodSelected = $('#payment_method').val() !== '';
        var paymentDateFilled = $('#payment_date').val() !== '';
        var isSettlementSelected = $('#is_settlement').val() !== '';
        if (caseSelected && amountFilled && paymentMethodSelected && paymentDateFilled && isSettlementSelected) {
            $('#recoverySubmitBtn').prop('disabled', false);
        } else {
            $('#recoverySubmitBtn').prop('disabled', true);
        }
    }

    $('#recovery_case_id, #recovery_amount, #payment_method, #payment_date, #is_settlement').on('change input', checkForm);
    checkForm(); // initial check
});
</script>