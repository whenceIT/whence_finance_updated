@extends('layouts.master')

@section('title')
    Audit Logs for {{ $user->first_name }} {{ $user->last_name }}
@endsection

@section('content')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #ddd;
}
.timeline-item {
    position: relative;
    margin-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    background: #007bff;
    border-radius: 50%;
    border: 2px solid #fff;
}
.timeline-content {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Audit Timeline for {{ $user->first_name }} {{ $user->last_name }}</h3>
                    <a href="{{ route('audits.index') }}" class="btn btn-secondary float-right">Back to Users</a>
                </div>
                <div class="card-body">
                    <div class="timeline" id="audit-timeline">
                        @foreach($audits as $audit)
                        <div class="timeline-item">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <h4>{{ $audit->event }}</h4>
                                <p><strong>Type:</strong> {{ $audit->auditable_type }}</p>
                                <p><strong>ID:</strong> {{ $audit->auditable_id }}</p>
                                <p><strong>Time:</strong> {{ $audit->created_at->format('Y-m-d H:i:s') }}</p>
                                <a href="{{ route('audits.show', $audit->id) }}" class="btn btn-sm btn-info">Details</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Function to fetch and update audits
    function fetchAudits() {
        $.ajax({
            url: '{{ route("audits.user", $user->id) }}',
            method: 'GET',
            data: { ajax: 1 },
            success: function(data) {
                var timeline = $('#audit-timeline');
                timeline.empty();
                data.audits.forEach(function(audit) {
                    var item = '<div class="timeline-item">' +
                        '<div class="timeline-marker"></div>' +
                        '<div class="timeline-content">' +
                        '<h4>' + audit.event + '</h4>' +
                        '<p><strong>Type:</strong> ' + audit.auditable_type + '</p>' +
                        '<p><strong>ID:</strong> ' + audit.auditable_id + '</p>' +
                        '<p><strong>Time:</strong> ' + audit.created_at + '</p>' +
                        '<a href="' + '{{ url("audits") }}/' + audit.id + '" class="btn btn-sm btn-info">Details</a>' +
                        '</div></div>';
                    timeline.append(item);
                });
            }
        });
    }

    // Poll every 30 seconds
    setInterval(fetchAudits, 30000);
});
</script>
@endsection