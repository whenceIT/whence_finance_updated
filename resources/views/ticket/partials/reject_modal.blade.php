<!-- Reject Ticket Modal -->
<div class="modal fade" id="rejectTicketModal" tabindex="-1" role="dialog" aria-labelledby="rejectTicketModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title" id="rejectTicketModalLabel"><i class="fa fa-times"></i> Reject Ticket</h5>
        </div>
        <form action="{{ url('ticket/reject') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-body">
                <input type="hidden" name="ticket_id" id="rejectTicketId">
                <div class="form-group">
                    <label>Ticket</label>
                    <div id="rejectTicketName" style="font-weight: bold;"></div>
                </div>
                <div class="form-group">
                    <label for="rejectComments">Comments <span class="text-danger">*</span></label>
                    <textarea name="comments" id="rejectComments" class="form-control" rows="4" placeholder="Enter rejection comments..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger"><i class="fa fa-times"></i> Reject & Close</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Handle reject button click
    $(document).on('click', '.open-reject-modal', function(){
        var ticketId = $(this).data('ticket-id');
        var ticketName = $(this).data('ticket-name');
        
        $('#rejectTicketId').val(ticketId);
        $('#rejectTicketName').text(ticketName);
        
        // Reset form
        $('#rejectComments').val('');
        
        $('#rejectTicketModal').modal('show');
    });
});
</script>