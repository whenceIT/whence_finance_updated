@extends('layouts.master')

@section('title', 'Administrative Records Management')

@section('content')
<section class="content-header">
    <h1>
        <small>Approve, review, and manage employee administrative records</small>
    </h1>
</section>
<!-- put bento grid dashboard stats -->
<section class="content">
    <div class="row">
        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>{{ $recordTypeStats['disciplinary']['total'] ?? 0 }}</h3>
                    <p>Disciplinary Records</p>
                    <!-- <p class="small">
                        Pending: {{ $recordTypeStats['disciplinary']['pending'] ?? 0 }}<br>
                        Active: {{ $recordTypeStats['disciplinary']['active'] ?? 0 }}<br>
                        Declined: {{ $recordTypeStats['disciplinary']['declined'] ?? 0 }}
                    </p> -->
                </div>
                <div class="icon">
                    <i class="fa fa-gavel"></i>
                </div>
                <a href="{{ url('hr/administrative-records?tab=pending') }}" class="small-box-footer">
                    View approvals <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $recordTypeStats['health']['total'] ?? 0 }}</h3>
                    <p>Health Records</p>
                    <!-- <p class="small">
                        Pending: {{ $recordTypeStats['health']['pending'] ?? 0 }}<br>
                        Active: {{ $recordTypeStats['health']['active'] ?? 0 }}<br>
                        Declined: {{ $recordTypeStats['health']['declined'] ?? 0 }}
                    </p> -->
                </div>
                <div class="icon">
                    <i class="fa fa-heartbeat"></i>
                </div>
                <a href="{{ url('hr/administrative-records?tab=pending') }}" class="small-box-footer">
                    View approvals <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $recordTypeStats['career']['total'] ?? 0 }}</h3>
                    <p>Career Progression</p>
                    <!-- <p class="small">
                        Pending: {{ $recordTypeStats['career']['pending'] ?? 0 }}<br>
                        Active: {{ $recordTypeStats['career']['active'] ?? 0 }}<br>
                        Declined: {{ $recordTypeStats['career']['declined'] ?? 0 }}
                    </p> -->
                </div>
                <div class="icon">
                    <i class="fa fa-briefcase"></i>
                </div>
                <a href="{{ url('hr/administrative-records?tab=pending') }}" class="small-box-footer">
                    View approvals <i class="fa fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="box box-primary">
        <div class="box-header with-border">
            <ul class="nav nav-tabs">
                <li class="{{ request('tab', 'pending') === 'pending' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=pending') }}" class="admin-records-tab" data-status="pending">
                        <i class="fa fa-clock-o"></i> Approvals
                        <span class="badge badge-warning tab-count" data-status="pending">{{ $statusCounts['pending'] ?? 0 }}</span>
                    </a>
                </li>
                <li class="{{ $tab === 'active' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=active') }}" class="admin-records-tab" data-status="active">
                        <i class="fa fa-check"></i> Active
                        <span class="badge badge-success tab-count" data-status="active">{{ $statusCounts['active'] ?? 0 }}</span>
                    </a>
                </li>
                <li class="{{ $tab === 'declined' ? 'active' : '' }}">
                    <a href="{{ url('hr/administrative-records?tab=declined') }}" class="admin-records-tab" data-status="declined">
                        <i class="fa fa-times"></i> Declined
                        <span class="badge badge-danger tab-count" data-status="declined">{{ $statusCounts['declined'] ?? 0 }}</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Record Type</th>
                            <th>Details</th>
                            <th>Created By</th>
                            <th>Date</th>
                            <th id="administrativeRecordsActionHeader">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="administrativeRecordsTableBody">
                        <tr><td colspan="6" class="text-center">Loading records...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<!-- Decline Modal -->
<div class="modal fade" id="declineModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Decline Administrative Record</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="declineForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="decline_reason">Reason for Decline <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="decline_reason" name="decline_reason" rows="4" placeholder="Please provide a reason for declining this record..." required></textarea>
                    </div>
                    <div id="employeeInfo" class="alert alert-info">
                        <!-- Employee info will be populated by JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline Record</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function declineRecord(recordId, employeeName) {
    $('#declineForm').attr('action', '{{ url("hr/administrative-records") }}/' + recordId + '/decline');
    $('#employeeInfo').html('<strong>Employee:</strong> ' + employeeName + '<br><strong>Record ID:</strong> ' + recordId);
    $('#decline_reason').val('');
    $('#declineModal').modal('show');
}
</script>

