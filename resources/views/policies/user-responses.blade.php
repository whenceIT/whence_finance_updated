@extends('layouts.master')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">User Policy Responses</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('policies.view_policies') }}" class="btn btn-secondary btn-sm">Back to Policies</a>
        </div>
    </div>
    <div class="box-body hidden-print">
                    <!-- Search Filters -->
                    <div class="d-flex flex-column flex-lg-row gap-3 mb-4 align-items-stretch align-items-lg-end">
                        <div class="flex-fill">
                            <label for="officeSelect" class="form-label fw-semibold text-muted">Select Office</label>
                            <select id="officeSelect" class="form-select">
                                <option value="">All Offices</option>
                                @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-fill">
                            <label for="userSearch" class="form-label fw-semibold text-muted">Search User</label>
                            <input type="text" id="userSearch" class="form-control" placeholder="Enter name or email">
                        </div>
                        <div class="flex-shrink-0 d-flex gap-2">
                            <button id="searchUsers" class="btn btn-primary px-4">Search Users</button>
                            <button id="loadDeclinedResponses" class="btn btn-danger px-4">Load Declined Responses</button>
                        </div>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading users...</p>
                    </div>
                    <br>
                    <!-- User Results -->
                    <div id="userResults" class="row g-3 mt-2">
                        <div class="col-md-12 mt-1">
                            <div class="alert alert-info text-center">
                                Select an office above to load users.
                            </div>
                        </div>
                    </div>

                    <!-- User Responses Section -->
                    <div id="userResponses" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Policy Responses</h4>
                            <button id="backToUsers" class="btn btn-secondary btn-sm">Back to Users</button>
                        </div>
                        <div class="card border-primary">
                            <div class="card-body">
                                <div id="responsesTable" class="table-responsive"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Declined Responses Table -->
                    <div id="declinedResponsesTable" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 class="mb-0">Declined Policy Responses</h4>
                            <button id="backToUsersFromDeclined" class="btn btn-secondary btn-sm">Back to Users</button>
                        </div>
                        <div class="card border-danger">
                            <div class="card-body">
                                <div id="declinedTableContent" class="table-responsive"></div>
                            </div>
                        </div>
                    </div>
    </div>
</div>

