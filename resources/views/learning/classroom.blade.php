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
    max-width: 100%;
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
    padding: 30px;
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
    margin-top: 24px;
}

/* Resource Preview Area */
.resource-preview {
    width: 100%;
    height: 500px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    overflow: hidden;
    background: #f5f5f5;
}

.resource-preview iframe,
.resource-preview embed,
.resource-preview object {
    width: 100%;
    height: 100%;
    border: none;
}

/* Quiz Section */
.quiz-section {
    display: none;
    padding: 30px;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
    border-top: 2px solid var(--primary-color);
    margin-top: 20px;
}

.quiz-section.visible {
    display: block;
}

.quiz-header {
    text-align: center;
    margin-bottom: 30px;
}

.quiz-header h3 {
    font-size: 24px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.quiz-header p {
    color: var(--text-secondary);
    font-size: 14px;
}

.quiz-actions {
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

.wizard-topic.selected {
    background: rgba(74, 144, 226, 0.2);
    border: 2px solid var(--primary-color);
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
    
    .resource-preview {
        height: 300px;
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
    
    .quiz-actions {
        flex-direction: column;
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
                    <div class="wizard-phase-header" onclick="togglePhase({{ $loop->index }})">
                        @php
                        $phaseCompleted = count(array_filter($phase['topics'], fn($t) => $t['is_completed'])) === count($phase['topics']);
                        $phaseActive = !$phaseCompleted && ($phaseIndex === 0 || (isset($phases[$phaseIndex-1]) && count(array_filter($phases[$phaseIndex-1]['topics'], fn($t) => $t['is_completed'])) === count($phases[$phaseIndex-1]['topics'])));
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
                        <div class="wizard-phase-toggle" id="phase-toggle-{{ $loop->index }}">
                            <i class="fa fa-chevron-down"></i>
                        </div>
                    </div>
                    
                    <div class="wizard-topics" id="phase-topics-{{ $loop->index }}">
                        @foreach($phase['topics'] as $topic)
                        @php
                        $topicCompleted = $topic['is_completed'];
                        $topicActive = !$topicCompleted && ($loop->first || (isset($phase['topics'][$loop->index-1]) && $phase['topics'][$loop->index-1]['is_completed']));
                        @endphp
                        <div class="wizard-topic {{ $topicActive ? 'active-item' : '' }}" 
                             onclick="openTopic({{ $topic['id'] }}, '{{ $topic['type'] }}', '{{ $topic['file_path'] ?? '' }}', {{ $topic['quiz_id'] ?? 'null' }})"
                             data-topic-id="{{ $topic['id'] }}"
                             data-topic-completed="{{ $topicCompleted ? 'true' : 'false' }}"
                             data-quiz-id="{{ $topic['quiz_id'] ?? 'null' }}"
                             data-quiz-passed="{{ $topic['quiz_passed'] ? 'true' : 'false' }}">
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
                                    @if($topic['quiz_id'])
                                        <button onclick="event.stopPropagation(); confirmTakeQuiz({{ $topic['quiz_id'] }}, '{{ $topic['title'] }}')" style="background: none; border: none; color: inherit; padding: 0; cursor: pointer;">
                                            <i class="fa fa-pencil"></i> Quiz
                                        </button>
                                    @else
                                        Review
                                    @endif
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
                <h1 id="topic-header-title">{{ $material->title }}</h1>
                <div class="header-actions">
                    @if(isset($material->file_path) && $material->file_path && strpos($material->file_path, 'http') === 0)
                    <a href="{{ $material->file_path }}" class="btn btn-default btn-sm" target="_blank">
                        <i class="fa fa-external-link"></i> Open Resource
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
                <div class="content-card" id="topic-content-card">
                    <div class="content-card-header">
                        <div class="content-card-title" id="content-card-title">{{ $material->title }}</div>
                    </div>
                    <div class="content-card-body" id="content-card-body">
                        <div class="content-icon" id="content-icon">
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
                        <h2 class="content-title" id="content-title">{{ $material->title }}</h2>
                        <p class="content-description" id="content-description">{{ $material->description }}</p>
                        
                        <!-- Resource Preview -->
                        <div id="resource-container" style="display: none;">
                            <div class="resource-preview" id="resource-preview"></div>
                        </div>
                        
                        <div class="content-actions" id="content-actions">
                            <button class="btn btn-primary btn-lg" onclick="selectFirstTopic()">
                                <i class="fa fa-play"></i> Start Learning
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quiz Section (shown after completion) -->
                <div class="quiz-section" id="quiz-section">
                    <div class="quiz-header">
                        <h3><i class="fa fa-graduation-cap" style="color: var(--primary-color);"></i> Topic Completed!</h3>
                        <p>Great job! You've completed this topic. {{ $topic['quiz_id'] ? 'Ready to test your knowledge?' : 'Continue to the next topic.' }}</p>
                    </div>
                    <div class="quiz-actions">
                        @if($topic['quiz_id'])
                        <button class="btn btn-success btn-lg" onclick="confirmTakeQuiz({{ $topic['quiz_id'] }}, '{{ $topic['title'] }}')">
                            <i class="fa fa-pencil"></i> {{ $topic['quiz_passed'] ? 'Retake Quiz' : 'Take Quiz' }}
                        </button>
                        @endif
                        @if(!$topic['quiz_id'] || $topic['quiz_passed'])
                        <button class="btn btn-secondary btn-lg" onclick="skipQuiz()">
                            <i class="fa fa-arrow-right"></i> Continue to Next Topic
                        </button>
                        @else
                        <button class="btn btn-secondary btn-lg" onclick="skipQuiz()" disabled style="opacity: 0.6; cursor: not-allowed;" title="Pass the quiz to continue">
                            <i class="fa fa-lock"></i> Pass Quiz to Continue
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Confirmation Modal -->
<div class="modal fade" id="quizConfirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-question-circle" style="color: var(--primary-color);"></i> Start Quiz</h4>
            </div>
            <div class="modal-body">
                <p id="quizConfirmMessage">Are you ready to take this quiz?</p>
                <p style="color: var(--text-secondary); font-size: 13px;">
                    <i class="fa fa-info-circle"></i> You need to pass this quiz to complete the topic and proceed to the next one.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <a href="#" id="quizConfirmLink" class="btn btn-primary">
                    <i class="fa fa-pencil"></i> Start Quiz
                </a>
            </div>
        </div>
    </div>
</div>

<script>
let currentTopicId = null;
let currentTopicFilePath = null;
let currentTopicType = null;
let currentTopicCompleted = false;
let currentQuizId = null;
let currentQuizPassed = false;

function togglePhase(phaseIndex) {
    const topicsDiv = document.getElementById('phase-topics-' + phaseIndex);
    const toggleIcon = document.getElementById('phase-toggle-' + phaseIndex);
    
    if (topicsDiv.style.display === 'none') {
        topicsDiv.style.display = 'block';
        toggleIcon.classList.add('expanded');
    } else {
        topicsDiv.style.display = 'none';
        toggleIcon.classList.remove('expanded');
    }
}

function toggleSidebar() {
    const sidebar = document.getElementById('classroom-sidebar');
    sidebar.classList.toggle('show');
}

function openTopic(topicId, topicType, topicFilePath, quizId = null) {
    console.log('Opening topic:', topicId, 'Type:', topicType, 'File:', topicFilePath, 'Quiz:', quizId);
    
    currentTopicId = topicId;
    currentTopicType = topicType;
    currentTopicFilePath = topicFilePath;
    currentQuizId = quizId;
    
    // Find topic completed status from the clicked element
    const topicElement = document.querySelector(`[data-topic-id="${topicId}"]`);
    currentTopicCompleted = topicElement ? topicElement.dataset.topicCompleted === 'true' : false;
    currentQuizPassed = topicElement ? topicElement.dataset.quizPassed === 'true' : false;
    
    // Update sidebar selection
    document.querySelectorAll('.wizard-topic').forEach(el => el.classList.remove('selected'));
    if (topicElement) {
        topicElement.classList.add('selected');
    }
    
    // Update content area with topic info
    updateTopicContent(topicId, topicType, topicFilePath);
}

function updateTopicContent(topicId, topicType, topicFilePath) {
    // Find topic data from the DOM
    const topicElement = document.querySelector(`[data-topic-id="${topicId}"]`);
    if (!topicElement) return;
    
    const topicTitle = topicElement.querySelector('.wizard-topic-title').textContent;
    const topicDuration = topicElement.querySelector('.wizard-topic-meta span:first-child').textContent;
    
    // Update header
    document.getElementById('topic-header-title').textContent = topicTitle;
    
    // Update content card
    document.getElementById('content-card-title').textContent = topicTitle;
    document.getElementById('content-title').textContent = topicTitle;
    document.getElementById('content-description').textContent = '{{ $material->description }}';
    
    // Update icon based on type
    const iconElement = document.getElementById('content-icon');
    let iconClass = 'fa-book';
    if (topicType == 'video') iconClass = 'fa-play-circle';
    else if (topicType == 'pdf') iconClass = 'fa-file-pdf-o';
    else if (topicType == 'ppt') iconClass = 'fa-file-powerpoint-o';
    else if (topicType == 'document') iconClass = 'fa-file-word-o';
    else if (topicType == 'audio') iconClass = 'fa-headphones';
    
    iconElement.innerHTML = `<i class="fa ${iconClass}"></i>`;
    
    // Show/hide resource preview
    const resourceContainer = document.getElementById('resource-container');
    const resourcePreview = document.getElementById('resource-preview');
    
    if (topicFilePath && topicFilePath.trim() !== '') {
        resourceContainer.style.display = 'block';
        
        // Try to embed the resource
        if (topicFilePath.includes('drive.google.com')) {
            // Google Drive embed
            let embedUrl = topicFilePath;
            if (embedUrl.includes('/view')) {
                embedUrl = embedUrl.replace('/view', '/preview');
            }
            if (embedUrl.includes('?')) {
                embedUrl += '&embedded=true';
            } else {
                embedUrl += '?embedded=true';
            }
            resourcePreview.innerHTML = `<iframe src="${embedUrl}" allowfullscreen></iframe>`;
        } else if (topicFilePath.match(/\.(pdf)$/i)) {
            resourcePreview.innerHTML = `<iframe src="${topicFilePath}" allowfullscreen></iframe>`;
        } else {
            // Generic link
            resourceContainer.style.display = 'none';
        }
    } else {
        resourceContainer.style.display = 'none';
    }
    
    // Update actions based on completion status
    const actionsDiv = document.getElementById('content-actions');
    const quizSection = document.getElementById('quiz-section');
    
    if (currentTopicCompleted) {
        quizSection.classList.add('visible');
        const topicTypeLabel = currentTopicType ? currentTopicType.charAt(0).toUpperCase() + currentTopicType.slice(1) : 'Resource';
        actionsDiv.innerHTML = `
            <a href="${topicFilePath}" class="btn btn-primary btn-lg" target="_blank">
                <i class="fa fa-external-link"></i> Open ${topicTypeLabel}
            </a>
            <button class="btn btn-secondary btn-lg" onclick="markAsComplete(true)">
                <i class="fa fa-refresh"></i> Review Topic
            </button>
        `;
    } else {
        quizSection.classList.remove('visible');
        const topicTypeLabel = currentTopicType ? currentTopicType.charAt(0).toUpperCase() + currentTopicType.slice(1) : 'Resource';
        actionsDiv.innerHTML = `
            ${topicFilePath && topicFilePath.trim() !== '' ? `
            <a href="${topicFilePath}" class="btn btn-primary btn-lg" target="_blank">
                <i class="fa fa-external-link"></i> Open ${topicTypeLabel}
            </a>
            ` : ''}
            <button class="btn btn-success btn-lg" onclick="markAsComplete(false)">
                <i class="fa fa-check-circle"></i> Mark Topic Complete
            </button>
        `;
    }
}

function selectFirstTopic() {
    // Find first active topic
    const firstActive = document.querySelector('.wizard-topic-action.start');
    if (firstActive) {
        const topicElement = firstActive.closest('.wizard-topic');
        if (topicElement) {
            const topicId = topicElement.dataset.topicId;
            const quizId = topicElement.dataset.quizId && topicElement.dataset.quizId !== 'null' ? topicElement.dataset.quizId : null;
            openTopic(topicId, '{{ $phases[0]['topics'][0]['type'] ?? 'video' }}', '{{ $phases[0]['topics'][0]['file_path'] ?? '' }}', quizId);
        }
    }
}

function markAsComplete(isReview) {
    if (!currentTopicId) {
        showFlashMessage('warning', 'No Topic Selected', 'Please select a topic first.', 'fa-exclamation-circle');
        return;
    }
    
    $.ajax({
        url: '{{ url('/learning/course/' . $material->id . '/complete-topic') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            topic_id: currentTopicId
        },
        success: function(response) {
            if (response.success) {
                showFlashMessage('success', 'Topic Completed!', response.message, 'fa-check-circle');
                
                // Update topic status
                const topicElement = document.querySelector(`[data-topic-id="${currentTopicId}"]`);
                if (topicElement) {
                    topicElement.dataset.topicCompleted = 'true';
                    topicElement.querySelector('.wizard-topic-icon').classList.remove('active', 'locked');
                    topicElement.querySelector('.wizard-topic-icon').classList.add('completed');
                    topicElement.querySelector('.wizard-topic-icon').innerHTML = '<i class="fa fa-check"></i>';
                    topicElement.querySelector('.wizard-topic-action').classList.remove('start', 'locked');
                    topicElement.querySelector('.wizard-topic-action').classList.add('completed');
                    topicElement.querySelector('.wizard-topic-action').textContent = 'Review';
                    
                    // Add done indicator
                    const metaDiv = topicElement.querySelector('.wizard-topic-meta');
                    if (!metaDiv.querySelector('.done-indicator')) {
                        metaDiv.innerHTML += '<span class="done-indicator"><i class="fa fa-check-circle"></i> Done</span>';
                    }
                }
                
                // Show quiz section
                document.getElementById('quiz-section').classList.add('visible');
                
                // Update content actions - show appropriate buttons based on quiz status
                const actionsDiv = document.getElementById('content-actions');
                if (currentQuizId && currentQuizId !== 'null' && !currentQuizPassed) {
                    // Quiz required but not passed
                    actionsDiv.innerHTML = `
                        ${currentTopicFilePath ? `
                        <a href="${currentTopicFilePath}" class="btn btn-primary btn-lg" target="_blank">
                            <i class="fa fa-external-link"></i> Open Resource
                        </a>
                        ` : ''}
                        <button class="btn btn-warning btn-lg" onclick="confirmTakeQuiz(currentQuizId, '${topicElement.querySelector('.wizard-topic-title').textContent}')">
                            <i class="fa fa-pencil"></i> Take Quiz to Complete
                        </button>
                    `;
                } else {
                    // No quiz or quiz passed - allow continuing
                    actionsDiv.innerHTML = `
                        ${currentTopicFilePath ? `
                        <a href="${currentTopicFilePath}" class="btn btn-primary btn-lg" target="_blank">
                            <i class="fa fa-external-link"></i> Open Resource
                        </a>
                        ` : ''}
                        <button class="btn btn-secondary btn-lg" onclick="skipQuiz()">
                            <i class="fa fa-arrow-right"></i> Continue to Next Topic
                        </button>
                    `;
                }
                
                // Reload to update progress
                setTimeout(function() {
                    location.reload();
                }, 2000);
            } else {
                // Handle quiz requirement error
                if (response.quiz_required) {
                    showFlashMessage('warning', 'Quiz Required', response.message, 'fa-exclamation-triangle');
                    // Update content actions to show quiz button
                    const actionsDiv = document.getElementById('content-actions');
                    actionsDiv.innerHTML = `
                        ${currentTopicFilePath ? `
                        <a href="${currentTopicFilePath}" class="btn btn-primary btn-lg" target="_blank">
                            <i class="fa fa-external-link"></i> Open Resource
                        </a>
                        ` : ''}
                        <button class="btn btn-warning btn-lg" onclick="confirmTakeQuiz(${response.quiz_id}, '${topicElement.querySelector('.wizard-topic-title').textContent}')">
                            <i class="fa fa-pencil"></i> Take Quiz First
                        </button>
                    `;
                } else {
                    showFlashMessage('error', 'Error', response.message, 'fa-times-circle');
                }
            }
        },
        error: function() {
            showFlashMessage('error', 'Error', 'An error occurred', 'fa-times-circle');
        }
    });
}

function skipQuiz() {
    // Check if current topic has a quiz that hasn't been passed
    if (currentQuizId && currentQuizId !== 'null' && !currentQuizPassed) {
        showFlashMessage('warning', 'Quiz Required', 'Please take and pass the quiz before proceeding to the next topic.', 'fa-exclamation-triangle');
        return;
    }
    
    // Move to next incomplete topic or show completion message
    showFlashMessage('info', 'Moving to Next Topic', 'Loading next topic...', 'fa-info-circle');
    
    // Find next incomplete topic
    const topics = document.querySelectorAll('.wizard-topic');
    let foundCurrent = false;
    for (let topic of topics) {
        if (foundCurrent) {
            if (topic.dataset.topicCompleted === 'false') {
                const topicId = topic.dataset.topicId;
                const quizId = topic.dataset.quizId && topic.dataset.quizId !== 'null' ? topic.dataset.quizId : null;
                const quizPassed = topic.dataset.quizPassed === 'true';
                
                // Get topic type from icon classes
                let topicType = 'video';
                if (topic.querySelector('.wizard-topic-icon .fa-file-pdf-o')) topicType = 'pdf';
                else if (topic.querySelector('.wizard-topic-icon .fa-file-powerpoint-o')) topicType = 'ppt';
                else if (topic.querySelector('.wizard-topic-icon .fa-file-word-o')) topicType = 'document';
                else if (topic.querySelector('.wizard-topic-icon .fa-headphones')) topicType = 'audio';
                
                // Check if next topic requires quiz and if passed
                if (quizId && !quizPassed) {
                    // Quiz required but not passed - redirect to quiz
                    window.location.href = '/learning/training-materials/quiz/' + quizId + '/take';
                } else {
                    openTopic(topicId, topicType, '', quizId);
                }
                return;
            }
        }
        if (topic.dataset.topicId == currentTopicId) {
            foundCurrent = true;
        }
    }
    
    // No more topics - course completed
    showFlashMessage('success', 'Course Completed!', 'Congratulations on completing this course!', 'fa-trophy');
}

// Expand first phase by default
document.addEventListener('DOMContentLoaded', function() {
    const firstToggle = document.getElementById('phase-toggle-0');
    if (firstToggle) {
        firstToggle.classList.add('expanded');
        document.getElementById('phase-topics-0').style.display = 'block';
    }
    
    // Load first incomplete topic if exists
    const firstIncomplete = document.querySelector('.wizard-topic[data-topic-completed="false"]');
    if (firstIncomplete) {
        const topicId = firstIncomplete.dataset.topicId;
        openTopic(topicId, 'video', '');
    }
});

// Quiz confirmation dialog
function confirmTakeQuiz(quizId, topicTitle) {
    document.getElementById('quizConfirmMessage').innerHTML = 
        'Are you ready to take the quiz for <strong style="color: var(--primary-color);">' + topicTitle + '</strong>?\n        <p style="margin-top: 10px; font-size: 13px;">You need to pass this quiz to complete the topic.</p>';
    document.getElementById('quizConfirmLink').href = '/learning/training-materials/quiz/' + quizId + '/take';
    $('#quizConfirmModal').modal('show');
}
</script>
@endsection
