@extends('layouts.master')

@section('title')
    Add Fund Transfer
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Add Fund Transfer</h3>
        </div>


           <div style="
    margin: 15px;
    padding: 15px 20px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 10px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
">
    <div>
        <div style="font-size: 13px; opacity: 0.9;">
            Current Wallet Balance
        </div>
        <div style="font-size: 28px; font-weight: bold; margin-top: 5px;">
            K{{ number_format($cashBalance, 2) }}
        </div>
    </div>

    <div style="
        background: rgba(255,255,255,0.2);
        width: 45px;
        height: 45px;
        border-radius: 50%;
        display:flex;
        align-items:center;
        justify-content:center;
        font-size:22px;
    ">
        💰
    </div>
</div>

        <form method="post" action="{{ url('/accounting/store_fund_transfers_and_payments') }}" class="form-horizontal" enctype="multipart/form-data" id="transferForm">
            {{ csrf_field() }}

            <input type="hidden" name="total_deducted" id="hidden_total_deducted">
                    <input type="hidden" name="user_id" id="hidden_user_id">
                    <input type="hidden" name="hidden_operator" id="hidden_operator">
<input type="hidden" name="receiver_id" id="hidden_receiver_id">


            <div class="box-body">

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">1. Movement Information</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="movement_type" class="control-label col-md-2">Movement Type</label>
                    <div class="col-md-4">
                        <select name="movement_type" id="movement_type" class="form-control select2" required>
                            <option value="">Select movement type</option>
                            <option value="transfer">Transfer</option>
             
                        </select>
                    </div>

                    <label for="date" class="control-label col-md-2">Transaction Date</label>
                    <div class="col-md-4">
                        <input type="text" name="date" id="date" class="form-control date-picker" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_id" class="control-label col-md-2">Branch</label>
                    <div class="col-md-4">
                        <select name="office_id" id="office_id" class="form-control select2" required>
                            <option value="">Select branch</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label for="amount" class="control-label col-md-2">Amount</label>
                    <div class="col-md-4">
                        <input type="number" min="0" name="amount" id="amount" class="form-control" placeholder="Enter amount" required>
                    </div>
                </div>


                <hr>

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">2. Account Details</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="from_account" class="control-label col-md-2">From Account</label>
                    <div class="col-md-4">
                        <select name="from_account" id="from_account" class="form-control select2" required>
                            <option value="withinhere">Withinhere</option>
                           
                        </select>
                    </div>

                    <label for="to_account" class="control-label col-md-2">To Account</label>
                    <div class="col-md-4">
                        <select name="to_account" id="to_account" class="form-control select2">
                            <option value="">Select destination account</option>
                          <option value="">Select method</option>
                            <option value="bank_transfer">Bank</option>
                            <option value="mobile_money">Mobile Money</option>
                            <option value="withinhere">Withinhere</option>
                        </select>
                        <p class="help-block" style="margin-bottom: 0;">Use this mainly for transfers between accounts.</p>
                    </div>

                    
                </div>

                <div id="mobileMoneySection" style="display:none;">

    <div class="form-group">
        <label class="control-label col-md-2">
            Phone Number
        </label>

        <div class="col-md-4">
            <input type="text"
                   id="phone"
                   name="phone"
                   class="form-control">
        </div>
    </div>

</div>

<div id="bankSection" style="display:none;">

    <div class="form-group">

        <label class="control-label col-md-2">
            Bank
        </label>

        <div class="col-md-4">

            <select id="bank_id"
                    name="bank_id"
                    class="form-control">
            </select>

        </div>

        <label class="control-label col-md-2">
            Account Number
        </label>

        <div class="col-md-4">

            <input type="text"
                   id="account_number"
                   name="account_number"
                   class="form-control">

        </div>

    </div>

</div>


<div id="withinhereSection" style="display:none;">

    <div class="form-group">

        <label class="control-label col-md-2">
            Wallet ID
        </label>

        <div class="col-md-4">

            <input type="text"
                   id="wallet_id"
                   name="wallet_id"
                   class="form-control"
                   placeholder="Enter Wallet ID">

        </div>

    </div>

