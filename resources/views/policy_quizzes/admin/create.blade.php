@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Create New Policy Quiz</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Quiz Details</h3>
                    </div>
                    <form method="POST" action="{{ route('admin.policy-quizzes.store') }}">
                        @csrf
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
                                               value="{{ old('title') }}" required 
                                               placeholder="e.g., Monthly Policy Quiz - July 2026">
                                        <small class="text-muted">A descriptive title for the quiz</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_questions">Number of Questions *</label>
                                        <input type="number" class="form-control" id="max_questions" name="max_questions" 
                                               value="{{ old('max_questions', 15) }}" min="1" max="50" required>
                                        <small class="text-muted">Max questions per attempt (default: 15)</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" 
                                          placeholder="Optional description of the quiz">{{ old('description') }}</textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="passing_threshold">Passing Threshold *</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="passing_threshold" name="passing_threshold" 
                                                   value="{{ old('passing_threshold', 80) }}" min="1" max="100" required>
                                            <span class="input-group-addon">%</span>
                                        </div>
                                        <small class="text-muted">Minimum percentage to pass (default: 80%)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time_limit_minutes">Time Limit *</label>
                                        <div class="input-group">
                                            <input type="number" class="form-control" id="time_limit_minutes" name="time_limit_minutes" 
                                                   value="{{ old('time_limit_minutes', 10) }}" min="1" max="180" required>
                                            <span class="input-group-addon">minutes</span>
                                        </div>
                                        <small class="text-muted">Time limit per attempt (default: 10 minutes)</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" name="active" value="1" checked> 
                                                Active Quiz
                                            </label>
                                        </div>
                                        <small class="text-muted">Inactive quizzes won't be visible to users</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="open_date">Open Date & Time *</label>
                                        <input type="datetime-local" class="form-control" id="open_date" name="open_date" 
                                               value="{{ old('open_date') }}" required>
                                        <small class="text-muted">When the quiz becomes available to users</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="close_date">Close Date & Time *</label>
                                        <input type="datetime-local" class="form-control" id="close_date" name="close_date" 
                                               value="{{ old('close_date') }}" required>
                                        <small class="text-muted">When the quiz closes for attempts</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Quick Setup Tips -->
                            <div class="alert alert-info">
                                <h4><i class="fa fa-lightbulb-o"></i> Quick Setup Tips:</h4>
                                <ul>
                                    <li>After creating the quiz, you'll be able to upload questions via CSV</li>
                                    <li>CSV format should include: Question, Option A, Option B, Option C, Option D, Correct Answer, Policy Link (optional), Explanation (optional)</li>
                                    <li>Correct Answer must be A, B, C, or D (case insensitive)</li>
                                    <li>Make sure you have enough questions (at least the "Number of Questions" specified above)</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="box-footer">
                            <a href="{{ route('admin.policy-quizzes.index') }}" class="btn btn-default">
                                <i class="fa fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary pull-right">
                                <i class="fa fa-save"></i> Create Quiz
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set default dates if not already set
    const now = new Date();
    const nextWeek = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000);
    
    // Format to YYYY-MM-DDTHH:mm for datetime-local input
    function formatDateTime(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const hours = String(date.getHours()).padStart(2, '0');
        const minutes = String(date.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }
    
    // Set default open date (tomorrow at 9 AM)
    const tomorrow = new Date(now);
    tomorrow.setDate(tomorrow.getDate() + 1);
    tomorrow.setHours(9, 0, 0, 0);
    
    // Set default close date (one week from tomorrow at 5 PM)
    const nextWeekClose = new Date(tomorrow);
    nextWeekClose.setDate(nextWeekClose.getDate() + 7);
    nextWeekClose.setHours(17, 0, 0, 0);
    
    // Only set if inputs are empty
    if (!$('#open_date').val()) {
        $('#open_date').val(formatDateTime(tomorrow));
    }
    if (!$('#close_date').val()) {
        $('#close_date').val(formatDateTime(nextWeekClose));
    }
    
    // Validation: Ensure close date is after open date
    $('form').on('submit', function(e) {
        const openDate = new Date($('#open_date').val());
        const closeDate = new Date($('#close_date').val());
        
        if (closeDate <= openDate) {
            e.preventDefault();
            alert('Close date must be after open date.');
            $('#close_date').focus();
            return false;
        }
    });
});
</script>
@endpush