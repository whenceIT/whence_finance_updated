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
            font-size: 1.25rem;
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
            font-size: 1.95rem;
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
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>Add Open Position
        </button>
        <button type="button" class="staffing-action-btn secondary" data-modal="addDepartment">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>Add Department
        </button>
        <button type="button" class="staffing-action-btn secondary" data-modal="addRole">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/>
            </svg>Add New Position
        </button>
    </div>

    <div class="staffing-nav-container" role="tablist">
        <button class="staffing-nav-btn active" data-section="positions" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase-fill" viewBox="0 0 16 16">
            <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5"/>
            <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85z"/>
            </svg>Open Positions
        </button>
        <button class="staffing-nav-btn" data-section="overview" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16">
            <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
            </svg>Staffing Overview
        </button>
        <button class="staffing-nav-btn" data-section="breakdown" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-building-fill-down" viewBox="0 0 16 16">
            <path d="M12.5 9a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7m.354 5.854 1.5-1.5a.5.5 0 0 0-.708-.708l-.646.647V10.5a.5.5 0 0 0-1 0v2.793l-.646-.647a.5.5 0 0 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0"/>
            <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v7.256A4.5 4.5 0 0 0 12.5 8a4.5 4.5 0 0 0-3.59 1.787A.5.5 0 0 0 9 9.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .39-.187A4.5 4.5 0 0 0 8.027 12H6.5a.5.5 0 0 0-.5.5V16H3a1 1 0 0 1-1-1zm2 1.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5m3 0v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5m3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5M7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5M4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
            </svg>Department Breakdown
        </button>
        <button class="staffing-nav-btn" data-section="hires" role="tab">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
            <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5m1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0M1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5"/>
            </svg>Recent Hires
        </button>
    </div>

    <div class="staffing-content-container" id="staffingTabsContent">
        <div class="staffing-section active" id="positions" role="tabpanel">
        <h5 style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; padding: 10px 24px; border-radius: 60px; font-weight: 600; font-size: 1.25rem; box-shadow: 0 8px 20px -4px rgba(59, 130, 246, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-briefcase-fill" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v1.384l7.614 2.03a1.5 1.5 0 0 0 .772 0L16 5.884V4.5A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5"/>
                <path d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5V6.85L8.129 8.947a.5.5 0 0 1-.258 0L0 6.85z"/>
            </svg>
            Open Positions
        </h5>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Position ID</th>
                            <th>Department</th>
                            <th>Position Title</th>
                            <th>Status</th>
                            <th>Posted Date</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($positions as $position)
                        <tr>
                                <td>
                                    @php
                                        $name = trim(explode('(', $position->name)[0]);
                                        $words = explode(' ', $name);
                                        $initials = strtoupper(implode('', array_map(function($w) { return isset($w[0]) ? $w[0] : ''; }, $words)));
                                    @endphp
                                    {{ $initials }}{{ $position->id }}
                                </td>
                            <td>{{ $position->department_id ? 'Department ' . $position->department_id : 'N/A' }}</td>
                            <td>{{ $position->name }}</td>
                            <td>
                                @if($position->status == 'Open')
                                    <span class="badge badge-success">Open</span>
                                @elseif($position->status == 'In Review')
                                    <span class="badge badge-warning">In Review</span>
                                @else
                                    <span class="badge badge-danger">{{ $position->status }}</span>
                                @endif
                            </td>
                                <td>{{ $position->posted_date ? $position->posted_date->diffForHumans() : 'N/A' }}</td>
                                <td>{{ $position->date_added ? $position->date_added->format('Y-m-d') : 'N/A' }}</td>
                                <td>
                                    <form method="POST" action="{{ route('goa.position.remove', $position->id) }}" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                    <button class="btn btn-sm btn-info" onclick="viewPosition({{ $position->id }})">View</button>
                                    <form method="POST" action="{{ route('goa.position.fill', $position->id) }}" style="display:inline;">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-success">Fill Position</button>
                                    </form>
                                </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No open positions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="staffing-section" id="overview" role="tabpanel">
            <h5 style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; padding: 10px 24px; border-radius: 60px; font-weight: 600; font-size: 1.25rem; box-shadow: 0 8px 20px -4px rgba(139, 92, 246, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-people-fill" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                    <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5"/>
                </svg>
                Staffing Overview
            </h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Total Positions
                            <span class="badge badge-primary badge-pill">{{ $totalPositions }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Filled Positions
                            <span class="badge badge-success badge-pill">{{ $filledPositions }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Vacant Positions
                            <span class="badge badge-warning badge-pill">{{ $vacantPositions }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Positions in Recruitment
                            <span class="badge badge-info badge-pill">{{ $inProcessPositions }}</span>
                        </li>
                    </ul>
        </div>

        <div class="staffing-section" id="breakdown" role="tabpanel">
            <h5 style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; padding: 10px 24px; border-radius: 60px; font-weight: 600; font-size: 1.25rem; box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-building-fill-down" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                    <path d="M12.5 9a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7m.354 5.854 1.5-1.5a.5.5 0 0 0-.708-.708l-.646.647V10.5a.5.5 0 0 0-1 0v2.793l-.646-.647a.5.5 0 0 0-.708.708l1.5 1.5a.5.5 0 0 0 .708 0"/>
                    <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v7.256A4.5 4.5 0 0 0 12.5 8a4.5 4.5 0 0 0-3.59 1.787A.5.5 0 0 0 9 9.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .39-.187A4.5 4.5 0 0 0 8.027 12H6.5a.5.5 0 0 0-.5.5V16H3a1 1 0 0 1-1-1zm2 1.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5m3 0v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5m3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zM4 5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5M7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5zm2.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5M4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5z"/>
                </svg>
                Department Breakdown
            </h5>
            <ul class="list-group list-group-flush">
                @forelse($departments as $dept)
                @php
                    $ratio = $dept->capacity > 0 ? $dept->filled_positions / $dept->capacity : ($dept->total_positions > 0 ? $dept->filled_positions / $dept->total_positions : 0);
                    $badgeClass = $ratio >= 1 ? 'badge-success' : ($ratio >= 0.8 ? 'badge-warning' : 'badge-danger');
                @endphp
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    {{ $dept->name }}
                    <span class="badge {{ $badgeClass }} badge-pill">{{ $dept->filled_positions }}/{{ $dept->capacity ?: $dept->total_positions }}</span>
                </li>
                @empty
                <li class="list-group-item d-flex justify-content-center">
                    No departments found.
                </li>
                @endforelse
            </ul>
        </div>

        <div class="staffing-section" id="hires" role="tabpanel">
            <h5 style="display: inline-flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 1.5rem; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: white; padding: 10px 24px; border-radius: 60px; font-weight: 600; font-size: 1.25rem; box-shadow: 0 8px 20px -4px rgba(99, 102, 241, 0.3), 0 4px 8px -4px rgba(0, 0, 0, 0.05); letter-spacing: -0.01em; backdrop-filter: blur(2px); border: 1px solid rgba(255, 255, 255, 0.2); transition: transform 0.2s ease, box-shadow 0.2s ease; cursor: default; width: 100%; margin-left: auto; margin-right: auto;">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16" style="filter: drop-shadow(0 1px 1px rgba(0,0,0,0.1));">
                    <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5m1.886 6.914L15 7.151V12.5a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5V7.15l6.614 1.764a1.5 1.5 0 0 0 .772 0M1.5 4h13a.5.5 0 0 1 .5.5v1.616L8.129 7.948a.5.5 0 0 1-.258 0L1 6.116V4.5a.5.5 0 0 1 .5-.5"/>
                </svg>
                Recent Hires
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
                        @forelse($recentHires as $hire)
                        <tr>
                            <td>{{ $hire->name }}</td>
                            <td>{{ $hire->name }}</td>
                            <td>{{ $hire->department ? $hire->department->name : 'N/A' }}</td>
                            <td>{{ $hire->updated_at ? $hire->updated_at->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                @if($hire->status == 'Open')
                                    <span class="badge badge-success">Open</span>
                                @elseif($hire->status == 'In Review')
                                    <span class="badge badge-warning">In Review</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($hire->status ?: 'Unknown') }}</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No recent positions found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="staffing-modal-backdrop" id="staffingModalBackdrop"></div>
    <div class="staffing-modal" id="staffingModal">
        <div class="staffing-modal-card">
            <div class="staffing-modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h4 class="staffing-modal-title" id="staffingModalTitle" style="color: white; margin-bottom: 0.25rem;">Add Open Position</h4>
                    <p class="text-muted small mb-0" id="staffingModalSubtitle" style="color: rgba(255,255,255,0.8);">Create a new vacancy or department entry quickly.</p>
                </div>
                <button type="button" class="staffing-modal-close" data-dismiss="staffing-modal" style="color: white;">×</button>
            </div>
            <div class="staffing-modal-body">
                <!-- Position Form -->
                <form id="positionForm" method="POST" action="{{ route('staff.update-position') }}">
                    @csrf
                    <div class="staffing-modal-section active" data-type="addPosition">
                        <div class="staffing-modal-form-group">
                            <label class="staffing-modal-label" for="positionId">Select Vacant Position</label>
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
                            <select class="staffing-modal-select" id="roleDepartment" name="roleDepartment">
                                <option value="">Select a Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
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

        function viewPosition(id) {
            fetch(`/goa_dashboard/position/${id}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('modalPositionId').textContent = data.id;
                    document.getElementById('modalPositionName').textContent = data.name;
                    document.getElementById('modalDepartment').textContent = data.department_id ? 'Department ' + data.department_id : 'N/A';
                    document.getElementById('modalStatus').textContent = data.status;
                    document.getElementById('modalJobDescription').textContent = data.job_description || 'N/A';
                    document.getElementById('modalPostedDate').textContent = data.posted_date || 'N/A';
                    document.getElementById('modalDateAdded').textContent = data.date_added || 'N/A';
                    document.getElementById('modalIsVacant').textContent = data.is_vacant ? 'Yes' : 'No';
                    document.getElementById('modalNumVacancies').textContent = data.num_of_vacancies || 0;
                    document.getElementById('modalNumActive').textContent = data.num_of_active || 0;
                    $('#positionModal').modal('show');
                });
        }
    </script>

    <!-- Position Details Modal -->
    <div class="modal fade" id="positionModal" tabindex="-1" role="dialog" aria-labelledby="positionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="positionModalLabel">Position Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Position ID:</strong> <span id="modalPositionId"></span></p>
                            <p><strong>Name:</strong> <span id="modalPositionName"></span></p>
                            <p><strong>Department:</strong> <span id="modalDepartment"></span></p>
                            <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                            <p><strong>Posted Date:</strong> <span id="modalPostedDate"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Date Added:</strong> <span id="modalDateAdded"></span></p>
                            <p><strong>Is Vacant:</strong> <span id="modalIsVacant"></span></p>
                            <p><strong>Number of Vacancies:</strong> <span id="modalNumVacancies"></span></p>
                            <p><strong>Number of Active:</strong> <span id="modalNumActive"></span></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <p><strong>Job Description:</strong></p>
                            <p id="modalJobDescription"></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection