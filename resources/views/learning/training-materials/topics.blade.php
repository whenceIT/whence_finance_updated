@extends('layouts.learning')

@section('title', 'Manage Topics & Quizzes - ' . $material->title . ' - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $material->title, 'url' => url('learning/training-materials/' . $material->id)],
    ['label' => 'Manage Topics & Quizzes', 'url' => '']
];
@endphp
@include('partials.breadcrumb')
<style>
.page-header {
    margin-bottom: 30px;
}

.page-header h1 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 8px;
}

.page-header p {
    color: var(--text-secondary);
}

.topics-container {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.topic-item {
    display: flex;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.2s;
}

.topic-item:last-child {
    border-bottom: none;
}

.topic-item:hover {
    background: var(--light-bg);
}

.topic-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin-right: 16px;
    flex-shrink: 0;
}

.topic-info {
    flex: 1;
}

.topic-title {
    font-weight: 600;
    font-size: 16px;
    margin-bottom: 4px;
    color: var(--text-primary);
}

.topic-meta {
    font-size: 13px;
    color: var(--text-secondary);
    display: flex;
    gap: 16px;
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
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 12px;
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

.topic-actions {
    display: flex;
    gap: 8px;
    margin-left: 16px;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 13px;
    border-radius: 6px;
}

.btn-success {
    background: #28a745;
    color: white;
    border: none;
}

.btn-success:hover {
    background: #218838;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: none;
}

.btn-primary:hover {
    background: var(--primary-color-dark);
}

.btn-secondary {
    background: #6c757d;
    color: white;
    border: none;
}

.btn-secondary:hover {
    background: #5a6268;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 64px;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
}

.empty-state p {
    color: var(--text-secondary);
    max-width: 400px;
    margin: 0 auto 20px;
}

.back-link {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 20px;
}

.back-link:hover {
    text-decoration: underline;
}

.material-info {
    background: var(--light-bg);
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-color);
}

.material-info h2 {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.material-info p {
    font-size: 14px;
    color: var(--text-secondary);
    margin: 0;
}
</style>

<div class="page-header">
    <a href="{{ url('learning/training-materials') }}" class="back-link">
        <i class="fa fa-arrow-left"></i> Back to Training Materials
    </a>
    <h1>Manage Topics & Quizzes</h1>
    <p>Configure learning topics and quizzes for your training materials</p>
</div>

<div class="topics-container">
    <div class="material-info">
        <h2>{{ $material->title }}</h2>
        <p>{{ $material->description ?: 'No description' }}</p>
    </div>
    
    @if($material->topics && $material->topics->count() > 0)
        @foreach($material->topics as $index => $topic)
        <div class="topic-item">
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
                            <i class="fa fa-check-circle"></i> Quiz: {{ $topic->quiz->title }}
                        </span>
                    @else
                        <span class="quiz-badge no-quiz">
                            <i class="fa fa-times-circle"></i> No Quiz
                        </span>
                    @endif
                </div>
            </div>
            <div class="topic-actions">
                @if($topic->quiz)
                    <a href="{{ url('/learning/training-materials/topic/' . $topic->id . '/quiz/manage') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit Quiz
                    </a>
                @else
                    <a href="{{ url('/learning/training-materials/topic/' . $topic->id . '/quiz/manage') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Add Quiz
                    </a>
                @endif
                <a href="{{ url('learning/course/' . $material->id . '/classroom?topic=' . $topic->id) }}" class="btn btn-secondary btn-sm" target="_blank">
                    <i class="fa fa-eye"></i> Preview
                </a>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fa fa-folder-open"></i>
            <h3>No Topics Found</h3>
            <p>This training material doesn't have any topics yet. Add topics to create a structured learning experience for your users.</p>
            <a href="{{ route('learning.training-materials.add-topics', ['materialId' => $material->id]) }}" class="btn btn-success" style="padding: 12px 30px; margin-top: 20px;">
                <i class="fa fa-plus"></i> Add First Topic
            </a>
        </div>
    @endif
</div>

<div style="margin-top: 30px; text-align: center; display: flex; gap: 15px; justify-content: center;">
    <a href="{{ route('learning.training-materials.add-topics', ['materialId' => $material->id]) }}" class="btn btn-success" style="padding: 12px 30px;">
        <i class="fa fa-plus"></i> Add Topics
    </a>
    <a href="{{ url('learning/training-materials/' . $material->id . '/edit') }}" class="btn btn-primary" style="padding: 12px 30px;">
        <i class="fa fa-edit"></i> Edit Training Material
    </a>
</div>
@endsection
