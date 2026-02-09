@extends('layouts.learning')

@section('title', 'Course Categories - Whence Learn')

@section('content')
<div class="page-header">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <h1>Course Categories</h1>
            <p>Manage your course categories and their configurations</p>
        </div>
        <a href="{{ url('course-categories/create') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; background: var(--primary-color); color: white;">
            <i class="fa fa-plus"></i> Add Category
        </a>
    </div>
</div>

<!-- Categories Table -->
<div class="box box-primary" style="background: white; border-radius: 10px; box-shadow: var(--shadow); overflow: hidden;">
    <div class="box-header" style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 class="box-title" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">
            <i class="fa fa-folder" style="color: var(--primary-color); margin-right: 10px;"></i>
            All Categories
        </h3>
    </div>
    <div class="box-body" style="padding: 20px;">
        @if(count($categories) > 0)
        <div style="overflow-x: auto;">
            <table class="table table-striped" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: var(--light-bg);">
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Icon</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Name</th>
                        <th style="padding: 15px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Description</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Order</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: var(--text-secondary); font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td style="padding: 15px;">
                            <span style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: rgba(74, 144, 226, 0.1); border-radius: 8px;">
                                <i class="fa {{ $category->icon }}" style="font-size: 20px; color: var(--primary-color);"></i>
                            </span>
                        </td>
                        <td style="padding: 15px;">
                            <strong style="font-size: 15px; color: var(--text-primary);">{{ $category->name }}</strong>
                        </td>
                        <td style="padding: 15px; color: var(--text-secondary); font-size: 14px;">
                            {{ !empty($category->description) ? \Illuminate\Support\Str::limit($category->description, 50) : '-' }}
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="background: var(--light-bg); padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600;">
                                {{ $category->sort_order ?? 0 }}
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            @if($category->is_active)
                            <span style="display: inline-block; background: rgba(80, 200, 120, 0.1); color: var(--secondary-color); padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                <i class="fa fa-check-circle" style="margin-right: 5px;"></i> Active
                            </span>
                            @else
                            <span style="display: inline-block; background: rgba(255, 107, 107, 0.1); color: var(--accent-color); padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600;">
                                <i class="fa fa-times-circle" style="margin-right: 5px;"></i> Inactive
                            </span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: right;">
                            <div style="display: flex; gap: 8px; justify-content: flex-end;">
                                <a href="{{ url('course-categories/' . $category->id . '/edit') }}" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="Edit">
                                    <i class="fa fa-edit" style="color: var(--primary-color);"></i>
                                </a>
                                <form action="{{ url('course-categories/' . $category->id . '/toggle-status') }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('POST')
                                    <button type="submit" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="Toggle Status" onclick="return confirm('Are you sure you want to toggle this category\'s status?')">
                                        @if($category->is_active)
                                        <i class="fa fa-eye-slash" style="color: var(--accent-color);"></i>
                                        @else
                                        <i class="fa fa-eye" style="color: var(--secondary-color);"></i>
                                        @endif
                                    </button>
                                </form>
                                <form action="{{ url('course-categories/' . $category->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-default" style="padding: 6px 12px; border-radius: 6px;" title="Delete" onclick="return confirm('Are you sure you want to delete this category? This action cannot be undone.')">
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
            <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px;">No Categories Found</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
                Start by creating your first course category.
            </p>
            <a href="{{ url('course-categories/create') }}" class="btn btn-primary" style="padding: 12px 24px; border-radius: 8px; border: none; background: var(--primary-color); color: white;">
                <i class="fa fa-plus"></i> Add Category
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
