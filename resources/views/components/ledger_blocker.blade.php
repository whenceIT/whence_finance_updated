@php 
 $ledgerBlocker = \App\Helpers\BlockerHelper::ledger_blocker();
@endphp
<div class="modal fade" id="ledgerBlockerModal" tabindex="-1" role="dialog" aria-labelledby="ledgerBlockerModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background:#fff3cd;color:#856404;">
                <h5 class="modal-title" id="ledgerBlockerModalLabel">Action Required</h5>
            </div>
            <div class="modal-body">
                <p id="ledgerBlockerMessage" class="mb-2">Your office requires attention before proceeding.</p>
                <p id="ledgerBlockerAmount" class="mb-3" style="font-size:1.75rem;font-weight:700;color:#c0392b;"></p>
                <p class="mb-3">Not recorded in <span id="depositType" style="font-weight:700" class="font-weight-bold text-danger"></span> for the months between January and May.</p>
                <div class="alert alert-warning" role="alert" style="margin-bottom:0;">
                    <p class="mb-2">If you recorded the payment under expenses, please provide the <strong>reference_numbers</strong> recorded in the expense to I.T, so that they are moved to deposits (internal funds)</p>
                    <p class="mb-0">If not, please make these payments. If nothing was recorded under expenses, contact the risk manager.</p>
                </div>
            </div>
            <div class="modal-footer">
                <div style="float: left; font-size: 13px; color: #666;">
                    Salaries, Savings, Housing acivating: <span id="countdown" style="font-weight: 700; color: #c0392b;"></span> from now.
                </div>
                <a href="{{ url('user/branch_deposits') }}" class="btn btn-primary">Go to Branch Deposits</a>
            </div>
        </div>
    </div>
</div>

<script>
    (function(){
        var ledgerBlocker = <?php echo json_encode($ledgerBlocker); ?>;
        if (ledgerBlocker && ledgerBlocker.status) {
            document.addEventListener('DOMContentLoaded', function(){
                var $modal = $('#ledgerBlockerModal');
                var details = '';
                if (ledgerBlocker.amount !== undefined && ledgerBlocker.amount !== null) {
                    details = 'Amount: ' + ledgerBlocker.amount;
                }
                var amountEl = document.getElementById('ledgerBlockerAmount');
                if (amountEl && ledgerBlocker.amount !== undefined && ledgerBlocker.amount !== null) {
                    var formatted = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'ZMW' }).format(ledgerBlocker.amount);
                    amountEl.textContent = formatted;
                }
                var depositEl = document.getElementById('depositType');
                if (depositEl && ledgerBlocker.deposit_type) depositEl.textContent = ledgerBlocker.deposit_type;
                $modal.modal({backdrop: 'static', keyboard: false});
                $modal.modal('show');
                $modal.on('hide.bs.modal', function(e){
                    e.preventDefault();
                    return false;
                });
            });
        }
        
        // Countdown to 21st of current month
        function updateCountdown() {
            var now = new Date();
            var year = now.getFullYear();
            var month = now.getMonth();
            var deadline = new Date(year, month, 22);
            var diff = deadline - now;
            var days = Math.floor(diff / (1000 * 60 * 60 * 24));
            var hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var mins = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            var countdownEl = document.getElementById('countdown');
            if (countdownEl) {
                countdownEl.textContent = days + 'd ' + hours + 'h ' + mins + 'm';
            }
        }
        updateCountdown();
        setInterval(updateCountdown, 60000);
    })();
</script>