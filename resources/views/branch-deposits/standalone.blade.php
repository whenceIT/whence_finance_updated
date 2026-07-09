@extends('layouts.master')

@section('title')
    Deposit Deadline Management
@endsection
@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
    $deadlineName = isset($deadline) ? $deadline->name : 'Building Deposit';
    $deadlineDateValue = isset($deadline) && $deadline->countdown_date ? \Carbon\Carbon::parse($deadline->countdown_date)->format('Y-m-d\TH:i') : '';
@endphp
@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Deposit Deadline Settings</h3>
            </div>
            <div class="box-body" style="min-height: 300px;">
                <form id="deadlineForm">
                    <div class="form-group">
                        <label for="deadline_name">Deadline Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="deadline_name" name="name" 
                               value="{{ old('name', $deadlineName) }}" required>
                    </div>
                    <div class="form-group">
                        <label for="deadline_date">Countdown Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="deadline_date" name="countdown_date" 
                               value="{{ old('countdown_date', $deadlineDateValue) }}" required>
                        <small class="text-muted">Set the deadline for deposit reminders</small>
                    </div>
                    <div id="deadline-error" class="text-danger" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary" id="deadlineSaveBtn">Save Deadline</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Blocked List</h3>
            </div>
            <div class="box-body" style="min-height: 300px;">
                <!-- Add New Blockage Button -->
                <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#blockageModal">
                    <i class="fa fa-plus"></i> Add Blockage
                </button>

                <!-- Blockages Table -->
                <table class="table table-bordered table-striped" id="blockages-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Office</th>
                            <th>Reason</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blockages ?? [] as $blockage)
                        <tr id="blockage-row-{{ $blockage->id }}">
                            <td>{{ $blockage->id }}</td>
                            <td>{{ $blockage->office?->name ?? 'N/A' }}</td>
                            <td>{{ $blockage->reason }}</td>
                            <td>{{ $blockage->created_at?->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm unblock-btn" data-id="{{ $blockage->id }}">
                                    <i class="fa fa-unlock"></i> Unblock
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Blockage Modal -->
<div class="modal fade" id="blockageModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Add Blockage</h4>
            </div>
            <form id="blockageForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="office_id">Office <span class="text-danger">*</span></label>
                        <select class="form-control select2" id="office_id" name="office_id[]" multiple required style="width: 100%;">
                            @foreach($offices ?? [] as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Reason Type Dropdown -->
                    <div class="form-group">
                        <div>
                            <label for="reason_type">Reason Type <span class="text-danger">*</span></label>
                            <select class="form-control" id="reason_type" name="reason_type" required>
                                <option value="">Select Reason Type</option>
                                <option value="Building & Infrastructure fee deposit">Building and infrastructure</option>
                                <option value="Statutory payments deposit">Statutory</option>
                                <option value="Administration Department fee deposit">Administration fees</option>
                                <option value="the K5,000 minimum, Debt Setup Cost">K5,000 minimum, Debt Setup Cost</option>
                            </select>
                        </div>
                        <div>
                            <label for="reason_status">Status <span class="text-danger">*</span></label>
                            <select class="form-control" id="reason_status" name="reason_status" required>
                                <option value="">Select Status</option>
                                <option value="Not paid">Not paid</option>
                                <option value="You have balance">Have a balance</option>
                            </select>
                        </div>
                    </div>

                    <!-- Month Selector - User Friendly with Checkboxes -->
                    <div class="form-group">
                        <label>Months <span class="text-danger">*</span></label>
                        <div id="month-selector" class="month-selector-container">
                            <?php
                                $months = [
                                    1 => 'January', 2 => 'February', 3 => 'March',
                                    4 => 'April', 5 => 'May', 6 => 'June',
                                    7 => 'July', 8 => 'August', 9 => 'September',
                                    10 => 'October', 11 => 'November', 12 => 'December'
                                ];
                                $currentMonth = date('n'); // 1-12
                            ?>
                            @foreach($months as $monthNum => $monthName)
                                <label class="month-checkbox-label {{ $monthNum == $currentMonth ? 'selected' : '' }}">
                                    <input type="checkbox" class="month-checkbox" name="months[]" value="{{ $monthName }} {{ date('Y') }}" {{ $monthNum == $currentMonth ? 'checked' : '' }}>
                                    <span>{{ $monthName }}</span>
                                </label>
                            @endforeach
                        </div>
                        <small class="text-muted">Click on months to select/deselect</small>
                    </div>
                    
                    <!-- Auto-generated Reason Field -->
                    <div class="form-group">
                        <label for="reason">Reason (Auto-generated) <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" maxlength="1000" readonly style="background-color: #f8f9fa;"></textarea>
                        <small class="text-muted">The reason will be auto-generated based on your selections above.</small>
                    </div>
                    
                    <div id="error-messages" class="text-danger" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#blockages-table').DataTable({
        order: [[0, 'desc']]
    });

    // Auto-generate reason field when selections change
    function updateReason() {
        var type = $('#reason_type').val();
        var status = $('#reason_status').val();
        var selectedMonths = [];
        
        // Get selected months from checkboxes
        $('.month-checkbox:checked').each(function() {
            selectedMonths.push($(this).val());
        });
        
        if (type && status) {
            // Build the reason with type and status first
            var reason = status + ' - in ' + type ;
            
            // Add months if selected
            if (selectedMonths.length > 0) {
                reason += '\nFor Months of: ' + selectedMonths.join(', ');
            }
            
            $('#reason').val(reason);
        } else {
            $('#reason').val('');
        }
    }
    
    // Listen for changes on all inputs
    $('#reason_type').on('change', updateReason);
    $('#reason_status').on('change', updateReason);
    
    // Handle month checkbox clicks
    $('.month-checkbox').on('click', updateReason);
    
    // Initialize reason on page load
    updateReason();

    // Initialize select2 for offices
    $('.select2').select2({
        width: '100%',
        placeholder: 'Select offices',
        allowClear: true
    });

    // Handle form submission
    $('#blockageForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('#error-messages').hide().empty();
        
        // Disable submit button
        $('#saveBtn').prop('disabled', true).text('Saving...');
        
        // Get form data
        var selectedOffices = $('#office_id').val() || [];
        
        var formData = {
            office_id: selectedOffices,
            reason: $('#reason').val(),
            _token: '{{ csrf_token() }}'
        };
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("blockages.store") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    // Show success message
                    toastr.success(response.message);
                    
                    // Reset form
                    $('#blockageForm')[0].reset();
                    
                    // Close modal
                    $('#blockageModal').modal('hide');
                    
                    // Reload table data (you can also append the new row)
                    location.reload();
                }
            },
            error: function(xhr) {
                // Handle validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul>';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul>';
                    $('#error-messages').html(errorHtml).show();
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                // Re-enable submit button
                $('#saveBtn').prop('disabled', false).text('Save');
            }
        });
    });

    // Handle unblock button click
    $(document).on('click', '.unblock-btn', function() {
        var blockageId = $(this).data('id');
        
        if (confirm('Are you sure you want to unblock this office?')) {
            $.ajax({
                url: '{{ route("blockages.destroy", ":id") }}'.replace(':id', blockageId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#blockage-row-' + blockageId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to unblock. Please try again.');
                }
            });
        }
    });

    // Handle deadline form submission
    $('#deadlineForm').on('submit', function(e) {
        e.preventDefault();
        
        $('#deadline-error').hide().empty();
        $('#deadlineSaveBtn').prop('disabled', true).text('Saving...');
        
        var formData = {
            name: $('#deadline_name').val(),
            countdown_date: $('#deadline_date').val(),
            _token: '{{ csrf_token() }}'
        };
        
        $.ajax({
            url: '{{ route("deposits.deadline.update") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul>';
                    $.each(errors, function(key, value) {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul>';
                    $('#deadline-error').html(errorHtml).show();
                } else {
                    toastr.error('An error occurred. Please try again.');
                }
            },
            complete: function() {
                $('#deadlineSaveBtn').prop('disabled', false).text('Save Deadline');
            }
        });
    });
});
</script>

@include('components.deposit-deadline-modal')
@include('components.setup-debt-reminder')

@endsection