@extends('layouts.learning')

@section('title', 'Upload File - Whence Learn')

<style>
.upload-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 30px;
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 12px;
    padding: 60px 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: var(--light-bg);
}

.upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(74, 226, 188, 0.05);
}

.upload-area.dragover {
    border-color: var(--primary-color);
    background: rgba(74, 226, 188, 0.1);
}

.upload-area i {
    font-size: 64px;
    color: var(--primary-color);
    margin-bottom: 20px;
}

.upload-area h3 {
    font-size: 20px;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.upload-area p {
    color: var(--text-secondary);
    font-size: 14px;
}

.file-types {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
    margin-top: 20px;
}

.file-type-badge {
    background: white;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
}

.file-input {
    display: none;
}

.upload-progress {
    display: none;
    margin-top: 30px;
    padding: 20px;
    background: var(--light-bg);
    border-radius: 8px;
}

.progress-bar-container {
    height: 8px;
    background: var(--border-color);
    border-radius: 4px;
    overflow: hidden;
    margin-bottom: 10px;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
    width: 0%;
    transition: width 0.3s ease;
}

.progress-text {
    font-size: 14px;
    color: var(--text-secondary);
    text-align: center;
}

.upload-status {
    margin-top: 15px;
    padding: 12px;
    border-radius: 6px;
    text-align: center;
    font-size: 14px;
}

.upload-status.success {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.upload-status.error {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.selected-file {
    margin-top: 20px;
    padding: 15px;
    background: var(--light-bg);
    border-radius: 8px;
    display: none;
}

.selected-file-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.selected-file-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.selected-file-details {
    flex: 1;
}

.selected-file-name {
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.selected-file-size {
    font-size: 13px;
    color: var(--text-secondary);
}

.remove-file {
    background: none;
    border: none;
    color: var(--accent-color);
    cursor: pointer;
    font-size: 18px;
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

.form-select {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 6px;
    background: white;
    font-size: 14px;
}

.btn-upload {
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

.btn-upload:hover {
    background: #3bc9a5;
    transform: translateY(-2px);
}

.btn-upload:disabled {
    background: var(--border-color);
    cursor: not-allowed;
    transform: none;
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

/* Animated Progress Bar */
.upload-progress {
    display: none;
    margin-top: 30px;
    padding: 25px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 1px solid var(--border-color);
}

.upload-progress.active {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.progress-bar-container {
    height: 12px;
    background: #e9ecef;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 15px;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color) 0%, #3bc9a5 50%, var(--primary-color) 100%);
    background-size: 200% 100%;
    width: 0%;
    transition: width 0.3s ease;
    border-radius: 6px;
    position: relative;
    animation: progressAnimation 2s ease-in-out infinite;
}

@keyframes progressAnimation {
    0% { background-position: 100% 0; }
    100% { background-position: -100% 0; }
}

.progress-bar-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(
        90deg,
        rgba(255,255,255,0) 0%,
        rgba(255,255,255,0.3) 50%,
        rgba(255,255,255,0) 100%
    );
    animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.progress-text {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-primary);
    text-align: center;
    margin-bottom: 10px;
}

.progress-percentage {
    font-size: 24px;
    font-weight: 700;
    color: var(--primary-color);
    text-align: center;
    display: block;
    margin-bottom: 5px;
}

/* Internet Strength Warning */
.internet-warning {
    background: rgba(255, 193, 7, 0.1);
    border-left: 4px solid #ffc107;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.internet-warning i {
    color: #ffc107;
    margin-right: 10px;
}

.internet-warning strong {
    color: #ffc107;
}

.internet-warning p {
    margin: 0;
    color: var(--text-secondary);
    font-size: 13px;
}
</style>

@section('content')
<div class="page-header">
    <h1>Upload File</h1>
    <p>Upload videos, audios, books, papers, and other documents</p>
</div>

<div class="upload-container">
    <!-- Internet Strength Warning -->
    <div class="internet-warning">
        <i class="fa fa-exclamation-triangle"></i>
        <strong>Internet Strength Warning</strong>
        <p>Please ensure your internet connection is stable before uploading large files to avoid network errors during the merge process.</p>
    </div>

    <form id="uploadForm" enctype="multipart/form-data">
        @csrf
        
        <!-- File Upload Area -->
        <div class="upload-area" id="uploadArea">
            <i class="fa fa-cloud-upload"></i>
            <h3>Drag & Drop your file here</h3>
            <p>or click to browse files</p>
            <div class="file-types">
                <span class="file-type-badge">MP4, MOV, AVI</span>
                <span class="file-type-badge">MP3, WAV, OGG</span>
                <span class="file-type-badge">PDF, DOC, DOCX</span>
                <span class="file-type-badge">EPUB, MOBI</span>
                <span class="file-type-badge">JPG, PNG, GIF</span>
            </div>
            <input type="file" id="fileInput" class="file-input" accept="video/*,audio/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/*,.epub,.mobi,.azw">
        </div>
        
        <!-- Selected File Info -->
        <div class="selected-file" id="selectedFile">
            <div class="selected-file-info">
                <div class="selected-file-icon">
                    <i class="fa fa-file"></i>
                </div>
                <div class="selected-file-details">
                    <div class="selected-file-name" id="fileName"></div>
                    <div class="selected-file-size" id="fileSize"></div>
                </div>
                <button type="button" class="remove-file" onclick="removeFile()">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
        
        <!-- File Type Selection -->
        <div class="form-group">
            <label class="form-label">File Type</label>
            <select name="type" id="typeSelect" class="form-select" required onchange="togglePosterField()">
                <option value="">Select file type</option>
                <option value="video">Video</option>
                <option value="audio">Audio</option>
                <option value="book">Book</option>
                <option value="paper">Paper</option>
                <option value="document">Document</option>
                <option value="image">Image</option>
                <option value="other">Other</option>
            </select>
        </div>
        
        <!-- General Topic -->
        <div class="form-group">
            <label class="form-label">General Topic</label>
            <select name="general_topic_id" id="generalTopicSelect" class="form-select">
                <option value="">Select a topic</option>
                @foreach($generalTopics as $topic)
                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Position -->
        <div class="form-group">
            <label class="form-label">Position</label>
            <select name="position_id" id="positionSelect" class="form-select">
                <option value="">Select a position</option>
                @foreach($positions as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        
        <!-- Poster Upload (for all file types) -->
        <div class="form-group" id="posterField">
            <label class="form-label">File Poster/Thumbnail</label>
            <div class="poster-upload-area" id="posterUploadArea" onclick="document.getElementById('posterInput').click()">
                <i class="fa fa-image"></i>
                <p>Click to upload poster image</p>
                <span style="font-size: 12px; color: var(--text-secondary);">Recommended: 1280x720 or 1920x1080 (JPG, PNG)</span>
                <input type="file" id="posterInput" name="poster" class="file-input" accept="image/*" onchange="handlePosterSelect(this)">
            </div>
            <div class="selected-poster" id="selectedPoster" style="display: none; margin-top: 10px;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <img id="posterPreview" src="" style="width: 120px; height: 68px; object-fit: cover; border-radius: 6px;">
                    <div>
                        <div id="posterName" style="font-weight: 600; font-size: 13px;"></div>
                        <button type="button" onclick="removePoster()" style="background: none; border: none; color: #dc3545; cursor: pointer; font-size: 12px;">Remove</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Upload Progress -->
        <div class="upload-progress" id="uploadProgress">
            <span class="progress-percentage" id="progressPercentage">0%</span>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBar"></div>
            </div>
            <div class="progress-text" id="progressText">Preparing upload...</div>
            <div class="upload-status" id="uploadStatus"></div>
        </div>
        
        <!-- Upload Button -->
        <button type="submit" class="btn-upload" id="uploadBtn" disabled>
            <i class="fa fa-upload"></i> Upload File
        </button>
    </form>
</div>

<!-- Loading Modal -->
<div id="loading-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: none; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);">
        <div id="loading-spinner" style="display: inline-block; width: 50px; height: 50px; border: 3px solid #f3f3f3; border-top: 3px solid var(--primary-color); border-radius: 50%; animation: spin 1s linear infinite; margin-bottom: 20px;"></div>
        <h3 id="loading-title" style="font-size: 18px; font-weight: 600; margin-bottom: 10px; color: var(--text-primary);">System is processing files</h3>
        <p id="loading-message" style="font-size: 14px; color: var(--text-secondary);">Please wait while we upload this file...</p>
    </div>
</div>

<script>
const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
const uploadPromises = {};
let isUploading = false;
var selectedFile = null;
var selectedPoster = null;

document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading modal
    const form = document.querySelector('form');
    const modal = document.getElementById('loading-modal');
    const title = document.getElementById('loading-title');
    const message = document.getElementById('loading-message');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (isUploading) {
                return;
            }
            
            isUploading = true;
            
            // Show loading modal
            modal.style.display = 'flex';
            title.textContent = 'Uploading and processing file';
            message.textContent = 'Please wait while we upload and process this file...';
            
            // Disable submit button to prevent double submissions
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                button.style.opacity = '0.7';
            });
            
            // Wait for all uploads to complete
            try {
                await Promise.all(Object.values(uploadPromises));
                form.submit();
            } catch (error) {
                console.error('Upload error:', error);
                alert('Error uploading files. Please try again.');
                isUploading = false;
                submitButtons.forEach(button => {
                    button.disabled = false;
                    button.style.opacity = '1';
                });
                modal.style.display = 'none';
            }
        });
    }
});

// Toggle poster field - always show for all file types
function togglePosterField() {
    var posterField = document.getElementById('posterField');
    posterField.style.display = 'block';
}

// Handle poster selection
function handlePosterSelect(input) {
    if (input.files && input.files[0]) {
        selectedPoster = input.files[0];
        
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('posterPreview').src = e.target.result;
            document.getElementById('posterName').textContent = selectedPoster.name;
            document.getElementById('selectedPoster').style.display = 'block';
        };
        reader.readAsDataURL(selectedPoster);
    }
}

// Remove poster
function removePoster() {
    selectedPoster = null;
    document.getElementById('posterInput').value = '';
    document.getElementById('selectedPoster').style.display = 'none';
    document.getElementById('posterPreview').src = '';
}

// Update progress display
function updateProgress(percent, text) {
    document.getElementById('progressBar').style.width = percent + '%';
    document.getElementById('progressPercentage').textContent = percent + '%';
    document.getElementById('progressText').textContent = text;
}

// File input handling
var fileInput = document.getElementById('fileInput');
var uploadArea = document.getElementById('uploadArea');

uploadArea.addEventListener('click', function() {
    fileInput.click();
});

uploadArea.addEventListener('dragover', function(e) {
    e.preventDefault();
    uploadArea.classList.add('dragover');
});

uploadArea.addEventListener('dragleave', function() {
    uploadArea.classList.remove('dragover');
});

uploadArea.addEventListener('drop', function(e) {
    e.preventDefault();
    uploadArea.classList.remove('dragover');
    
    if (e.dataTransfer.files.length > 0) {
        handleFileSelect(e.dataTransfer.files[0]);
    }
});

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        handleFileSelect(this.files[0]);
    }
});

async function handleFileSelect(file) {
    selectedFile = file;
    
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('selectedFile').style.display = 'block';
    document.getElementById('uploadBtn').disabled = false;
    
    // Auto-detect file type
    var type = detectFileType(file);
    document.getElementById('typeSelect').value = type;
    // Toggle poster field based on detected file type
    togglePosterField();
    
    // Start immediate upload
    const progressContainer = document.getElementById('uploadProgress');
    const fileNameElement = document.getElementById('fileName');
    const progressText = document.getElementById('progressText');
    const progressBar = document.getElementById('progressBar');
    
    progressContainer.classList.add('active');
    progressText.textContent = '0%';
    progressBar.style.width = '0%';
    
    // Create and store upload promise
    uploadPromises['file'] = uploadFile(file, type, (progress) => {
        progressText.textContent = `Uploading chunk ${Math.ceil(progress / 100)} of ${Math.ceil(file.size / CHUNK_SIZE)}`;
        progressBar.style.width = `${progress}%`;
        document.getElementById('progressPercentage').textContent = `${Math.round(progress)}%`;
    });
}

