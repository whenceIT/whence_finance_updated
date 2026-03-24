@extends('layouts.learning')

@section('title', 'General Topics - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
    ['label' => 'General Topics', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>General Topics</h1>
            <p>Manage general topics and their configurations</p>
        </div>
         <a href="{{ route('learning.settings.general-topics.create') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; background: var(--primary-color); color: white;">
            <i class="fa fa-plus"></i> Add Topic
        </a>
    </div>
</div>

<!-- General Topics Table -->
<div class="box box-primary" style="background: white; border-radius: 10px; box-shadow: var(--shadow); overflow: hidden;">
    <div class="box-header" style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 class="box-title" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">
            <i class="fa fa-folder" style="color: var(--primary-color); margin-right: 10px;"></i>
            All Topics
        </h3>
    </div>
    <div class="box-body" style="padding: 20px;">
        @if(count($topics) > 0)
        <div style="overflow-x: auto;">
            <table class="table table-striped" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--light-bg);">
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Poster</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Description</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Uploads</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Upload Views</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topics as $topic)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 15px;">
                            @if($topic->poster)
                            <img src="{{ $topic->poster }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;" alt="{{ $topic->name }}">
                            @else
                            <i class="fa fa-image" style="font-size: 24px; color: var(--text-secondary);"></i>
                            @endif
                        </td>
                        <td style="padding: 15px;">
                            <strong style="font-size: 15px; color: var(--text-primary);">{{ $topic->name }}</strong>
                        </td>
                        <td style="padding: 15px; color: var(--text-secondary); font-size: 14px;">
                            {{ !empty($topic->description) ? \Illuminate\Support\Str::limit($topic->description, 100) : '-' }}
                        </td>
                        <td style="padding: 15px; text-align: center; color: var(--text-secondary); font-size: 14px;">
                            {{ $topic->uploads->count() }}
                        </td>
                        <td style="padding: 15px; text-align: center; color: var(--text-secondary); font-size: 14px;">
                            @php
                                $totalUploadViews = $topic->uploads ? $topic->uploads->sum('views_count') : 0;
                            @endphp
                            <span style="font-weight: 600; color: var(--primary-color);">{{ number_format($totalUploadViews) }}</span>
                        </td>
                        <td style="padding: 15px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <button type="button" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="View Details" onclick="viewTopicDetails({{ $topic->id }})">
                                    <i class="fa fa-eye" style="color: var(--primary-color);"></i>
                                </button>
                                <a href="{{ route('learning.settings.general-topics.edit', $topic->id) }}" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="Edit">
                                    <i class="fa fa-edit" style="color: var(--primary-color);"></i>
                                </a>
                                <form action="{{ route('learning.settings.general-topics.destroy', $topic->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="Delete" onclick="return confirm('Are you sure you want to delete this topic? This action cannot be undone.')">
                                        <i class="fa fa-trash" style="color: var(--accent-color);"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div style="text-align: center; padding: 40px 20px;">
            <i class="fa fa-folder-open" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px;">No Topics Found</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
                Start by creating your first general topic.
            </p>
            <a href="{{ route('learning.settings.general-topics.create') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; background: var(--primary-color); color: white;">
                <i class="fa fa-plus"></i> Add Topic
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Topic Details Modal -->
<div id="topicDetailsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content" style="border-radius: 8px;">
            <div class="modal-header" style="background: var(--primary-color); color: white; border-radius: 8px 8px 0 0; padding: 20px;">
                <h4 class="modal-title" id="modalTitle">Topic Details</h4>
                <button type="button" class="close" data-dismiss="modal" style="color: white; opacity: 1;">&times;</button>
            </div>
            <div class="modal-body" id="modalBody" style="padding: 20px;">
                <!-- Loading indicator -->
                <div id="loadingIndicator" style="text-align: center; padding: 40px;">
                    <i class="fa fa-spinner fa-spin" style="font-size: 36px; color: var(--primary-color);"></i>
                    <p style="margin-top: 20px; color: var(--text-secondary);">Loading topic details...</p>
                </div>
                <!-- Topic details content -->
                <div id="topicDetailsContent" style="display: none;">
                    <!-- Poster -->
                    <div id="topicPoster" style="margin-bottom: 20px; text-align: center;">
                        <!-- Poster image will be loaded here -->
                    </div>
                    <!-- Name -->
                    <h3 id="topicName" style="margin-bottom: 10px; color: var(--text-primary);"></h3>
                    <!-- Description -->
                    <p id="topicDescription" style="margin-bottom: 20px; color: var(--text-secondary);"></p>
                    <!-- Uploads -->
                    <div style="margin-bottom: 20px;">
                        <h4 style="margin-bottom: 10px; color: var(--text-primary);">Uploads</h4>
                        <div id="topicUploads" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px;">
                            <!-- Uploads will be listed here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 15px;">
                <button type="button" class="btn btn-default" data-dismiss="modal" style="padding: 8px 16px; border-radius: 6px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Toastr Notifications -->
