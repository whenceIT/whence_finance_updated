@extends('layouts.learning')

@section('title', 'Add Training Material - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => 'Add Course', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Add Training Material</h1>
    <p>Create a new course with multiple topics</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); max-width: 900px; margin: 0 auto;">
    
    <!-- Wizard Progress -->
    <div style="display: flex; justify-content: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 0;">
            <div id="step-indicator-1" class="step-indicator active" style="display: flex; flex-direction: column; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary-color); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">1</div>
                <span style="font-size: 12px; color: var(--primary-color); font-weight: 500;">Course Info</span>
            </div>
            <div style="width: 80px; height: 2px; background: var(--border-color);" id="step-line-1"></div>
            <div id="step-indicator-2" class="step-indicator" style="display: flex; flex-direction: column; align-items: center;">
                <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--border-color); color: var(--text-secondary); display: flex; align-items: center; justify-content: center; font-weight: 600; margin-bottom: 8px;">2</div>
                <span style="font-size: 12px; color: var(--text-secondary);">Topics</span>
            </div>
        </div>
    </div>

    <form action="{{ route('learning.training-materials.store-course-info') }}" method="POST" id="training-material-form" enctype="multipart/form-data">
        @csrf
        
        <!-- Step 1: Course Info -->
        <div id="step-1" class="wizard-step">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 24px; color: var(--text-primary);">Course Information</h3>
            
            <!-- Title -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Title <span style="color: var(--accent-color);">*</span>
                </label>
                <input type="text" name="title" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px;" placeholder="Enter course title">
            </div>
            
            <!-- Description -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Description
                </label>
                <textarea name="description" rows="3" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; resize: vertical;" placeholder="Enter course description (optional)"></textarea>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">

                <!-- Department -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                        Department <span style="color: var(--accent-color);">*</span>
                    </label>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        @php
                            $departments = ['Operations', 'Recoveries', 'Administration', 'Finance', 'IT', 'HR', 'Legal', 'Compliance', 'General'];
                        @endphp
                        @foreach($departments as $dept)
                            <label style="display: inline-flex; align-items: center; cursor: pointer; padding: 8px 16px; border: 2px solid var(--border-color); border-radius: 20px; background: white; color: var(--text-primary); font-size: 14px; transition: all 0.3s;">
                                <input type="radio" name="department" value="{{ $dept }}" required style="display: none;">
                                {{ $dept }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Categories -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Categories
                </label>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $category)
                            <label style="display: inline-flex; align-items: center; cursor: pointer; padding: 8px 16px; border: 2px solid var(--border-color); border-radius: 20px; background: white; color: var(--text-primary); font-size: 14px; transition: all 0.3s;">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" style="display: none;">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    @endif
                </div>
                <small style="color: var(--text-secondary); font-size: 12px;">Select one or more categories</small>
            </div>
            
            <!-- Target Role -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Target Audience <span style="color: var(--accent-color);">*</span>
                </label>
                <select name="target_role" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; background: white;">
                    <option value="all">All Staff</option>
                    <option value="1">Admin</option>
                    <option value="4">Manager</option>
                    <option value="6">Supervisor</option>
                    <option value="3">Intern</option>
                    <option value="5">Staff</option>
                    <option value="10">Client</option>
                </select>
            </div>
            
            <!-- Poster Upload (for videos) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Video Poster/Thumbnail (Optional)
                </label>
                <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 30px; text-align: center; background: var(--light-bg); cursor: pointer;" onclick="document.getElementById('posterInput').click()">
                    <i class="fa fa-image" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">
                        Upload a poster image for video playback
                    </p>
                    <input type="file" id="posterInput" name="poster" style="display: none;" accept="image/*">
                    <p style="color: var(--text-secondary); font-size: 12px; margin-top: 10px;">
                        Recommended: 1280x720 or 1920x1080 (JPG, PNG)
                    </p>
                </div>
                <div id="posterPreview" style="display: none; margin-top: 10px;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <img id="posterImage" src="" style="width: 120px; height: 68px; object-fit: cover; border-radius: 6px;">
                        <div>
                            <div id="posterName" style="font-weight: 600; font-size: 13px;"></div>
                            <button type="button" onclick="removePoster()" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;">Remove</button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Options -->
            <div style="display: flex; gap: 30px; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                        <input type="checkbox" name="is_active" checked style="margin-right: 8px;">
                        Active
                    </label>
                </div>
                <!-- <div style="flex: 1;">
                    <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                        <input type="checkbox" name="is_featured" style="margin-right: 8px;">
                        Featured
                    </label>
                </div> -->
            </div>
            
            <!-- Step 1 Navigation -->
            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ url('learning/training-materials') }}" style="padding: 12px 24px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
                <button type="submit" style="padding: 12px 30px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Next: Add Topics <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Loading Modal -->
