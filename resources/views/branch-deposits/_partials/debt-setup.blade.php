@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
@endphp

<div class="">
    <div class="">
        <div class="box-header with-border" style="background: linear-gradient(90deg, #ffebee 0%, #ffcdd2 100%);padding:16px 20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.08);border:1px solid rgba(255,255,255,0.3);backdrop-filter:blur(10px);margin-bottom:20px;">
            <h3 class="box-title" style="margin:0;font-size:18px;font-weight:700;color:#1a0000;line-height:1.3;display:flex;align-items:center;gap:8px;">
                <i class="fa fa-money"></i> Record Setup Debt Deposit
            </h3>
        </div>
        <div class="">
            @if($debtBlocker)
            <p class="text-muted" style="margin-bottom:20px;">
                Record payments towards your K5,000 monthly setup debt requirement. 
                This will reduce your outstanding balance and may unblock loan operations.
            </p>
            @else
            @endif

            <div class="row">
                <div class="col-md-8 col-sm-12">
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
                            <input type="number" id="setup-amount" class="form-control" min="500" step="0.01" placeholder="" required inputmode="decimal" style="font-size: 16px; padding: 12px; border-radius: 6px;">
                            <small class="text-muted">Monthly minimum should total upto: K5,000</small>
                        </div>

                        <button type="submit" id="setup-submit-btn" class="glass-submit-btn">
                            <span class="submit-btn-content">
                                <i class="fa fa-save"></i>
                                <span>Save Deposit</span>
                            </span>
                            <span class="submit-btn-loading" style="display: none;">
                                <i class="fa fa-spinner fa-spin"></i>
                                <span>Processing...</span>
                            </span>
                        </button>

                    </form>
                </div>
                
                <div class="col-md-4 col-sm-12">
                    @if($debtBlocker)
                    <div class="info-box bg-yellow">
                        <span class="info-box-icon"><i class="fa fa-exclamation-triangle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Required Minimum</span>
                            <span class="info-box-number">K5,000</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info" style="margin-top:15px;">
                        <i class="fa fa-info-circle"></i> <strong>Policy Warning:</strong> Please ensure you enter a correct and genuine Payment Reference Number. Incorrect or forged references will result in delayed processing, or branch operation lockout.
                    </div>
                    @else
                    <div class="info-box bg-green">
                        <span class="info-box-icon"><i class="fa fa-check-circle"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text info-box-number">Paid</span>
                            <span class="info-box-text">
                              You have already paid the minimum<br>required amount for this month's<br>setup debt. 
                              If you would like to make<br>an additional payment, you may do so.
                            </span>
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
                window.KiloAlert.error('Please select a payment method.');
                return;
            }
            if (!reference) {
                window.KiloAlert.error('Please enter a payment reference number.');
                return;
            }
            if (isNaN(amount) || amount < 500) {
                window.KiloAlert.error('Amount must be at least K5,000');
                return;
            }

            if (!confirm('Confirm: The K' + amount.toFixed(2) + ' deposit has been made and the details above are correct?')) {
                return;
            }

            // Disable submit button
            var submitBtn = $('#setup-submit-btn');
            submitBtn.prop('disabled', true).addClass('disabled');
            $('.submit-btn-content').hide();
            $('.submit-btn-loading').show();

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
                        window.KiloAlert.success('Deposit recorded successfully! The page will now reload.');
                        window.location.reload();
                    } else {
                        window.KiloAlert.error('Error: ' + (response.message || 'Unknown error occurred'));
                        submitBtn.prop('disabled', false).removeClass('disabled');
                        $('.submit-btn-content').show();
                        $('.submit-btn-loading').hide();
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
                    window.KiloAlert.error(errorMsg);
                    submitBtn.prop('disabled', false).removeClass('disabled');
                    $('.submit-btn-content').show();
                    $('.submit-btn-loading').hide();
                }
            });
        });
    });
</script>

<style>
    .glass-submit-btn {
        position: relative;
        width: 100%;
        padding: 14px 20px;
        font-size: 16px;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 16px;
        cursor: pointer;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
        letter-spacing: 0.5px;
        margin-top: 10px;
    }

    .glass-submit-btn::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transform: rotate(30deg);
        transition: all 0.5s ease;
    }

    .glass-submit-btn:hover::before {
        transform: rotate(30deg) translateX(100%);
    }

    .glass-submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 40px rgba(102, 126, 234, 0.4);
    }

    .glass-submit-btn:active {
        transform: translateY(0);
    }

    .glass-submit-btn.disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .submit-btn-content {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .submit-btn-content.loading {
        opacity: 0;
    }

    .submit-btn-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: 1;
    }
</style>

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
            if (isNaN(amount) || amount < 500) {
                alert('Amount must be at least K500');
                return;
            }

            if (!confirm('Confirm: The K' + amount.toFixed(2) + ' deposit has been made and the details above are correct?')) {
                return;
            }

            // Disable submit button
            var submitBtn = $('#setup-submit-btn');
            submitBtn.prop('disabled', true).addClass('disabled');
            $('.submit-btn-content').hide();
            $('.submit-btn-loading').show();

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
                        submitBtn.prop('disabled', false).removeClass('disabled');
                        $('.submit-btn-content').show();
                        $('.submit-btn-loading').hide();
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
                    submitBtn.prop('disabled', false).removeClass('disabled');
                    $('.submit-btn-content').show();
                    $('.submit-btn-loading').hide();
                }
            });
        });
    });
</script>
