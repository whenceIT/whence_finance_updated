@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Question {{ $questionNum }} of {{ $totalQuestions }}</h3>
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
                        <form id="quiz-form" method="POST" action="{{ route('policy.quizzes.answer', $quiz->id) }}">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">
                            
                            <div class="question-container">
                                <div class="question-text" style="font-size: 18px; font-weight: bold; margin-bottom: 20px;">
                                    {{ $currentQuestion->question_text }}
                                </div>
                                
                                <div class="options-container" style="margin-bottom: 30px;">
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
                                                <strong>{{ $letter }}.</strong> {{ $option }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                
                                @if($currentQuestion->policy_link)
                                    <div class="policy-link" style="margin-bottom: 20px; font-style: italic;">
                                        <i class="fa fa-book"></i> 
                                        Related policy: 
                                        <a href="{{ $currentQuestion->policy_link }}" target="_blank" class="text-primary">
                                            View Policy Document
                                        </a>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="navigation-buttons" style="margin-top: 30px;">
                                <div class="row">
                                    <div class="col-md-6">
                                        @if($questionNum > 1)
                                            <a href="{{ route('policy.quizzes.question', ['id' => $quiz->id, 'question' => $questionNum - 1]) }}" 
                                               class="btn btn-default btn-lg">
                                                <i class="fa fa-arrow-left"></i> Previous Question
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <div class="col-md-6 text-right">
                                        @if($questionNum < $totalQuestions)
                                            <a href="{{ route('policy.quizzes.question', ['id' => $quiz->id, 'question' => $questionNum + 1]) }}" 
                                               class="btn btn-primary btn-lg" id="next-btn">
                                                Next Question <i class="fa fa-arrow-right"></i>
                                            </a>
                                        @else
                                            <button type="button" class="btn btn-success btn-lg" id="submit-quiz-btn">
                                                <i class="fa fa-check-circle"></i> Submit Quiz
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <div class="progress" style="margin-top: 30px;">
                            <div class="progress-bar progress-bar-primary progress-bar-striped" 
                                 role="progressbar" 
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
                <p>You have answered {{ $questionNum }} out of {{ $totalQuestions }} questions.</p>
                <p class="text-warning"><i class="fa fa-exclamation-triangle"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <form id="final-submit-form" method="POST" action="{{ route('policy.quizzes.submit', $quiz->id) }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Yes, Submit Quiz</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let remainingSeconds = {{ $remainingSeconds }};
    let timerInterval;
    
    // Timer functionality
    function updateTimer() {
        if (remainingSeconds <= 0) {
            clearInterval(timerInterval);
            // Auto-submit the form
            document.getElementById('final-submit-form').submit();
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
        const questionId = $('input[name="question_id"]').val();
        
        $.ajax({
            url: $('#quiz-form').attr('action'),
            method: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                question_id: questionId,
                answer: answer
            },
            success: function(response) {
                // Optionally show a success indicator
                console.log('Answer saved:', response.is_correct);
            },
            error: function(xhr) {
                console.error('Error saving answer:', xhr);
            }
        });
    });
    
    // Handle next button - prevent navigation if no answer selected
    $('#next-btn').on('click', function(e) {
        const selectedAnswer = $('input[name="answer"]:checked').val();
        if (!selectedAnswer) {
            e.preventDefault();
            alert('Please select an answer before proceeding to the next question.');
            return false;
        }
    });
    
    // Handle submit button
    $('#submit-quiz-btn').on('click', function() {
        $('#submitModal').modal('show');
    });
    
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
@endpush