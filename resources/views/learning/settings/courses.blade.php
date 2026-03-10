@extends('layouts.learning')

@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <h1>
                Manage Courses
                <small>All courses in the system</small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{ route('learning.dashboard') }}"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="{{ route('learning.settings') }}">Settings</a></li>
                <li class="active">Manage Courses</li>
            </ol>
        </section>

        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">All Courses</h3>
                        </div>

                        <div class="box-body">
                            <table id="coursesTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Type</th>
                                        <th>Category</th>
                                        <th>Created By</th>
                                        <th>Enrolled Users</th>
                                        <th>Topics</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $course)
                                        <tr>
                                            <td>{{ $course->title }}</td>
                                            <td>{{ ucfirst($course->material_type) }}</td>
                                            <td>{{ $course->category_name }}</td>
                                            <td>
                                                @php
                                                    $creator = App\Models\User::find($course->created_by);
                                                @endphp
                                                {{ $creator ? $creator->first_name . ' ' . $creator->last_name : 'Unknown' }}
                                            </td>
                                            <td>{{ $course->enrollments()->count() }}</td>
                                            <td>{{ $course->topics()->count() }}</td>
                                            <td>
                                                <span class="label label-{{ $course->is_active ? 'success' : 'danger' }}">
                                                    {{ $course->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-info btn-sm" onclick="viewCourseDetails({{ $course->id }})">
                                                    <i class="fa fa-eye"></i> View Details
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Course Details Modal -->
    <div class="modal fade" id="courseDetailsModal" tabindex="-1" role="dialog" aria-labelledby="courseDetailsModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #bdf5f5 0%, #eafffe 100%); color: white; border-bottom: none;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: white; opacity: 0.8;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="courseDetailsModalLabel" style="color:#000; font-weight: 600; font-size: 20px;">
                        <i class="fa fa-info-circle"></i> Course Details
                    </h4>
                </div>
                <div class="modal-body" id="courseDetailsBody">
                    <!-- Loading spinner -->
                    <div class="text-center" style="padding: 60px 20px;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); margin-bottom: 20px;">
                            <i class="fa fa-spinner fa-spin fa-3x" style="color: white;"></i>
                        </div>
                        <p style="font-size: 16px; color: #6c757d; font-weight: 500;">Loading course details...</p>
                    </div>
                </div>
                <div class="modal-footer" style="border-top: none; padding-top: 0;">
                    <button type="button" class="btn btn-default" data-dismiss="modal" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; border-radius: 8px; padding: 10px 20px; font-weight: 500;">
                        <i class="fa fa-times"></i> Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-scripts')
    <script>
        // Global function to view course details
        function viewCourseDetails(courseId) {
            console.log('Viewing course details for ID:', courseId);
            
            // Show modal
            $('#courseDetailsModal').modal('show');

            // Fetch course details
            var url = '{{ url('learning/settings/courses') }}/' + courseId + '/details';
            
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    console.log('AJAX Success:', response);
                    var course = response.course;
                    var creator = response.creator;
                    var enrolledUsers = response.enrolledUsers;

                    // Build course details HTML
                    var html = '<div class="course-details-container">' +
                        '<div class="course-header">' +
                            '<div class="course-title-section">' +
                                '<h2 class="course-main-title">' + course.title + '</h2>' +
                                '<span class="course-badge badge-' + (course.is_active ? 'success' : 'danger') + '">' +
                                    (course.is_active ? 'Active' : 'Inactive') +
                                '</span>' +
                            '</div>' +
                            '<p class="course-description">' + course.description + '</p>' +
                        '</div>' +
                        
                        '<div class="course-meta-grid">' +
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-file-text"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Type</span>' +
                                    '<span class="meta-value">' + capitalizeFirst(course.material_type) + '</span>' +
                                '</div>' +
                            '</div>' +
                            
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-folder"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Category</span>' +
                                    '<span class="meta-value">' + (course.category_name || 'N/A') + '</span>' +
                                '</div>' +
                            '</div>' +
                            
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-user"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Created By</span>' +
                                    '<span class="meta-value">' + (creator ? creator.first_name + ' ' + creator.last_name : 'Unknown') + '</span>' +
                                '</div>' +
                            '</div>' +
                            
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-calendar"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Created At</span>' +
                                    '<span class="meta-value">' + formatDate(course.created_at) + '</span>' +
                                '</div>' +
                            '</div>' +
                            
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-users"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Enrolled Users</span>' +
                                    '<span class="meta-value">' + enrolledUsers.length + '</span>' +
                                '</div>' +
                            '</div>' +
                            
                            '<div class="meta-card">' +
                                '<div class="meta-icon"><i class="fa fa-list"></i></div>' +
                                '<div class="meta-content">' +
                                    '<span class="meta-label">Topics</span>' +
                                    '<span class="meta-value">' + course.all_topics.length + '</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                    
                    if (course.all_topics.length > 0) {
                        html += '<div class="topics-section">' +
                            '<div class="section-header">' +
                                '<h3><i class="fa fa-book"></i> Course Topics</h3>' +
                            '</div>' +
                            '<div class="topics-grid">';
                        
                        $.each(course.all_topics, function(index, topic) {
                            // Check available resources
                            var resources = [];
                            if (topic.pdf_file_path) resources.push('PDF');
                            if (topic.video_file_path) resources.push('Video');
                            if (topic.audio_file_path) resources.push('Audio');
                            if (topic.ppt_file_path) resources.push('PPT');
                            if (topic.document_file_path) resources.push('Document');
                            
                            html += '<div class="topic-card">' +
                                '<div class="topic-header">' +
                                    '<i class="fa fa-tag topic-icon"></i>' +
                                    '<h4 class="topic-name">' + topic.topic_name + '</h4>' +
                                    '<span class="topic-status badge-' + (topic.is_active ? 'success' : 'danger') + '">' +
                                        (topic.is_active ? 'Active' : 'Inactive') +
                                    '</span>' +
                                '</div>' +
                                '<div class="topic-content">';
                            
                            // Display resources
                            if (resources.length > 0) {
                                html += '<div class="topic-resources">' +
                                    '<div class="resources-label">Resources:</div>' +
                                    '<div class="resources-list">';
                                $.each(resources, function(resIndex, resource) {
                                    var resourceIcon = '';
                                    switch(resource) {
                                        case 'PDF': resourceIcon = 'fa-file-pdf-o'; break;
                                        case 'Video': resourceIcon = 'fa-video-camera'; break;
                                        case 'Audio': resourceIcon = 'fa-music'; break;
                                        case 'PPT': resourceIcon = 'fa-file-powerpoint-o'; break;
                                        case 'Document': resourceIcon = 'fa-file-word-o'; break;
                                    }
                                    html += '<span class="resource-tag">' +
                                        '<i class="fa fa-' + resourceIcon + '"></i> ' + resource +
                                    '</span>';
                                });
                                html += '</div></div>';
                            }
                            
                            // Display quiz information
                            if (topic.quiz && topic.quiz.title) {
                                html += '<div class="topic-quiz">' +
                                    '<div class="quiz-label">Quiz:</div>' +
                                    '<div class="quiz-info">' +
                                        '<i class="fa fa-question-circle"></i> ' +
                                        topic.quiz.title +
                                    '</div>' +
                                '</div>';
                            }
                            
                            // Display duration
                            if (topic.duration) {
                                var durationText = formatDuration(topic.duration);
                                html += '<div class="topic-duration">' +
                                    '<div class="duration-label">Duration:</div>' +
                                    '<div class="duration-info">' +
                                        '<i class="fa fa-clock-o"></i> ' + durationText +
                                    '</div>' +
                                '</div>';
                            }
                            
                            html += '</div></div>';
                        });
                        
                        html += '</div></div>';
                    } else {
                        html += '<div class="empty-state">' +
                            '<i class="fa fa-book"></i>' +
                            '<p>No topics available for this course</p>' +
                        '</div>';
                    }
                    
                    if (enrolledUsers.length > 0) {
                        html += '<div class="enrolled-users-section">' +
                            '<div class="section-header">' +
                                '<h3><i class="fa fa-users"></i> Enrolled Users (' + enrolledUsers.length + ')</h3>' +
                            '</div>' +
                            '<div class="users-grid">';
                        
                        $.each(enrolledUsers, function(index, user) {
                            html += '<div class="user-card">' +
                                '<div class="user-avatar">' +
                                    '<i class="fa fa-user-circle"></i>' +
                                '</div>' +
                                '<div class="user-info">' +
                                    '<h4 class="user-name">' + user.first_name + ' ' + user.last_name + '</h4>' +
                                    '<p class="user-email">' + user.email + '</p>' +
                                    (user.designation ? '<p class="user-designation">' + user.designation + '</p>' : '') +
                                '</div>' +
                            '</div>';
                        });
                        
                        html += '</div></div>';
                    } else {
                        html += '<div class="empty-state">' +
                            '<i class="fa fa-users"></i>' +
                            '<p>No users are currently enrolled in this course</p>' +
                        '</div>';
                    }
                    
                    html += '</div>';

                    $('#courseDetailsBody').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', status, error);
                    console.error('Response:', xhr.responseText);
                    $('#courseDetailsBody').html(`
                        <div style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 20px; border-radius: 12px; text-align: center;">
                            <i class="fa fa-exclamation-triangle" style="font-size: 36px; margin-bottom: 12px; color: #dc3545;"></i>
                            <h4 style="font-weight: 600; margin: 0 0 8px 0;">Error!</h4>
                            <p style="margin: 0; font-size: 14px;">Failed to load course details. Please try again.</p>
                        </div>
                    `);
                }
            });
        }

        $(document).ready(function() {
            // Initialize DataTable
            $('#coursesTable').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            // Reset modal body when closing
            $('#courseDetailsModal').on('hidden.bs.modal', function() {
                $('#courseDetailsBody').html(`
                    <div class="text-center" style="padding: 60px 20px;">
                        <div style="display: inline-flex; align-items: center; justify-content: center; width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #ffffff 0%, #4ba2a2 100%); margin-bottom: 20px;">
                            <i class="fa fa-spinner fa-spin fa-3x" style="color: black;"></i>
                        </div>
                        <p style="font-size: 16px; color: #6c757d; font-weight: 500;">Loading course details...</p>
                    </div>
                `);
            });
        });

        // Helper functions (global scope)
        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        function formatDate(dateString) {
            var date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }

        function formatDuration(seconds) {
            if (!seconds) return 'N/A';
            
            var hours = Math.floor(seconds / 3600);
            var minutes = Math.floor((seconds % 3600) / 60);
            var remainingSeconds = seconds % 60;
            
            if (hours > 0) {
                return sprintf('%d:%02d:%02d', hours, minutes, remainingSeconds);
            }
            
            return sprintf('%d:%02d', minutes, remainingSeconds);
        }

        function sprintf(format) {
            var args = Array.prototype.slice.call(arguments, 1);
            var i = 0;
            return format.replace(/%(\w+)/g, function(match, type) {
                switch(type) {
                    case 'd': return args[i++];
                    case '02d': 
                        var num = args[i++];
                        return num.toString().padStart(2, '0');
                    default: return match;
                }
            });
        }

        // Custom styles for the modal
        const styles = `
            <style>
                .course-details-container {
                    font-family: 'Roboto', sans-serif;
                    color: #2c3e50;
                    line-height: 1.6;
                }

                .course-header {
                    background: linear-gradient(135deg, #ffffff 0%, #d6eae8 100%);
                    color: white;
                    padding: 30px;
                    border-radius: 12px;
                    margin-bottom: 30px;
                    box-shadow: 0 8px 32px rgba(102, 126, 234, 0.3);
                }

                .course-title-section {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    margin-bottom: 15px;
                    flex-wrap: wrap;
                    gap: 15px;
                }

                .course-main-title {
                    font-size: 28px;
                    font-weight: 700;
                    color: #2c3e50;
                    margin: 0;
                    text-shadow: 0 1px 2px rgba(20, 16, 16, 0.63);
                }

                .course-badge {
                    padding: 8px 16px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 14px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                }

                .course-badge.badge-success {
                    background: rgba(40, 167, 69, 0.9);
                }

                .course-badge.badge-danger {
                    background: rgba(220, 53, 69, 0.9);
                }

                .course-description {
                    font-size: 16px;
                    opacity: 0.95;
                    margin: 0;
                    line-height: 1.8;
                }

                .course-meta-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                    gap: 20px;
                    margin-bottom: 30px;
                }

                .meta-card {
                    background: white;
                    padding: 10px;
                    border-radius: 12px;
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
                    transition: all 0.3s ease;
                    border: 1px solid rgba(0,0,0,0.05);
                }

                .meta-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                }

                .meta-icon {
                    width: 50px;
                    height: 50px;
                    border-radius: 12px;
                    background: linear-gradient(135deg, #a2afec 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 20px;
                    color: white;
                    flex-shrink: 0;
                }

                .meta-content {
                    flex: 1;
                }

                .meta-label {
                    display: block;
                    font-size: 9px;
                    font-weight: 600;
                    color: #7f8c8d;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 4px;
                }

                .meta-value {
                    display: block;
                    font-size: 12px;
                    font-weight: 600;
                    color: #2c3e50;
                }

                .section-header {
                    margin-bottom: 20px;
                }

                .section-header h3 {
                    font-size: 22px;
                    font-weight: 700;
                    color: #2c3e50;
                    margin: 0;
                    display: flex;
                    align-items: center;
                    gap: 10px;
                }

                .section-header h3 i {
                    color: #667eea;
                }

                .topics-section, .enrolled-users-section {
                    margin-bottom: 30px;
                }

                .topics-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
                    gap: 16px;
                }

                .topic-card {
                    background: white;
                    padding: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
                    transition: all 0.3s ease;
                    border: 1px solid rgba(0,0,0,0.05);
                }

                .topic-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                }

                .topic-header {
                    display: flex;
                    align-items: center;
                    gap: 12px;
                    margin-bottom: 16px;
                }

                .topic-icon {
                    font-size: 19px;
                    color: #667eea;
                    width: 40px;
                    height: 40px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: rgba(102, 126, 234, 0.1);
                    border-radius: 8px;
                }

                .topic-name {
                    flex: 1;
                    font-size: 16px;
                    font-weight: 600;
                    color: #141c25;
                    margin: 0;
                }

                .topic-status {
                    padding: 4px 12px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: 600;
                    text-transform: uppercase;
                }

                .topic-status.badge-success {
                    background: #d4edda;
                    color: #155724;
                }

                .topic-status.badge-danger {
                    background: #f8d7da;
                    color: #721c24;
                }

                .topic-content {
                    padding-top: 16px;
                    border-top: 1px solid rgba(0,0,0,0.05);
                }

                .topic-resources, .topic-quiz, .topic-duration {
                    margin-bottom: 12px;
                }

                .resources-label, .quiz-label, .duration-label {
                    font-size: 11px;
                    font-weight: 600;
                    color: #7f8c8d;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    margin-bottom: 8px;
                    display: block;
                }

                .resources-list {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 8px;
                }

                .resource-tag {
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    padding: 4px 10px;
                    background: rgba(102, 126, 234, 0.1);
                    border: 1px solid rgba(102, 126, 234, 0.2);
                    border-radius: 6px;
                    font-size: 12px;
                    font-weight: 500;
                    color: #667eea;
                    transition: all 0.2s ease;
                }

                .resource-tag:hover {
                    background: rgba(102, 126, 234, 0.15);
                    border-color: rgba(102, 126, 234, 0.3);
                }

                .resource-tag i {
                    font-size: 14px;
                }

                .quiz-info, .duration-info {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 13px;
                    color: #2c3e50;
                }

                .quiz-info i, .duration-info i {
                    color: #667eea;
                    font-size: 14px;
                }

                .users-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                    gap: 16px;
                }

                .user-card {
                    background: white;
                    padding: 20px;
                    border-radius: 12px;
                    box-shadow: 0 4px 16px rgba(0,0,0,0.08);
                    transition: all 0.3s ease;
                    border: 1px solid rgba(0,0,0,0.05);
                    display: flex;
                    align-items: center;
                    gap: 15px;
                }

                .user-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 24px rgba(0,0,0,0.12);
                }

                .user-avatar {
                    width: 50px;
                    height: 50px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 24px;
                    color: white;
                    flex-shrink: 0;
                }

                .user-info {
                    flex: 1;
                }

                .user-name {
                    font-size: 16px;
                    font-weight: 600;
                    color: #2c3e50;
                    margin: 0 0 4px 0;
                }

                .user-email {
                    font-size: 13px;
                    color: #7f8c8d;
                    margin: 0 0 4px 0;
                }

                .user-designation {
                    font-size: 12px;
                    color: #667eea;
                    margin: 0;
                }

                .empty-state {
                    text-align: center;
                    padding: 60px 20px;
                    background: white;
                    border-radius: 12px;
                    border: 2px dashed #dee2e6;
                }

                .empty-state i {
                    font-size: 48px;
                    color: #adb5bd;
                    margin-bottom: 16px;
                }

                .empty-state p {
                    font-size: 16px;
                    color: #6c757d;
                    margin: 0;
                    font-weight: 500;
                }

                @media (max-width: 768px) {
                    .course-main-title {
                        font-size: 22px;
                    }

                    .course-meta-grid {
                        grid-template-columns: 1fr;
                    }

                    .topics-grid {
                        grid-template-columns: 1fr;
                    }

                    .users-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        `;

        // Add styles to the document
        if (!document.querySelector('#course-details-styles')) {
            const styleElement = document.createElement('style');
            styleElement.id = 'course-details-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
    </script>
@endsection
