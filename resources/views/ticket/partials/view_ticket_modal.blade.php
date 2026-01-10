<!-- View Ticket Modal -->
<div class="modal fade" id="viewTicketModal" tabindex="-1" role="dialog" aria-labelledby="viewTicketModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h5 class="modal-title" id="viewTicketModalLabel"><svg xmlns="http://www.w3.org/2000/svg" width="21" height="21" fill="currentColor" class="bi bi-ticket-perforated-fill" viewBox="0 0 16 16">
                <path d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5V6a.5.5 0 0 1-.5.5 1.5 1.5 0 0 0 0 3 .5.5 0 0 1 .5.5v1.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 11.5V10a.5.5 0 0 1 .5-.5 1.5 1.5 0 1 0 0-3A.5.5 0 0 1 0 6zm4-1v1h1v-1zm1 3v-1H4v1zm7 0v-1h-1v1zm-1-2h1v-1h-1zm-6 3H4v1h1zm7 1v-1h-1v1zm-7 1H4v1h1zm7 1v-1h-1v1zm-8 1v1h1v-1zm7 1h1v-1h-1z"/>
                </svg><span id="viewTicketName"></span>
            </h5>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label>Ticket Number</label>
                <div id="ticketNumber">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Opened By</label>
                <div id="ticketOpenedBy">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Phone</label>
                <div id="ticketOpenedPhone">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <div id="ticketOpenedEmail">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Time ago Opened</label>
                <div id="ticketOpenedAt">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Description</label>
                <div id="ticketDescription" style="white-space: pre-wrap;">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Days to Close</label>
                <div id="ticketDays">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <div id="ticketRemarks" style="white-space: pre-wrap;">&mdash;</div>
            </div>
            <div class="form-group">
                <label>Rating</label>
                <div id="ticketRatingStars"></div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>