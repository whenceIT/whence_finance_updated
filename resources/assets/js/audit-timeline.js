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
                    html += '<style>';
                    html += '.timeline { position: relative; padding-left: 40px; }';
                    html += '.timeline::before { content: ""; position: absolute; left: 15px; top: 0; bottom: 0; width: 2px; background: linear-gradient(to bottom, #007bff, #0056b3); }';
                    html += '.timeline-item { position: relative; margin-bottom: 25px; display: flex; align-items: flex-start; }';
                    html += '.timeline-marker { position: absolute; left: -28px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; background: #007bff; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 6px rgba(0,123,255,0.4); }';
                    html += '.timeline-marker::after { content: "\\f00c"; font-family: "Font Awesome 5 Free"; font-weight: 900; position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); font-size: 8px; color: #fff; }';
                    html += '.timeline-content { flex: 1; background: #fff; padding: 15px 20px; border-radius: 10px; box-shadow: 0 2px 15px rgba(0,0,0,0.08); border: 1px solid #e9ecef; transition: all 0.3s ease; }';
                    html += '.timeline-content:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,123,255,0.15); }';
                    html += '.date-group { margin-bottom: 35px; }';
                    html += '.date-header { font-weight: 600; font-size: 14px; color: #495057; margin-bottom: 20px; padding: 8px 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: inline-block; color: #fff; text-transform: uppercase; letter-spacing: 1px; }';
                    html += '.timeline-event { font-size: 16px; font-weight: 600; color: #212529; margin-bottom: 8px; }';
                    html += '.timeline-meta { display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap; }';
                    html += '.timeline-time { color: #6c757d; font-size: 13px; display: flex; align-items: center; gap: 5px; }';
                    html += '.timeline-time.after-hours { color: #dc3545; font-weight: 600; }';
                    html += '.timeline-btn { display: inline-flex; align-items: center; gap: 5px; padding: 6px 14px; border-radius: 20px; font-size: 12px; transition: all 0.3s ease; }';
                    html += '</style>';

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
                            var timeClass = isAfterHours ? ' after-hours' : '';
                            var timeIcon = isAfterHours ? '<i class="fa fa-moon"></i>' : '<i class="fa fa-clock"></i>';

                            html += '<div class="timeline-event">' + audit.event + '</div>';
                            html += '<div class="timeline-meta">';
                            html += '<span class="timeline-time' + timeClass + '">' + timeIcon + ' ' + auditTime.toLocaleTimeString() + '</span>';
                            html += '<a href="{{ url("audits") }}/' + audit.id + '" class="btn btn-sm btn-info timeline-btn" target="_blank"><i class="fa fa-eye"></i> Details</a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
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