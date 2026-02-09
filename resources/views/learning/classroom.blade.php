@extends('layouts.learning')

@section('title', 'Classroom: ' . $material->title . ' - Whence Learn')

@section('content')
<style>
/* Full width container - override layout constraints */
.classroom-page {
    margin: -24px;
    min-height: calc(100vh - 112px);
    background: var(--light-bg);
}

.classroom-container {
    display: flex;
    min-height: calc(100vh - 112px);
}

/* Sidebar - collapsible on mobile */
.classroom-sidebar {
    width: 320px;
    min-width: 320px;
    background: white;
    border-right: 1px solid var(--border-color);
    overflow-y: auto;
    height: calc(100vh - 112px);
    position: sticky;
    top: 112px;
}

.classroom-main {
    flex: 1;
    padding: 0;
    overflow-y: auto;
    height: calc(100vh - 112px);
}

/* Header - full width */
.classroom-header {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 30px;
    background: white;
    border-bottom: 1px solid var(--border-color);
    position: sticky;
    top: 0;
    z-index: 10;
}

.classroom-header a {
    color: var(--primary-color);
    font-size: 20px;
}

.classroom-header h1 {
    font-size: 20px;
    font-weight: 600;
    margin: 0;
    flex: 1;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Progress bar in header */
.progress-section {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 30px;
    background: white;
    border-bottom: 1px solid var(--border-color);
}

.progress-info {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 150px;
}

.progress-label {
    font-size: 13px;
    color: var(--text-secondary);
}

.progress-percentage {
    font-size: 14px;
    font-weight: 600;
    color: var(--primary-color);
    min-width: 45px;
    text-align: right;
}

.progress-bar-container {
    flex: 1;
    height: 8px;
    background: var(--light-bg);
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Content area */
.content-area {
    padding: 30px;
}

.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
    max-width: 900px;
    margin: 0 auto;
}

.content-card-header {
    padding: 24px 30px;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.content-card-title {
    font-size: 18px;
    font-weight: 600;
}

.content-card-body {
    padding: 40px 30px;
    text-align: center;
}

.content-icon {
    font-size: 72px;
    color: var(--primary-color);
    margin-bottom: 24px;
}

.content-title {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 16px;
    color: var(--text-primary);
}

.content-description {
    color: var(--text-secondary);
    margin-bottom: 32px;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.content-actions {
    display: flex;
    justify-content: center;
    gap: 16px;
    flex-wrap: wrap;
}

/* Vertical Wizard Styles */
.wizard-container {
    padding: 0;
}

.wizard-phase {
    border-bottom: 1px solid var(--border-color);
}

.wizard-phase:last-child {
    border-bottom: none;
}

.wizard-phase-header {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.wizard-phase-header:hover {
    background: var(--light-bg);
}

.wizard-phase-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--light-bg);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 16px;
    color: var(--primary-color);
}

.wizard-phase-icon.completed {
    background: var(--secondary-color);
    color: white;
}

.wizard-phase-icon.active {
    background: var(--primary-color);
    color: white;
}

.wizard-phase-info {
    flex: 1;
}

.wizard-phase-title {
    font-weight: 600;
    font-size: 14px;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.wizard-phase-description {
    font-size: 11px;
    color: var(--text-secondary);
}

.wizard-phase-toggle {
    font-size: 12px;
    color: var(--text-secondary);
    transition: transform 0.2s ease;
    padding: 4px;
}

.wizard-phase-toggle.expanded {
    transform: rotate(180deg);
}

.wizard-topics {
    padding: 0 16px 16px 68px;
}

.wizard-topic {
    display: flex;
    align-items: center;
    padding: 14px 16px;
    background: var(--light-bg);
    border-radius: 8px;
    margin-bottom: 10px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.wizard-topic:hover {
    background: rgba(74, 144, 226, 0.1);
}

.wizard-topic:last-child {
    margin-bottom: 0;
}

.wizard-topic.active-item {
    background: rgba(74, 144, 226, 0.15);
    border: 1px solid var(--primary-color);
}

.wizard-topic-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    font-size: 12px;
    color: var(--text-secondary);
}

.wizard-topic-icon.completed {
    background: var(--secondary-color);
    color: white;
}

.wizard-topic-icon.active {
    background: var(--primary-color);
    color: white;
}

.wizard-topic-icon.locked {
    background: #e0e0e0;
    color: #999;
}

.wizard-topic-info {
    flex: 1;
    min-width: 0;
}

.wizard-topic-title {
    font-weight: 500;
    font-size: 13px;
    color: var(--text-primary);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.wizard-topic-meta {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 11px;
    color: var(--text-secondary);
}

.wizard-topic-meta span {
    display: flex;
    align-items: center;
    gap: 3px;
}

.wizard-topic-action {
    padding: 5px 10px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    white-space: nowrap;
}

.wizard-topic-action.start {
    background: var(--primary-color);
    color: white;
}

.wizard-topic-action.continue {
    background: var(--secondary-color);
    color: white;
}

.wizard-topic-action.completed {
    background: transparent;
    color: var(--secondary-color);
    border: 1px solid var(--secondary-color);
}

.wizard-topic-action.locked {
    background: #f0f0f0;
    color: #999;
    border: none;
}

/* Mobile sidebar toggle */
.mobile-sidebar-toggle {
    display: none;
    padding: 8px 12px;
    background: var(--light-bg);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
    color: var(--text-primary);
}

/* Responsive */
@media (max-width: 992px) {
    .classroom-page {
        margin: -16px;
        min-height: calc(100vh - 96px);
    }
    
    .classroom-container {
        min-height: calc(100vh - 96px);
        flex-direction: column;
    }
    
    .classroom-sidebar {
        width: 100%;
        min-width: 100%;
        height: auto;
        max-height: 300px;
        position: relative;
        top: 0;
        border-right: none;
        border-bottom: 1px solid var(--border-color);
        display: none;
    }
    
    .classroom-sidebar.show {
        display: block;
    }
    
    .classroom-main {
        height: auto;
        min-height: calc(100vh - 96px);
    }
    
    .classroom-header {
        padding: 16px 20px;
        flex-wrap: wrap;
        gap: 12px;
    }
    
    .classroom-header h1 {
        font-size: 18px;
        order: 1;
        flex: 1;
        min-width: 200px;
    }
    
    .header-actions {
        order: 2;
    }
    
    .mobile-sidebar-toggle {
        display: flex;
        align-items: center;
        gap: 8px;
        order: 0;
    }
    
    .progress-section {
        padding: 12px 20px;
    }
    
    .content-area {
        padding: 20px;
    }
    
    .content-card-body {
        padding: 30px 20px;
    }
    
    .content-icon {
        font-size: 56px;
    }
    
    .content-title {
        font-size: 20px;
    }
    
    .wizard-topics {
        padding: 0 16px 16px 60px;
    }
}

@media (max-width: 576px) {
    .classroom-header {
        padding: 12px 16px;
    }
    
    .progress-section {
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
    }
    
    .progress-info {
        min-width: auto;
        justify-content: space-between;
    }
    
    .content-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .btn-lg {
        width: 100%;
    }
    
    .wizard-topic {
        flex-wrap: wrap;
        gap: 8px;
    }
    
    .wizard-topic-action {
        width: 100%;
        text-align: center;
    }
}
</style>

<div class="classroom-page">
    <div class="classroom-container">
        <!-- Sidebar with Vertical Wizard -->
        <div class="classroom-sidebar" id="classroom-sidebar">
            <!-- Course Progress Header -->
            <div style="padding: 16px 20px; border-bottom: 1px solid var(--border-color);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-size: 11px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px;">Progress</span>
                    <span style="font-size: 14px; font-weight: 600; color: var(--primary-color);">{{ $progress }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                </div>
            </div>
            
            <!-- Vertical Wizard -->
            <div class="wizard-container">
                @foreach($phases as $phaseIndex => $phase)
                <div class="wizard-phase">
                    <div class="wizard-phase-header" onclick="togglePhase({{ $phase['id'] }})">
                        @php
                        $phaseCompleted = count(array_filter($phase['topics'], fn($t) => $t['is_completed'])) === count($phase['topics']);
                        $phaseActive = !$phaseCompleted && ($phaseIndex === 0 || count(array_filter($phases[$phaseIndex-1]['topics'], fn($t) => $t['is_completed'])) === count($phases[$phaseIndex-1]['topics']));
                        @endphp
                        <div class="wizard-phase-icon {{ $phaseCompleted ? 'completed' : ($phaseActive ? 'active' : '') }}">
                            @if($phaseCompleted)
                                <i class="fa fa-check"></i>
                            @else
                                <i class="fa {{ $phase['icon'] ?? 'fa-book' }}"></i>
                            @endif
                        </div>
                        <div class="wizard-phase-info">
                            <div class="wizard-phase-title">{{ $phase['title'] }}</div>
                            <div class="wizard-phase-description">{{ $phase['description'] }}</div>
                        </div>
                        <div class="wizard-phase-toggle" id="phase-toggle-{{ $phase['id'] }}">
                            <i class="fa fa-chevron-down"></i>
                        </div>
                    </div>
                    
                    <div class="wizard-topics" id="phase-topics-{{ $phase['id'] }}">
                        @foreach($phase['topics'] as $topic)
                        <div class="wizard-topic {{ $loop->first ? 'active-item' : '' }}" onclick="openTopic({{ $topic['id'] }}, '{{ $topic['type'] }}')">
                            @php
                            $topicCompleted = $topic['is_completed'];
                            $topicActive = !$topicCompleted && ($loop->first || (isset($phase['topics'][$loop->index-1]) && $phase['topics'][$loop->index-1]['is_completed']));
                            @endphp
                            <div class="wizard-topic-icon {{ $topicCompleted ? 'completed' : ($topicActive ? 'active' : 'locked') }}">
                                @if($topicCompleted)
                                    <i class="fa fa-check"></i>
                                @elseif($topic['type'] == 'video')
                                    <i class="fa fa-play"></i>
                                @elseif($topic['type'] == 'pdf')
                                    <i class="fa fa-file-pdf-o"></i>
                                @elseif($topic['type'] == 'ppt')
                                    <i class="fa fa-file-powerpoint-o"></i>
                                @elseif($topic['type'] == 'document')
                                    <i class="fa fa-file-word-o"></i>
                                @elseif($topic['type'] == 'audio')
                                    <i class="fa fa-headphones"></i>
                                @else
                                    <i class="fa fa-file-o"></i>
                                @endif
                            </div>
                            <div class="wizard-topic-info">
                                <div class="wizard-topic-title">{{ $topic['title'] }}</div>
                                <div class="wizard-topic-meta">
                                    <span><i class="fa fa-clock-o"></i> {{ $topic['duration'] }}</span>
                                    @if($topicCompleted)
                                        <span><i class="fa fa-check-circle"></i> Done</span>
                                    @endif
                                </div>
                            </div>
                            <div class="wizard-topic-action {{ $topicCompleted ? 'completed' : ($topicActive ? 'start' : 'locked') }}">
                                @if($topicCompleted)
                                    Review
                                @elseif($topicActive)
                                    {{ $loop->parent->first ? 'Start' : 'Continue' }}
                                @else
                                    <i class="fa fa-lock"></i>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Main Content Area -->
        <div class="classroom-main">
            <!-- Header -->
            <div class="classroom-header" id="classroom-header">
                <button class="mobile-sidebar-toggle" onclick="toggleSidebar()">
                    <i class="fa fa-bars"></i>
                    <span>Course Content</span>
                </button>
                <a href="{{ url('/learning/course/' . $material->id) }}" title="Back to Course">
                    <i class="fa fa-arrow-left"></i>
                </a>
                <h1>{{ $material->title }}</h1>
                <div class="header-actions">
                    @if(isset($material->file_path) && $material->file_path)
                    <a href="{{ asset($material->file_path) }}" class="btn btn-default btn-sm" target="_blank">
                        <i class="fa fa-download"></i> Download
                    </a>
                    @endif
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="progress-section">
                <div class="progress-info">
                    <span class="progress-label">Course Progress</span>
                    <span class="progress-percentage">{{ $progress }}%</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: {{ $progress }}%;"></div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="content-area" id="content-area">
                @php
                // Get the first incomplete topic or the first topic
                $currentTopic = null;
                foreach ($phases as $phase) {
                    foreach ($phase['topics'] as $topic) {
                        if (!$topic['is_completed']) {
                            $currentTopic = $topic;
                            break 2;
                        }
                    }
                }
                if (!$currentTopic && count($phases) > 0 && count($phases[0]['topics']) > 0) {
                    $currentTopic = $phases[0]['topics'][0];
                }
                @endphp
                
                <div class="content-card">
                    @if($currentTopic)
                    <div class="content-card-header">
                        <div class="content-card-title">{{ $currentTopic['title'] }}</div>
                    </div>
                    <div class="content-card-body">
                        <div class="content-icon">
                            @if($currentTopic['type'] == 'video')
                                <i class="fa fa-play-circle"></i>
                            @elseif($currentTopic['type'] == 'pdf')
                                <i class="fa fa-file-pdf-o"></i>
                            @elseif($currentTopic['type'] == 'ppt')
                                <i class="fa fa-file-powerpoint-o"></i>
                            @elseif($currentTopic['type'] == 'document')
                                <i class="fa fa-file-word-o"></i>
                            @elseif($currentTopic['type'] == 'audio')
                                <i class="fa fa-headphones"></i>
                            @else
                                <i class="fa fa-book"></i>
                            @endif
                        </div>
                        <h2 class="content-title">{{ $currentTopic['title'] }}</h2>
                        <p class="content-description">{{ $material->description }}</p>
                        <div class="content-actions">
                            @if(isset($currentTopic['file_path']) && $currentTopic['file_path'])
                            <a href="{{ asset($currentTopic['file_path']) }}" class="btn btn-primary btn-lg" target="_blank">
                                <i class="fa fa-external-link"></i> Open {{ ucfirst($currentTopic['type']) }}
                            </a>
                            @endif
                            @if($progress < 100)
                            <button class="btn btn-success btn-lg" onclick="markAsComplete()">
                                <i class="fa fa-check-circle"></i> Mark Topic Complete
                            </button>
                            @else
                            <button class="btn btn-success btn-lg" disabled>
                                <i class="fa fa-check-circle"></i> Course Completed
                            </button>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="content-card-header">
                        <div class="content-card-title">{{ $material->title }}</div>
                    </div>
                    <div class="content-card-body">
                        <div class="content-icon">
                            @if($material->material_type == 'video')
                                <i class="fa fa-play-circle"></i>
                            @elseif($material->material_type == 'document')
                                <i class="fa fa-file-pdf-o"></i>
                            @elseif($material->material_type == 'audio')
                                <i class="fa fa-headphones"></i>
                            @else
                                <i class="fa fa-book"></i>
                            @endif
                        </div>
                        <h2 class="content-title">{{ $material->title }}</h2>
                        <p class="content-description">{{ $material->description }}</p>
                        <div class="content-actions">
                            @if(isset($material->file_path) && $material->file_path)
                            <a href="{{ asset($material->file_path) }}" class="btn btn-primary btn-lg" target="_blank">
                                <i class="fa fa-download"></i> Download Material
                            </a>
                            @endif
                            @if($progress < 100)
                            <button class="btn btn-success btn-lg" onclick="markAsComplete()">
                                <i class="fa fa-check-circle"></i> Mark as Complete
                            </button>
                            @else
                            <button class="btn btn-success btn-lg" disabled>
                                <i class="fa fa-check-circle"></i> Completed
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePhase(phaseId) {
    const topicsDiv = document.getElementById('phase-topics-' + phaseId);
    const toggleIcon = document.getElementById('phase-toggle-' + phaseId);
    
    if (topicsDiv.style.display === 'none') {
        topicsDiv.style.display = 'block';
        toggleIcon.classList.add('expanded');
    } else {
        topicsDiv.style.display = 'none';
        toggleIcon.classList.remove('expanded');
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('classroomSidebar');
    sidebar.classList.toggle('show');
}

function openTopic(topicId, topicType) {
    console.log('Opening topic:', topicId, 'Type:', topicType);
}

function markAsComplete() {
    $.ajax({
        url: '{{ url('/learning/course/' . $material->id . '/complete') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Course marked as complete!', 'Success');
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                toastr.error(response.message, 'Error');
            }
        },
        error: function() {
            toastr.error('An error occurred', 'Error');
        }
    });
}

// Expand first phase by default
document.addEventListener('DOMContentLoaded', function() {
    const firstToggle = document.getElementById('phase-toggle-1');
    if (firstToggle) {
        firstToggle.classList.add('expanded');
        document.getElementById('phase-topics-1').style.display = 'block';
    }
});
</script>
@endsection
