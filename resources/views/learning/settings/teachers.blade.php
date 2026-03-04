@extends('layouts.learning')

@section('title', 'Teachers Management - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
    ['label' => 'Teachers Management', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Teachers Management</h1>
    <p>Manage teacher profiles and assign training capabilities</p>
</div>

@php
$user = Sentinel::getUser();
$role = $user ? $user->roles->first() : null;
$isAdmin = $role && $role->id == 1;

// Get all offices for dropdown
$offices = \App\Models\Office::orderBy('name', 'asc')->get();

// Get all users who are trainers
$trainers = \App\Models\User::where('istrainer', 1)->with('office', 'roles')->get();
@endphp

<!-- Admin Section: Update istrainer Field -->
@if($isAdmin)
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h3 class="card-title mb-0">
            <i class="fa fa-user-plus mr-2"></i>
            Assign Trainer Status
        </h3>
        <p class="card-text mb-0 mt-2" style="opacity: 0.9; font-size: 14px;">
            Select Office → Role → Users to grant trainer status (istrainer = 1)
        </p>
    </div>
    <div class="card-body">
        <!-- Step 1: Select Office -->
        <div class="form-group">
            <label for="officeSelect" class="font-weight-bold">
                Step 1: Select Office
                <span class="text-danger">*</span>
            </label>
            <select id="officeSelect" class="form-control select2" style="width: 100%;">
                <option value="">-- Select Office --</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}">{{ $office->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Step 2: Select Role -->
        <div class="form-group">
            <label for="roleSelect" class="font-weight-bold">
                Step 2: Select Role
                <span class="text-danger">*</span>
            </label>
            <select id="roleSelect" class="form-control select2" style="width: 100%;" disabled>
                <option value="">-- Select Role --</option>
            </select>
        </div>

        <!-- Step 3: Select Users -->
        <div class="form-group">
            <label for="userSelect" class="font-weight-bold">
                Step 3: Select Users
                <span class="text-danger">*</span>
            </label>
            <select id="userSelect" class="form-control select2" style="width: 100%;" multiple disabled>
            </select>
            <small class="form-text text-muted">
                Hold Ctrl/Cmd to select multiple users
            </small>
        </div>

        <!-- Selected Users Summary -->
        <div id="selectedUsersSummary" class="alert alert-info" style="display: none;">
            <strong>Selected Users:</strong> <span id="selectedCount">0</span> user(s)
            <ul id="selectedUsersList" class="mb-0 mt-2" style="max-height: 150px; overflow-y: auto;"></ul>
        </div>

        <!-- Submit Button -->
        <div class="form-group mt-3">
            <button type="button" id="submitTrainerBtn" class="btn btn-primary" disabled>
                <i class="fa fa-check mr-1"></i>
                Grant Trainer Status
            </button>
        </div>
    </div>
</div>
@endif

<!-- Current Trainers Table (Vertical Layout) -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fa fa-user-tie mr-2"></i>
            Current Trainers
        </h3>
    </div>
    <div class="card-body p-0">
        @if($trainers->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Office</th>
                        <th>Role</th>
                        <th>Designation</th>
                        <th>Department</th>
                        <th>Status</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trainers as $index => $trainer)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $trainer->first_name }} {{ $trainer->last_name }}</strong>
                        </td>
                        <td>{{ $trainer->email }}</td>
                        <td>
                            @if($trainer->office)
                                {{ $trainer->office->name }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($trainer->roles && $trainer->roles->count() > 0)
                                @foreach($trainer->roles as $r)
                                    <span class="badge badge-info">{{ $r->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">No Role</span>
                            @endif
                        </td>
                        <td>{{ $trainer->designation ?? 'N/A' }}</td>
                        <td>{{ $trainer->department ?? 'N/A' }}</td>
                        <td>
                            @if($trainer->istrainer == 1)
                                <span class="badge badge-success">
                                    <i class="fa fa-check mr-1"></i>Trainer
                                </span>
                            @else
                                <span class="badge badge-secondary">Not Trainer</span>
                            @endif
                        </td>
                        <td>
                            <form action="{{ route('learning.settings.teachers.remove-trainer', $trainer->id) }}" 
                                  method="POST" 
                                  style="display: inline;"
                                  onsubmit="return confirm('Are you sure you want to revoke trainer status from {{ $trainer->first_name }} {{ $trainer->last_name }}?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Revoke Trainer Status">
                                    <i class="fa fa-times"></i> Revoke
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="fa fa-user-tie text-muted" style="font-size: 64px; opacity: 0.3;"></i>
            <p class="text-muted mt-3">No trainers found.</p>
            <p class="text-muted">Use the form above to assign trainer status to users.</p>
        </div>
        @endif
    </div>
</div>

<!-- Non-Dismissible Confirmation Modal -->
<div class="modal fade" id="confirmTrainerModal" tabindex="-1" role="dialog" 
     data-backdrop="static" data-keyboard="false" aria-labelledby="confirmTrainerModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="confirmTrainerModalTitle">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Confirm Trainer Assignment
                </h5>
            </div>
            <div class="modal-body">
                <p>You are about to grant trainer status (istrainer = 1) to the following user(s):</p>
                <ul id="modalUsersList" class="mb-3" style="max-height: 200px; overflow-y: auto;"></ul>
                <p class="mb-0"><strong>This action will:</strong></p>
                <ul class="mb-0">
                    <li>Enable these users to manage training materials</li>
                    <li>Allow them to access the Teachers Management section</li>
                    <li>Grant permissions to create and manage course content</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fa fa-times mr-1"></i> Cancel
                </button>
                <form id="confirmTrainerForm" action="{{ route('learning.settings.teachers.update-trainer') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_ids" id="selectedUserIds">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-check mr-1"></i> Confirm & Grant
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        placeholder: function() {
            return $(this).data('placeholder') || '-- Select --';
        }
    });

    // Step 1: Office Selection - Load ALL Roles (not dependent on office)
    $('#officeSelect').on('change', function() {
        var officeId = $(this).val();
        var $roleSelect = $('#roleSelect');
        var $userSelect = $('#userSelect');
        
        $roleSelect.prop('disabled', true).empty().append('<option value="">-- Select Role --</option>');
        $userSelect.prop('disabled', true).empty();
        $('#selectedUsersSummary').hide();
        $('#submitTrainerBtn').prop('disabled', true);
        
        // Always load ALL roles when office changes
        $.ajax({
            url: '{{ url("learning/api/all-roles") }}',
            type: 'GET',
            success: function(roles) {
                if (roles.length > 0) {
                    $.each(roles, function(index, role) {
                        $roleSelect.append('<option value="' + role.id + '">' + role.name + '</option>');
                    });
                    $roleSelect.prop('disabled', false);
                } else {
                    $roleSelect.append('<option value="">No roles found</option>');
                }
            },
            error: function() {
                alert('Error loading roles. Please try again.');
            }
        });
    });

    // Step 2: Role Selection - Load Users by Role Only (not office + role)
    $('#roleSelect').on('change', function() {
        var roleId = $(this).val();
        var $userSelect = $('#userSelect');
        
        $userSelect.prop('disabled', true).empty();
        $userSelect.append('<option value="">Loading users...</option>');
        $('#selectedUsersSummary').hide();
        $('#submitTrainerBtn').prop('disabled', true);
        
        if (roleId) {
            // Fetch users by role only
            $.ajax({
                url: '{{ url("learning/api/users-by-role") }}/' + roleId,
                type: 'GET',
                success: function(users) {
                    $userSelect.empty();
                    if (users.length > 0) {
                        $.each(users, function(index, user) {
                            var officeInfo = user.office ? ' (' + user.office.name + ')' : '';
                            var displayName = user.first_name + ' ' + user.last_name + officeInfo;
                            $userSelect.append('<option value="' + user.id + '">' + displayName + '</option>');
                        });
                        $userSelect.prop('disabled', false);
                    } else {
                        $userSelect.append('<option value="">No users found with selected role</option>');
                    }
                },
                error: function() {
                    $userSelect.empty();
                    $userSelect.append('<option value="">Error loading users</option>');
                    alert('Error loading users. Please try again.');
                }
            });
        }
    });

    // Step 3: User Selection - Update Summary and Enable Submit
    $('#userSelect').on('change', function() {
        var selectedUsers = $(this).val() || [];
        var $summary = $('#selectedUsersSummary');
        var $list = $('#selectedUsersList');
        var $btn = $('#submitTrainerBtn');
        
        $('#selectedCount').text(selectedUsers.length);
        $list.empty();
        
        if (selectedUsers.length > 0) {
            // Get selected user names
            var selectedOptions = $(this).find('option:selected');
            selectedOptions.each(function() {
                $list.append('<li>' + $(this).text() + '</li>');
            });
            $summary.show();
            $btn.prop('disabled', false);
        } else {
            $summary.hide();
            $btn.prop('disabled', true);
        }
    });

    // Submit Button - Show Confirmation Modal
    $('#submitTrainerBtn').on('click', function() {
        var selectedUsers = $('#userSelect').val() || [];
        var $modalList = $('#modalUsersList');
        var $input = $('#selectedUserIds');
        
        $modalList.empty();
        
        if (selectedUsers.length > 0) {
            var selectedOptions = $('#userSelect').find('option:selected');
            selectedOptions.each(function() {
                $modalList.append('<li>' + $(this).text() + '</li>');
            });
            
            $input.val(selectedUsers.join(','));
            $('#confirmTrainerModal').modal('show');
        }
    });
});
</script>
@endsection
