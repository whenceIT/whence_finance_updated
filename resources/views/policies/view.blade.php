@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 border rounded p-4 bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Company Policies</h2>
                <div>
                    <button type="button" class="btn btn-success me-2" onclick="acceptAllPolicies()">Accept All</button>
                    <button type="button" class="btn btn-danger me-2" onclick="declineAllPolicies()">Decline All</button>
                    <!-- <button type="button" class="btn btn-warning me-2" onclick="resetAllPolicies()">Reset All</button> -->
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($policies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="ps-3">Policy Title</th>
                                        <th scope="col">File Type</th>
                                        <th scope="col">File Size</th>
                                        <th scope="col" class="text-center">Your Response</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($policies as $policy)
                                        <tr data-policy-id="{{ $policy->id }}">
                                            <td class="ps-3">{{ $policy->title }}</td>
                                            <td>{{ strtoupper(pathinfo($policy->file_name, PATHINFO_EXTENSION)) }}</td>
                                            <td>{{ round($policy->file_size / 1024, 2) }} KB</td>
                                            <td class="text-center">
                                                @if($policy->userPolicyResponses->isNotEmpty())
                                                    @if($policy->userPolicyResponses->first()->status == 'accepted')
                                                        <span class="label label-success">Accepted</span>
                                                    @elseif($policy->userPolicyResponses->first()->status == 'declined')
                                                        <span class="label label-danger">Declined</span>
                                                    @elseif($policy->userPolicyResponses->first()->status == 'pending')
                                                        <span class="label label-warning">Pending</span>
                                                    @endif
                                                @else
                                                    <span class="label label-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td class="text-center">

                                                <a href="{{ $policy->file_url }}" download class="btn btn-sm btn-success" title="Download">
                                                    <i></i> Download
                                                </a>
                                                <button type="button" class="btn btn-sm btn-primary" title="View Preview" onclick="openPolicyModal({{ $policy->id }}, '{{ $policy->title }}', '{{ $policy->file_url }}', '{{ $policy->file_type }}')">
                                                    <i></i> View Preview
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-3" role="alert">
                            No policies found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Policy Preview Modal -->
<div id="policyModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1050; align-items: center; justify-content: center;">
    <div class="modal-content-custom" style="background: white; width: 95%; max-width: 1400px; height: 95%; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); overflow: hidden; display: flex; flex-direction: column;">
        <div class="modal-header-custom" style="padding: 10px 15px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
            <h5 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600;"></h5>
            <button type="button" onclick="closePolicyModal()" style="background: none; border: none; font-size: 28px; cursor: pointer; color: #6c757d; line-height: 1;">&times;</button>
        </div>
        <div class="modal-body-custom" id="modalBody" style="flex: 1; padding: 0; overflow: hidden;">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer-custom" id="modalFooter" style="padding: 10px 15px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa;">
            <!-- Buttons will be loaded here -->
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="confirmationModalLabel">Confirm Action</h4>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                Are you sure?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary" id="confirmYes">Yes</button>
            </div>
        </div>
    </div>
</div>

@include('policies.signature_animation')

<script>
let confirmCallback = null;

function showConfirmation(message, callback) {
    document.getElementById('confirmationModalBody').innerText = message;
    confirmCallback = callback;
    $('#confirmationModal').modal('show');
}

document.getElementById('confirmYes').addEventListener('click', function() {
    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
    $('#confirmationModal').modal('hide');
});

function acceptAllPolicies() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const rows = document.querySelectorAll('tr[data-policy-id]');
    const nonAcceptedPolicies = [];

    rows.forEach(row => {
        const responseCell = row.querySelector('td:nth-child(4)');
        if (responseCell && responseCell.textContent.trim() !== 'Accepted') {
            nonAcceptedPolicies.push(row.getAttribute('data-policy-id'));
        }
    });

    if (nonAcceptedPolicies.length === 0) {
        alert('All policies are already accepted.');
        return;
    }

    showConfirmation('Are you sure you want to accept all non-accepted policies?', () => {
        const button = document.querySelector('button[onclick="acceptAllPolicies()"]');
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

        let completed = 0;
        nonAcceptedPolicies.forEach(policyId => {
            fetch(`/policies/${policyId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: `status=accepted&_token=${csrfToken}`
            }).then(response => {
                completed++;
                if (completed === nonAcceptedPolicies.length) {
                    location.reload();
                }
            });
        });
    });
}

function declineAllPolicies() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const rows = document.querySelectorAll('tr[data-policy-id]');
    const nonDeclinedPolicies = [];

    rows.forEach(row => {
        const responseCell = row.querySelector('td:nth-child(4)');
        if (responseCell && responseCell.textContent.trim() !== 'Declined') {
            nonDeclinedPolicies.push(row.getAttribute('data-policy-id'));
        }
    });

    if (nonDeclinedPolicies.length === 0) {
        alert('All policies are already declined.');
        return;
    }

    showConfirmation('Are you sure you want to decline all non-declined policies?', () => {
        const button = document.querySelector('button[onclick="declineAllPolicies()"]');
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

        let completed = 0;
        nonDeclinedPolicies.forEach(policyId => {
            fetch(`/policies/${policyId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: `status=declined&_token=${csrfToken}`
            }).then(response => {
                completed++;
                if (completed === nonDeclinedPolicies.length) {
                    location.reload();
                }
            });
        });
    });
}

