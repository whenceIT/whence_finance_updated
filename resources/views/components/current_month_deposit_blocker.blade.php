<div class="modal fade" id="currentMonthDepositBlocker" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-sm" style="margin: 20% auto;">
        <div class="modal-content">
            <div class="modal-header bg-blue" style="border-radius: 0; padding: 10px 15px;">
                <h4 class="modal-title" style="margin: 0; font-size: 16px; font-weight: 600;">
                    <i class="fa fa-exclamation-triangle"></i> Monthly Deposit Required
                </h4>
            </div>
            <div class="modal-body text-center" style="padding: 20px;">
                <i class="fa fa-university" style="font-size: 48px; color: #126cf3; margin-bottom: 15px;"></i>
                <p style="margin-bottom: 15px; font-size: 14px;">
                    You have not fully paid your <strong>June's and current required madatory deposits in Building, Administration, and Statutory</strong>. 
                    Please make the necessary deposits to continue using this system.
                </p>
                <p style="font-size: 12px; color: #999; margin-bottom: 20px;">
                    <i class="fa fa-info-circle"></i> 
                    <span id="deposit-deadline-info">Deadline: Loading...</span>
                </p>
            </div>
            <div class="modal-footer" style="border-top: none; padding: 10px 15px;">
                <a href="{{ url('user/branch_deposits') }}" class="btn btn-blue" style="background-color: #1252f3; border-color: #f39c12; color: #fff; width: 100%;">
                    <i class="fa fa-money"></i> Make Deposits Now
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.btn-orange {
    transition: all 0.3s ease;
}
.btn-orange:hover {
    background-color: #e67e22 !important;
    border-color: #e67e22 !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(243, 156, 18, 0.4);
}
.modal-header.bg-orange {
    background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
}
</style>

<script>
$(document).ready(function() {
    var monthNames = ["January", "February", "March", "April", "May", "June", 
                      "July", "August", "September", "October", "November", "December"];
    var currentMonth = monthNames[new Date().getMonth()];
    var currentYear = new Date().getFullYear();
    
    $('#deposit-deadline-info').text(currentMonth + ' ' + currentYear + ' deposits required');
    
    $('#currentMonthDepositBlocker').modal({
        backdrop: 'static',
        keyboard: false,
        show: true
    });
});
</script>