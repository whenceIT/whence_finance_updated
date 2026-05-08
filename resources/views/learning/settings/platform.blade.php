@extends('layouts.learning')

@section('title', 'Platform Settings - Whence Learn')

@section('content')
<style>
.modal-fullscreen .modal-dialog {
    width: 100vw;
    height: 100vh;
    margin: 0;
    padding: 0;
}
.modal-fullscreen .modal-content {
    height: 100%;
    border-radius: 0;
}

/* Multi-select styles */
.multi-select-container {
    position: relative;
}

.multi-select {
    position: relative;
    width: 100%;
}

.multi-select-selected {
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    cursor: pointer;
    user-select: none;
    color: #666;
    transition: border-color 0.3s ease;
}

.multi-select-selected:hover {
    border-color: #007bff;
}

.multi-select-selected.active {
    border-color: #007bff;
    color: #333;
}

.multi-select-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 6px 6px;
    max-height: 300px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.multi-select-options.active {
    display: block;
}

.multi-select-option {
    padding: 10px 12px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    display: flex;
    align-items: center;
}

.multi-select-option:hover {
    background-color: #f8f9fa;
}

.multi-select-option input[type="checkbox"] {
    margin-right: 10px;
    width: 16px;
    height: 16px;
    cursor: pointer;
}

