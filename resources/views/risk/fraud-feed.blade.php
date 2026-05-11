@extends('layouts.master')

@section('title')
    Real-Time Fraud Alert Feed
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Real-Time Fraud Alert Feed</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success">Refresh Feed</button>
                </div>
            </div>
            <div class="box-body">
                <p>A live fraud monitoring section displaying real-time alerts such as after-hours LMS logins, duplicate NRC usage, suspicious overrides, repeated reversals, unusual transaction patterns, payments linked to staff numbers, dormant account activations, and high-risk approval patterns.</p>

                <!-- Alert Filters -->
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Alert Types</option>
                            <option>After-hours Logins</option>
                            <option>Duplicate NRC</option>
                            <option>Suspicious Overrides</option>
                            <option>Repeated Reversals</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Branches</option>
                            <option>Branch A</option>
                            <option>Branch B</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="datetime-local" class="form-control" placeholder="To Date">
                    </div>
                </div>

                <!-- Live Alerts Feed -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Live Alerts</h4>
                            </div>
                            <div class="box-body" style="max-height: 400px; overflow-y: auto;">
                                <div class="alert alert-danger">
                                    <strong>Critical:</strong> After-hours LMS login detected for User ID 123 at Branch A - 2023-10-01 22:30
                                    <button class="btn btn-xs btn-warning pull-right">Investigate</button>
                                </div>
                                <div class="alert alert-warning">
                                    <strong>Warning:</strong> Duplicate NRC usage detected - NRC: 123456789 at Branch B - 2023-10-01 14:15
                                    <button class="btn btn-xs btn-warning pull-right">Investigate</button>
                                </div>
                                <div class="alert alert-info">
                                    <strong>Info:</strong> Suspicious override on transaction #45678 at Branch C - 2023-10-01 11:45
                                    <button class="btn btn-xs btn-warning pull-right">Investigate</button>
                                </div>
                                <!-- Add more alerts as needed -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Alert Statistics (Last 24 Hours)</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Critical Alerts</span>
                                                <span class="info-box-number">3</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-yellow"><i class="fa fa-warning"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Warning Alerts</span>
                                                <span class="info-box-number">12</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-blue"><i class="fa fa-info-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Info Alerts</span>
                                                <span class="info-box-number">25</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Resolved</span>
                                                <span class="info-box-number">18</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection