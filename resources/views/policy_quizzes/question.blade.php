@extends('layouts.master')


@section('content')
<div class="content">
    <section class="content">
        <div class="row">
            <div class="col-md-12">

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                @endif

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
                                 style="width: {{ ($currentQuestionIndex < $totalQuestions ? ($currentQuestionIndex + 1) / $totalQuestions * 100 : 100) }}%">
                                Question {{ $currentQuestionIndex < $totalQuestions ? $currentQuestionIndex + 1 : 'Summary' }} of {{ $totalQuestions + 1 }}
                            </div>
                        </div>
                        
                        <form id="quiz-form">
                            @csrf
                            <input type="hidden" name="attempt_id" value="{{ $attempt->id }}">
                            <input type="hidden" name="quiz_id" value="{{ $quiz->id }}">
                            <input type="hidden" name="current_question_index" value="{{ $currentQuestionIndex }}">
                            
                            <div id="questions-container">
                                @foreach($questions as $index => $question)
                                    <div class="question-card" data-question-index="{{ $index }}" style="display: {{ $index == $currentQuestionIndex ? 'block' : 'none' }}; margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                                        <h4 class="question-text" style="font-weight: bold; margin-bottom: 15px;">
                                            {{ $index + 1 }}. {{ $question->question_text }}
                                        </h4>
                                        
                                        @foreach(['A', 'B', 'C', 'D'] as $letter)
                                            <div class="radio" style="margin-bottom: 12px; padding: 10px; border: 1px solid #eee; border-radius: 4px;">
                                                <label style="font-size: 14px; cursor: pointer;">
                                                    <input type="radio" 
                                                           name="answer_{{ $index }}" 
                                                           value="{{ $letter }}"
                                                           class="answer-radio"
                                                           data-question-id="{{ $question->id }}"
                                                           data-question-index="{{ $index }}"
                                                           data-letter="{{ $letter }}">
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
                                
                                <div class="question-card summary-card" data-question-index="summary" style="display: none; margin-bottom: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
                                    <h4 class="question-text" style="font-weight: bold; margin-bottom: 15px;">
                                        Summary
                                    </h4>
                                    
                                    <div class="alert alert-info">
                                        <i class="fa fa-info-circle"></i> 
                                        Review your answers before completing the quiz.
                                    </div>
                                    
                                    <div id="answers-summary">
                                        @foreach($questions as $index => $question)
                                            <!-- <div class="answer-summary" style="margin-bottom: 10px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
                                                <strong>{{ $index + 1 }}.</strong> 
                                                {{ $question->question_text }}
                                                <br>
                                                <small>Your answer: 
                                                    @if($attempt->answers()->where('question_id', $question->id)->exists())
                                                        {{ $attempt->answers()->where('question_id', $question->id)->first()->selected_answer }}
                                                    @else
                                                        <span style="color: #999;">Not answered</span>
                                                    @endif
                                                </small>
                                            </div> -->
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-center" style="margin-top: 20px;">
                                <button type="submit" id="complete-btn" class="btn btn-success btn-lg" style="display: none;">
                                    <i class="fa fa-check-circle"></i> Complete Quiz
                                </button>
                            </div>
                        </form>
                        </div>
                        
                        <div class="box-footer">
                            <button type="button" id="prev-btn" class="btn btn-default btn-lg">
                                <i class="fa fa-arrow-left"></i> Previous
                            </button>
                            <button type="button" id="next-btn" class="btn btn-primary btn-lg">
                                Next <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    const quizId = {{ $quiz->id }};
    let currentQuestionIndex = {{ $currentQuestionIndex }};
    const totalQuestions = {{ $totalQuestions }};
    let remainingSeconds = {{ $remainingSeconds }};
    let timerInterval;
    
    function saveAnswer(questionId, letter, index) {
        const token = $('input[name="_token"]').val();
        
        if (!token) {
            console.error('CSRF token not found');
            return;
        }
        
        console.log('Saving answer:', { questionId, answer: letter, index });
        
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/answer',
            method: 'POST',
            data: {
                _token: token,
                question_id: questionId,
                answer: letter
            },
            success: function(response) {
                console.log('Answer saved for question', index + 1, response);
            },
            error: function(xhr) {
                console.error('Error saving answer', xhr.responseText);
                alert('Error saving answer. Please try again.');
            }
        });
    }
    
    function submitAnswer(index) {
        const questionCard = $('.question-card[data-question-index="' + index + '"]');
        const checkedRadio = questionCard.find('.answer-radio:checked');
        
        if (checkedRadio.length) {
            const questionId = checkedRadio.data('question-id');
            const letter = checkedRadio.data('letter');
            saveAnswer(questionId, letter, index);
        }
    }
    
    $(document).on('change', '.answer-radio', function() {
        const questionId = $(this).data('question-id');
        const letter = $(this).data('letter');
        const index = $(this).data('question-index');
        
        saveAnswer(questionId, letter, index);
    });
    
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
    
    $('#prev-btn').on('click', function() {
        if(currentQuestionIndex > 0) {
            if (currentQuestionIndex < totalQuestions) {
                submitAnswer(currentQuestionIndex);
            }
            currentQuestionIndex--;
            showQuestion(currentQuestionIndex);
        }
    });
    
    $('#next-btn').on('click', function() {
        if (currentQuestionIndex < totalQuestions) {
            submitAnswer(currentQuestionIndex);
            currentQuestionIndex++;
            showQuestion(currentQuestionIndex);
        }
    });
    
    function showQuestion(index) {
        $('.question-card').hide();
        $('.summary-card').hide();
        
        if (index === totalQuestions) {
            $('.summary-card').show();
            $('#prev-btn').prop('disabled', false);
            $('#next-btn').hide();
            $('#complete-btn').show();
        } else {
            $('.question-card').eq(index).show();
            const isFirstQuestion = index === 0;
            
            $('#prev-btn').prop('disabled', isFirstQuestion);
            $('#next-btn').show();
            $('#complete-btn').hide();
        }
        
        const percent = ((index < totalQuestions ? index + 1 : totalQuestions) / (totalQuestions + 1)) * 100;
        $('.progress-bar').css('width', percent + '%').text('Question ' + (index < totalQuestions ? index + 1 : 'Summary') + ' of ' + (totalQuestions + 1));
        
        $('input[name="current_question_index"]').val(index);
    }
    
    $('#quiz-form').on('submit', function(e) {
        e.preventDefault();
        submitQuiz();
    });
    
    function submitQuiz() {
        const token = $('input[name="_token"]').val();
        
        if (!token) {
            alert('CSRF token missing. Please refresh the page.');
            return;
        }
        
        for (let i = 0; i < totalQuestions; i++) {
            submitAnswer(i);
        }
        
        $('#complete-btn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        
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
                console.error('Error submitting quiz', xhr.responseText);
                alert('Error submitting quiz. Please try again.');
                $('#complete-btn').prop('disabled', false).html('<i class="fa fa-check-circle"></i> Complete Quiz');
            }
        });
    }
    
    $('.answer-radio:checked').each(function() {
        const questionId = $(this).data('question-id');
        const letter = $(this).data('letter');
        const index = $(this).data('question-index');
        saveAnswer(questionId, letter, index);
    });
    
    if (currentQuestionIndex === totalQuestions) {
        showQuestion(totalQuestions);
    }
});
</script>
@endsection