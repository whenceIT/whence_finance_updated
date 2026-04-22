$(document).ready(function() {
    // Function to update notification count
    function updateNotificationCount() {
        $.ajax({
            url: '/notification-count',
            method: 'GET',
            success: function(response) {
                var count = response.count || 0;
                var mobileBadge = $('#notificationBadge');
                var desktopBadge = $('#notificationBadgeDesk');

                if (count > 0) {
                    var displayCount = count > 99 ? '99+' : count;
                    mobileBadge.text(displayCount).show();
                    desktopBadge.text(displayCount).show();
                } else {
                    mobileBadge.hide();
                    desktopBadge.hide();
                }
            },
            error: function(xhr) {
                console.error('Error fetching notification count:', xhr);
            }
        });
    }

    // Initial load
    updateNotificationCount();

    // Poll every 30 seconds
    setInterval(updateNotificationCount, 30000);

            // Run scheduled commands at 17:00 local time on Tuesdays
            setInterval(function() {
                var now = new Date();
                if (now.getHours() === 17 && now.getDay() === 2) {
            fetch('/run-scheduled-commands', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('Scheduled commands executed successfully at 17:00');
                } else {
                    console.error('Error executing scheduled commands:', data.message);
                }
            })
            .catch(error => {
                console.error('Error calling scheduled commands:', error);
            });
        }
    }, 10 * 60 * 1000); // Check every 10 minutes
});