<script>
(function() {
    const dataUrl = '{{ url('hr/administrative-records/data') }}';
    const recordsBody = document.getElementById('administrativeRecordsTableBody');
    const tabLinks = document.querySelectorAll('.admin-records-tab');
    const tabCounts = document.querySelectorAll('.tab-count');
    const activeStatus = '{{ $tab }}' || 'pending';

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function createRecordDetails(record) {
        if (record.record_type === 'disciplinary') {
            let details = `<strong>Type:</strong> ${escapeHtml(record.disciplinary_type || 'N/A')}<br>`;
            if (record.warning_type) {
                details += `<strong>Warning:</strong> ${escapeHtml(record.warning_type)} (${escapeHtml(record.warning_level || 'N/A')})<br>`;
            }
            if (record.number_of_days) {
                details += `<strong>Number of Days:</strong> ${escapeHtml(record.number_of_days)}<br>`;
                if (record.absence_dates && record.absence_dates.length) {
                    const dateBadges = record.absence_dates.map(d => `<span class="badge badge-info">${new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })}</span>`).join(' ');
                    details += `<strong>Dates:</strong> ${dateBadges}<br>`;
                }
            }
            if (record.comments) {
                details += `<strong>Comments:</strong> ${escapeHtml(record.comments)}`;
            }
            return details;
        }

        if (record.record_type === 'health') {
            let details = `<strong>Type:</strong> ${escapeHtml(record.health_type || 'N/A')}<br>`;
            if (record.incident_type) {
                details += `<strong>Incident:</strong> ${escapeHtml(record.incident_type)}<br>`;
            }
            if (record.description) {
                details += `<strong>Description:</strong> ${escapeHtml(record.description)}`;
            }
            return details;
        }

        if (record.record_type === 'career') {
            let details = `<strong>Type:</strong> ${escapeHtml(record.career_type || 'N/A')}<br>`;
            if (record.name) {
                details += `<strong>Name:</strong> ${escapeHtml(record.name)}<br>`;
            }
            if (record.description) {
                details += `<strong>Description:</strong> ${escapeHtml(record.description)}`;
            }
            return details;
        }

        return '';
    }

    function updateTabSelection(status) {
        tabLinks.forEach(link => {
            const li = link.closest('li');
            if (!li) return;
            if (link.dataset.status === status) {
                li.classList.add('active');
            } else {
                li.classList.remove('active');
            }
        });

        // Update action header based on status
        const actionHeader = document.getElementById('administrativeRecordsActionHeader');
        if (status === 'pending') {
            actionHeader.textContent = 'Actions';
        } else if (status === 'declined') {
            actionHeader.textContent = 'Decline Reason';
        } else {
            actionHeader.textContent = 'Approved By';
        }
    }

    function updateTabCount(status, total) {
        tabCounts.forEach(count => {
            if (count.dataset.status === status) {
                count.textContent = total;
            }
        });
    }

    function renderRecords(records, status) {
        if (!records.length) {
            recordsBody.innerHTML = `<tr><td colspan="6" class="text-center">No ${escapeHtml(status)} records found.</td></tr>`;
            return;
        }

        const rows = records.map(record => {
            let actionCell = '';
            if (status === 'pending') {
                const employeeName = escapeHtml(record.employee.full_name);
                actionCell = `
                    <td>
                        <form action="{{ url('hr/administrative-records') }}/` + record.id + `/approve" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-success btn-xs" onclick="return confirm('Are you sure you want to approve this record?')">
                                <i class="fa fa-check"></i> Approve
                            </button>
                        </form>
                        <button type="button" class="btn btn-danger btn-xs" onclick="declineRecord(${record.id}, '${employeeName}')">
                            <i class="fa fa-times"></i> Decline
                        </button>
                    </td>
                `;
            } else if (status === 'declined') {
                actionCell = `<td><span class="text-danger">${escapeHtml(record.decline_reason || 'N/A')}</span><br><small class="text-muted">Declined by: ${escapeHtml(record.approver_name || 'Unknown')}</small></td>`;
            } else {
                actionCell = `<td>${escapeHtml(record.approver_name || 'Unknown')}<br><small class="text-muted">${escapeHtml(record.approved_at || '')}</small></td>`;
            }

            return `
                <tr>
                    <td>
                        <strong>${escapeHtml(record.employee.full_name)}</strong><br>
                        <small class="text-muted">${escapeHtml(record.employee.employee_number)}</small>
                    </td>
                    <td><span class="label label-primary">${escapeHtml(record.record_type)}</span></td>
                    <td>${createRecordDetails(record)}</td>
                    <td>${escapeHtml(record.creator_name)}</td>
                    <td>${escapeHtml(record.created_at)}</td>
                    ${actionCell}
                </tr>
            `;
        }).join('');

        recordsBody.innerHTML = rows;
    }

    async function fetchRecords(status) {
        updateTabSelection(status);
        recordsBody.innerHTML = `<tr><td colspan="6" class="text-center">Loading records...</td></tr>`;

        try {
            const response = await fetch(`${dataUrl}?status=${encodeURIComponent(status)}`, {
                headers: { 'Accept': 'application/json' }
            });
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            const payload = await response.json();
            renderRecords(payload.records, status);
            updateTabCount(status, payload.total);
            window.history.replaceState({}, '', `{{ url('hr/administrative-records') }}?tab=${status}`);
        } catch (error) {
            recordsBody.innerHTML = `<tr><td colspan="6" class="text-danger text-center">Unable to load records. Please refresh the page.</td></tr>`;
            console.error('Failed to load administrative records:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        tabLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                fetchRecords(this.dataset.status);
            });
        });

        fetchRecords(activeStatus);
    });
})();
</script>
@endsection