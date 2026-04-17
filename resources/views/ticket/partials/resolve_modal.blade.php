<!-- Resolve Ticket Modal -->
<div class="modal fade" id="resolveTicketModal" tabindex="-1" role="dialog" aria-labelledby="resolveTicketModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title" id="resolveTicketModalLabel"><i class="fa fa-check"></i> Resolve & Close Ticket</h5>
        </div>
        <form action="{{ url('ticket/resolve') }}" method="POST">
            {{ csrf_field() }}
            <div class="modal-body">
                <input type="hidden" name="ticket_id" id="resolveTicketId">
                <div class="form-group">
                    <label>Ticket</label>
                    <div id="resolveTicketName" style="font-weight: bold;"></div>
                </div>
                <div class="form-group">
                    <label for="resolveComments">Resolution Comment <span class="text-danger">*</span></label>
                    <textarea name="comments" id="resolveComments" class="form-control" rows="4" placeholder="Enter resolution details..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success"><i class="fa fa-check"></i> Resolve & Close</button>
            </div>
        </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    // Handle resolve button click
    $(document).on('click', '.open-resolve-modal', function(){
        var ticketId = $(this).data('ticket-id');
        var ticketName = $(this).data('ticket-name');

        $('#resolveTicketId').val(ticketId);
        $('#resolveTicketName').text(ticketName);

        // Reset form
        $('#resolveComments').val('');

        $('#resolveTicketModal').modal('show');
    });
});
</script>