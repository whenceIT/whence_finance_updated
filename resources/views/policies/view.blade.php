@extends('layouts.master')

@section('content')
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
            <svg style="color:#fff" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-medical-fill" viewBox="0 0 16 16">
            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-3 2v.634l.549-.317a.5.5 0 1 1 .5.866L7 7l.549.317a.5.5 0 1 1-.5.866L6.5 7.866V8.5a.5.5 0 0 1-1 0v-.634l-.549.317a.5.5 0 1 1-.5-.866L5 7l-.549-.317a.5.5 0 0 1 .5-.866l.549.317V5.5a.5.5 0 1 1 1 0m-2 4.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1m0 2h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1 0-1"/>
            </svg>
        </div>
        <h1>Company Policies & Documents</h1>
        <p>Manage and review company policies with proper categorization and access controls</p>
    </div>

    <!-- Main Action Bar -->
    <div class="action-bar">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex gap-3 flex-wrap">
                <button type="button" class="btn btn-success" onclick="acceptAllPolicies()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0"/>
                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708"/>
                        </svg> Accept All
                </button>
                <button type="button" class="btn btn-danger" onclick="declineAllPolicies()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-slash-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M11.354 4.646a.5.5 0 0 0-.708 0l-6 6a.5.5 0 0 0 .708.708l6-6a.5.5 0 0 0 0-.708"/>
                    </svg> Decline All
                </button>
                @if($isAdmin)
                    <a href="{{ route('policies.add_policies') }}" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-plus" viewBox="0 0 16 16">
                        <path d="M8 6.5a.5.5 0 0 1 .5.5v1.5H10a.5.5 0 0 1 0 1H8.5V11a.5.5 0 0 1-1 0V9.5H6a.5.5 0 0 1 0-1h1.5V7a.5.5 0 0 1 .5-.5"/>
                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z"/>
                        </svg> Add New Document
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter" viewBox="0 0 16 16">
                    <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
                    </svg>
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
                                    <span class="font-weight-bold text-dark">Category</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold text-dark">Access Level</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
                                    <span class="font-weight-bold text-dark">File Type</span>
                                </div>
                            </th>
                            <th scope="col">
                                <div class="d-flex align-items-center">
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
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-files" viewBox="0 0 16 16">
                                            <path d="M13 0H6a2 2 0 0 0-2 2 2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 2 2 0 0 0 2-2V2a2 2 0 0 0-2-2m0 13V4a2 2 0 0 0-2-2H5a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1M3 4a1 1 0 0 1 1-1h7a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/>
                                            </svg>                                       
                                        </div>
                                        <div class="document-info">
                                            <h6 style="text-transform: capitalize;">
                                                {{ $policy->title }}
                                            </h6>

                                            @if($policy->description)
                                                <p style="text-transform: capitalize;">
                                                    {{ \Illuminate\Support\Str::limit($policy->description, 80) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($policy->category)
                                        <p class="badge bg-primary">{{ $policy->category->name }}</p>
                                    @else
                                        <p class="badge bg-secondary">Policies</p>
                                    @endif
                                </td>
                                <td>
                                    @if($policy->access_level == 'managerial')
                                        <span class="badge bg-warning">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-rolodex" viewBox="0 0 16 16">
                                            <path d="M8 9.05a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                            <path d="M1 1a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h.5a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5.5.5 0 0 1 1 0 .5.5 0 0 0 .5.5h.5a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1H6.707L6 1.293A1 1 0 0 0 5.293 1zm0 1h4.293L6 2.707A1 1 0 0 0 6.707 3H15v10h-.085a1.5 1.5 0 0 0-2.4-.63C11.885 11.223 10.554 10 8 10c-2.555 0-3.886 1.224-4.514 2.37a1.5 1.5 0 0 0-2.4.63H1z"/>
                                            </svg> Managerial
                                        </span>
                                    @else
                                        <span class="badge bg-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
                                            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                                            </svg> All Staff
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
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check" viewBox="0 0 16 16">
                                                <path d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z"/>
                                                </svg> Accepted
                                            </span>
                                        @elseif($response->status == 'declined')
                                            <span class="badge bg-danger">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-octagon" viewBox="0 0 16 16">
                                                <path d="M4.54.146A.5.5 0 0 1 4.893 0h6.214a.5.5 0 0 1 .353.146l4.394 4.394a.5.5 0 0 1 .146.353v6.214a.5.5 0 0 1-.146.353l-4.394 4.394a.5.5 0 0 1-.353.146H4.893a.5.5 0 0 1-.353-.146L.146 11.46A.5.5 0 0 1 0 11.107V4.893a.5.5 0 0 1 .146-.353zM5.1 1 1 5.1v5.8L5.1 15h5.8l4.1-4.1V5.1L10.9 1z"/>
                                                <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                                                </svg>Declined
                                            </span>
                                        @elseif($response->status == 'pending')
                                            <span class="badge bg-warning">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pause-fill" viewBox="0 0 16 16">
                                                <path d="M5.5 3.5A1.5 1.5 0 0 1 7 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5m5 0A1.5 1.5 0 0 1 12 5v6a1.5 1.5 0 0 1-3 0V5a1.5 1.5 0 0 1 1.5-1.5"/>
                                                </svg> Pending
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-clock-history" viewBox="0 0 16 16">
                                            <path d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022zm2.004.45a7 7 0 0 0-.985-.299l.219-.976q.576.129 1.126.342zm1.37.71a7 7 0 0 0-.439-.27l.493-.87a8 8 0 0 1 .979.654l-.615.789a7 7 0 0 0-.418-.302zm1.834 1.79a7 7 0 0 0-.653-.796l.724-.69q.406.429.747.91zm.744 1.352a7 7 0 0 0-.214-.468l.893-.45a8 8 0 0 1 .45 1.088l-.95.313a7 7 0 0 0-.179-.483m.53 2.507a7 7 0 0 0-.1-1.025l.985-.17q.1.58.116 1.17zm-.131 1.538q.05-.254.081-.51l.993.123a8 8 0 0 1-.23 1.155l-.964-.267q.069-.247.12-.501m-.952 2.379q.276-.436.486-.908l.914.405q-.24.54-.555 1.038zm-.964 1.205q.183-.183.35-.378l.758.653a8 8 0 0 1-.401.432z"/>
                                            <path d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0z"/>
                                            <path d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5"/>
                                            </svg> Pending
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ $policy->file_url }}" download class="btn btn-success btn-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1z"/>
                                            <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                                            </svg> Download
                                        </a>
                                        <button type="button" class="btn btn-primary btn-sm" 
                                                title="View Preview" 
                                                onclick="openPolicyModal({{ $policy->id }}, '{{ addslashes($policy->title) }}', '{{ $policy->file_url }}', '{{ $policy->file_type }}')">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z"/>
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                                </svg> View
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                    <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5"/>
                                                    </svg> Delete
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
