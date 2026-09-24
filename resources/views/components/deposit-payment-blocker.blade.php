@php
    $user = Sentinel::getUser();

    $blockages = collect();
    $status = false;
    $blockage = null;

    if ($user) {
        $blockages = \App\Models\Blockage::where('office_id', $user->office_id)->get();
        $status = $blockages->isNotEmpty();
        $blockage = $blockages->first();
    }
@endphp
@if($status && request()->path() != 'user/branch_deposits')
    @if($blockage->time_to_unlock === null || $blockage->time_to_unlock > now())
    <!-- If $blockage time_to_unlock has not passed or time_to_unlock is null - continue to display the blocking -->
    <!-- Deposit Payment Blocker Modal -->
<div id="depositPaymentBlocker" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(10, 10, 30, 0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.4s ease-out;
">
    <div style="
        background: rgba(255, 255, 255, 0.98);
        padding: 35px 30px;
        border-radius: 16px;
        max-width: 420px;
        width: 90%;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.35);
        text-align: center;
        border: 1px solid rgba(255, 255, 255, 0.3);
        animation: slideUp 0.5s ease-out;
    ">
        <!-- Warning Icon -->
        <div style="
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(238, 90, 111, 0.3);
            animation: pulse 2s infinite;
        ">
            <i class="fa fa-exclamation-triangle" style="font-size: 32px; color: #fff;"></i>
        </div>
        
        <!-- Title -->
        <h3 style="
            margin: 0 0 15px 0;
            font-size: 22px;
            font-weight: 700;
            color: #2d3436;
            letter-spacing: -0.5px;
        ">
            Access Restricted
        </h3>
        
        <!-- Message -->
        <p style="
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 20px 0;
            color: #636e72;
        ">
            {{ $blockage->reason ?? 'Please make the deposit arrears in order to access the system' }}
        </p>
       
        
        <!-- Contact Info -->
        <div style="
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 10px;
            padding: 14px 18px;
            border-left: 4px solid #3498db;
            margin-bottom: 20px;
        ">
            <p style="
                margin: 0;
                font-size: 14px;
                color: #495057;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
            ">
                <i class="fa fa-info-circle" style="color: #3498db; font-size: 16px;"></i>
                <span>Contact <strong style="color: #2d3436;">Risk Department</strong></span>
            </p>
        </div>
         @if($blockage->time_to_unlock)
        <div style="
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid #fbbf24;
        ">
            <p style="
                margin: 0;
                font-size: 14px;
                color: #92400e;
                font-weight: 600;
            ">
                <i class="fa fa-clock-o" style="margin-right: 6px;"></i>
                Access will be restored in: <span id="timeToUnlockCountdown" style="font-weight: 700;">--:--:--</span>
            </p>
        </div>
        <script>
            (function() {
                var unlockTime = new Date("{{ $blockage->time_to_unlock->toIso8601String() }}").getTime();
                var countdownElement = document.getElementById('timeToUnlockCountdown');

                function updateCountdown() {
                    var now = new Date().getTime();
                    var distance = unlockTime - now;

                    if (distance <= 0) {
                        countdownElement.textContent = '00:00:00';
                        clearInterval(timer);
                        location.reload();
                        return;
                    }

                    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    var timeString = (hours < 10 ? '0' : '') + hours + ':' +
                                     (minutes < 10 ? '0' : '') + minutes + ':' +
                                     (seconds < 10 ? '0' : '') + seconds;

                    countdownElement.textContent = timeString;
                }

                updateCountdown();
                var timer = setInterval(updateCountdown, 1000);
            })();
        </script>
        @else
        <!-- Action Button -->
        <a href="/user/branch_deposits" style="
            display: inline-block;
            padding: 12px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(102, 126, 234, 0.4)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.3)';">
            <i class="fa fa-credit-card" style="margin-right: 6px;"></i>
            Make Deposit Payment
        </a>
        @endif
        <!-- Lock Icon -->
        <div style="margin-top: 20px; opacity: 0.3;">
            <i class="fa fa-lock" style="font-size: 18px; color: #636e72;"></i>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    @keyframes slideUp {
        from {
            transform: translateY(30px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            box-shadow: 0 8px 20px rgba(238, 90, 111, 0.3);
        }
        50% {
            transform: scale(1.05);
            box-shadow: 0 12px 30px rgba(238, 90, 111, 0.5);
        }
    }
    
    /* Prevent any interaction with the page */
    #depositPaymentBlocker ~ * {
        pointer-events: none !important;
        user-select: none !important;
    }
</style>

<script>
    // Prevent closing the modal
    document.addEventListener('DOMContentLoaded', function() {
        // Disable escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }, true);
        
        // Disable right-click
        const blocker = document.getElementById('depositPaymentBlocker');
        if (blocker) {
            blocker.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
            
            // Prevent clicking outside to close
            blocker.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Disable F12 and other dev tools shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.keyCode === 123 || // F12
                (e.ctrlKey && e.shiftKey && e.keyCode === 73) || // Ctrl+Shift+I
                (e.ctrlKey && e.shiftKey && e.keyCode === 74) || // Ctrl+Shift+J
                (e.ctrlKey && e.keyCode === 85)) { // Ctrl+U
                e.preventDefault();
                return false;
            }
        });
    });
</script>
    @endif
@endif
