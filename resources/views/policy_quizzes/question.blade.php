@extends('layouts.master')

@section('title')
Quiz - {{ $quiz->title }}
@endsection

@section('content')
<div class="content">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $quiz->title }}</h3>
                        <div class="box-tools pull-right">
                            <span class="label label-info"><i class="fa fa-clock-o"></i> Time Remaining: <span id="timer">{{ floor($remainingSeconds / 60) }}:{{ sprintf('%02d', $remainingSeconds % 60) }}</span></span>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="progress" style="margin-bottom: 20px;">
                            <div class="progress-bar progress-bar-primary" role="progressbar" 
                                 style="width: {{ ($currentQuestionIndex + 1) / $totalQuestions * 100 }}%">
                                Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}
                            </div>
                        </div>
                        
                        <form id="quiz-form">
                            @csrf
                            <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
                            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                            <input type="hidden" name="current_question_index" value="{{ $currentQuestionIndex }}">
                            
                            <div id="questions-container">
                                @foreach($questions as $index => $question)
                                    <div class="question-card" style="display: {{ $index == $currentQuestionIndex ? 'block' : 'none' }}; margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                                        <h4 class="question-text" style="font-weight: bold; margin-bottom: 15px;">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                        </h4>
                                        
                                        @foreach(['A', 'B', 'C', 'D'] as $letter)
                                            <div class="radio" style="margin-bottom: 12px; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                                <label style="font-size: 14px;">
                                                    <input type="radio" 
                                                           name="answer_{{ $index }}" 
                                                           value="{{ $letter }}"
                                                           {{ $attempt->answers()->where('question_id', $question->id)->where('selected_answer', $letter)->exists() ? 'checked' : '' }}
                                                           class="answer-radio"
                                                           data-question-id="{{ $question->id }}"
                                                           data-question-index="{{ $index }}">
                                                    <strong>{{ $letter }}.</strong> 
                                                    <span>{{ $question->{'option_' . strtolower($letter)} }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        
                                        @if($question->policy_link)
                                            <div class="alert alert-warning" style="margin-top: 10px;">
                                                <i class="fa fa-book"></i> 
                                                <a href="{{ $question->policy_link }}" target="_blank" class="btn btn-xs btn-warning">View Related Policy</a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="text-center" style="margin-top: 20px;">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fa fa-check-circle"></i> Submit Quiz
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="box-footer">
                        <button type="button" id="prev-btn" class="btn btn-default btn-lg" {{ $currentQuestionIndex == 0 ? 'disabled' : '' }}>
                            <i class="fa fa-arrow-left"></i> Previous
                        </button>
                        <button type="button" id="next-btn" class="btn btn-primary btn-lg" {{ $currentQuestionIndex >= $totalQuestions - 1 ? 'disabled' : '' }}>
                            Next <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@section('footer-scripts')
<script>
$(document).ready(function() {
    const totalQuestions = {{ $totalQuestions }};
    let currentQuestionIndex = {{ $currentQuestionIndex }};
    let remainingSeconds = {{ $remainingSeconds }};
    const quizId = {{ $quiz->id }};
    const attemptId = {{ $attempt->id }};
    let timerInterval;
    
    // Timer
    function updateTimer() {
        remainingSeconds--;
        const mins = Math.floor(remainingSeconds / 60);
        const secs = remainingSeconds % 60;
        $('#timer').text(mins + ':' + (secs < 10 ? '0' : '') + secs);
        
        if(remainingSeconds <= 0) {
            clearInterval(timerInterval);
            submitQuiz();
        }
    }
    
    timerInterval = setInterval(updateTimer, 1000);
    
    // Auto-save answer
    $(document).on('change', '.answer-radio', function() {
        const questionIndex = $(this).data('question-index');
        const questionId = $(this).data('question-id');
        const answer = $(this).val();
        const token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/answer',
            method: 'POST',
            data: {
                _token: token,
                question_id: questionId,
                answer: answer
            },
            success: function(response) {
                console.log('Answer saved for question', questionIndex + 1);
            },
            error: function(xhr) {
                console.error('Error saving answer');
            }
        });
    });
    
    // Navigation
    $('#prev-btn').on('click', function() {
        if(currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    });
    
    $('#next-btn').on('click', function() {
        if(currentQuestionIndex < totalQuestions - 1) {
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }
    });
    
    function showQuestion(index) {
        $('.question-card').hide();
        $('.question-card').eq(index).show();
        
        // Update button states
        $('#prev-btn').prop('disabled', index === 0);
        $('#next-btn').prop('disabled', index >= totalQuestions - 1);
        
        // Update progress
        const percent = ((index + 1) / totalQuestions) * 100;
        $('.progress-bar').css('width', percent + '%').text('Question ' + (index + 1) + ' of ' + totalQuestions);
    }
    
    // Submit quiz
    function submitQuiz() {
        const token = $('input[name="_token"]').val() || $('meta[name="csrf-token"]').attr('content');
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/submit',
            method: 'POST',
            data: {
                _token: token
            },
            success: function(response) {
                window.location.href = '/policy-quizzes/' + quizId + '/results';
            },
            error: function(xhr) {
                console.error('Error submitting quiz');
                alert('Error submitting quiz. Please try again.');
            }
        });
    }
    
    // Prevent leaving
    window.addEventListener('beforeunload', function(e) {
        if(remainingSeconds > 0) {
            e.preventDefault();
            e.returnValue = 'You have an active quiz in progress. Are you sure you want to leave?';
        }
    });
});
</script>
@endsection