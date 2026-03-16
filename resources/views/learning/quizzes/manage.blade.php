@extends('layouts.learning')

@section('title', 'Manage Quiz - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $topic->trainingMaterial->title ?? 'Course', 'url' => url('learning/training-materials/' . $topic->trainingMaterial->id)],
    ['label' => 'Manage Topics & Quizzes', 'url' => url('learning/training-materials/' . $topic->trainingMaterial->id . '/topics')],
    ['label' => 'Manage Quiz', 'url' => '']
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

/* Quiz Card Styles */
.quiz-builder-card {
    background: white;
    border-radius: 12px;
    box-shadow: var(--shadow);
    overflow: hidden;
}

.quiz-builder-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
    color: white;
    padding: 24px 30px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.quiz-builder-header h2 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 12px;
}

.quiz-builder-header h2 i {
    font-size: 24px;
}

.quiz-topic-badge {
    background: rgba(255,255,255,0.2);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
}

.quiz-builder-body {
    padding: 30px;
}

/* Form Styles */
.form-section {
    margin-bottom: 30px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: var(--primary-color);
}

.form-card {
    background: var(--light-bg);
    border-radius: 10px;
    padding: 24px;
    border: 1px solid var(--border-color);
}

.form-group {
    margin-bottom: 20px;
}

.form-group:last-child {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-weight: 600;
    margin-bottom: 8px;
    color: var(--text-primary);
    font-size: 14px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
    background: white;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.15);
}

textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Questions Section */
.questions-section {
    margin-top: 40px;
}

.question-card {
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.3s;
    position: relative;
}

.question-card:hover {
    border-color: var(--primary-color);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}

.question-card-number {
    position: absolute;
    top: -12px;
    left: 20px;
    background: var(--primary-color);
    color: white;
    padding: 4px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
}

.question-header .form-group {
    flex: 1;
    margin-right: 16px;
    margin-bottom: 0;
}

.btn-remove-question {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

.btn-remove-question:hover {
    background: #dc3545;
    color: white;
    transform: scale(1.1);
}

/* Options */
.options-container {
    margin-top: 16px;
}

.options-label {
    font-weight: 600;
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.option-item {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 12px;
    padding: 12px;
    background: var(--light-bg);
    border-radius: 8px;
    border: 1px solid var(--border-color);
    transition: all 0.2s;
}

.option-item:hover {
    border-color: var(--primary-color);
}

.option-radio {
    width: 20px;
    height: 20px;
    cursor: pointer;
}

.option-input {
    flex: 1;
}

.btn-remove-option {
    background: transparent;
    color: #dc3545;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    opacity: 0.6;
}

.btn-remove-option:hover {
    background: rgba(220, 53, 69, 0.1);
    opacity: 1;
}

.btn-add-option {
    background: transparent;
    color: var(--primary-color);
    border: 1px dashed var(--primary-color);
    padding: 10px 20px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    margin-top: 8px;
}

.btn-add-option:hover {
    background: rgba(74, 144, 226, 0.1);
}

/* Action Buttons */
.quiz-builder-footer {
    padding: 20px 30px;
    border-top: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--light-bg);
}

.btn-add-question {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.35);
    transition: all 0.3s;
}

.btn-add-question:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(40, 167, 69, 0.45);
}

.btn-save-quiz {
    background: linear-gradient(135deg, var(--primary-color) 0%, #357abd 100%);
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.35);
    transition: all 0.3s;
}

.btn-save-quiz:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(74, 144, 226, 0.45);
}

.btn-back {
    background: white;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    padding: 12px 24px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 500;
    font-size: 14px;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    transition: all 0.3s;
}

.btn-back:hover {
    background: var(--light-bg);
    border-color: var(--text-secondary);
}

/* Empty State */
.empty-questions {
    text-align: center;
    padding: 60px 20px;
    background: var(--light-bg);
    border-radius: 12px;
    border: 2px dashed var(--border-color);
}

.empty-questions i {
    font-size: 64px;
    color: var(--text-secondary);
    margin-bottom: 20px;
}

.empty-questions h3 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text-primary);
}

.empty-questions p {
    color: var(--text-secondary);
    max-width: 400px;
    margin: 0 auto 20px;
}

/* Row adjustments */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: 0 -15px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 0 15px;
}

