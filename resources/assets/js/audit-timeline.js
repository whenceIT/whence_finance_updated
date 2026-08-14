$(document).ready(function() {
    // Function to fetch and display audit timeline
    function fetchAuditTimeline(userId) {
        var auditContent = $('#audit-content-' + userId);
        auditContent.html('<div class="text-center"><i class="fa fa-spinner fa-spin"></i> Loading audit timeline...</div>');

        $.ajax({
            url: '{{ route("audits.user", ":userId") }}'.replace(':userId', userId),
            data: { ajax: 1 },
            method: 'GET',
            success: function(data) {
                if (data.audits && data.audits.length > 0) {
                    // Group audits by date
                    var groupedAudits = {};
                    var today = new Date();
                    var yesterday = new Date(today);
                    yesterday.setDate(yesterday.getDate() - 1); 

                    data.audits.forEach(function(audit) {
                        var auditDate = new Date(audit.created_at);
                        var dateKey;

                        if (auditDate.toDateString() === today.toDateString()) {
                            dateKey = 'Today';
                        } else if (auditDate.toDateString() === yesterday.toDateString()) {
                            dateKey = 'Yesterday';
                        } else {
                            dateKey = auditDate.toLocaleDateString('en-US', {
                                weekday: 'long',
                                year: 'numeric',
                                month: 'long',
                                day: 'numeric'
                            });
                        }

                        if (!groupedAudits[dateKey]) {
                            groupedAudits[dateKey] = [];
                        }
                        groupedAudits[dateKey].push(audit);
                    });

                    var html = '<div class="audit-timeline-wrapper" id="audit-timeline-' + userId + '">';
                    html += '<div class="timeline-header">';
                    html += '<span><i class="fa fa-history"></i> Click row to toggle</span>';
                    html += '<button class="refresh-audit-btn" data-user-id="' + userId + '"><i class="fa fa-refresh"></i> Refresh</button>';
                    html += '</div>';
                    html += '<div class="timeline">';
                    html += '<div class="timeline-line"></div>';

                    Object.keys(groupedAudits).forEach(function(dateKey) {
                        html += '<div class="date-group">';
                        html += '<div class="date-header">' + dateKey + '</div>';

                        groupedAudits[dateKey].forEach(function(audit) {
                            var auditTime = new Date(audit.created_at);
                            var hour = auditTime.getHours();
                            var isAfterHours = (hour >= 19 || hour <= 5);
                            var timeClass = isAfterHours ? ' after-hours' : '';
                            var timeIcon = isAfterHours ? '<i class="fa fa-moon"></i>' : '<i class="fa fa-clock"></i>';

                            html += '<div class="timeline-item">';
                            html += '<div class="timeline-marker"></div>';
                            html += '<div class="timeline-content">';
                            html += '<div class="timeline-event">' + audit.event + '</div>';
                            html += '<div class="timeline-meta">';
                            html += '<span class="timeline-time' + timeClass + '">' + timeIcon + ' ' + auditTime.toLocaleTimeString() + '</span>';
                            html += '<a href="{{ url("audits") }}/' + audit.id + '" class="timeline-btn" target="_blank"><i class="fa fa-eye"></i> Details</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        });

                        html += '</div>';
                    });

                    html += '</div>';
                    html += '</div>';
                    auditContent.html(html);
                } else {
                    auditContent.html('<p>No audit logs found for this user.</p>');
                }
            },
            error: function(xhr, status, error) {
                auditContent.html('<p class="text-danger">Error loading audit logs: ' + error + '</p>');
            }
        });
    }

    // Handle user row clicks
    $('.user-row').on('click', function(e) {
        // Prevent click if clicking on the View or Refresh button
        if ($(e.target).closest('a').length > 0) return;

        var userId = $(this).data('user-id');
        var auditRow = $('#audit-' + userId);

        if (auditRow.is(':visible')) {
            auditRow.hide();
        } else {
            // Check if content needs to be loaded
            var auditContent = $('#audit-content-' + userId);
            if (auditContent.find('.fa-spinner').length > 0) {
                fetchAuditTimeline(userId);
            }
            auditRow.show();
        }
    });

    // Handle refresh button clicks
    $(document).on('click', '.refresh-audit-btn', function(e) {
        e.stopPropagation();
        var userId = $(this).data('user-id');
        var btn = $(this);
        
        // Add spinning animation
        btn.html('<i class="fa fa-spinner fa-spin"></i> Refreshing...');
        btn.prop('disabled', true);
        
        fetchAuditTimeline(userId);
        
        // Reset button after a short delay
        setTimeout(function() {
            btn.html('<i class="fa fa-refresh"></i> Refresh');
            btn.prop('disabled', false);
        }, 1000);
    });
});