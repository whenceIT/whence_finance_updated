@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Custom Styles */
    .policy-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1.5rem;
    }

    .header-section {
        text-align: center;
        margin-bottom: 3rem;
        padding: 2rem 0;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    }

    .header-icon i {
        font-size: 2.5rem;
        color: white;
    }

    .header-section h1 {
        font-size: 2.5rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 0.5rem;
    }

    .header-section p {
        font-size: 1.125rem;
        color: #718096;
        max-width: 600px;
        margin: 0 auto;
    }

    .action-bar {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .action-bar .quick-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .action-bar .quick-actions .icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .action-bar .quick-actions span {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }

    .btn-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(17, 153, 142, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(235, 51, 73, 0.3);
    }

    .filter-section {
        background: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .filter-label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-control-lg {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .stats-card .stats-label {
        font-size: 0.875rem;
        opacity: 0.9;
        margin-bottom: 0.5rem;
    }

    .stats-card .stats-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .stats-card .stats-unit {
        font-size: 0.875rem;
        opacity: 0.9;
    }

    .stats-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stats-icon i {
        font-size: 1.25rem;
    }

    .policies-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .table thead {
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    }

    .table thead th {
        padding: 1rem 1.5rem;
        font-weight: 600;
        color: #2d3748;
        border-bottom: 2px solid #e2e8f0;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f7fafc;
    }

    .table tbody tr:hover {
        background: #f7fafc;
    }

    .table tbody td {
        padding: 1.25rem 1.5rem;
        vertical-align: middle;
    }

    .document-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        margin-right: 1rem;
    }

    .document-info h6 {
        font-size: 1.575rem;
        font-weight: 900;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }

    .document-info p {
        font-size: 0.975rem;
        color: #718096;
        margin: 0;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge.bg-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .badge.bg-danger {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
    }

    .badge.bg-secondary {
        background: #e2e8f0;
        color: #4a5568;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        transition: all 0.2s ease;
    }

    .btn-sm:hover {
        transform: translateY(-1px);
    }

    .alert {
        border-radius: 8px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    }

    .alert-success {
        background: #f0fff4;
        border-left: 4px solid #38ef7d;
    }

    .alert-info {
        background: #ebf8ff;
        border-left: 4px solid #667eea;
    }

    .modal-content-custom {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: modalSlideIn 0.3s ease-out;
    }

    .modal-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-header-custom h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }

    .modal-header-custom button {
        background: none;
        border: none;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        opacity: 0.8;
        transition: opacity 0.2s ease;
    }

    .modal-header-custom button:hover {
        opacity: 1;
    }

    .modal-body-custom {
        padding: 0;
        flex: 1;
        overflow: hidden;
    }

    .modal-footer-custom {
        background: #f7fafc;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
    }

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

    @media (max-width: 768px) {
        .header-section h1 {
            font-size: 1.75rem;
        }

        .header-section p {
            font-size: 1rem;
        }

        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .action-bar .quick-actions {
            margin-bottom: 1rem;
        }

        .filter-section {
            padding: 1.5rem;
        }

        .table-responsive {
            font-size: 0.875rem;
        }

        .table tbody td {
            padding: 0.75rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
    }
</style>

<div class="policy-container">
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-icon">
            <i class="fas fa-file-contract"></i>
        </div>
        <h1>Company Policies & Documents</h1>
        <p>Manage and review company policies with proper categorization and access controls</p>
    </div>

    <!-- Main Action Bar -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex gap-3 flex-wrap">
                <button type="button" class="btn btn-success" onclick="acceptAllPolicies()">
                    <i class="fas fa-check-circle mr-2"></i> Accept All
                </button>
                <button type="button" class="btn btn-danger" onclick="declineAllPolicies()">
                    <i class="fas fa-times-circle mr-2"></i> Decline All
                </button>
                @if($isAdmin)
                    <a href="{{ route('policies.add_policies') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle mr-2"></i> Add New Document
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle text-success mr-3"></i>
                <div class="text-success">{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-6 mb-3">
                <label for="category_filter" class="filter-label">
                    <i class="fas fa-filter text-primary mr-2"></i>
                    Filter by Category:
                </label>
                <div style="position: relative;">
                    <select name="category_filter" id="category_filter" class="form-control form-control-lg" style="font-size: 1.123rem;" onchange="filterByCategory(this.value)">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ $selectedCategory == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    <div id="filterLoader" style="display: none; position: absolute; top: 50%; right: 1rem; transform: translateY(-50%);">
                        <i class="fas fa-spinner fa-spin text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Policies Table -->
    <div class="policies-table">
        @if($policies->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col" style="text-align: center;">
                                <div style="display: inline-flex; align-items: center;">
                                    <i class="fas fa-file-alt text-primary" style="margin-right: 6px;"></i>
                                    <span style="font-weight: bold; color: #212529;">Document Title</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-folder text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Category</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-shield-alt text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Access Level</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-file-code text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">File Type</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-database text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">File Size</span>
                                </div>
                            </th>
                            <th scope="col" class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-clipboard-check text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Your Response</span>
                                </div>
                            </th>
                            <th scope="col" class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-cog text-primary mr-2"></i>
                                    <span class="font-weight-bold text-dark">Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @foreach($policies as $policy)
                            <tr data-policy-id="{{ $policy->id }}">
                                <td>
                                    <div style="display: inline-flex; align-items: center;" class="d-flex align-items-center">
                                        <div class="document-icon">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="document-info">
                                            <h6>{{ $policy->title }}</h6>
                                            @if($policy->description)
                                                <p>{{ \Illuminate\Support\Str::limit($policy->description, 80) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($policy->category)
                                        <span class="badge bg-primary">{{ $policy->category->name }}</span>
                                    @else
                                        <span class="badge bg-secondary">Policies</span>
                                    @endif
                                </td>
                                <td>
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
                                <td>
                                    <span class="badge bg-primary">{{ strtoupper(pathinfo($policy->file_name, PATHINFO_EXTENSION)) }}</span>
                                </td>
                                <td>
                                    <span class="text-secondary">{{ round($policy->file_size / 1024, 2) }} KB</span>
                                </td>
                                <td class="text-center">
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
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ $policy->file_url }}" download class="btn btn-success btn-sm">
                                            <i class="fas fa-download mr-1"></i> Download
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" 
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
                                            <button type="button" class="btn btn-danger btn-sm" 
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
            <div class="alert alert-info mb-0 text-center py-5" role="alert">
                <div class="d-flex flex-column align-items-center">
                    <div class="header-icon" style="width: 60px; height: 60px;">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <span class="text-secondary">No documents found for the selected category.</span>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Custom Policy Preview Modal -->
<div id="policyModal" class="custom-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1050; align-items: center; justify-content: center;">
    <div class="modal-content-custom" style="width: 95%; max-width: 1200px; height: 90%; display: flex; flex-direction: column;">
        <div class="modal-header-custom">
            <h5 id="modalTitle"></h5>
            <button type="button" onclick="closePolicyModal()">
                &times;
            </button>
        </div>
        <div class="modal-body-custom" id="modalBody">
            <!-- Content will be loaded here -->
        </div>
        <div class="modal-footer-custom" id="modalFooter">
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
        // Show preloader
        const filterLoader = document.getElementById('filterLoader');
        const categorySelect = document.getElementById('category_filter');
        filterLoader.style.display = 'block';
        categorySelect.disabled = true;
        
        // Redirect with delay for better user experience
        setTimeout(() => {
            if (categoryId) {
                window.location.href = '?category=' + categoryId;
            } else {
                window.location.href = window.location.pathname;
            }
        }, 300); // 300ms delay for preloader to be visible
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
