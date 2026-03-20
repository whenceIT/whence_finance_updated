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

/* Wizard styles */
.wizard-container {
    position: relative;
}

.wizard-progress {
    display: flex;
    justify-content: space-between;
    margin-bottom: 40px;
    position: relative;
}

.wizard-progress::before {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--border-color);
    z-index: 1;
}

.wizard-progress::after {
    content: '';
    position: absolute;
    top: 20px;
    left: 0;
    width: 0%;
    height: 2px;
    background: var(--primary-color);
    z-index: 2;
    transition: width 0.3s ease;
}

.wizard-progress.active-step-1::after { width: 0%; }
.wizard-progress.active-step-2::after { width: 50%; }
.wizard-progress.active-step-3::after { width: 100%; }

.wizard-step {
    position: relative;
    z-index: 3;
    text-align: center;
    flex: 1;
}

.wizard-step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    border: 2px solid var(--border-color);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.wizard-step.active .wizard-step-number {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.wizard-step.completed .wizard-step-number {
    background: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.wizard-step-label {
    font-size: 14px;
    color: var(--text-secondary);
    font-weight: 500;
}

.wizard-step.active .wizard-step-label {
    color: var(--text-primary);
    font-weight: 600;
}

/* Step content */
.wizard-step-content {
    display: none;
    animation: fadeIn 0.3s ease;
}

.wizard-step-content.active {
    display: block;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Buttons */
.wizard-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 30px;
}

.btn-wizard {
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    border: none;
}

.btn-wizard:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.btn-wizard-prev {
    background: white;
    color: var(--text-primary);
    border: 1px solid var(--border-color);
}

.btn-wizard-prev:hover:not(:disabled) {
    background: var(--light-bg);
}

.btn-wizard-next {
    background: var(--primary-color);
    color: white;
}

.btn-wizard-next:hover:not(:disabled) {
    background: #3bc9a5;
}

.btn-wizard-submit {
    background: var(--success-color);
    color: white;
    width: 100%;
}

.btn-wizard-submit:hover:not(:disabled) {
    background: #28a745;
}

/* File upload */
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

/* Poster upload */
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

/* Progress bar styles */
.progress-container {
    margin-top: 8px;
    display: none;
}

.progress-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 4px;
}

.file-name {
    font-size: 12px;
    color: var(--text-secondary);
}

.progress-text {
    font-size: 12px;
    color: var(--text-secondary);
}

.progress-bar-container {
    width: 100%;
    height: 4px;
    background: var(--border-color);
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: var(--secondary-color);
    width: 0%;
    transition: width 0.3s ease;
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

/* Selected file info */
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

/* Poster preview */
.selected-poster {
    display: none;
    margin-top: 10px;
}

.poster-preview-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.poster-preview {
    width: 120px;
    height: 68px;
    object-fit: cover;
    border-radius: 6px;
}

/* Form styles */
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

/* Loading Modal */
#loading-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}

#loading-modal .modal-content {
    background: white;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    max-width: 400px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

#loading-spinner {
    display: inline-block;
    width: 50px;
    height: 50px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

#loading-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text-primary);
}

#loading-message {
    font-size: 14px;
    color: var(--text-secondary);
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

    <div class="wizard-container">
        <!-- Wizard Progress -->
        <div class="wizard-progress" id="wizardProgress">
            <div class="wizard-step active" data-step="1">
                <div class="wizard-step-number">1</div>
                <div class="wizard-step-label">File Details</div>
            </div>
            <div class="wizard-step" data-step="2">
                <div class="wizard-step-number">2</div>
                <div class="wizard-step-label">Additional Info</div>
            </div>
            <div class="wizard-step" data-step="3">
                <div class="wizard-step-number">3</div>
                <div class="wizard-step-label">Upload & Confirm</div>
            </div>
        </div>

        <form id="uploadForm" action="{{ route('learning.general-uploads.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Step 1: File Details -->
            <div class="wizard-step-content active" data-step="1">
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
                    <input type="file" id="fileInput" class="file-input" accept="video/*,audio/*,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,image/*,.epub,.mobi,.azw" onchange="handleFileSelect(this, 'file')">
                </div>
                
                <!-- File Progress Container -->
                <div id="file-progress-container" class="progress-container">
                    <div class="progress-header">
                        <span id="file-file-name" class="file-name"></span>
                        <span id="file-progress-text" class="progress-text">0%</span>
                    </div>
                    <div class="progress-bar-container">
                        <div id="file-progress-bar" class="progress-bar-fill"></div>
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

                <!-- Poster Upload (for all file types) -->
                <div class="form-group">
                    <label class="form-label">File Poster/Thumbnail</label>
                    <div class="poster-upload-area" id="posterUploadArea" onclick="document.getElementById('posterInput').click()">
                        <i class="fa fa-image"></i>
                        <p>Click to upload poster image</p>
                        <span style="font-size: 12px; color: var(--text-secondary);">Recommended: 1280x720 or 1920x1080 (JPG, PNG)</span>
                        <input type="file" id="posterInput" name="poster" class="file-input" accept="image/*" onchange="handleFileSelect(this, 'poster')">
                    </div>
                    <div id="poster-progress-container" class="progress-container">
                        <div class="progress-header">
                            <span id="poster-file-name" class="file-name"></span>
                            <span id="poster-progress-text" class="progress-text">0%</span>
                        </div>
                        <div class="progress-bar-container">
                            <div id="poster-progress-bar" class="progress-bar-fill"></div>
                        </div>
                    </div>
                    <div class="selected-poster" id="selectedPoster" style="display: none; margin-top: 10px;">
                        <div class="poster-preview-container">
                            <img id="posterPreview" src="" class="poster-preview">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Additional Info -->
            <div class="wizard-step-content" data-step="2">
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
            </div>

            <!-- Step 3: Upload & Confirm -->
            <div class="wizard-step-content" data-step="3">
                <div class="form-group">
                    <h3 style="margin-bottom: 20px;">Upload Summary</h3>
                    
                    <!-- File Info -->
                    <div id="file-summary" style="display: none; margin-bottom: 20px; padding: 15px; background: var(--light-bg); border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">Main File</h4>
                        <div class="selected-file-info">
                            <div class="selected-file-icon">
                                <i id="file-summary-icon" class="fa fa-file"></i>
                            </div>
                            <div class="selected-file-details">
                                <div class="selected-file-name" id="file-summary-name"></div>
                                <div class="selected-file-size" id="file-summary-size"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Poster Info -->
                    <div id="poster-summary" style="display: none; margin-bottom: 20px; padding: 15px; background: var(--light-bg); border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">Poster/Thumbnail</h4>
                        <div class="selected-file-info">
                            <div class="selected-file-icon">
                                <i class="fa fa-image"></i>
                            </div>
                            <div class="selected-file-details">
                                <div class="selected-file-name" id="poster-summary-name"></div>
                                <div class="selected-file-size" id="poster-summary-size"></div>
                            </div>
                        </div>
                    </div>

                    <!-- File Type Info -->
                    <div id="type-summary" style="display: none; margin-bottom: 20px; padding: 15px; background: var(--light-bg); border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">File Type</h4>
                        <p id="type-summary-text"></p>
                    </div>

                    <!-- Additional Info -->
                    <div id="additional-info-summary" style="display: none; margin-bottom: 20px; padding: 15px; background: var(--light-bg); border-radius: 8px;">
                        <h4 style="margin-top: 0; margin-bottom: 10px;">Additional Information</h4>
                        <p id="topic-summary" style="margin: 5px 0;"></p>
                        <p id="position-summary" style="margin: 5px 0;"></p>
                    </div>
                </div>
            </div>

            <!-- Hidden fields to store uploaded file paths -->
            <input type="hidden" id="file_file_path" name="file_path">
            <input type="hidden" id="poster_file_path" name="poster_path">
            
            <!-- Wizard Buttons -->
            <div class="wizard-buttons">
                <button type="button" class="btn-wizard btn-wizard-prev" id="prevBtn" disabled onclick="previousStep()">
                    <i class="fa fa-arrow-left"></i> Previous
                </button>
                <button type="button" class="btn-wizard btn-wizard-next" id="nextBtn" disabled onclick="nextStep()">
                    Next <i class="fa fa-arrow-right"></i>
                </button>
                <button type="submit" class="btn-wizard btn-wizard-submit" id="submitBtn" style="display: none;">
                    <i class="fa fa-upload"></i> Upload File
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Modal -->
<div id="loading-modal">
    <div class="modal-content">
        <div id="loading-spinner"></div>
        <h3 id="loading-title">System is processing files</h3>
        <p id="loading-message">Please wait while we upload this file...</p>
    </div>
</div>

<script>
const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
const uploadPromises = {};
let isUploading = false;
var selectedFile = null;
var selectedPoster = null;
var currentStep = 1;
var totalSteps = 3;

document.addEventListener('DOMContentLoaded', function() {
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
            // Create a mock input element to pass to handleFileSelect
            var mockInput = {
                files: [e.dataTransfer.files[0]]
            };
            handleFileSelect(mockInput, 'file');
        }
    });

    // Form submission with loading modal
    const form = document.getElementById('uploadForm');
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
            title.textContent = 'Uploading and processing files';
            message.textContent = 'Please wait while we upload and add this file...';
            
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

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateWizard();
    }
}

