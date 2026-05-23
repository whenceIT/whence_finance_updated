@extends('layouts.master')
@section('title')
    GOA Manager - Fleet Vehicle Details
@endsection

@section('content')
<div class="container-fluid">
    <!-- Sleek Professional Header -->
    <div class="fleet-show-header">
        <div class="header-content">
            <div class="header-main">
                <div class="vehicle-icon-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="vehicle-icon" viewBox="0 0 16 16">
                        <path d="M4 9a1 1 0 1 1-2 0 1 1 0 0 1 2 0m10 0a1 1 0 1 1-2 0 1 1 0 0 1 2 0M6 8a1 1 0 0 0 0 2h4a1 1 0 1 0 0-2zM4.862 4.276 3.906 6.19a.51.51 0 0 0 .497.731c.91-.073 2.35-.17 3.597-.17s2.688.097 3.597.17a.51.51 0 0 0 .497-.731l-.956-1.913A.5.5 0 0 0 10.691 4H5.309a.5.5 0 0 0-.447.276"/>
                        <path d="M2.52 3.515A2.5 2.5 0 0 1 4.82 2h6.362c1 0 1.904.596 2.298 1.515l.792 1.848c.075.175.21.319.38.404.5.25.855.715.965 1.262l.335 1.679q.05.242.049.49v.413c0 .814-.39 1.543-1 1.997V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.338c-1.292.048-2.745.088-4 .088s-2.708-.04-4-.088V13.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-1.892c-.61-.454-1-1.183-1-1.997v-.413a2.5 2.5 0 0 1 .049-.49l.335-1.68c.11-.546.465-1.012.964-1.261a.8.8 0 0 0 .381-.404l.792-1.848ZM4.82 3a1.5 1.5 0 0 0-1.379.91l-.792 1.847a1.8 1.8 0 0 1-.853.904.8.8 0 0 0-.43.564L1.03 8.904a1.5 1.5 0 0 0-.03.294v.413c0 .796.62 1.448 1.408 1.484 1.555.07 3.786.155 5.592.155s4.037-.084 5.592-.155A1.48 1.48 0 0 0 15 9.611v-.413q0-.148-.03-.294l-.335-1.68a.8.8 0 0 0-.43-.563 1.8 1.8 0 0 1-.853-.904l-.792-1.848A1.5 1.5 0 0 0 11.18 3z"/>
                    </svg>
                </div>
                <div class="header-text">
                    <div class="vehicle-title-row">
                        <h1 class="vehicle-id">{{ $fleet->vehicle_id }}</h1>
                        @if($fleet->vehicle_status == 'Active')
                            <span class="status-badge status-active">ACTIVE</span>
                        @elseif($fleet->vehicle_status == 'Maintenance')
                            <span class="status-badge status-maintenance">MAINTENANCE</span>
                        @else
                            <span class="status-badge status-inactive">{{ strtoupper($fleet->vehicle_status) }}</span>
                        @endif
                    </div>
                    <div class="vehicle-meta">
                        <span class="meta-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v.217l7 4.2 7-4.2V4a1 1 0 0 0-1-1H2z"/></svg>
                            {{ $fleet->vehicle_type ?: 'Vehicle' }}
                        </span>
                        <span class="meta-separator">•</span>
                        <span class="meta-item">{{ $fleet->vehicle_model ?: 'Unknown Model' }}</span>
                    </div>
                </div>
            </div>
            <div class="header-meta">
                <div class="updated-label">Last Updated</div>
                <div class="updated-value">{{ $fleet->updated_at ? $fleet->updated_at->diffForHumans() : 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Colorful Iconic Quick Stats -->
    <div class="stats-grid">
        <div class="stat-card stat-driver">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">ASSIGNED DRIVER</div>
                <div class="stat-value">{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : 'Unassigned' }}</div>
            </div>
        </div>

        <div class="stat-card stat-office">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4.5 0A.5.5 0 0 0 4 0.5V2H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-2V.5a.5.5 0 0 0-.5-.5h-7zM3 3h10v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3z"/>
                    <path d="M5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">OFFICE / LOCATION</div>
                <div class="stat-value">{{ $fleet->office ? $fleet->office->name : 'No Office' }}</div>
            </div>
        </div>

        <div class="stat-card stat-insurance">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 0a1 1 0 0 1 1 1v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V1a1 1 0 0 1 1-1zM4 3.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-8zM2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4zm3 1a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3A.5.5 0 0 1 5 5zm2.5 0a.5.5 0 0 1 .5.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 1 .5-.5z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">INSURANCE EXPIRY</div>
                <div class="stat-value">{{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}</div>
            </div>
        </div>

        <div class="stat-card stat-value">
            <div class="stat-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M4 3.06V1a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v2.06a1 1 0 0 1 .986.836l.5 3.5a.5.5 0 0 1-.486.564H.514a.5.5 0 0 1-.486-.564l.5-3.5A1 1 0 0 1 4 3.06zM4.5 1v2.06l-.5 3.5h7.98l-.5-3.5V1h-7z"/>
                    <path d="M4 10.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5z"/>
                </svg>
            </div>
            <div class="stat-content">
                <div class="stat-label">CURRENT VALUE</div>
                <div class="stat-value">{{ $fleet->current_value ? 'ZMW ' . number_format($fleet->current_value, 0) : 'N/A' }}</div>
            </div>
        </div>
    </div>

    <!-- Organized Detailed Sections -->
    <div class="details-grid">
        <!-- Vehicle Details Card -->
        <div class="info-card">
            <div class="info-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                    <path d="m8.93 6.588-2.29.287-.082.38.45.083c.294.07.352.176.288.469l-.738 3.468c-.194.897.105 1.319.808 1.319.545 0 1.178-.252 1.465-.598l.088-.416c-.2.176-.492.246-.686.246-.275 0-.375-.193-.304-.533L8.93 6.588zM9 4.5a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/>
                </svg>
                <span>Vehicle Specifications</span>
            </div>
            <div class="info-body">
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 0a1 1 0 0 1 1 1v1.5a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V1a1 1 0 0 1 1-1z"/></svg>
                        Vehicle ID
                    </div>
                    <div class="info-value">{{ $fleet->vehicle_id }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.5 0A.5.5 0 0 0 4 0.5V2H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-2V.5a.5.5 0 0 0-.5-.5h-7zM3 3h10v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3z"/></svg>
                        Type
                    </div>
                    <div class="info-value">{{ $fleet->vehicle_type ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l4.414 4.414a1 1 0 0 1 .293.707V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V2zm7 1v3h3l-3-3z"/></svg>
                        Model
                    </div>
                    <div class="info-value">{{ $fleet->vehicle_model ?: 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.433 10.07C14.133 10.585 16 11.15 16 13a3 3 0 0 1-3 3c-1.074 0-2.057-.447-2.742-1.172-.65.65-1.583 1.172-2.758 1.172a3 3 0 0 1-3-3c0-1.85 1.867-2.415 3.567-2.93C6.533 9.585 4.667 9.02 4.667 7a3 3 0 0 1 3-3c1.175 0 2.108.522 2.758 1.172.685-.725 1.668-1.172 2.742-1.172a3 3 0 0 1 3 3c0 1.85-1.867 2.415-3.567 2.93zM12 13a1 1 0 1 0 0-2 1 1 0 0 0 0 2zM4.667 7a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/></svg>
                        Color
                    </div>
                    <div class="info-value">
                        <span class="color-swatch">{{ $fleet->color ?: 'N/A' }}</span>
                    </div>
                </div>
                <div class="info-row last">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/></svg>
                        White Book
                    </div>
                    <div class="info-value">{{ ucfirst($fleet->white_book ?? 'N/A') }}</div>
                </div>
            </div>
        </div>

        <!-- Assignment & Status Card -->
        <div class="info-card">
            <div class="info-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1H7zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M5.216 14A2.238 2.238 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.325 6.325 0 0 0 5.422 5H7.5A2.5 2.5 0 0 1 10 7.5v1.75c.5.25 1 .5 1.5.75V7.5A3.5 3.5 0 0 0 8 4H5.5a3.5 3.5 0 0 0-3.5 3.5v5.25c.5-.25 1-.5 1.5-.75V7.5A2.5 2.5 0 0 1 5.5 5H7a6.325 6.325 0 0 0-1.514 5.28C6.32 11.25 7 12.645 7 14a2.238 2.238 0 0 1-.216.999H5.5z"/>
                </svg>
                <span>Assignment & Status</span>
            </div>
            <div class="info-body">
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/></svg>
                        Assigned To
                    </div>
                    <div class="info-value">{{ $fleet->user ? $fleet->user->first_name . ' ' . $fleet->user->last_name : 'Unassigned' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M4.5 0A.5.5 0 0 0 4 0.5V2H2a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2h-2V.5a.5.5 0 0 0-.5-.5h-7zM3 3h10v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V3z"/></svg>
                        Office
                    </div>
                    <div class="info-value">{{ $fleet->office ? $fleet->office->name : 'N/A' }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM2 2a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H2z"/></svg>
                        Last Maintenance
                    </div>
                    <div class="info-value">{{ $fleet->last_maintenance ? $fleet->last_maintenance->format('d M Y') : 'N/A' }}</div>
                </div>
                <div class="info-row last">
                    <div class="info-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M5 3a5 5 0 0 0 0 10h6a5 5 0 0 0 0-10H5zm6 9a4 4 0 0 1-4 4H5a4 4 0 0 1-4-4V8a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v4z"/></svg>
                        Status
                    </div>
                    <div class="info-value">
                        @if($fleet->vehicle_status == 'Active')
                            <span class="inline-badge badge-success">Active & Operational</span>
                        @elseif($fleet->vehicle_status == 'Maintenance')
                            <span class="inline-badge badge-warning">Under Maintenance</span>
                        @else
                            <span class="inline-badge badge-danger">{{ $fleet->vehicle_status }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financials & Documents Card -->
    <div class="info-card full-width">
        <div class="info-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                <path d="M14 3a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1h12zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2z"/>
                <path d="M8 10a1 1 0 0 1-1-1V6a1 1 0 0 1 2 0v3a1 1 0 0 1-1 1zm-2.5 1a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 1 0v1a.5.5 0 0 1-.5.5z"/>
            </svg>
            <span>Financials & Compliance</span>
        </div>
        <div class="info-body financial-body">
            <div class="financial-item">
                <div class="financial-label">Date Purchased</div>
                <div class="financial-value">{{ $fleet->date_purchased ? $fleet->date_purchased->format('d M Y') : 'N/A' }}</div>
            </div>
            <div class="financial-item">
                <div class="financial-label">Current Value</div>
                <div class="financial-value highlight-green">{{ $fleet->current_value ? 'ZMW ' . number_format($fleet->current_value, 2) : 'N/A' }}</div>
            </div>
            <div class="financial-item">
                <div class="financial-label">Insurance Expires</div>
                <div class="financial-value">
                    {{ $fleet->insurance_expire_date ? $fleet->insurance_expire_date->format('d M Y') : 'N/A' }}
                    @if($fleet->insurance_expire_date && $fleet->insurance_expire_date->isPast())
                        <span class="inline-badge badge-danger" style="margin-left: 0.5rem;">Expired</span>
                    @elseif($fleet->insurance_expire_date && $fleet->insurance_expire_date->diffInDays() < 30)
                        <span class="inline-badge badge-warning" style="margin-left: 0.5rem;">Expiring Soon</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Professional Action Bar -->
    <div class="action-bar">
        <a href="{{ route('goa.fleet-management') }}" class="fleet-action-btn secondary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M12 8a.5.5 0 0 1-.5.5H5.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L5.707 7.5H11.5a.5.5 0 0 1 .5.5z"/>
            </svg>
            Back to Fleet Management
        </a>
        <a href="{{ route('fleets.edit', $fleet->id) }}" class="fleet-action-btn primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L12.5 5.207 9 8.707V10h1.293l3.293-3.293zM11 10v-1h-1v1h1z"/>
            </svg>
            Edit Vehicle Details
        </a>
    </div>
</div>

<style>
    /* Fleet Show - Neat, Colorful, Iconic, Sleek, Professional */
    .fleet-show-header {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 16px;
        box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.1), 0 4px 6px -1px rgba(15, 23, 42, 0.05);
        padding: 1.75rem 2rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-main {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .vehicle-icon-wrapper {
        width: 64px;
        height: 64px;
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 8px 20px -4px rgba(37, 99, 235, 0.3);
        flex-shrink: 0;
    }

    .vehicle-icon {
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.15));
    }

    .header-text {
        flex: 1;
        min-width: 0;
    }

    .vehicle-title-row {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .vehicle-id {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.02em;
        line-height: 1;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35rem 0.9rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .status-maintenance {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .status-inactive {
        background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
        color: white;
    }

    .vehicle-meta {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.35rem;
        font-size: 0.95rem;
        color: #64748b;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .meta-separator {
        color: #cbd5e1;
    }

    .header-meta {
        text-align: right;
        min-width: 140px;
    }

    .updated-label {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .updated-value {
        font-size: 0.95rem;
        color: #475569;
        font-weight: 600;
    }

    /* Stats Grid - Colorful & Iconic */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 4px 15px -2px rgba(15, 23, 42, 0.08), 0 2px 4px -2px rgba(15, 23, 42, 0.06);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f5f9;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.1), 0 8px 10px -6px rgba(15, 23, 42, 0.1);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .stat-driver .stat-icon { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; }
    .stat-office .stat-icon { background: linear-gradient(135deg, #14b8a6 0%, #0f766e 100%); color: #fff; }
    .stat-insurance .stat-icon { background: linear-gradient(135deg, #f59e0b 0%, #b45309 100%); color: #fff; }
    .stat-value .stat-icon { background: linear-gradient(135deg, #10b981 0%, #047857 100%); color: #fff; }

    .stat-content {
        flex: 1;
        min-width: 0;
    }

    .stat-label {
        font-size: 0.68rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 0.15rem;
    }

    .stat-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.2;
        word-break: break-word;
    }

    /* Details Grid */
    .details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .info-card {
        background: #ffffff;
        border-radius: 14px;
        box-shadow: 0 4px 15px -3px rgba(15, 23, 42, 0.07), 0 2px 6px -2px rgba(15, 23, 42, 0.05);
        border: 1px solid #e2e8f0;
        overflow: hidden;
        transition: box-shadow 0.2s ease;
    }

    .info-card:hover {
        box-shadow: 0 10px 25px -8px rgba(15, 23, 42, 0.12);
    }

    .info-card.full-width {
        grid-column: 1 / -1;
    }

    .info-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        padding: 0.9rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-weight: 700;
        color: #334155;
        font-size: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .info-header svg {
        color: #2563eb;
        flex-shrink: 0;
    }

    .info-body {
        padding: 0.35rem 0;
    }

    .info-row {
        display: flex;
        align-items: center;
        padding: 0.65rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.925rem;
    }

    .info-row.last {
        border-bottom: none;
    }

    .info-label {
        width: 42%;
        color: #64748b;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.45rem;
    }

    .info-label svg {
        color: #94a3b8;
        flex-shrink: 0;
    }

    .info-value {
        width: 58%;
        color: #0f172a;
        font-weight: 600;
    }

    .color-swatch {
        display: inline-block;
        padding: 0.15rem 0.7rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        font-weight: 600;
        color: #334155;
    }

    .inline-badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .badge-success { background: #dcfce7; color: #166534; }
    .badge-warning { background: #fef3c7; color: #92400e; }
    .badge-danger { background: #fee2e2; color: #991b1b; }

    /* Financials */
    .financial-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 0.25rem;
        padding: 0.5rem 0;
    }

    .financial-item {
        padding: 0.75rem 1.25rem;
        border-right: 1px solid #f1f5f9;
    }

    .financial-item:last-child {
        border-right: none;
    }

    .financial-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-bottom: 0.2rem;
    }

    .financial-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: #0f172a;
    }

    .financial-value.highlight-green {
        color: #059669;
    }

    /* Action Bar */
    .action-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        padding: 0 0.25rem;
    }

    .fleet-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    }

    .fleet-action-btn.primary {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: white;
    }

    .fleet-action-btn.primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3);
    }

    .fleet-action-btn.secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .fleet-action-btn.secondary:hover {
        background: #e2e8f0;
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            align-items: flex-start;
        }
        .header-meta {
            text-align: left;
            margin-top: 0.5rem;
        }
        .stats-grid {
            grid-template-columns: 1fr;
        }
        .details-grid {
            grid-template-columns: 1fr;
        }
        .financial-body {
            grid-template-columns: 1fr;
        }
        .financial-item {
            border-right: none;
            border-bottom: 1px solid #f1f5f9;
        }
        .financial-item:last-child {
            border-bottom: none;
        }
        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .fleet-action-btn {
            justify-content: center;
        }
    }
</style>
@endsection
