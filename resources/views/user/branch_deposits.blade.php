@extends('layouts.master')

@section('title', 'Monthly Deposits')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Monthly Deposits</h1>

        <p class="text-muted" style="margin-top: 8px;">
            <i class="fa fa-info-circle"></i>
            Deposits must be completed in the order shown. 
            Only the currently active deposit section can be opened.
            The next deposit will unlock automatically after the previous one is completed.
            This is to ensure that deposit priorities are followed correctly.
        </p>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-body" id="depositSteps">
                {{-- Deposit steps will be dynamically loaded here --}}
            </div>
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

    function today() {
        var d = new Date();
        return d.getFullYear() + '-' +
            String(d.getMonth()+1).padStart(2,'0') + '-' +
            String(d.getDate()).padStart(2,'0');
    }

    function lockAll() {
        $('.deposit-item').each(function () {
            $(this).find('input,button').prop('disabled', true);
            $(this).css('opacity', 0.5);
        });
    }

    function unlock(id) {
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .find('input,button').prop('disabled', false)
            .closest('.deposit-item').css('opacity', 1);
    }

    function markCompleted(id) {
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .closest('.deposit-item')
            .addClass('bg-success')
            .css('opacity', 1);

        $('.deposit-item[data-deposit-id="'+id+'"]')
           .find('input.amount, input.reference, .complete-btn')
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
                <div class="deposit-item" data-deposit-id="${d.id}">
                    <h4>${d.name}</h4>
                    <p class="existing-amount text-muted">Current Amount: 0</p>

                    <small class="text-muted">
                        Enter Payment Reference Number (Example: MP260223.0953.J76581)
                    </small>
                    <input type="text" class="form-control reference" placeholder="MP260223.0953.J76581" required>
                    <br>

                    <input type="number" class="form-control amount" placeholder="Enter amount to add">
                    <br>
                    <button class="btn btn-primary complete-btn">Save Deposit</button>
                    ${skipBtn}
                </div><hr>
            `);
        });

        checkCompletedDeposits();
    });

    /* ---------- CHECK COMPLETED ---------- */
    function checkCompletedDeposits() {
        $.get('https://lms2backend.whencefinancesystem.com/check-deposits-report', {
            branch: branchId,
            date: today().slice(0,7)
        }, function (response) {

            lockAll();

            if (!response || !response.length) {
                unlock(depositOrder[0]);
                return;
            }

            var completedIds = response.map(r => r.deposit_type);
            completedIds.forEach(markCompleted);

            response.forEach(r => {
                let box = $('.deposit-item[data-deposit-id="'+r.deposit_type+'"]');
                box.find('.existing-amount').text(`Current Amount: ${r.amount}`);
                box.find('input.amount').val('');
            });

            for (let id of depositOrder) {
                if (!completedIds.includes(id)) {
                    unlock(id);
                    break;
                }
            }
        });
    }

    /* ---------- SAVE DEPOSIT ---------- */
    $(document).on('click', '.complete-btn', function () {

        let box = $(this).closest('.deposit-item');
        currentDepositType = box.data('deposit-id');

        let raw = box.find('.amount').val();
        currentDepositAmount = parseFloat(raw);

        currentReferenceNumber = box.find('.reference').val().trim();

        if (!currentReferenceNumber) {
            alert('Please enter a payment reference number.');
            return;
        }

        if (isNaN(currentDepositAmount) || currentDepositAmount <= 0) {
            alert('Enter a valid amount to add');
            return;
        }

        if (
            (currentDepositType == 1 && currentDepositAmount < 100) ||
            (currentDepositType == 3 && currentDepositAmount < 100) ||
            (currentDepositType == 5 && currentDepositAmount < 100)
        ) {
            alert('Please enter minimum deposit amount.');
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
        success: function (response) {

$.ajax({
    url: 'https://lms2backend.whencefinancesystem.com/create-deposit-log',
    type: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({
        deposit_type: currentDepositType,
        office_id: branchId,
        user_id: userId, 
        amount: currentDepositAmount,
        reference_number: currentReferenceNumber
    })
})
.done(function () {
    console.log('Deposit log created successfully');
    location.reload();
})
.fail(function (xhr, status, error) {
    console.error('Deposit log failed:', status, error, xhr.responseText);
    location.reload();
});

        },
        error: function (err) {
            console.error('Deposit failed:', err);
        }
    });
});
    /* ---------- SKIP OPTIONAL (SAVE ZERO) ---------- */
    $(document).on('click', '.skip-btn', function () {

        if (!confirm('Skip Managers Housing deposit? This will be recorded as 0.')) return;

        let depositId = $(this).closest('.deposit-item').data('deposit-id');

        $.ajax({
            url: 'https://lms2backend.whencefinancesystem.com/create-deposit',
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
</script>
@endsection