<div id="loading-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
        <div id="loading-spinner" style="display: inline-block; width: 50px; height: 50px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
        <h3 id="loading-title" style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">System is processing files</h3>
        <p id="loading-message" style="font-size: 14px; color: var(--text-secondary);">Please wait while we create this course...</p>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
.step-indicator.active div {
    background: var(--primary-color) !important;
    color: white !important;
}
.step-indicator.active span {
    color: var(--primary-color) !important;
}
.step-indicator.active + div {
    background: var(--primary-color) !important;
}
input[type="radio"]:checked + label {
    border-color: var(--primary-color) !important;
    background: rgba(74, 144, 226, 0.1) !important;
}
/* Button Pill Styles */
label.pill-label {
    transition: all 0.3s ease;
}

label.pill-label:hover {
    border-color: var(--primary-color) !important;
    background: rgba(74, 144, 226, 0.1) !important;
    color: var(--primary-color) !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Department pill functionality
    const departmentInputs = document.querySelectorAll('input[name="department"]');
    
    departmentInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Reset all department pills
            document.querySelectorAll('input[name="department"]').forEach(radio => {
                const label = radio.parentElement;
                label.style.background = 'white';
                label.style.color = 'var(--text-primary)';
                label.style.borderColor = 'var(--border-color)';
            });
            
            // Highlight selected department pill
            if (this.checked) {
                const label = this.parentElement;
                label.style.background = 'var(--primary-color)';
                label.style.color = 'white';
                label.style.borderColor = 'var(--primary-color)';
            }
        });
    });
    
    // Category pill functionality
    const categoryInputs = document.querySelectorAll('input[name="category_ids[]"]');
    
    categoryInputs.forEach(input => {
        input.addEventListener('change', function() {
            const label = this.parentElement;
            
            if (this.checked) {
                label.style.background = 'var(--primary-color)';
                label.style.color = 'white';
                label.style.borderColor = 'var(--primary-color)';
            } else {
                label.style.background = 'white';
                label.style.color = 'var(--text-primary)';
                label.style.borderColor = 'var(--border-color)';
            }
        });
    });
    
    // Poster upload functionality
    const posterInput = document.getElementById('posterInput');
    const posterPreview = document.getElementById('posterPreview');
    const posterImage = document.getElementById('posterImage');
    const posterName = document.getElementById('posterName');
    
    if (posterInput) {
        posterInput.addEventListener('change', function(e) {
            if (e.target.files && e.target.files[0]) {
                const file = e.target.files[0];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    posterImage.src = e.target.result;
                    posterName.textContent = file.name;
                    posterPreview.style.display = 'block';
                };
                
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Form submission with loading modal and optimizations
    const form = document.getElementById('training-material-form');
    const modal = document.getElementById('loading-modal');
    const title = document.getElementById('loading-title');
    const message = document.getElementById('loading-message');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Optimize form submission
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state on button
            submitButton.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            submitButton.disabled = true;
            submitButton.style.opacity = '0.7';
            
            // Show loading modal
            modal.style.display = 'flex';
            title.textContent = 'Creating Course';
            message.textContent = 'This will only take a moment...';
            
            // Prevent double submission
            form.dataset.submitting = 'true';
        });
    }
});

// Remove poster function
function removePoster() {
    const posterInput = document.getElementById('posterInput');
    const posterPreview = document.getElementById('posterPreview');
    const posterImage = document.getElementById('posterImage');
    const posterName = document.getElementById('posterName');
    
    posterInput.value = '';
    posterPreview.style.display = 'none';
    posterImage.src = '';
    posterName.textContent = '';
}
</script>
@endsection
