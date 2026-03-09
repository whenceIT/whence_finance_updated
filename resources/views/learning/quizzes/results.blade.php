@extends('layouts.learning')

@section('title', 'Quiz Results - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => $quiz->topic->trainingMaterial->title ?? 'Course', 'url' => url('learning/training-materials/' . $quiz->topic->trainingMaterial->id)],
    ['label' => $quiz->topic->topic_name, 'url' => url('learning/course/' . $quiz->topic->trainingMaterial->id . '/classroom?topic=' . $quiz->topic->id)],
    ['label' => 'Quiz Results', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>{{ $quiz->title }} - Results</h1>
</div>

<div class="results-container">
    <div class="text-center mb-4">
        <h2 class="{{ $passed ? 'text-success' : 'text-danger' }}">
            {{ $passed ? '<i class="fa fa-trophy"></i> Congratulations!' : '<i class="fa fa-times-circle"></i> Keep Trying!' }}
        </h2>
        <div style="font-size: 48px; font-weight: bold; color: {{ $passed ? '#28a745' : '#dc3545' }};">
            {{ $percentage }}%
        </div>
        <p>You scored {{ $score }} out of {{ $totalPoints }} points</p>
        <p class="text-muted">Passing score: {{ $quiz->passing_score }}%</p>
    </div>

    <hr>

    <h4>Question Review</h4>
    @foreach($quiz->questions as $qIndex => $question)
    <div class="question-review {{ $results[$question->id]['correct'] ? 'correct' : 'incorrect' }}">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">
                    Question {{ $qIndex + 1 }} 
                    <span class="badge badge-primary">{{ $question->points }} point(s)</span>
                    {{ $results[$question->id]['correct'] ? 
                        '<span class="badge badge-success float-right"><i class="fa fa-check"></i> Correct</span>' : 
                        '<span class="badge badge-danger float-right"><i class="fa fa-times"></i> Incorrect</span>' }}
                </h5>
            </div>
            <div class="card-body">
                <p class="question-text">{{ $question->question }}</p>
                
                <div class="options-list">
                    @foreach($question->options as $oIndex => $option)
                    <div class="option-item {{ 
                        $option->is_correct ? 'correct-option' : 
                        ($results[$question->id]['selected_option'] == $option->id ? 'selected-option' : '') 
                    }}">
                        <label>
                            <input type="radio" name="answers[{{ $question->id }}]" 
                                   value="{{ $option->id }}" 
                                   disabled 
                                   {{ $option->is_correct ? 'checked' : '' }}>
                            <span class="option-text">{{ chr(65 + $oIndex) }}. {{ $option->option_text }}</span>
                            @if($option->is_correct)
                                <i class="fa fa-check text-success"></i>
                            @elseif($results[$question->id]['selected_option'] == $option->id)
                                <i class="fa fa-times text-danger"></i>
                            @endif
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <div class="text-center" style="margin-top: 30px;">
        <a href="{{ url('learning/course/' . $quiz->topic->trainingMaterial->id . '/classroom') }}" class="btn btn-primary btn-lg">
            <i class="fa fa-arrow-left"></i> Back to Classroom
        </a>
    </div>
</div>

<style>
.results-container {
    max-width: 800px;
    margin: 0 auto;
}

.question-review {
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

.question-review.correct {
    border-left: 4px solid #28a745;
}

.question-review.incorrect {
    border-left: 4px solid #dc3545;
}

.correct-option {
    background: #d4edda !important;
}

.selected-option {
    background: #fff3cd !important;
}
</style>
@endsection
