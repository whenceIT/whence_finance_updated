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

                    var html = '<div class="timeline" style="position: relative; padding-left: 30px;">';
                    html += '<style>.timeline::before { content: ""; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: #ddd; }';
                    html += '.timeline-item { position: relative; margin-bottom: 20px; }';
                    html += '.timeline-marker { position: absolute; left: -22px; top: 5px; width: 12px; height: 12px; background: #007bff; border-radius: 50%; border: 2px solid #fff; }';
                    html += '.timeline-content { background: #f8f9fa; padding: 10px; border-radius: 5px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }';
                    html += '.date-group { margin-bottom: 30px; }';
                    html += '.date-header { font-weight: bold; font-size: 16px; color: #007bff; margin-bottom: 15px; padding: 5px 10px; background: #e9ecef; border-radius: 3px; }</style>';

                    Object.keys(groupedAudits).forEach(function(dateKey) {
                        html += '<div class="date-group">';
                        html += '<div class="date-header">' + dateKey + '</div>';

                        groupedAudits[dateKey].forEach(function(audit) {
                            html += '<div class="timeline-item">';
                            html += '<div class="timeline-marker"></div>';
                            html += '<div class="timeline-content">';
                            var auditTime = new Date(audit.created_at);
                            var hour = auditTime.getHours();
                            var isAfterHours = (hour >= 19 || hour <= 5);
                            var timeClass = isAfterHours ? ' style="color: #dc3545; font-weight: bold;"' : '';

                            html += '<h5>' + audit.event + '</h5>';
                            html += '<p><strong>Time:</strong> <span' + timeClass + '>' + auditTime.toLocaleTimeString() + '</span></p>';
                            html += '<a href="{{ url("audits") }}/' + audit.id + '" class="btn btn-sm btn-info" target="_blank">Details</a>';
                            html += '</div></div>';
                        });

                        html += '</div>';
                    });

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
        // Prevent click if clicking on the View button
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

    // Real-time polling for visible audit timelines
    setInterval(function() {
        $('.audit-row:visible').each(function() {
            var userId = $(this).attr('id').replace('audit-', '');
            fetchAuditTimeline(userId);
        });
    }, 30000); // Update every 30 seconds
});