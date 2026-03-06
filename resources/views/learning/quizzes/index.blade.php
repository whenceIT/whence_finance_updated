@extends('layouts.learning')

@section('title', 'Quiz Management - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Training Materials', 'url' => url('learning/training-materials')],
    ['label' => 'Quiz Management', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Quiz Management</h1>
    <p>Manage quizzes for course topics</p>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Topic</th>
                <th>Course</th>
                <th>Quiz</th>
                <th>Questions</th>
                <th>Passing Score</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topics as $topic)
            <tr>
                <td>{{ $topic->topic_name }}</td>
                <td>{{ $topic->trainingMaterial->title ?? 'N/A' }}</td>
                <td>
                    @if($topic->quiz)
                        {{ $topic->quiz->title }}
                    @else
                        <span class="text-muted">No quiz</span>
                    @endif
                </td>
                <td>
                    @if($topic->quiz)
                        {{ $topic->quiz->questions->count() }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($topic->quiz)
                        {{ $topic->quiz->passing_score }}%
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($topic->quiz && $topic->quiz->is_active)
                        <span class="label label-success">Active</span>
                    @else
                        <span class="label label-default">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ url('learning/training-materials/topic/' . $topic->id . '/quiz/manage') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-cog"></i> {{ $topic->quiz ? 'Edit Quiz' : 'Create Quiz' }}
                    </a>
                    @if($topic->quiz)
                    <button onclick="deleteQuiz({{ $topic->quiz->id }})" class="btn btn-danger btn-sm">
                        <i class="fa fa-trash"></i>
                    </button>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No topics found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{ $topics->links() }}

<script>
function deleteQuiz(quizId) {
    if (confirm('Are you sure you want to delete this quiz?')) {
        $.ajax({
            url: '{{ url('learning/training-materials/quiz') }}/' + quizId + '/delete',
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    showFlashMessage('success', 'Quiz Deleted', response.message, 'fa-check-circle');
                    location.reload();
                } else {
                    showFlashMessage('error', 'Error', response.message, 'fa-times-circle');
                }
            }
        });
    }
}
</script>
@endsection
