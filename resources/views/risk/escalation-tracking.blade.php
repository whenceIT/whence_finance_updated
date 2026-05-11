@extends('layouts.master')

@section('title')
    Executive Escalation Tracking
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Executive Escalation Tracking</h3>
            </div>
            <div class="box-body">
                <p>Since the framework allows escalation beyond the Management Accountant under certain circumstances, this dashboard tracks escalations raised, response timelines, matters ignored beyond threshold periods, escalation outcomes, and executive action status.</p>

                <!-- Escalation Metrics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>24</h3>
                                <p>Total Escalations</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>18</h3>
                                <p>Resolved</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>4</h3>
                                <p>Pending</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>2</h3>
                                <p>Overdue</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Escalation Tracking Table -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Recent Escalations</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Escalation ID</th>
                                            <th>Date Raised</th>
                                            <th>Risk Type</th>
                                            <th>Branch/Department</th>
                                            <th>Status</th>
                                            <th>Response Time</th>
                                            <th>Outcome</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>ESC-2023-001</td>
                                            <td>2023-09-15</td>
                                            <td>Fraud Alert</td>
                                            <td>Branch A</td>
                                            <td><span class="label label-success">Resolved</span></td>
                                            <td>3 days</td>
                                            <td>Investigation Completed</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>ESC-2023-002</td>
                                            <td>2023-09-20</td>
                                            <td>Compliance Breach</td>
                                            <td>Department X</td>
                                            <td><span class="label label-warning">Pending</span></td>
                                            <td>8 days</td>
                                            <td>Awaiting Review</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>ESC-2023-003</td>
                                            <td>2023-09-10</td>
                                            <td>Cash Discrepancy</td>
                                            <td>Branch B</td>
                                            <td><span class="label label-danger">Overdue</span></td>
                                            <td>25 days</td>
                                            <td>Escalated Further</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <!-- Add more rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Response Time Analysis -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Response Time Analysis</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-clock-o"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Avg Response Time</span>
                                                <span class="info-box-number">5.2 Days</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-yellow"><i class="fa fa-warning"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Escalations > 7 Days</span>
                                                <span class="info-box-number">6</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Ignored > 14 Days</span>
                                                <span class="info-box-number">2</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Escalation Outcomes Chart Placeholder -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Escalation Outcomes</h4>
                            </div>
                            <div class="box-body">
                                <p>Pie chart showing escalation outcomes.</p>
                                <div style="height: 300px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Escalation Outcomes Chart</span>
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