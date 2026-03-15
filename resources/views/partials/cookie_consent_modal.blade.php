<!-- Cookie Consent Modal Styles -->
<style>
    /* Cookie Consent Modal Styles */
    .cookie-modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: flex;
        justify-content: center;
        align-items: flex-end;
        z-index: 100000;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }

    .cookie-modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .cookie-modal {
        position: fixed;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%) translateY(20px);
        background: white;
        border-radius: 12px 12px 0 0;
        max-width: 480px;
        width: 90%;
        box-shadow: 0 -4px 30px rgba(0, 0, 0, 0.3);
        transition: transform 0.3s ease;
        overflow: hidden;
        margin-bottom: 0;
    }

    .cookie-modal-overlay.show .cookie-modal {
        transform: translateX(-50%) translateY(0);
    }

    .cookie-modal-header {
        background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
        color: white;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .cookie-modal-header i {
        font-size: 28px;
    }

    .cookie-modal-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .cookie-modal-body {
        padding: 24px;
        background: white;
    }

    .cookie-modal-body p {
        color: var(--text-secondary);
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 16px;
    }

    .cookie-modal-body .cookie-features {
        background: var(--light-bg);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 16px;
    }

    .cookie-modal-body .cookie-features li {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
        font-size: 13px;
        color: var(--text-primary);
    }

    .cookie-modal-body .cookie-features li:last-child {
        margin-bottom: 0;
    }

    .cookie-modal-body .cookie-features li i {
        color: var(--secondary-color);
        font-size: 16px;
    }

    .cookie-modal-footer {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .cookie-modal-footer .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    .cookie-modal-footer .btn-accept {
        background: var(--secondary-color);
        color: white;
    }

    .cookie-modal-footer .btn-accept:hover {
        background: #45b369;
        transform: translateY(-1px);
    }

    .cookie-modal-footer .btn-decline {
        background: var(--light-bg);
        color: var(--text-secondary);
    }

    .cookie-modal-footer .btn-decline:hover {
        background: var(--border-color);
    }
</style>

<!-- Cookie Consent Modal HTML -->
<div class="cookie-modal-overlay" id="cookieConsentModal">
    <div class="cookie-modal" style="background: white;">
        <div class="cookie-modal-header">
            <i class="fa fa-cookie-bite"></i>
            <h3>Cookie & Storage Consent</h3>
        </div>
        <div class="cookie-modal-body" style="background: white;">
            <p>We use cookies and local storage to provide you with a better learning experience. Please accept to enable the following features:</p>
            <ul class="cookie-features">
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Save your learning progress and preferences</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Remember your login session for seamless access</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Enable video streaming for Google Meet integration</span>
                </li>
                <li>
                    <i class="fa fa-check-circle"></i>
                    <span>Track course completion and certificates</span>
                </li>
            </ul>
            <p style="font-size: 12px; color: #888; margin-bottom: 0;">
                <i class="fa fa-info-circle"></i> Your data is stored locally on your device and is not shared with third parties.
            </p>
        </div>
        <div class="cookie-modal-footer">
            <button type="button" class="btn btn-decline" id="declineCookies">Not Now</button>
            <button type="button" class="btn btn-accept" id="acceptCookies">Accept & Continue</button>
        </div>
    </div>
</div>

<!-- Cookie Consent JavaScript -->
<script>
    // Cookie Consent Handler
    const COOKIE_CONSENT_KEY = 'whenceLearnCookieConsent';
    const USER_DATA_KEY = 'whenceLearnUserData';
    
    function initCookieConsent() {
        // Check if user has accepted cookies
        // Modal always shows until user accepts (not when declined)
        const consentAccepted = localStorage.getItem(COOKIE_CONSENT_KEY) === 'accepted';
        
        if (!consentAccepted) {
            // Show cookie consent modal after a short delay
            setTimeout(function() {
                $('#cookieConsentModal').addClass('show');
            }, 1000);
        } else {
            // If consent was previously given, restore user data
            restoreUserData();
        }
    }
    
    function restoreUserData() {
        const userData = localStorage.getItem(USER_DATA_KEY);
        if (userData) {
            try {
                const parsed = JSON.parse(userData);
                console.log('User data restored from localStorage:', parsed);
                // You can use this data for personalization
                window.learningUserData = parsed;
            } catch (e) {
                console.error('Error parsing user data:', e);
            }
        }
    }
    
    function acceptCookies() {
        // Store consent in localStorage
        localStorage.setItem(COOKIE_CONSENT_KEY, 'accepted');
        localStorage.setItem(COOKIE_CONSENT_KEY + '_date', new Date().toISOString());
        
        // Store user details for learning experience and streaming
        @if($user)
        var userData = {
            id: {{ $user->id }},
            first_name: '{{ $user->first_name }}',
            last_name: '{{ $user->last_name }}',
            email: '{{ $user->email }}',
            role: '{{ $role ? $role->name : 'Staff' }}',
            is_trainer: {{ $user->istrainer ?? 0 }},
            accepted_at: new Date().toISOString(),
            purpose: ['learning_progress', 'streaming', 'personalization']
        };
        localStorage.setItem(USER_DATA_KEY, JSON.stringify(window.learningUserData = userData));
        console.log('User data stored in localStorage for learning and streaming');
        @endif
        
        // Hide modal with animation
        $('#cookieConsentModal').removeClass('show');
        
        // Show success message
        showFlashMessage('success', 'Cookie Consent', 'Your preferences have been saved. Enjoy your learning experience!', 'fa-check-circle');
    }
    
    function declineCookies() {
        // Just hide the modal - it will show again on next visit until accepted
        // This ensures the modal always comes up until user accepts
        $('#cookieConsentModal').removeClass('show');
        
        console.log('Cookie consent deferred - will show again on next visit');
    }
    
    // Bind cookie consent buttons
    $(document).ready(function() {
        $('#acceptCookies').on('click', acceptCookies);
        $('#declineCookies').on('click', declineCookies);
        
        // Close modal on overlay click
        $('#cookieConsentModal').on('click', function(e) {
            if (e.target === this) {
                declineCookies();
            }
        });
        
        // Initialize cookie consent on page load
        initCookieConsent();
    });
</script>