</div>

                <div class="form-group">
                    <label for="payee_name" class="control-label col-md-2">Payee / Recipient</label>
                    <div class="col-md-4">
                        <input type="text" name="payee_name" id="payee_name" class="form-control" placeholder="Supplier, person, or institution">
                    </div>

                    <!-- <label for="expense_category" class="control-label col-md-2">Expense Category</label>
                    <div class="col-md-4">
                        <select name="expense_category" id="expense_category" class="form-control select2">
                            <option value="">Select category</option>
                            <option value="operations">Operations</option>
                            <option value="supplies">Supplies</option>
                            <option value="transport">Transport</option>
                            <option value="utilities">Utilities</option>
                            <option value="maintenance">Maintenance</option>
                            <option value="bank_charges">Bank Charges</option>
                            <option value="other">Other</option>
                        </select>
                    </div> -->
                </div>

                <hr>

                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-md-12">
                        <h4 style="margin-top: 0; margin-bottom: 15px; color: #3c8dbc;">3. Purpose and Notes</h4>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title" class="control-label col-md-2">Title</label>
                    <div class="col-md-10">
                        <input type="text" name="title" id="title" class="form-control" placeholder="Short title for this fund movement">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description" class="control-label col-md-2">Description</label>
                    <div class="col-md-10">
                        <textarea name="description" id="description" rows="4" class="form-control" placeholder="Add a clear explanation of why the funds were moved"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="remarks" class="control-label col-md-2">Internal Remarks</label>
                    <div class="col-md-10">
                        <textarea name="remarks" id="remarks" rows="3" class="form-control" placeholder="Optional notes for finance/admin review"></textarea>
                    </div>
                </div>

                <hr>

                <hr>



            </div>

            <div class="box-footer">
         <button type="button"
        id="previewTransferBtn"
        class="btn btn-primary pull-right">
    Save Fund Movement
</button>
            </div>
        </form>
    </div>

    <div class="modal fade" id="transferPreviewModal">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    &times;

                </button>

                <h4 class="modal-title">
                    Confirm Transfer
                </h4>

            </div>

            <div class="modal-body">

                <table class="table table-bordered">

                    <tr>
                        <th>Payment Type</th>
                        <td id="previewPaymentType"></td>
                    </tr>

                    <tr id="walletIdRow" style="display:none;">
    <th>Wallet ID</th>
    <td id="previewWalletId"></td>
</tr>

                    <tr>
                        <th>Account Name</th>
                        <td id="accountName"></td>
                    </tr>

                    <tr>
                        <th>Phone</th>
                        <td id="resolvedPhone"></td>
                    </tr>

                    <tr id="bankNameRow" style="display:none;">
                        <th>Bank</th>
                        <td id="previewBankName"></td>
                    </tr>

                    <tr id="accountNumberRow" style="display:none;">
                        <th>Account Number</th>
                        <td id="previewAccountNumber"></td>
                    </tr>

                    <tr>
                        <th>Amount</th>
                        <td id="withdrawalAmount"></td>
                    </tr>

                    <tr>
                        <th>Gateway Fee</th>
                        <td id="gatewayFee"></td>
                    </tr>

                    <tr>
                        <th>Withinhere Fee</th>
                        <td id="withinhereFee"></td>
                    </tr>

                    <tr>
                        <th>Total Charge</th>
                        <td id="totalCharge"></td>
                    </tr>

                    <tr>
                        <th>Total Deducted</th>
                        <td id="totalDeducted"></td>
                    </tr>

                </table>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">

                    Cancel

                </button>

                <button type="button"
                        id="confirmTransferBtn"
                        class="btn btn-success">

                    Confirm & Save

                </button>

            </div>

        </div>

    </div>

</div>

<div class="modal fade" id="passwordConfirmModal">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button"
                        class="close"
                        data-dismiss="modal">
                    <span>&times;</span>
                </button>

                <h4 class="modal-title">
                    Confirm Password
                </h4>
            </div>

            <div class="modal-body">

                <div class="alert alert-danger"
                     id="passwordError"
                     style="display:none;">
                    Incorrect password.
                </div>

                <div class="form-group">
                    <label>Enter your password to continue</label>

                    <input
                        type="password"
                        id="confirmPassword"
                        class="form-control"
                        autocomplete="current-password">
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="btn btn-default"
                        data-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="verifyPasswordBtn"
                        class="btn btn-success">
                    Verify & Save
                </button>

            </div>

        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
    <script>

        function loadBanks() {

    $.ajax({
        url: 'https://withinheremobileapi.com/api/v1/payment/banks',
        type: 'GET',

        success: function(response) {

            if(response.status){

                let options =
                    '<option value="">Select Bank</option>';

                response.data.forEach(function(bank){

                    options +=
                        '<option value="' + bank.id + '">' +
                        bank.name +
                        '</option>';
                });

                $('#bank_id').html(options);
            }
        }
    });
}