function nextStep() {
    if (currentStep < totalSteps) {
        // Validate current step
        if (currentStep === 1) {
            if (!selectedFile) {
                toastr.error('Please select a file to upload', 'Error');
                return;
            }
            
            if (!document.getElementById('typeSelect').value) {
                toastr.error('Please select a file type', 'Error');
                return;
            }
        }
        
        currentStep++;
        updateWizard();
        
        // Show summary when on step 3
        if (currentStep === 3) {
            showSummary();
        }
    }
}

function updateWizard() {
    // Update progress bar
    const progress = document.getElementById('wizardProgress');
    progress.className = `wizard-progress active-step-${currentStep}`;
    
    // Update steps
    const steps = document.querySelectorAll('.wizard-step');
    steps.forEach(step => {
        const stepNumber = parseInt(step.dataset.step);
        step.classList.remove('active', 'completed');
        
        if (stepNumber < currentStep) {
            step.classList.add('completed');
        } else if (stepNumber === currentStep) {
            step.classList.add('active');
        }
    });
    
    // Update content
    const contents = document.querySelectorAll('.wizard-step-content');
    contents.forEach(content => {
        const stepNumber = parseInt(content.dataset.step);
        content.classList.remove('active');
        
        if (stepNumber === currentStep) {
            content.classList.add('active');
        }
    });
    
    // Update buttons
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    prevBtn.disabled = currentStep === 1;
    nextBtn.style.display = currentStep === totalSteps ? 'none' : 'block';
    submitBtn.style.display = currentStep === totalSteps ? 'block' : 'none';
    
    nextBtn.disabled = currentStep === 1 && !selectedFile;
}

