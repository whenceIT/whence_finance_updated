@extends('layouts.master')

@section('title')
    Policy Violation Details
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Violation #{{ $violation->id }}</h3>
                    <div class="box-tools">
                        <a href="{{ route('policies.dashboard') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Violation Details</h4>
                            <table class="table table-bordered">
                                <tr>
                                    <th>User:</th>
                                    <td>{{ $violation->user->first_name }} {{ $violation->user->last_name }}</td>
                                </tr>
                                <tr>
                                    <th>Policy:</th>
                                    <td>{{ $violation->policy->title }}</td>
                                </tr>
                                <tr>
                                    <th>Description:</th>
                                    <td>{{ $violation->description }}</td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        <span class="label label-{{ $violation->status == 'resolved' ? 'success' : ($violation->status == 'escalated' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($violation->status) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Reported Date:</th>
                                    <td>{{ $violation->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h4>Evidence Files</h4>
                            @if(count($violation->evidence) > 0)
                                <div class="list-group">
                                    @foreach($violation->evidence as $file)
                                        <a href="#" class="list-group-item">
                                            <i class="fa fa-file"></i> {{ $file->filename }}
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No evidence files attached.</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h4>Actions</h4>
                            <div class="btn-group">
                                @if($violation->status == 'pending')
                                    <button class="btn btn-warning" onclick="changeStatus({{ $violation->id }}, 'investigating')">Start Investigation</button>
                                @elseif($violation->status == 'investigating')
                                    <button class="btn btn-success" onclick="changeStatus({{ $violation->id }}, 'resolved')">Mark as Resolved</button>
                                    <button class="btn btn-danger" onclick="changeStatus({{ $violation->id }}, 'escalated')">Escalate</button>
                                @endif
                                <button class="btn btn-info" onclick="attachEvidence({{ $violation->id }})">Attach Evidence</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeStatus(violationId, newStatus) {
    $.post('{{ route("policies.violations.updateStatus") }}', {
        violation_id: violationId,
        status: newStatus,
        _token: '{{ csrf_token() }}'
    })
    .done(function() {
        location.reload();
    })
    .fail(function() {
        alert('Error updating status');
    });
}

function attachEvidence(violationId) {
    const input = document.createElement('input');
    input.type = 'file';
    input.multiple = true;
    input.accept = 'image/*,.pdf,.doc,.docx';
    input.onchange = function(e) {
        const files = e.target.files;
        const formData = new FormData();
        formData.append('violation_id', violationId);
        formData.append('_token', '{{ csrf_token() }}');
        for (let i = 0; i < files.length; i++) {
            formData.append('evidence[]', files[i]);
        }

        $.ajax({
            url: '{{ route("policies.violations.attachEvidence") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                location.reload();
            },
            error: function() {
                alert('Error attaching evidence');
            }
        });
    };
    input.click();
}
</script>
@endsection