function getOperator(phoneNumber){

    let digit = phoneNumber.charAt(2);

    switch(digit){

        case "7":
            return "airtel";

        case "6":
            return "mtn";

        case "5":
            return "zamtel";

        default:
            return null;
    }
}

        $(document).ready(function () {

        loadBanks();

$('#to_account').change(function(){

    let value = $(this).val();

    $('#mobileMoneySection').hide();
    $('#bankSection').hide();
    $('#withinhereSection').hide();

    if(value === 'mobile_money'){
        $('#mobileMoneySection').show();
    }

    if(value === 'bank_transfer'){
        $('#bankSection').show();
    }

    if(value === 'withinhere'){
        $('#withinhereSection').show();
    }

});

            $(".form-horizontal").validate();

            $('form').on('submit', function () {
                const $btn = $(this).find('button[type="submit"]');
                $btn.prop('disabled', true).text('Saving...');
            });
        });

$('#previewTransferBtn').click(function(){

    let destination = $('#to_account').val();
    let amount = $('#amount').val();

  $('#bankNameRow').hide();
$('#accountNumberRow').hide();
$('#walletIdRow').hide();

    // =========================
    // MOBILE MONEY
    // =========================

    if(destination === 'mobile_money'){

        let phone = $('#phone').val();

        let operator = getOperator(phone);

        if(!operator){
            alert('Unable to determine operator.');
            return;
        }

        $.ajax({

            url: 'https://withinheremobileapi.com/api/v1/payment/resolve/mobile',

            type: 'POST',

            contentType: 'application/json',

            data: JSON.stringify({

                phone: phone,

                operator: operator

            }),

            success: function(resolveResponse){

                if(!resolveResponse.success){
                    alert('Unable to verify mobile money account.');
                    return;
                }

                let user_id = "{{ $user_id }}";

                  $('#hidden_total_deducted').val(resolveResponse.data.totalDeducted);
                        $('#hidden_user_id').val(user_id);
                        $('#hidden_operator').val(operator);

                $.ajax({

                    url: 'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',

                    type: 'POST',

                    contentType: 'application/json',

                    data: JSON.stringify({

                        amount: amount,

                        payout_type: 'withinhere_to_mno'

                    }),

                    success: function(chargeResponse){

                        $('#previewPaymentType').text(
                            'Mobile Money'
                        );

                        $('#accountName').text(
                            resolveResponse.data.accountName
                        );

                        $('#resolvedPhone').text(
                            resolveResponse.data.phone
                        );

                        $('#withdrawalAmount').text(
                            Number(chargeResponse.data.withdrawalAmount).toFixed(2)
                        );

                        $('#gatewayFee').text(
                            Number(chargeResponse.data.gatewayFee).toFixed(2)
                        );

                        $('#withinhereFee').text(
                            Number(chargeResponse.data.withinhereFee).toFixed(2)
                        );

                        $('#totalCharge').text(
                            Number(chargeResponse.data.totalCharge).toFixed(2)
                        );

                        $('#totalDeducted').text(
                            Number(chargeResponse.data.totalDeducted).toFixed(2)
                        );

                        $('#transferPreviewModal').modal('show');

                    },

                    error: function(){
                        alert('Unable to retrieve charges.');
                    }

                });

            },

            error: function(){
                alert('Unable to verify mobile account.');
            }

        });

    }

    // =========================
    // BANK TRANSFER
    // =========================

    else if(destination === 'bank_transfer'){

        let bankId = $('#bank_id').val();
        let accountNumber = $('#account_number').val();

        if(!bankId){
            alert('Please select a bank.');
            return;
        }

        if(!accountNumber){
            alert('Please enter an account number.');
            return;
        }

        $.ajax({

            url: 'https://withinheremobileapi.com/api/v1/payment/resolve/bank',

            type: 'POST',

            contentType: 'application/json',

            data: JSON.stringify({

                accountNumber: accountNumber,

                bankId: bankId

            }),

            success: function(bankResponse){

                if(!bankResponse.success){
                    alert('Unable to verify bank account.');
                    return;
                }

                $.ajax({

                    url: 'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',

                    type: 'POST',

                    contentType: 'application/json',

                    data: JSON.stringify({

                        amount: amount,

                        payout_type: 'withinhere_to_bank'

                    }),

                    success: function(chargeResponse){

                        $('#previewPaymentType').text(
                            'Bank Transfer'
                        );

                        $('#bankNameRow').show();
                        $('#accountNumberRow').show();

                        $('#previewBankName').text(
                            $('#bank_id option:selected').text()
                        );

                        $('#previewAccountNumber').text(
                            accountNumber
                        );

                        $('#accountName').text(
                            bankResponse.data.accountName
                        );

                        $('#resolvedPhone').text('-');

                        $('#withdrawalAmount').text(
                            Number(chargeResponse.data.withdrawalAmount).toFixed(2)
                        );

                        $('#gatewayFee').text(
                            Number(chargeResponse.data.gatewayFee).toFixed(2)
                        );

                        $('#withinhereFee').text(
                            Number(chargeResponse.data.withinhereFee).toFixed(2)
                        );

                        $('#totalCharge').text(
                            Number(chargeResponse.data.totalCharge).toFixed(2)
                        );

                        $('#totalDeducted').text(
                            Number(chargeResponse.data.totalDeducted).toFixed(2)
                        );

                        $('#transferPreviewModal').modal('show');

                    },

                    error: function(){
                        alert('Unable to retrieve bank transfer charges.');
                    }

                });

            },

            error: function(){
                alert('Unable to verify bank account.');
            }

        });

    }

    // =========================
    // WITHINHERE
    // =========================
else if(destination === 'withinhere') {

    let walletId = $('#wallet_id').val();

    if(!walletId){

        alert('Please enter a wallet ID.');

        return;
    }

    $.ajax({

        url:
        'https://withinheremobileapi.com/api/v1/transfer/wallet/' +
        walletId,

        type: 'GET',

        success: function(walletResponse){

            if(!walletResponse.fullName){

                alert('Unable to verify wallet.');

                return;
            }

                let user_id = "{{ $user_id }}";

                        $('#hidden_user_id').val(user_id);
                        $('#hidden_receiver').val(walletResponse.userId);

            $.ajax({

                url:
                'https://withinheremobileapi.com/api/v1/transfer/withdrawal/charges',

                type: 'POST',

                contentType: 'application/json',

                data: JSON.stringify({

                    amount: amount,

                    payout_type:
                    'withinhere_to_user'

                }),

                success: function(chargeResponse){

                    $('#previewPaymentType').text(
                        'Withinhere Transfer'
                    );

                    $('#walletIdRow').show();

                    $('#previewWalletId').text(
                        walletId
                    );

                    $('#accountName').text(
                        walletResponse.fullName
                    );

                    $('#resolvedPhone').text(
                        '-'
                    );

                    $('#previewBankName').text('');
                    $('#previewAccountNumber').text('');

                    $('#withdrawalAmount').text(
                        Number(
                            chargeResponse.data.withdrawalAmount
                        ).toFixed(2)
                    );

                    $('#gatewayFee').text(
                        Number(
                            chargeResponse.data.gatewayFee
                        ).toFixed(2)
                    );

                    $('#withinhereFee').text(
                        Number(
                            chargeResponse.data.withinhereFee
                        ).toFixed(2)
                    );

                    $('#totalCharge').text(
                        Number(
                            chargeResponse.data.totalCharge
                        ).toFixed(2)
                    );

                    $('#totalDeducted').text(
                        Number(
                            chargeResponse.data.totalDeducted
                        ).toFixed(2)
                    );

                    $('#transferPreviewModal')
                        .modal('show');

                },

                error: function(){

                    alert(
                        'Unable to retrieve Withinhere transfer charges.'
                    );

                }

            });

        },

        error: function(){

            alert(
                'Unable to verify wallet ID.'
            );

        }

    });

}


});

