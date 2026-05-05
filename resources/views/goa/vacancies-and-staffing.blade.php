@extends('layouts.master')
@section('title')
    GOA Manager - Vacancies and Staffing
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Manage staffing levels and track open positions.</p>
        </div>
    </div>

    <style>
        .staffing-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            margin-bottom: 1rem;
            justify-content: flex-end;
        }

        .staffing-action-btn {
            border: none;
            border-radius: 8px;
            padding: 0.8rem 1.25rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(135deg, #0069d9 0%, #0056b3 100%);
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .staffing-action-btn.secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .staffing-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            opacity: 0.95;
        }

        .staffing-nav-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }

        .staffing-nav-btn {
            border: none;
            background: white;
            color: #6c757d;
            font-weight: 500;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .staffing-nav-btn:hover {
            background-color: rgba(0, 123, 255, 0.08);
            color: #495057;
            transform: translateY(-2px);
        }

        .staffing-nav-btn.active {
            background: linear-gradient(135deg, #e7f3ff 0%, #f0f7ff 100%);
            color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            border-bottom: 3px solid #007bff;
        }

        .staffing-content-container {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .staffing-section {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .staffing-section.active {
            display: block;
        }

        .staffing-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            z-index: 1040;
        }

        .staffing-modal {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(94vw, 680px);
            max-height: 90vh;
            overflow: hidden;
            display: none;
            z-index: 1050;
        }

        .staffing-modal-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 55px rgba(0, 0, 0, 0.18);
            overflow: hidden;
        }

        .staffing-modal-header,
        .staffing-modal-footer {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .staffing-modal-footer {
            border-top: 1px solid #e9ecef;
            border-bottom: none;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .staffing-modal-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .staffing-modal-close {
            background: transparent;
            border: none;
            font-size: 1.5rem;
            line-height: 1;
            color: #495057;
            cursor: pointer;
        }

        .staffing-modal-body {
            padding: 1.5rem;
            max-height: 66vh;
            overflow-y: auto;
        }

        .staffing-modal-form-group {
            margin-bottom: 1rem;
        }

        .staffing-modal-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #343a40;
        }

        .staffing-modal-input,
        .staffing-modal-select,
        .staffing-modal-textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            color: #495057;
            background: #f8f9fa;
        }

        .staffing-modal-textarea {
            min-height: 130px;
            resize: vertical;
        }

        .staffing-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .staffing-modal-submit,
        .staffing-modal-secondary {
            border: none;
            border-radius: 10rem;
            padding: 0.85rem 1.4rem;
            font-weight: 600;
            cursor: pointer;
        }

        .staffing-modal-submit {
            background: #007bff;
            color: #fff;
        }

        .staffing-modal-secondary {
            background: #f1f3f5;
            color: #495057;
        }

        .staffing-modal-submit:hover,
        .staffing-modal-secondary:hover {
            opacity: 0.95;
        }

        .staffing-modal-section {
            display: none;
        }

        .staffing-modal-section.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <div class="staffing-actions">
        <button type="button" class="staffing-action-btn" data-modal="addPosition">
            <i class="fas fa-plus"></i>Add Open Position
        </button>
        <button type="button" class="staffing-action-btn secondary" data-modal="addDepartment">
            <i class="fas fa-building"></i>Add Department
        </button>
        <button type="button" class="staffing-action-btn secondary" data-modal="addRole">
            <i class="fas fa-user-plus"></i>Add New Position
        </button>
    </div>

    <div class="staffing-nav-container" role="tablist">
        <button class="staffing-nav-btn active" data-section="positions" role="tab">
            <i class="fas fa-briefcase"></i>Open Positions
        </button>
        <button class="staffing-nav-btn" data-section="overview" role="tab">
            <i class="fas fa-chart-line"></i>Staffing Overview
        </button>
        <button class="staffing-nav-btn" data-section="breakdown" role="tab">
            <i class="fas fa-sitemap"></i>Department Breakdown
        </button>
        <button class="staffing-nav-btn" data-section="hires" role="tab">
            <i class="fas fa-user-check"></i>Recent Hires
        </button>
    </div>

    <div class="staffing-content-container" id="staffingTabsContent">
        <div class="staffing-section active" id="positions" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-briefcase text-primary"></i> Open Positions
            </h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Position ID</th>
                                    <th>Department</th>
                                    <th>Position Title</th>
                                    <th>Grade</th>
                                    <th>Status</th>
                                    <th>Posted Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($positions as $position)
                                <tr>
                                    <td>{{ $position->id }}</td>
                                    <td>{{ $position->department_id ? 'Department ' . $position->department_id : 'N/A' }}</td>
                                    <td>{{ $position->name }}</td>
                                    <td>{{ $position->num_of_active ?? 'N/A' }}</td>
                                    <td>
                                        @if($position->status == 'Open')
                                            <span class="badge badge-success">Open</span>
                                        @elseif($position->status == 'In Review')
                                            <span class="badge badge-warning">In Review</span>
                                        @else
                                            <span class="badge badge-danger">{{ $position->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $position->posted_date ? $position->posted_date->format('Y-m-d') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">Edit</button>
                                        <button class="btn btn-sm btn-info">View</button>
                                        <button class="btn btn-sm btn-success">Fill Position</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">No open positions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
        </div>

        <div class="staffing-section" id="overview" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-chart-line text-primary"></i> Staffing Overview
            </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Positions
                            <span class="badge badge-primary badge-pill">500</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Filled Positions
                            <span class="badge badge-success badge-pill">450</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Vacant Positions
                            <span class="badge badge-warning badge-pill">50</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Positions in Recruitment
                            <span class="badge badge-info badge-pill">12</span>
                        </li>
                    </ul>
        </div>

        <div class="staffing-section" id="breakdown" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-sitemap text-primary"></i> Department Breakdown
            </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Finance
                            <span class="badge badge-success badge-pill">45/50</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            IT
                            <span class="badge badge-warning badge-pill">18/20</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Public Works
                            <span class="badge badge-danger badge-pill">75/85</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Health
                            <span class="badge badge-success badge-pill">120/120</span>
                        </li>
                    </ul>
        </div>

        <div class="staffing-section" id="hires" role="tabpanel">
            <h5 class="mb-4">
                <i class="fas fa-user-check text-primary"></i> Recent Hires
            </h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Hire Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Jane Doe</td>
                                    <td>Accountant</td>
                                    <td>Finance</td>
                                    <td>2026-04-15</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Mike Johnson</td>
                                    <td>IT Support</td>
                                    <td>IT</td>
                                    <td>2026-04-10</td>
                                    <td><span class="badge badge-success">Active</span></td>
                                </tr>
                                <tr>
                                    <td>Sarah Wilson</td>
                                    <td>Engineer</td>
                                    <td>Public Works</td>
                                    <td>2026-04-05</td>
                                    <td><span class="badge badge-info">On Probation</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
        </div>
    </div>

    <div class="staffing-modal-backdrop" id="staffingModalBackdrop"></div>
    <div class="staffing-modal" id="staffingModal">
        <div class="staffing-modal-card">
            <div class="staffing-modal-header">
                <div>
                    <h4 class="staffing-modal-title" id="staffingModalTitle">Add Open Position</h4>
                    <p class="text-muted small mb-0" id="staffingModalSubtitle">Create a new vacancy or department entry quickly.</p>
                </div>
                <button type="button" class="staffing-modal-close" data-dismiss="staffing-modal">×</button>
            </div>
            <div class="staffing-modal-body">
                <!-- Position Form -->
                <form id="positionForm" method="POST" action="{{ route('staff.update-position') }}">
                    @csrf
                    <div class="staffing-modal-section active" data-type="addPosition">
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="positionId">Select Position</label>
                            <select class="staffing-modal-select" id="positionId" name="positionId">
                                <option value="">-- Select Position --</option>
                                @foreach(\App\Models\Position::all() as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="numOfVacancies">Number of Vacancies</label>
                            <input class="staffing-modal-input" id="numOfVacancies" name="numOfVacancies" type="number" min="0" placeholder="0">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="numOfActive">Number of Active</label>
                            <input class="staffing-modal-input" id="numOfActive" name="numOfActive" type="number" min="0" placeholder="0">
                        </div>
                    </div>
                </form>

                <!-- Department Form -->
                <form id="departmentForm" method="POST" action="{{ route('staff.store-department') }}">
                    @csrf
                    <div class="staffing-modal-section" data-type="addDepartment">
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="departmentName">Department Name</label>
                            <input class="staffing-modal-input" id="departmentName" name="departmentName" type="text" placeholder="Human Resources">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="departmentHead">Department Head</label>
                            <input class="staffing-modal-input" id="departmentHead" name="departmentHead" type="text" placeholder="Jane Smith">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="departmentCapacity">Current Capacity</label>
                            <input class="staffing-modal-input" id="departmentCapacity" name="departmentCapacity" type="number" placeholder="50">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="departmentNotes">Notes</label>
                            <textarea class="staffing-modal-textarea" id="departmentNotes" name="departmentNotes" placeholder="Optional notes about the department."></textarea>
                        </div>
                    </div>
                </form>

                <!-- Role Form -->
                <form id="roleForm" method="POST" action="{{ route('staff.store-role') }}">
                    @csrf
                    <div class="staffing-modal-section" data-type="addRole">
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="roleTitle">New Position Title</label>
                            <input class="staffing-modal-input" id="roleTitle" name="roleTitle" type="text" placeholder="IT Support Specialist">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="roleDepartment">Department</label>
                            <input class="staffing-modal-input" id="roleDepartment" name="roleDepartment" type="text" placeholder="IT">
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="roleLevel">Role Level</label>
                            <select class="staffing-modal-select" id="roleLevel" name="roleLevel">
                                <option>Entry</option>
                                <option>Mid</option>
                                <option>Senior</option>
                            </select>
                        </div>
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="roleDescription">Description</label>
                            <textarea class="staffing-modal-textarea" id="roleDescription" name="roleDescription" placeholder="Describe the new role and responsibilities."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="staffing-modal-footer">
                <button type="button" class="staffing-modal-secondary" data-dismiss="staffing-modal">Cancel</button>
                <button type="submit" class="staffing-modal-submit" form="staffingModalForm">Save</button>
            </div>
        </div>
    </div>

    <script>
        const staffingModalBackdrop = document.getElementById('staffingModalBackdrop');
        const staffingModal = document.getElementById('staffingModal');
        const staffingModalTitle = document.getElementById('staffingModalTitle');
        const staffingModalSections = document.querySelectorAll('.staffing-modal-section');

        const modalConfig = {
            addPosition: {
                title: 'Add Open Position',
                subtitle: 'Create a new vacancy and track its details.',
            },
            addDepartment: {
                title: 'Add Department',
                subtitle: 'Register a new department for your staffing structure.',
            },
            addRole: {
                title: 'Add New Position',
                subtitle: 'Define a new role for future hiring.',
            }
        };

        function openStaffingModal(type) {
            const config = modalConfig[type] || modalConfig.addPosition;
            staffingModalTitle.textContent = config.title;
            document.getElementById('staffingModalSubtitle').textContent = config.subtitle;
            const submitButton = document.querySelector('.staffing-modal-submit');
            if (type === 'addPosition') {
                submitButton.setAttribute('form', 'positionForm');
            } else if (type === 'addDepartment') {
                submitButton.setAttribute('form', 'departmentForm');
            } else if (type === 'addRole') {
                submitButton.setAttribute('form', 'roleForm');
            }
            staffingModalSections.forEach(section => {
                section.classList.toggle('active', section.dataset.type === type);
            });
            staffingModalBackdrop.style.display = 'block';
            staffingModal.style.display = 'block';
        }

        function closeStaffingModal() {
            staffingModalBackdrop.style.display = 'none';
            staffingModal.style.display = 'none';
        }

        document.querySelectorAll('[data-modal]').forEach(button => {
            button.addEventListener('click', () => {
                openStaffingModal(button.dataset.modal);
            });
        });

        document.querySelectorAll('[data-dismiss="staffing-modal"]').forEach(button => {
            button.addEventListener('click', closeStaffingModal);
        });

        staffingModalBackdrop.addEventListener('click', closeStaffingModal);

        // Form submits to server

        document.querySelectorAll('.staffing-nav-btn').forEach(button => {
            button.addEventListener('click', function() {
                const sectionId = this.getAttribute('data-section');
                
                // Remove active class from all buttons and sections
                document.querySelectorAll('.staffing-nav-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.staffing-section').forEach(section => section.classList.remove('active'));
                
                // Add active class to clicked button and corresponding section
                this.classList.add('active');
                document.getElementById(sectionId).classList.add('active');
            });
        });
    </script>
@endsection