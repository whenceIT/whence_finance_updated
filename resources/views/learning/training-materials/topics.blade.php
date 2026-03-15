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

.btn-info {
    background: #17a2b8;
    color: white;
    border: none;
}

.btn-info:hover {
    background: #138496;
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
    <h1>Manage Topics & Quizzes</h1>
    <p>Configure learning topics and quizzes for your training materials</p>
</div>


<div style="margin-top: 20px; display: flex; gap: 12px; justify-content: flex-end; align-items: center;">
    <a href="{{ route('learning.training-materials.add-topics', ['materialId' => $material->id]) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; font-size: 14px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(40, 167, 69, 0.45)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(40, 167, 69, 0.35)'">
        <i class="fa fa-plus-circle"></i> Add Topics
    </a>
    <a href="{{ url('learning/training-materials/' . $material->id . '/edit') }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 10px 24px; background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; font-size: 14px; box-shadow: 0 4px 12px rgba(74, 144, 226, 0.35); transition: all 0.3s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(74, 144, 226, 0.45)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(74, 144, 226, 0.35)'">
        <i class="fa fa-edit"></i> Edit Material
    </a>
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
                <a href="{{ url('/learning/training-materials/topic/' . $topic->id . '/edit') }}" class="btn btn-secondary btn-sm">
                    <i class="fa fa-edit"></i> Edit Topic
                </a>
                @if($topic->quiz)
                    <button type="button" class="btn btn-info btn-sm" onclick="previewQuiz({{ $topic->id }}, '{{ addslashes($topic->quiz->title) }}', {{ $topic->quiz->id }})">
                        <i class="fa fa-eye"></i> Preview Quiz
                    </button>
                    <a href="{{ url('/learning/training-materials/topic/' . $topic->id . '/quiz/manage') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-edit"></i> Edit Quiz
                    </a>
                @else
                    <a href="{{ url('/learning/training-materials/topic/' . $topic->id . '/quiz/manage') }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Add Quiz
                    </a>
                @endif
                <a href="{{ url('learning/course/' . $material->id . '/classroom?topic=' . $topic->id . '&preview=1') }}" class="btn btn-secondary btn-sm" target="_blank">
                    <i class="fa fa-play"></i> Preview
                </a>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <i class="fa fa-folder-open"></i>
            <h3>No Topics Found</h3>
            <p>This training material doesn't have any topics yet. Add topics to create a structured learning experience for your users.</p>
            <a href="{{ route('learning.training-materials.add-topics', ['materialId' => $material->id]) }}" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 28px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; text-decoration: none; font-size: 14px; box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35); transition: all 0.3s; margin-top: 20px;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 16px rgba(40, 167, 69, 0.45)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(40, 167, 69, 0.35)'">
                <i class="fa fa-plus-circle"></i> Add First Topic
            </a>
        </div>
    @endif
</div>

<!-- Quiz Preview Modal -->
<div id="quizPreviewModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999; overflow-y: auto;" onclick="closeQuizPreview(event)">
    <div style="background: white; max-width: 700px; margin: 50px auto; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.3);" onclick="event.stopPropagation()">
        <div style="background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%); padding: 20px 25px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: white; font-size: 18px;">
                <i class="fa fa-list-alt"></i> <span id="quizPreviewTitle">Quiz Preview</span>
            </h3>
            <button onclick="closeQuizPreviewModal()" style="background: rgba(255,255,255,0.2); border: none; color: white; width: 32px; height: 32px; border-radius: 50%; cursor: pointer; font-size: 16px;">
                <i class="fa fa-times"></i>
            </button>
        </div>
        <div id="quizPreviewContent" style="padding: 25px; max-height: 60vh; overflow-y: auto;">
            <!-- Quiz questions will be loaded here -->
            <div style="text-align: center; padding: 40px;">
                <i class="fa fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i>
                <p style="margin-top: 15px; color: var(--text-secondary);">Loading quiz...</p>
            </div>
        </div>
        <div style="padding: 15px 25px; background: var(--light-bg); border-top: 1px solid var(--border-color); display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="closeQuizPreviewModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Quiz Preview Modal
function previewQuiz(topicId, quizTitle, quizId) {
    document.getElementById('quizPreviewTitle').textContent = quizTitle;
    document.getElementById('quizPreviewModal').style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Load quiz questions via AJAX
    fetchQuizQuestions(quizId);
}