function resetAllPolicies() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const rows = document.querySelectorAll('tr[data-policy-id]');
    const respondedPolicies = [];

    rows.forEach(row => {
        const responseCell = row.querySelector('td:nth-child(4)');
        if (responseCell && responseCell.textContent.trim() !== 'Pending') {
            respondedPolicies.push(row.getAttribute('data-policy-id'));
        }
    });

    if (respondedPolicies.length === 0) {
        alert('All policies are already pending.');
        return;
    }

    showConfirmation('Are you sure you want to reset all policies to pending?', () => {
        const button = document.querySelector('button[onclick="resetAllPolicies()"]');
        button.disabled = true;
        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';

        let completed = 0;
        respondedPolicies.forEach(policyId => {
            fetch(`/policies/${policyId}/respond`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: `status=pending&_token=${csrfToken}`
            }).then(response => {
                completed++;
                if (completed === respondedPolicies.length) {
                    location.reload();
                }
            });
        });
    });
}

function openPolicyModal(policyId, title, url, fileType) {
    document.getElementById('modalTitle').innerHTML = title;

    // Show loading spinner
    document.getElementById('modalBody').innerHTML = `
        <div style="display: flex; justify-content: center; align-items: center; height: 100%; font-size: 18px; color: #666;">
            <i class="fa fa-spinner fa-spin" style="margin-right: 10px;"></i> Loading document...
        </div>
    `;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const footerContent = `
        <button type="button" class="btn btn-success" onclick="acceptPolicy(${policyId})">Accept</button>
        <form action="/policies/${policyId}/respond" method="POST" style="display: inline;" id="acceptForm${policyId}">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="status" value="accepted">
        </form>
        <form action="/policies/${policyId}/respond" method="POST" style="display: inline;">
            <input type="hidden" name="_token" value="${csrfToken}">
            <input type="hidden" name="status" value="declined">
            <button type="submit" class="btn btn-danger">Decline</button>
        </form>
        <button type="button" class="btn btn-secondary" onclick="closePolicyModal()">Close</button>
    `;
    document.getElementById('modalFooter').innerHTML = footerContent;

    document.getElementById('policyModal').style.display = 'flex';

    // Load content after modal is shown
    setTimeout(() => {
        let content = `<div style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-size: 14px; color: #495057;">
            <strong>Note:</strong> If you can not view the policy below, you can use the external link to open it in your browser and come back to accept/respond.
            <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Open in Browser</a>
        </div>`;
        if (fileType.includes('pdf')) {
            content += `<embed src="${url}" width="100%" height="100%" type="application/pdf">`;
        } else if (fileType.includes('word') || fileType.includes('document')) {
            content += `<iframe src="https://docs.google.com/gview?url=${encodeURIComponent(url)}&embedded=true" width="100%" height="100%" style="border: none;"></iframe>`;
        } else {
            content += `<p>Preview not available for this file type.</p><a href="${url}" target="_blank">Open File</a>`;
        }
        document.getElementById('modalBody').innerHTML = content;
    }, 100);
}

function closePolicyModal() {
    document.getElementById('policyModal').style.display = 'none';
    document.getElementById('modalBody').innerHTML = ''; // Clear content
}

// Close modal when clicking outside
document.getElementById('policyModal').addEventListener('click', function(event) {
    if (event.target === this) {
        closePolicyModal();
    }
});

function acceptPolicy(policyId) {
    // Show the signing animation
    document.getElementById('signingOverlay').style.display = 'flex';

    // After animation completes, submit the form
    setTimeout(() => {
        document.getElementById(`acceptForm${policyId}`).submit();
    }, 3500); // 3.5 seconds for animation
}

</script>

@endsection