.multi-select-option label {
    margin: 0;
    cursor: pointer;
    flex: 1;
    font-size: 14px;
    color: #333;
}
</style>
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
    ['label' => 'Platform', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Platform Settings</h1>
    <p>Configure platform-wide settings and preferences</p>
</div>

<!-- Platform Settings Content -->
<div class="settings-content">
    <div class="card">
        <div class="card-header">
            <h2>Platform Configuration</h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-content-push">
                            Content Push Mechanism
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-tracking-monitoring">
                            Tracking & Monitoring
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-feedback-loop">
                            Feedback Loop to Users
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-performance-triggers">
                            Performance-Linked Learning Triggers
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-escalation-enforcement">
                            Escalation & Enforcement Mechanisms
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-management-dashboard">
                            Management & Executive Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="modal-content-push" tabindex="-1" role="dialog" aria-labelledby="modal-content-push-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-content-push-label">Content Push Mechanism</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="content-type-select">Select Content Type:</label>
                    <select class="form-control" id="content-type-select">
                        <option value="">-- Select --</option>
                        <option value="courses">Courses</option>
                        <option value="general">General</option>
                    </select>
                </div>

                <div id="courses-list" style="display: none;">
                    <h6>Training Materials:</h6>
                    <ul class="list-group">
                        @php
                            $materials = \App\Models\TrainingMaterial::all();
                        @endphp
                        @forelse($materials as $material)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $material->title }}
                                <span class="badge badge-primary badge-pill">{{ $material->material_type }}</span>
                            </li>
                        @empty
                            <li class="list-group-item">No training materials available.</li>
                        @endforelse
                    </ul>
                </div>

                <div id="general-list" style="display: none;">
                    <h6>General Uploads:</h6>
                    <select id="topic-filter" class="form-control mb-3">
                        <option value="">All Topics</option>
                        @php
                            $topics = \App\Models\GeneralTopic::all();
                        @endphp
                        @foreach($topics as $topic)
                            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                        @endforeach
                    </select>
                    <ul class="list-group">
                        @php
                            $uploads = \App\Models\GeneralUpload::with('positions')->get();
                        @endphp
                        @forelse($uploads as $upload)
                            <li class="list-group-item" data-topic-id="{{ $upload->general_topic_id }}">
                                <div data-toggle="collapse" data-target="#collapse-{{ $upload->id }}" style="cursor:pointer;" class="d-flex justify-content-between align-items-center">
                                    <span>{{ $upload->name }}</span>
                                    <span class="badge badge-secondary badge-pill">{{ $upload->type_label }}</span>
                                </div>
                                <div id="collapse-{{ $upload->id }}" class="collapse mt-2">
                                    <div class="card card-body">
                                        <p><strong>Assigned Positions:</strong></p>
                                        @php
                                            $badgeClasses = ['badge-primary', 'badge-success', 'badge-info', 'badge-warning', 'badge-danger'];
                                            $index = 0;
                                        @endphp
                                        @if($upload->positions->count() > 0)
                                            <div class="d-flex flex-wrap">
                                                @foreach($upload->positions as $position)
                                                    <span class="badge {{ $badgeClasses[$index++ % count($badgeClasses)] }}" style="margin-right: 5px; margin-bottom: 5px;">{{ $position->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <p>No positions assigned.</p>
                                        @endif
                                        <button class="btn btn-primary btn-sm mt-2" onclick="toggleAssignForm({{ $upload->id }})">Assign Positions</button>
                                        <div id="assign-form-{{ $upload->id }}" style="display: none; margin-top: 10px;">
                                            <form>
                                                <div class="form-group">
                                                    <label class="form-label">Select Positions</label>
                                                    <div class="multi-select-container">
                                                        <div class="multi-select" id="positionMultiSelect-{{ $upload->id }}">
                                                            <div class="multi-select-selected" id="positionSelected-{{ $upload->id }}">Select positions</div>
                                                            <div class="multi-select-options" id="positionOptions-{{ $upload->id }}">
                                                                @php
                                                                    $positions = \App\Models\Position::all();
                                                                @endphp
                                                                @foreach($positions as $position)
                                                                    <div class="multi-select-option" data-value="{{ $position->id }}">
                                                                        <input type="checkbox" id="position_{{ $upload->id }}_{{ $position->id }}" name="position_id[]" value="{{ $position->id }}">
                                                                        <label for="position_{{ $upload->id }}_{{ $position->id }}">{{ $position->name }}</label>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-success btn-sm" onclick="savePositions({{ $upload->id }})">Save</button>
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="toggleAssignForm({{ $upload->id }})">Cancel</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item">No general uploads available.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-tracking-monitoring" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-monitoring-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-tracking-monitoring-label">Tracking & Monitoring</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Tracking & Monitoring -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-feedback-loop" tabindex="-1" role="dialog" aria-labelledby="modal-feedback-loop-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-feedback-loop-label">Feedback Loop to Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Feedback Loop to Users -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-performance-triggers" tabindex="-1" role="dialog" aria-labelledby="modal-performance-triggers-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-performance-triggers-label">Performance-Linked Learning Triggers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Performance-Linked Learning Triggers -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-escalation-enforcement" tabindex="-1" role="dialog" aria-labelledby="modal-escalation-enforcement-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-escalation-enforcement-label">Escalation & Enforcement Mechanisms</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Escalation & Enforcement Mechanisms -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-management-dashboard" tabindex="-1" role="dialog" aria-labelledby="modal-management-dashboard-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-management-dashboard-label">Management & Executive Dashboard</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Management & Executive Dashboard -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAssignForm(uploadId) {
    var form = document.getElementById('assign-form-' + uploadId);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

function savePositions(uploadId) {
    var checkboxes = document.querySelectorAll('#positionOptions-' + uploadId + ' input[type="checkbox"]:checked');
    var positionIds = Array.from(checkboxes).map(function(cb) {
        return cb.value;
    });

    $.post('/learning/general-uploads/assign-positions', {
        upload_id: uploadId,
        position_ids: positionIds,
        _token: '{{ csrf_token() }}'
    })
    .done(function(data) {
        alert('Positions saved successfully');
        toggleAssignForm(uploadId);
        // Optionally refresh the positions display
        location.reload();
    })
    .fail(function(xhr) {
        alert('Error saving positions: ' + xhr.responseJSON.message);
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var select = document.getElementById('content-type-select');
    var coursesList = document.getElementById('courses-list');
    var generalList = document.getElementById('general-list');

    select.addEventListener('change', function() {
        var selected = this.value;
        if (selected === 'courses') {
            coursesList.style.display = 'block';
            generalList.style.display = 'none';
        } else if (selected === 'general') {
            coursesList.style.display = 'none';
            generalList.style.display = 'block';
        } else {
            coursesList.style.display = 'none';
            generalList.style.display = 'none';
        }
    });

    // Topic filter for general uploads
    var topicFilter = document.getElementById('topic-filter');
    if (topicFilter) {
        topicFilter.addEventListener('change', function() {
            var selectedTopic = this.value;
            var items = document.querySelectorAll('#general-list .list-group-item');
            items.forEach(function(item) {
                var topicId = item.getAttribute('data-topic-id');
                if (selectedTopic === '' || topicId == selectedTopic) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Initialize multi-select for each upload
    @php
        $uploadIds = \App\Models\GeneralUpload::pluck('id')->toArray();
    @endphp
    @foreach($uploadIds as $id)
        initMultiSelect({{ $id }});
    @endforeach
});

function initMultiSelect(uploadId) {
    var multiSelect = document.getElementById('positionMultiSelect-' + uploadId);
    if (!multiSelect) return;

    var selected = document.getElementById('positionSelected-' + uploadId);
    var options = document.getElementById('positionOptions-' + uploadId);
    var checkboxes = document.querySelectorAll('#positionOptions-' + uploadId + ' input[type="checkbox"]');

    selected.addEventListener('click', function() {
        options.classList.toggle('active');
        selected.classList.toggle('active');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!multiSelect.contains(event.target)) {
            options.classList.remove('active');
            selected.classList.remove('active');
        }
    });

    // Update selected text
    checkboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            var selectedValues = [];
            checkboxes.forEach(function(cb) {
                if (cb.checked) {
                    selectedValues.push(cb.labels[0].textContent);
                }
            });

            if (selectedValues.length > 0) {
                if (selectedValues.length > 2) {
                    selected.textContent = selectedValues.length + ' positions selected';
                } else {
                    selected.textContent = selectedValues.join(', ');
                }
                selected.classList.add('active');
            } else {
                selected.textContent = 'Select positions';
                selected.classList.remove('active');
            }
        });
    });
}
</script>
@endsection