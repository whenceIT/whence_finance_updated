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
                        <td style="padding: 15px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
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
</script>
@endsection
