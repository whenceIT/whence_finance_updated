@extends('layouts.master')

@section('title', 'Staff Survey Responses')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Staff Survey Responses</h3>
    </div>
    
    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover" id="surveyTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Staff Member</th>
                        <th>Branch</th>
                        <th>Length of Service</th>
                        <th>Submitted Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($surveys as $survey)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>
                            @if($survey->user)
                                <strong>{{ $survey->user->first_name }} {{ $survey->user->last_name }}</strong><br>
                                <small>{{ $survey->user->email }}</small>
                            @else
                                <span class="text-muted">Unknown User</span>
                            @endif
                        </td>
                        <td>{{ $survey->branch }}</td>
                        <td>{{ $survey->length_of_service }}</td>
                        <td>{{ $survey->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            <a href="{{ route('survey.show_response', $survey->id) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i> View Details
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="text-center">
            {{ $surveys->links() }}
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#surveyTable').DataTable({
        "order": [[4, "desc"]],
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]]
    });
});
</script>
@endsection
