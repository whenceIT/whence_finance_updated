<div class="content">
    <div class="box box-danger">
        <div class="box-header with-border bg-red">
            <h3 class="box-title">
                <i class="fa fa-money"></i> Record Setup Debt Deposit
            </h3>
        </div>
        <div class="box-body">
            @if($debtBlocker)
            <p class="text-muted" style="margin-bottom:20px;">
                Record payments towards your K5,000 monthly setup debt requirement. This will reduce your outstanding balance and may unblock loan operations.
            </p>
            @else
            <p class="text-muted" style="margin-bottom:20px;">
                This current month paid successfully 
            </p>
            @endif

            <div class="row">
                
                @if($debtBlocker)
                <div class="col-md-8">
                    <form id="setup-debt-deposit-form">
                        <div class="form-group">
                            <label style="font-weight:600; font-size:16px;">Payment Method <span class="text-danger">*</span></label>
                            <select id="setup-payment-method" class="form-control" required style="font-size: 16px; padding: 0px; border-radius: 6px;">
                                <option value="">Select Method</option>
                                <option value="airtel">Airtel Money</option>
                                <option value="zanaco_express">Zanaco Express</option>
                                <option value="mtn">MTN MoMo</option>
                                <option value="zanaco_cash">Zanaco Cash Deposit</option>
                                <option value="access">Access</option>
                                <option value="withinhere">WithinHere</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label style="font-weight:600; font-size:16px;">Reference Number <span class="text-danger">*</span></label>
                            <small id="setup-format-hint" class="text-muted" style="display:block; margin-bottom:6px; font-size:14px;">Enter Payment Reference Number</small>
                            <input type="text" id="setup-reference" class="form-control" placeholder="Enter reference number" required style="font-size: 16px; padding: 12px; border-radius: 6px;">
                        </div>

                        <div class="form-group">
                            <label style="font-weight:600; font-size:16px;">Amount (ZMW) <span class="text-danger">*</span></label>
                            <input type="number" id="setup-amount" class="form-control" min="5000" step="0.01" placeholder="5000.00" required inputmode="decimal" style="font-size: 16px; padding: 12px; border-radius: 6px;">
                            <small class="text-muted">Minimum: K5,000</small>
                        </div>

                        <div class="alert alert-info" style="margin-top:15px;">
                            <i class="fa fa-info-circle"></i> <strong>Policy Warning:</strong> Please ensure you enter a correct and genuine Payment Reference Number. Incorrect or forged references will result in delayed processing.
                        </div>

                        <button type="submit" id="setup-submit-btn" class="btn btn-primary btn-lg" style="width:100%; font-size:16px; padding:14px; margin-top:10px;">
                            <i class="fa fa-save"></i> Submit Setup Debt Deposit
                        </button>
                    </form>
                </div>
                @endif
                
                <div class="col-md-4">
                    @if($debtBlocker)
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Outstanding Balance</span>
                            <span class="info-box-number">K5,000</span>
                        </div>
                    </div>
                    <div class="callout callout-warning">
                        <h4><i class="fa fa-info"></i> Note:</h4>
                        <p style="font-size:13px;">Complete your monthly setup debt deposit to unlock all loan operations for your branch.</p>
                    </div>
                    @else
                            <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Paid</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Payment method hint updates
        function updateSetupReferenceHint(method) {
            var hintEl = $('#setup-format-hint');
            var inputEl = $('#setup-reference');
            inputEl.val('');
            
            switch (method) {
                case 'airtel':
                    hintEl.text('Format: MP260223.0953.J76581');
                    inputEl.attr('placeholder', 'MP260223.0953.J76581');
                    break;
                case 'zanaco_express':
                    hintEl.text('Format: 12 digit number (002504072516)');
                    inputEl.attr('placeholder', '002504072516');
                    break;
                case 'mtn':
                    hintEl.text('Format: 10 digit number (8704564481)');
                    inputEl.attr('placeholder', '8704564481');
                    break;
                case 'zanaco_cash':
                    hintEl.text('Format: 16 digit number (0502605703255600)');
                    inputEl.attr('placeholder', '0502605703255600');
                    break;
                case 'access':
                    hintEl.text('Format: FJB2606341708208');
                    inputEl.attr('placeholder', 'FJB2606341708208');
                    break;
                case 'withinhere':
                    hintEl.text('Format: 1777356230718931');
                    inputEl.attr('placeholder', '1777356230718931');
                    break;
                default:
                    hintEl.text('Enter Payment Reference Number');
                    inputEl.attr('placeholder', 'Enter reference number');
            }
        }

        $('#setup-payment-method').on('change', function() {
            updateSetupReferenceHint($(this).val());
        });

        // Form submission
        $('#setup-debt-deposit-form').on('submit', function(e) {
            e.preventDefault();
            
            var paymentMethod = $('#setup-payment-method').val();
            var reference = $('#setup-reference').val().trim();
            var amount = parseFloat($('#setup-amount').val());

            if (!paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            if (!reference) {
                alert('Please enter a payment reference number.');
                return;
            }
            if (isNaN(amount) || amount < 5000) {
                alert('Amount must be at least K5,000');
                return;
            }

            if (!confirm('Confirm: The K' + amount.toFixed(2) + ' deposit has been made and the details above are correct?')) {
                return;
            }

            // Disable submit button
            var submitBtn = $('#setup-submit-btn');
            submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Processing...');

            $.ajax({
                url: '{{ route("risk.setup-debt-transactions.store") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    office_id: {{ Sentinel::getUser()->office_id }},
                    amount: amount,
                    payment_method: paymentMethod,
                    reference_number: reference,
                    notes: 'Setup Debt Monthly Deposit - Branch Deposits Page'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Deposit recorded successfully! The page will now reload.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                        submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Submit Setup Debt Deposit');
                    }
                },
                error: function(xhr) {
                    var errorMsg = 'Failed to record deposit.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += ' ' + xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg += ' ' + errors.join(', ');
                    }
                    alert(errorMsg);
                    submitBtn.prop('disabled', false).html('<i class="fa fa-save"></i> Submit Setup Debt Deposit');
                }
            });
        });
    });
</script>