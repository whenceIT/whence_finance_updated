@extends('layouts.master')
@section('title')
    Survey Response Details
@endsection

@section('content')
<style>
    .header {
        overflow: hidden;
        padding: 20px 10px;
        background: #f5f5f5;
        border-bottom: 2px solid #ddd;
    }
    
    .headerContainer {
        flex-direction: column;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .main {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }
    
    .infobox {
        flex-direction: column;
        align-items: center;
        display: flex;
        justify-content: center;
        border-width: thin;
        width: 100%;
        border-style: solid;
        padding: 30px;
        background-color: #f9f9f9;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .question-box {
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .question-text {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 10px;
        color: #333;
    }
    
    .answer-text {
        font-size: 15px;
        padding: 15px;
        background-color: #f0f8ff;
        border-left: 4px solid #007bff;
        border-radius: 3px;
    }
    
    .score-badge {
        font-size: 24px;
        font-weight: bold;
        padding: 10px 20px;
        border-radius: 5px;
    }
    
    .status-badge {
        font-size: 18px;
        padding: 8px 15px;
        border-radius: 4px;
    }
</style>

<div>
    {{-- Header --}}
    <div class="header">
        <div class="headerContainer">
            <img src="{{asset('images/icons/icon-72x72.png') }}" style="width: 60px; margin-bottom: 10px;"/>
            <h3>Whence Financial Services</h3>
            <p style="font-weight: bold; font-size: 18px; text-decoration: underline;">Survey Response Details</p>
        </div>
    </div>

    {{-- Summary Section --}}
    <div class="main">
        <div class="infobox">
            <div class="row" style="width: 100%;">
                <div class="col-md-6" style="text-align: center; margin-bottom: 20px;">
                    <p style="font-weight: bold; font-size: 16px;">Respondent</p>
                    @if($response->user)
                        <h4>{{ $response->user->first_name }} {{ $response->user->last_name }}</h4>
                        <p>{{ $response->user->email }}</p>
                        @if($response->user->office)
                            <p><strong>Office:</strong> {{ $response->user->office->name }}</p>
                        @endif
                        @if($response->user->role)
                            <p><strong>Role:</strong> {{ $response->user->role->name }}</p>
                        @endif
                    @else
                        <p class="text-muted">User information not available</p>
                    @endif
                </div>

                <div class="col-md-6" style="text-align: center; margin-bottom: 20px;">
                    <p style="font-weight: bold; font-size: 16px;">Survey Information</p>
                    @if($response->survey)
                        <h4>{{ $response->survey->title }}</h4>
                        <p>{{ $response->survey->description }}</p>
                    @else
                        <p class="text-muted">Survey information not available</p>
                    @endif
                </div>
            </div>

            <div class="row" style="width: 100%; margin-top: 20px;">
                <div class="col-md-3" style="text-align: center;">
                    <p style="font-weight: bold;">Status</p>
                    @if($response->status == 'completed')
                        <span class="status-badge label label-success">Completed</span>
                    @elseif($response->status == 'pending')
                        <span class="status-badge label label-warning">Pending</span>
                    @elseif($response->status == 'in_progress')
                        <span class="status-badge label label-info">In Progress</span>
                    @else
                        <span class="status-badge label label-default">{{ ucfirst($response->status) }}</span>
                    @endif
                </div>

                <div class="col-md-3" style="text-align: center;">
                    <p style="font-weight: bold;">Score</p>
                    @if($response->score !== null)
                        <span class="score-badge badge bg-{{ $response->score >= 80 ? 'green' : ($response->score >= 60 ? 'yellow' : 'red') }}">
                            {{ $response->score }}%
                        </span>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>

                <div class="col-md-3" style="text-align: center;">
                    <p style="font-weight: bold;">Started</p>
                    @if($response->started_at)
                        <p>{{ \Carbon\Carbon::parse($response->started_at)->format('d M, Y H:i') }}</p>
                    @else
                        <span class="text-muted">N/A</span>
                    @endif
                </div>

                <div class="col-md-3" style="text-align: center;">
                    <p style="font-weight: bold;">Submitted</p>
                    @if($response->submitted_at)
                        <p>{{ \Carbon\Carbon::parse($response->submitted_at)->format('d M, Y H:i') }}</p>
                    @else
                        <span class="text-muted">Not submitted</span>
                    @endif
                </div>
            </div>

            @if($response->submitted_at && $response->started_at)
            <div class="row" style="width: 100%; margin-top: 20px;">
                <div class="col-md-12" style="text-align: center;">
                    <p style="font-weight: bold;">Time Taken</p>
                    <p style="font-size: 18px;">
                        {{ $response->started_at->diffForHumans($response->submitted_at) }}
                    </p>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Questions and Answers Section --}}
    <div style="padding: 30px;">
        <h3 style="text-align: center; margin-bottom: 30px; color: #333;">Questions & Answers</h3>
        
        @if(isset($questions) && count($questions) > 0)
            @foreach($questions as $question)
                <div class="question-box">
                    <p class="question-text">
                        <span style="color: #007bff;">Q{{ $loop->index + 1 }}.</span> 
                        {{ $question->question_text }}
                    </p>
                    
                    <div class="answer-text">
                        @if($question->question_type == 'text' || $question->question_type == 'textarea')
                            <p><strong>Answer:</strong> {{ $question->answer ?? 'Not answered' }}</p>
                        
                        @elseif($question->question_type == 'multiple_choice' || $question->question_type == 'single_choice')
                            <p><strong>Selected Option:</strong> {{ $question->answer ?? 'Not answered' }}</p>
                            @if(isset($question->options) && is_array($question->options))
                                <div style="margin-top: 10px; padding-left: 20px;">
                                    @foreach($question->options as $option)
                                        <p style="margin: 5px 0;">
                                            @if($question->answer == $option)
                                                <i class="fa fa-check-circle" style="color: #28a745;"></i>
                                                <strong>{{ $option }}</strong>
                                            @else
                                                <i class="fa fa-circle-o" style="color: #ccc;"></i>
                                                {{ $option }}
                                            @endif
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        
                        @elseif($question->question_type == 'rating')
                            <p><strong>Rating:</strong> 
                                @if($question->answer !== null)
                                    <span style="font-size: 20px; color: #007bff;">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $question->answer)
                                                <i class="fa fa-star"></i>
                                            @else
                                                <i class="fa fa-star-o" style="color: #ddd;"></i>
                                            @endif
                                        @endfor
                                    </span>
                                    ({{ $question->answer }}/5)
                                @else
                                    <span class="text-muted">Not rated</span>
                                @endif
                            </p>
                        
                        @elseif($question->question_type == 'yes_no')
                            <p><strong>Answer:</strong> 
                                @if($question->answer == 'yes')
                                    <span class="label label-success">Yes</span>
                                @elseif($question->answer == 'no')
                                    <span class="label label-danger">No</span>
                                @else
                                    <span class="text-muted">Not answered</span>
                                @endif
                            </p>
                        
                        @elseif($question->question_type == 'date')
                            <p><strong>Date:</strong> {{ $question->answer ?? 'Not answered' }}</p>
                        
                        @elseif($question->question_type == 'number')
                            <p><strong>Number:</strong> {{ $question->answer ?? 'Not answered' }}</p>
                        
                        @else
                            <p><strong>Answer:</strong> {{ $question->answer ?? 'Not answered' }}</p>
                        @endif
                    </div>

                    {{-- Question Score if available --}}
                    @if($question->score !== null)
                    <div style="margin-top: 10px; padding: 10px; background-color: #e8f5e9; border-radius: 3px;">
                        <p style="margin: 0;">
                            <strong>Question Score:</strong> 
                            <span class="badge bg-{{ $question->score >= 80 ? 'green' : ($question->score >= 60 ? 'yellow' : 'red') }}">
                                {{ $question->score }}%
                            </span>
                            @if($question->max_score !== null)
                                (Max: {{ $question->max_score }})
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="alert alert-info text-center">
                <i class="fa fa-info-circle"></i> No questions found for this survey response.
            </div>
        @endif
    </div>

    {{-- Comments/Feedback Section --}}
    @if(isset($response->comments) && !empty($response->comments))
    <div style="padding: 0 30px 30px;">
        <h3 style="margin-bottom: 20px;">Comments / Feedback</h3>
        <div class="question-box">
            <p>{{ $response->comments }}</p>
        </div>
    </div>
    @endif

    {{-- Action Buttons --}}
    <div style="padding: 20px 30px; text-align: center;">
        <a href="{{ url('user/survey_responses') }}" class="btn btn-default">
            <i class="fa fa-arrow-left"></i> Back to Responses
        </a>
        
        @if(Sentinel::hasAccess('surveys.export'))
            <a href="{{ url('user/survey_response/' . $response->id . '/export') }}" class="btn btn-success">
                <i class="fa fa-file-pdf-o"></i> Export PDF
            </a>
        @endif
        
        @if(Sentinel::hasAccess('surveys.print'))
            <button type="button" class="btn btn-primary" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        @endif

        @if(Sentinel::hasAccess('surveys.delete'))
            <a href="{{ url('user/survey_response/' . $response->id . '/delete') }}" 
               class="btn btn-danger delete" 
               onclick="return confirm('Are you sure you want to delete this response?');">
                <i class="fa fa-trash"></i> Delete Response
            </a>
        @endif
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        // Any additional JavaScript can be added here
    });
</script>
@endsection
