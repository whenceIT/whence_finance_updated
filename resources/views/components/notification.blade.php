<!-- Notification Component -->
<div class="notification-overlay" id="notificationOverlay" onclick="closeNotificationPanel()"></div>
<div class="notification-panel" id="notificationPanel">
    <div class="notification-panel-header">
        <h3>Notifications</h3>
        <div style="display: flex; gap: 10px;">
            <button onclick="closeNotificationPanel()" style="background: none; border: none; font-size: 24px; color: #999; cursor: pointer;">&times;</button>
        </div>
    </div>
    <div class="notification-panel-body" id="notificationList">
        <div style="text-align: center; padding: 20px; color: #999;">
            <i class="fa fa-bell-o" style="font-size: 40px; margin-bottom: 10px;"></i>
            <p>No notifications</p>
        </div>
    </div>
</div>

<!-- CSS for Notification Panel -->
<style>
    .notification-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9998;
        display: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .notification-overlay.active {
        display: block;
        opacity: 1;
    }

    .notification-panel {
        position: fixed;
        top: 0;
        right: -400px;
        width: 380px;
        height: 100vh;
        background: white;
        box-shadow: -5px 0 30px rgba(0, 0, 0, 0.3);
        z-index: 9999;
        transition: right 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        display: flex;
        flex-direction: column;
    }

    .notification-panel.active {
        right: 0;
    }

    .notification-panel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid #eee;
        background: #00a04a;
        color: white;
    }

    .notification-panel-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
    }

    .notification-panel-body {
        flex: 1;
        overflow-y: auto;
        padding: 0;
    }

    .notification-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        cursor: pointer;
        transition: background 0.2s;
    }

    .notification-item:hover {
        background: #f8f9fa;
    }

    .notification-item.unread {
        background: #e8f4fd;
    }

    .notification-item-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
        font-size: 14px;
    }

    .notification-item-message {
        color: #666;
        font-size: 13px;
        margin-bottom: 8px;
        line-height: 1.4;
    }

    .notification-item-time {
        color: #999;
        font-size: 12px;
    }

    .notification-confirm {
        display: flex;
        gap: 8px;
        margin-top: 10px;
        opacity: 0;
        max-height: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .notification-confirm.active {
        opacity: 1;
        max-height: 40px;
    }

    .notification-item.deleting {
        opacity: 0;
        max-height: 0;
        margin: 0;
        padding: 0;
        border: none;
        transition: all 0.3s ease;
    }
</style>

