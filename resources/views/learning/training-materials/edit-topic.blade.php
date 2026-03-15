@extends('layouts.learning')

@section('title', 'Edit Topic - ' . $topic->topic_name . ' - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $material->title, 'url' => url('learning/training-materials/' . $material->id)],
    ['label' => 'Manage Topics & Quizzes', 'url' => route('learning.training-materials.topics', ['materialId' => $material->id])],
    ['label' => 'Edit Topic', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<!-- Uppy CSS -->
<link rel="stylesheet" href="https://releases.transloadit.com/uppy/v3.22.2/uppy.min.css">

<div class="page-header">
    <h1>Edit Topic: {{ $topic->topic_name }}</h1>
    <p>Update the topic details and resources for "{{ $material->title }}"</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); max-width: 1200px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
        <!-- Existing Topics Sidebar -->
        @if($topics->count() > 0)
        <div style="background: var(--light-bg); border-radius: 8px; padding: 20px; border: 1px solid var(--border-color); height: fit-content; position: sticky; top: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">All Topics ({{ $topics->count() }})</h3>
            <div>
                @foreach($topics as $t)
                <div style="display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color); {{ $t->id == $topic->id ? 'background: rgba(74, 144, 226, 0.1); border-radius: 4px; margin: 0 -8px; padding: 12px 8px;' : '' }}">
                    <div style="display: flex; flex-direction: column; flex: 1;">
                        <span style="color: {{ $t->id == $topic->id ? 'var(--primary-color)' : 'var(--text-primary)' }}; font-size: 14px; font-weight: {{ $t->id == $topic->id ? '600' : '400' }};">
                            {{ $loop->iteration }}. {{ $t->topic_name }}
                            @if($t->id == $topic->id)
                            <span style="background: var(--primary-color); color: white; font-size: 10px; padding: 2px 6px; border-radius: 3px; margin-left: 4px;">EDITING</span>
                            @endif
                        </span>
                        @if($t->duration)
                        <span style="color: var(--text-secondary); font-size: 11px; margin-top: 2px;">{{ $t->duration }} min</span>
                        @endif
                        <!-- Resource icons -->
                        <div style="display: flex; gap: 4px; margin-top: 4px;">
                            @if($t->video_file_path)
                            <span style="font-size: 10px; background: rgba(255, 107, 107, 0.1); color: #ff6b6b; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-video-camera"></i>
                            </span>
                            @endif
                            @if($t->audio_file_path)
                            <span style="font-size: 10px; background: rgba(80, 200, 120, 0.1); color: #50c878; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-headphones"></i>
                            </span>
                            @endif
                            @if($t->pdf_file_path)
                            <span style="font-size: 10px; background: rgba(74, 144, 226, 0.1); color: #4a90e2; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-pdf-o"></i>
                            </span>
                            @endif
                            @if($t->ppt_file_path)
                            <span style="font-size: 10px; background: rgba(247, 183, 51, 0.1); color: #f7b733; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-powerpoint-o"></i>
                            </span>
                            @endif
                            @if($t->document_file_path)
                            <span style="font-size: 10px; background: rgba(149, 165, 166, 0.1); color: #95a5a6; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border-color);">
                <a href="{{ route('learning.training-materials.topics', ['materialId' => $material->id]) }}" style="display: block; text-align: center; padding: 10px; background: var(--primary-color); color: white; border-radius: 6px; text-decoration: none; font-weight: 500;">
                    <i class="fa fa-list"></i> Back to Topics
                </a>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div>
            <!-- Course Info Summary -->
            <div style="background: var(--light-bg); border-radius: 8px; padding: 20px; margin-bottom: 30px; border: 1px solid var(--border-color);">
                <h4 style="font-size: 16px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Course Information</h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">Material Type</label>
                        <p style="font-size: 14px; color: var(--text-primary); margin: 4px 0;">
                            @if($material->material_type == 'document')
                                <i class="fa fa-file-pdf-o" style="color: #4a90e2;"></i> Document
                            @elseif($material->material_type == 'video')
                                <i class="fa fa-video-camera" style="color: #ff6b6b;"></i> Video
                            @elseif($material->material_type == 'audio')
                                <i class="fa fa-headphones" style="color: #50c878;"></i> Audio
                            @endif
                        </p>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">Department</label>
                        <p style="font-size: 14px; color: var(--text-primary); margin: 4px 0;">{{ $material->department }}</p>
                    </div>
                    <div>
                        <label style="font-size: 12px; font-weight: 600; color: var(--text-secondary);">Target Audience</label>
                        <p style="font-size: 14px; color: var(--text-primary); margin: 4px 0;">
                            @if($material->target_role == 'all')
                                All Staff
                            @elseif($material->target_role == '1')
                                Admin
                            @elseif($material->target_role == '4')
                                Manager
                            @elseif($material->target_role == '6')
                                Supervisor
                            @elseif($material->target_role == '3')
                                Intern
                            @elseif($material->target_role == '5')
                                Staff
                            @elseif($material->target_role == '10')
                                Client
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Edit Topic Form -->
            <div style="margin-bottom: 30px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">Edit Topic</h3>
                <form action="{{ route('learning.training-materials.update-topic', ['topicId' => $topic->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <!-- Topic Name -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Topic Name *</label>
                            <input type="text" name="topic_name" required value="{{ old('topic_name', $topic->topic_name) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="Enter topic name">
                        </div>
                        
                        <!-- Duration -->
                        <div>
                            <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Duration (min)</label>
                            <input type="number" name="topic_duration" min="1" value="{{ old('topic_duration', $topic->duration) }}" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="e.g., 15">
                        </div>
                    </div>
                    
                    <!-- Current Files Info -->
                    <div style="background: var(--light-bg); border-radius: 8px; padding: 16px; margin-bottom: 20px; border: 1px solid var(--border-color);">
                        <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Current Files</h4>
                        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                            @if($topic->video_file_path)
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(255, 107, 107, 0.1); padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #ff6b6b;">
                                <i class="fa fa-video-camera"></i>
                                <span>Video</span>
                            </div>
                            @endif
                            @if($topic->audio_file_path)
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(80, 200, 120, 0.1); padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #50c878;">
                                <i class="fa fa-headphones"></i>
                                <span>Audio</span>
                            </div>
                            @endif
                            @if($topic->pdf_file_path)
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(74, 144, 226, 0.1); padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #4a90e2;">
                                <i class="fa fa-file-pdf-o"></i>
                                <span>PDF</span>
                            </div>
                            @endif
                            @if($topic->ppt_file_path)
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(247, 183, 51, 0.1); padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #f7b733;">
                                <i class="fa fa-file-powerpoint-o"></i>
                                <span>PPT</span>
                            </div>
                            @endif
                            @if($topic->document_file_path)
                            <div style="display: flex; align-items: center; gap: 6px; background: rgba(149, 165, 166, 0.1); padding: 6px 12px; border-radius: 4px; font-size: 12px; color: #95a5a6;">
                                <i class="fa fa-file-text-o"></i>
                                <span>Document</span>
                            </div>
                            @endif
                            @if(!$topic->video_file_path && !$topic->audio_file_path && !$topic->pdf_file_path && !$topic->ppt_file_path && !$topic->document_file_path)
                            <span style="color: var(--text-secondary); font-size: 12px;">No files attached</span>
                            @endif
                        </div>
                        <p style="font-size: 11px; color: var(--text-secondary); margin-top: 8px; margin-bottom: 0;">
                            <i class="fa fa-info-circle"></i> To replace a file, upload a new one below. Leave empty to keep the existing file.
                        </p>
                    </div>
                    
                    <!-- File Uploads with Custom Chunked Upload -->
                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            Video File (replace existing)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-video-camera" style="font-size: 16px; color: #ff6b6b;"></i>
                            <input type="file" id="video-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="video/*" onchange="handleFileSelect(this, 'video')">
                        </div>
                        <div id="video-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span id="video-file-name" style="font-size: 12px; color: var(--text-secondary);"></span>
                                <span id="video-progress-text" style="font-size: 12px; color: var(--text-secondary);">0%</span>
                            </div>
                            <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                <div id="video-progress-bar" style="height: 100%; background: var(--secondary-color); width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <input type="hidden" id="video_file_path" name="video_file_path">
                        <input type="hidden" id="video_file_name" name="video_file_name">
                        <small style="color: var(--text-secondary); font-size: 11px;">
                            Upload a video file (MP4, MOV, etc.)
                        </small>
                    </div>

                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            Audio File (replace existing)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-headphones" style="font-size: 16px; color: #50c878;"></i>
                            <input type="file" id="audio-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="audio/*" onchange="handleFileSelect(this, 'audio')">
                        </div>
                        <div id="audio-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span id="audio-file-name" style="font-size: 12px; color: var(--text-secondary);"></span>
                                <span id="audio-progress-text" style="font-size: 12px; color: var(--text-secondary);">0%</span>
                            </div>
                            <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                <div id="audio-progress-bar" style="height: 100%; background: var(--secondary-color); width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <input type="hidden" id="audio_file_path" name="audio_file_path">
                        <input type="hidden" id="audio_file_name" name="audio_file_name">
                        <small style="color: var(--text-secondary); font-size: 11px;">
                            Upload an audio file (MP3, WAV, etc.)
                        </small>
                    </div>

                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            PDF File (replace existing)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-pdf-o" style="font-size: 16px; color: #4a90e2;"></i>
                            <input type="file" id="pdf-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/pdf" onchange="handleFileSelect(this, 'pdf')">
                        </div>
                        <div id="pdf-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span id="pdf-file-name" style="font-size: 12px; color: var(--text-secondary);"></span>
                                <span id="pdf-progress-text" style="font-size: 12px; color: var(--text-secondary);">0%</span>
                            </div>
                            <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                <div id="pdf-progress-bar" style="height: 100%; background: var(--secondary-color); width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <input type="hidden" id="pdf_file_path" name="pdf_file_path">
                        <input type="hidden" id="pdf_file_name" name="pdf_file_name">
                        <small style="color: var(--text-secondary); font-size: 11px;">
                            Upload a PDF document
                        </small>
                    </div>

                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            PPT File (replace existing)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-powerpoint-o" style="font-size: 16px; color: #f7b733;"></i>
                            <input type="file" id="ppt-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" onchange="handleFileSelect(this, 'ppt')">
                        </div>
                        <div id="ppt-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span id="ppt-file-name" style="font-size: 12px; color: var(--text-secondary);"></span>
                                <span id="ppt-progress-text" style="font-size: 12px; color: var(--text-secondary);">0%</span>
                            </div>
                            <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                <div id="ppt-progress-bar" style="height: 100%; background: var(--secondary-color); width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <input type="hidden" id="ppt_file_path" name="ppt_file_path">
                        <input type="hidden" id="ppt_file_name" name="ppt_file_name">
                        <small style="color: var(--text-secondary); font-size: 11px;">
                            Upload a PowerPoint presentation
                        </small>
                    </div>

                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            Document File (replace existing)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-text-o" style="font-size: 16px; color: #95a5a6;"></i>
                            <input type="file" id="document-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" onchange="handleFileSelect(this, 'document')">
                        </div>
                        <div id="document-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                <span id="document-file-name" style="font-size: 12px; color: var(--text-secondary);"></span>
                                <span id="document-progress-text" style="font-size: 12px; color: var(--text-secondary);">0%</span>
                            </div>
                            <div style="width: 100%; height: 4px; background: var(--border-color); border-radius: 2px; overflow: hidden;">
                                <div id="document-progress-bar" style="height: 100%; background: var(--secondary-color); width: 0%; transition: width 0.3s ease;"></div>
                            </div>
                        </div>
                        <input type="hidden" id="document_file_path" name="document_file_path">
                        <input type="hidden" id="document_file_name" name="document_file_name">
                        <small style="color: var(--text-secondary); font-size: 11px;">
                            Upload a document file (DOC, DOCX, TXT, etc.)
                        </small>
                    </div>

                    <!-- Form Navigation -->
                    <div style="display: flex; gap: 15px; justify-content: space-between; margin-top: 24px;">
                        <a href="{{ route('learning.training-materials.topics', ['materialId' => $material->id]) }}" style="padding: 12px 24px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600;">
                            <i class="fa fa-arrow-left" style="margin-right: 8px;"></i> Cancel
                        </a>
                        <button type="submit" style="padding: 12px 30px; background: var(--secondary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                            <i class="fa fa-save"></i> Update Topic
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
/* Custom Styles for this page */
.page-header h1 {
    font-size: 28px;
    margin-bottom: 8px;
}

.page-header p {
    font-size: 14px;
    color: var(--text-secondary);
}
</style>

<script>
const CHUNK_SIZE = 5 * 1024 * 1024; // 5MB chunks
const uploadPromises = {};
let isUploading = false;

document.addEventListener('DOMContentLoaded', function() {
    // Form submission with loading modal
    const form = document.querySelector('form');
    
    if (form) {
        form.addEventListener('submit', async function(e) {
            // Check if there are any pending uploads
            const pendingUploads = Object.keys(uploadPromises).filter(key => uploadPromises[key] instanceof Promise);
            
            if (pendingUploads.length > 0 && isUploading) {
                e.preventDefault();
                alert('Please wait for file uploads to complete before submitting.');
                return;
            }
        });
    }
});

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
        
        const response = await fetch('{{ url("learning/training-materials/upload-chunk") }}', {
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
    const mergeResponse = await fetch('{{ url("learning/training-materials/merge-chunks") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            filename: file.name,
            fileId: fileId,
            type: type,
            totalChunks: totalChunks
        })
    });
    
    if (!mergeResponse.ok) {
        throw new Error('Failed to merge chunks');
    }
    
    const data = await mergeResponse.json();
    document.getElementById(`${type}_file_path`).value = data.filePath;
    document.getElementById(`${type}_file_name`).value = data.fileName;
}

function generateFileId(file) {
    return `${file.name}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
}
</script>
@endsection