$('#verifyPasswordBtn').on('click', function () {

    let password = $('#confirmPassword').val();

    if(password === ''){
        alert('Please enter your password.');
        return;
    }

    $(this)
        .prop('disabled', true)
        .text('Verifying...');

    $.ajax({

        url: "{{ url('loan/verify-password') }}",

        type: "POST",

        data: {
            password: password,
            _token: "{{ csrf_token() }}"
        },

        success: function(response){

            if(response.success){

                $('#passwordConfirmModal').modal('hide');

                $('#transferForm').submit();

            }else{

                $('#passwordError').show();

                $('#verifyPasswordBtn')
                    .prop('disabled', false)
                    .text('Verify & Save');

            }

        },

        error: function(){

            $('#passwordError').show();

            $('#verifyPasswordBtn')
                .prop('disabled', false)
                .text('Verify & Save');

        }

    });

});

$('#passwordConfirmModal').on('shown.bs.modal', function () {

    $('#verifyPasswordBtn')
        .prop('disabled', false)
        .text('Verify & Save');

    $('#confirmPassword').val('');

    $('#passwordError').hide();

});

$('#confirmTransferBtn').click(function(){

    $('#transferPreviewModal').modal('hide');

    $('#confirmPassword').val('');

    $('#passwordError').hide();

    $('#verifyPasswordBtn')
        .prop('disabled', false)
        .text('Verify & Save');

    $('#passwordConfirmModal').modal('show');

});






    </script>
@endsection