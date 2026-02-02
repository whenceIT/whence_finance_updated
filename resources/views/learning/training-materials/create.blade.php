@extends('layouts.learning')

@section('title', 'Add Training Material - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Add Training Material</h1>
    <p>Upload and manage learning resources</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); max-width: 800px; margin: 0 auto;">
    <form action="{{ url('learning/training-materials') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Title -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Title <span style="color: var(--accent-color);">*</span>
            </label>
            <input type="text" name="title" required style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px;" placeholder="Enter material title">
        </div>
        
        <!-- Description -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Description
            </label>
            <textarea name="description" rows="4" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px; resize: vertical;" placeholder="Enter material description (optional)"></textarea>
        </div>
        
        <!-- Material Type -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Material Type <span style="color: var(--accent-color);">*</span>
            </label>
            <div style="display: flex; gap: 15px;">
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="document" required style="margin-right: 8px;">
                    <i class="fa fa-file-pdf-o" style="color: #4a90e2; margin-right: 8px;"></i>
                    Document
                </label>
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="audio" style="margin-right: 8px;">
                    <i class="fa fa-headphones" style="color: #50c878; margin-right: 8px;"></i>
                    Audio
                </label>
                <label style="display: flex; align-items: center; cursor: pointer; padding: 10px 20px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                    <input type="radio" name="material_type" value="video" style="margin-right: 8px;">
                    <i class="fa fa-video-camera" style="color: #ff6b6b; margin-right: 8px;"></i>
                    Video
                </label>
            </div>
        </div>
        
        <!-- File Upload -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Upload File <span style="color: var(--accent-color);">*</span>
            </label>
            <div style="border: 2px dashed var(--border-color); border-radius: 8px; padding: 30px; text-align: center; background: var(--light-bg);">
                <i class="fa fa-cloud-upload" style="font-size: 48px; color: var(--text-secondary); margin-bottom: 15px;"></i>
                <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 10px;">
                    Drag and drop your file here, or click to browse
                </p>
                <input type="file" name="file" required style="width: 100%;" accept=".pdf,.doc,.doc,.docx,.mp3,.wav,.mp4,.webm">
                <p style="color: var(--text-secondary); font-size: 12px; margin-top: 10px;">
                    Supported formats: PDF, DOC, DOCX, MP3, WAV, MP4, WEBM (Max: 100MB)
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
                <option value="Operations">Operations</option>
                <option value="Recoveries">Recoveries</option>
                <option value="Administration">Administration</option>
                <option value="Finance">Finance</option>
                <option value="IT">IT</option>
                <option value="HR">HR</option>
                <option value="Legal">Legal</option>
                <option value="Compliance">Compliance</option>
                <option value="General">General</option>
            </select>
        </div>
        
        <!-- Category -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                Category
            </label>
            <input type="text" name="category" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: 6px; font-size: 14px;" placeholder="Enter category (optional)">
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
        
        <!-- Options -->
        <div style="display: flex; gap: 30px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                    <input type="checkbox" name="is_active" checked style="margin-right: 8px;">
                    Active
                </label>
            </div>
            <div style="flex: 1;">
                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 600; color: var(--text-primary);">
                    <input type="checkbox" name="is_featured" style="margin-right: 8px;">
                    Featured
                </label>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div style="display: flex; gap: 15px;">
            <a href="{{ url('learning/training-materials') }}" style="flex: 1; padding: 12px 30px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-align: center; text-decoration: none; font-weight: 600; transition: background 0.3s;">
                Cancel
            </a>
            <button type="submit" style="flex: 2; padding: 12px 30px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 16px; transition: background 0.3s;">
                <i class="fa fa-upload"></i> Upload Material
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
    
    input[type="file"]:hover {
        border-color: var(--primary-color);
    }
</style>
@endsection
