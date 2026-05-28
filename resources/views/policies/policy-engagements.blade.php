@extends('layouts.master')

@section('content')
<style>
    :root {
        --pe-primary: #6366f1;
        --pe-primary-dark: #4f46e5;
        --pe-success: #10b981;
        --pe-warning: #f59e0b;
        --pe-danger: #ef4444;
        --pe-gray-50: #f8fafc;
        --pe-gray-100: #f1f5f9;
        --pe-gray-200: #e2e8f0;
        --pe-gray-700: #334155;
        --pe-gray-800: #1e293b;
    }

    .policy-engagements-page {
        max-width: 1400px;
        margin: 0 auto;
        padding: 1.5rem;
        background: var(--pe-gray-50);
        min-height: calc(100vh - 100px);
    }

    .pe-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pe-header h1 {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--pe-gray-800);
        margin: 0;
    }

    .pe-summary-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .pe-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--pe-gray-100);
    }

    .pe-card .pe-card-title {
        font-size: 1.125rem;
        color: var(--pe-gray-500);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.75rem;
    }

    .pe-card .pe-card-value {
        font-size: 2.25rem;
        font-weight: 700;
        color: var(--pe-gray-800);
    }

    .pe-card.primary {
        background: linear-gradient(135deg, var(--pe-primary) 0%, var(--pe-primary-dark) 100%);
        color: white;
    }

    .pe-card.primary .pe-card-title,
    .pe-card.primary .pe-card-value {
        color: white;
    }

    .pe-section {
        background: white;
        border-radius: 12px;
        padding: 1.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--pe-gray-100);
        margin-bottom: 1.5rem;
    }

    .pe-section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--pe-gray-800);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--pe-gray-200);
    }

    .pe-role-group {
        margin-bottom: 2.5rem;
    }

    .pe-role-header {
        background: var(--pe-gray-100);
        padding: 1.25rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .pe-role-name {
        font-size: 1.375rem;
        font-weight: 600;
        color: var(--pe-gray-800);
    }

    .pe-role-stats {
        display: flex;
        gap: 2rem;
        font-size: 1.125rem;
    }

    .pe-role-stats .pe-stat {
        text-align: center;
    }

    .pe-role-stats .pe-stat-value {
        font-weight: 700;
        color: var(--pe-primary);
        font-size: 1.25rem;
    }

    .pe-engagement-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 1.125rem;
    }

    .pe-engagement-table th,
    .pe-engagement-table td {
        padding: 1rem;
        text-align: left;
        border-bottom: 1px solid var(--pe-gray-200);
    }

    .pe-engagement-table th {
        background: var(--pe-gray-50);
        font-weight: 600;
        color: var(--pe-gray-700);
        text-transform: uppercase;
        font-size: 0.875rem;
        letter-spacing: 0.05em;
    }

    .pe-engagement-table tbody tr:hover {
        background: var(--pe-gray-50);
    }

    .pe-user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--pe-primary) 0%, #8b5cf6 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 1rem;
    }

    .pe-badge {
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .pe-badge.policy {
        background: #dbeafe;
        color: #1d4ed8;
    }

    .pe-badge.potd {
        background: #fef3c7;
        color: #d97706;
    }

    .pe-badge.time {
        background: #d1fae5;
        color: #059669;
    }

    .pe-empty-state {
        text-align: center;
        padding: 3rem;
        color: var(--pe-gray-500);
    }

    .pe-empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .pe-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .pe-role-stats {
            flex-direction: column;
            gap: 0.75rem;
        }

        .pe-engagement-table {
            font-size: 0.875rem;
        }

        .pe-engagement-table th,
        .pe-engagement-table td {
            padding: 0.75rem;
        }
    }
</style>

