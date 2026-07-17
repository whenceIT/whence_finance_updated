@extends('layouts.master')

@section('content')
<div class="content">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Question <span id="question-num">{{ $questionNum }}</span> of {{ $totalQuestions }}</h3>
                        <div class="box-tools pull-right">
                            <div class="timer-box" style="padding: 10px;">
                                <h4 style="margin: 0;">
                                    <i class="fa fa-clock-o"></i> 
                                    Time Remaining: 
                                    <span id="timer">{{ floor($remainingSeconds / 60) }}:{{ sprintf('%02d', $remainingSeconds % 60) }}</span>
                                </h4>
                            </div>
                        </div>
                    </div>
                    
                    <div class="box-body">
                        <div id="question-container">
                            <input type="hidden" id="quiz-id" value="{{ $quiz->id }}">
                            <input type="hidden" id="current-question-id" value="{{ $currentQuestion->id }}">
                            <input type="hidden" id="current-question-num" value="{{ $questionNum }}">
                            <input type="hidden" id="total-questions" value="{{ $totalQuestions }}">
                            
                            <div class="question-text" style="font-size: 18px; font-weight: bold; margin-bottom: 20px;" id="question-text">
                                {{ $currentQuestion->question_text }}
                            </div>
                            
                            <div class="options-container" style="margin-bottom: 30px;" id="options-container">
                                @php
                                    $options = [
                                        'A' => $currentQuestion->option_a,
                                        'B' => $currentQuestion->option_b,
                                        'C' => $currentQuestion->option_c,
                                        'D' => $currentQuestion->option_d,
                                    ];
                                @endphp
                                
                                @foreach($options as $letter => $option)
                                    <div class="radio" style="margin-bottom: 15px;">
                                        <label style="font-size: 16px;">
                                            <input type="radio" name="answer" value="{{ $letter }}" 
                                                   {{ $userAnswer && $userAnswer->selected_answer == $letter ? 'checked' : '' }}
                                                   class="answer-radio" data-answer="{{ $letter }}">
                                            <strong>{{ $letter }}.</strong> <span class="option-text">{{ $option }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            
                            @if($currentQuestion->policy_link)
                                <div class="policy-link" style="margin-bottom: 20px; font-style: italic;" id="policy-link-container">
                                    <i class="fa fa-book"></i> 
                                    Related policy: 
                                    <a href="{{ $currentQuestion->policy_link }}" target="_blank" class="text-primary">
                                        View Policy Document
                                    </a>
                                </div>
                            @else
                                <div class="policy-link" style="margin-bottom: 20px; font-style: italic; display: none;" id="policy-link-container">
                                    <i class="fa fa-book"></i> 
                                    Related policy: 
                                    <a href="" target="_blank" class="text-primary" id="policy-link">
                                        View Policy Document
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <div class="navigation-buttons" style="margin-top: 30px;">
                            <div class="row">
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-default btn-lg" id="prev-btn" {{ $questionNum <= 1 ? 'disabled' : '' }}>
                                        <i class="fa fa-arrow-left"></i> Previous Question
                                    </button>
                                </div>
                                
                                <div class="col-md-6 text-right">
                                    <button type="button" class="btn btn-primary btn-lg" id="next-btn">
                                        Next Question <i class="fa fa-arrow-right"></i>
                                    </button>
                                    <button type="button" class="btn btn-success btn-lg" id="submit-quiz-btn" style="display: none;">
                                        <i class="fa fa-check-circle"></i> Submit Quiz
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="progress" style="margin-top: 30px;">
                            <div class="progress-bar progress-bar-primary progress-bar-striped" 
                                 role="progressbar" 
                                 id="progress-bar"
                                 style="width: {{ ($questionNum / $totalQuestions) * 100 }}%">
                                {{ $questionNum }} / {{ $totalQuestions }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Submit Confirmation Modal -->
<div class="modal fade" id="submitModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Submit Quiz</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to submit your quiz?</p>
                <p>You have answered <span id="answered-count">{{ $questionNum }}</span> out of {{ $totalQuestions }} questions.</p>
                <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirm-submit-btn">Yes, Submit Quiz</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let remainingSeconds = {{ $remainingSeconds }};
    let timerInterval;
    let currentQuestionNum = {{ $questionNum }};
    let totalQuestions = {{ $totalQuestions }};
    let quizId = {{ $quiz->id }};
    
    // Timer functionality
    function updateTimer() {
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            // Auto-submit
            submitQuiz();
            return;
        }
        
        remainingSeconds--;
        const minutes = Math.floor(remainingSeconds / 60);
        const seconds = remainingSeconds % 60;
        $('#timer').text(minutes + ':' + (seconds < 10 ? '0' : '') + seconds);
    }
    
    // Start timer
    if (remainingSeconds > 0) {
        timerInterval = setInterval(updateTimer, 1000);
    }
    
    // Auto-save answer when user selects an option
    $('.answer-radio').on('change', function() {
        const answer = $(this).val();
        const questionId = $('#current-question-id').val();
        
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/answer',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                question_id: questionId,
                answer: answer
            },
            success: function(response) {
                console.log('Answer saved - is_correct:', response.is_correct);
            },
            error: function(xhr) {
                console.error('Error saving answer:', xhr);
            }
        });
    });
    
    // Load question via AJAX
    function loadQuestion(questionNum) {
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/question/' + questionNum + '/ajax',
            method: 'GET',
            success: function(data) {
                // Update question text
                $('#question-text').text(data.question.text);
                $('#current-question-id').val(data.question.id);
                $('#current-question-num').val(data.question_num);
                $('#question-num').text(data.question_num);
                
                // Update options
                const options = {
                    'A': data.question.option_a,
                    'B': data.question.option_b,
                    'C': data.question.option_c,
                    'D': data.question.option_d
                };
                
                let optionsHtml = '';
                for (const [letter, text] of Object.entries(options)) {
                    const checked = data.user_answer === letter ? 'checked' : '';
                    optionsHtml += `
                        <div class="radio" style="margin-bottom: 15px;">
                            <label style="font-size: 16px;">
                                <input type="radio" name="answer" value="${letter}" ${checked} class="answer-radio" data-answer="${letter}">
                                <strong>${letter}.</strong> <span class="option-text">${text}</span>
                            </label>
                        </div>
                    `;
                }
                $('#options-container').html(optionsHtml);
                
                // Re-attach change handler for new radios
                $('.answer-radio').on('change', function() {
                    const answer = $(this).val();
                    const questionId = $('#current-question-id').val();
                    
                    $.ajax({
                        url: '/policy-quizzes/' + quizId + '/answer',
                        method: 'POST',
                        data: {
                            _token: $('input[name="_token"]').val(),
                            question_id: questionId,
                            answer: answer
                        },
                        success: function(response) {
                            console.log('Answer saved - is_correct:', response.is_correct);
                        },
                        error: function(xhr) {
                            console.error('Error saving answer:', xhr);
                        }
                    });
                });
                
                // Update policy link
                if (data.question.policy_link) {
                    $('#policy-link-container').show();
                    $('#policy-link').attr('href', data.question.policy_link);
                } else {
                    $('#policy-link-container').hide();
                }
                
                // Update progress bar
                const progress = (data.question_num / data.total_questions) * 100;
                $('#progress-bar').css('width', progress + '%');
                $('#progress-bar').text(data.question_num + ' / ' + data.total_questions);
                
                // Update navigation buttons
                if (data.is_first_question) {
                    $('#prev-btn').prop('disabled', true);
                } else {
                    $('#prev-btn').prop('disabled', false);
                }
                
                if (data.is_last_question) {
                    $('#next-btn').hide();
                    $('#submit-quiz-btn').show();
                } else {
                    $('#next-btn').show();
                    $('#submit-quiz-btn').hide();
                }
                
                // Update remaining time
                remainingSeconds = data.remaining_seconds;
                
                // Update state
                currentQuestionNum = data.question_num;
                totalQuestions = data.total_questions;
            },
            error: function(xhr) {
                console.error('Error loading question:', xhr);
                alert('Error loading question. Please try again.');
            }
        });
    }
    
    // Previous button
    $('#prev-btn').on('click', function() {
        if (currentQuestionNum > 1) {
            loadQuestion(currentQuestionNum - 1);
        }
    });
    
    // Next button
    $('#next-btn').on('click', function() {
        if (currentQuestionNum < totalQuestions) {
            loadQuestion(currentQuestionNum + 1);
        }
    });
    
    // Submit quiz button
    $('#submit-quiz-btn').on('click', function() {
        $('#submitModal').modal('show');
    });
    
    // Confirm submit
    $('#confirm-submit-btn').on('click', function() {
        submitQuiz();
    });
    
    // Submit quiz function
    function submitQuiz() {
        $.ajax({
            url: '/policy-quizzes/' + quizId + '/submit',
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val()
            },
            success: function(response) {
                window.location.href = '/policy-quizzes/' + quizId + '/results';
            },
            error: function(xhr) {
                console.error('Error submitting quiz:', xhr);
                alert('Error submitting quiz. Please try again.');
            }
        });
    }
    
    // Prevent accidental navigation away from page
    window.addEventListener('beforeunload', function(e) {
        if (remainingSeconds > 0) {
            const confirmationMessage = 'You have an active quiz in progress. Are you sure you want to leave?';
            e.returnValue = confirmationMessage;
            return confirmationMessage;
        }
    });
});
</script>
@endsection