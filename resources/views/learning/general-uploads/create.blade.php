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
</style>

@section('content')
<div class="page-header">
    <h1>Upload File</h1>
    <p>Upload videos, audios, books, papers, and other documents</p>
</div>

<div class="upload-container">
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
            <select name="type" id="typeSelect" class="form-select" required>
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
        
        <!-- Upload Progress -->
        <div class="upload-progress" id="uploadProgress">
            <div class="progress-bar-container">
                <div class="progress-bar-fill" id="progressBar"></div>
            </div>
            <div class="progress-text" id="progressText">Uploading... 0%</div>
            <div class="upload-status" id="uploadStatus"></div>
        </div>
        
        <!-- Upload Button -->
        <button type="submit" class="btn-upload" id="uploadBtn" disabled>
            <i class="fa fa-upload"></i> Upload File
        </button>
    </form>
</div>

<script>
var selectedFile = null;
var chunkSize = 5 * 1024 * 1024; // 5MB chunks

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

function handleFileSelect(file) {
    selectedFile = file;
    
    document.getElementById('fileName').textContent = file.name;
    document.getElementById('fileSize').textContent = formatFileSize(file.size);
    document.getElementById('selectedFile').style.display = 'block';
    document.getElementById('uploadBtn').disabled = false;
    
    // Auto-detect file type
    var type = detectFileType(file);
    document.getElementById('typeSelect').value = type;
}

function removeFile() {
    selectedFile = null;
    fileInput.value = '';
    document.getElementById('selectedFile').style.display = 'none';
    document.getElementById('uploadBtn').disabled = true;
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

// Upload form submission
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!selectedFile) {
        return;
    }
    
    var type = document.getElementById('typeSelect').value;
    if (!type) {
        alert('Please select a file type');
        return;
    }
    
    // Check if file needs chunked upload
    if (selectedFile.size > chunkSize) {
        uploadChunked(selectedFile, type);
    } else {
        uploadRegular(selectedFile, type);
    }
});

function uploadRegular(file, type) {
    var formData = new FormData();
    formData.append('file', file);
    formData.append('type', type);
    
    var xhr = new XMLHttpRequest();
    
    // Show progress
    document.getElementById('uploadProgress').style.display = 'block';
    document.getElementById('uploadBtn').disabled = true;
    
    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            var percent = Math.round((e.loaded / e.total) * 100);
            document.getElementById('progressBar').style.width = percent + '%';
            document.getElementById('progressText').textContent = 'Uploading... ' + percent + '%';
        }
    });
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('uploadStatus').className = 'upload-status success';
                document.getElementById('uploadStatus').textContent = 'File uploaded successfully!';
                setTimeout(function() {
                    window.location.href = '{{ url('learning/general-uploads') }}';
                }, 1500);
            } else {
                document.getElementById('uploadStatus').className = 'upload-status error';
                document.getElementById('uploadStatus').textContent = 'Error: ' + response.message;
                document.getElementById('uploadBtn').disabled = false;
            }
        } else {
            document.getElementById('uploadStatus').className = 'upload-status error';
            document.getElementById('uploadStatus').textContent = 'Upload failed. Please try again.';
            document.getElementById('uploadBtn').disabled = false;
        }
    };
    
    xhr.onerror = function() {
        document.getElementById('uploadStatus').className = 'upload-status error';
        document.getElementById('uploadStatus').textContent = 'Network error. Please try again.';
        document.getElementById('uploadBtn').disabled = false;
    };
    
    xhr.open('POST', '{{ url('learning/general-uploads') }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.send(formData);
}

function uploadChunked(file, type) {
    var fileId = 'file_' + Date.now();
    var totalChunks = Math.ceil(file.size / chunkSize);
    var chunkIndex = 0;
    
    document.getElementById('uploadProgress').style.display = 'block';
    document.getElementById('uploadBtn').disabled = true;
    
    function uploadNextChunk() {
        if (chunkIndex >= totalChunks) {
            // All chunks uploaded, merge them
            mergeChunks(fileId, file.name, totalChunks, type);
            return;
        }
        
        var start = chunkIndex * chunkSize;
        var end = Math.min(start + chunkSize, file.size);
        var chunk = file.slice(start, end);
        
        var formData = new FormData();
        formData.append('chunk', chunk);
        formData.append('index', chunkIndex);
        formData.append('totalChunks', totalChunks);
        formData.append('filename', file.name);
        formData.append('fileId', fileId);
        formData.append('type', type);
        
        var xhr = new XMLHttpRequest();
        
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                var chunkProgress = (e.loaded / e.total) * 100;
                var overallProgress = ((chunkIndex + (chunkProgress / 100)) / totalChunks) * 100;
                document.getElementById('progressBar').style.width = overallProgress + '%';
                document.getElementById('progressText').textContent = 'Uploading chunk ' + (chunkIndex + 1) + ' of ' + totalChunks + '... ' + Math.round(overallProgress) + '%';
            }
        });
        
        xhr.onload = function() {
            if (xhr.status === 200) {
                chunkIndex++;
                uploadNextChunk();
            } else {
                document.getElementById('uploadStatus').className = 'upload-status error';
                document.getElementById('uploadStatus').textContent = 'Chunk upload failed. Please try again.';
                document.getElementById('uploadBtn').disabled = false;
            }
        };
        
        xhr.onerror = function() {
            document.getElementById('uploadStatus').className = 'upload-status error';
            document.getElementById('uploadStatus').textContent = 'Network error during chunk upload.';
            document.getElementById('uploadBtn').disabled = false;
        };
        
        xhr.open('POST', '{{ url('learning/general-uploads/upload-chunk') }}');
        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
        xhr.send(formData);
    }
    
    uploadNextChunk();
}

function mergeChunks(fileId, filename, totalChunks, type) {
    var formData = new FormData();
    formData.append('fileId', fileId);
    formData.append('filename', filename);
    formData.append('totalChunks', totalChunks);
    formData.append('type', type);
    
    document.getElementById('progressText').textContent = 'Finalizing upload...';
    
    var xhr = new XMLHttpRequest();
    
    xhr.onload = function() {
        if (xhr.status === 200) {
            var response = JSON.parse(xhr.responseText);
            if (response.success) {
                document.getElementById('uploadStatus').className = 'upload-status success';
                document.getElementById('uploadStatus').textContent = 'File uploaded successfully!';
                setTimeout(function() {
                    window.location.href = '{{ url('learning/general-uploads') }}';
                }, 1500);
            } else {
                document.getElementById('uploadStatus').className = 'upload-status error';
                document.getElementById('uploadStatus').textContent = 'Error: ' + response.message;
                document.getElementById('uploadBtn').disabled = false;
            }
        } else {
            document.getElementById('uploadStatus').className = 'upload-status error';
            document.getElementById('uploadStatus').textContent = 'Merge failed. Please try again.';
            document.getElementById('uploadBtn').disabled = false;
        }
    };
    
    xhr.onerror = function() {
        document.getElementById('uploadStatus').className = 'upload-status error';
        document.getElementById('uploadStatus').textContent = 'Network error during merge.';
        document.getElementById('uploadBtn').disabled = false;
    };
    
    xhr.open('POST', '{{ url('learning/general-uploads/merge-chunks') }}');
    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
    xhr.send(formData);
}
</script>

<div style="margin-top: 30px; text-align: center;">
    <a href="{{ url('learning/general-uploads') }}" style="color: var(--primary-color); text-decoration: none;">
        <i class="fa fa-arrow-left"></i> Back to General Uploads
    </a>
</div>
@endsection