<div class="policy-engagements-page">
    <div class="pe-header">
        <h1>
            <i class="fa fa-chart-line"></i>
            Policy of Day Engagements
        </h1>
    </div>

    <div class="pe-summary-cards">
        <div class="pe-card primary">
            <div class="pe-card-title">Total Views</div>
            <div class="pe-card-value">{{ $summary['total_views'] }}</div>
        </div>
        <div class="pe-card">
            <div class="pe-card-title">Total Engagement Time</div>
            <div class="pe-card-value">{{ number_format($summary['total_engagement_time']) }}s</div>
        </div>
        <div class="pe-card">
            <div class="pe-card-title">Avg Engagement Time</div>
            <div class="pe-card-value">{{ number_format($summary['avg_engagement_time'], 1) }}s</div>
        </div>
        <div class="pe-card">
            <div class="pe-card-title">Today's POTD Views</div>
            <div class="pe-card-value">{{ $summary['today_potd_views'] }}</div>
        </div>
    </div>

    <div class="pe-section">
        <h2 class="pe-section-title" style="font-size: 1.75rem;">Today's Policy of the Day Engagements</h2>
        
        @if($todayPotdEngagements->isEmpty())
            <div class="pe-empty-state">
                <i class="fa fa-star"></i>
                <p>No Policy of the Day views recorded today.</p>
            </div>
        @else
            <table class="pe-engagement-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Office</th>
                        <th>Policy Title</th>
                        <th>Engagement Time</th>
                        <th>Time Viewed</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($todayPotdEngagements as $engagement)
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="pe-user-avatar">
                                        {{ substr($engagement->user->first_name ?? 'U', 0, 1) }}{{ substr($engagement->user->last_name ?? '', 0, 1) }}
                                    </div>
                                    <span>{{ $engagement->user->first_name ?? 'Unknown' }} {{ $engagement->user->last_name ?? '' }}</span>
                                </div>
                            </td>
                            <td style="font-size: 1.125rem;">{{ $engagement->user->office->name ?? 'N/A' }}</td>
                            <td style="font-size: 1.125rem;">{{ $engagement->policyOfTheDay->title ?? 'N/A' }}</td>
                            <td>
                                <span class="pe-badge time" style="font-size: 1rem;">{{ number_format($engagement->engagement_time) }}s</span>
                            </td>
                            <td style="font-size: 1.125rem;">{{ $engagement->created_at->format('H:i:s') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <div class="pe-section">
        <h2 class="pe-section-title">Engagements by Role</h2>
        
        @if($groupedEngagements->isEmpty())
            <div class="pe-empty-state">
                <i class="fa fa-inbox"></i>
                <p>No policy engagement data found.</p>
            </div>
        @else
            @foreach($groupedEngagements as $roleName => $engagements)
                <div class="pe-role-group">
                    <div class="pe-role-header">
                        <span class="pe-role-name">{{ $roleName }}</span>
                        <div class="pe-role-stats">
                            <span class="pe-stat">
                                <div>Views</div>
                                <div class="pe-stat-value">{{ $engagements->count() }}</div>
                            </span>
                            <span class="pe-stat">
                                <div>Total Time</div>
                                <div class="pe-stat-value">{{ number_format($engagements->sum('engagement_time')) }}s</div>
                            </span>
                            <span class="pe-stat">
                                <div>Avg Time</div>
                                <div class="pe-stat-value">{{ number_format($engagements->avg('engagement_time'), 1) }}s</div>
                            </span>
                        </div>
                    </div>
                    
                    <table class="pe-engagement-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Office</th>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Engagement Time</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($engagements as $engagement)
                                <tr>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div class="pe-user-avatar">
                                                {{ substr($engagement->user->first_name ?? 'U', 0, 1) }}{{ substr($engagement->user->last_name ?? '', 0, 1) }}
                                            </div>
                                            <span style="font-size: 1.125rem;">{{ $engagement->user->first_name ?? 'Unknown' }} {{ $engagement->user->last_name ?? '' }}</span>
                                        </div>
                                    </td>
                                    <td style="font-size: 1.125rem;">{{ $engagement->user->office->name ?? 'N/A' }}</td>
                                    <td style="font-size: 1.125rem;">
                                        @if($engagement->policy_of_the_day_id)
                                            <span class="pe-badge potd">Policy of the Day</span>
                                        @elseif($engagement->policy_id)
                                            <span class="pe-badge policy">Policy Document</span>
                                        @endif
                                    </td>
                                    <td style="font-size: 1.125rem;">
                                        @if($engagement->policy_of_the_day_id)
                                            {{ $engagement->policyOfTheDay->title ?? 'N/A' }}
                                        @elseif($engagement->policy_id)
                                            {{ $engagement->policy->title ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="pe-badge time" style="font-size: 1rem;">{{ number_format($engagement->engagement_time) }}s</span>
                                    </td>
                                    <td style="font-size: 1.125rem;">{{ $engagement->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endforeach
        @endif
    </div>

    <div class="pe-section">
        <h2 class="pe-section-title">Users Ignoring POTD (Viewed < 50%)</h2>
        
        @if($ignoringPotdUsers->isEmpty())
            <div class="pe-empty-state">
                <i class="fa fa-check-circle"></i>
                <p>All users have viewed at least 50% of Policy of the Day records.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="pe-engagement-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Office</th>
                            <th style="text-align: center;">POTD Views</th>
                            <th style="text-align: center;">Total POTD</th>
                            <th style="text-align: center;">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ignoringPotdUsers as $item)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div class="pe-user-avatar">
                                            {{ substr($item['user']->first_name ?? 'U', 0, 1) }}{{ substr($item['user']->last_name ?? '', 0, 1) }}
                                        </div>
                                        <span style="font-size: 1.125rem;">{{ $item['user']->first_name ?? 'Unknown' }} {{ $item['user']->last_name ?? '' }}</span>
                                    </div>
                                </td>
                                <td style="font-size: 1.125rem;">{{ $item['role_name'] }}</td>
                                <td style="font-size: 1.125rem;">{{ $item['user']->office->name ?? 'N/A' }}</td>
                                <td style="text-align: center; font-size: 1.125rem;">{{ $item['viewed_count'] }}</td>
                                <td style="text-align: center; font-size: 1.125rem;">{{ $item['total_potd'] }}</td>
                                <td style="text-align: center;">
                                    <span class="pe-badge" style="background: #fee2e2; color: #dc2626; font-size: 1rem;">
                                        {{ $item['percentage'] }}%
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection