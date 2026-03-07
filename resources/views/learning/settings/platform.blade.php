@extends('layouts.learning')

@section('title', 'Platform Settings - Whence Learn')

@section('content')
@php
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => url('learning/dashboard')],
    ['label' => 'Settings', 'url' => url('learning/settings')],
    ['label' => 'Platform', 'url' => '']
];
@endphp
@include('partials.breadcrumb')

<div class="page-header">
    <h1>Platform Settings</h1>
    <p>Configure platform-wide settings and preferences</p>
</div>

<!-- Platform Settings Content -->
<div class="settings-content">
    <div class="card">
        <div class="card-header">
            <h2>Platform Configuration</h2>
        </div>
        <div class="card-body">
            <p>Platform settings functionality will be implemented here.</p>
            
            <div class="alert alert-info">
                This section is under development. Please check back later.
            </div>
        </div>
    </div>
</div>
@endsection