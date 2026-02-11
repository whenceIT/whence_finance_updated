@extends('layouts.learning')

@section('title', 'Add Training Material - Whence Learn')

@section('content')
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

    <form action="{{ url('learning/training-materials') }}" method="POST" id="training-material-form">
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
                <!-- Material Type -->
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                        Material Type <span style="color: var(--accent-color);">*</span>
                    </label>
                    <div style="display: flex; gap: 10px;">
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 8px 12px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                            <input type="radio" name="material_type" value="document" required style="margin-right: 8px;">
                            <i class="fa fa-file-pdf-o" style="color: #4a90e2; margin-right: 6px;"></i>
                            <span style="font-size: 13px;">Document</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 8px 12px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                            <input type="radio" name="material_type" value="video" style="margin-right: 8px;">
                            <i class="fa fa-video-camera" style="color: #ff6b6b; margin-right: 6px;"></i>
                            <span style="font-size: 13px;">Video</span>
                        </label>
                        <label style="display: flex; align-items: center; cursor: pointer; padding: 8px 12px; border: 2px solid var(--border-color); border-radius: 6px; background: white; flex: 1;">
                            <input type="radio" name="material_type" value="audio" style="margin-right: 8px;">
                            <i class="fa fa-headphones" style="color: #50c878; margin-right: 6px;"></i>
                            <span style="font-size: 13px;">Audio</span>
                        </label>
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
            </div>
            
            <!-- Categories (Many-to-Many) -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px; color: var(--text-primary);">
                    Categories
                </label>
                <select name="category_ids[]" class="category-select2" multiple style="width: 100%;">
                    @if(isset($categories) && count($categories) > 0)
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    @endif
                </select>
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
            
            <!-- Step 1 Navigation -->
            <div style="display: flex; gap: 15px; justify-content: flex-end;">
                <a href="{{ url('learning/training-materials') }}" style="padding: 12px 24px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600;">
                    Cancel
                </a>
                <button type="button" onclick="nextStep()" style="padding: 12px 30px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    Next: Add Topics <i class="fa fa-arrow-right" style="margin-left: 8px;"></i>
                </button>
            </div>
        </div>
        
        <!-- Step 2: Topics -->
        <div id="step-2" class="wizard-step" style="display: none;">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 24px; color: var(--text-primary);">Course Topics</h3>
            <p style="color: var(--text-secondary); margin-bottom: 20px; font-size: 14px;">Add topics to your course. Each topic can be a video, PDF, PPT, or document.</p>
            
            <!-- Topics Container -->
            <div id="topics-container">
                <!-- Topics will be added here dynamically -->
            </div>
            
            <!-- Add Topic Button -->
            <div style="margin-bottom: 30px;">
                <button type="button" onclick="addTopic()" style="padding: 10px 20px; background: white; color: var(--primary-color); border: 2px dashed var(--primary-color); border-radius: 6px; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                    <i class="fa fa-plus"></i> Add Topic
                </button>
            </div>
            
            <!-- Step 2 Navigation -->
            <div style="display: flex; gap: 15px; justify-content: space-between;">
                <button type="button" onclick="prevStep()" style="padding: 12px 24px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fa fa-arrow-left" style="margin-right: 8px;"></i> Previous
                </button>
                <button type="submit" style="padding: 12px 30px; background: var(--secondary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fa fa-check"></i> Create Course
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Topic Template -->
<template id="topic-template">
    <div class="topic-item" style="background: var(--light-bg); border-radius: 8px; padding: 20px; margin-bottom: 15px; border: 1px solid var(--border-color);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h4 style="font-size: 14px; font-weight: 600; color: var(--text-primary); margin: 0;">Topic #<span class="topic-number"></span></h4>
            <button type="button" onclick="removeTopic(this)" style="background: none; border: none; color: var(--accent-color); cursor: pointer; font-size: 18px;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        
        <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 15px;">
            <!-- Topic Name -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Topic Name *</label>
                <input type="text" name="topic_name[]" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="Enter topic name">
            </div>
            
            <!-- Topic Type -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Type *</label>
                <select name="topic_type[]" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px; background: white;">
                    <option value="video">Video</option>
                    <option value="pdf">PDF</option>
                    <option value="ppt">PPT</option>
                    <option value="document">Document</option>
                </select>
            </div>
            
            <!-- Duration -->
            <div>
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Duration (min)</label>
                <input type="number" name="topic_duration[]" min="1" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="e.g., 15">
            </div>
        </div>
        
        <!-- File Link (Google Drive or URL) -->
        <div style="margin-top: 15px;">
            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Resource Link *</label>
            <div style="border: 1px solid var(--border-color); border-radius: 6px; padding: 15px; background: white;">
                <i class="fa fa-link" style="font-size: 16px; color: var(--primary-color); margin-right: 8px;"></i>
                <input type="url" name="topic_file[]" required style="width: calc(100% - 30px); padding: 10px; border: none; font-size: 13px;" placeholder="Paste Google Drive or file URL here">
            </div>
            <small style="color: var(--text-secondary); font-size: 11px;">Paste a link to Google Drive, Dropbox, or any file URL</small>
        </div>
    </div>
