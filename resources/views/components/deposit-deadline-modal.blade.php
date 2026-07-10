<!-- Deposit Widgets Container -->
<div class="deposit-widgets-container">
    <div id="depositDeadlineWidget">
        <div class="deadline-widget">
            <div class="widget-header">
                <i class="fa fa-exclamation-triangle"></i> <span id="current-month">Monthly</span> Deposits | Weekly Reminder
                <button class="widget-close" id="closeWidget">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            <div class="widget-body">
                <div class="countdown-display">
                    <div class="countdown-item"><span class="countdown-number" id="days">-</span><span class="countdown-text">Days</span></div>
                    <div class="countdown-item"><span class="countdown-number" id="hours">-</span><span class="countdown-text">Hrs</span></div>
                    <div class="countdown-item"><span class="countdown-number" id="minutes">-</span><span class="countdown-text">Min</span></div>
                </div>
                <p class="widget-message" id="deadline-message">Complete <b id="deadline-name">--</b>, and prevent system locking.</p>
            </div>
        </div>
    </div>
</div>

<style>
    .deposit-widgets-container {
        position: fixed;
        bottom: 15px;
        left: 15px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-width: 240px;
    }
    
    #depositDeadlineWidget {
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
        background: linear-gradient(135deg, #eab366 0%, #ff9d00 100%);
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        width: 240px;
        overflow: hidden;
    }
    
    .widget-header {
        background: rgba(0, 0, 0, 0.2);
        padding: 8px 12px;
        color: #fff;
        font-weight: bold;
        font-size: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .widget-close {
        background: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        width: 18px;
        height: 18px;
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
        padding: 10px;
    }
    
    .countdown-display {
        display: flex;
        justify-content: space-around;
        gap: 8px;
        margin-bottom: 8px;
    }
    
    .countdown-item {
        text-align: center;
        flex: 1;
    }
    
    .countdown-number {
        display: block;
        font-size: 22px;
        font-weight: bold;
        color: #fff;
        line-height: 1;
    }
    
    .countdown-text {
        display: block;
        font-size: 9px;
        color: rgba(255, 255, 255, 0.8);
        text-transform: uppercase;
        margin-top: 2px;
    }
    
    .widget-message {
        color: #fff;
        font-size: 11px;
        text-align: center;
        margin: 0;
        padding: 6px;
        background: rgba(0, 0, 0, 0.15);
        border-radius: 4px;
    }
    
    .widget-message strong {
        font-size: 12px;
    }
</style>

<script>
    $(document).ready(function() {
        const deadlineData = @json($deadline ?? (object)[]);
        const deadlineDate = deadlineData.countdown_date ? new Date(deadlineData.countdown_date).getTime() : null;
        const deadlineName = deadlineData.name || 'Building Deposit';
        const monthNames = ["January", "February", "March", "April", "May", "June", 
                           "July", "August", "September", "October", "November", "December"];
        
        if (deadlineDate) {
            const deadlineMonth = monthNames[new Date(deadlineData.countdown_date).getMonth()];
            $('#current-month').text(deadlineMonth);
        }
        
        $('#deadline-name').text(deadlineName);
        
        const updateCountdown = function() {
            if (!deadlineDate) {
                clearInterval(countdownInterval);
                return;
            }
            
            const now = new Date().getTime();
            const distance = deadlineDate - now;
            
            if (distance < 0) {
                clearInterval(countdownInterval);
                $('#days').text('0');
                $('#hours').text('0');
                $('#minutes').text('0');
                $('#deadline-message').html('<strong style="color: #ffcccc;">Deposit deadline has passed.</strong>');

                // Trigger server-side lock via AJAX (only for authenticated users)
                // try {
                //     $.ajax({
                //         url: '{{ route('deadline.trigger-lock') }}',
                //         method: 'POST',
                //         data: {
                //             _token: '{{ csrf_token() }}'
                //         }
                //     }).done(function(res) {
                //         console.log('Deadline trigger response', res);
                //     }).fail(function(err) {
                //         console.error('Failed to trigger deadline lock', err);
                //     });
                // } catch (e) {
                //     console.error(e);
                // }

                return;
            }
            
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            
            $('#days').text(days);
            $('#hours').text(hours);
            $('#minutes').text(minutes);
            
            if (days === 0) {
                $('.deadline-widget').addClass('urgent');
            }
        };
        
        const countdownInterval = setInterval(updateCountdown, 1000);
        updateCountdown();
        
        if (deadlineDate) {
            const now = new Date().getTime();
            const daysUntilDeadline = Math.ceil((deadlineDate - now) / (1000 * 60 * 60 * 24));
            
            if (daysUntilDeadline <= 30 && daysUntilDeadline >= 0) {
                $('#depositDeadlineWidget').fadeIn(300);
            }
        }
        
        $('#closeWidget').on('click', function() {
            $('#depositDeadlineWidget').fadeOut(300);
        });
    });
</script>