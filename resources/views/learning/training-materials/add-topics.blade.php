@extends('layouts.learning')

@section('title', 'Add Topics - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => 'Add Course', 'url' => url('learning/training-materials/create')],
    ['label' => 'Add Topics', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Add Topics to "{{ $material->title }}"</h1>
    <p>Add topics to your course. Each topic can have multiple resources including videos, audio, PDFs, PPTs, or documents.</p>
</div>

<div style="background: white; border-radius: 12px; padding: 30px; box-shadow: var(--shadow); max-width: 1200px; margin: 0 auto;">
    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 30px;">
        <!-- Existing Topics Sidebar -->
        @if($topics->count() > 0)
        <div style="background: var(--light-bg); border-radius: 8px; padding: 20px; border: 1px solid var(--border-color); height: fit-content; position: sticky; top: 20px;">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">Existing Topics ({{ $topics->count() }})</h3>
            <div>
                @foreach($topics as $topic)
                <div style="display: flex; align-items: flex-start; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                    <div style="display: flex; flex-direction: column; flex: 1;">
                        <span style="color: var(--text-primary); font-size: 14px; font-weight: 500;">{{ $loop->iteration }}. {{ $topic->topic_name }}</span>
                        @if($topic->duration)
                        <span style="color: var(--text-secondary); font-size: 11px; margin-top: 2px;">{{ $topic->duration }} min</span>
                        @endif
                        @if($topic->file_name)
                        <span style="color: var(--text-secondary); font-size: 11px; margin-top: 2px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $topic->file_name }}</span>
                        @endif
                        <!-- Resource icons -->
                        <div style="display: flex; gap: 4px; margin-top: 4px;">
                            @if($topic->video_file_path)
                            <span style="font-size: 10px; background: rgba(255, 107, 107, 0.1); color: #ff6b6b; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-video-camera"></i>
                            </span>
                            @endif
                            @if($topic->audio_file_path)
                            <span style="font-size: 10px; background: rgba(80, 200, 120, 0.1); color: #50c878; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-headphones"></i>
                            </span>
                            @endif
                            @if($topic->pdf_file_path)
                            <span style="font-size: 10px; background: rgba(74, 144, 226, 0.1); color: #4a90e2; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-pdf-o"></i>
                            </span>
                            @endif
                            @if($topic->ppt_file_path)
                            <span style="font-size: 10px; background: rgba(247, 183, 51, 0.1); color: #f7b733; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-powerpoint-o"></i>
                            </span>
                            @endif
                            @if($topic->document_file_path)
                            <span style="font-size: 10px; background: rgba(149, 165, 166, 0.1); color: #95a5a6; padding: 2px 6px; border-radius: 3px;">
                                <i class="fa fa-file-text-o"></i>
                            </span>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('learning.training-materials.remove-topic', ['materialId' => $material->id, 'topicId' => $topic->id]) }}" 
                       style="color: var(--accent-color); text-decoration: none; font-size: 12px; margin-left: 8px; flex-shrink: 0;"
                       onclick="return confirm('Are you sure you want to remove this topic?')">
                        <i class="fa fa-trash"></i>
                    </a>
                </div>
                @endforeach
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

            <!-- Add New Topic Form -->
            <div style="margin-bottom: 30px;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px; color: var(--text-primary);">Add New Topic</h3>
        <form action="{{ route('learning.training-materials.store-topic', ['materialId' => $material->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px; margin-bottom: 15px;">
                <!-- Topic Name -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Topic Name *</label>
                    <input type="text" name="topic_name" required style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="Enter topic name">
                </div>
                
                <!-- Duration -->
                <div>
                    <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">Duration (min)</label>
                    <input type="number" name="topic_duration" min="1" style="width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; font-size: 13px;" placeholder="e.g., 15">
                </div>
            </div>
            
            <!-- File Uploads -->
            <div style="margin-top: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                    Video File
                </label>
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                    <i class="fa fa-video-camera" style="font-size: 16px; color: #ff6b6b;"></i>
                    <input type="file" name="video_topic_file" 
                        style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                        accept="video/*">
                </div>
                <small style="color: var(--text-secondary); font-size: 11px;">
                    Upload a video file (MP4, MOV, etc.)
                </small>
            </div>

            <div style="margin-top: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                    Audio File
                </label>
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                    <i class="fa fa-headphones" style="font-size: 16px; color: #50c878;"></i>
                    <input type="file" name="audio_topic_file" 
                        style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                        accept="audio/*">
                </div>
                <small style="color: var(--text-secondary); font-size: 11px;">
                    Upload an audio file (MP3, WAV, etc.)
                </small>
            </div>

            <div style="margin-top: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                    PDF File
                </label>
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                    <i class="fa fa-file-pdf-o" style="font-size: 16px; color: #4a90e2;"></i>
                    <input type="file" name="pdf_topic_file" 
                        style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                        accept="application/pdf">
                </div>
                <small style="color: var(--text-secondary); font-size: 11px;">
                    Upload a PDF document
                </small>
            </div>

            <div style="margin-top: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                    PPT File
                </label>
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                    <i class="fa fa-file-powerpoint-o" style="font-size: 16px; color: #f7b733;"></i>
                    <input type="file" name="ppt_topic_file" 
                        style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                        accept="application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation">
                </div>
                <small style="color: var(--text-secondary); font-size: 11px;">
                    Upload a PowerPoint presentation
                </small>
            </div>

            <div style="margin-top: 15px;">
                <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                    Document File
                </label>
                <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                    <i class="fa fa-file-text-o" style="font-size: 16px; color: #95a5a6;"></i>
                    <input type="file" name="document_topic_file" 
                        style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                        accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain">
                </div>
                <small style="color: var(--text-secondary); font-size: 11px;">
                    Upload a document file (DOC, DOCX, TXT, etc.)
                </small>
            </div>

            <!-- Form Navigation -->
            <div style="display: flex; gap: 15px; justify-content: space-between; margin-top: 24px;">
                <a href="{{ route('learning.training-materials.index') }}" style="padding: 12px 24px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600;">
                    <i class="fa fa-arrow-left" style="margin-right: 8px;"></i> Finish
                </a>
                <button type="submit" style="padding: 12px 30px; background: var(--secondary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                    <i class="fa fa-plus"></i> Add Topic
                </button>
            </div>
        </form>
            </div>

            <!-- Course Management Links -->
            <div style="display: flex; gap: 15px; justify-content: center;">
                <a href="{{ route('learning.training-materials.edit', ['id' => $material->id]) }}" style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    <i class="fa fa-edit"></i> Edit Course Info
                </a>
                <a href="{{ route('learning.training-materials.topics', ['materialId' => $material->id]) }}" style="padding: 10px 20px; background: white; color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 14px;">
                    <i class="fa fa-list"></i> Manage Topics & Quizzes
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom Styles for this page */
.page-header h1 {
    font-size: 28px;
    margin-bottom: 8px;
}

.page-header p {
    font-size: 14px;
    color: var(--text-secondary);
}

.existing-topics {
    margin-bottom: 30px;
}

.existing-topics h3 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
    color: var(--text-primary);
}

.topic-item {
    background: var(--light-bg);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 15px;
    border: 1px solid var(--border-color);
}

.topic-item h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 15px 0;
}

.topic-item .topic-info {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.topic-item .topic-details {
    flex: 1;
}

.topic-item .topic-actions {
    display: flex;
    gap: 10px;
}

.topic-item .topic-actions a {
    padding: 6px 12px;
    border-radius: 4px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}

.topic-item .topic-actions .remove-btn {
    background: var(--accent-color);
    color: white;
}

.topic-item .topic-actions .remove-btn:hover {
    background: #e74c3c;
}
</style>

<script>
// No custom JavaScript needed for this page
</script>
@endsection