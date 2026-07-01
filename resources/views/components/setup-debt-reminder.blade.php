
<!-- Setup Debt Reminder Widget -->
<div id="setupDebtReminderWidget" style="display: none;">
    <div class="debt-reminder-widget">
        <div class="widget-header">
            <i class="fa fa-bell"></i> Setup Debt Payment Reminder
            <button class="widget-close" id="closeDebtWidget">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div class="widget-body">
            <div class="countdown-display">
                <div class="countdown-item">
                    <span class="countdown-number" id="debt-days">-</span>
                    <span class="countdown-text">Days</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="debt-hours">-</span>
                    <span class="countdown-text">Hours</span>
                </div>
                <div class="countdown-item">
                    <span class="countdown-number" id="debt-minutes">-</span>
                    <span class="countdown-text">Mins</span>
                </div>
            </div>
            <p class="widget-message" id="debt-message">
                Record your <strong style="font-size: 16px;">K5,000 minimum</strong> setup debt payment before <strong id="debt-deadline-date">July 5, 2026</strong>
            </p>
            <a href="{{ url('user/branch_deposits') }}" class="widget-action-btn">
                <i class="fa fa-money"></i> Record Payment Now
            </a>
        </div>
    </div>
</div>

<style>
    #setupDebtReminderWidget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideInRight 0.5s ease-out;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    .debt-reminder-widget {
        background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        width: 300px;
        overflow: hidden;
    }
    
    .debt-reminder-widget .widget-header {
        background: rgba(0, 0, 0, 0.2);
        padding: 10px 15px;
        color: #fff;
        font-weight: bold;
        font-size: 13px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .debt-reminder-widget .widget-close {
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
    
    .debt-reminder-widget .widget-close:hover {
        background: rgba(255, 255, 255, 0.2);
    }
    
    .debt-reminder-widget .widget-body {
        padding: 15px;
    }
    
    .debt-reminder-widget .countdown-display {
        display: flex;
        justify-content: space-around;
        gap: 10px;
        margin-bottom: 12px;
    }
    
    .debt-reminder-widget .countdown-item {
        text-align: center;
        flex: 1;
    }
    
    .debt-reminder-widget .countdown-number {
        display: block;
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        line-height: 1;
    }
    
    .debt-reminder-widget .countdown-text {
        display: block;
        font-size: 10px;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        margin-top: 3px;
    }
    
    .debt-reminder-widget .widget-message {
        color: #fff;
        font-size: 13px;
        text-align: center;
        margin: 0 0 12px 0;
        padding: 10px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 6px;
        line-height: 1.5;
    }
    
    .debt-reminder-widget .widget-action-btn {
        display: block;
        width: 100%;
        text-align: center;
        padding: 10px 15px;
        background: #fff;
        color: #e67e22;
        font-weight: 600;
        font-size: 14px;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .debt-reminder-widget .widget-action-btn:hover {
        background: #f8f9fa;
        color: #d35400;
        transform: translateY(-1px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    }
    
    /* Urgent state (last day) */
    .debt-reminder-widget.urgent {
        background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        animation: pulseDebt 2s infinite;
    }
    
    @keyframes pulseDebt {
        0%, 100% {
            box-shadow: 0 8px 24px rgba(231, 76, 60, 0.4);
        }
        50% {
            box-shadow: 0 8px 24px rgba(231, 76, 60, 0.8);
        }
    }
    
    /* Mobile responsive */
    @media (max-width: 768px) {
        #setupDebtReminderWidget {
            bottom: 10px;
            right: 10px;
            left: 10px;
        }
        
        .debt-reminder-widget {
            width: auto;
        }
    }
    
    /* Stack widgets on mobile */
    @media (max-width: 768px) {
        #depositDeadlineWidget {
            bottom: auto;
            top: 80px;
            left: 10px;
            right: 10px;
        }
        
        #setupDebtReminderWidget {
            bottom: 10px;
            left: 10px;
            right: 10px;
        }
        
        .deadline-widget,
        .debt-reminder-widget {
            width: auto;
        }
    }
</style>

<script>
    $(document).ready(function() {
        // Deadline date: July 5, 2026 at 23:59:59
        const debtDeadline = new Date('2026-07-05T23:59:59').getTime();
        
        // Calculate days until deadline
        const now = new Date().getTime();
        const daysUntilDebt = Math.ceil((debtDeadline - now) / (1000 * 60 * 60 * 24));
        
        // Show widget if deadline hasn't passed and within 30 days
        if (daysUntilDebt >= 0 && daysUntilDebt <= 30) {
            $('#setupDebtReminderWidget').fadeIn(300);
        }
        
        // Update countdown every second
        const debtCountdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = debtDeadline - now;
            
            if (distance < 0) {
                clearInterval(debtCountdownInterval);
                $('#debt-days').text('0');
                $('#debt-hours').text('0');
                $('#debt-minutes').text('0');
                $('#debt-message').html('<strong style="color: #ffcccc;">Setup debt payment deadline has passed.</strong>');
                $('.debt-reminder-widget').addClass('urgent');
                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            $('#debt-days').text(days);
            $('#debt-hours').text(hours);
            $('#debt-minutes').text(minutes);
            
            // Update message based on urgency
            if (days === 0) {
                $('#debt-message').html('⚠️ <strong style="font-size: 16px;">URGENT: Less than 24 hours!</strong><br>Record your <strong>K5,000 minimum</strong> setup debt payment before <strong>July 5, 2026</strong>');
                $('.debt-reminder-widget').addClass('urgent');
            } else if (days === 1) {
                $('#debt-message').html('You have <strong>1 day</strong> left!<br>Record your <strong style="font-size: 16px;">K5,000 minimum</strong> setup debt payment before <strong>July 5, 2026</strong>');
                $('.debt-reminder-widget').addClass('urgent');
            } else if (days <= 3) {
                $('#debt-message').html('You have <strong>' + days + ' days</strong> left!<br>Record your <strong style="font-size: 16px;">K5,000 minimum</strong> setup debt payment before <strong>July 5, 2026</strong>');
            } else {
                $('#debt-message').html('Record your <strong style="font-size: 16px;">K5,000 minimum</strong> setup debt payment before <strong>July 5, 2026</strong>');
            }
        }, 1000);
        
        // Handle close button
        $('#closeDebtWidget').on('click', function() {
            $('#setupDebtReminderWidget').fadeOut(300);
        });
    });
</script>
