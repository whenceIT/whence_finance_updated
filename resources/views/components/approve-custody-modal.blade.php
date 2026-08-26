<div class="modal fade" id="approveCustodyModal" tabindex="-1" role="dialog" aria-labelledby="approveCustodyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white" id="approveCustodyModalLabel">
                    <i class="fa fa-check-circle"></i> Approve Vehicle Custody
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #fff;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="approveCustodyMessage"></p>
                
                <div id="approveCustodyDetails" style="background: #f8f9fa; border-radius: 8px; padding: 15px; margin-top: 10px;">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Decline
                </button>
                <button type="button" class="btn btn-primary" id="confirmApproveCustody">
                    <i class="fa fa-check"></i> Accept
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentCustodyId = null;
    
    window.showApproveCustodyModal = function(custody) {
        currentCustodyId = custody.id;
        
        const message = `Approve Vehicle Custody: <strong>${custody.vehicle_registration || 'N/A'}</strong> is under your custody and supervision.`;
        document.getElementById('approveCustodyMessage').innerHTML = message;
        
        const details = `
            <strong>Vehicle:</strong> ${custody.vehicle_make || ''} ${custody.vehicle_model || ''} (${custody.vehicle_registration || 'N/A'})<br>
            <strong>Received At:</strong> ${custody.received_at || 'N/A'}<br>
            <strong>Garage:</strong> ${custody.garage_name || 'N/A'}<br>
            <strong>Location:</strong> ${custody.garage_location || 'N/A'}<br>
            <strong>Contact Person:</strong> ${custody.garage_contact_person || 'N/A'}<br>
            <strong>Phone:</strong> ${custody.garage_contact_phone || 'N/A'}
        `;
        document.getElementById('approveCustodyDetails').innerHTML = details;
        
        $('#approveCustodyModal').modal('show');
    };
    
    document.getElementById('confirmApproveCustody').addEventListener('click', function() {
        if (!currentCustodyId) return;
        
        fetch(`/vehicle-custody/${currentCustodyId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                $('#approveCustodyModal').modal('hide');
                location.reload();
            } else {
                alert('Failed to approve custody: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving custody');
        });
    });
});
</script>
