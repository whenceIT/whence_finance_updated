@extends('layouts.master')

@section('content')
<style>
    /* Policy Dashboard - Bento Grid Minimalist UI - Page Specific Styles */
    .policy-dashboard-page {
        /* Font Size Variables - Larger/Readable Sizes */
        --pd-font-xs: 1rem;
        --pd-font-sm: 1.125rem;
        --pd-font-base: 1.375rem;
        --pd-font-md: 1.5rem;
        --pd-font-lg: 1.75rem;
        --pd-font-xl: 2rem;
        --pd-font-2xl: 2.25rem;
        --pd-font-3xl: 2.75rem;
        --pd-font-4xl: 3.5rem;

        /* Color Variables */
        --pd-primary: #6366f1;
        --pd-primary-dark: #4f46e5;
        --pd-secondary: #8b5cf6;
        --pd-success: #10b981;
        --pd-warning: #f59e0b;
        --pd-danger: #ef4444;
        --pd-info: #3b82f6;
        --pd-dark: #1e293b;
        --pd-gray-50: #f8fafc;
        --pd-gray-100: #f1f5f9;
        --pd-gray-200: #e2e8f0;
        --pd-gray-300: #cbd5e1;
        --pd-gray-400: #94a3b8;
        --pd-gray-500: #64748b;
        --pd-gray-600: #475569;
        --pd-gray-700: #334155;
        --pd-gray-800: #1e293b;
        --pd-gray-900: #0f172a;
    }

    .policy-dashboard-page .policy-dashboard {
        max-width: 1600px;
        margin: 0 auto;
        padding: 1.5rem;
        background: var(--pd-gray-50);
        min-height: calc(100vh - 100px);
        font-size: var(--pd-font-base);
    }

    /* Header */
    .policy-dashboard-page .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .policy-dashboard-page .header-left h1 {
        font-size: var(--pd-font-3xl);
        font-weight: 700;
        color: var(--pd-gray-800);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .policy-dashboard-page .header-left h1 i {
        color: var(--pd-primary);
    }

    .policy-dashboard-page .header-left p {
        color: var(--pd-gray-500);
        margin: 0.25rem 0 0 0;
        font-size: var(--pd-font-base);
    }

    .policy-dashboard-page .header-actions {
        display: flex;
        gap: 0.75rem;
    }

    .policy-dashboard-page .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        font-size: var(--pd-font-base);
        font-weight: 500;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .policy-dashboard-page .btn-primary {
        background: var(--pd-primary);
        color: white;
    }

    .policy-dashboard-page .btn-primary:hover {
        background: var(--pd-primary-dark);
        transform: translateY(-1px);
    }

    .policy-dashboard-page .btn-secondary {
        background: white;
        color: var(--pd-gray-700);
        border: 1px solid var(--pd-gray-200);
    }

    .policy-dashboard-page .btn-secondary:hover {
        background: var(--pd-gray-50);
        border-color: var(--pd-gray-300);
    }

    /* Bento Grid */
    .policy-dashboard-page .bento-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        grid-template-rows: auto;
        gap: 1rem;
    }

    /* Card Base */
    .policy-dashboard-page .bento-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--pd-gray-100);
        transition: all 0.3s ease;
    }

    .policy-dashboard-page .bento-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .policy-dashboard-page .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .policy-dashboard-page .card-title {
        font-size: var(--pd-font-base);
        font-weight: 600;
        color: var(--pd-gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .policy-dashboard-page .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--pd-font-md);
    }

    .policy-dashboard-page .card-value {
        font-size: var(--pd-font-4xl);
        font-weight: 700;
        color: var(--pd-gray-800);
        line-height: 1;
    }

    .policy-dashboard-page .card-label {
        font-size: var(--pd-font-sm);
        color: var(--pd-gray-400);
        margin-top: 0.25rem;
    }

    .policy-dashboard-page .card-trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: var(--pd-font-sm);
        font-weight: 500;
        margin-top: 0.5rem;
        padding: 0.25rem 0.5rem;
        border-radius: 20px;
    }

    .policy-dashboard-page .trend-up {
        background: #d1fae5;
        color: #059669;
    }

    .policy-dashboard-page .trend-down {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Grid Spans */
    .policy-dashboard-page .span-3 { grid-column: span 3; }
    .policy-dashboard-page .span-4 { grid-column: span 4; }
    .policy-dashboard-page .span-6 { grid-column: span 6; }
    .policy-dashboard-page .span-8 { grid-column: span 8; }
    .policy-dashboard-page .span-12 { grid-column: span 12; }

    /* Stat Cards Colors */
    .policy-dashboard-page .stat-primary { background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%); }
    .policy-dashboard-page .stat-success { background: linear-gradient(135deg, #10b981 0%, #34d399 100%); }
    .policy-dashboard-page .stat-warning { background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%); }
    .policy-dashboard-page .stat-danger { background: linear-gradient(135deg, #ef4444 0%, #f87171 100%); }
    .policy-dashboard-page .stat-info { background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%); }

    .policy-dashboard-page .stat-card {
        color: white;
    }

    .policy-dashboard-page .stat-card .card-icon {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .policy-dashboard-page .stat-card .card-value,
    .policy-dashboard-page .stat-card .card-title,
    .policy-dashboard-page .stat-card .card-label {
        color: white;
    }

    .policy-dashboard-page .stat-card .card-label {
        opacity: 0.9;
    }

    /* Quick Actions */
    .policy-dashboard-page .quick-actions-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .policy-dashboard-page .action-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        background: var(--pd-gray-50);
        border-radius: 10px;
        text-decoration: none;
        color: var(--pd-gray-700);
        transition: all 0.2s ease;
    }

    .policy-dashboard-page .action-item:hover {
        background: var(--pd-primary);
        color: white;
        transform: translateX(4px);
    }

    .policy-dashboard-page .action-item i {
        font-size: var(--pd-font-lg);
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border-radius: 8px;
        color: var(--pd-primary);
    }

    .policy-dashboard-page .action-item:hover i {
        background: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .policy-dashboard-page .action-text {
        font-size: var(--pd-font-base);
        font-weight: 500;
    }

    /* Recent Activity */
    .policy-dashboard-page .activity-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .policy-dashboard-page .activity-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.875rem;
        background: var(--pd-gray-50);
        border-radius: 10px;
    }

    .policy-dashboard-page .activity-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--pd-primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--pd-font-sm);
        font-weight: 600;
    }

    .policy-dashboard-page .activity-content {
        flex: 1;
        min-width: 0;
    }

    .policy-dashboard-page .activity-title {
        font-size: var(--pd-font-base);
        font-weight: 500;
        color: var(--pd-gray-800);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .policy-dashboard-page .activity-meta {
        font-size: var(--pd-font-sm);
        color: var(--pd-gray-400);
    }

    .policy-dashboard-page .activity-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 20px;
        font-size: var(--pd-font-xs);
        font-weight: 600;
        text-transform: uppercase;
    }

    .policy-dashboard-page .badge-acknowledged {
        background: #d1fae5;
        color: #059669;
    }

    .policy-dashboard-page .badge-pending {
        background: #fef3c7;
        color: #d97706;
    }

    .policy-dashboard-page .badge-declined {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Policy List */
    .policy-dashboard-page .policy-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .policy-dashboard-page .policy-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.875rem 1rem;
        background: var(--pd-gray-50);
        border-radius: 10px;
        transition: all 0.2s ease;
    }

    .policy-dashboard-page .policy-item:hover {
        background: var(--pd-gray-100);
    }

    .policy-dashboard-page .policy-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .policy-dashboard-page .policy-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: var(--pd-font-base);
    }

    .policy-dashboard-page .policy-title {
        font-size: var(--pd-font-base);
        font-weight: 500;
        color: var(--pd-gray-800);
    }

    .policy-dashboard-page .policy-category {
        font-size: var(--pd-font-xs);
        color: var(--pd-gray-400);
    }

    .policy-dashboard-page .policy-status {
        display: flex;
        align-items: center;
        gap: 0.375rem;
        font-size: var(--pd-font-sm);
        font-weight: 500;
    }

    .policy-dashboard-page .status-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

    .policy-dashboard-page .status-active { background: #10b981; }
    .policy-dashboard-page .status-inactive { background: #94a3b8; }

    /* Categories */
    .policy-dashboard-page .category-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .policy-dashboard-page .category-item {
        padding: 1.25rem;
        background: var(--pd-gray-50);
        border-radius: 12px;
        text-align: center;
        transition: all 0.2s ease;
    }

    .policy-dashboard-page .category-item:hover {
        background: var(--pd-primary);
        color: white;
    }

    .policy-dashboard-page .category-item:hover .category-count {
        color: white;
    }

    .policy-dashboard-page .category-icon {
        font-size: var(--pd-font-2xl);
        margin-bottom: 0.5rem;
    }

    .policy-dashboard-page .category-name {
        font-size: var(--pd-font-base);
        font-weight: 500;
        margin-bottom: 0.25rem;
    }

    .policy-dashboard-page .category-count {
        font-size: var(--pd-font-xl);
        font-weight: 700;
        color: var(--pd-primary);
    }

    /* Progress Bars */
    .policy-dashboard-page .progress-section {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .policy-dashboard-page .progress-item {
        display: flex;
        flex-direction: column;
        gap: 0.375rem;
    }

    .policy-dashboard-page .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .policy-dashboard-page .progress-label {
        font-size: var(--pd-font-base);
        font-weight: 500;
        color: var(--pd-gray-700);
    }

    .policy-dashboard-page .progress-value {
        font-size: var(--pd-font-sm);
        font-weight: 600;
        color: var(--pd-gray-500);
    }

    .policy-dashboard-page .progress-bar {
        height: 10px;
        background: var(--pd-gray-100);
        border-radius: 4px;
        overflow: hidden;
    }

    .policy-dashboard-page .progress-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.5s ease;
    }

    .policy-dashboard-page .fill-primary { background: var(--pd-primary); }
    .policy-dashboard-page .fill-success { background: var(--pd-success); }
    .policy-dashboard-page .fill-warning { background: var(--pd-warning); }
    .policy-dashboard-page .fill-danger { background: var(--pd-danger); }

    /* Responsive */
    @media (max-width: 1200px) {
        .policy-dashboard-page .span-3 { grid-column: span 6; }
        .policy-dashboard-page .span-4 { grid-column: span 6; }
        .policy-dashboard-page .span-6 { grid-column: span 12; }
        .policy-dashboard-page .span-8 { grid-column: span 12; }
    }

    @media (max-width: 768px) {
        .policy-dashboard-page .bento-grid {
            grid-template-columns: 1fr;
        }
        
        .policy-dashboard-page .span-3,
        .policy-dashboard-page .span-4,
        .policy-dashboard-page .span-6,
        .policy-dashboard-page .span-8,
        .policy-dashboard-page .span-12 {
            grid-column: span 1;
        }

        .policy-dashboard-page .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .policy-dashboard-page .header-actions {
            width: 100%;
        }

        .policy-dashboard-page .header-actions .btn {
            flex: 1;
            justify-content: center;
        }
    }

    /* Empty State */
    .policy-dashboard-page .empty-state {
        text-align: center;
        padding: 2rem;
        color: var(--pd-gray-400);
    }

    .policy-dashboard-page .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .policy-dashboard-page .empty-state p {
        font-size: var(--pd-font-base);
    }
</style>

<div class="policy-dashboard-page">
<div class="policy-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-left">
            <h1>
                <i class="fa fa-shield-alt"></i>
                Policy Management
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
        <div class="bento-card span-3 stat-danger">
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

        <!-- Quick Actions -->
        <div class="bento-card span-4">
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
        <div class="bento-card span-4">
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
                        <span class="progress-label">Acknowledged</span>
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
        <div class="bento-card span-4">
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
            @if($recentResponses->count() > 0)
            <div class="activity-list">
                @foreach($recentResponses as $response)
                <div class="activity-item">
                    <div class="activity-avatar">
                        {{ substr($response->user->first_name ?? 'U', 0, 1) }}{{ substr($response->user->last_name ?? '', 0, 1) }}
                    </div>
                    <div class="activity-content">
                        <div class="activity-title">{{ $response->user->first_name ?? 'Unknown' }} {{ $response->user->last_name ?? '' }}</div>
                        <div class="activity-meta">{{ $response->policy->title ?? 'Unknown Policy' }}</div>
                    </div>
                    <span class="activity-badge badge-{{ $response->status }}">
                        {{ ucfirst($response->status) }}
                    </span>
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
@endsection