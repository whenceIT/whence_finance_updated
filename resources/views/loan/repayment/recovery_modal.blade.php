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
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="control-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group input-group-lg" style="margin-bottom: 15px;">
                            <span class="input-group-addon"><strong>K</strong></span>
                            <input type="number" name="amount" id="recovery_amount" class="form-control" step="0.01" required placeholder="Enter recovered amount" style="font-size: 18px; font-weight: bold;">
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" style="font-weight: bold;">Dept Share Amount</label>
                        <input type="number" name="dept_share_amount" id="dept_share_amount" class="form-control" step="0.01" placeholder="Enter dept share amount" style="background-color: #fdecea;">
                        <small class="text-muted">Amount allocated to Recoveries Dept (optional)</small>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Payment Date</label>
                        <input type="text" name="date" id="payment_date" class="form-control date-picker" value="{{ date('Y-m-d') }}">
                    </div>
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
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label" id="paymentReferenceLabel">Payment Reference</label>
                        <input type="text" name="payment_reference" id="payment_reference" class="form-control" placeholder="Cheque number, transaction ID, etc.">
                    </div>
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
                    <div class="form-group" id="bankRow" style="display:none;">
                        <label class="control-label">Bank Name</label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter bank name">
                    </div>
                </div>
            </div>

            <!-- Attribution Summary Panel -->
            

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
        if (caseSelected && amountFilled) {
            $('#recoverySubmitBtn').prop('disabled', false);
        } else {
            $('#recoverySubmitBtn').prop('disabled', true);
        }
    }

    $('#recovery_case_id, #recovery_amount').on('change input', checkForm);
    checkForm(); // initial check
});
</script>