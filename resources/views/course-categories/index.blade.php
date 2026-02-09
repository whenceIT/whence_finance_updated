@extends('layouts.learning')
@section('title', 'Course Categories - Whence Learn')
@section('content')
    @php
    $user = Sentinel::getUser();
    $role = $user ? $user->roles->first() : null;
    $isAdmin = $role && $role->id == 1;
    @endphp

<div class="content-wrapper">
    <div class="page-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
            <div>
                <h1 style="font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px;">Course Categories</h1>
                <p style="color: var(--text-secondary); font-size: 14px; margin: 0;">Browse and manage your course categories</p>
            </div>
            @if($isAdmin)
            <a href="{{ url('course-categories/create') }}" class="btn btn-primary btn-sm" style="padding: 8px 16px; border-radius: 4px;">
                <i class="fa fa-plus"></i> Add Category
            </a>
            @endif
        </div>
    </div>

    <!-- Categories Grid -->
    @if(count($categories) > 0)
    <div class="categories-grid" style="display: flex; flex-wrap: wrap; gap: 16px; margin-top: 20px;">
        @foreach($categories as $category)
        <a href="{{ url('learning/courses?category=' . urlencode($category->name)) }}" class="category-card-link" style="text-decoration: none; color: inherit; display: block;">
        <div class="category-card" style="background: white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); overflow: hidden; width: 280px; flex-shrink: 0; transition: transform 0.2s, box-shadow 0.2s; cursor: pointer;">
            <div style="padding: 12px 15px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid #eee;">
                <span style="display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 6px; background: {{ $category->color ? $category->color . '20' : '#4a90e220' }};">
                    <i class="fa {{ $category->icon }}" style="font-size: 16px; color: {{ $category->color ?: 'var(--primary-color)' }};"></i>
                </span>
                <div style="flex: 1; min-width: 0;">
                    <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0 0 4px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $category->name }}</h4>
                    @if($category->is_active)
                    <span style="display: inline-block; background: rgba(80, 200, 120, 0.15); color: #2e7d32; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">
                        Active
                    </span>
                    @else
                    <span style="display: inline-block; background: rgba(255, 107, 107, 0.15); color: #c62828; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">
                        Inactive
                    </span>
                    @endif
                </div>
            </div>
            <div style="padding: 12px 15px;">
                <p style="color: var(--text-secondary); font-size: 12px; margin: 0 0 12px 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                    {{ !empty($category->description) ? $category->description : 'No description available' }}
                </p>
                <span style="color: var(--primary-color); font-size: 12px; font-weight: 500;">
                    <i class="fa fa-arrow-right"></i> View Courses
                </span>
                @if($isAdmin)
                <div style="display: flex; gap: 8px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #eee;" onclick="event.preventDefault(); event.stopPropagation();">
                    <a href="{{ url('course-categories/' . $category->id . '/edit') }}" class="btn btn-default btn-xs" style="flex: 1; padding: 5px 10px; font-size: 11px; border: 1px solid #ddd; border-radius: 4px; text-align: center;" onclick="event.stopPropagation();">
                        <i class="fa fa-edit" style="color: var(--primary-color);"></i> Edit
                    </a>
                    <form action="{{ url('course-categories/' . $category->id . '/toggle-status') }}" method="POST" style="flex: 1;">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-default btn-xs" style="width: 100%; padding: 5px 10px; font-size: 11px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="event.stopPropagation(); return confirm('Are you sure?')">
                            @if($category->is_active)
                            <i class="fa fa-eye-slash" style="color: #f44336;"></i> Hide
                            @else
                            <i class="fa fa-eye" style="color: #4caf50;"></i> Show
                            @endif
                        </button>
                    </form>
                    <form action="{{ url('course-categories/' . $category->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-default btn-xs" style="padding: 5px 10px; font-size: 11px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer;" onclick="event.stopPropagation(); return confirm('Are you sure?')">
                            <i class="fa fa-trash" style="color: #f44336;"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        </a>
        @endforeach
    </div>
    @else
    <div style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
        <i class="fa fa-folder-open" style="font-size: 48px; margin-bottom: 15px; opacity: 0.5;"></i>
        <h3 style="font-size: 18px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px;">No Categories Found</h3>
        <p style="margin-bottom: 20px;">Start by creating your first course category.</p>
        @if($isAdmin)
        <a href="{{ url('course-categories/create') }}" class="btn btn-primary btn-sm" style="padding: 8px 16px; border-radius: 4px;">
            <i class="fa fa-plus"></i> Add Category
        </a>
        @endif
    </div>
    @endif
</div>

<!-- Toastr Notifications -->
@if(Session::has('toastr_type'))
<script>
    toastr.{{ Session::get('toastr_type') }}('{{ Session::get('toastr_message') }}', '{{ Session::get('toastr_title', 'Notification') }}');
</script>
@endif

<style>
.content-wrapper {
    width: 100%;
}
.category-card-link {
    display: inline-block;
}
.category-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.btn-primary {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}
.btn-default {
    background: #f8f9fa;
    border-color: #ddd;
    color: #333;
}
.btn-default:hover {
    background: #e9ecef;
}
</style>

<script>
$(document).ready(function() {
    var currentPath = window.location.pathname;
    $('.sidebar-menu a').each(function() {
        var $link = $(this);
        if (currentPath === $link.attr('href')) {
            $link.addClass('active');
        }
    });
});
</script>
@endsection
