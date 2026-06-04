@extends('layouts.master')

@section('title', 'Monthly Deposits')

@section('content')
<style>
.deposit-card {
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    padding: 20px;
    margin-bottom: 20px;
    background: #fff;
    border-left: 5px solid #3c8dbc;
    transition: 0.2s ease-in-out;
}

.deposit-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.deposit-card.completed {
    border-left: 5px solid #00a65a;
    background: #f6fffa;
}

.deposit-title {
    font-weight: 600;
    margin-bottom: 10px;
}

.deposit-header-box {
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    margin-bottom: 20px;
}

.deposit-label {
    font-weight: 600;
    margin-bottom: 5px;
}

.btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

.btn-warning {
    background-color: #f39c12;
    border-color: #e08e0b;
}
</style>

<!-- Test with Anchor House First -->
@if( Sentinel::getUser()->role->role_id == 4 && in_array(Sentinel::getUser()->office_id, [6,8])) 
    <x-debt-blocker/>
@endif

<div class="content-wrapper">
    
    @if( Sentinel::getUser()->role->role_id == 4 && in_array(Sentinel::getUser()->office_id, [6,8])) 
        @php
            $currentMonthYear = date('F Y', strtotime('now'));
        @endphp
        @include('risk.partials.office-exemptions-card', ['officeIdParam' => Sentinel::getUser()->office_id, 'cardtitle' => 'Please make sure you have made the following ENABLED deposits for ' . $currentMonthYear])

    @endif
    <section class="content-header">

        <div class="deposit-header-box">
            <h2 style="margin-top:0;">Monthly Deposits</h2>

            <p class="text-muted" style="margin-bottom:15px;">
                <i class="fa fa-info-circle"></i>
                Deposits must be completed in order. 
                Only the currently active deposit section can be opened.
                The next deposit unlocks automatically after completion.
            </p>

            <div style="max-width:300px;">
                <label class="deposit-label">Select Month</label>
                <input type="month" id="monthFilter" class="form-control">
            </div>
        </div>

    </section>

    <section class="content">
        <div id="depositSteps">
            {{-- Deposit steps injected here --}}
        </div>
    </section>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="depositConfirmModal" tabindex="-1" role="dialog" aria-labelledby="depositConfirmLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">&times;</button>
        <h4 class="modal-title" id="depositConfirmLabel">Confirm Deposit</h4>
      </div>
      <div class="modal-body">
        <p>Before proceeding, please confirm:</p>
        <ol>
          <li>The total amount covers the full required deposit.</li>
          <li>The deposit has gone through and will not be returned.</li>
          <li>Clicking "Confirm" means you accept responsibility for this transaction.</li>
        </ol>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" id="modalConfirmBtn" class="btn btn-success">Confirm</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function () {

    var branchId = {{ $office_id }};
    var userId = {{$userId}};
    var depositOrder = [];

    var currentDepositType = null;
    var currentDepositAmount = null;
    var currentReferenceNumber = null;
    var currentPaymentMethod = null;

    // Default Month = Current Month
    var now = new Date();
    var currentMonth = now.getFullYear() + '-' + 
        String(now.getMonth()+1).padStart(2,'0');
    $('#monthFilter').val(currentMonth);

    function today() {
        var selectedMonth = $('#monthFilter').val();
        return selectedMonth + '-01';
    }

    function lockAll() {
        $('.deposit-item').each(function () {
            $(this).find('input,button,select').prop('disabled', true);
            $(this).css('opacity', 0.5);
        });
    }

    function unlock(id) {
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .find('input,button,select').prop('disabled', false)
            .closest('.deposit-item').css('opacity', 1);
    }

    function unlockAll() {
        $('.deposit-item').each(function () {
            $(this).find('input,button,select').prop('disabled', false);
            $(this).css('opacity', 1);
        });
    }

    function markCompleted(id) {
        let box = $('.deposit-item[data-deposit-id="'+id+'"]');

        box.addClass('completed')
           .css('opacity', 1);

        // Re-enable fields so user can add more deposits
        box.find('input.amount, input.reference, select.payment-method, .complete-btn')
           .prop('disabled', false);
    }

    /* ---------- LOAD DEPOSIT TYPES ---------- */
    $.get('https://lms2backend.whencefinancesystem.com/deposit-types', function (res) {

        var deposits = res.data || res;
        var container = $('#depositSteps').empty();

        deposits.forEach(function (d) {

            depositOrder.push(d.id);

            var skipBtn = (d.id == 2 || d.id == 3 || d.id == 1)
                ? `<button class="btn btn-warning skip-btn">Skip Deposit</button>`
                : '';

            container.append(`
                <div class="deposit-item deposit-card" data-deposit-id="${d.id}">
                    <h4 class="deposit-title">${d.name}</h4>
                    <p class="existing-amount text-muted">Current Amount: 0</p>

                    <label class="deposit-label">Payment Method</label>
                    <select class="form-control payment-method">
                        <option value="">Select Method</option>
                        <option value="airtel">Airtel Money</option>
                        <option value="zanaco_express">Zanaco Express</option>
                        <option value="mtn">MTN MoMo</option>
                        <option value="zanaco_cash">Zanaco Cash Deposit</option>
                        <option value="access">Access</option>
                        <option value="withinhere">WithinHere</option>
                    </select>
                    <br>

                    <small class="text-muted format-hint">Enter Payment Reference Number</small>
                    <input type="text" class="form-control reference" placeholder="Enter reference number" required>
                    <br>

                    <input type="number" class="form-control amount" placeholder="Enter amount to add">
                    <br>
                    <button class="btn btn-primary complete-btn">Save Deposit</button>
                    ${skipBtn}
                </div>
            `);
        });

        checkCompletedDeposits();
    });

    /* ---------- CHECK COMPLETED ---------- */
    function checkCompletedDeposits() {

        var selectedMonth = $('#monthFilter').val();

        $.get('https://lms2backend.whencefinancesystem.com/check-deposits-report', {
            branch: branchId,
            date: selectedMonth
        }, function (response) {

            // lockAll();
            unlockAll();
            $('.existing-amount').text('Current Amount: 0');
            $('.deposit-item').removeClass('completed');
            $('.deposit-item input').val('');
            $('.deposit-item select').val('');
        });
    }

    $('#monthFilter').change(function(){
        checkCompletedDeposits();
    });

    /* ---------- PAYMENT METHOD FORMAT HINT ---------- */
    $(document).on('change', '.payment-method', function () {

        let box = $(this).closest('.deposit-item');
        let hint = box.find('.format-hint');
        let referenceInput = box.find('.reference');

        referenceInput.val('');

        switch ($(this).val()) {
            case 'airtel':
                hint.text('Format: MP260223.0953.J76581');
                referenceInput.attr('placeholder', 'MP260223.0953.J76581');
                break;

            case 'zanaco_express':
                hint.text('Format: 12 digit number (002504072516)');
                referenceInput.attr('placeholder', '002504072516');
                break;

            case 'mtn':
                hint.text('Format: 10 digit number (8704564481)');
                referenceInput.attr('placeholder', '8704564481');
                break;

            case 'zanaco_cash':
                hint.text('Format: 16 digit number (0502605703255600)');
                referenceInput.attr('placeholder', '0502605703255600');
                break;

            case 'access':
                hint.text('Format: FJB2606341708208');
                referenceInput.attr('placeholder', 'FJB2606341708208');
                break;
            case 'withinhere':
                hint.text('Format: 1777356230718931');
                referenceInput.attr('placeholder', '1777356230718931');
                break;

            default:
                hint.text('Enter Payment Reference Number');
        }
    });

    /* ---------- SAVE DEPOSIT ---------- */
    $(document).on('click', '.complete-btn', function () {

        let box = $(this).closest('.deposit-item');
        currentDepositType = box.data('deposit-id');

        let raw = box.find('.amount').val();
        currentDepositAmount = parseFloat(raw);
        currentReferenceNumber = box.find('.reference').val().trim();
        let paymentMethod = box.find('.payment-method').val();
        currentPaymentMethod = paymentMethod;

        if (!paymentMethod) {
            alert('Please select a payment method.');
            return;
        }

        if (!currentReferenceNumber) {
            alert('Please enter a payment reference number.');
            return;
        }

        if (isNaN(currentDepositAmount) || currentDepositAmount <= 0) {
            alert('Enter a valid amount to add');
            return;
        }

        // Format Validation
        let valid = false;

        switch (paymentMethod) {
            case 'airtel':
                valid = /^[A-Za-z]{2}\d{6}\.\d{4}\.[A-Za-z]\d{5}$/.test(currentReferenceNumber);
                break;
            case 'zanaco_express':
                valid = /^\d{12}$/.test(currentReferenceNumber);
                break;
            case 'mtn':
                valid = /^\d{10}$/.test(currentReferenceNumber);
                break;
            case 'zanaco_cash':
                valid = /^\d{16}$/.test(currentReferenceNumber);
                break;
            case 'access':
                valid = /^[A-Za-z]{3}\d{13}$/.test(currentReferenceNumber);
                break;
            case 'withinhere':
                valid = /^\d+$/.test(currentReferenceNumber);
                break;
        }

        if (!valid) {
            alert('Invalid reference format for selected payment method.');
            return;
        }

        $('#depositConfirmModal').modal('show');
    });

    $('#modalConfirmBtn').click(function () {

        $('#depositConfirmModal').modal('hide');

        $.ajax({
            url: 'https://lms2backend.whencefinancesystem.com/create-deposit',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                deposit_type: currentDepositType,
                office: branchId,
                amount: currentDepositAmount,
                date: today()
            }),
            success: function () {

                $.ajax({
                    url: 'https://lms2backend.whencefinancesystem.com/create-deposit-log',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        deposit_type: currentDepositType,
                        office_id: branchId,
                        user_id: userId,
                        amount: currentDepositAmount,
                        reference_number: currentReferenceNumber,
                        deposit_method: currentPaymentMethod 
                    })
                })
                .done(function () {
                    location.reload();
                })
                .fail(function () {
                    location.reload();
                });

            }
        });
    });

    /* ---------- SKIP OPTIONAL ---------- */
    $(document).on('click', '.skip-btn', function () {

        if (!confirm('Skip Managers Housing deposit? This will be recorded as 0.')) return;

        let depositId = $(this).closest('.deposit-item').data('deposit-id');

        $.ajax({
            url: 'http:localhost:5000/create-deposit',
            type: 'POST',
            data: {
                deposit_type: depositId,
                office: branchId,
                amount: 0,
                date: today()
            },
            success: function () {
                location.reload();
            }
        });

    });
});

