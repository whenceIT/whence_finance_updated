@extends('layouts.master')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <h1>Policy Quizzes Management</h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Quizzes</h3>
                        <div class="box-tools">
                            <a href="{{ route('admin.policy-quizzes.create') }}" class="btn btn-success btn-sm">
                                <i class="fa fa-plus"></i> Create New Quiz
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <i class="fa fa-check"></i> {{ session('success') }}
                            </div>
                        @endif
                        
                        @if($quizzes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Questions</th>
                                            <th>Time Limit</th>
                                            <th>Open Date</th>
                                            <th>Close Date</th>
                                            <th>Pass %</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($quizzes as $quiz)
                                            @php
                                                $questionCount = $quiz->questions()->count();
                                                $attemptCount = $quiz->attempts()->whereNotNull('completed_at')->count();
                                            @endphp
                                            <tr>
                                                <td>{{ $quiz->id }}</td>
                                                <td>
                                                    <strong>{{ $quiz->title }}</strong>
                                                    @if($quiz->description)
                                                        <br><small class="text-muted">{{ Str::limit($quiz->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($quiz->active && $quiz->isOpen())
                                                        <span class="label label-success">Active & Open</span>
                                                    @elseif($quiz->active && $quiz->open_date > now())
                                                        <span class="label label-info">Scheduled</span>
                                                    @elseif($quiz->active)
                                                        <span class="label label-warning">Closed</span>
                                                    @else
                                                        <span class="label label-default">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $questionCount }} / {{ $quiz->max_questions }}
                                                    @if($questionCount < $quiz->max_questions)
                                                        <br><small class="text-warning">Need more questions</small>
                                                    @endif
                                                </td>
                                                <td>{{ $quiz->time_limit_minutes }} min</td>
                                                <td>{{ $quiz->open_date->format('M d, Y H:i') }}</td>
                                                <td>{{ $quiz->close_date->format('M d, Y H:i') }}</td>
                                                <td>{{ $quiz->passing_threshold }}%</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('admin.policy-quizzes.edit', $quiz->id) }}" 
                                                           class="btn btn-xs btn-primary" title="Edit">
                                                            <i class="fa fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('policy.quizzes.upload', $quiz->id) }}" 
                                                           class="btn btn-xs btn-info" title="Upload Questions">
                                                            <i class="fa fa-upload"></i>
                                                        </a>
                                                        <a href="{{ route('policy.quizzes.completion-dashboard', $quiz->id) }}" 
                                                           class="btn btn-xs btn-success" title="View Completion">
                                                            <i class="fa fa-users"></i>
                                                        </a>
                                                        <a href="{{ route('policy.quizzes.report', $quiz->id) }}" 
                                                           class="btn btn-xs btn-warning" title="Export Report">
                                                            <i class="fa fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-xs btn-danger" 
                                                                onclick="deleteQuiz({{ $quiz->id }})" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Statistics -->
                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box bg-aqua">
                                        <span class="info-box-icon"><i class="fa fa-list"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Quizzes</span>
                                            <span class="info-box-number">{{ $quizzes->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box bg-green">
                                        <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Active Quizzes</span>
                                            <span class="info-box-number">{{ $quizzes->where('active', true)->where('isOpen')->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box bg-yellow">
                                        <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Scheduled</span>
                                            <span class="info-box-number">{{ $quizzes->where('active', true)->where('open_date', '>', now())->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-3 col-sm-6 col-xs-12">
                                    <div class="info-box bg-red">
                                        <span class="info-box-icon"><i class="fa fa-times"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Inactive</span>
                                            <span class="info-box-number">{{ $quizzes->where('active', false)->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> No quizzes found. 
                                <a href="{{ route('admin.policy-quizzes.create') }}">Create your first quiz</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm Delete</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this quiz?</p>
                <p class="text-danger"><i class="fa fa-exclamation-triangle"></i> This will also delete all questions and attempts associated with this quiz.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteQuiz(quizId) {
    $('#delete-form').attr('action', '/admin/policy-quizzes/' + quizId);
    $('#deleteModal').modal('show');
}
</script>
@endpush