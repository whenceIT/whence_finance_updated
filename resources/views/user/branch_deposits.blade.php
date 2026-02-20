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
    var depositOrder = [];

    var currentDepositType = null;
    var currentDepositAmount = null;

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

    // ---------- Keep input enabled but mark completed ----------
    function markCompleted(id) {
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .closest('.deposit-item')
            .addClass('bg-success')
            .css('opacity', 1);

        // Keep input and button enabled so managers can add more
        $('.deposit-item[data-deposit-id="'+id+'"]')
            .find('input.amount, .complete-btn')
            .prop('disabled', false);
    }

    /* ---------- LOAD DEPOSIT TYPES ---------- */
    $.get('https://lms2backend.whencefinancesystem.com/deposit-types', function (res) {

        var deposits = res.data || res;
        var container = $('#depositSteps').empty();

        deposits.forEach(function (d) {

            depositOrder.push(d.id);

            var skipBtn = d.id == 2
                ? `<button class="btn btn-warning skip-btn">Skip Deposit</button>`
                : '';

            container.append(`
                <div class="deposit-item" data-deposit-id="${d.id}">
                    <h4>${d.name}</h4>
                    <p class="existing-amount text-muted">Current Amount: 0</p>
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
                // Show existing amount
                box.find('.existing-amount').text(`Current Amount: ${r.amount}`);
                // Clear input for new addition
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

        if (isNaN(currentDepositAmount) || currentDepositAmount <= 0) {
            alert('Enter a valid amount to add');
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
                location.reload();
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