function loadOfficesSettings() {
    $.get('/settings/platform/offices-settings', function(data) {
        var officeId = new URLSearchParams(window.location.search).get('office_id');
        if (officeId && data && data.OfficeName) {
            renderExemptions(data);
            return;
        }
        var tableHtml = '';
        data.forEach(function(o) {
            tableHtml += '<tr>' +
                '<td>' + o.name + '</td>' +
                '<td>' + o.code + '</td>' +
                '<td>' + (o.admin ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.building ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.statutory ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '<td>' + (o.set_up_debt ? '<span class="label label-success">Enabled</span>' : '<span class="label label-danger">Disabled</span>') + '</td>' +
                '</tr>';
        });
        $('#offices-settings-table').html(tableHtml);
    });
}

function renderExemptions(data) {
    var exemptions = [
        {
            title: 'Administration Department Fee Deposit',
            description: data.admin ? 'Obligated to make payment to Administration Department fee deposit' : 'Excluded from making payment to Administration Department fee deposit',
            enabled: data.admin,
            color: data.admin ? '#28a745' : '#dc3545'
        },
        {
            title: 'Building & Infrastructure Fee Deposit',
            description: data.building ? 'Obligated to make payment to Building & Infrastructure fee deposits' : 'Excluded from making payment to Building & Infrastructure fee deposits',
            enabled: data.building,
            color: data.building ? '#28a745' : '#dc3545'
        },
        {
            title: 'Statutory Payments Deposit',
            description: data.statutory ? 'Obligated to make payment to Statutory payments deposits' : 'Excluded from making payment to Statutory payments deposits',
            enabled: data.statutory,
            color: data.statutory ? '#28a745' : '#dc3545'
        },
        {
            title: 'Setup Cost Debt Payment',
            description: data.set_up_debt ? 'Obligated to make payment towards K5,000 minimum debt for setup cost' : 'Excluded from making payment towards setup cost debt',
            enabled: data.set_up_debt,
            color: data.set_up_debt ? '#28a745' : '#dc3545'
        }
    ];
    
    var html = '<div style="display: flex; flex-direction: column; gap: 12px;">';
    exemptions.forEach(function(e) {
        html += '<div style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; border-radius: 8px; background: ' + e.color + '15; border-left: 4px solid ' + e.color + ';">' +
            '<div style="flex: 1;">' +
            '<div style="font-weight: 600; color: #343a40; font-size: 14px; margin-bottom: 4px;">' + e.title + '</div>' +
            '<div style="color: #6c757d; font-size: 13px;">' + e.description + '</div>' +
            '</div>' +
            '<div style="text-align: center; min-width: 100px;">' +
            '<span style="background: ' + e.color + '; color: white; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600;">' +
            (e.enabled ? 'ENABLED' : 'DISABLED') +
            '</span>' +
            '</div>' +
            '</div>';
    });
    html += '</div>';
    $('#office-exemptions-body').html(html).show();
}

var officeIdParam = new URLSearchParams(window.location.search).get('office_id');
if (officeIdParam) {
    loadOfficesSettings();
}
</script>
@endsection



