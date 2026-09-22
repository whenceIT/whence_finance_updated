@extends('layouts.master')
@section('title')
    GOA Manager - Recruitment Pipeline
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p class="lead">Track and visualize the recruitment process across all branches and positions.</p>
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

    <style>
        .pipeline-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pipeline-funnel {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .funnel-stage {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .funnel-stage-label {
            width: 150px;
            font-weight: 600;
            color: #475569;
            font-size: 0.95rem;
        }

        .funnel-bar-container {
            flex: 1;
            background: #f1f5f9;
            border-radius: 8px;
            height: 32px;
            position: relative;
            overflow: hidden;
        }

        .funnel-bar {
            height: 100%;
            border-radius: 8px;
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .funnel-count {
            width: 80px;
            text-align: right;
            font-weight: 600;
            color: #111827;
        }

        .stage-vacancies { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stage-applicants { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); }
        .stage-shortlisted { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stage-interviewed { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stage-selected { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
        .stage-offers { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
        .stage-reported { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); }

        .vacancy-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #ffffff;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .vacancy-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .vacancy-card-title {
            margin: 0;
            font-weight: 600;
            color: #111827;
        }

        .vacancy-card-meta {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .pipeline-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: #ffffff;
            border-left: 4px solid #3b82f6;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat-label {
            font-size: 0.85rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: #111827;
        }

        .stat-trend {
            font-size: 0.8rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .stage-badge {
            display: inline-block;
            padding: 0.25rem 0.6rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-vacancy { background: #e0e7ff; color: #3730a3; }
        .badge-applicant { background: #dbeafe; color: #1e40af; }
        .badge-shortlist { background: #dcfce7; color: #166534; }
        .badge-interview { background: #ede9fe; color: #4c1d95; }
        .badge-selected { background: #fef3c7; color: #92400e; }
        .badge-offer { background: #fce7f3; color: #831843; }
        .badge-reported { background: #ccfbf1; color: #134e4a; }
    </style>

    {{-- Filter by Branch --}}
    <form method="GET" action="{{ route('goa.recruitment-pipeline') }}" class="mb-4" style="background: #ffffff; padding: 1rem; border-radius: 12px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <div class="form-group" style="display: inline-block; min-width: 280px;">
            <label for="pipelineBranchFilter"><i class="fa fa-building"></i> Branch</label>
            <select class="form-control" id="pipelineBranchFilter" name="office_id"
                    onchange="document.getElementById('recruitmentPipelineForm').submit();">
                <option value="">All Branches</option>
                @foreach($offices as $office)
                    <option value="{{ $office->id }}" {{ (int) old('office_id', 0) === (int) $office->id ? 'selected' : '' }}>
                        {{ $office->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="tab" id="pipelineFilterTab" value="{{ $activeTab ?? '' }}">
        <noscript><button type="submit" class="btn btn-primary" style="margin-left: 1rem;"><i class="fa fa-filter"></i> Apply</button></noscript>
    </form>

    {{-- Pipeline Funnel --}}
    <div class="pipeline-container">
        <h5 class="mb-4"><i class="fa fa-project-diagram"></i> Recruitment Funnel</h5>

        @php
            $maxValue = max(
                $pipelineStats['vacancies'],
                $pipelineStats['applicants'],
                1
            );
        @endphp

        <div class="pipeline-funnel">
            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-building"></i> Vacancies</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-vacancies"
                         style="width: {{ $pipelineStats['vacancies'] > 0 ? ($pipelineStats['vacancies'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['vacancies'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['vacancies'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-user-plus"></i> Applicants</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-applicants"
                         style="width: {{ $pipelineStats['applicants'] > 0 ? ($pipelineStats['applicants'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['applicants'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['applicants'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-list"></i> Shortlisted</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-shortlisted"
                         style="width: {{ $pipelineStats['shortlisted'] > 0 ? ($pipelineStats['shortlisted'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['shortlisted'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['shortlisted'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-user-check"></i> Interviewed</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-interviewed"
                         style="width: {{ $pipelineStats['interviewed'] > 0 ? ($pipelineStats['interviewed'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['interviewed'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['interviewed'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-check-circle"></i> Selected</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-selected"
                         style="width: {{ $pipelineStats['selected'] > 0 ? ($pipelineStats['selected'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['selected'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['selected'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-handshake"></i> Offers Issued</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-offers"
                         style="width: {{ $pipelineStats['offers_issued'] > 0 ? ($pipelineStats['offers_issued'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['offers_issued'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['offers_issued'] }}</div>
            </div>

            <div class="funnel-stage">
                <div class="funnel-stage-label"><i class="fa fa-user"></i> Reported</div>
                <div class="funnel-bar-container">
                    <div class="funnel-bar stage-reported"
                         style="width: {{ $pipelineStats['reported'] > 0 ? ($pipelineStats['reported'] / $maxValue) * 100 : 0 }}%;">
                        {{ $pipelineStats['reported'] }}
                    </div>
                </div>
                <div class="funnel-count">{{ $pipelineStats['reported'] }}</div>
            </div>
        </div>
    </div>

    {{-- Pipeline Summary Cards --}}
    <div class="pipeline-stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total Vacancies</div>
            <div class="stat-value">{{ number_format($pipelineStats['vacancies']) }}</div>
            <div class="stat-trend">{{ $selectedOffice ? $selectedOffice->name : 'All branches' }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Total Applicants</div>
            <div class="stat-value">{{ number_format($pipelineStats['applicants']) }}</div>
            <div class="stat-trend">Applications received</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Shortlisted</div>
            <div class="stat-value">{{ number_format($pipelineStats['shortlisted']) }}</div>
            <div class="stat-trend">{{ $pipelineStats['total_applicants'] > 0 ? round(($pipelineStats['shortlisted'] / $pipelineStats['total_applicants']) * 100, 1) : 0 }}% of applicants</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Selected Candidates</div>
            <div class="stat-value">{{ number_format($pipelineStats['selected']) }}</div>
            <div class="stat-trend">{{ $pipelineStats['shortlisted'] > 0 ? round(($pipelineStats['selected'] / $pipelineStats['shortlisted']) * 100, 1) : 0 }}% conversion rate</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Offers Issued</div>
            <div class="stat-value">{{ number_format($pipelineStats['offers_issued']) }}</div>
            <div class="stat-trend">{{ $pipelineStats['selected'] > 0 ? round(($pipelineStats['offers_issued'] / $pipelineStats['selected']) * 100, 1) : 0 }}% of selected</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Reported</div>
            <div class="stat-value">{{ number_format($pipelineStats['reported']) }}</div>
            <div class="stat-trend">{{ $pipelineStats['offers_issued'] > 0 ? round(($pipelineStats['reported'] / $pipelineStats['offers_issued']) * 100, 1) : 0 }}% accepted offers</div>
        </div>
    </div>

    {{-- Individual Vacancy Details --}}
    <h5 class="mb-3"><i class="fa fa-list-alt"></i> Vacancy Details by Branch</h5>

    @forelse($vacanciesWithPipeline as $item)
        @php
            $vacancy = $item['vacancy'];
            $pipeline = $item['pipeline'];
            $conversion = $item['conversion_rates'];
        @endphp

        <div class="vacancy-card">
            <div class="vacancy-card-header">
                <div>
                    <h6 class="vacancy-card-title">{{ $vacancy->position->name ?? 'Unknown Position' }}</h6>
                    <div class="vacancy-card-meta">
                        Branch: {{ $vacancy->office->name ?? 'No branch' }} |
                        District: {{ $vacancy->office->district->name ?? 'N/A' }} |
                        Date Opened: {{ $vacancy->date_arose ? $vacancy->date_arose->format('d M Y') : 'N/A' }}
                    </div>
                </div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <span class="stage-badge badge-vacancy">{{ $pipeline['vacancies'] }} vacancy{{ $pipeline['vacancies'] > 1 ? 'ies' : 'y' }}</span>
                    <span class="stage-badge badge-applicant">{{ $pipeline['applicants'] }} applicants</span>
                    <span class="stage-badge badge-shortlist">{{ $pipeline['shortlisted'] }} shortlisted</span>
                    <span class="stage-badge badge-interview">{{ $pipeline['interviewed'] }} interviewed</span>
                    <span class="stage-badge badge-selected">{{ $pipeline['selected'] }} selected</span>
                    <span class="stage-badge badge-offer">{{ $pipeline['offers_issued'] }} offers</span>
                    <span class="stage-badge badge-reported">{{ $pipeline['reported'] }} reported</span>
                </div>
            </div>

            <div style="font-size: 0.9rem; color: #6b7280; margin-top: 0.5rem;">
                <strong>Conversion Funnel:</strong>
                {{ $conversion['application_to_shortlist'] }}% applicants → shortlisted,
                {{ $conversion['shortlist_to_interview'] }}% shortlisted → interviewed,
                {{ $conversion['interview_to_select'] }}% interviewed → selected,
                {{ $conversion['select_to_offer'] }}% selected → offer,
                {{ $conversion['offer_to_report'] }}% offers → reported
                @if($vacancy->actual_reporting_date)
                    <br><strong>Confirmed Hire:</strong> {{ $vacancy->selected_candidate }} reported on {{ $vacancy->actual_reporting_date->format('d M Y') }}
                @endif
            </div>
        </div>
    @empty
        <div class="alert alert-info" style="background: #f0fdf4; border-left: 4px solid #10b981;">
            <i class="fa fa-info-circle"></i> No vacancy records found for the selected branch.
            <a href="{{ route('goa.branch-staffing-capacity', ['tab' => 'vacancies']) }}" class="ml-2">View vacancies in Branch Staffing Capacity</a>
        </div>
    @endforelse

</div>
@endsection