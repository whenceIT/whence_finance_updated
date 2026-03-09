@extends('layouts.learning')

@section('title', 'Take Quiz - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $quiz->topic->trainingMaterial->title ?? 'Course', 'url' => url('learning/training-materials/' . $quiz->topic->trainingMaterial->id)],
    ['label' => $quiz->topic->topic_name, 'url' => url('learning/course/' . $quiz->topic->trainingMaterial->id . '/classroom?topic=' . $quiz->topic->id)],
    ['label' => 'Take Quiz', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>{{ $quiz->title }}</h1>
    <p>{{ $quiz->description }}</p>
</div>

@if($passed)
<div class="alert alert-success">
    <i class="fa fa-check-circle"></i> 
    <strong>Congratulations!</strong> You have already passed this quiz with a score of 
    {{ $passed->percentage }}%.
</div>
@endif

@if($attempts->count() > 0 && !$passed)
<div class="alert alert-info">
    <i class="fa fa-info-circle"></i> 
    You have attempted this quiz {{ $attempts->count() }} time(s). 
    Previous best score: {{ $attempts->max('percentage') }}%
</div>
@endif

<form id="quiz-form" method="POST" action="{{ route('learning.quizzes.submit', ['quizId' => $quiz->id]) }}">
    @csrf
    <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
    
    @foreach($quiz->questions as $qIndex => $question)
    <div class="question-card" id="question-{{ $question->id }}">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Question {{ $qIndex + 1 }}</h4>
                <span class="badge badge-primary">{{ $question->points }} point(s)</span>
            </div>
            <div class="card-body">
                <p class="question-text">{{ $question->question }}</p>
                
                <div class="options-list">
                    @foreach($question->options as $oIndex => $option)
                    <div class="option-item">
                        <label>
                            <input type="radio" name="answers[{{ $question->id }}]" 
                                   value="{{ $option->id }}" 
                                   {{ $attempts->count() > 0 && $passed ? 'disabled' : '' }}>
                            <span class="option-text">{{ chr(65 + $oIndex) }}. {{ $option->option_text }}</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach
    
    @if(!$passed)
    <div class="text-center" style="margin-top: 30px;">
        <button type="submit" class="btn btn-success btn-lg">
            <i class="fa fa-paper-plane"></i> Submit Quiz
        </button>
    </div>
    @endif
</form>

@if($passed)
<div class="text-center" style="margin-top: 30px;">
    <a href="{{ url('learning/course/' . $quiz->topic->trainingMaterial->id . '/classroom') }}" class="btn btn-primary btn-lg">
        <i class="fa fa-arrow-left"></i> Back to Classroom
    </a>
</div>
@endif



<script>
// Form validation to ensure all questions are answered before submission
$('#quiz-form').on('submit', function(e) {
    const answers = {};
    $('input[name^="answers["]:checked').each(function() {
        answers[$(this).attr('name').match(/answers\[(\d+)\]/)[1]] = $(this).val();
    });
    
    if (Object.keys(answers).length < {{ $quiz->questions->count() }}) {
        e.preventDefault();
        showFlashMessage('warning', 'Incomplete Quiz', 'Please answer all questions before submitting.', 'fa-exclamation-circle');
        return false;
    }
});
</script>

<style>
.question-card {
    margin-bottom: 20px;
}

.card {
    border: 1px solid #ddd;
    border-radius: 8px;
}

.card-header {
    background: #f8f9fa;
    padding: 15px 20px;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-body {
    padding: 20px;
}

.question-text {
    font-size: 16px;
    font-weight: 500;
    margin-bottom: 20px;
}

.option-item {
    padding: 10px 15px;
    margin-bottom: 8px;
    background: #f8f9fa;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.option-item:hover {
    background: #e9ecef;
}

.option-item label {
    cursor: pointer;
    width: 100%;
    margin: 0;
}

.option-item input[type="radio"] {
    margin-right: 10px;
}

.question-review {
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 6px;
    background: #f8f9fa;
}

.question-review.correct {
    border-left: 4px solid #28a745;
}

.question-review.incorrect {
    border-left: 4px solid #dc3545;
}
</style>
@endsection
