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
</style>

<!-- JavaScript for Notification Panel -->
<script>
    function toggleNotificationDropdown(event) {
        event.preventDefault();
        var isActive = $('#notificationPanel').hasClass('active');
        $('#notificationOverlay').toggleClass('active');
        $('#notificationPanel').toggleClass('active');

        if (!isActive) {
            // Opening the panel, fetch notifications
            fetchNotifications();
        }
        markAllNotificationsAsRead();
    }

    function fetchNotifications() {
        fetch('/notifications')
            .then(response => response.json())
            .then(data => {
                renderNotifications(data);
            })
            .catch(error => {
                console.error('Error fetching notifications:', error);
                document.getElementById('notificationList').innerHTML = '<div style="text-align: center; padding: 20px; color: #999;"><i class="fa fa-exclamation-triangle" style="font-size: 40px; margin-bottom: 10px;"></i><p>Error loading notifications</p></div>';
            });
    }

    function renderNotifications(notifications) {
        const list = document.getElementById('notificationList');
        const badge = document.getElementById('notificationBadge');

        if (notifications.length === 0) {
            list.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;"><i class="fa fa-bell-o" style="font-size: 40px; margin-bottom: 10px;"></i><p>No notifications</p></div>';
            badge.style.display = 'none';
            return;
        }

        badge.textContent = notifications.length;
        badge.style.display = 'inline';

        let html = '';
        notifications.forEach(notification => {
            let iconHtml = '';
            if (notification.type === 'training_recommendation' && notification.upload_poster) {
                iconHtml = `<img src="${notification.upload_poster}" style="width: 32px; height: 32px; object-fit: cover; border-radius: 4px;" alt="Training Resource">`;
            } else {
                const iconClass = getNotificationIcon(notification.type);
                iconHtml = `<i class="${iconClass}" style="font-size: 16px;"></i>`;
            }
            html += `
                <div class="notification-item" style="padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; transition: background 0.2s;" onmouseover="this.style.background='#f8f9fa'" onmouseout="this.style.background=''" onclick="handleNotificationClick('${notification.id}', '${notification.link_to}')">
                    <div style="display: flex; align-items: flex-start;">
                        <div style="margin-right: 10px; color: #007bff;">
                            ${iconHtml}
                        </div>
                        <div style="flex: 1;">
                            <p style="margin: 0; font-size: 14px; color: #333;">${notification.message}</p>
                            <small style="color: #999; font-size: 12px;">${notification.time_ago}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        list.innerHTML = html;
    }

    function getNotificationIcon(type) {
        switch(type) {
            case 'loan_created':
                return 'fa fa-money';
            case 'loan_declined':
                return 'fa fa-times-circle';
            case 'risk_review':
                return 'fa fa-exclamation-triangle';
            case 'loan_transaction_approval':
                return 'fa fa-check-circle';
            case 'user_anniversary_3_months':
            case 'user_anniversary_6_months':
                return 'fa fa-birthday-cake';
            case 'anniversary_summary':
                return 'fa fa-users';
            case 'training_recommendation':
                return 'fa fa-play-circle';
            default:
                return 'fa fa-bell';
        }
    }

    function openNotificationPanel() {
        document.getElementById('notificationOverlay').classList.add('active');
        document.getElementById('notificationPanel').classList.add('active');
        loadNotifications();
    }

    function closeNotificationPanel() {
        document.getElementById('notificationOverlay').classList.remove('active');
        document.getElementById('notificationPanel').classList.remove('active');
    }

    function loadNotifications() {
        // Fetch notifications from server
        // This would typically be an AJAX call to your notifications endpoint
        var notificationList = document.getElementById('notificationList');
        notificationList.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;"><i class="fa fa-spinner fa-spin" style="font-size: 30px;"></i><p style="margin-top: 10px;">Loading notifications...</p></div>';

        // Simulated delay - replace with actual AJAX call
        setTimeout(function() {
            // Example: You would fetch from your notifications API
            // For now, showing empty state
            notificationList.innerHTML = '<div style="text-align: center; padding: 40px; color: #999;"><i class="fa fa-bell-o" style="font-size: 40px; margin-bottom: 10px;"></i><p>No notifications</p></div>';
        }, 1000);
    }

    // Load notification count on page load
    document.addEventListener('DOMContentLoaded', function() {
        fetchNotifications();
    });

    function handleNotificationClick(notificationId, linkTo) {
        // Mark notification as read
        fetch('/notifications/' + notificationId + '/mark-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Navigate to the link
                window.location.href = linkTo;
            } else {
                // Still navigate even if marking as read failed
                window.location.href = linkTo;
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
            // Still navigate even if there's an error
            window.location.href = linkTo;
        });
    }

    function markAllNotificationsAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close the panel and update the count
                // Refresh the notification count
                if (typeof updateNotificationCount === 'function') {
                    updateNotificationCount();
                }
            } else {
                console.log('Failed to mark notifications as read');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            console.log('Failed to mark notifications as read');
        });
    }
</script>