.col-md-12 {
    flex: 0 0 100%;
    max-width: 100%;
    padding: 0 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .quiz-builder-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .quiz-builder-body {
        padding: 20px;
    }
    
    .question-header {
        flex-direction: column;
    }
    
    .question-header .form-group {
        margin-right: 0;
        margin-bottom: 12px;
    }
    
    .btn-remove-question {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    
    .quiz-builder-footer {
        flex-direction: column;
        gap: 16px;
    }
    
    .quiz-builder-footer .btn-add-question,
    .quiz-builder-footer .btn-save-quiz {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="page-header">
    <h1>Manage Quiz</h1>
    <p>Create or edit quiz for: <strong style="color: var(--primary-color);">{{ $topic->topic_name }}</strong></p>
</div>

<form id="quiz-form" method="POST" action="{{ route('learning.quizzes.save', ['topicId' => $topic->id]) }}">
    @csrf
    <input type="hidden" name="topic_id" value="{{ $topic->id }}">
    
    <div class="quiz-builder-card">
        <!-- Header -->
        <div class="quiz-builder-header">
            <h2><i class="fa fa-graduation-cap"></i> Quiz Builder</h2>
            <span class="quiz-topic-badge">{{ $topic->trainingMaterial->title ?? 'Course' }}</span>
        </div>
        
        <!-- Body -->
        <div class="quiz-builder-body">
            <!-- Quiz Details Section -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fa fa-info-circle"></i>
                    Quiz Details
                </div>
                <div class="form-card">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Quiz Title *</label>
                                <input type="text" name="quiz_title" class="form-control" 
                                       value="{{ $quiz->title ?? 'Quiz for ' . $topic->topic_name }}" required
                                       placeholder="Enter quiz title">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="quiz_description" class="form-control" rows="3"
                                          placeholder="Enter quiz description (optional)">{{ $quiz->description ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passing Score (%) *</label>
                                <input type="number" name="passing_score" class="form-control" 
                                       value="{{ $quiz->passing_score ?? 70 }}" min="0" max="100" required
                                       placeholder="70">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Time Limit (minutes)</label>
                                <input type="number" name="time_limit" class="form-control" 
                                       value="{{ $quiz->time_limit ?? '' }}" min="1" 
                                       placeholder="Optional">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Questions Section -->
            <div class="questions-section">
                <div class="form-section-title">
                    <i class="fa fa-question-circle"></i>
                    Questions
                </div>
                
                <div id="questions-container">
                    @if($quiz && $quiz->questions->count() > 0)
                        @foreach($quiz->questions as $qIndex => $question)
                        <div class="question-card" data-index="{{ $qIndex }}">
                            <span class="question-card-number">Question {{ $qIndex + 1 }}</span>
                            <div class="question-header">
                                <div class="form-group">
                                    <input type="text" name="questions[{{ $qIndex }}][text]" 
                                           class="form-control" 
                                           value="{{ $question->question }}" required
                                           placeholder="Enter your question">
                                </div>
                                <button type="button" class="btn-remove-question" 
                                        onclick="removeQuestion({{ $qIndex }})" title="Remove Question">
                                    <i class="fa fa-times"></i>
                                </button>
                            </div>
                            
                            <input type="hidden" name="questions[{{ $qIndex }}][type]" value="multiple_choice">
                            <input type="hidden" name="questions[{{ $qIndex }}][points]" value="1">
                            
                            <div class="options-container">
                                <div class="options-label">
                                    <i class="fa fa-list-ul"></i>
                                    Options (select the correct answer)
                                </div>
                                @foreach($question->options as $oIndex => $option)
                                <div class="option-item">
                                    <input type="radio" name="questions[{{ $qIndex }}][correct_option]" 
                                           value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }}
                                           class="option-radio">
                                    <input type="text" name="questions[{{ $qIndex }}][options][]" 
                                           class="form-control option-input" 
                                           value="{{ $option->option_text }}" required
                                           placeholder="Option {{ $oIndex + 1 }}">
                                    @if($oIndex > 1)
                                    <button type="button" class="btn-remove-option" onclick="removeOption(this)">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                                @endforeach
                                
                                <button type="button" class="btn-add-option" onclick="addOption(this)">
                                    <i class="fa fa-plus"></i> Add Option
                                </button>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="empty-questions" id="empty-questions">
                            <i class="fa fa-question-circle-o"></i>
                            <h3>No Questions Yet</h3>
                            <p>Add questions to create an engaging quiz for your learners.</p>
                        </div>
                    @endif
                </div>
                
                <button type="button" class="btn-add-question" onclick="addQuestion()">
                    <i class="fa fa-plus-circle"></i> Add Question
                </button>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="quiz-builder-footer">
            <a href="{{ url('learning/training-materials/' . $topic->trainingMaterial->id . '/topics') }}" class="btn-back">
                <i class="fa fa-arrow-left"></i> Back to Topics
            </a>
            <button type="submit" class="btn-save-quiz">
                <i class="fa fa-save"></i> Save Quiz
            </button>
        </div>
    </div>
</form>

<script>
let questionCount = {{ $quiz ? $quiz->questions->count() : 0 }};

function addQuestion() {
    // Hide empty state if visible
    const emptyState = document.getElementById('empty-questions');
    if (emptyState) {
        emptyState.style.display = 'none';
    }
    
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-card" data-index="${questionCount}">
            <span class="question-card-number">Question ${questionCount + 1}</span>
            <div class="question-header">
                <div class="form-group">
                    <input type="text" name="questions[${questionCount}][text]" class="form-control" required
                           placeholder="Enter your question">
                </div>
                <button type="button" class="btn-remove-question" onclick="removeQuestion(${questionCount})" title="Remove Question">
                    <i class="fa fa-times"></i>
                </button>
            </div>
            
            <input type="hidden" name="questions[${questionCount}][type]" value="multiple_choice">
            <input type="hidden" name="questions[${questionCount}][points]" value="1">
            
            <div class="options-container">
                <div class="options-label">
                    <i class="fa fa-list-ul"></i>
                    Options (select the correct answer)
                </div>
                <div class="option-item">
                    <input type="radio" name="questions[${questionCount}][correct_option]" value="0" checked class="option-radio">
                    <input type="text" name="questions[${questionCount}][options][]" class="form-control option-input" 
                           placeholder="Option 1" required>
                </div>
                <div class="option-item">
                    <input type="radio" name="questions[${questionCount}][correct_option]" value="1" class="option-radio">
                    <input type="text" name="questions[${questionCount}][options][]" class="form-control option-input" 
                           placeholder="Option 2" required>
                </div>
                
                <button type="button" class="btn-add-option" onclick="addOption(this)">
                    <i class="fa fa-plus"></i> Add Option
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    questionCount++;
}

function removeQuestion(index) {
    const item = document.querySelector(`.question-card[data-index="${index}"]`);
    if (item) {
        item.remove();
        
        // Show empty state if no questions left
        const container = document.getElementById('questions-container');
        const remainingQuestions = container.querySelectorAll('.question-card');
        if (remainingQuestions.length === 0) {
            const emptyHtml = `
                <div class="empty-questions" id="empty-questions">
                    <i class="fa fa-question-circle-o"></i>
                    <h3>No Questions Yet</h3>
                    <p>Add questions to create an engaging quiz for your learners.</p>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', emptyHtml);
        }
    }
}

function addOption(button) {
    const optionsContainer = button.closest('.options-container');
    const questionCard = button.closest('.question-card');
    const index = questionCard.dataset.index;
    const optionItems = optionsContainer.querySelectorAll('.option-item');
    const optionCount = optionItems.length;
    
    const html = `
        <div class="option-item">
            <input type="radio" name="questions[${index}][correct_option]" value="${optionCount}" class="option-radio">
            <input type="text" name="questions[${index}][options][]" class="form-control option-input" 
                   placeholder="Option ${optionCount + 1}" required>
            <button type="button" class="btn-remove-option" onclick="removeOption(this)">
                <i class="fa fa-times"></i>
            </button>
        </div>
    `;
    button.insertAdjacentHTML('beforebegin', html);
}

function removeOption(button) {
    const optionItem = button.closest('.option-item');
    const optionsContainer = optionItem.closest('.options-container');
    const questionCard = optionsContainer.closest('.question-card');
    const index = questionCard.dataset.index;
    
    optionItem.remove();
    
    // Re-index remaining options
    const remainingOptions = optionsContainer.querySelectorAll('.option-item');
    remainingOptions.forEach((opt, idx) => {
        const radio = opt.querySelector('input[type="radio"]');
        const textInput = opt.querySelector('input[type="text"]');
        
        radio.name = `questions[${index}][correct_option]`;
        radio.value = idx;
        
        textInput.name = `questions[${index}][options][]`;
    });
}

// Form submission with validation
document.getElementById('quiz-form').addEventListener('submit', function(e) {
    const questions = document.querySelectorAll('.question-card');
    
    if (questions.length === 0) {
        e.preventDefault();
        if (typeof showFlashMessage === 'function') {
            showFlashMessage('warning', 'Validation Error', 'Please add at least one question to the quiz.', 'fa-exclamation-triangle');
        } else {
            alert('Please add at least one question to the quiz.');
        }
        return false;
    }
    
    // Check each question has at least 2 options
    let valid = true;
    questions.forEach((q, idx) => {
        const options = q.querySelectorAll('.option-item');
        if (options.length < 2) {
            valid = false;
        }
    });
    
    if (!valid) {
        e.preventDefault();
        if (typeof showFlashMessage === 'function') {
            showFlashMessage('warning', 'Validation Error', 'Each question must have at least 2 options.', 'fa-exclamation-triangle');
        } else {
            alert('Each question must have at least 2 options.');
        }
        return false;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    submitBtn.disabled = true;
});
</script>
@endsection
