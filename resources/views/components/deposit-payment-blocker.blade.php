@php
    // Check if deposit payment is required for June 2026
    $showDepositBlocker = false;
    $blockerMonth = 'June 2026';
    
    // Only show for specific roles (not admin or super users)
    $user = Sentinel::getUser();
    $role = $user && $user->roles->first() ? $user->roles->first()->id : null;
    
    // Exclude admin roles (1, 4, 6, 9, 10) from the blocker
    if ($user && !in_array($role, [1, 4, 6, 9, 10, 11])) {
        $officeId = $user->office_id;
        
        // Check if office has made the required deposit for June 2026
        $juneDeposit = \DB::table('deposits')
            ->where('office', $officeId)
            ->whereYear('date', 2026)
            ->whereMonth('date', 6)
            ->where('deposit_type', 3) // Building fund deposit type
            ->where('status', 1) // Approved deposits only
            ->sum('amount');
        
        // Get required amount for the deposit type
        $requiredAmount = \DB::table('deposit_types')
            ->where('id', 3)
            ->value('monthly_amount') ?? 0;
        
        // Show blocker if deposit is not fully paid
        if ($juneDeposit < $requiredAmount) {
            $showDepositBlocker = true;
        }
    }
@endphp

@if($showDepositBlocker)
<!-- Deposit Payment Blocker Modal -->
<div id="depositPaymentBlocker" style="
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.95);
    z-index: 999999;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.5s ease-in-out;
">
    <div style="
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 40px;
        border-radius: 20px;
        max-width: 600px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        text-align: center;
        color: white;
        animation: slideIn 0.6s ease-out;
    ">
        <!-- Warning Icon -->
        <div style="
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 30px;
            animation: pulse 2s infinite;
        ">
            <i class="fa fa-exclamation-triangle" style="font-size: 50px; color: #fff;"></i>
        </div>
        
        <!-- Title -->
        <h2 style="
            margin: 0 0 20px 0;
            font-size: 32px;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        ">
            System Access Restricted
        </h2>
        
        <!-- Message -->
        <p style="
            font-size: 18px;
            line-height: 1.8;
            margin: 0 0 30px 0;
            color: rgba(255, 255, 255, 0.95);
        ">
            Please make the deposit payment for <strong>{{ $blockerMonth }}</strong> in order to access the system.
        </p>
        
        <!-- Contact Info -->
        <div style="
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
        ">
            <p style="
                margin: 0;
                font-size: 16px;
                color: rgba(255, 255, 255, 0.9);
            ">
                <i class="fa fa-info-circle" style="margin-right: 8px;"></i>
                Contact <strong>Risk Department</strong> for more details
            </p>
        </div>
        
        <!-- Decorative Elements -->
        <div style="margin-top: 30px; opacity: 0.6;">
            <i class="fa fa-lock" style="font-size: 24px;"></i>
        </div>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    @keyframes slideIn {
        from {
            transform: translateY(-50px);
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
            opacity: 1;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.8;
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
        document.getElementById('depositPaymentBlocker').addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
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
        
        // Prevent clicking outside to close
        document.getElementById('depositPaymentBlocker').addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
</script>
@endif
