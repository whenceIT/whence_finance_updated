@extends('layouts.master')
@section('title')
Policy Management | System
@endsection
@section('content')
<link rel="stylesheet" href="{{ asset('css/policies/dashboard.css') }}">

<div class="policy-dashboard-page">
<div class="policy-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1>
                <i class="fa fa-shield-alt"></i>
                Dashboard
            </h1>
            <p>Manage company policies, track acknowledgments, and monitor compliance</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('policies.add_policies') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i>
                Add Policy
            </a>
            <a href="{{ route('policies.view_policies') }}" class="btn btn-secondary">
                <i class="fa fa-book"></i>
                View All
            </a>
            <button class="btn btn-info" onclick="showPolicyOfTheDayModal()">
                <i class="fa fa-star"></i>
                Policy of the Day
            </button>
        </div>
    </div>

    <!-- Bento Grid -->
    <div class="bento-grid">
        <!-- Total Policies -->
        <div class="bento-card span-3 stat-primary">
            <div class="card-header">
                <span class="card-title">Total Policies</span>
                <div class="card-icon">
                    <i class="fa fa-file-text"></i>
                </div>
            </div>
            <div class="card-value">{{ $totalPolicies }}</div>
            <div class="card-label">Company policies</div>
            <div class="card-trend trend-up">
                <i class="fa fa-arrow-up"></i>
                {{ $activePolicies }} active
            </div>
        </div>

        <!-- Total Responses -->
        <div class="bento-card span-3 stat-success">
            <div class="card-header">
                <span class="card-title">Acknowledged</span>
                <div class="card-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
            <div class="card-value">{{ $acknowledgedCount }}</div>
            <div class="card-label">User acknowledgments</div>
            <div class="card-trend trend-up">
                <i class="fa fa-arrow-up"></i>
                {{ $totalResponses }} total responses
            </div>
        </div>

        <!-- Pending Responses -->
        <div class="bento-card span-3 stat-warning">
            <div class="card-header">
                <span class="card-title">Pending</span>
                <div class="card-icon">
                    <i class="fa fa-clock"></i>
                </div>
            </div>
            <div class="card-value">{{ $pendingCount }}</div>
            <div class="card-label">Awaiting response</div>
            <div class="card-trend trend-up">
                <i class="fa fa-exclamation-circle"></i>
                Needs attention
            </div>
        </div>

        <!-- Declined -->
        <div class="bento-card span-3 stat-danger" onclick="showDeclinedPoliciesModal()">
            <div class="card-header">
                <span class="card-title">Declined</span>
                <div class="card-icon">
                    <i class="fa fa-times-circle"></i>
                </div>
            </div>
            <div class="card-value">{{ $declinedCount }}</div>
            <div class="card-label">Policy rejections</div>
            <div class="card-trend trend-down">
                <i class="fa fa-arrow-down"></i>
                Requires follow-up
            </div>
        </div>

        <!-- Policy Violations -->
        <div class="bento-card span-3 stat-warning" onclick="showViolationsModal()">
            <div class="card-header">
                <span class="card-title">Violations</span>
                <div class="card-icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="card-value">{{ $violationsCount ?? 0 }}</div>
            <div class="card-label">Policy violations</div>
            <div class="card-trend trend-down">
                <i class="fa fa-flag"></i>
                Needs investigation
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bento-card span-3">
            <div class="card-header">
                <span class="card-title">Quick Actions</span>
                <div class="card-icon" style="background: #e0e7ff; color: #6366f1;">
                    <i class="fa fa-bolt"></i>
                </div>
            </div>
            <div class="quick-actions-grid">
                <a href="{{ route('policies.add_policies') }}" class="action-item">
                    <i class="fa fa-plus-circle"></i>
                    <span class="action-text">Add New Policy</span>
                </a>
                <a href="{{ route('policies.view_policies') }}" class="action-item">
                    <i class="fa fa-eye"></i>
                    <span class="action-text">View Policies</span>
                </a>
                <a href="{{ route('policies.user_responses') }}" class="action-item">
                    <i class="fa fa-users"></i>
                    <span class="action-text">User Responses</span>
                </a>
                <a href="#" class="action-item">
                    <i class="fa fa-chart-bar"></i>
                    <span class="action-text">Reports</span>
                </a>
            </div>
        </div>

        <!-- Response Status -->
        <div class="bento-card span-3">
            <div class="card-header">
                <span class="card-title">Response Status</span>
                <div class="card-icon" style="background: #d1fae5; color: #10b981;">
                    <i class="fa fa-chart-pie"></i>
                </div>
            </div>
            <div class="progress-section">
                @php
                    $total = $totalResponses > 0 ? $totalResponses : 1;
                    $ackPercent = round(($acknowledgedCount / $total) * 100);
                    $pendPercent = round(($pendingCount / $total) * 100);
                    $declPercent = round(($declinedCount / $total) * 100);
                @endphp
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Acknowledged/Accepted</span>
                        <span class="progress-value">{{ $ackPercent }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-success" style="width: {{ $ackPercent }}%"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Pending</span>
                        <span class="progress-value">{{ $pendPercent }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-warning" style="width: {{ $pendPercent }}%"></div>
                    </div>
                </div>
                <div class="progress-item">
                    <div class="progress-header">
                        <span class="progress-label">Declined</span>
                        <span class="progress-value">{{ $declPercent }}%</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill fill-danger" style="width: {{ $declPercent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Categories -->
        <div class="bento-card span-3">
            <div class="card-header">
                <span class="card-title">Policy Categories</span>
                <div class="card-icon" style="background: #ede9fe; color: #8b5cf6;">
                    <i class="fa fa-tags"></i>
                </div>
            </div>
            @if($categories->count() > 0)
            <div class="category-grid">
                @foreach($categories as $category)
                <div class="category-item">
                    <div class="category-icon">
                        @switch($category->name)
                            @case('HR')
                                <i class="fa fa-users"></i>
                                @break
                            @case('Finance')
                                <i class="fa fa-money"></i>
                                @break
                            @case('Operations')
                                <i class="fa fa-cogs"></i>
                                @break
                            @default
                                <i class="fa fa-folder"></i>
                        @endswitch
                    </div>
                    <div class="category-name">{{ $category->name }}</div>
                    <div class="category-count">{{ $category->policies_count ?? 0 }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fa fa-folder-open"></i>
                <p>No categories yet</p>
            </div>
            @endif
        </div>

        <!-- Recent Policies -->
        <div class="bento-card span-6">
            <div class="card-header">
                <span class="card-title">Recent Policies</span>
                <div class="card-icon" style="background: #dbeafe; color: #3b82f6;">
                    <i class="fa fa-file-alt"></i>
                </div>
            </div>
            @if($recentPolicies->count() > 0)
            <div class="policy-list">
                @foreach($recentPolicies as $policy)
                <a href="#" class="policy-item">
                    <div class="policy-info">
                        <div class="policy-icon" style="background: #e0e7ff; color: #6366f1;">
                            <i class="fa fa-file"></i>
                        </div>
                        <div>
                            <div class="policy-title">{{ $policy->title }}</div>
                            <div class="policy-category">{{ $policy->category->name ?? 'Uncategorized' }}</div>
                        </div>
                    </div>
                    <div class="policy-status">
                        <span class="status-dot {{ $policy->is_active ? 'status-active' : 'status-inactive' }}"></span>
                        {{ $policy->is_active ? 'Active' : 'Inactive' }}
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="empty-state">
                <i class="fa fa-file-plus"></i>
                <p>No policies yet. Create your first policy!</p>
            </div>
            @endif
        </div>

         <!-- Recent Activity -->
         <div class="bento-card span-6">
             <div class="card-header">
                 <span class="card-title">Recent Activity</span>
                 <div class="card-icon" style="background: #fef3c7; color: #f59e0b;">
                     <i class="fa fa-history"></i>
                 </div>
             </div>
             @php
                 $groupedResponses = collect($recentResponses)->groupBy(function($response) {
                     return $response->user->id ?? 'unknown';
                 });
             @endphp
             @if($groupedResponses->count() > 0)
             <div class="activity-list">
                 @foreach($groupedResponses as $userId => $responses)
                 @php
                     $user = $responses->first()->user;
                 @endphp
                 <div class="user-activity-group">
                     <div class="activity-item user-header">
                         <div class="activity-avatar">
                             {{ substr($user->first_name ?? 'U', 0, 1) }}{{ substr($user->last_name ?? '', 0, 1) }}
                         </div>
                         <div class="activity-content">
                             <div class="activity-title">{{ $user->first_name ?? 'Unknown' }} {{ $user->last_name ?? '' }}</div>
                             <div class="activity-meta">{{ $responses->count() }} recent activities</div>
                         </div>
                     </div>
                     @foreach($responses as $response)
                     <div class="activity-item">
                        <div class="activity-content">
                            <div class="activity-title">{{ $response->policy->title ?? 'Unknown Policy' }}</div>
                            {{ ucfirst($response->status) }}
                            <div class="activity-meta">{{ $response->created_at->diffForHumans() }}</div>
                        </div>
                     </div>
                     @endforeach
                 </div>
                 @endforeach
             </div>
             @else
             <div class="empty-state">
                 <i class="fa fa-inbox"></i>
                 <p>No recent activity</p>
             </div>
             @endif
         </div>
    </div>
</div>

<!-- Declined Policies Modal -->
<div class="bottom-sheet-overlay" id="declinedPoliciesOverlay">
    <div class="bottom-sheet" id="declinedPoliciesSheet">
        <button class="bottom-sheet-close" id="closeDeclinedPoliciesSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title">Declined Policies</h3>
            <div class="declined-policies-list">
                @php
                    $grouped = collect($declinedPolicies)->groupBy(function($response) {
                        return $response->user->id ?? 'unknown';
                    });
                @endphp
                @forelse($grouped as $userId => $responses)
                <details class="user-declined-group">
                    <summary class="user-declined-summary">
                        <strong>{{ $responses->first()->user->first_name ?? 'Unknown' }} {{ $responses->first()->user->last_name ?? '' }}</strong>
                        <span class="declined-count">({{ $responses->count() }} policies)</span>
                    </summary>
                    <div class="user-declined-policies">
                        @foreach($responses as $response)
                        <div class="declined-policy-item">
                            <div class="policy-info">
                                <strong>{{ $response->policy->title ?? 'Unknown Policy' }}</strong>
                                <br>
                                <small>
                                    Date: {{ $response->created_at->format('M d, Y H:i') }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </details>
                @empty
                <p>No declined policies found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Policy Violations Modal -->
<div class="bottom-sheet-overlay" id="violationsOverlay">
    <div class="bottom-sheet" id="violationsSheet">
        <button class="bottom-sheet-close" id="closeViolationsSheet">&times;</button>
        <div class="bottom-sheet-handle"></div>
        <div class="bottom-sheet-content">
            <h3 class="bottom-sheet-title">Policy Violation Reports</h3>
            <div class="violations-container">
                <!-- Filters -->
                <div class="violations-filters">
                    <div class="filter-row">
                        <select id="violationStatus" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="pending">Pending</option>
                            <option value="investigating">Investigating</option>
                            <option value="resolved">Resolved</option>
                            <option value="escalated">Escalated</option>
                        </select>
                        <select id="violationBranch" class="form-control">
                            <option value="">All Branches</option>
                        </select>
                        <select id="violationPolicyType" class="form-control">
                            <option value="">All Policy Types</option>
                        </select>
                        <input type="date" id="violationDateFrom" class="form-control">
                        <input type="date" id="violationDateTo" class="form-control">
                        <button class="btn btn-primary" onclick="filterViolations()">Filter</button>
                        <button class="btn btn-secondary" onclick="clearFilters()">Clear</button>
                    </div>
                </div>

                <!-- Violations List -->
                <div class="violations-list" id="violationsList">
                    <div class="empty-state" id="noViolations">
                        <i class="fa fa-clipboard-list"></i>
                        <p>No violations found matching the filters.</p>
                    </div>
                </div>

                <!-- Add New Violation -->
                <div class="add-violation-section">
                    <button class="btn btn-primary" onclick="showAddViolationForm()">Report New Violation</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Violation Modal -->
<div class="modal fade" id="addViolationModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Report Policy Violation</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addViolationForm">
                    <div class="form-group">
                        <label for="violationUser">User</label>
                        <select id="violationUser" class="form-control" required>
                            <option value="">Select User</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="violationPolicy">Policy Violated</label>
                        <select id="violationPolicy" class="form-control" required>
                            <option value="">Select Policy</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="violationDescription">Description</label>
                        <textarea id="violationDescription" class="form-control" rows="3" placeholder="Describe the violation..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="violationEvidence">Attach Evidence</label>
                        <input type="file" id="violationEvidence" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                        <small class="form-text text-muted">Supported formats: Images, PDF, Word documents</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitViolation()">Report Violation</button>
            </div>
        </div>
    </div>
</div>

<!-- Policy of the Day Modal -->
<div class="modal fade" id="policyOfTheDayModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Create Policy of the Day</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="policyOfTheDayForm">
                    <div class="form-group">
                        <label for="potdTitle">Title</label>
                        <input type="text" id="potdTitle" class="form-control" placeholder="Enter policy title" required>
                    </div>
                    <div class="form-group">
                        <label for="potdContent">Short Content (Digestible format)</label>
                        <textarea id="potdContent" class="form-control" rows="4" placeholder="Enter short, digestible content for dashboard display" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="potdFullContent">Full Content (Optional)</label>
                        <textarea id="potdFullContent" class="form-control" rows="6" placeholder="Enter full content if different from short content"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="potdPolicy">Link to Existing Policy (Optional)</label>
                        <select id="potdPolicy" class="form-control">
                            <option value="">Select Policy (Optional)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="potdScheduledDate">Scheduled Date (Optional)</label>
                        <input type="date" id="potdScheduledDate" class="form-control">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" id="potdIsRandom" class="form-check-input">
                        <label for="potdIsRandom" class="form-check-label">Allow random selection</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPolicyOfTheDay()">Create Policy of the Day</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeclinedPoliciesModal() {
        $('#declinedPoliciesOverlay').addClass('active');
        $('#declinedPoliciesSheet').addClass('active');
    }

    // Close declined policies modal when clicking close button
    $('#closeDeclinedPoliciesSheet').on('click', function () {
        $('#declinedPoliciesOverlay').removeClass('active');
        $('#declinedPoliciesSheet').removeClass('active');
    });

    // Close declined policies modal when clicking overlay
    $('#declinedPoliciesOverlay').on('click', function (e) {
        if (e.target === this) {
            $('#declinedPoliciesOverlay').removeClass('active');
            $('#declinedPoliciesSheet').removeClass('active');
        }
    });

    function showViolationsModal() {
        $('#violationsOverlay').addClass('active');
        $('#violationsSheet').addClass('active');
        loadViolations();
        loadFilterOptions();
    }

    // Close violations modal when clicking close button
    $('#closeViolationsSheet').on('click', function () {
        $('#violationsOverlay').removeClass('active');
        $('#violationsSheet').removeClass('active');
    });

    // Close violations modal when clicking overlay
    $('#violationsOverlay').on('click', function (e) {
        if (e.target === this) {
            $('#violationsOverlay').removeClass('active');
            $('#violationsSheet').removeClass('active');
        }
    });

    function loadViolations() {
        // AJAX call to load violations
        $.get('{{ route("policies.violations.list") }}')
            .done(function(data) {
                renderViolations(data);
            })
            .fail(function() {
                $('#violationsList').html('<p>Error loading violations.</p>');
            });
    }

    function renderViolations(violations) {
        if (violations.length === 0) {
            $('#violationsList').html('<div class="empty-state"><i class="fa fa-clipboard-list"></i><p>No violations found matching the filters.</p></div>');
            return;
        }

        let html = '';
        violations.forEach(function(violation) {
            html += `
                <div class="violation-item" data-id="${violation.id}">
                    <div class="violation-header">
                        <strong>${violation.policy_title}</strong>
                        <span class="violation-status status-${violation.status}">${violation.status.charAt(0).toUpperCase() + violation.status.slice(1)}</span>
                    </div>
                    <div class="violation-details">
                        <p><strong>User:</strong> ${violation.user_name}</p>
                        <p><strong>Branch:</strong> ${violation.branch_name || 'N/A'}</p>
                        <p><strong>Date:</strong> ${violation.created_at}</p>
                        <p><strong>Description:</strong> ${violation.description}</p>
                        ${violation.evidence_count > 0 ? `<p><strong>Evidence:</strong> ${violation.evidence_count} files attached</p>` : ''}
                    </div>
                    <div class="violation-actions">
                        ${violation.status === 'pending' ? `<button class="btn btn-sm btn-warning" onclick="changeStatus(${violation.id}, 'investigating')">Investigate</button>` : ''}
                        ${violation.status === 'investigating' ? `<button class="btn btn-sm btn-success" onclick="changeStatus(${violation.id}, 'resolved')">Resolve</button>` : ''}
                        ${violation.status === 'pending' || violation.status === 'investigating' ? `<button class="btn btn-sm btn-danger" onclick="changeStatus(${violation.id}, 'escalated')">Escalate</button>` : ''}
                        <button class="btn btn-sm btn-info" onclick="attachEvidence(${violation.id})">Attach Evidence</button>
                        <button class="btn btn-sm btn-secondary" onclick="viewDetails(${violation.id})">View Details</button>
                    </div>
                </div>
            `;
        });
        $('#violationsList').html(html);
    }

    function loadFilterOptions() {
        // Load branches
        $.get('{{ route("policies.violations.branches") }}')
            .done(function(branches) {
                let options = '<option value="">All Branches</option>';
                branches.forEach(function(branch) {
                    options += `<option value="${branch.id}">${branch.name}</option>`;
                });
                $('#violationBranch').html(options);
            });

        // Load policy categories
        $.get('{{ route("policies.violations.categories") }}')
            .done(function(categories) {
                let options = '<option value="">All Policy Types</option>';
                categories.forEach(function(category) {
                    options += `<option value="${category.id}">${category.name}</option>`;
                });
                $('#violationPolicyType').html(options);
            });
    }

    function filterViolations() {
        const filters = {
            status: $('#violationStatus').val(),
            branch_id: $('#violationBranch').val(),
            category_id: $('#violationPolicyType').val(),
            date_from: $('#violationDateFrom').val(),
            date_to: $('#violationDateTo').val()
        };

        $.get('{{ route("policies.violations.list") }}', filters)
            .done(function(data) {
                renderViolations(data);
            });
    }

    function clearFilters() {
        $('#violationStatus, #violationBranch, #violationPolicyType, #violationDateFrom, #violationDateTo').val('');
        loadViolations();
    }

    function changeStatus(violationId, newStatus) {
        $.post('{{ route("policies.violations.updateStatus") }}', {
            violation_id: violationId,
            status: newStatus,
            _token: '{{ csrf_token() }}'
        })
        .done(function() {
            loadViolations();
        })
        .fail(function() {
            alert('Error updating status');
        });
    }

    function attachEvidence(violationId) {
        const input = document.createElement('input');
        input.type = 'file';
        input.multiple = true;
        input.accept = 'image/*,.pdf,.doc,.docx';
        input.onchange = function(e) {
            const files = e.target.files;
            const formData = new FormData();
            formData.append('violation_id', violationId);
            formData.append('_token', '{{ csrf_token() }}');
            for (let i = 0; i < files.length; i++) {
                formData.append('evidence[]', files[i]);
            }

            $.ajax({
                url: '{{ route("policies.violations.attachEvidence") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    loadViolations();
                },
                error: function() {
                    alert('Error attaching evidence');
                }
            });
        };
        input.click();
    }

    function viewDetails(violationId) {
        // Open detailed view - using the correct path from route definition
        window.open('/policies/violations/' + violationId, '_blank');
    }

    function showAddViolationForm() {
        // Load users and policies for the form
        $.get('{{ route("policies.violations.users") }}')
            .done(function(users) {
                let options = '<option value="">Select User</option>';
                users.forEach(function(user) {
                    options += `<option value="${user.id}">${user.first_name} ${user.last_name}</option>`;
                });
                $('#violationUser').html(options);
            });

        $.get('{{ route("policies.violations.policies") }}')
            .done(function(policies) {
                let options = '<option value="">Select Policy</option>';
                policies.forEach(function(policy) {
                    options += `<option value="${policy.id}">${policy.title}</option>`;
                });
                $('#violationPolicy').html(options);
            });

        $('#addViolationModal').modal('show');
    }

    function submitViolation() {
        const formData = new FormData();
        formData.append('user_id', $('#violationUser').val());
        formData.append('policy_id', $('#violationPolicy').val());
        formData.append('description', $('#violationDescription').val());
        formData.append('_token', '{{ csrf_token() }}');

        const files = $('#violationEvidence')[0].files;
        for (let i = 0; i < files.length; i++) {
            formData.append('evidence[]', files[i]);
        }

        $.ajax({
            url: '{{ route("policies.violations.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                $('#addViolationModal').modal('hide');
                $('#addViolationForm')[0].reset();
                loadViolations();
            },
            error: function() {
                alert('Error reporting violation');
            }
        });
    }

    function showPolicyOfTheDayModal() {
        // Simply open the modal to store Policy of the Day
        $('#policyOfTheDayModal').modal('show');

        // Optionally load policies for the dropdown (non-blocking)
        $.get('{{ route("policies.violations.policies") }}')
            .done(function(policies) {
                let options = '<option value="">Select Policy (Optional)</option>';
                policies.forEach(function(policy) {
                    options += `<option value="${policy.id}">${policy.title}</option>`;
                });
                $('#potdPolicy').html(options);
            })
            .fail(function() {
                // If loading fails, just leave empty
                $('#potdPolicy').html('<option value="">Select Policy (Optional)</option>');
            });
    }

    function submitPolicyOfTheDay() {
        const submitBtn = $('button[onclick="submitPolicyOfTheDay()"]');
        const originalText = submitBtn.text();

        // Show loading state
        submitBtn.prop('disabled', true).addClass('btn-loading').text('Creating...');

        const formData = {
            title: $('#potdTitle').val(),
            content: $('#potdContent').val(),
            full_content: $('#potdFullContent').val(),
            policy_id: $('#potdPolicy').val(),
            scheduled_date: $('#potdScheduledDate').val(),
            is_random: $('#potdIsRandom').is(':checked') ? 1 : 0,
            _token: '{{ csrf_token() }}'
        };

        $.post('{{ route("policies.policy-of-the-day.store") }}', formData)
            .done(function() {
                $('#policyOfTheDayModal').modal('hide');
                $('#policyOfTheDayForm')[0].reset();
                alert('Policy of the Day created successfully!');
            })
            .fail(function() {
                alert('Error creating Policy of the Day');
            })
            .always(function() {
                // Restore button state
                submitBtn.prop('disabled', false).removeClass('btn-loading').text(originalText);
            });
    }
</script>
@endsection
