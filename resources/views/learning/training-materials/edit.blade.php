@extends('layouts.learning')

@section('title', 'Edit Training Material - Whence Learn')

@section('content')
<!-- Back Button -->
<div style="margin-bottom: 20px;">
    <a href="{{ url('learning/training-materials') }}" style="display: inline-flex; align-items: center; padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600; transition: background 0.3s;">
        <i class="fa fa-arrow-left"></i> Back to Materials
    </a>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); max-width: 800px; margin: 0 auto;">
    <form action="{{ url('learning/training-materials/' . $material->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Title -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Title <span style="color: var(--accent-color);">*</span>
            </label>
            <input type="text" name="title" required value="{{ old('title', $material->title) }}" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px;">
        </div>
        
        <!-- Description -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Description
            </label>
            <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; resize: vertical;">{{ old('description', $material->description) }}</textarea>
        </div>
        
        <!-- File Upload
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Upload New File <span style="color: var(--accent-color);">*</span>
            </label>
            <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 30px; text-align: center; background: var(--light-bg);">
                <i class="fa fa-cloud-upload" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">
                    Current file: {{ $material->file_name }} ({{ $material->human_file_size }})
                </p>
                <input type="file" name="file" style="width: 100%;" accept=".pdf" required>
                <p style="color: var(--text-secondary); font-size: 12px; margin-top: 10px;">
                    Only PDF files are accepted. Leave blank to keep current file.
                </p>
            </div>
        </div> -->
        
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
                    <label style="display: inline-flex; align-items: center; cursor: pointer; padding: 8px 16px; border: 2px solid var(--border-color); border-radius: 20px; background: {{ $material->department == $dept ? 'var(--primary-color)' : 'white' }}; color: {{ $material->department == $dept ? 'white' : 'var(--text-primary)' }}; font-size: 14px; transition: all 0.3s;">
                        <input type="radio" name="department" value="{{ $dept }}" {{ $material->department == $dept ? 'checked' : '' }} required style="display: none;">
                        {{ $dept }}
                    </label>
                @endforeach
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
                        <label style="display: inline-flex; align-items: center; cursor: pointer; padding: 8px 16px; border: 2px solid var(--border-color); border-radius: 20px; background: {{ $material->categories->contains($category->id) ? 'var(--primary-color)' : 'white' }}; color: {{ $material->categories->contains($category->id) ? 'white' : 'var(--text-primary)' }}; font-size: 14px; transition: all 0.3s;">
                            <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" {{ $material->categories->contains($category->id) ? 'checked' : '' }} style="display: none;">
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
                <option value="all" {{ $material->target_role == 'all' ? 'selected' : '' }}>All Staff</option>
                <option value="1" {{ $material->target_role == '1' ? 'selected' : '' }}>Admin</option>
                <option value="4" {{ $material->target_role == '4' ? 'selected' : '' }}>Manager</option>
                <option value="6" {{ $material->target_role == '6' ? 'selected' : '' }}>Supervisor</option>
                <option value="3" {{ $material->target_role == '3' ? 'selected' : '' }}>Intern</option>
                <option value="5" {{ $material->target_role == '5' ? 'selected' : '' }}>Staff</option>
                <option value="10" {{ $material->target_role == '10' ? 'selected' : '' }}>Client</option>
            </select>
        </div>
        
        <!-- Options -->
        <div style="display: flex; gap: 30px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                    <input type="checkbox" name="is_active" {{ $material->is_active ? 'checked' : '' }} style="margin-right: 8px;">
                    Active
                </label>
            </div>
            <div style="flex: 1;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                    <input type="checkbox" name="is_featured" {{ $material->is_featured ? 'checked' : '' }} style="margin-right: 8px;">
                    Featured
                </label>
            </div>
        </div>
        
        <!-- Submit Buttons -->
        <div style="display: flex; gap: 15px;">
            <a href="{{ url('learning/training-materials/' . $material->id) }}" style="flex: 1; padding: 12px 30px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.3s;">
                Cancel
            </a>
            <button type="submit" style="flex: 2; padding: 12px 30px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background 0.3s;">
                <i class="fa fa-save"></i> Update Material
            </button>
        </div>
    </form>
</div>

<!-- Loading Modal -->
<div id="loading-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
        <div id="loading-spinner" style="display: inline-block; width: 50px; height: 50px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
        <h3 id="loading-title" style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">System is processing files</h3>
        <p id="loading-message" style="font-size: 14px; color: var(--text-secondary);">Please wait while we update this course...</p>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
    input[type="file"] {
        cursor: pointer;
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
        
        // Form submission with loading modal
        const form = document.querySelector('form');
        const modal = document.getElementById('loading-modal');
        const title = document.getElementById('loading-title');
        const message = document.getElementById('loading-message');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Show loading modal
                modal.style.display = 'flex';
                title.textContent = 'System is processing files';
                message.textContent = 'Please wait while we update this course...';
                
                // Disable submit button to prevent double submissions
                const submitButtons = form.querySelectorAll('button[type="submit"]');
                submitButtons.forEach(button => {
                    button.disabled = true;
                    button.style.opacity = '0.7';
                });
            });
        }
    });
</script>
@endsection
