@extends('layouts.learning')

@section('title', 'Take Quiz - Whence Learn')

@section('content')
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

<form id="quiz-form">
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

<div id="results-modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Quiz Results</h4>
            </div>
            <div class="modal-body" id="results-content">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" onclick="location.reload()">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$('#quiz-form').on('submit', function(e) {
    e.preventDefault();
    
    const answers = {};
    $('input[name^="answers["]:checked').each(function() {
        answers[$(this).attr('name').match(/answers\[(\d+)\]/)[1]] = $(this).val();
    });
    
    if (Object.keys(answers).length < {{ $quiz->questions->count() }}) {
        showFlashMessage('warning', 'Incomplete Quiz', 'Please answer all questions before submitting.', 'fa-exclamation-circle');
        return;
    }
    
    $.ajax({
        url: '{{ url('learning/quiz/' . $quiz->id . '/submit') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            answers: answers
        },
        success: function(response) {
            if (response.success) {
                let resultsHtml = `
                    <div class="text-center mb-4">
                        <h2 class="${response.passed ? 'text-success' : 'text-danger'}">
                            ${response.passed ? '<i class="fa fa-trophy"></i> Congratulations!' : '<i class="fa fa-times-circle"></i> Keep Trying!'}
                        </h2>
                        <div style="font-size: 48px; font-weight: bold; color: ${response.passed ? '#28a745' : '#dc3545'};">
                            ${response.percentage}%
                        </div>
                        <p>You scored ${response.score} out of ${response.total_points} points</p>
                        <p class="text-muted">Passing score: ${response.passing_score}%</p>
                    </div>
                    <hr>
                    <h4>Question Review</h4>
                `;
                
                @foreach($quiz->questions as $question)
                const q{{ $question->id }} = response.results[{{ $question->id }}];
                resultsHtml += `
                    <div class="question-review ${q{{ $question->id }}.correct ? 'correct' : 'incorrect'}">
                        <p><strong>Q{{ $loop->iteration }}: ${q{{ $question->id }}.correct ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>'}</strong></p>
                    </div>
                `;
                @endforeach
                
                $('#results-content').html(resultsHtml);
                $('#results-modal').modal('show');
            }
        },
        error: function() {
            showFlashMessage('error', 'Error', 'An error occurred while submitting the quiz.', 'fa-times-circle');
        }
    });
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
