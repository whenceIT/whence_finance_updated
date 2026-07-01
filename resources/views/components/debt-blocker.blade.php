@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
@endphp
@if($debtBlocker)
    <style>
        /* Prevent iOS zoom on input focus */
        @media screen and (max-width: 768px) {
            #blocker-payment-method,
            #blocker-reference,
            #blocker-amount {
                font-size: 16px !important;
            }
            
            /* Prevent body scroll when blocker is active */
            body {
                overflow: hidden;
                position: fixed;
                width: 100%;
            }
            
            /* Make modal scrollable on mobile */
            #debt-blocker-card {
                max-height: 90vh;
                overflow-y: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
        
        /* Better focus states for mobile */
        #blocker-payment-method:focus,
        #blocker-reference:focus,
        #blocker-amount:focus {
            outline: 2px solid #3498db;
            outline-offset: 2px;
            border-color: #3498db;
        }
        
        /* Prevent double-tap zoom on buttons */
        #blocker-show-form-btn,
        #blocker-submit-btn {
            touch-action: manipulation;
            -webkit-tap-highlight-color: rgba(0,0,0,0.1);
        }
    </style>
    <div id="debt-blocker-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); z-index: 999999; display: flex; align-items: center; justify-content: center; overflow-y: auto; -webkit-overflow-scrolling: touch;">
        <div id="debt-blocker-card" style="background: #ffffff; border-radius: 12px; box-shadow: 0 15px 40px rgba(0,0,0,0.35); max-width: 480px; width: 94%; padding: 28px 24px; text-align: center; border: 2px solid #e74c3c;">
            
            <!-- WARNING STATE -->
            <div id="blocker-warning-state">
                <div style="margin-bottom: 12px;">
                    <i class="fa fa-exclamation-circle" style="font-size: 52px; color: #e74c3c;"></i>
                </div>
                <h3 style="font-weight: 700; color: #c0392b; margin-bottom: 12px; font-size: 21px; line-height: 1.3;">
                    
                
                Monthly Deposit Required
                </h3>
                <p style="font-size: 16.5px; color: #444; line-height: 1.55; margin-bottom: 22px;">
                    Please make your <strong style="color:#c0392b; font-size: 18px;">K5,000</strong> required monthly deposit<br>
                    towards your K5,000, in order to proceed with loan operations.
                </p>

                <button type="button" id="blocker-show-form-btn" class="btn btn-lg" style="background: #e74c3c; color: white; padding: 11px 32px; font-weight: 600; font-size: 15px; border-radius: 6px; border: none; cursor: pointer;">
                    <i class="fa fa-money"></i> &nbsp; Make Deposit Now
                </button>

                <div style="margin-top: 18px; font-size: 12.5px; color: #888;">
                    This step is mandatory before any loan actions can be performed.
                </div>
            </div>

            <!-- DEPOSIT FORM STATE (hidden until button click) -->
            <div id="blocker-deposit-form" style="display: none; text-align: left;">
                <h4 style="color: #c0392b; font-weight: 700; margin-bottom: 4px; font-size: 18px;">
                    Record K5,000 Setup Deposit
                </h4>
                <p style="font-size: 13px; color: #666; margin-bottom: 14px;">
                    This will unblock loan operations for your office.
                </p>

                <label style="font-weight:600; font-size:16px; display:block; margin-bottom:6px;">Payment Method</label>
                <select id="blocker-payment-method" class="form-control" style="margin-bottom: 12px; font-size: 16px; padding: 12px; border-radius: 6px; -webkit-appearance: none; appearance: none; background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 12px center; background-size: 20px; padding-right: 40px;">
                    <option value="">Select Method</option>
                    <option value="airtel">Airtel Money</option>
                    <!-- <option value="airtel_app">Airtel App</option> -->
                    <option value="zanaco_express">Zanaco Express</option>
                    <option value="mtn">MTN MoMo</option>
                    <option value="zanaco_cash">Zanaco Cash Deposit</option>
                    <option value="access">Access</option>
                    <option value="withinhere">WithinHere</option>
                </select>

                <label style="font-weight:600; font-size:16px; display:block; margin-bottom:6px;">Reference Number</label>
                <small id="blocker-format-hint" class="text-muted" style="display:block; margin-bottom:6px; font-size:14px;">Enter Payment Reference Number</small>
                <input type="text" id="blocker-reference" class="form-control" placeholder="Enter reference number" style="margin-bottom: 12px; font-size: 16px; padding: 12px; border-radius: 6px; border: 1px solid #ccc;">

                <label style="font-weight:600; font-size:16px; display:block; margin-bottom:6px;">Amount (ZMW)</label>
                <input type="number" id="blocker-amount" class="form-control" min="5000" step="0.01" inputmode="decimal" style="margin-bottom: 14px; font-size: 16px; padding: 12px; border-radius: 6px; border: 1px solid #ccc;">                
                 <br>
                <p style="font-size: 15px; margin-bottom: 14px;">Current Outstanding balance: <strong>K5,000</strong></p>
                <button type="button" id="blocker-submit-btn" class="btn btn-lg" style="background:#e74c3c; color:white; width:100%; font-weight:600; padding:14px; border-radius:6px; border:none; font-size:16px; cursor:pointer; touch-action: manipulation;">
                    Submit Deposit &amp; Unblock
                </button>

                
                <!-- Put a policy warning that says to enter correct genuiune Payment Reference Number -->
                <div style="background:#f8f9fa; border:1px solid #dee2e6; padding:12px; margin-top:14px; border-radius:6px;">
                    <p style="font-size:13px; color:#666; margin-bottom:0; line-height:1.5;">
                        <strong>Policy Warning:</strong> Please ensure you enter a correct and genuine Payment Reference Number. Incorrect or forged references will result in delayed processing.
                    </p>
                </div>
            </div>

        </div>
    </div>

    <script>
    (function () {
        var branchId = {{ $blockerUser->office_id ?? 0 }};
        var userId = {{ $blockerUser->id ?? 0 }};
        var currentDepositType = 0; // Setup Debt (matches BlockerHelper debt_blocker)
        var requiredBalance = {{ 5000 }};

        function today() {
            var d = new Date();
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-01';
        }

        function updateReferenceHint(method, hintEl, inputEl) {
            inputEl.val('');
            switch (method) {
                case 'airtel':
                    hintEl.text('Format: MP260223.0953.J76581');
                    inputEl.attr('placeholder', 'MP260223.0953.J76581');
                    break;
                case 'airtel_app':
                    hintEl.text('Format: APCZM194947529952000');
                    inputEl.attr('placeholder', 'APCZM194947529952000');
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

        // Switch from warning to form
        $('#blocker-show-form-btn').on('click', function () {
            $('#blocker-warning-state').hide();
            $('#blocker-deposit-form').show();
        });

        // Payment method hint
        $('#blocker-payment-method').on('change', function () {
            updateReferenceHint(
                $(this).val(),
                $('#blocker-format-hint'),
                $('#blocker-reference')
            );
        });

        // Cancel back to warning
        $('#blocker-cancel-form').on('click', function (e) {
            e.preventDefault();
            $('#blocker-deposit-form').hide();
            $('#blocker-warning-state').show();
            // reset form using the real outstanding balance
            $('#blocker-payment-method').val('');
            $('#blocker-reference').val('');
            $('#blocker-amount').val(requiredBalance);
            $('#blocker-format-hint').text('Enter Payment Reference Number');
        });

        // Submit handler - two-call pattern
        $('#blocker-submit-btn').on('click', function () {
            var paymentMethod = $('#blocker-payment-method').val();
            var reference = $('#blocker-reference').val().trim();
            var amount = parseFloat($('#blocker-amount').val());

            if (amount < 5000) {
                alert('Minimum amount is K5,000');
                return;
            }
            if (!paymentMethod) {
                alert('Please select a payment method.');
                return;
            }
            if (!reference) {
                alert('Please enter a payment reference number.');
                return;
            }
            if (isNaN(amount) || amount <= 0) {
                alert('Enter a valid amount.');
                return;
            }

            if (!confirm('Confirm: The K5,000 deposit has been made and the details above are correct?')) {
                return;
            }

            var currentDepositAmount = amount;
            var currentReferenceNumber = reference;
            var currentPaymentMethod = paymentMethod;

            // Disable submit button to prevent double-submission
            $('#blocker-submit-btn').prop('disabled', true).text('Processing...');

            // Record this transaction in the SetupDebtTransactions model/table
            $.ajax({
                url: '{{ route("risk.setup-debt-transactions.store") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    office_id: branchId,
                    amount: currentDepositAmount,
                    payment_method: currentPaymentMethod,
                    reference_number: currentReferenceNumber,
                    notes: 'Setup Debt Monthly Deposit'
                },
                success: function (response) {
                    if (response.success) {
                        alert('Deposit recorded successfully! The page will now reload.');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Unknown error occurred'));
                        $('#blocker-submit-btn').prop('disabled', false).text('Submit Deposit & Unblock');
                    }
                },
                error: function (xhr) {
                    var errorMsg = 'Failed to record deposit.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += ' ' + xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMsg += ' ' + errors.join(', ');
                    }
                    alert(errorMsg);
                    $('#blocker-submit-btn').prop('disabled', false).text('Submit Deposit & Unblock');
                }
            });
        });
    })();
    </script>
@endif
