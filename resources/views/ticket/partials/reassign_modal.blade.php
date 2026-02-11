<!-- Reassign Ticket Modal -->
<div class="modal fade" id="reassignTicketModal" tabindex="-1" role="dialog" aria-labelledby="reassignTicketModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title" id="reassignTicketModalLabel"><i class="fa fa-user"></i> Reassign Ticket</h5>
        </div>
        <form action="{{ url('ticket/reassign') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-body">
                <input type="hidden" name="ticket_id" id="reassignTicketId">
                <div class="form-group">
                    <label>Ticket</label>
                    <div id="reassignTicketName" style="font-weight: bold;"></div>
                </div>
                <div class="form-group">
                    <label>Current Assigned To</label>
                    <div id="reassignCurrentAssigned" style="color: #666;"></div>
                </div>
                <div class="form-group">
                    <label for="reassignOffice">Office</label>
                    <select name="office_id" id="reassignOffice" class="form-control">
                        <option value="">Select Office</option>
                        @foreach($offices as $office)
                            <option value="{{ $office->id }}">{{ $office->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="reassignRole">Role</label>
                    <select name="role_id" id="reassignRole" class="form-control" disabled>
                        <option value="">Select Role</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="reassignTo">Reassign To</label>
                    <select name="assigned_to" id="reassignTo" class="form-control" disabled required>
                        <option value="">Select User</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reassignRemarks">Remarks</label>
                    <textarea name="remarks" id="reassignRemarks" class="form-control" rows="3" placeholder="Add reassignment notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary"><i class="fa fa-user"></i> Reassign</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // When office changes, load roles and reset other fields
    $('#reassignOffice').change(function() {
        var officeId = $(this).val();
        $('#reassignRole').prop('disabled', officeId ? false : true);
        $('#reassignRole').html('<option value="">Select Role</option>');
        $('#reassignTo').prop('disabled', true);
        $('#reassignTo').html('<option value="">Select User</option>');
        
        if (officeId) {
            // Load roles based on office - fetch all roles for now
            var roles = @json($roles);
            $.each(roles, function(index, role) {
                $('#reassignRole').append('<option value="' + role.id + '">' + role.name + '</option>');
            });
        }
    });

    // When role changes, load users
    $('#reassignRole').change(function() {
        var officeId = $('#reassignOffice').val();
        var roleId = $(this).val();
        $('#reassignTo').prop('disabled', roleId ? false : true);
        $('#reassignTo').html('<option value="">Select User</option>');
        
        if (officeId && roleId) {
            // Fetch users by office and role via AJAX
            $.ajax({
                url: '{{ url("ticket/users") }}',
                type: 'GET',
                data: {
                    office_id: officeId,
                    role_id: roleId,
                    type: 'assign'
                },
                success: function(response) {
                    if (response.success && response.users.length > 0) {
                        $.each(response.users, function(index, user) {
                            var displayName = user.first_name + ' ' + (user.last_name || '') + ' (' + user.email + ')';
                            $('#reassignTo').append('<option value="' + user.id + '">' + displayName + '</option>');
                        });
                    } else {
                        $('#reassignTo').append('<option value="">No users found</option>');
                    }
                },
                error: function() {
                    $('#reassignTo').append('<option value="">Error loading users</option>');
                }
            });
        }
    });

    // Handle reassign button click
    $(document).on('click', '.open-reassign-modal', function(){
        var ticketId = $(this).data('ticket-id');
        var ticketName = $(this).data('ticket-name');
        var currentAssigned = $(this).data('current-assigned');
        
        $('#reassignTicketId').val(ticketId);
        $('#reassignTicketName').text(ticketName);
        $('#reassignCurrentAssigned').text(currentAssigned ? currentAssigned : 'Unassigned');
        
        // Reset form
        $('#reassignOffice').val('');
        $('#reassignRole').html('<option value="">Select Role</option>').prop('disabled', true);
        $('#reassignTo').html('<option value="">Select User</option>').prop('disabled', true);
        $('#reassignRemarks').val('');
        
        $('#reassignTicketModal').modal('show');
    });
});
</script>