function removeFile() {
    selectedFile = null;
    fileInput.value = '';
    document.getElementById('selectedFile').style.display = 'none';
    document.getElementById('uploadBtn').disabled = true;
    document.getElementById('uploadProgress').classList.remove('active');
    delete uploadPromises['file'];
}

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

function detectFileType(file) {
    var type = 'other';
    var mimeType = file.type.toLowerCase();
    var name = file.name.toLowerCase();
    var extension = name.split('.').pop();
    
    if (mimeType.startsWith('video/') || ['mp4', 'mov', 'avi', 'mkv', 'webm', 'flv', 'wmv'].includes(extension)) {
        type = 'video';
    } else if (mimeType.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'].includes(extension)) {
        type = 'audio';
    } else if (mimeType.startsWith('image/') || ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'].includes(extension)) {
        type = 'image';
    } else if (['epub', 'mobi', 'azw', 'azw3'].includes(extension)) {
        type = 'book';
    } else if (mimeType === 'application/pdf' && (name.includes('paper') || name.includes('research'))) {
        type = 'paper';
    } else if (['pdf', 'doc', 'docx', 'txt', 'rtf', 'odt'].includes(extension)) {
        type = 'document';
    }
    
    return type;
}

async function uploadFile(file, type, onProgress) {
    const chunkSize = CHUNK_SIZE;
    const totalChunks = Math.ceil(file.size / chunkSize);
    const fileId = generateFileId(file);
    
    for (let i = 0; i < totalChunks; i++) {
        const start = i * chunkSize;
        const end = Math.min(start + chunkSize, file.size);
        const chunk = file.slice(start, end);
        
        const formData = new FormData();
        formData.append('chunk', chunk);
        formData.append('index', i);
        formData.append('totalChunks', totalChunks);
        formData.append('filename', file.name);
        formData.append('fileId', fileId);
        formData.append('type', type);
        
        // Add poster to first chunk if available
        if (i === 0 && selectedPoster) {
            formData.append('poster', selectedPoster);
        }
        
        const response = await fetch('{{ url("learning/general-uploads/upload-chunk") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        });
        
        if (!response.ok) {
            throw new Error(`Failed to upload chunk ${i + 1}/${totalChunks}`);
        }
        
        const progress = ((i + 1) / totalChunks) * 100;
        onProgress(progress);
    }
    
    // Merge chunks and get file path
    const mergeResponse = await fetch('{{ url("learning/general-uploads/merge-chunks") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            filename: file.name,
            fileId: fileId,
            type: type,
            totalChunks: totalChunks,
            general_topic_id: document.getElementById('generalTopicSelect').value,
            position_id: document.getElementById('positionSelect').value
        })
    });
    
    if (!mergeResponse.ok) {
        throw new Error('Failed to merge chunks');
    }
    
    const data = await mergeResponse.json();
    // Create hidden input to store file path for form submission
    const fileInput = document.createElement('input');
    fileInput.type = 'hidden';
    fileInput.name = 'file_path';
    fileInput.value = data.filePath;
    document.getElementById('uploadForm').appendChild(fileInput);
}

function generateFileId(file) {
    return `${file.name}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
}
</script>

<div style="margin-top: 30px; text-align: center;">
    <a href="{{ url('learning/general-uploads') }}" style="color: var(--primary-color); text-decoration: none;">
        <i class="fa fa-arrow-left"></i> Back to General Uploads
    </a>
</div>
@endsection
