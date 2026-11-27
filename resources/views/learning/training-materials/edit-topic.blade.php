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

                        @php
                            $videoFiles = $topic->video_file_path ? array_filter(explode(',', $topic->video_file_path)) : [];
                            $audioFiles = $topic->audio_file_path ? array_filter(explode(',', $topic->audio_file_path)) : [];
                            $pdfFiles = $topic->pdf_file_path ? array_filter(explode(',', $topic->pdf_file_path)) : [];
                            $pptFiles = $topic->ppt_file_path ? array_filter(explode(',', $topic->ppt_file_path)) : [];
                            $documentFiles = $topic->document_file_path ? array_filter(explode(',', $topic->document_file_path)) : [];
                            $sortFn = function($a, $b) {
                                $aName = basename($a);
                                $bName = basename($b);
                                preg_match('/\d+/', $aName, $aMatch);
                                preg_match('/\d+/', $bName, $bMatch);
                                $aNum = isset($aMatch[0]) ? (int)$aMatch[0] : PHP_INT_MAX;
                                $bNum = isset($bMatch[0]) ? (int)$bMatch[0] : PHP_INT_MAX;
                                return $aNum - $bNum;
                            };
                            usort($videoFiles, $sortFn);
                            usort($audioFiles, $sortFn);
                            usort($pdfFiles, $sortFn);
                            usort($pptFiles, $sortFn);
                            usort($documentFiles, $sortFn);
                            $totalFiles = count($videoFiles) + count($audioFiles) + count($pdfFiles) + count($pptFiles) + count($documentFiles);
                            $firstNonEmptyGroup = null;
                            foreach (['video' => $videoFiles, 'audio' => $audioFiles, 'pdf' => $pdfFiles, 'ppt' => $pptFiles, 'document' => $documentFiles] as $group => $files) {
                                if (count($files) > 0) {
                                    $firstNonEmptyGroup = $group;
                                    break;
                                }
                            }
                        @endphp

                        @if($totalFiles > 0)
                        <!-- Category Tabs -->
                        <div style="display: flex; gap: 8px; overflow-x: auto; padding-bottom: 10px; border-bottom: 1px solid var(--border-color); margin-bottom: 12px;">
                            <button class="resource-tab {{ count($videoFiles) ? 'active' : '' }}" onclick="showFileGroup('video', this)" data-group="video" style="{{ count($videoFiles) ? '' : 'opacity:0.5;cursor:not-allowed;' }}">
                                <i class="fa fa-video-camera"></i> Video <span class="resource-tab-count">({{ count($videoFiles) }})</span>
                            </button>
                            <button class="resource-tab {{ count($audioFiles) ? 'active' : '' }}" onclick="showFileGroup('audio', this)" data-group="audio" style="{{ count($audioFiles) ? '' : 'opacity:0.5;cursor:not-allowed;' }}">
                                <i class="fa fa-headphones"></i> Audio <span class="resource-tab-count">({{ count($audioFiles) }})</span>
                            </button>
                            <button class="resource-tab {{ count($pdfFiles) ? 'active' : '' }}" onclick="showFileGroup('pdf', this)" data-group="pdf" style="{{ count($pdfFiles) ? '' : 'opacity:0.5;cursor:not-allowed;' }}">
                                <i class="fa fa-file-pdf-o"></i> PDF <span class="resource-tab-count">({{ count($pdfFiles) }})</span>
                            </button>
                            <button class="resource-tab {{ count($pptFiles) ? 'active' : '' }}" onclick="showFileGroup('ppt', this)" data-group="ppt" style="{{ count($pptFiles) ? '' : 'opacity:0.5;cursor:not-allowed;' }}">
                                <i class="fa fa-file-powerpoint-o"></i> PowerPoint <span class="resource-tab-count">({{ count($pptFiles) }})</span>
                            </button>
                            <button class="resource-tab {{ count($documentFiles) ? 'active' : '' }}" onclick="showFileGroup('document', this)" data-group="document" style="{{ count($documentFiles) ? '' : 'opacity:0.5;cursor:not-allowed;' }}">
                                <i class="fa fa-file-text-o"></i> Word Document <span class="resource-tab-count">({{ count($documentFiles) }})</span>
                            </button>
                        </div>

                        <!-- File Groups -->
                        <div style="display: flex; flex-direction: column; gap: 10px;">
                            <!-- Video -->
                            <div class="file-group-panel {{ count($videoFiles) ? '' : 'resource-group-empty' }}" id="file-group-video" style="{{ ($firstNonEmptyGroup === 'video') ? 'display:block' : 'display:none' }};">
                                @foreach($videoFiles as $vf)
                                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <i class="fa fa-video-camera" style="color: #ff6b6b; margin-right: 6px;"></i>
                                        <span style="font-size: 12px; color: var(--text-primary);">{{ e(basename($vf)) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" style="padding: 4px 10px; font-size: 11px; margin-left: auto; color: #dc3545; border-color: #dc3545;" data-delete-url="{{ route('learning.training-materials.remove-file', ['topicId' => $topic->id, 'fileType' => 'video_file_path', 'fileIndex' => $loop->index]) }}" onclick="if(confirm('Are you sure you want to remove this file?')){ deleteFile(this); }">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <!-- Audio -->
                            <div class="file-group-panel {{ count($audioFiles) ? '' : 'resource-group-empty' }}" id="file-group-audio" style="{{ ($firstNonEmptyGroup === 'audio') ? 'display:block' : 'display:none' }};">
                                @foreach($audioFiles as $af)
                                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <i class="fa fa-headphones" style="color: #50c878; margin-right: 6px;"></i>
                                        <span style="font-size: 12px; color: var(--text-primary);">{{ e(basename($af)) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" style="padding: 4px 10px; font-size: 11px; margin-left: auto; color: #dc3545; border-color: #dc3545;" data-delete-url="{{ route('learning.training-materials.remove-file', ['topicId' => $topic->id, 'fileType' => 'audio_file_path', 'fileIndex' => $loop->index]) }}" onclick="if(confirm('Are you sure you want to remove this file?')){ deleteFile(this); }">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <!-- PDF -->
                            <div class="file-group-panel {{ count($pdfFiles) ? '' : 'resource-group-empty' }}" id="file-group-pdf" style="{{ ($firstNonEmptyGroup === 'pdf') ? 'display:block' : 'display:none' }};">
                                @foreach($pdfFiles as $pf)
                                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <i class="fa fa-file-pdf-o" style="color: #4a90e2; margin-right: 6px;"></i>
                                        <span style="font-size: 12px; color: var(--text-primary);">{{ e(basename($pf)) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" style="padding: 4px 10px; font-size: 11px; margin-left: auto; color: #dc3545; border-color: #dc3545;" data-delete-url="{{ route('learning.training-materials.remove-file', ['topicId' => $topic->id, 'fileType' => 'pdf_file_path', 'fileIndex' => $loop->index]) }}" onclick="if(confirm('Are you sure you want to remove this file?')){ deleteFile(this); }">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <!-- PowerPoint -->
                            <div class="file-group-panel {{ count($pptFiles) ? '' : 'resource-group-empty' }}" id="file-group-ppt" style="{{ ($firstNonEmptyGroup === 'ppt') ? 'display:block' : 'display:none' }};">
                                @foreach($pptFiles as $ptf)
                                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <i class="fa fa-file-powerpoint-o" style="color: #f7b733; margin-right: 6px;"></i>
                                        <span style="font-size: 12px; color: var(--text-primary);">{{ e(basename($ptf)) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" style="padding: 4px 10px; font-size: 11px; margin-left: auto; color: #dc3545; border-color: #dc3545;" data-delete-url="{{ route('learning.training-materials.remove-file', ['topicId' => $topic->id, 'fileType' => 'ppt_file_path', 'fileIndex' => $loop->index]) }}" onclick="if(confirm('Are you sure you want to remove this file?')){ deleteFile(this); }">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <!-- Word Document -->
                            <div class="file-group-panel {{ count($documentFiles) ? '' : 'resource-group-empty' }}" id="file-group-document" style="{{ ($firstNonEmptyGroup === 'document') ? 'display:block' : 'display:none' }};">
                                @foreach($documentFiles as $df)
                                <div style="display: flex; align-items: center; gap: 8px; background: white; padding: 8px 12px; border-radius: 6px; border: 1px solid var(--border-color);">
                                    <div style="flex: 1;">
                                        <i class="fa fa-file-text-o" style="color: #95a5a6; margin-right: 6px;"></i>
                                        <span style="font-size: 12px; color: var(--text-primary);">{{ e(basename($df)) }}</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-default" style="padding: 4px 10px; font-size: 11px; margin-left: auto; color: #dc3545; border-color: #dc3545;" data-delete-url="{{ route('learning.training-materials.remove-file', ['topicId' => $topic->id, 'fileType' => 'document_file_path', 'fileIndex' => $loop->index]) }}" onclick="if(confirm('Are you sure you want to remove this file?')){ deleteFile(this); }">
                                        <i class="fa fa-trash"></i> Delete
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <p style="color: var(--text-secondary); font-size: 12px;">No files attached</p>
                        @endif

                        <p style="font-size: 11px; color: var(--text-secondary); margin-top: 8px; margin-bottom: 0;">
                            <i class="fa fa-info-circle"></i> To add more files, upload below. Leave empty to keep existing files.
                        </p>
                    </div>
                    
                    <!-- File Uploads with Custom Chunked Upload -->
                    <div style="margin-top: 15px;">
                        <label style="display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; color: var(--text-primary);">
                            Video File (add files)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-video-camera" style="font-size: 16px; color: #ff6b6b;"></i>
                            <input type="file" id="video-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="video/*" onchange="handleFileSelect(this, 'video')" multiple>
                        </div>
                        <div id="video-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: block; margin-bottom: 4px;">
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
                            Audio File (add files)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-headphones" style="font-size: 16px; color: #50c878;"></i>
                            <input type="file" id="audio-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="audio/*" onchange="handleFileSelect(this, 'audio')" multiple>
                        </div>
                        <div id="audio-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: block; margin-bottom: 4px;">
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
                            PDF File (add files)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-pdf-o" style="font-size: 16px; color: #4a90e2;"></i>
                            <input type="file" id="pdf-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/pdf" onchange="handleFileSelect(this, 'pdf')" multiple>
                        </div>
                        <div id="pdf-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: block; margin-bottom: 4px;">
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
                            PPT File (add files)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-powerpoint-o" style="font-size: 16px; color: #f7b733;"></i>
                            <input type="file" id="ppt-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" onchange="handleFileSelect(this, 'ppt')" multiple>
                        </div>
                        <div id="ppt-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: block; margin-bottom: 4px;">
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
                            Document File (add files)
                        </label>
                        <div style="display: flex; align-items: center; gap: 8px; border: 1px solid var(--border-color); border-radius: 6px; padding: 8px 12px; background: white;">
                            <i class="fa fa-file-text-o" style="font-size: 16px; color: #95a5a6;"></i>
                            <input type="file" id="document-file-input" 
                                style="flex: 1; padding: 6px 0; border: none; font-size: 13px; background: transparent;"
                                accept="application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,text/plain" onchange="handleFileSelect(this, 'document')" multiple>
                        </div>
                        <div id="document-progress-container" style="margin-top: 8px; display: none;">
                            <div style="display: block; margin-bottom: 4px;">
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
        form.addEventListener('submit', function(e) {
            if (isUploading) {
                e.preventDefault();
                return;
            }

            isUploading = true;
            const submitButtons = form.querySelectorAll('button[type="submit"]');
            submitButtons.forEach(button => {
                button.disabled = true;
                button.style.opacity = '0.7';
            });

            Object.values(uploadPromises).filter(Boolean).forEach(promise => {
                promise.catch(error => console.error('Background upload error:', error));
            });
        });
    }
});

async function handleFileSelect(input, type) {
    const files = Array.from(input.files || []);
    if (!files.length) return;

    const progressContainer = document.getElementById(`${type}-progress-container`);
    const fileNameElement = document.getElementById(`${type}-file-name`);
    const progressText = document.getElementById(`${type}-progress-text`);
    const progressBar = document.getElementById(`${type}-progress-bar`);

    // single file keeps original container
    if (files.length === 1) {
        const file = files[0];
        fileNameElement.textContent = file.name;
        progressContainer.style.display = 'block';
        progressText.textContent = '0%';
        progressBar.style.width = '0%';
        uploadPromises[type] = uploadFile(file, type, (progress) => {
            progressText.textContent = `${Math.round(progress)}%`;
            progressBar.style.width = `${progress}%`;
        });
        return;
    }

    // multiple: create a progress container per file
    files.forEach(function(file, idx) {
        const key = `${type}_${idx}`;
        const container = document.createElement('div');
        container.className = 'progress-container';
        container.style.display = 'block';
        container.innerHTML = `
            <div style="display:block;margin-bottom:4px;">
                <span id="${key}-file-name" style="font-size:12px;color:var(--text-secondary);"></span>
                <span id="${key}-progress-text" style="font-size:12px;color:var(--text-secondary);">0%</span>
            </div>
            <div style="width:100%;height:4px;background:var(--border-color);border-radius:2px;overflow:hidden;">
                <div id="${key}-progress-bar" style="height:100%;background:var(--secondary-color);width:0%;transition:width .3s ease;"></div>
            </div>
        `;
        progressContainer.parentNode.insertBefore(container, progressContainer.nextSibling);
        const fn = document.getElementById(`${key}-file-name`);
        const pt = document.getElementById(`${key}-progress-text`);
        const pb = document.getElementById(`${key}-progress-bar`);
        fn.textContent = file.name;
        uploadPromises[key] = uploadFile(file, type, (progress) => {
            pt.textContent = `${Math.round(progress)}%`;
            pb.style.width = `${progress}%`;
        });
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
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        });

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || `Failed to upload chunk ${i + 1}/${totalChunks}`);
        }

        const progress = ((i + 1) / totalChunks) * 100;
        onProgress(progress);
    }

    // Merge chunks and get file path
    const mergeResponse = await fetch('{{ url("learning/training-materials/merge-chunks") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            filename: file.name,
            fileId: fileId,
            type: type,
            totalChunks: totalChunks
        })
    });

    const data = await mergeResponse.json().catch(() => ({}));
    if (!mergeResponse.ok || data.status === 'error') {
        throw new Error(data.message || 'Failed to merge chunks');
    }
    onProgress(100);

    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = `${type}_file_path[]`;
    hidden.value = data.filePath;
    document.querySelector('form').appendChild(hidden);
}

function generateFileId(file) {
    return `${file.name}_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
}

// Toggle between file category groups
function showFileGroup(type, btn) {
    // Hide all panels
    document.querySelectorAll('.file-group-panel').forEach(function(panel){
        panel.style.display = 'none';
    });
    // Show selected panel
    var panel = document.getElementById('file-group-' + type);
    if (panel) panel.style.display = 'block';
    // Update active tab styling
    document.querySelectorAll('.resource-tab').forEach(function(b){
        b.style.background = '';
        b.style.color = '';
        b.style.fontWeight = '600';
    });
    if (btn) {
        btn.style.background = 'var(--primary-color)';
        btn.style.color = 'white';
        btn.style.fontWeight = '700';
    }
}

// Delete a file from a topic's comma-separated list
function deleteFile(btn) {
    var url = btn.getAttribute('data-delete-url');
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            // Remove the row from DOM
            var row = btn.closest('div[style*="display: flex; align-items: center"]');
            if (row) {
                row.remove();
            }
            // If no files remain in this group, show empty state
            var panel = btn.closest('.file-group-panel');
            if (panel && panel.querySelectorAll('div[style*="display: flex; align-items: center"]').length === 0) {
                panel.innerHTML = '<div style="padding: 20px; text-align: center; color: var(--text-secondary); font-size: 13px;">No files in this group.</div>';
            }
        } else {
            alert(data.error || 'Failed to remove file.');
        }
    })
    .catch(function() {
        alert('An error occurred while removing the file.');
    });
}

// Activate first available file group on page load
document.addEventListener('DOMContentLoaded', function() {
    var firstTab = document.querySelector('.resource-tab:not([style*="opacity:0.5"])');
    if (firstTab) {
        showFileGroup(firstTab.dataset.group, firstTab);
    }
});
</script>
@endsection