<script>
function loadUsers(query = '', officeId = '') {
    const loadingDiv = document.getElementById('loadingIndicator');
    const resultsDiv = document.getElementById('userResults');

    loadingDiv.style.display = 'block';
    resultsDiv.innerHTML = '';
    document.getElementById('declinedResponsesTable').style.display = 'none';

    fetch(`/policies/search-users?query=${encodeURIComponent(query)}&office_id=${officeId}`)
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';

            if (data.length === 0) {
                resultsDiv.innerHTML = `
                    <div class="col-12">
                        <div class="alert alert-info text-center" role="alert">
                            No users found matching the criteria.
                        </div>
                    </div>
                `;
                return;
            }

            data.forEach(user => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-xl-3 col-lg-4 col-md-6 col-sm-12';

                colDiv.innerHTML = `
                    <div class="card h-100 border-0 shadow-sm hover-card">
                        <div class="card-body text-center">
                            <div class="avatar-circle mx-auto mb-3">
                                <span class="text-primary fw-bold">${user.name.charAt(0).toUpperCase()}</span>
                            </div>
                            <h6 class="card-title fw-bold text-truncate">${user.name}</h6>
                            <p class="card-text text-muted small">${user.email}</p>
                            <button class="btn btn-primary btn-sm w-100" onclick="viewUserResponses(${user.id}, '${user.name}')">View Responses</button>
                        </div>
                    </div>
                `;

                resultsDiv.appendChild(colDiv);
            });
        })
        .catch(error => {
            loadingDiv.style.display = 'none';
            resultsDiv.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center" role="alert">
                        Error loading users. Please try again.
                    </div>
                </div>
            `;
        });
}

document.getElementById('officeSelect').addEventListener('change', function() {
    const officeId = this.value;
    if (officeId) {
        loadUsers('', officeId);
    } else {
        document.getElementById('userResults').innerHTML = '';
        document.getElementById('userResponses').style.display = 'none';
        document.getElementById('declinedResponsesTable').style.display = 'none';
    }
});

document.getElementById('searchUsers').addEventListener('click', function() {
    const query = document.getElementById('userSearch').value;
    const officeId = document.getElementById('officeSelect').value;
    loadUsers(query, officeId);
    document.getElementById('declinedResponsesTable').style.display = 'none';
});

function viewUserResponses(userId, userName) {
    const userResultsDiv = document.getElementById('userResults');
    const responsesDiv = document.getElementById('userResponses');
    const tableDiv = document.getElementById('responsesTable');

    // Hide user results and show responses
    userResultsDiv.style.display = 'none';
    responsesDiv.style.display = 'block';

    // Show loading in table
    tableDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading responses for ${userName}...</p>
        </div>
    `;

    fetch(`/policies/user-responses/${userId}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Policy Title</th>
                                <th class="text-center">Response Status</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            if (data.length === 0) {
                html += `
                    <tr>
                        <td colspan="2" class="text-center text-muted py-4">
                            No policies found.
                        </td>
                    </tr>
                `;
            } else {
                data.forEach(response => {
                    const status = response.status === 'accepted' ? '<span class="badge badge-success">Accepted</span>' :
                                response.status === 'declined' ? '<span class="badge badge-danger">Declined</span>' :
                                '<span class="badge badge-warning">Pending</span>';
                    html += `<tr><td class="fw-semibold">${response.policy_title}</td><td class="text-center">${status}</td></tr>`;
                });
            }

            html += `
                        </tbody>
                    </table>
                </div>
            `;

            tableDiv.innerHTML = html;
        })
        .catch(error => {
            tableDiv.innerHTML = `
                <div class="alert alert-danger text-center" role="alert">
                    Error loading responses. Please try again.
                </div>
            `;
        });
}

document.getElementById('backToUsers').addEventListener('click', function() {
    document.getElementById('userResults').style.display = 'flex';
    document.getElementById('userResponses').style.display = 'none';
});

document.getElementById('loadDeclinedResponses').addEventListener('click', function() {
    const officeId = document.getElementById('officeSelect').value;
    const userQuery = document.getElementById('userSearch').value;
    const tableDiv = document.getElementById('declinedTableContent');
    const responsesDiv = document.getElementById('declinedResponsesTable');
    const userResultsDiv = document.getElementById('userResults');
    const userResponsesDiv = document.getElementById('userResponses');

    // Hide user results and responses
    userResultsDiv.style.display = 'none';
    userResponsesDiv.style.display = 'none';
    responsesDiv.style.display = 'block';

    // Show loading
    tableDiv.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading declined responses...</p>
        </div>
    `;

    fetch(`/policies/declined-responses?office_id=${officeId}&user_query=${encodeURIComponent(userQuery)}`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <table class="table table-hover table-striped">
                    <thead class="table-danger">
                        <tr>
                            <th>User Name</th>
                            <th>Email</th>
                            <th>Branch</th>
                            <th>Policy Title</th>
                            <th>Status</th>
                            <th>Responded At</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            if (data.length === 0) {
                html += `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            No declined responses found.
                        </td>
                    </tr>
                `;
            } else {
                data.forEach(response => {
                    html += `<tr>
                        <td class="fw-semibold">${response.user_name}</td>
                        <td>${response.user_email}</td>
                        <td>${response.office_name}</td>
                        <td>${response.policy_title}</td>
                        <td><span class="badge badge-danger">${response.status}</span></td>
                        <td>${response.responded_at}</td>
                    </tr>`;
                });
            }

            html += `
                    </tbody>
                </table>
            `;

            tableDiv.innerHTML = html;
        })
        .catch(error => {
            tableDiv.innerHTML = `
                <div class="alert alert-danger text-center" role="alert">
                    Error loading declined responses. Please try again.
                </div>
            `;
        });
});

document.getElementById('backToUsersFromDeclined').addEventListener('click', function() {
    document.getElementById('userResults').style.display = 'flex';
    document.getElementById('declinedResponsesTable').style.display = 'none';
    document.getElementById('userResponses').style.display = 'none';
});
</script>

@endsection

@section('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.avatar-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    font-weight: bold;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}

.table th {
    border-top: none;
    font-weight: 600;
}

.badge {
    font-size: 0.85em;
}

@media (max-width: 768px) {
    .avatar-circle {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }

    .card-title {
        font-size: 1rem;
    }
}
</style>
@endsection