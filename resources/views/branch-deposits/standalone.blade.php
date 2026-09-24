@extends('layouts.master')

@section('title')
    Deposit Deadline Management
@endsection
@php
    $blockerUser = Sentinel::getUser();
    $debtBlocker = \App\Helpers\BlockerHelper::debt_blocker($blockerUser);
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
                        <select class="form-control" id="deadline_name" name="name" required>
                            <option value="">Select Deadline Name</option>
                            <option value="Administration Department fee deposit">Administration Department fee deposit</option>
                            <option value="Managers Housing deposit">Managers Housing deposit</option>
                            <option value="Building & Infrastructure fee deposits">Building & Infrastructure fee deposits</option>
                            <option value="Salaries deposits">Salaries deposits</option>
                            <option value="Statutory payments deposits">Statutory payments deposits</option>
                            <option value="Savings deposits">Savings deposits</option>
                            <option value="Debt Setup Cost">Debt Setup Cost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="deadline_date">Countdown Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control" id="deadline_date" name="countdown_date" required>
                        <small class="text-muted">Set the deadline for deposit reminders</small>
                    </div>
                    <div id="deadline-error" class="text-danger" style="display:none;"></div>
                    <button type="submit" class="btn btn-primary" id="deadlineSaveBtn">Add Deadline</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- List all deadlines as cards (ordered by countdown date) -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">All Deadlines</h3>
                    </div>
                    <div class="box-body">
                        @if($deadlines->isNotEmpty())
                            <div class="row">
                                @foreach($deadlines as $deadline)
                                <div class="col-md-2 col-sm-3 col-xs-4" id="deadline-card-{{ $deadline->id }}">
                                    <div class="small-box bg-aqua" style="cursor: default; min-height: 160px;">
                                        <div class="inner">
                                            <h3 style="font-size: 15px; margin-bottom: 8px;">
                                                {{ $deadline->name }}
                                                <button type="button" class="btn btn-danger btn-xs delete-deadline-btn" data-id="{{ $deadline->id }}" style="float: right; margin-top: -4px;">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </h3>
                                            <p>
                                                <i class="fa fa-calendar"></i> {{ \Carbon\Carbon::parse($deadline->countdown_date)->format('Y-m-d H:i') }}
                                            </p>
                                            <p style="margin-bottom: 0;">
                                                <i class="fa fa-clock-o"></i> Created: {{ $deadline->created_at->format('Y-m-d H:i:s') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center" style="padding: 40px; color: #888;">
                                <i class="fa fa-clock-o" style="font-size: 48px; color: #ccc;"></i>
                                <p style="margin-top: 15px; font-size: 16px;">No deadlines recorded yet</p>
                            </div>
                        @endif
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
                                <button type="button" class="btn btn-info btn-sm view-movements-btn"
                                    data-id="{{ $blockage->id }}"
                                    data-office="{{ $blockage->office?->name ?? 'N/A' }}">
                                    <i class="fa fa-list"></i> Track Movements
                                </button>
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

<!-- Fund Movements Modal -->
<div class="modal fade" id="fundMovementsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    Fund Movements &mdash; <span id="fm-office-name"></span>
                </h4>
            </div>
            <div class="modal-body">
                <!-- Blockage context -->
                <div id="fm-blockage-info" class="alert alert-warning" style="display:none;"></div>

                <!-- Loading spinner -->
                <div id="fm-loading" class="text-center" style="padding: 30px; display:none;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p>Loading movements&hellip;</p>
                </div>

                <!-- Empty state -->
                <div id="fm-empty" class="text-center" style="padding: 30px; display:none;">
                    <i class="fa fa-inbox fa-2x" style="color:#ccc;"></i>
                    <p style="margin-top:10px; color:#888;">No fund movements found for this office.</p>
                </div>

                <!-- Movements table -->
                <div id="fm-table-wrap" style="display:none; overflow-x:auto;">
                    <table class="table table-bordered table-striped table-condensed" id="fm-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Payee</th>
                                <th>Amount</th>
                                <th>Method</th>
                                <th>Status</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody id="fm-tbody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                                <option value="Administration Department fee deposit">Administration Department fee deposit</option>
                                <option value="Managers Housing deposit">Managers Housing deposit</option>
                                <option value="Building & Infrastructure fee deposits">Building & Infrastructure fee deposits</option>
                                <option value="Salaries deposits">Salaries deposits</option>
                                <option value="Statutory payments deposits">Statutory payments deposits</option>
                                <option value="Savings deposits">Savings deposits</option>
                                <option value="Branch Setup Debt">Branch Setup Debt</option>
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
            url: '{{ route("deposits.deadline.store") }}',
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
                $('#deadlineSaveBtn').prop('disabled', false).text('Add Deadline');
            }
        });
    });

    // Handle view fund movements button
    $(document).on('click', '.view-movements-btn', function () {
        var blockageId  = $(this).data('id');
        var officeName  = $(this).data('office');

        // Reset modal state
        $('#fm-office-name').text(officeName);
        $('#fm-blockage-info').hide().empty();
        $('#fm-loading').show();
        $('#fm-empty').hide();
        $('#fm-table-wrap').hide();
        $('#fm-tbody').empty();

        $('#fundMovementsModal').modal('show');

        $.ajax({
            url: '/api/fund-movements/blocked/' + blockageId,
            type: 'GET',
            success: function (response) {
                $('#fm-loading').hide();

                if (response.blockage) {
                    $('#fm-blockage-info')
                        .html('<strong>Blocked reason:</strong> ' + response.blockage.reason +
                              (response.blockage.blocked_at ? ' &mdash; <strong>Blocked since:</strong> ' + response.blockage.blocked_at : ''))
                        .show();
                }

                if (!response.success || !response.data || response.data.length === 0) {
                    $('#fm-empty').show();
                    return;
                }

                var rows = '';
                $.each(response.data, function (i, m) {
                    var createdBy = (m.user)
                        ? (m.user.first_name + ' ' + m.user.last_name)
                        : (m.created_by || 'N/A');

                    rows += '<tr>' +
                        '<td>' + (i + 1) + '</td>' +
                        '<td>' + (m.transaction_date || 'N/A') + '</td>' +
                        '<td>' + (m.movement_type || 'N/A') + '</td>' +
                        '<td>' + (m.title || 'N/A') + '</td>' +
                        '<td>' + (m.payee_name || 'N/A') + '</td>' +
                        '<td>' + (m.amount !== null ? parseFloat(m.amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : 'N/A') + '</td>' +
                        '<td>' + (m.payment_method || 'N/A') + '</td>' +
                        '<td><span class="label label-' + (m.status === 'approved' ? 'success' : (m.status === 'pending' ? 'warning' : 'default')) + '">' + (m.status || 'N/A') + '</span></td>' +
                        '<td>' + createdBy + '</td>' +
                    '</tr>';
                });

                $('#fm-tbody').html(rows);
                $('#fm-table-wrap').show();
            },
            error: function (xhr) {
                $('#fm-loading').hide();
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Failed to load fund movements.';
                toastr.error(msg);
                $('#fundMovementsModal').modal('hide');
            }
        });
    });

    // Handle delete deadline button click
    $(document).on('click', '.delete-deadline-btn', function() {
        var deadlineId = $(this).data('id');

        if (confirm('Are you sure you want to delete this deadline?')) {
            $.ajax({
                url: '{{ route("deposits.deadline.destroy", ":id") }}'.replace(':id', deadlineId),
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        toastr.success(response.message);
                        $('#deadline-row-' + deadlineId).fadeOut(300, function() {
                            $(this).remove();
                        });
                    }
                },
                error: function(xhr) {
                    toastr.error('Failed to delete deadline. Please try again.');
                }
            });
        }
    });
});
</script>

@include('components.deposit-deadline-modal')
<!-- @include('components.setup-debt-reminder') -->

@endsection