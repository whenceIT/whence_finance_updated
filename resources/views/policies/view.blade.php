@extends('layouts.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <!-- Header Section -->
            <div class="text-center mb-5">
                <div class="d-inline-block bg-primary p-3 rounded-circle mb-3">
                    <i class="fas fa-file-contract text-white fa-2x"></i>
                </div>
                <h1 class="text-dark font-weight-bold mb-2">Company Policies & Documents</h1>
                <p class="text-secondary">Manage and review company policies with proper categorization and access controls</p>
            </div>

            <!-- Main Action Bar -->
            <div class="card border-0 shadow-lg rounded-lg mb-4">
                <div class="card-body bg-light p-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="flex items-center gap-2">
                            <div class="bg-white p-2 rounded shadow-sm">
                                <i class="fas fa-search text-primary"></i>
                            </div>
                            <span class="font-weight-bold text-dark">Quick Actions</span>
                        </div>
                        <div class="d-flex gap-3 flex-wrap">
                            <button type="button" class="btn btn-success btn-lg px-4 py-2 rounded-lg" onclick="acceptAllPolicies()">
                                <i class="fas fa-check-circle mr-2"></i> Accept All
                            </button>
                            <button type="button" class="btn btn-danger btn-lg px-4 py-2 rounded-lg" onclick="declineAllPolicies()">
                                <i class="fas fa-times-circle mr-2"></i> Decline All
                            </button>
                            @if($isAdmin)
                                <a href="{{ route('policies.add_policies') }}" class="btn btn-primary btn-lg px-4 py-2 rounded-lg">
                                    <i class="fas fa-plus-circle mr-2"></i> Add New Document
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Alert -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4 border-0 rounded-lg shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success mr-3"></i>
                        <div class="text-success">{{ session('success') }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="card border-0 shadow-lg rounded-lg mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-end">
                        <div class="col-md-6 mb-3">
                            <div class="bg-white p-4 rounded shadow-sm border">
                                <label for="category_filter" class="form-label font-weight-bold text-dark mb-2">
                                    <i class="fas fa-filter text-primary mr-2"></i>
                                    Filter by Category:
                                </label>
                                <select name="category_filter" id="category_filter" class="form-control form-control-lg rounded border-primary" onchange="filterByCategory(this.value)">
                                    <option value="">All Categories</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="bg-primary text-white p-4 rounded shadow-lg">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="text-white-50 text-sm">Total Documents</p>
                                        <h3 class="font-weight-bold text-white">{{ $policies->count() }} <span class="text-white-50 text-sm">Documents</span></h3>
                                    </div>
                                    <div class="bg-white bg-opacity-20 p-3 rounded">
                                        <i class="fas fa-file-contract text-white"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Policies Table -->
            <div class="card border-0 shadow-lg rounded-lg overflow-hidden">
                <div class="card-body p-0">
                    @if($policies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th scope="col" class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-alt text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">Document Title</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-folder text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">Category</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-shield-alt text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">Access Level</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-file-code text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">File Type</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="py-3">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-database text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">File Size</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-center py-3">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-clipboard-check text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">Your Response</span>
                                            </div>
                                        </th>
                                        <th scope="col" class="text-center py-3">
                                            <div class="d-flex align-items-center justify-content-center">
                                                <i class="fas fa-cog text-primary mr-2"></i>
                                                <span class="font-weight-bold text-dark">Actions</span>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white">
                                    @foreach($policies as $policy)
                                        <tr data-policy-id="{{ $policy->id }}" class="border-bottom">
                                            <td class="ps-4 py-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-primary p-2 rounded mr-3">
                                                        <i class="fas fa-file-pdf text-white"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="font-weight-bold text-dark mb-1">{{ $policy->title }}</h6>
                                                        @if($policy->description)
                                                            <p class="text-secondary mb-0">{{ \Illuminate\Support\Str::limit($policy->description, 80) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3">
                                                @if($policy->category)
                                                    <span class="badge bg-primary">{{ $policy->category->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary">Policies</span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                @if($policy->access_level == 'managerial')
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-user-tie mr-1"></i> Managerial
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-users mr-1"></i> All Staff
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-3">
                                                <span class="badge bg-info">{{ strtoupper(pathinfo($policy->file_name, PATHINFO_EXTENSION)) }}</span>
                                            </td>
                                            <td class="py-3">
                                                <span class="text-secondary">{{ round($policy->file_size / 1024, 2) }} KB</span>
                                            </td>
                                            <td class="text-center py-3">
                                                @php
                                                    $response = $policy->userPolicyResponses->first();
                                                @endphp
                                                @if($response)
                                                    @if($response->status == 'accepted')
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-check-circle mr-1"></i> Accepted
                                                        </span>
                                                    @elseif($response->status == 'declined')
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle mr-1"></i> Declined
                                                        </span>
                                                    @elseif($response->status == 'pending')
                                                        <span class="badge bg-warning">
                                                            <i class="fas fa-clock mr-1"></i> Pending
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-clock mr-1"></i> Pending
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center py-3">
                                                <div class="d-flex justify-content-center gap-2">
                                                    <a href="{{ $policy->file_url }}" download class="btn btn-success btn-sm px-3 py-1 rounded">
                                                        <i class="fas fa-download mr-1"></i> Download
                                                    </a>
                                                    <button type="button" class="btn btn-primary btn-sm px-3 py-1 rounded" 
                                                            title="View Preview" 
                                                            onclick="openPolicyModal({{ $policy->id }}, '{{ addslashes($policy->title) }}', '{{ $policy->file_url }}', '{{ $policy->file_type }}')">
                                                        <i class="fas fa-eye mr-1"></i> View
                                                    </button>
                                                    @php
                                                        $user = Sentinel::getUser();
                                                        $canDelete = false;
                                                        $userRole = $user->roles->first();
                                                        
                                                        if ($userRole && $userRole->id == 1) {
                                                            $canDelete = true;
                                                        } elseif ($policy->created_by == $user->id) {
                                                            $canDelete = true;
                                                        }
                                                    @endphp
                                                    @if($canDelete)
                                                        <button type="button" class="btn btn-danger btn-sm px-3 py-1 rounded" 
                                                                title="Delete Policy" 
                                                                onclick="deletePolicy({{ $policy->id }})">
                                                            <i class="fas fa-trash mr-1"></i> Delete
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info border-0 rounded-0 m-0 py-5 text-center" role="alert">
                            <div class="d-flex flex-column align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded-circle mb-3">
                                    <i class="fas fa-info-circle text-primary"></i>
                                </div>
                                <span class="text-secondary">No documents found for the selected category.</span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Policy Preview Modal -->
<div id="policyModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center;">
    <div class="modal-content-custom" style="background: white; width: 95%; max-width: 1200px; height: 90%; border-radius: 16px; box-shadow: 0 25px 50px rgba(0,0,0,0.2); overflow: hidden; display: flex; flex-direction: column; animation: modalSlideIn 0.3s ease-out;">
        <div class="modal-header-custom" style="padding: 15px 20px; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; background: #f8f9fa;">
            <h5 id="modalTitle" style="margin: 0; font-size: 18px; font-weight: 600; color: #343a40;"></h5>
            <button type="button" onclick="closePolicyModal()" style="background: none; border: none; font-size: 24px; cursor: pointer; color: #6c757d; line-height: 1;">
                &times;
            </button>
        </div>
        <div class="modal-body-custom" id="modalBody" style="flex: 1; padding: 0; overflow: hidden;">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer-custom" id="modalFooter" style="padding: 15px 20px; border-top: 1px solid #dee2e6; display: flex; justify-content: flex-end; gap: 10px; background: #f8f9fa;">
            <!-- Buttons will be loaded here -->
        </div>
    </div>
</div>

<!-- Custom Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 rounded-lg shadow-lg">
            <div class="modal-header bg-light border-0 rounded-t-lg">
                <h4 class="modal-title font-weight-bold text-dark" id="confirmationModalLabel">Confirm Action</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="confirmationModalBody">
                Are you sure?
            </div>
            <div class="modal-footer border-0 rounded-b-lg">
                <button type="button" class="btn btn-secondary px-4 py-2 rounded" data-dismiss="modal">No</button>
                <button type="button" class="btn btn-primary px-4 py-2 rounded" id="confirmYes">Yes</button>
            </div>
        </div>
    </div>
</div>

@include('policies.signature_animation')

<script>
    let confirmCallback = null;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    `;
    document.head.appendChild(style);

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

    function filterByCategory(categoryId) {
        if (categoryId) {
            window.location.href = '?category=' + categoryId;
        } else {
            window.location.href = window.location.pathname;
        }
    }

    function acceptAllPolicies() {
        const rows = document.querySelectorAll('tr[data-policy-id]');
        const nonAcceptedPolicies = [];

        rows.forEach(row => {
            const responseCell = row.querySelector('td:nth-child(6)');
            if (responseCell && !responseCell.textContent.includes('Accepted')) {
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
            button.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Processing...';

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
        const rows = document.querySelectorAll('tr[data-policy-id]');
        const nonDeclinedPolicies = [];

        rows.forEach(row => {
            const responseCell = row.querySelector('td:nth-child(6)');
            if (responseCell && !responseCell.textContent.includes('Declined')) {
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
            button.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i> Processing...';

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

    function openPolicyModal(policyId, title, url, fileType) {
        document.getElementById('modalTitle').innerHTML = title;

        // Show loading spinner
        document.getElementById('modalBody').innerHTML = `
            <div style="display: flex; justify-content: center; align-items: center; height: 100%; font-size: 18px; color: #6c757d;">
                <i class="fa fa-spinner fa-spin" style="margin-right: 10px;"></i> Loading document...
            </div>
        `;

        const footerContent = `
            <button type="button" class="btn btn-success px-4 py-2 rounded" onclick="acceptPolicy(${policyId})">
                <i class="fas fa-check mr-1"></i> Accept
            </button>
            <form action="/policies/${policyId}/respond" method="POST" style="display: inline;" id="acceptForm${policyId}">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="status" value="accepted">
            </form>
            <form action="/policies/${policyId}/respond" method="POST" style="display: inline;">
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="status" value="declined">
                <button type="submit" class="btn btn-danger px-4 py-2 rounded">
                    <i class="fas fa-times mr-1"></i> Decline
                </button>
            </form>
            <button type="button" class="btn btn-secondary px-4 py-2 rounded" onclick="closePolicyModal()">
                <i class="fas fa-times mr-1"></i> Close
            </button>
        `;
        document.getElementById('modalFooter').innerHTML = footerContent;

        document.getElementById('policyModal').style.display = 'flex';

        // Load content after modal is shown
        setTimeout(() => {
            let content = `<div style="padding: 10px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; font-size: 14px; color: #495057;">
                <strong>Note:</strong> If you can not view the policy below, you can use the external link to open it in your browser and come back to accept/respond.
                <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary ml-2">Open in Browser</a>
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

    function deletePolicy(policyId) {
        if (confirm('Are you sure you want to delete this policy? This action cannot be undone.')) {
            window.location.href = `/policies/${policyId}/delete`;
        }
    }
</script>
@endsection