function showSummary() {
    // File info
    if (selectedFile) {
        document.getElementById('file-summary').style.display = 'block';
        document.getElementById('file-summary-name').textContent = selectedFile.name;
        document.getElementById('file-summary-size').textContent = formatFileSize(selectedFile.size);
        
        const fileType = detectFileType(selectedFile);
        const icon = fileType === 'video' ? 'fa-file-video-o' : 
                     fileType === 'audio' ? 'fa-file-audio-o' : 
                     fileType === 'image' ? 'fa-file-image-o' : 'fa-file';
        document.getElementById('file-summary-icon').className = `fa ${icon}`;
    }
    
    // Poster info
    if (selectedPoster) {
        document.getElementById('poster-summary').style.display = 'block';
        document.getElementById('poster-summary-name').textContent = selectedPoster.name;
        document.getElementById('poster-summary-size').textContent = formatFileSize(selectedPoster.size);
    }
    
    // File type
    const typeSelect = document.getElementById('typeSelect');
    if (typeSelect.value) {
        document.getElementById('type-summary').style.display = 'block';
        document.getElementById('type-summary-text').textContent = typeSelect.options[typeSelect.selectedIndex].text;
    }
    
    // Additional info
    const topicSelect = document.getElementById('generalTopicSelect');
    const positionSelect = document.getElementById('positionSelect');
    
    if (topicSelect.value || positionSelect.value) {
        document.getElementById('additional-info-summary').style.display = 'block';
        
        if (topicSelect.value) {
            document.getElementById('topic-summary').textContent = `Topic: ${topicSelect.options[topicSelect.selectedIndex].text}`;
        }
        
        if (positionSelect.value) {
            document.getElementById('position-summary').textContent = `Position: ${positionSelect.options[positionSelect.selectedIndex].text}`;
        }
    }
}

async function handleFileSelect(input, type) {
    const file = input.files[0];
    if (!file) return;
    
    const progressContainer = document.getElementById(`${type}-progress-container`);
    const fileNameElement = document.getElementById(`${type}-file-name`);
    const progressText = document.getElementById(`${type}-progress-text`);
    const progressBar = document.getElementById(`${type}-progress-bar`);
    
    fileNameElement.textContent = file.name;
    progressContainer.style.display = 'block';
    progressText.textContent = '0%';
    progressBar.style.width = '0%';
    
    // For poster preview
    if (type === 'poster') {
        selectedPoster = file;
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('posterPreview').src = e.target.result;
            document.getElementById('selectedPoster').style.display = 'block';
        };
        reader.readAsDataURL(selectedPoster);
    } else {
        selectedFile = file;
        // Auto-detect file type
        var fileType = detectFileType(file);
        document.getElementById('typeSelect').value = fileType;
        // Enable next button
        document.getElementById('nextBtn').disabled = false;
    }
    
    // Create and store upload promise
    uploadPromises[type] = uploadFile(file, type, (progress) => {
        progressText.textContent = `${Math.round(progress)}%`;
        progressBar.style.width = `${progress}%`;
    });
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
    document.getElementById(`${type}_file_path`).value = data.filePath;
}

function generateFileId(file) {
    return `${file.name}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
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
    
    // Validate type against allowed values
    const allowedTypes = ['video', 'audio', 'book', 'paper', 'document', 'image', 'other'];
    return allowedTypes.includes(type) ? type : 'other';
}
</script>

<div style="margin-top: 30px; text-align: center;">
    <a href="{{ url('learning/general-uploads') }}" style="color: var(--primary-color); text-decoration: none;">
        <i class="fa fa-arrow-left"></i> Back to General Uploads
    </a>
</div>
@endsection
