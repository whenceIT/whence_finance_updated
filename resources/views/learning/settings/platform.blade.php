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
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-content-push">
                            Content Push Mechanism
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-tracking-monitoring">
                            Tracking & Monitoring
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-feedback-loop">
                            Feedback Loop to Users
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-performance-triggers">
                            Performance-Linked Learning Triggers
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-escalation-enforcement">
                            Escalation & Enforcement Mechanisms
                        </a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action" data-toggle="modal" data-target="#modal-management-dashboard">
                            Management & Executive Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('learning.settings.modals.content-push')

<div class="modal fade" id="modal-tracking-monitoring" tabindex="-1" role="dialog" aria-labelledby="modal-tracking-monitoring-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-tracking-monitoring-label">Tracking & Monitoring</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Tracking & Monitoring -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-feedback-loop" tabindex="-1" role="dialog" aria-labelledby="modal-feedback-loop-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-feedback-loop-label">Feedback Loop to Users</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Feedback Loop to Users -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-performance-triggers" tabindex="-1" role="dialog" aria-labelledby="modal-performance-triggers-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-performance-triggers-label">Performance-Linked Learning Triggers</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Performance-Linked Learning Triggers -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-escalation-enforcement" tabindex="-1" role="dialog" aria-labelledby="modal-escalation-enforcement-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-escalation-enforcement-label">Escalation & Enforcement Mechanisms</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Escalation & Enforcement Mechanisms -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-management-dashboard" tabindex="-1" role="dialog" aria-labelledby="modal-management-dashboard-label" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-management-dashboard-label">Management & Executive Dashboard</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Content for Management & Executive Dashboard -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection