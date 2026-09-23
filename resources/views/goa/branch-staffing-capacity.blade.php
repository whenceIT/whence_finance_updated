@extends('layouts.master')
@section('title')
    GOA Manager - Branch Staffing Capacity
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Compare the approved staffing capacity of each branch against the personnel actually working there.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>There were some issues with your submission.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @php
        $baseTabs = ['dashboard', 'branches', 'vacancies', 'setup'];
        $validTabs = $selectedOffice ? array_merge($baseTabs, ['recruitment-pipeline']) : $baseTabs;
        $activeTab = in_array($activeTab, $validTabs, true) ? $activeTab : 'dashboard';
        $selectedOfficeId = $selectedOffice->id ?? null;
        $recruitmentStatuses = \App\Models\Vacancy::RECRUITMENT_STATUSES;
        $interviewStatuses = \App\Models\Vacancy::INTERVIEW_STATUSES;
        $offerStatuses = \App\Models\Vacancy::OFFER_STATUSES;

        // Payload used by the vacancy modal when editing an existing record
        $vacancyModalPayload = function ($vacancy) {
            return [
                'id' => $vacancy->id,
                'office_id' => $vacancy->office_id,
                'position_id' => $vacancy->position_id,
                'num_of_vacancies' => $vacancy->num_of_vacancies,
                'date_arose' => $vacancy->date_arose ? $vacancy->date_arose->format('Y-m-d') : '',
                'reason' => $vacancy->reason,
                'recruitment_status' => $vacancy->recruitment_status ?: $vacancy->status,
                'num_of_applicants' => $vacancy->num_of_applicants,
                'num_of_shortlisted' => $vacancy->num_of_shortlisted,
                'interview_status' => $vacancy->interview_status,
                'selected_candidate' => $vacancy->selected_candidate,
                'offer_status' => $vacancy->offer_status,
                'expected_reporting_date' => $vacancy->expected_reporting_date ? $vacancy->expected_reporting_date->format('Y-m-d') : '',
                'actual_reporting_date' => $vacancy->actual_reporting_date ? $vacancy->actual_reporting_date->format('Y-m-d') : '',
                'notes' => $vacancy->notes,
            ];
        };
    @endphp

    <style>
        .capacity-nav-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            padding: 1.5rem;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 0;
        }

        .capacity-nav-btn {
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
            font-size: 1.25rem;
        }

        .capacity-nav-btn:hover {
            background-color: rgba(0, 123, 255, 0.08);
            color: #495057;
            transform: translateY(-2px);
        }

        .capacity-nav-btn.active {
            background: linear-gradient(135deg, #e7f3ff 0%, #f0f7ff 100%);
            color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
            border-bottom: 3px solid #007bff;
        }

        .capacity-content-container {
            background: white;
            border-radius: 0 0 12px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 2rem;
        }

        .capacity-section {
            display: none;
            animation: capacityFadeIn 0.3s ease;
        }

        .capacity-section.active {
            display: block;
        }

        .capacity-section-title {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 60px;
            font-weight: 600;
            font-size: 1.25rem;
            box-shadow: 0 8px 20px -4px rgba(59, 130, 246, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);
            cursor: default;
            width: 100%;
            margin-left: auto;
            margin-right: auto;
        }

        .capacity-empty-state {
            border: 1px dashed #ced4da;
            border-radius: 12px;
            background: #f8f9fa;
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #6c757d;
        }

        .capacity-empty-state i {
            font-size: 2rem;
            color: #adb5bd;
            display: block;
            margin-bottom: 0.75rem;
        }

        @keyframes capacityFadeIn {
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
    <style>
        .capacity-filter-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            margin-bottom: 1.25rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .capacity-filter-bar .form-group {
            margin-bottom: 0;
            min-width: 280px;
        }

        .capacity-branch-header {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border: 1px solid #e5e7eb;
            border-left: 5px solid #3b82f6;
            border-radius: 12px;
            padding: 1.1rem 1.4rem;
            margin-bottom: 1.25rem;
        }

        .capacity-branch-header h3 {
            margin: 0 0 0.35rem 0;
            font-weight: 700;
            color: #111827;
        }

        .capacity-branch-header p {
            margin: 0;
            color: #64748b;
        }

        .capacity-kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .capacity-kpi {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
            border-radius: 10px;
            padding: 1rem 1.15rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        }

        .capacity-kpi .capacity-kpi-label {
            display: block;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 0.35rem;
        }

        .capacity-kpi .capacity-kpi-value {
            font-size: 1.9rem;
            font-weight: 700;
            color: #111827;
            line-height: 1.1;
        }

        .capacity-kpi .capacity-kpi-note {
            display: block;
            font-size: 0.95rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .capacity-kpi.kpi-approved {
            border-left-color: #6366f1;
        }

        .capacity-kpi.kpi-current {
            border-left-color: #10b981;
        }

        .capacity-kpi.kpi-vacant {
            border-left-color: #ef4444;
        }

        .capacity-kpi.kpi-staffing {
            border-left-color: #f59e0b;
        }

        .capacity-kpi.kpi-vacancy-pct {
            border-left-color: #0ea5e9;
        }

        .capacity-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.95rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .capacity-badge.ok {
            background: #dcfce7;
            color: #166534;
        }

        .capacity-badge.warn {
            background: #fef3c7;
            color: #92400e;
        }

        .capacity-badge.critical {
            background: #fee2e2;
            color: #991b1b;
        }

        .capacity-badge.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .capacity-badge.muted {
            background: #f3f4f6;
            color: #4b5563;
        }

        .capacity-progress {
            height: 8px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 0.4rem;
        }

        .capacity-progress > span {
            display: block;
            height: 100%;
            border-radius: 20px;
            background: #10b981;
        }

        .capacity-progress > span.warn {
            background: #f59e0b;
        }

        .capacity-progress > span.critical {
            background: #ef4444;
        }
    </style>
    <style>
        .capacity-table {
            font-size: 1.05rem;
        }

        .capacity-table thead th {
            background: #f8fafc;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #475569;
            border-bottom: 2px solid #e2e8f0 !important;
            vertical-align: middle !important;
        }

        .capacity-table td,
        .capacity-table th {
            vertical-align: middle !important;
        }

        .capacity-table tbody tr.capacity-row-warning {
            background: #fffbeb;
        }

        .capacity-personnel-toggle {
            border: none;
            background: transparent;
            color: #2563eb;
            font-weight: 600;
            padding: 0;
            cursor: pointer;
            text-decoration: underline;
        }

        .capacity-personnel-panel {
            display: none;
            background: #f8fafc;
        }

        .capacity-personnel-panel.open {
            display: table-row;
        }

        .capacity-personnel-list {
            margin: 0;
            padding-left: 1.1rem;
            columns: 2;
        }

        .capacity-personnel-list li {
            line-height: 1.6;
            break-inside: avoid;
        }

        .capacity-roster {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 0.75rem;
        }

        .capacity-roster-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.85rem 1rem;
            background: #ffffff;
        }

        .capacity-roster-card h6 {
            margin: 0 0 0.5rem 0;
            font-weight: 700;
            color: #1f2937;
        }

        .capacity-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            display: none;
            z-index: 1040;
        }

        .capacity-modal-backdrop.open {
            display: block;
        }

        .capacity-modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            padding: 1rem;
        }

        .capacity-modal.open {
            display: flex;
        }

        .capacity-modal-card {
            width: 100%;
            max-width: 880px;
            max-height: 92vh;
            overflow: hidden;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 24px 80px rgba(15, 23, 42, 0.2);
        }

        .capacity-modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.15rem 1.5rem;
        }

        .capacity-modal-header h4 {
            color: #ffffff;
            margin: 0;
            font-size: 1.35rem;
            font-weight: 700;
        }

        .capacity-modal-close {
            background: transparent;
            border: none;
            color: #ffffff;
            font-size: 1.6rem;
            line-height: 1;
            cursor: pointer;
        }

        .capacity-modal-body {
            padding: 1.5rem;
            max-height: 64vh;
            overflow-y: auto;
        }

        .capacity-modal-footer {
            padding: 1.15rem 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .capacity-modal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .capacity-modal-grid .capacity-field-wide {
            grid-column: 1 / -1;
        }

        .capacity-field label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.35rem;
            font-size: 1rem;
        }

        .capacity-field .form-control {
            border-radius: 9px;
        }

        @media (max-width: 768px) {
            .capacity-modal-grid {
                grid-template-columns: 1fr;
            }

            .capacity-personnel-list {
                columns: 1;
            }
        }
    </style>
    <form method="GET" action="{{ route('goa.branch-staffing-capacity') }}" id="capacityFilterForm" class="capacity-filter-bar">
        <div class="form-group">
            <label for="capacityBranchFilter"><i class="fa fa-building"></i> Branch</label>
            <select class="form-control" id="capacityBranchFilter" name="office_id"
                    onchange="document.getElementById('capacityFilterForm').submit();">
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ (int) $selectedOfficeId === (int) $office->id ? 'selected' : '' }}>
                        {{ $office->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="tab" id="capacityFilterTab" value="{{ $activeTab }}">
        <div class="form-group">
            <noscript><button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button></noscript>
        </div>
    </form>

    <div class="capacity-nav-container" role="tablist">
        <button class="capacity-nav-btn {{ $activeTab === 'dashboard' ? 'active' : '' }}" data-section="dashboard" role="tab" type="button">
            <i class="fa fa-tachometer"></i> Branch Dashboard
        </button>
        <button class="capacity-nav-btn {{ $activeTab === 'branches' ? 'active' : '' }}" data-section="branches" role="tab" type="button">
            <i class="fa fa-building-o"></i> All Branches
        </button>
        <button class="capacity-nav-btn {{ $activeTab === 'vacancies' ? 'active' : '' }}" data-section="vacancies" role="tab" type="button">
            <i class="fa fa-briefcase"></i> Vacancy Register
        </button>
        <button class="capacity-nav-btn {{ $activeTab === 'setup' ? 'active' : '' }}" data-section="setup" role="tab" type="button">
            <i class="fa fa-sliders"></i> Capacity Setup
        </button>
        @if($selectedOffice)
            <button class="capacity-nav-btn {{ $activeTab === 'recruitment-pipeline' ? 'active' : '' }}" data-section="recruitment-pipeline" role="tab" type="button">
                <i class="fa fa-project-diagram"></i> Recruitment Pipeline
            </button>
        @endif
    </div>

    <div class="capacity-content-container" id="capacityTabsContent">

        {{-- ==================================================================
             BRANCH DASHBOARD — approved capacity vs current personnel
        =================================================================== --}}
        <div class="capacity-section {{ $activeTab === 'dashboard' ? 'active' : '' }}" id="dashboard" role="tabpanel">
            <h5 class="capacity-section-title"><i class="fa fa-tachometer"></i> Branch Staffing Dashboard</h5>

            @if(!$selectedOffice)
                <div class="capacity-empty-state">
                    <i class="fa fa-building-o"></i>
                    <p class="mb-0">No active branch could be found for this selection.</p>
                </div>
            @else
                <div class="capacity-branch-header">
                    <div>
                        <h3>{{ $selectedOffice->name }}</h3>
                        <p>
                            <i class="fa fa-map-marker"></i> {{ $selectedOffice->district->name ?? 'No district' }}
                            &nbsp;•&nbsp; {{ $selectedOffice->province->name ?? 'No province' }}
                            &nbsp;•&nbsp; <i class="fa fa-users"></i> Headcount on file: {{ $selectedOffice->branch_capacity ?? 'Not set' }}
                            &nbsp;•&nbsp; <i class="fa fa-desktop"></i> Workstations: {{ $selectedOffice->workstations ?? 'Not set' }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('goa.branch-staffing-capacity', ['office_id' => $selectedOffice->id, 'tab' => 'setup']) }}"
                           class="btn btn-default btn-sm">
                            <i class="fa fa-sliders"></i> Set approved capacity
                        </a>
                        <button type="button" class="btn btn-primary btn-sm"
                                data-vacancy-create="{{ json_encode([
                                    'office_id' => $selectedOffice->id,
                                    'position_id' => '',
                                    'num_of_vacancies' => '',
                                    'date_arose' => now()->format('Y-m-d'),
                                    'recruitment_status' => 'Open',
                                ]) }}">
                            <i class="fa fa-plus"></i> Log vacancy
                        </button>
                    </div>
                </div>
                <div class="capacity-kpi-grid">
                    <div class="capacity-kpi kpi-approved">
                        <span class="capacity-kpi-label">Approved Capacity</span>
                        <span class="capacity-kpi-value">{{ number_format($approvedTotal) }}</span>
                         <span class="capacity-kpi-note">
                             {{ $structureDefined ? 'Branch headcount on file' : 'No capacity set — set via the Setup tab' }}
                         </span>
                    </div>
                    <div class="capacity-kpi kpi-current">
                        <span class="capacity-kpi-label">Current Personnel</span>
                        <span class="capacity-kpi-value">{{ number_format($personnelTotal) }}</span>
                        <span class="capacity-kpi-note">Active staff attached to this branch</span>
                    </div>
                    <div class="capacity-kpi kpi-vacant">
                        <span class="capacity-kpi-label">Total Vacancies</span>
                        <span class="capacity-kpi-value">{{ number_format($vacancyTotal) }}</span>
                        <span class="capacity-kpi-note">Approved capacity &minus; current personnel</span>
                    </div>
                    <div class="capacity-kpi kpi-staffing">
                        <span class="capacity-kpi-label">Staffing Level</span>
                        <span class="capacity-kpi-value">
                            {{ $staffingPercentage === null ? 'N/A' : $staffingPercentage . '%' }}
                        </span>
                        @php
                            $staffingBarClass = $staffingPercentage === null ? '' : ($staffingPercentage >= 90 ? '' : ($staffingPercentage >= 70 ? 'warn' : 'critical'));
                        @endphp
                        <div class="capacity-progress">
                            <span class="{{ $staffingBarClass }}"
                                  style="width: {{ $staffingPercentage === null ? 0 : min($staffingPercentage, 100) }}%;"></span>
                        </div>
                    </div>
                    <div class="capacity-kpi kpi-vacancy-pct">
                        <span class="capacity-kpi-label">Vacancy Percentage</span>
                        <span class="capacity-kpi-value">{{ $vacancyPercentage }}%</span>
                        <span class="capacity-kpi-note">{{ $vacancyTotal }} of {{ $approvedTotal }} approved positions unfilled</span>
                    </div>
                </div>

                @unless($structureDefined)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        No approved staffing capacity has been set for this branch yet. The figures above show zero
                        approved capacity.
                        <a href="{{ route('goa.branch-staffing-capacity', ['office_id' => $selectedOffice->id, 'tab' => 'setup']) }}">
                            Set the approved capacity now &raquo;
                        </a>
                    </div>
                @endunless
                <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                    <i class="fa fa-list-ol"></i> Positions: Approved Capacity vs Current Personnel
                </h5>

                <div class="table-responsive">
                    <table class="table table-striped capacity-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th class="text-right">Approved</th>
                                <th class="text-right">Current</th>
                                <th class="text-right">Vacancy</th>
                                <th class="text-right">Staffing %</th>
                                <th>Status</th>
                                <th>Personnel</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($positionRows as $row)
                                @php
                                    $rowPct = $row['staffing_percentage'];
                                    $rowBadge = $rowPct === null ? 'muted' : ($rowPct >= 90 ? 'ok' : ($rowPct >= 70 ? 'warn' : 'critical'));
                                @endphp
                                <tr class="{{ (!$row['in_structure'] || $row['vacancy'] > 0) ? 'capacity-row-warning' : '' }}">
                                    <td>
                                        <strong>{{ $row['position']->name ?? 'Unknown position' }}</strong>
                                        @unless($row['in_structure'])
                                            <span class="capacity-badge muted">Not in structure</span>
                                        @endunless
                                    </td>
                                    <td class="text-right">{{ $row['approved'] }}</td>
                                    <td class="text-right">{{ $row['current'] }}</td>
                                    <td class="text-right">
                                        @if($row['vacancy'] > 0)
                                            <span class="capacity-badge critical">{{ $row['vacancy'] }}</span>
                                        @else
                                            <span class="capacity-badge ok">0</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($rowPct === null)
                                            <span class="text-muted">N/A</span>
                                        @else
                                            {{ $rowPct }}%
                                        @endif
                                        @if($row['surplus'] > 0)
                                            <br><span class="capacity-badge info">+{{ $row['surplus'] }} over</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="capacity-badge {{ $rowBadge }}">
                                            {{ $row['vacancy'] > 0 ? $row['vacancy'] . ' to fill' : 'Fully staffed' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($row['personnel']->isEmpty())
                                            <span class="text-muted">&mdash;</span>
                                        @else
                                            <button type="button" class="capacity-personnel-toggle"
                                                    data-personnel-toggle="personnel-{{ $row['position_id'] }}">
                                                {{ $row['personnel']->count() }} name(s)
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @if($row['personnel']->isNotEmpty())
                                    <tr class="capacity-personnel-panel" id="personnel-{{ $row['position_id'] }}">
                                        <td colspan="7">
                                            <strong>Current personnel in {{ $row['position']->name ?? 'this position' }}:</strong>
                                            <ul class="capacity-personnel-list">
                                                @foreach($row['personnel'] as $member)
                                                    <li>
                                                        {{ $member->first_name }} {{ $member->last_name }}
                                                        @if($member->employee_number)
                                                            <small class="text-muted">({{ $member->employee_number }})</small>
                                                        @endif
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No position data found for this branch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Total</th>
                                <th class="text-right">{{ $approvedTotal }}</th>
                                <th class="text-right">{{ $personnelTotal }}</th>
                                <th class="text-right">{{ $vacancyTotal }}</th>
                                <th class="text-right">{{ $staffingPercentage === null ? 'N/A' : $staffingPercentage . '%' }}</th>
                                <th colspan="2">{{ $vacancyPercentage }}% vacancy rate</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 8px 20px -4px rgba(245, 158, 11, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05); color: #1f2937;">
                    <i class="fa fa-briefcase"></i> Vacancies by Position &amp; Recruitment Status
                </h5>

                @php
                    $gapRows = $positionRows->filter(function ($row) use ($vacancyByPosition) {
                        return $row['vacancy'] > 0 || $vacancyByPosition->has($row['position_id']);
                    });
                @endphp

                <div class="table-responsive">
                    <table class="table table-striped capacity-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th class="text-right">Approved</th>
                                <th class="text-right">Current</th>
                                <th class="text-right">Vacant</th>
                                <th>Recruitment Status</th>
                                <th class="text-right">Applicants</th>
                                <th class="text-right">Shortlisted</th>
                                <th class="text-right">Days Vacant</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($gapRows as $row)
                                @php $vacancyRecord = $vacancyByPosition->get($row['position_id']); @endphp
                                <tr>
                                    <td><strong>{{ $row['position']->name ?? 'Unknown position' }}</strong></td>
                                    <td class="text-right">{{ $row['approved'] }}</td>
                                    <td class="text-right">{{ $row['current'] }}</td>
                                    <td class="text-right"><span class="capacity-badge critical">{{ $row['vacancy'] }}</span></td>
                                    <td>
                                        @if($vacancyRecord)
                                            @php
                                                $recruitStatus = $vacancyRecord->recruitment_status ?: $vacancyRecord->status;
                                                $recruitBadge = $vacancyRecord->is_filled ? 'ok' : ($recruitStatus === 'Open' ? 'critical' : 'warn');
                                            @endphp
                                            <span class="capacity-badge {{ $recruitBadge }}">{{ $recruitStatus }}</span>
                                        @else
                                            <span class="capacity-badge muted">Not logged</span>
                                        @endif
                                    </td>
                                    <td class="text-right">{{ $vacancyRecord->num_of_applicants ?? 0 }}</td>
                                    <td class="text-right">{{ $vacancyRecord->num_of_shortlisted ?? 0 }}</td>
                                    <td class="text-right">
                                        @if($vacancyRecord && $vacancyRecord->days_vacant !== null)
                                            {{ $vacancyRecord->days_vacant }}
                                        @else
                                            <span class="text-muted">&mdash;</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($vacancyRecord)
                                            <button type="button" class="btn btn-xs btn-info"
                                                    data-vacancy-edit="{{ json_encode($vacancyModalPayload($vacancyRecord)) }}">
                                                <i class="fa fa-pencil"></i> Manage
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-xs btn-primary"
                                                    data-vacancy-create="{{ json_encode([
                                                        'office_id' => $selectedOffice->id,
                                                        'position_id' => $row['position_id'],
                                                        'num_of_vacancies' => $row['vacancy'],
                                                        'date_arose' => now()->format('Y-m-d'),
                                                        'recruitment_status' => 'Open',
                                                    ]) }}">
                                                <i class="fa fa-plus"></i> Log vacancy
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted">
                                        No vacancies &mdash; every approved position on this branch is filled.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                    <i class="fa fa-users"></i> Current Personnel ({{ $personnelTotal }})
                </h5>

                @php
                    $rosterGroups = $personnel->groupBy(function ($member) {
                        return $member->position->name ?? 'No position assigned';
                    });
                @endphp

                @forelse($rosterGroups as $positionName => $group)
                    <div class="capacity-roster-card" style="margin-bottom: 0.75rem;">
                        <h6>{{ $positionName }} <span class="capacity-badge info">{{ $group->count() }}</span></h6>
                        <ul class="capacity-personnel-list">
                            @foreach($group as $member)
                                <li style="display: flex; align-items: center; gap: 0.4rem;">
                                    <span>
                                        {{ $member->first_name }} {{ $member->last_name }}
                                        @if($member->employee_number)
                                            <small class="text-muted">({{ $member->employee_number }})</small>
                                        @endif
                                    </span>
                                    @if($positionName === 'No position assigned')
                                        <button type="button"
                                                class="btn btn-xs btn-default"
                                                title="Assign position"
                                                data-assign-position="{{ json_encode(['user_id' => $member->id, 'name' => $member->first_name . ' ' . $member->last_name]) }}"
                                                style="padding: 1px 5px; line-height: 1.4;">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <div class="capacity-empty-state">
                        <i class="fa fa-user-o"></i>
                        <p class="mb-0">No active personnel are currently attached to this branch.</p>
                    </div>
                @endforelse
            @endif
        </div>
        {{-- ==================================================================
             ALL BRANCHES — approved capacity vs personnel comparison
        =================================================================== --}}
        <div class="capacity-section {{ $activeTab === 'branches' ? 'active' : '' }}" id="branches" role="tabpanel">
            <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #0ea5e9 0%, #0369a1 100%); box-shadow: 0 8px 20px -4px rgba(14, 165, 233, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                <i class="fa fa-building-o"></i> Branch Capacity Comparison
            </h5>

            <div class="capacity-kpi-grid">
                <div class="capacity-kpi kpi-approved">
                    <span class="capacity-kpi-label">Total Approved Capacity</span>
                    <span class="capacity-kpi-value">{{ number_format($overallMetrics['approved']) }}</span>
                    <span class="capacity-kpi-note">Across {{ $branchSummary->count() }} active branches</span>
                </div>
                <div class="capacity-kpi kpi-current">
                    <span class="capacity-kpi-label">Total Personnel</span>
                    <span class="capacity-kpi-value">{{ number_format($overallMetrics['current']) }}</span>
                    <span class="capacity-kpi-note">Active staff on the branches</span>
                </div>
                <div class="capacity-kpi kpi-vacant">
                    <span class="capacity-kpi-label">Total Vacancies</span>
                    <span class="capacity-kpi-value">{{ number_format($overallMetrics['vacancy']) }}</span>
                    <span class="capacity-kpi-note">Company-wide staffing gap</span>
                </div>
                <div class="capacity-kpi kpi-staffing">
                    <span class="capacity-kpi-label">Overall Staffing Level</span>
                    <span class="capacity-kpi-value">{{ $overallMetrics['staffing_percentage'] === null ? 'N/A' : $overallMetrics['staffing_percentage'] . '%' }}</span>
                    @php
                        $overallPct = $overallMetrics['staffing_percentage'];
                        $overallBarClass = $overallPct === null ? '' : ($overallPct >= 90 ? '' : ($overallPct >= 70 ? 'warn' : 'critical'));
                    @endphp
                    <div class="capacity-progress">
                        <span class="{{ $overallBarClass }}" style="width: {{ $overallPct === null ? 0 : min($overallPct, 100) }}%;"></span>
                    </div>
                </div>
            </div>

            {{-- Branch comparison table --}}
            <div class="table-responsive">
                <table class="table table-striped capacity-table">
                    <thead>
                        <tr>
                            <th>Branch</th>
                            <th>District</th>
                            <th>Province</th>
                            <th class="text-right">Approved</th>
                            <th class="text-right">Current</th>
                            <th class="text-right">Vacancies</th>
                            <th class="text-right">Staffing %</th>
                            <th class="text-right">Vacancy %</th>
                            <th>Structure</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branchSummary as $summary)
                            @php
                                $summaryPct = $summary['staffing_percentage'];
                                $summaryBadge = $summaryPct === null ? 'muted' : ($summaryPct >= 90 ? 'ok' : ($summaryPct >= 70 ? 'warn' : 'critical'));
                            @endphp
                            <tr class="{{ $summary['vacancy'] > 0 ? 'capacity-row-warning' : '' }}">
                                <td>
                                    <strong>{{ $summary['office']->name }}</strong>
                                    @if((int) $selectedOfficeId === (int) $summary['office']->id)
                                        <span class="capacity-badge info">selected</span>
                                    @endif
                                </td>
                                <td>{{ $summary['office']->district->name ?? '&mdash;' }}</td>
                                <td>{{ $summary['office']->province->name ?? '&mdash;' }}</td>
                                <td class="text-right">{{ $summary['approved'] }}</td>
                                <td class="text-right">{{ $summary['current'] }}</td>
                                <td class="text-right">
                                    @if($summary['vacancy'] > 0)
                                        <span class="capacity-badge critical">{{ $summary['vacancy'] }}</span>
                                    @else
                                        <span class="capacity-badge ok">0</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <span class="capacity-badge {{ $summaryBadge }}">{{ $summaryPct === null ? 'N/A' : $summaryPct . '%' }}</span>
                                </td>
                                <td class="text-right">{{ $summary['vacancy_percentage'] }}%</td>
                                <td>
                                    @if($summary['structure_defined'])
                                        <span class="capacity-badge ok">Captured</span>
                                    @else
                                        <span class="capacity-badge muted">Headcount only</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('goa.branch-staffing-capacity', ['office_id' => $summary['office']->id, 'tab' => 'dashboard']) }}"
                                       class="btn btn-xs btn-default">
                                       <i class="fa fa-search"></i> View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted">No active branches found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ==================================================================
              RECRUITMENT PIPELINE — track recruitment process overview
         =================================================================== --}}
        <div class="capacity-section {{ $activeTab === 'recruitment-pipeline' ? 'active' : '' }}" id="recruitment-pipeline" role="tabpanel">
            <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); box-shadow: 0 8px 20px -4px rgba(139, 92, 246, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                <i class="fa fa-project-diagram"></i> Recruitment Pipeline
            </h5>

            <div class="capacity-empty-state" style="margin-bottom: 1rem;">
                <i class="fa fa-info-circle"></i>
                <p class="mb-0">View the <a href="{{ route('goa.recruitment-pipeline', ['office_id' => $selectedOfficeId]) }}" style="color: #3b82f6; text-decoration: underline;">full recruitment pipeline dashboard</a> for detailed tracking across all branches.</p>
            </div>

            @if($selectedOffice)
                @php
                    $vacanciesForOffice = $branchVacancies->filter(function($v) {
                        return $v->position_id !== null;
                    });
                    
                    $totalApplicants = $branchVacancies->sum('num_of_applicants');
                    $totalShortlisted = $branchVacancies->sum('num_of_shortlisted');
                    $totalInterviewed = $branchVacancies->whereIn('interview_status', ['Completed', 'In Progress'])->count();
                    $totalSelected = $branchVacancies->whereNotNull('selected_candidate')->where('selected_candidate', '!=', '')->count();
                    $totalOffersIssued = $branchVacancies->whereIn('offer_status', ['Accepted', 'Pending'])->count();
                    $totalReported = $branchVacancies->whereNotNull('actual_reporting_date')->count();
                @endphp

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                    <div class="capacity-kpi kpi-approved">
                        <span class="capacity-kpi-label">Total Vacancies</span>
                        <span class="capacity-kpi-value">{{ $branchVacancies->sum('num_of_vacancies') }}</span>
                    </div>
                    <div class="capacity-kpi kpi-current">
                        <span class="capacity-kpi-label">Applicants</span>
                        <span class="capacity-kpi-value">{{ number_format($totalApplicants) }}</span>
                    </div>
                    <div class="capacity-kpi kpi-vacant">
                        <span class="capacity-kpi-label">Shortlisted</span>
                        <span class="capacity-kpi-value">{{ number_format($totalShortlisted) }}</span>
                    </div>
                    <div class="capacity-kpi kpi-staffing">
                        <span class="capacity-kpi-label">Selected</span>
                        <span class="capacity-kpi-value">{{ number_format($totalSelected) }}</span>
                    </div>
                </div>

                @if($branchVacancies->isNotEmpty())
                    <table class="table table-striped capacity-table">
                        <thead>
                            <tr>
                                <th>Position</th>
                                <th class="text-right">Vacancies</th>
                                <th class="text-right">Applicants</th>
                                <th class="text-right">Shortlisted</th>
                                <th>Interview Status</th>
                                <th>Selected</th>
                                <th>Offer Status</th>
                                <th>Reported</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branchVacancies as $vacancy)
                                @php
                                    $offered = in_array($vacancy->offer_status ?? '', ['Accepted', 'Pending']);
                                    $interviewed = in_array($vacancy->interview_status ?? '', ['Completed', 'In Progress']);
                                @endphp
                                <tr>
                                    <td><strong>{{ $vacancy->position->name ?? 'Unknown' }}</strong></td>
                                    <td class="text-right">{{ $vacancy->num_of_vacancies }}</td>
                                    <td class="text-right">{{ number_format($vacancy->num_of_applicants) }}</td>
                                    <td class="text-right">{{ number_format($vacancy->num_of_shortlisted) }}</td>
                                    <td>{{ $vacancy->interview_status ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $vacancy->selected_candidate ? 'Yes (' . $vacancy->selected_candidate . ')' : 'No' }}</td>
                                    <td class="text-right">
                                        @if($offered)
                                            <span class="capacity-badge ok">{{ $vacancy->offer_status }}</span>
                                        @else
                                            <span class="capacity-badge muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        @if($vacancy->actual_reporting_date)
                                            <span class="capacity-badge info">{{ $vacancy->actual_reporting_date->format('Y-m-d') }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">No vacancy records for this branch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <div class="capacity-empty-state">
                        <i class="fa fa-file-invoice"></i>
                        <p class="mb-0">No vacancy records found for this branch.</p>
                    </div>
                @endif
            @else
                <div class="capacity-empty-state">
                    <i class="fa fa-building-o"></i>
                    <p class="mb-0">Select a branch to view its recruitment pipeline.</p>
                </div>
            @endif
        </div>
        {{-- ==================================================================
              VACANCY REGISTER — every vacancy with its recruitment tracking
         =================================================================== --}}
        <div class="capacity-section {{ $activeTab === 'vacancies' ? 'active' : '' }}" id="vacancies" role="tabpanel">
            <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                <i class="fa fa-briefcase"></i> Vacancy Register &mdash; {{ $selectedOffice->name ?? 'No branch selected' }}
            </h5>

            <div style="margin-bottom: 1rem; text-align: right;">
                <button type="button" class="btn btn-primary"
                        data-vacancy-create="{{ json_encode([
                            'office_id' => $selectedOfficeId,
                            'position_id' => '',
                            'num_of_vacancies' => '',
                            'date_arose' => now()->format('Y-m-d'),
                            'recruitment_status' => 'Open',
                        ]) }}">
                    <i class="fa fa-plus"></i> Log vacancy record
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped capacity-table">
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th class="text-right">No.</th>
                            <th>Date Arose</th>
                            <th>Reason</th>
                            <th class="text-right">Days Vacant</th>
                            <th>Recruitment</th>
                            <th class="text-right">Applicants</th>
                            <th class="text-right">Shortlisted</th>
                            <th>Interview</th>
                            <th>Selected Candidate</th>
                            <th>Offer</th>
                            <th>Expected Report</th>
                            <th>Actual Report</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branchVacancies as $vacancy)
                            @php
                                $recruitStatus = $vacancy->recruitment_status ?: $vacancy->status;
                                $recruitBadge = $vacancy->is_filled ? 'ok' : ($recruitStatus === 'Open' ? 'critical' : 'warn');
                            @endphp
                            <tr>
                                <td><strong>{{ $vacancy->position->name ?? 'Unknown position' }}</strong></td>
                                <td class="text-right">{{ $vacancy->num_of_vacancies }}</td>
                                <td>{{ $vacancy->date_arose ? $vacancy->date_arose->format('d M Y') : '—' }}</td>
                                <td>{{ $vacancy->reason ?: '—' }}</td>
                                <td class="text-right">
                                    @if($vacancy->days_vacant !== null)
                                        <span class="capacity-badge {{ $vacancy->is_open ? ($vacancy->days_vacant > 60 ? 'critical' : 'warn') : 'ok' }}">
                                            {{ $vacancy->days_vacant }}
                                        </span>
                                    @else
                                        <span class="text-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td><span class="capacity-badge {{ $recruitBadge }}">{{ $recruitStatus }}</span></td>
                                <td class="text-right">{{ $vacancy->num_of_applicants }}</td>
                                <td class="text-right">{{ $vacancy->num_of_shortlisted }}</td>
                                <td>{{ $vacancy->interview_status ?: '—' }}</td>
                                <td>{{ $vacancy->selected_candidate ?: '—' }}</td>
                                <td>{{ $vacancy->offer_status ?: '—' }}</td>
                                <td>{{ $vacancy->expected_reporting_date ? $vacancy->expected_reporting_date->format('d M Y') : '—' }}</td>
                                <td>{{ $vacancy->actual_reporting_date ? $vacancy->actual_reporting_date->format('d M Y') : '—' }}</td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-info"
                                            data-vacancy-edit="{{ json_encode($vacancyModalPayload($vacancy)) }}">
                                        <i class="fa fa-pencil"></i> Update
                                    </button>
                                    <form method="POST" action="{{ route('goa.branch-vacancy.destroy', $vacancy->id) }}"
                                          style="display: inline;" onsubmit="return confirm('Remove this vacancy record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="14" class="text-center text-muted">No vacancy records captured for this branch yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{-- ==================================================================
              CAPACITY SETUP — total approved headcount for the branch
        =================================================================== --}}
        <div class="capacity-section {{ $activeTab === 'setup' ? 'active' : '' }}" id="setup" role="tabpanel">
            <h5 class="capacity-section-title" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05);">
                <i class="fa fa-sliders"></i> Approved Staffing Capacity &mdash; {{ $selectedOffice->name ?? 'No branch selected' }}
            </h5>

            @if(!$selectedOffice)
                <div class="capacity-empty-state">
                    <i class="fa fa-building-o"></i>
                    <p class="mb-0">Select a branch before setting up its staffing structure.</p>
                </div>
            @else
                <p class="text-muted">
                    Set the total approved staffing capacity (headcount) for this branch. The system compares it against the
                    personnel currently attached to the branch to calculate the vacancy and the staffing percentage.
                </p>

                <form method="POST" action="{{ route('goa.branch-capacity.store') }}">
                    @csrf
                    <input type="hidden" name="office_id" value="{{ $selectedOffice->id }}">

                    <div class="form-group">
                        <label for="branchCapacity">Approved Capacity (Total Headcount)</label>
                        <input type="number" min="0" max="10000" class="form-control"
                               id="branchCapacity" name="branch_capacity"
                               value="{{ old('branch_capacity', $selectedOffice->branch_capacity ?? 0) }}"
                               required>
                    </div>

                    <div style="text-align: right;">
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-save"></i> Save approved capacity
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
    {{-- /.capacity-content-container --}}

    <div class="capacity-modal-backdrop" id="capacityModalBackdrop"></div>
    <div class="capacity-modal" id="vacancyModal">
        <div class="capacity-modal-card">
            <div class="capacity-modal-header">
                <h4 id="vacancyModalTitle">Log Vacancy Record</h4>
                <button type="button" class="capacity-modal-close" data-vacancy-modal-close="1">&times;</button>
            </div>
            <div class="capacity-modal-body">
                <form id="vacancyForm" method="POST" action="{{ route('goa.branch-vacancy.store') }}">
                    @csrf
                    <input type="hidden" name="vacancy_id" id="vacancyId" value="{{ old('vacancy_id') }}">
                    <input type="hidden" name="_method" id="vacancyFormMethod" value="{{ old('_method', 'POST') }}">

                    <div class="capacity-modal-grid">
                        <div class="capacity-field">
                            <label for="vacancyOffice">Branch</label>
                            <select class="form-control" id="vacancyOffice" name="office_id" required>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}"
                                            {{ (int) old('office_id', $selectedOfficeId) === (int) $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyPosition">Position</label>
                            <select class="form-control" id="vacancyPosition" name="position_id" required>
                                <option value="">-- Select Position --</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}"
                                            {{ (int) old('position_id') === (int) $position->id ? 'selected' : '' }}>
                                        {{ $position->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyCount">Number of Vacancies</label>
                            <input type="number" min="0" class="form-control" id="vacancyCount" name="num_of_vacancies"
                                   value="{{ old('num_of_vacancies') }}" placeholder="0">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyDateArose">Date Vacancy Arose</label>
                            <input type="date" class="form-control" id="vacancyDateArose" name="date_arose"
                                   value="{{ old('date_arose') }}">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyReason">Reason for Vacancy</label>
                            <select class="form-control" id="vacancyReason" name="reason">
                                <option value="" disabled {{ old('reason') ? '' : 'selected' }}>-- Select Reason --</option>
                                @php
                                    $vacancyReasons = [
                                        'New Position'                  => 'New Position – Newly created position due to business growth or expansion.',
                                        'Employee Resignation'          => 'Employee Resignation – Previous employee resigned.',
                                        'Employee Termination'          => 'Employee Termination – Previous employee was terminated.',
                                        'Employee Retirement'           => 'Employee Retirement – Previous employee retired.',
                                        'Employee Transfer'             => 'Employee Transfer – Previous employee transferred to another branch or department.',
                                        'Promotion'                     => 'Promotion – Previous employee was promoted to another position.',
                                        'Internal Transfer'             => 'Internal Transfer – Position became vacant due to an internal movement.',
                                        'Employee Death'                => 'Employee Death – Position became vacant following the death of the employee.',
                                        'Contract Expired'              => 'Contract Expired – Previous employee\'s contract ended.',
                                        'Replacement'                   => 'Replacement – Vacancy created to replace an existing employee.',
                                        'Branch Expansion'              => 'Branch Expansion – Additional staff required due to branch expansion.',
                                        'Increased Workload'            => 'Increased Workload – Additional staff required because of increased workload.',
                                        'New Branch/Office'             => 'New Branch/Office – Staff required for a newly opened branch or office.',
                                        'Organizational Restructuring'  => 'Organizational Restructuring – Vacancy created following organizational changes.',
                                        'Temporary Vacancy'             => 'Temporary Vacancy – Position temporarily vacant due to leave or absence.',
                                        'Maternity/Parental Leave'      => 'Maternity/Parental Leave – Temporary replacement required.',
                                        'Long-Term Leave'               => 'Long-Term Leave – Temporary replacement required for an employee on extended leave.',
                                        'Skills Gap'                    => 'Skills Gap – Additional employee required to address a skills shortage.',
                                        'Staffing Adjustment'           => 'Staffing Adjustment – Position required to bring staffing levels in line with approved capacity.',
                                        'Other'                         => 'Other – Reason not covered by the available options.',
                                    ];
                                @endphp
                                @foreach($vacancyReasons as $value => $label)
                                    <option value="{{ $value }}" {{ old('reason') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyRecruitmentStatus">Recruitment Status</label>
                            <select class="form-control" id="vacancyRecruitmentStatus" name="recruitment_status">
                                @foreach($recruitmentStatuses as $status)
                                    <option value="{{ $status }}" {{ old('recruitment_status') === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyApplicants">Number of Applicants</label>
                            <input type="number" min="0" class="form-control" id="vacancyApplicants" name="num_of_applicants"
                                   value="{{ old('num_of_applicants') }}" placeholder="0">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyShortlisted">Number Shortlisted</label>
                            <input type="number" min="0" class="form-control" id="vacancyShortlisted" name="num_of_shortlisted"
                                   value="{{ old('num_of_shortlisted') }}" placeholder="0">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyInterviewStatus">Interview Status</label>
                            <select class="form-control" id="vacancyInterviewStatus" name="interview_status">
                                <option value="">-- Select --</option>
                                @foreach($interviewStatuses as $status)
                                    <option value="{{ $status }}" {{ old('interview_status') === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyCandidate">Selected Candidate</label>
                            <input type="text" class="form-control" id="vacancyCandidate" name="selected_candidate"
                                   value="{{ old('selected_candidate') }}" placeholder="Full name">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyOfferStatus">Offer Status</label>
                            <select class="form-control" id="vacancyOfferStatus" name="offer_status">
                                <option value="">-- Select --</option>
                                @foreach($offerStatuses as $status)
                                    <option value="{{ $status }}" {{ old('offer_status') === $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyExpectedDate">Expected Reporting Date</label>
                            <input type="date" class="form-control" id="vacancyExpectedDate" name="expected_reporting_date"
                                   value="{{ old('expected_reporting_date') }}">
                        </div>
                        <div class="capacity-field">
                            <label for="vacancyActualDate">Actual Reporting Date</label>
                            <input type="date" class="form-control" id="vacancyActualDate" name="actual_reporting_date"
                                   value="{{ old('actual_reporting_date') }}">
                        </div>
                        <div class="capacity-field capacity-field-wide">
                            <label for="vacancyNotes">Notes</label>
                            <textarea class="form-control" id="vacancyNotes" name="notes" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="capacity-modal-footer">
                <button type="button" class="btn btn-default" data-vacancy-modal-close="1">Cancel</button>
                <button type="submit" class="btn btn-primary" form="vacancyForm">
                    <i class="fa fa-save"></i> Save Vacancy Record
                </button>
            </div>
        </div>
    </div>
    {{-- Assign Position Modal --}}
    <div class="capacity-modal" id="assignPositionModal">
        <div class="capacity-modal-card" style="max-width: 480px;">
            <div class="capacity-modal-header">
                <h4 id="assignPositionModalTitle">Assign Position</h4>
                <button type="button" class="capacity-modal-close" data-assign-position-modal-close="1">&times;</button>
            </div>
            <div class="capacity-modal-body">
                <form id="assignPositionForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="user_id" id="assignPositionUserId">
                    <div class="capacity-field">
                        <label for="assignPositionSelect">Position</label>
                        <select class="form-control" id="assignPositionSelect" name="position_id" required>
                            <option value="">-- Select Position --</option>
                            @foreach($positions as $position)
                                <option value="{{ $position->id }}">{{ $position->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="capacity-modal-footer">
                <button type="button" class="btn btn-default" data-assign-position-modal-close="1">Cancel</button>
                <button type="submit" class="btn btn-primary" form="assignPositionForm">
                    <i class="fa fa-save"></i> Save
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            // ── Assign-position modal ──────────────────────────────────────
            var assignModal     = document.getElementById('assignPositionModal');
            var assignTitle     = document.getElementById('assignPositionModalTitle');
            var assignForm      = document.getElementById('assignPositionForm');
            var assignUserIdEl  = document.getElementById('assignPositionUserId');
            var assignSelect    = document.getElementById('assignPositionSelect');
            var assignBaseUrl   = '{{ route('goa.personnel.assign-position', ['user' => '__USER_ID__']) }}';

            function openAssignModal(data) {
                assignUserIdEl.value = data.user_id;
                assignTitle.textContent = 'Assign Position — ' + (data.name || '');
                assignSelect.value = '';
                assignForm.setAttribute('action', assignBaseUrl.replace('__USER_ID__', data.user_id));
                document.getElementById('capacityModalBackdrop').classList.add('open');
                assignModal.classList.add('open');
            }

            function closeAssignModal() {
                document.getElementById('capacityModalBackdrop').classList.remove('open');
                assignModal.classList.remove('open');
            }

            // ── End assign-position modal ──────────────────────────────────

            var filterTab = document.getElementById('capacityFilterTab');
            var modalBackdrop = document.getElementById('capacityModalBackdrop');
            var vacancyModal = document.getElementById('vacancyModal');
            var vacancyForm = document.getElementById('vacancyForm');
            var vacancyMethod = document.getElementById('vacancyFormMethod');
            var vacancyId = document.getElementById('vacancyId');
            var vacancyTitle = document.getElementById('vacancyModalTitle');
            var updateUrl = '{{ route('goa.branch-vacancy.update', ['id' => '__VACANCY_ID__']) }}';
            var storeUrl = '{{ route('goa.branch-vacancy.store') }}';

            var vacancyFields = [
                'office_id', 'position_id', 'num_of_vacancies', 'date_arose', 'reason', 'recruitment_status',
                'num_of_applicants', 'num_of_shortlisted', 'interview_status', 'selected_candidate', 'offer_status',
                'expected_reporting_date', 'actual_reporting_date', 'notes'
            ];

            // Tabs — the active tab is remembered so it survives a branch change
            document.querySelectorAll('.capacity-nav-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    var sectionId = this.getAttribute('data-section');

                    document.querySelectorAll('.capacity-nav-btn').forEach(function (btn) {
                        btn.classList.remove('active');
                    });
                    document.querySelectorAll('.capacity-section').forEach(function (section) {
                        section.classList.remove('active');
                    });

                    this.classList.add('active');
                    var target = document.getElementById(sectionId);
                    if (target) {
                        target.classList.add('active');
                    }
                    if (filterTab) {
                        filterTab.value = sectionId;
                    }
                });
            });

            function closest(element, selector) {
                return (element && element.closest) ? element.closest(selector) : null;
            }

            function parsePayload(value) {
                try {
                    return JSON.parse(value || '{}');
                } catch (error) {
                    return {};
                }
            }

            function fillVacancyForm(data) {
                vacancyFields.forEach(function (field) {
                    var input = vacancyForm.querySelector('[name="' + field + '"]');
                    if (!input) {
                        return;
                    }
                    input.value = (data && data[field] !== null && typeof data[field] !== 'undefined') ? data[field] : '';
                });
            }

            function openVacancyModal(mode, data) {
                data = data || {};

                if (mode === 'edit' && data.id) {
                    vacancyForm.setAttribute('action', updateUrl.replace('__VACANCY_ID__', data.id));
                    vacancyMethod.value = 'PUT';
                    vacancyId.value = data.id;
                    vacancyTitle.textContent = 'Update Vacancy Record';
                } else {
                    vacancyForm.setAttribute('action', storeUrl);
                    vacancyMethod.value = 'POST';
                    vacancyId.value = '';
                    vacancyTitle.textContent = 'Log Vacancy Record';
                }

                fillVacancyForm(data);
                modalBackdrop.classList.add('open');
                vacancyModal.classList.add('open');
            }

            function closeVacancyModal() {
                modalBackdrop.classList.remove('open');
                vacancyModal.classList.remove('open');
            }

            document.addEventListener('click', function (event) {
                var editButton = closest(event.target, '[data-vacancy-edit]');
                if (editButton) {
                    openVacancyModal('edit', parsePayload(editButton.getAttribute('data-vacancy-edit')));
                    return;
                }

                var createButton = closest(event.target, '[data-vacancy-create]');
                if (createButton) {
                    openVacancyModal('create', parsePayload(createButton.getAttribute('data-vacancy-create')));
                    return;
                }

                var personnelToggle = closest(event.target, '[data-personnel-toggle]');
                if (personnelToggle) {
                    var panel = document.getElementById(personnelToggle.getAttribute('data-personnel-toggle'));
                    if (panel) {
                        panel.classList.toggle('open');
                    }
                    return;
                }

                if (closest(event.target, '[data-vacancy-modal-close]')) {
                    closeVacancyModal();
                }

                var assignBtn = closest(event.target, '[data-assign-position]');
                if (assignBtn) {
                    openAssignModal(parsePayload(assignBtn.getAttribute('data-assign-position')));
                    return;
                }

                if (closest(event.target, '[data-assign-position-modal-close]')) {
                    closeAssignModal();
                }
            });

            modalBackdrop.addEventListener('click', function () {
                closeVacancyModal();
                closeAssignModal();
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeVacancyModal();
                    closeAssignModal();
                }
            });

            @if($errors->any())
                // Validation failed — reopen the modal (in edit mode when an update was submitted)
                openVacancyModal('{{ old('_method') === 'PUT' ? 'edit' : 'create' }}',
                    Object.assign({}, @json(old()), { id: @json(old('vacancy_id')) }));
            @endif
        })();
    </script>
    {{-- /.container-fluid --}}
</div>
@endsection


