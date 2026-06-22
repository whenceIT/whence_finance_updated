@if(Sentinel::getUser()->role->role_id == 4)
<!-- Deposit Deadline Countdown Widget -->
<div id="depositDeadlineWidget" style="display: none;">
    <div class="deadline-widget">
        <div class="widget-header">
            <i class="fa fa-exclamation-triangle"></i> <span id="current-month">Monthly</span> Deposits Reminder
            <button class="widget-close" id="closeWidget">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="widget-body">
            <div class="countdown-display">
                <div class="countdown-item">
                    <span class="countdown-number" id="days">-</span>
                    <span class="countdown-text">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="hours">-</span>
                    <span class="countdown-text">Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="minutes">-</span>
                    <span class="countdown-text">Mins</span>
                </div>
            </div>
            <p class="widget-message" id="deadline-message">
                You have <strong id="days-remaining">-</strong> days to complete all required deposits for <strong id="deposit-month">June 2026</strong>.
            </p>
        </div>
    </div>
</div>

<style>
    #depositDeadlineWidget {
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
    
    .deadline-widget {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        width: 280px;
        overflow: hidden;
    }
    
    .widget-header {
        background: rgba(0, 0, 0, 0.2);
        padding: 10px 15px;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .widget-close {
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
    
    .widget-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .widget-body {
        padding: 15px;
    }
    
    .countdown-display {
        display: flex;
        justify-content: space-around;
        gap: 10px;
        margin-bottom: 12px;
    }
    
    .countdown-item {
        text-align: center;
        flex: 1;
    }
    
    .countdown-number {
        display: block;
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        line-height: 1;
    }
    
    .countdown-text {
        display: block;
        font-size: 10px;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        margin-top: 3px;
    }
    
    .widget-message {
        color: #fff;
        font-size: 12px;
        text-align: center;
        margin: 0;
        padding: 8px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 6px;
    }
    
    .widget-message strong {
        font-size: 14px;
    }
    
    /* Urgent state */
    .deadline-widget.urgent {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 8px 24px rgba(231, 76, 60, 0.4);
        }
        50% {
            box-shadow: 0 8px 24px rgba(231, 76, 60, 0.8);
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Deadline date: June 27, 2026
        const deadlineDate = new Date('2026-06-27T23:59:59').getTime();
        
        // Get current month name
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                           "July", "August", "September", "October", "November", "December"];
        const deadlineMonth = monthNames[5]; // June (0-indexed)
        const deadlineYear = 2026;
        
        // Update header and message with current month
        $('#current-month').text(deadlineMonth);
        $('#deposit-month').text(deadlineMonth + ' ' + deadlineYear);
        
        // Check if widget should be shown
        const widgetDismissed = localStorage.getItem('depositDeadlineWidgetDismissed');
        const dismissedDate = localStorage.getItem('depositDeadlineWidgetDismissedDate');
        const today = new Date().toDateString();
        
        // Show widget if not dismissed today and within 7 days of deadline
        const now = new Date().getTime();
        const daysUntilDeadline = Math.ceil((deadlineDate - now) / (1000 * 60 * 60 * 24));
        
        if (daysUntilDeadline <= 7 && daysUntilDeadline >= 0 && dismissedDate !== today) {
            $('#depositDeadlineWidget').fadeIn(300);
        }
        
        // Update countdown every second
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = deadlineDate - now;
            
            if (distance < 0) {
                clearInterval(countdownInterval);
                $('#days').text('0');
                $('#hours').text('0');
                $('#minutes').text('0');
                $('#days-remaining').text('0');
                $('#deadline-message').html('<strong style="color: #ffcccc;">Deposit deadline has passed.</strong>');
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            $('#days').text(days);
            $('#hours').text(hours);
            $('#minutes').text(minutes);
            $('#days-remaining').text(days);
            
            // Update message and style based on urgency
            if (days === 0) {
                $('#deadline-message').html('⚠️ <strong>URGENT: Less than 24 hours</strong> to complete all required deposits for <strong>' + deadlineMonth + ' ' + deadlineYear + '</strong>.');
                $('.deadline-widget').addClass('urgent');
            } else if (days === 1) {
                $('#deadline-message').html('You have <strong>1 day</strong> to complete all required deposits for <strong>' + deadlineMonth + ' ' + deadlineYear + '</strong>.');
                $('.deadline-widget').addClass('urgent');
            } else {
                $('#deadline-message').html('You have <strong>' + days + ' days</strong> to complete all required deposits for <strong>' + deadlineMonth + ' ' + deadlineYear + '</strong>.');
            }
        }, 1000);
        
        // Handle close button
        $('#closeWidget').on('click', function() {
            localStorage.setItem('depositDeadlineWidgetDismissed', 'true');
            localStorage.setItem('depositDeadlineWidgetDismissedDate', today);
            $('#depositDeadlineWidget').fadeOut(300);
        });
    });
</script>
@endif