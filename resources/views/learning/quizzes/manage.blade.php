@extends('layouts.learning')

@section('title', 'Manage Quiz - Whence Learn')

@section('content')
<div class="page-header">
    <h1>Manage Quiz</h1>
    <p>Create or edit quiz for: {{ $topic->topic_name }}</p>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Quiz Details</h3>
            </div>
            <form id="quiz-form">
                @csrf
                <input type="hidden" name="topic_id" value="{{ $topic->id }}">
                
                <div class="box-body">
                    <div class="form-group">
                        <label>Quiz Title *</label>
                        <input type="text" name="quiz_title" class="form-control" 
                               value="{{ $quiz->title ?? 'Quiz for ' . $topic->topic_name }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="quiz_description" class="form-control" rows="3">{{ $quiz->description ?? '' }}</textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Passing Score (%) *</label>
                                <input type="number" name="passing_score" class="form-control" 
                                       value="{{ $quiz->passing_score ?? 70 }}" min="0" max="100" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Time Limit (minutes)</label>
                                <input type="number" name="time_limit" class="form-control" 
                                       value="{{ $quiz->time_limit ?? '' }}" min="1" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h4>Questions</h4>
                    <div id="questions-container">
                        @if($quiz && $quiz->questions->count() > 0)
                            @foreach($quiz->questions as $qIndex => $question)
                            <div class="question-item" data-index="{{ $qIndex }}">
                                <div class="well">
                                    <div class="row">
                                        <div class="col-md-11">
                                            <div class="form-group">
                                                <label>Question {{ $qIndex + 1 }}</label>
                                                <input type="text" name="questions[{{ $qIndex }}][text]" 
                                                       class="form-control" 
                                                       value="{{ $question->question }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    onclick="removeQuestion({{ $qIndex }})">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <input type="hidden" name="questions[{{ $qIndex }}][type]" value="multiple_choice">
                                    <input type="hidden" name="questions[{{ $qIndex }}][points]" value="1">
                                    
                                    <label>Options (select the correct answer)</label>
                                    @foreach($question->options as $oIndex => $option)
                                    <div class="row option-row">
                                        <div class="col-md-1 text-center">
                                            <input type="radio" name="questions[{{ $qIndex }}][correct_option]" 
                                                   value="{{ $oIndex }}" {{ $option->is_correct ? 'checked' : '' }}>
                                        </div>
                                        <div class="col-md-10">
                                            <input type="text" name="questions[{{ $qIndex }}][options][]" 
                                                   class="form-control" 
                                                   value="{{ $option->option_text }}" required>
                                        </div>
                                        <div class="col-md-1">
                                            @if($oIndex > 1)
                                            <button type="button" class="btn btn-default btn-sm" onclick="removeOption(this)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                    
                                    <button type="button" class="btn btn-default btn-sm" onclick="addOption(this)">
                                        <i class="fa fa-plus"></i> Add Option
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                    
                    <button type="button" class="btn btn-success" onclick="addQuestion()">
                        <i class="fa fa-plus"></i> Add Question
                    </button>
                </div>
                
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Quiz
                    </button>
                    <a href="{{ url('learning/training-materials/quiz') }}" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let questionCount = {{ $quiz ? $quiz->questions->count() : 0 }};

function addQuestion() {
    const container = document.getElementById('questions-container');
    const html = `
        <div class="question-item" data-index="${questionCount}">
            <div class="well">
                <div class="row">
                    <div class="col-md-11">
                        <div class="form-group">
                            <label>Question ${questionCount + 1}</label>
                            <input type="text" name="questions[${questionCount}][text]" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(${questionCount})">
                            <i class="fa fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <input type="hidden" name="questions[${questionCount}][type]" value="multiple_choice">
                <input type="hidden" name="questions[${questionCount}][points]" value="1">
                
                <label>Options (select the correct answer)</label>
                <div class="option-row">
                    <div class="col-md-1 text-center">
                        <input type="radio" name="questions[${questionCount}][correct_option]" value="0" checked>
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="questions[${questionCount}][options][]" class="form-control" placeholder="Option 1" required>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                <div class="option-row">
                    <div class="col-md-1 text-center">
                        <input type="radio" name="questions[${questionCount}][correct_option]" value="1">
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="questions[${questionCount}][options][]" class="form-control" placeholder="Option 2" required>
                    </div>
                    <div class="col-md-1"></div>
                </div>
                
                <button type="button" class="btn btn-default btn-sm" onclick="addOption(this)">
                    <i class="fa fa-plus"></i> Add Option
                </button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', html);
    questionCount++;
}

function removeQuestion(index) {
    const item = document.querySelector(`.question-item[data-index="${index}"]`);
    if (item) item.remove();
}

function addOption(button) {
    const well = button.closest('.well');
    const questionItem = well.closest('.question-item');
    const index = questionItem.dataset.index;
    const optionInputs = well.querySelectorAll('input[name^="questions[' + index + '][options]"]');
    const optionCount = optionInputs.length;
    
    const html = `
        <div class="option-row" style="margin-top: 10px;">
            <div class="col-md-1 text-center">
                <input type="radio" name="questions[${index}][correct_option]" value="${optionCount}">
            </div>
            <div class="col-md-10">
                <input type="text" name="questions[${index}][options][]" class="form-control" placeholder="Option ${optionCount + 1}" required>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-default btn-sm" onclick="removeOption(this)">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    `;
    button.insertAdjacentHTML('beforebegin', html);
}

function removeOption(button) {
    button.closest('.option-row').remove();
}

$('#quiz-form').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: '{{ url('learning/training-materials/topic/' . $topic->id . '/quiz/save') }}',
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            if (response.success) {
                showFlashMessage('success', 'Quiz Saved', response.message, 'fa-check-circle');
                setTimeout(function() {
                    window.location.href = '{{ url('learning/training-materials/quiz') }}';
                }, 1500);
            } else {
                showFlashMessage('error', 'Error', response.message, 'fa-times-circle');
            }
        },
        error: function() {
            showFlashMessage('error', 'Error', 'An error occurred', 'fa-times-circle');
        }
    });
});
</script>
@endsection
