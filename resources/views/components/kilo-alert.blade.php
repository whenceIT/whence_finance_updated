<div id="kiloAlertContainer" style="position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 400px;"></div>

<script>
window.KiloAlert = {
    container: null,
    init: function() {
        this.container = document.getElementById('kiloAlertContainer');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'kiloAlertContainer';
            this.container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; display: flex; flex-direction: column; gap: 10px; max-width: 400px;';
            document.body.appendChild(this.container);
        }
    },
    show: function(message, type, duration) {
        if (!this.container) this.init();
        
        const colors = {
            success: { bg: '#27ae60', text: '#fff' },
            error: { bg: '#e74c3c', text: '#fff' },
            warning: { bg: '#f39c12', text: '#fff' },
            info: { bg: '#3498db', text: '#fff' }
        };
        
        const icon = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            warning: 'fa-exclamation-triangle',
            info: 'fa-info-circle'
        };
        
        const bg = colors[type] || colors.info;
        const ic = icon[type] || icon.info;
        
        const div = document.createElement('div');
        div.style.cssText = `
            background: ${bg.bg}; color: ${bg.text}; padding: 14px 16px; border-radius: 8px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.15); font-size: 14px; display: flex; 
            align-items: center; gap: 10px; animation: kiloAlertSlideIn 0.3s ease-out;
        `;
        div.innerHTML = `
            <i class="fa ${ic}" style="font-size: 18px;"></i>
            <span>${message}</span>
        `;
        
        this.container.appendChild(div);
        
        if (duration !== false) {
            setTimeout(() => {
                div.style.animation = 'kiloAlertSlideOut 0.3s ease-in';
                setTimeout(() => div.remove(), 300);
            }, duration || 3000);
        }
    },
    success: function(msg, dur) { this.show(msg, 'success', dur); },
    error: function(msg, dur) { this.show(msg, 'error', dur); },
    warning: function(msg, dur) { this.show(msg, 'warning', dur); },
    info: function(msg, dur) { this.show(msg, 'info', dur); }
};

// Initialize on load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => window.KiloAlert.init());
} else {
    window.KiloAlert.init();
}

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes kiloAlertSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes kiloAlertSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
`;
document.head.appendChild(style);
</script>`;