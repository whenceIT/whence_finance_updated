@php 
    $ledgerBlocker = \App\Helpers\BlockerHelper::ledger_blocker();
@endphp

<!-- General Reminder Widget -->
<div id="generalReminderWidget" style="display: none;">
    <div class="reminder-widget">
        <div class="widget-header">
            <i class="fa fa-exclamation-circle"></i> Ledger Reconciliation Required
            <button class="widget-close" id="closeReminderWidget">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="widget-body">
            <div class="alert-icon">
                <i class="fa fa-warning"></i>
            </div>
            <p class="widget-title">Action Required</p>
            <p class="widget-amount" id="reminder-amount">-</p>
            <p class="widget-message">
                Not recorded in <strong id="reminder-deposit-type">-</strong> in one of the months between January and May.
            </p>
            <div class="widget-info">
                <p><strong>If you recorded the payment under expenses:</strong></p>
                <p>Please provide the <strong>reference numbers</strong> recorded in the expense to I.T, so that they are moved to deposits (internal funds).</p>
                <p><strong>If not:</strong></p>
                <p>Please make these payments. If nothing was recorded under expenses, contact the risk manager.</p>
            </div>
            <a href="{{ url('user/branch_deposits') }}" class="btn-primary-widget">Go to Branch Deposits</a>
        </div>
    </div>
</div>

<style>
    #generalReminderWidget {
        position: fixed;
        bottom: 20px;
        left: 20px;
        z-index: 9999;
        animation: slideIn 0.5s ease-out;
    }
    
    @keyframes slideIn {
        from {
            transform: translateX(-100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .reminder-widget {
        background: linear-gradient(135deg, #ff9500 0%, #ff6b00 100%);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        width: 320px;
        overflow: hidden;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 8px 24px rgba(255, 149, 0, 0.4);
        }
        50% {
            box-shadow: 0 8px 24px rgba(255, 149, 0, 0.8);
        }
    }
    
    .reminder-widget .widget-header {
        background: rgba(0, 0, 0, 0.2);
        padding: 10px 15px;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .reminder-widget .widget-close {
        background: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.2s;
    }
    
    .reminder-widget .widget-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .reminder-widget .widget-body {
        padding: 20px 15px 15px;
        text-align: center;
    }
    
    .reminder-widget .alert-icon {
        font-size: 48px;
        color: #fff;
        margin-bottom: 10px;
        animation: shake 2s infinite;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
    .reminder-widget .widget-title {
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        margin: 0 0 10px 0;
    }
    
    .reminder-widget .widget-amount {
        color: #fff;
        font-size: 32px;
        font-weight: bold;
        margin: 10px 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .reminder-widget .widget-message {
        color: #fff;
        font-size: 13px;
        margin: 10px 0;
        padding: 10px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 6px;
    }
    
    .reminder-widget .widget-message strong {
        font-weight: 700;
        text-decoration: underline;
    }
    
    .reminder-widget .widget-info {
        color: #fff;
        font-size: 11px;
        text-align: left;
        margin: 12px 0;
        padding: 12px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 6px;
        line-height: 1.5;
    }
    
    .reminder-widget .widget-info p {
        margin: 0 0 8px 0;
    }
    
    .reminder-widget .widget-info p:last-child {
        margin-bottom: 0;
    }
    
    .reminder-widget .widget-info strong {
        font-weight: 700;
    }
    
    .reminder-widget .btn-primary-widget {
        display: inline-block;
        background: #fff;
        color: #ff9500;
        padding: 10px 20px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: bold;
        font-size: 13px;
        margin-top: 10px;
        transition: all 0.3s;
    }
    
    .reminder-widget .btn-primary-widget:hover {
        background: #f0f0f0;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
</style>

<script>
    $(document).ready(function() {
        var ledgerBlocker = <?php echo json_encode($ledgerBlocker); ?>;
        
        // Check if widget should be shown
        const widgetDismissed = localStorage.getItem('generalReminderWidgetDismissed');
        const dismissedDate = localStorage.getItem('generalReminderWidgetDismissedDate');
        const today = new Date().toDateString();
        
        // Show widget if ledger blocker is active and not dismissed today
        if (ledgerBlocker && ledgerBlocker.status && dismissedDate !== today) {
            // Update amount
            if (ledgerBlocker.amount !== undefined && ledgerBlocker.amount !== null) {
                var formatted = new Intl.NumberFormat('en-US', { 
                    style: 'currency', 
                    currency: 'ZMW',
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(ledgerBlocker.amount);
                $('#reminder-amount').text(formatted);
            }
            
            // Update deposit type
            if (ledgerBlocker.deposit_type) {
                $('#reminder-deposit-type').text(ledgerBlocker.deposit_type);
            }
            
            // Show the widget
            $('#generalReminderWidget').fadeIn(300);
        }
        
        // Handle close button
        $('#closeReminderWidget').on('click', function() {
            localStorage.setItem('generalReminderWidgetDismissed', 'true');
            localStorage.setItem('generalReminderWidgetDismissedDate', today);
            $('#generalReminderWidget').fadeOut(300);
        });
    });
</script>
