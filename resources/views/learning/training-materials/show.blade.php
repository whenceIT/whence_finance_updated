@extends('layouts.learning')

@section('title', $material->title . ' - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $material->title, 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<style>
@media (max-width: 768px) {
    .page-header > div {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .trainer-header-card {
        min-width: auto !important;
        margin-left: 40px !important;
    }
    .page-header h1 {
        font-size: 18px !important;
    }
    .page-header p {
        font-size: 12px !important;
    }
}
.topic-item {
    display: flex;
    align-items: center;
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.2s;
    cursor: pointer;
}

.topic-item:last-child {
    border-bottom: none;
}

.topic-item:hover {
    background: var(--light-bg);
}

.topic-number {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 16px;
    flex-shrink: 0;
    font-size: 14px;
}

.topic-info {
    flex: 1;
}

.topic-title {
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 4px;
    color: var(--text-primary);
}

.topic-meta {
    font-size: 12px;
    color: var(--text-secondary);
    display: flex;
    gap: 12px;
    align-items: center;
}

.topic-meta span {
    display: flex;
    align-items: center;
    gap: 4px;
}

.quiz-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 500;
}

.quiz-badge.has-quiz {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.quiz-badge.no-quiz {
    background: rgba(108, 117, 125, 0.1);
    color: #6c757d;
}

.btn-play-topic {
    padding: 8px 16px;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-play-topic:hover {
    background: var(--primary-color-dark);
    transform: translateY(-1px);
}

.empty-topics {
    text-align: center;
    padding: 40px 20px;
    color: var(--text-secondary);
}

.empty-topics i {
    font-size: 48px;
    margin-bottom: 15px;
    color: var(--text-secondary);
}
</style>

<div class="page-header">
    <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 16px;">
        <div style="display: flex; align-items: center; gap: 16px; flex: 1; min-width: 0;">
            <a href="{{ url('/learning/training-materials') }}" style="color: var(--primary-color); font-size: 24px;">
                <i class="fa fa-arrow-left"></i>
            </a>
            <div style="min-width: 0;">
                <h1 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $material->title }}</h1>
                <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: var(--text-secondary); font-size: 14px;">
                    {{ $material->department ?? 'General' }} • {{ $material->material_type ?? 'Training Material' }}
                </p>
            </div>
        </div>
        
        <!-- Trainer/Creator Info Card in Header -->
        @if($material->creator)
        <div class="trainer-header-card" style="background: white; border-radius: 12px; box-shadow: var(--shadow); padding: 12px 16px; display: flex; align-items: center; gap: 12px; min-width: 200px; flex-shrink: 0;">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 16px; flex-shrink: 0;">
                <i class="fa fa-user"></i>
            </div>
            <div style="flex: 1; min-width: 0;">
                <div style="font-weight: 600; font-size: 13px; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $material->creator->first_name }} {{ $material->creator->last_name }}
                </div>
                @if($material->creator->roles && $material->creator->roles->first())
                <div style="font-size: 11px; color: var(--primary-color); font-weight: 500;">
                    {{ $material->creator->roles->first()->name }}
                </div>
                @endif
                <div style="font-size: 10px; color: var(--text-secondary); margin-top: 2px;">
                    <i class="fa fa-clock-o"></i> {{ $material->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Material Content -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow);">
            <div class="panel-body" style="padding: 30px;">
                <!-- Material Preview -->
                <div style="background: var(--light-bg); border-radius: 8px; padding: 40px; text-align: center; margin-bottom: 24px;">
                    @if($material->material_type == 'video')
                        <i class="fa fa-play-circle" style="font-size: 64px; color: var(--primary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Video Content</p>
                    @elseif($material->material_type == 'audio')
                        <i class="fa fa-headphones" style="font-size: 64px; color: var(--secondary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Audio Content</p>
                    @elseif($material->material_type == 'document')
                        <i class="fa fa-file-pdf-o" style="font-size: 64px; color: var(--accent-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Document</p>
                    @elseif($material->material_type == 'link')
                        <i class="fa fa-link" style="font-size: 64px; color: var(--secondary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">External Link</p>
                    @else
                        <i class="fa fa-book" style="font-size: 64px; color: var(--primary-color);"></i>
                        <p style="margin-top: 16px; color: var(--text-secondary);">Training Material</p>
                    @endif
                </div>

                <!-- Material Details -->
                <div style="margin-bottom: 24px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 12px;">About This Material</h3>
                    <p style="color: var(--text-secondary); line-height: 1.8;">
                        {{ $material->description ?: 'No description available.' }}
                    </p>
                </div>

                <!-- Material Meta -->
                <div style="display: flex; gap: 24px; flex-wrap: wrap; margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-clock-o" style="color: var(--primary-color);"></i>
                        <span>{{ $material->human_duration ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-folder" style="color: var(--primary-color);"></i>
                        <span>{{ $material->category ?? 'General' }}</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <i class="fa fa-building" style="color: var(--primary-color);"></i>
                        <span>{{ $material->department ?? 'All Departments' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Classroom / Topics Section -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 24px;">
            <div class="panel-body" style="padding: 0;">
                <div style="padding: 20px 24px; border-bottom: 1px solid var(--border-color);">
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0;">
                        <i class="fa fa-chalkboard-teacher" style="color: var(--primary-color); margin-right: 10px;"></i>
                        Classroom
                    </h3>
                    <p style="color: var(--text-secondary); font-size: 13px; margin: 5px 0 0 0;">
                        {{ $material->allTopics->count() }} topics • Click any topic to view details
                    </p>
                </div>

                @if($material->allTopics && $material->allTopics->count() > 0)
                    <div style="max-height: 500px; overflow-y: auto;">
                        @foreach($material->allTopics as $index => $topic)
                        <div class="topic-item" onclick="showTopicPreview({{ $topic->id }}, '{{ addslashes($topic->topic_name) }}')">
                            <div class="topic-number">{{ $index + 1 }}</div>
                            <div class="topic-info">
                                <div class="topic-title">{{ $topic->topic_name }}</div>
                                <div class="topic-meta">
                                    <span>
                                        <i class="fa fa-clock-o"></i> 
                                        {{ $topic->duration ? $topic->duration . ' min' : 'N/A' }}
                                    </span>
                                    <span>
                                        <i class="fa fa-file-o"></i> 
                                        {{ ucfirst($topic->topic_type) }}
                                    </span>
                                    @if($topic->quiz)
                                        <span class="quiz-badge has-quiz">
                                            <i class="fa fa-check-circle"></i> Quiz
                                        </span>
                                    @else
                                        <span class="quiz-badge no-quiz">
                                            <i class="fa fa-times-circle"></i> No Quiz
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <button class="btn-play-topic">
                                <i class="fa fa-play"></i> View
                            </button>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-topics">
                        <i class="fa fa-folder-open"></i>
                        <h4>No Topics Available</h4>
                        <p>This training material doesn't have any topics yet.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Materials -->
        @if($material->categories && $material->categories->count() > 0)
        @php
        $relatedMaterials = \App\Models\TrainingMaterial::whereHas('categories', function ($query) use ($material) {
            $query->whereIn('course_category_id', $material->categories->pluck('id'));
        })->where('id', '!=', $material->id)->limit(6)->get();
        @endphp

        @if($relatedMaterials->count() > 0)
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 24px;">
            <div class="panel-body" style="padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                    <i class="fa fa-link" style="color: var(--primary-color); margin-right: 10px;"></i>
                    Related Materials
                </h3>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px;">
                    @foreach($relatedMaterials as $related)
                    <a href="{{ url('learning/training-materials/' . $related->id) }}" style="display: block; text-decoration: none; color: inherit;">
                        <div style="background: var(--light-bg); border-radius: 8px; padding: 15px; transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='none'">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                                <i class="fa {{ $related->icon }}" style="font-size: 24px; color: {{ $related->type_color }};"></i>
                                <span style="background: rgba(255,255,255,0.9); padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600;">{{ $related->department }}</span>
                            </div>
                            <h4 style="font-size: 13px; font-weight: 600; color: var(--text-primary); margin: 0 0 8px 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $related->title }}</h4>
                            <p style="font-size: 11px; color: var(--text-secondary); margin: 0; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $related->description ?: 'No description' }}</p>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @endif
    </div>

    <div class="col-md-4">
        <!-- Sidebar -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow);">
            <div class="panel-body" style="padding: 24px;">
                <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Start Learning</h3>
                
                <!-- Download Section -->
                @if($material->file_path)
                    <a href="{{ url('learning/training-materials/' . $material->id . '/download') }}" class="btn btn-primary btn-block" style="margin-bottom: 12px;">
                        <i class="fa fa-download"></i> Download Material
                    </a>
                @else
                    <div style="text-align: center; padding: 15px; background: var(--light-bg); border-radius: 8px; margin-bottom: 16px;">
                        <i class="fa fa-info-circle" style="color: var(--text-secondary); font-size: 20px; margin-bottom: 8px;"></i>
                        <p style="color: var(--text-secondary); font-size: 12px; margin: 0;">No downloadable materials</p>
                    </div>
                @endif

                <!-- Classroom Button -->
                @if($material->allTopics && $material->allTopics->count() > 0)
                    <a href="{{ url('learning/training-materials/' . $material->id . '/topics') }}" class="btn btn-success btn-block" style="margin-bottom: 12px;">
                        <i class="fa fa-chalkboard-teacher"></i> Manage Topics
                    </a>
                @endif

                <!-- Trainer Actions -->
                @if(Sentinel::getUser() && in_array(Sentinel::getUser()->roles->first()->id, ['1', '6', '4']))
                <div style="border-top: 1px solid var(--border-color); padding-top: 16px; margin-top: 16px;">
                    <h4 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-secondary);">
                        <i class="fa fa-cog"></i> Trainer Options
                    </h4>
                    <a href="{{ url('learning/training-materials/' . $material->id . '/edit') }}" class="btn btn-default btn-block" style="margin-bottom: 8px;">
                        <i class="fa fa-edit"></i> Edit Material
                    </a>
                    <a href="{{ url('learning/training-materials/' . $material->id . '/topics') }}" class="btn btn-default btn-block">
                        <i class="fa fa-list"></i> Manage Topics
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Material Info -->
        <div class="panel panel-default" style="border-radius: 10px; box-shadow: var(--shadow); margin-top: 16px;">
            <div class="panel-body" style="padding: 24px;">
                <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">Material Information</h3>
                
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">Views</span>
                    <div style="font-weight: 600;">{{ $material->view_count ?? 0 }}</div>
                </div>
                
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">Downloads</span>
                    <div style="font-weight: 600;">{{ $material->download_count ?? 0 }}</div>
                </div>

                @if(isset($material->created_at))
                <div style="margin-bottom: 12px;">
                    <span style="color: var(--text-secondary); font-size: 12px;">Added</span>
                    <div style="font-weight: 600;">{{ $material->created_at->format('M d, Y') }}</div>
                </div>
                @endif

                @if($material->categories && $material->categories->count() > 0)
                <div>
                    <span style="color: var(--text-secondary); font-size: 12px;">Categories</span>
                    <div style="margin-top: 8px;">
                        @foreach($material->categories as $cat)
                            <span style="display: inline-block; background: rgba(74, 144, 226, 0.1); color: var(--primary-color); padding: 3px 10px; border-radius: 12px; font-size: 11px; margin-right: 4px; margin-bottom: 4px;">{{ $cat->name }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Topic Preview Modal -->
<div id="topicPreviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; overflow-y: auto;" onclick="closeTopicPreview(event)">
    <div style="background: white; max-width: 700px; margin: 50px auto; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: white; font-size: 18px;">
                <i class="fa fa-book"></i> <span id="topicPreviewTitle">Topic Details</span>
            </h3>
            <button onclick="closeTopicPreviewModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 16px;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="topicPreviewContent" style="padding: 25px; max-height: 60vh; overflow-y: auto;">
            <!-- Topic details will be loaded here -->
            <div style="text-align: center; padding: 40px;">
                <i class="fa fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i>
                <p style="margin-top: 15px; color: var(--text-secondary);">Loading topic details...</p>
            </div>
        </div>
        <div style="padding: 15px 25px; background: var(--light-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="closeTopicPreviewModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Topic data passed from server
var topicData = {};
@if($material->allTopics)
@foreach($material->allTopics as $topic)
topicData[{{ $topic->id }}] = {
    id: {{ $topic->id }},
    name: '{{ addslashes($topic->topic_name) }}',
    type: '{{ $topic->topic_type }}',
    duration: {{ $topic->duration ?? 'null' }},
    video_path: '{{ $topic->video_file_path ?? '' }}',
    audio_path: '{{ $topic->audio_file_path ?? '' }}',
    pdf_path: '{{ $topic->pdf_file_path ?? '' }}',
    quiz: {{ $topic->quiz ? '{id: ' . $topic->quiz->id . ', title: \'' . addslashes($topic->quiz->title) . '\'}' : 'null' }}
};
@endforeach
@endif

function showTopicPreview(topicId, topicName) {
    document.getElementById('topicPreviewTitle').textContent = topicName;
    document.getElementById('topicPreviewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    renderTopicDetails(topicId);
}

function closeTopicPreviewModal() {
    document.getElementById('topicPreviewModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function closeTopicPreview(event) {
    if (event.target.id === 'topicPreviewModal') {
        closeTopicPreviewModal();
    }
}

function renderTopicDetails(topicId) {
    var contentDiv = document.getElementById('topicPreviewContent');
    var topic = topicData[topicId];
    
    if (!topic) {
        contentDiv.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-exclamation-circle" style="font-size: 48px; color: var(--text-secondary);"></i><p style="margin-top: 15px; color: var(--text-secondary);">Topic not found.</p></div>';
        return;
    }
    
    var html = '';
    
    // Topic Info Card
    html += '<div style="background: var(--light-bg); border-radius: 8px; padding: 20px; margin-bottom: 20px;">';
    html += '<h4 style="margin: 0 0 15px 0; font-size: 16px; color: var(--text-primary);">' + topic.name + '</h4>';
    html += '<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 15px;">';
    html += '<div style="display: flex; align-items: center; gap: 6px;"><i class="fa fa-clock-o" style="color: var(--primary-color);"></i><span style="font-size: 13px; color: var(--text-secondary);">' + (topic.duration ? topic.duration + ' minutes' : 'N/A') + '</span></div>';
    html += '<div style="display: flex; align-items: center; gap: 6px;"><i class="fa fa-file-o" style="color: var(--primary-color);"></i><span style="font-size: 13px; color: var(--text-secondary); text-transform: capitalize;">' + topic.type + '</span></div>';
    html += '</div>';
    
    if (topic.quiz) {
        html += '<div style="background: rgba(40, 167, 69, 0.1); padding: 10px 15px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;">';
        html += '<i class="fa fa-check-circle" style="color: #28a745;"></i>';
        html += '<span style="color: #28a745; font-weight: 500; font-size: 13px;">Quiz Available: ' + topic.quiz.title + '</span>';
        html += '</div>';
    } else {
        html += '<div style="background: rgba(108, 117, 125, 0.1); padding: 10px 15px; border-radius: 6px; display: inline-flex; align-items: center; gap: 8px;">';
        html += '<i class="fa fa-times-circle" style="color: #6c757d;"></i>';
        html += '<span style="color: #6c757d; font-weight: 500; font-size: 13px;">No Quiz</span>';
        html += '</div>';
    }
    html += '</div>';
    
    // File Resources
    html += '<h5 style="font-size: 14px; font-weight: 600; margin-bottom: 12px; color: var(--text-primary);">Available Resources</h5>';
    html += '<div style="display: flex; flex-direction: column; gap: 10px;">';
    
    var hasResources = false;
    
    if (topic.video_path) {
        hasResources = true;
        html += '<a href="' + topic.video_path + '" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; background: rgba(74, 144, 226, 0.1); border-radius: 8px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform=\'translateX(4px)\'" onmouseout="this.style.transform=\'translateX(0)\'">';
        html += '<i class="fa fa-video-camera" style="color: var(--primary-color); font-size: 20px;"></i>';
        html += '<div style="flex: 1;"><div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">Video</div><div style="font-size: 11px; color: var(--text-secondary);">Click to watch</div></div>';
        html += '<i class="fa fa-external-link" style="color: var(--text-secondary);"></i>';
        html += '</a>';
    }
    
    if (topic.audio_path) {
        hasResources = true;
        html += '<a href="' + topic.audio_path + '" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; background: rgba(40, 167, 69, 0.1); border-radius: 8px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform=\'translateX(4px)\'" onmouseout="this.style.transform=\'translateX(0)\'">';
        html += '<i class="fa fa-headphones" style="color: #28a745; font-size: 20px;"></i>';
        html += '<div style="flex: 1;"><div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">Audio</div><div style="font-size: 11px; color: var(--text-secondary);">Click to listen</div></div>';
        html += '<i class="fa fa-external-link" style="color: var(--text-secondary);"></i>';
        html += '</a>';
    }
    
    if (topic.pdf_path) {
        hasResources = true;
        html += '<a href="' + topic.pdf_path + '" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px 15px; background: rgba(231, 76, 60, 0.1); border-radius: 8px; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform=\'translateX(4px)\'" onmouseout="this.style.transform=\'translateX(0)\'">';
        html += '<i class="fa fa-file-pdf-o" style="color: #e74c3c; font-size: 20px;"></i>';
        html += '<div style="flex: 1;"><div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">PDF Document</div><div style="font-size: 11px; color: var(--text-secondary);">Click to view</div></div>';
        html += '<i class="fa fa-external-link" style="color: var(--text-secondary);"></i>';
        html += '</a>';
    }
    
    if (!hasResources) {
        html += '<div style="text-align: center; padding: 30px; background: var(--light-bg); border-radius: 8px; color: var(--text-secondary);">';
        html += '<i class="fa fa-folder-open" style="font-size: 32px; margin-bottom: 10px;"></i>';
        html += '<p style="margin: 0; font-size: 13px;">No resources available for this topic.</p>';
        html += '</div>';
    }
    
    html += '</div>';
    
    contentDiv.innerHTML = html;
}
</script>
@endsection
