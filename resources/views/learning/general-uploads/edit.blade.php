@extends('layouts.learning')

@section('title', 'Edit Upload - Whence Learn')

<style>
.upload-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-primary);
}

.form-input {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: white;
    font-size: 14px;
}

.form-select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: white;
    font-size: 14px;
}

.upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--light-bg);
}

.upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(52, 152, 219, 0.05);
}

.upload-area i {
    font-size: 36px;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.upload-area p {
    margin: 5px 0;
    color: var(--text-primary);
    font-weight: 500;
}

.poster-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--light-bg);
}

.poster-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(52, 152, 219, 0.05);
}

.poster-upload-area i {
    font-size: 36px;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.poster-upload-area p {
    margin: 5px 0;
    color: var(--text-primary);
    font-weight: 500;
}

.file-input {
    display: none;
}

.selected-poster {
    margin-top: 10px;
}

.selected-poster img {
    width: 120px;
    height: 68px;
    object-fit: cover;
    border-radius: 6px;
}

.selected-file {
    margin-top: 10px;
    padding: 15px;
    background: var(--light-bg);
    border-radius: 6px;
}

.btn-save {
    width: 100%;
    padding: 14px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 20px;
}

.btn-save:hover {
    background: #3bc9a5;
    transform: translateY(-2px);
}

.btn-save:disabled {
    background: var(--border-color);
    cursor: not-allowed;
    transform: none;
}
</style>

@section('content')
<div class="page-header">
    <h1>Edit Upload</h1>
    <p>Modify your uploaded file details</p>
</div>

<div class="upload-container">
    <form action="{{ url('learning/general-uploads/' . $upload->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <!-- File Name -->
        <div class="form-group">
            <label class="form-label">File Name</label>
            <input type="text" name="name" id="name" class="form-input" value="{{ old('name', $upload->name) }}" required>
        </div>
        
        <!-- General Topic -->
        <div class="form-group">
            <label class="form-label">General Topic</label>
            <select name="general_topic_id" id="generalTopicSelect" class="form-select">
                <option value="">Select a topic</option>
                @foreach($generalTopics as $topic)
                    <option value="{{ $topic->id }}" {{ old('general_topic_id', $upload->general_topic_id) == $topic->id ? 'selected' : '' }}>{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Position -->
        <div class="form-group">
            <label class="form-label">Position</label>
            <select name="position_id[]" id="positionSelect" class="form-select" multiple>
                <option value="">Select positions</option>
                @foreach($positions as $id => $name)
                    <option value="{{ $id }}" {{ in_array($id, old('position_id', $upload->positions->pluck('id')->toArray())) ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- File Type -->
        <div class="form-group">
            <label class="form-label">File Type</label>
            <select name="type" id="typeSelect" class="form-select" required onchange="togglePosterField()">
                <option value="">Select file type</option>
                <option value="video" {{ old('type', $upload->type) == 'video' ? 'selected' : '' }}>Video</option>
                <option value="audio" {{ old('type', $upload->type) == 'audio' ? 'selected' : '' }}>Audio</option>
                <option value="book" {{ old('type', $upload->type) == 'book' ? 'selected' : '' }}>Book</option>
                <option value="paper" {{ old('type', $upload->type) == 'paper' ? 'selected' : '' }}>Paper</option>
                <option value="document" {{ old('type', $upload->type) == 'document' ? 'selected' : '' }}>Document</option>
                <option value="image" {{ old('type', $upload->type) == 'image' ? 'selected' : '' }}>Image</option>
                <option value="other" {{ old('type', $upload->type) == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        
        <!-- Poster Upload (for videos) -->
        <div class="form-group" id="posterField" style="display: {{ old('type', $upload->type) == 'video' ? 'block' : 'none' }};">
            <label class="form-label">Video Poster/Thumbnail</label>
            <div class="poster-upload-area" id="posterUploadArea" onclick="document.getElementById('posterInput').click()">
                <i class="fa fa-image"></i>
                <p>Click to upload poster image</p>
                <span style="font-size: 12px; color: var(--text-secondary);">Recommended: 1280x720 or 1920x1080 (JPG, PNG)</span>
                <input type="file" id="posterInput" name="poster" class="file-input" accept="image/*" onchange="handlePosterSelect(this)">
            </div>
            <div class="selected-poster" id="selectedPoster" style="display: {{ old('poster', $upload->poster) ? 'block' : 'none' }}; margin-top: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img id="posterPreview" src="{{ old('poster', $upload->poster) }}" style="width: 120px; height: 68px; object-fit: cover; border-radius: 6px;">
                    <div>
                        <div id="posterName" style="font-weight: 600; font-size: 13px;">{{ old('poster', $upload->poster) ? basename($upload->poster) : 'Poster' }}</div>
                        <button type="button" onclick="removePoster()" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;">Remove</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- File Upload -->
        <div class="form-group">
            <label class="form-label">Replace File (optional)</label>
            <div class="upload-area" id="uploadArea">
                <i class="fa fa-cloud-upload"></i>
                <p style="margin: 10px 0; color: var(--text-primary); font-weight: 500;">Click to select a new file</p>
                <span style="font-size: 12px; color: var(--text-secondary);">MP4, MOV, AVI, MP3, WAV, OGG, PDF, DOC, DOCX, EPUB, MOBI, JPG, PNG, GIF</span>
                <input type="file" id="fileInput" name="file" class="file-input" accept="video/*,audio/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/*,.epub,.mobi,.azw" onchange="handleFileSelect(this)">
            </div>
            <div class="selected-file" id="selectedFile" style="display: none; margin-top: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="fa fa-file"></i>
                    </div>
                    <div>
                        <div id="fileName" style="font-weight: 600; color: var(--text-primary);"></div>
                        <div id="fileSize" style="font-size: 13px; color: var(--text-secondary);"></div>
                    </div>
                    <button type="button" onclick="removeFile()" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 18px;">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Current File Info -->
        <div class="form-group">
            <label class="form-label">Current File</label>
            <div style="background: var(--light-bg); padding: 15px; border-radius: 6px;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <div style="width: 50px; height: 50px; background: var(--primary-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 24px;">
                        <i class="fa {{ $upload->icon ?? 'fa-file' }}"></i>
                    </div>
                    <div>
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $upload->name }}</div>
                        <div style="font-size: 13px; color: var(--text-secondary);">{{ $upload->formatted_size }}</div>
                    </div>
                    <a href="{{ $upload->path }}" target="_blank" style="margin-left: auto; padding: 8px 16px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 500;">
                        <i class="fa fa-download"></i> Download
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <button type="submit" class="btn-save">
            <i class="fa fa-save"></i> Save Changes
        </button>
    </form>
</div>

<script>
var selectedPoster = {{ old('poster', $upload->poster) ? 'true' : 'false' }};
var selectedFile = null;

// Toggle poster field based on file type
function togglePosterField() {
    var type = document.getElementById('typeSelect').value;
    var posterField = document.getElementById('posterField');
    
    if (type === 'video') {
        posterField.style.display = 'block';
    } else {
        posterField.style.display = 'none';
        selectedPoster = false;
    }
}

// Handle poster selection
function handlePosterSelect(input) {
    if (input.files && input.files[0]) {
        selectedPoster = true;
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('posterPreview').src = e.target.result;
            document.getElementById('posterName').textContent = input.files[0].name;
            document.getElementById('selectedPoster').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Remove poster
function removePoster() {
    selectedPoster = false;
    document.getElementById('posterInput').value = '';
    document.getElementById('selectedPoster').style.display = 'none';
    document.getElementById('posterPreview').src = '';
}

// Handle file input click
document.getElementById('uploadArea').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

// Handle file selection
function handleFileSelect(input) {
    if (input.files && input.files.length > 0) {
        selectedFile = input.files[0];
        document.getElementById('fileName').textContent = selectedFile.name;
        document.getElementById('fileSize').textContent = formatFileSize(selectedFile.size);
        document.getElementById('selectedFile').style.display = 'block';
    }
}

// Remove selected file
function removeFile() {
    selectedFile = null;
    document.getElementById('fileInput').value = '';
    document.getElementById('selectedFile').style.display = 'none';
}

// Format file size
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' B';
    }
}
</script>

<div style="margin-top: 30px; text-align: center;">
    <a href="{{ url('learning/general-uploads') }}" style="color: var(--primary-color); text-decoration: none;">
        <i class="fa fa-arrow-left"></i> Back to General Uploads
    </a>
</div>
@endsection
