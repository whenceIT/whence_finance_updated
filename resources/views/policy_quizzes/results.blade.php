@extends('layouts.master')

@section('content')
<div class="content">
    <section class="content-header">
        <h1>Quiz Results</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- Results Summary -->
                <div class="box box-{{ $attempt->passed ? 'success' : 'danger' }}">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{ $quiz->title }}</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-{{ $attempt->passed ? 'green' : 'red' }}">
                                        <i class="fa fa-{{ $attempt->passed ? 'check' : 'times' }}"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Status</span>
                                        <span class="info-box-number" style="font-size: 24px;">
                                            {{ $attempt->passed ? 'PASSED' : 'FAILED' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-blue">
                                        <i class="fa fa-percent"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Your Score</span>
                                        <span class="info-box-number" style="font-size: 24px;">
                                            {{ number_format($attempt->score_percentage ?? 0, 1) }}%
                                        </span>
                                        <small>Passing threshold: {{ $quiz->passing_threshold }}%</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="info-box">
                                    <span class="info-box-icon bg-purple">
                                        <i class="fa fa-calendar"></i>
                                    </span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Completed On</span>
                                        <span class="info-box-number" style="font-size: 20px;">
                                            {{ $attempt->completed_at ? $attempt->completed_at->format('M d, Y') : 'N/A' }}
                                        </span>
                                        <small>{{ $attempt->completed_at ? $attempt->completed_at->format('h:i A') : '' }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Score Breakdown -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <div class="progress-group">
                                    <span class="progress-text">Correct Answers</span>
                                    <span class="progress-number">
                                        {{ $answers->where('is_correct', true)->count() }} / {{ $answers->count() }}
                                    </span>
                                    <div class="progress sm">
<div class="progress-bar progress-bar-green" 
                                         style="width: {{ $attempt->score_percentage ?? 0 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="row" style="margin-top: 30px;">
                            <div class="col-md-12 text-center">
                                <a href="{{ route('policy.quizzes.index') }}" class="btn btn-primary btn-lg">
                                    <i class="fa fa-home"></i> Back to Quizzes
                                </a>
                                
                                @if(!$attempt->passed)
                                    @if($quiz->isOpen())
                                        <a href="{{ route('policy.quizzes.start', $quiz->id) }}" 
                                           class="btn btn-warning btn-lg" 
                                           onclick="return confirm('Are you sure you want to retake this quiz? Your previous attempt will be recorded but you can try again.');">
                                            <i class="fa fa-redo"></i> Retake Quiz
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Incorrect Answers Review -->
                @if($incorrectAnswers->count() > 0)
                <div class="box box-warning">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-exclamation-triangle"></i> 
                            Questions to Review ({{ $incorrectAnswers->count() }} incorrect)
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            Review these questions and their correct answers to improve your understanding.
                        </div>
                        
                        @foreach($incorrectAnswers as $index => $answer)
                        <div class="question-review" style="margin-bottom: 30px; padding: 15px; border: 1px solid #f0f0f0; border-radius: 5px;">
                            <h4 style="margin-top: 0;">
                                Question {{ $index + 1 }}: {{ $answer->question->question_text }}
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="panel panel-danger">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Your Answer</h3>
                                        </div>
                                        <div class="panel-body">
                                            <p><strong>{{ $answer->selected_answer }}.</strong> 
                                               {{ $answer->question->{'option_' . strtolower($answer->selected_answer)} }}
                                            </p>
                                            <span class="label label-danger">Incorrect</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="panel panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Correct Answer</h3>
                                        </div>
                                        <div class="panel-body">
                                            <p><strong>{{ $answer->question->correct_answer }}.</strong> 
                                               {{ $answer->question->getCorrectOptionText() }}
                                            </p>
                                            <span class="label label-success">Correct</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($answer->question->explanation)
                            <div class="explanation" style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">
                                <strong><i class="fa fa-lightbulb-o"></i> Explanation:</strong>
                                <p style="margin-bottom: 5px;">{{ $answer->question->explanation }}</p>
                            </div>
                            @endif
                            
                            @if($answer->question->policy_link)
                            <div class="policy-link" style="margin-top: 10px;">
                                <a href="{{ $answer->question->policy_link }}" target="_blank" class="btn btn-xs btn-info">
                                    <i class="fa fa-book"></i> Review Related Policy
                                </a>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-check-circle"></i> 
                            Perfect Score!
                        </h3>
                    </div>
                    <div class="box-body text-center">
                        <div class="alert alert-success" style="font-size: 18px;">
                            <i class="fa fa-trophy fa-2x"></i><br>
                            Congratulations! You answered all questions correctly.
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- All Answers Summary -->
                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Detailed Results</h3>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Your Answer</th>
                                        <th>Correct Answer</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($answers as $index => $answer)
                                    <tr class="{{ $answer->is_correct ? 'success' : 'danger' }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td style="max-width: 400px;">{{ Str::limit($answer->question->question_text, 100) }}</td>
                                        <td>
                                            <strong>{{ $answer->selected_answer }}.</strong> 
                                            {{ Str::limit($answer->question->{'option_' . strtolower($answer->selected_answer)}, 50) }}
                                        </td>
                                        <td>
                                            <strong>{{ $answer->question->correct_answer }}.</strong> 
                                            {{ Str::limit($answer->question->getCorrectOptionText(), 50) }}
                                        </td>
                                        <td>
                                            @if($answer->is_correct)
                                                <span class="label label-success">Correct</span>
                                            @else
                                                <span class="label label-danger">Incorrect</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection