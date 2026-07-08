@extends('layouts.master')

@section('title')
    Branch Blocking List
@endsection

@section('content')
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
                        <select class="form-control" id="office_id" name="office_id" required>
                            <option value="">Select Office</option>
                            @foreach($offices ?? [] as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Reason Type Dropdown -->
                    <div class="form-group">
                        <label for="reason_type">Reason Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="reason_type" name="reason_type" required>
                            <option value="">Select Reason Type</option>
                            <option value="Building and infrastructure">Building and infrastructure</option>
                            <option value="Statutory">Statutory</option>
                            <option value="Administration fees">Administration fees</option>
                            <option value="Debt Setup Cost">Debt Setup Cost</option>
                        </select>
                    </div>
                    
                    <!-- Status Dropdown -->
                    <div class="form-group">
                        <label for="reason_status">Status <span class="text-danger">*</span></label>
                        <select class="form-control" id="reason_status" name="reason_status" required>
                            <option value="">Select Status</option>
                            <option value="not paid">not paid</option>
                            <option value="balance">balance</option>
                        </select>
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
        
        if (type && status) {
            $('#reason').val(type + ' - ' + status);
        } else {
            $('#reason').val('');
        }
    }
    
    // Listen for changes on both dropdowns
    $('#reason_type').on('change', updateReason);
    $('#reason_status').on('change', updateReason);

    // Handle form submission
    $('#blockageForm').on('submit', function(e) {
        e.preventDefault();
        
        // Clear previous errors
        $('#error-messages').hide().empty();
        
        // Disable submit button
        $('#saveBtn').prop('disabled', true).text('Saving...');
        
        // Get form data
        var formData = {
            office_id: $('#office_id').val(),
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
                        // Remove the row from the table
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
});
</script>

@endsection