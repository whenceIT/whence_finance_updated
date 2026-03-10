@extends('layouts.learning')

@section('title', 'Students Management - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
    ['label' => 'Students', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Students Management</h1>
    <p>Manage student profiles and learning progress</p>
</div>

<!-- Students Management Content -->
<div class="settings-content">
    <div class="card">
        <div class="card-header">
            <h2>Students List</h2>
        </div>
        <div class="card-body">
            <p>Students management functionality will be implemented here.</p>
            
            @if(empty($students))
                <div class="alert alert-info">
                    No students found.
                </div>
            @else
                <!-- Students table will be added here -->
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Enrollment Status</th>
                            <th>Progress</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Student rows will be populated here -->
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection