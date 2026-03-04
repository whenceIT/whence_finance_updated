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
        
        <!-- Material Type -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Material Type <span style="color: var(--accent-color);">*</span>
            </label>
            <div style="display: flex; gap: 15px;">
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="document" {{ $material->material_type == 'document' ? 'checked' : '' }} style="margin-right: 8px;">
                    <i class="fa fa-file-pdf-o" style="color: #4a90e2; margin-right: 8px;"></i>
                    Document
                </label>
                <!-- <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="audio" {{ $material->material_type == 'audio' ? 'checked' : '' }} style="margin-right: 8px;">
                    <i class="fa fa-headphones" style="color: #50c878; margin-right: 8px;"></i>
                    Audio
                </label>
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="video" {{ $material->material_type == 'video' ? 'checked' : '' }} style="margin-right: 8px;">
                    <i class="fa fa-video-camera" style="color: #ff6b6b; margin-right: 8px;"></i>
                    Video
                </label> -->
            </div>
        </div>
        
        <!-- File Upload -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Upload New File (Mandatory) <span style="color: var(--accent-color);">*</span>
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
        </div>
        
        <!-- Department -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Department <span style="color: var(--accent-color);">*</span>
            </label>
            <select name="department" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; background: white;">
                <option value="">Select Department</option>
                <option value="Operations" {{ $material->department == 'Operations' ? 'selected' : '' }}>Operations</option>
                <option value="Recoveries" {{ $material->department == 'Recoveries' ? 'selected' : '' }}>Recoveries</option>
                <option value="Administration" {{ $material->department == 'Administration' ? 'selected' : '' }}>Administration</option>
                <option value="Finance" {{ $material->department == 'Finance' ? 'selected' : '' }}>Finance</option>
                <option value="IT" {{ $material->department == 'IT' ? 'selected' : '' }}>IT</option>
                <option value="HR" {{ $material->department == 'HR' ? 'selected' : '' }}>HR</option>
                <option value="Legal" {{ $material->department == 'Legal' ? 'selected' : '' }}>Legal</option>
                <option value="Compliance" {{ $material->department == 'Compliance' ? 'selected' : '' }}>Compliance</option>
                <option value="General" {{ $material->department == 'General' ? 'selected' : '' }}>General</option>
            </select>
        </div>
        
        <!-- Category -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Category
            </label>
            <input type="text" name="category" value="{{ old('category', $material->category) }}" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px;" placeholder="Enter category (optional)">
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

<style>
    input[type="radio"]:checked + label {
        border-color: var(--primary-color) !important;
        background: rgba(74, 144, 226, 0.1) !important;
    }
    
    input[type="file"] {
        cursor: pointer;
    }
</style>
@endsection
