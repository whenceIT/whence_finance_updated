@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Edit Policy Quiz</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Edit Quiz: {{ $quiz->title }}</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.policy-quizzes.update', $quiz->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="box-body">
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible">
                                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                                    <strong>Please fix the following errors:</strong>
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Quiz Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="{{ old('title', $quiz->title) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_questions">Number of Questions *</label>
                                        <input type="number" class="form-control" id="max_questions" name="max_questions" 
                                               value="{{ old('max_questions', $quiz->max_questions) }}" min="1" max="50" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $quiz->description) }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="passing_threshold">Passing Threshold *</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="passing_threshold" name="passing_threshold" 
                                                   value="{{ old('passing_threshold', $quiz->passing_threshold) }}" min="1" max="100" required>
                                            <span class="input-group-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time_limit_minutes">Time Limit *</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="time_limit_minutes" name="time_limit_minutes" 
                                                   value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1" max="180" required>
                                            <span class="input-group-addon">minutes</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="active" value="1" {{ $quiz->active ? 'checked' : '' }}> 
                                                Active Quiz
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="open_date">Open Date & Time *</label>
                                        <input type="datetime-local" class="form-control" id="open_date" name="open_date" 
                                               value="{{ old('open_date', $quiz->open_date->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="close_date">Close Date & Time *</label>
                                        <input type="datetime-local" class="form-control" id="close_date" name="close_date" 
                                               value="{{ old('close_date', $quiz->close_date->format('Y-m-d\TH:i')) }}" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quiz Statistics -->
                            <div class="well" style="margin-top: 20px;">
                                <h4>Quiz Statistics</h4>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p><strong>Questions:</strong> {{ $quiz->questions()->count() }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Attempts:</strong> {{ $quiz->attempts()->count() }}</p>
                                    </div>
                                    <div class="col-md-4">
                                        <p><strong>Completed:</strong> {{ $quiz->attempts()->whereNotNull('completed_at')->count() }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('policy.quizzes.upload', $quiz->id) }}" class="btn btn-info btn-sm">
                                    <i class="fa fa-upload"></i> Upload Questions
                                </a>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <a href="{{ route('admin.policy-quizzes.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> Update Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection