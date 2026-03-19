@extends('layouts.learning')

@section('title', 'Edit General Topic - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
     ['label' => 'General Topics', 'url' => route('learning.settings.general-topics.index')],
    ['label' => 'Edit Topic', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Edit General Topic</h1>
    <p>Update general topic details</p>
</div>

<!-- Edit General Topic Form -->
<div class="box box-primary" style="background: white; border-radius: 10px; box-shadow: var(--shadow); overflow: hidden;">
    <div class="box-header" style="padding: 20px; border-bottom: 1px solid var(--border-color);">
        <h3 class="box-title" style="font-size: 18px; font-weight: 600; color: var(--text-primary);">
            <i class="fa fa-edit" style="color: var(--primary-color); margin-right: 10px;"></i>
            Topic Details
        </h3>
    </div>
    <div class="box-body" style="padding: 20px;">
         <form action="{{ route('learning.settings.general-topics.update', $topic->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Topic Name <span style="color: var(--accent-color);">*</span>
                </label>
                <input type="text" name="name" id="name" class="form-control" 
                       value="{{ old('name', $topic->name) }}" 
                       required 
                       style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; background: white; font-size: 14px;"
                       placeholder="Enter topic name">
                @error('name')
                <div style="color: var(--accent-color); font-size: 13px; margin-top: 5px;">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="description" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Description
                </label>
                <textarea name="description" id="description" class="form-control" 
                          rows="5" 
                          style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; background: white; font-size: 14px; resize: vertical;"
                          placeholder="Enter topic description">{{ old('description', $topic->description) }}</textarea>
                @error('description')
                <div style="color: var(--accent-color); font-size: 13px; margin-top: 5px;">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="poster" style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Poster Image (Optional)
                </label>
                <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 30px; text-align: center; background: var(--light-bg); cursor: pointer;" onclick="document.getElementById('posterInput').click()">
                    <i class="fa fa-image" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">
                        Upload a poster image for the topic
                    </p>
                    <input type="file" id="posterInput" name="poster" style="display: none;" accept="image/*" onchange="handlePosterSelect(this)">
                    <p style="color: var(--text-secondary); font-size: 12px; margin-top: 10px;">
                        Supported formats: JPG, PNG, GIF (Max: 2MB)
                    </p>
                </div>
                <div id="posterPreview" style="display: {{ $topic->poster ? 'block' : 'none' }}; margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img id="posterImage" src="{{ $topic->poster }}" style="width: 120px; height: 68px; object-fit: cover; border-radius: 6px;">
                        <div>
                            <div id="posterName" style="font-weight: 600; font-size: 13px;">{{ basename($topic->poster) ?? 'Poster' }}</div>
                            <button type="button" onclick="removePoster()" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;">Remove</button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px; margin-top: 24px;">
                <button type="submit" class="btn btn-primary" id="updateBtn" style="padding: 12px 24px; border-radius: 8px; border: none; background: var(--primary-color); color: white; font-weight: 600;">
                    <span id="updateBtnText"><i class="fa fa-save"></i> Update Topic</span>
                    <span id="updateBtnLoader" style="display: none;"><i class="fa fa-spinner fa-spin"></i> Updating...</span>
                </button>
                 <a href="{{ route('learning.settings.general-topics.index') }}" class="btn btn-default" style="padding: 12px 24px; border-radius: 8px; border: 1px solid var(--border-color); background: white; color: var(--text-primary); font-weight: 600;">
                    <i class="fa fa-times"></i> Cancel
                </a>
            </div>
        </form>
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

.btn-default:hover, .btn-default:focus {
    background: var(--light-bg) !important;
    border-color: var(--border-color) !important;
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
    
    // Handle form submission with loading state
    var form = document.querySelector('form');
    var updateBtn = document.getElementById('updateBtn');
    var updateBtnText = document.getElementById('updateBtnText');
    var updateBtnLoader = document.getElementById('updateBtnLoader');
    
    if (form && updateBtn) {
        form.addEventListener('submit', function() {
            updateBtn.disabled = true;
            updateBtnText.style.display = 'none';
            updateBtnLoader.style.display = 'inline';
        });
    }
});

// Poster upload functionality
function handlePosterSelect(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('posterImage').src = e.target.result;
            document.getElementById('posterName').textContent = input.files[0].name;
            document.getElementById('posterPreview').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove poster function
function removePoster() {
    document.getElementById('posterInput').value = '';
    document.getElementById('posterPreview').style.display = 'none';
    document.getElementById('posterImage').src = '';
    document.getElementById('posterName').textContent = '';
}
</script>
@endsection