function closeQuizPreviewModal() {
    document.getElementById('quizPreviewModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

function closeQuizPreview(event) {
    if (event.target.id === 'quizPreviewModal') {
        closeQuizPreviewModal();
    }
}

function fetchQuizQuestions(quizId) {
    var contentDiv = document.getElementById('quizPreviewContent');
    contentDiv.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i><p style="margin-top: 15px; color: var(--text-secondary);">Loading quiz...</p></div>';
    
    // Make AJAX request to get quiz questions
    fetch('{{ url('learning/quiz') }}/' + quizId + '/questions', {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.questions && data.questions.length > 0) {
            renderQuizQuestions(data.questions);
        } else {
            contentDiv.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-exclamation-circle" style="font-size: 48px; color: var(--text-secondary);"></i><p style="margin-top: 15px; color: var(--text-secondary);">No questions found for this quiz.</p></div>';
        }
    })
    .catch(error => {
        console.error('Error loading quiz:', error);
        // Try to load from the page data if available
        loadQuizFromPage(quizId);
    });
}

function loadQuizFromPage(quizId) {
    var contentDiv = document.getElementById('quizPreviewContent');
    
    // Check if quiz data is available in the page
    @if(isset($topicQuizzes))
    var quizData = @json($topicQuizzes);
    if (quizData && quizData[quizId]) {
        renderQuizQuestions(quizData[quizId].questions || []);
        return;
    }
    @endif
    
    contentDiv.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-exclamation-circle" style="font-size: 48px; color: var(--text-secondary);"></i><p style="margin-top: 15px; color: var(--text-secondary);">Unable to load quiz questions.</p><button onclick="window.location.href=\'{{ url('learning/training-materials/topic') }}/' + quizId + '/quiz/manage'\'" style="margin-top: 15px; padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer;">Manage Quiz</button></div>';
}

function renderQuizQuestions(questions) {
    var contentDiv = document.getElementById('quizPreviewContent');
    
    if (!questions || questions.length === 0) {
        contentDiv.innerHTML = '<div style="text-align: center; padding: 40px;"><i class="fa fa-exclamation-circle" style="font-size: 48px; color: var(--text-secondary);"></i><p style="margin-top: 15px; color: var(--text-secondary);">No questions found for this quiz.</p></div>';
        return;
    }
    
    var html = '<div style="display: flex; flex-direction: column; gap: 20px;">';
    
    questions.forEach(function(question, index) {
        var questionNumber = index + 1;
        var typeLabel = question.question_type === 'multiple_choice' ? 'Multiple Choice' : (question.question_type === 'true_false' ? 'True/False' : 'Short Answer');
        var points = question.points || 1;
        
        html += '<div style="background: var(--light-bg); border-radius: 8px; padding: 20px; border-left: 4px solid var(--primary-color);">';
        html += '<div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">';
        html += '<span style="background: var(--primary-color); color: white; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">Q' + questionNumber + '</span>';
        html += '<span style="color: var(--text-secondary); font-size: 12px;">' + typeLabel + ' • ' + points + ' point' + (points > 1 ? 's' : '') + '</span>';
        html += '</div>';
        html += '<h4 style="margin: 0 0 15px 0; font-size: 16px; color: var(--text-primary);">' + question.question + '</h4>';
        
        if (question.options && question.options.length > 0) {
            html += '<div style="display: flex; flex-direction: column; gap: 8px;">';
            question.options.forEach(function(option, optIndex) {
                var isCorrect = option.is_correct;
                html += '<div style="display: flex; align-items: center; gap: 10px; padding: 10px 15px; background: ' + (isCorrect ? 'rgba(40, 167, 69, 0.1)' : 'white') + '; border: 1px solid ' + (isCorrect ? '#28a745' : 'var(--border-color)') + '; border-radius: 6px;">';
                html += '<span style="width: 24px; height: 24px; border-radius: 50%; background: ' + (isCorrect ? '#28a745' : '#e9ecef') + '; color: ' + (isCorrect ? 'white' : '#6c757d') + '; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600;">' + String.fromCharCode(65 + optIndex) + '</span>';
                html += '<span style="flex: 1; color: ' + (isCorrect ? '#28a745' : 'var(--text-primary)') + '; font-weight: ' + (isCorrect ? '600' : '400') + ';">' + option.option_text + '</span>';
                if (isCorrect) {
                    html += '<i class="fa fa-check-circle" style="color: #28a745;"></i>';
                }
                html += '</div>';
            });
            html += '</div>';
        }
        
        if (question.correct_answer) {
            html += '<div style="margin-top: 12px; padding: 10px; background: rgba(40, 167, 69, 0.1); border-radius: 6px;">';
            html += '<span style="font-size: 12px; color: #28a745; font-weight: 600;"><i class="fa fa-check"></i> Correct Answer: </span>';
            html += '<span style="color: var(--text-primary);">' + question.correct_answer + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
    });
    
    html += '</div>';
    html += '<div style="margin-top: 20px; padding: 15px; background: var(--light-bg); border-radius: 8px; text-align: center;">';
    html += '<span style="color: var(--text-secondary); font-size: 14px;">Total Questions: </span>';
    html += '<span style="color: var(--text-primary); font-weight: 600;">' + questions.length + '</span>';
    html += '</div>';
    
    contentDiv.innerHTML = html;
}
</script>
@endsection