</template>

<style>
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
/* Select2 Custom Styles */
.category-select2 ~ .select2-container {
    width: 100% !important;
}
.category-select2 ~ .select2-container .select2-selection {
    border: 1px solid var(--border-color) !important;
    border-radius: 6px !important;
    min-height: 42px !important;
    padding: 4px 8px !important;
    background: white !important;
}
.category-select2 ~ .select2-container .select2-selection__choice {
    background: var(--primary-color) !important;
    color: white !important;
    border: none !important;
    border-radius: 4px !important;
    padding: 4px 8px !important;
    margin: 2px !important;
}
.category-select2 ~ .select2-container .select2-selection__choice__remove {
    color: white !important;
    margin-right: 4px !important;
}
.category-select2 ~ .select2-container .select2-search--inline {
    padding: 0 !important;
}
.category-select2 ~ .select2-container .select2-selection__rendered {
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 4px !important;
}
</style>

<script>
let topicCount = 0;

// Initialize Select2 for categories
document.addEventListener('DOMContentLoaded', function() {
    if (typeof $ !== 'undefined' && $.fn.select2) {
        $('.category-select2').select2({
            placeholder: 'Select categories',
            allowClear: true,
            width: '100%'
        });
    }
});

function nextStep() {
    // Validate step 1
    const title = document.querySelector('input[name="title"]').value;
    const materialType = document.querySelector('input[name="material_type"]:checked');
    const department = document.querySelector('select[name="department"]').value;
    const targetRole = document.querySelector('select[name="target_role"]').value;
    
    if (!title) {
        alert('Please enter a course title.');
        return;
    }
    if (!materialType) {
        alert('Please select a material type.');
        return;
    }
    if (!department) {
        alert('Please select a department.');
        return;
    }
    if (!targetRole) {
        alert('Please select a target audience.');
        return;
    }
    
    document.getElementById('step-1').style.display = 'none';
    document.getElementById('step-2').style.display = 'block';
    
    document.getElementById('step-indicator-2').classList.add('active');
    document.getElementById('step-indicator-2').querySelector('div').style.background = 'var(--primary-color)';
    document.getElementById('step-indicator-2').querySelector('div').style.color = 'white';
    document.getElementById('step-indicator-2').querySelector('span').style.color = 'var(--primary-color)';
    document.getElementById('step-line-1').style.background = 'var(--primary-color)';
    
    // Add first topic if none exist
    if (topicCount === 0) {
        addTopic();
    }
}

function prevStep() {
    document.getElementById('step-2').style.display = 'none';
    document.getElementById('step-1').style.display = 'block';
    
    document.getElementById('step-indicator-2').classList.remove('active');
    document.getElementById('step-indicator-2').querySelector('div').style.background = 'var(--border-color)';
    document.getElementById('step-indicator-2').querySelector('div').style.color = 'var(--text-secondary)';
    document.getElementById('step-indicator-2').querySelector('span').style.color = 'var(--text-secondary)';
    document.getElementById('step-line-1').style.background = 'var(--border-color)';
}

function addTopic() {
    topicCount++;
    const template = document.getElementById('topic-template');
    const clone = template.content.cloneNode(true);
    
    clone.querySelector('.topic-number').textContent = topicCount;
    document.getElementById('topics-container').appendChild(clone);
    
    // Update file input name with index
    const topicItems = document.querySelectorAll('.topic-item');
    const lastTopic = topicItems[topicItems.length - 1];
    const fileInputs = lastTopic.querySelectorAll('input[type="file"]');
    fileInputs.forEach((input, idx) => {
        input.name = `topic_file[${topicCount - 1}]`;
    });
}

function removeTopic(button) {
    const topicItem = button.closest('.topic-item');
    topicItem.remove();
    
    // Renumber topics
    const topicItems = document.querySelectorAll('.topic-item');
    topicItems.forEach((item, index) => {
        item.querySelector('.topic-number').textContent = index + 1;
    });
    topicCount = topicItems.length;
}
</script>
@endsection