@if(Session::has('toastr_type'))
<script>
    toastr.{{ Session::get('toastr_type') }}('{{ Session::get('toastr_message') }}', '{{ Session::get('toastr_title', 'Notification') }}');
</script>
@endif

<style>
.form-control:focus {
    border-color: var(--primary-color) !important;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1) !important;
}

.btn-primary {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.btn-primary:hover, .btn-primary:focus {
    background: #357abd !important;
    border-color: #357abd !important;
}

.modal-content {
    box-shadow: var(--shadow-hover) !important;
}
</style>
@endsection

@section('footer-scripts')
<script>
// Handle active state for settings page
$(document).ready(function() {
    var currentPath = window.location.pathname;
    var currentUrl = window.location.href;
    
    // Handle sidebar menu active state
    $('.sidebar-menu a').each(function() {
        var $link = $(this);
        var linkHref = $link.attr('href');
        
        if (currentPath === linkHref || currentUrl.includes(linkHref)) {
            $link.addClass('active');
        }
    });

    // Handle nav links active state
    $('.learning-nav a').each(function() {
        var $link = $(this);
        var linkHref = $link.attr('href');
        
        if (currentPath === linkHref || currentUrl.includes(linkHref)) {
            $link.addClass('active');
        }
    });

    // Handle user dropdown active state
    if (currentPath.includes('/settings')) {
        $('.user-dropdown-item[href*="settings"]').addClass('active');
    }
});

// View topic details
function viewTopicDetails(topicId) {
    // Get topic data
    var topic = @json($topics).find(function(t) { return t.id === topicId; });
    
    if (topic) {
        // Show loading indicator
        $('#loadingIndicator').show();
        $('#topicDetailsContent').hide();
        
        // Display modal
        $('#topicDetailsModal').modal('show');
        
        // Simulate loading time for better user experience
        setTimeout(function() {
            // Hide loading indicator
            $('#loadingIndicator').hide();
            $('#topicDetailsContent').show();
            
            // Set topic details
            $('#modalTitle').text(topic.name);
            $('#topicName').text(topic.name);
            $('#topicDescription').text(topic.description || 'No description available');
            
            // Show poster
            var posterDiv = $('#topicPoster');
            if (topic.poster) {
                posterDiv.html('<img src="' + topic.poster + '" style="width: 100%; max-height: 300px; object-fit: cover; border-radius: 4px;">');
            } else {
                posterDiv.html('<i class="fa fa-image" style="font-size: 64px; color: var(--text-secondary);"></i>');
            }
            
            // Show uploads
            var uploadsDiv = $('#topicUploads');
            if (topic.uploads.length > 0) {
                var uploadsHtml = '';
                topic.uploads.forEach(function(upload) {
                    var icon = getUploadIcon(upload.type);
                    uploadsHtml += '<div style="background: white; border: 1px solid var(--border-color); border-radius: 6px; padding: 10px; display: flex; align-items: center; gap: 10px;">';
                    uploadsHtml += '<i class="fa ' + icon + '" style="font-size: 24px; color: var(--text-secondary);"></i>';
                    uploadsHtml += '<div>';
                    uploadsHtml += '<p style="margin: 0; font-weight: 600; color: var(--text-primary);">' + upload.name + '</p>';
                    uploadsHtml += '<p style="margin: 0; font-size: 12px; color: var(--text-secondary);">' + formatFileSize(upload.file_size) + ' • ' + upload.type + '</p>';
                    uploadsHtml += '</div>';
                    uploadsHtml += '</div>';
                });
                uploadsDiv.html(uploadsHtml);
            } else {
                uploadsDiv.html('<p style="color: var(--text-secondary); grid-column: 1 / -1;">No uploads in this topic</p>');
            }
        }, 500);
    }
}

// Get upload icon based on type
function getUploadIcon(type) {
    var icons = {
        'video': 'fa-video-camera',
        'audio': 'fa-headphones',
        'book': 'fa-book',
        'paper': 'fa-file-text',
        'document': 'fa-file-word-o',
        'image': 'fa-image',
        'other': 'fa-file'
    };
    
    return icons[type] || icons['other'];
}

// Format file size
function formatFileSize(bytes) {
    if (!bytes) return '0 B';
    
    var units = ['B', 'KB', 'MB', 'GB', 'TB'];
    var size = bytes;
    var unitIndex = 0;
    
    while (size >= 1024 && unitIndex < units.length - 1) {
        size /= 1024;
        unitIndex++;
    }
    
    return Math.round(size * 100) / 100 + ' ' + units[unitIndex];
}
</script>
@endsection
