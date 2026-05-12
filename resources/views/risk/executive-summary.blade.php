@extends('layouts.master')

@section('title')
    Executive Risk Summary
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Executive Risk Summary</h3>
            </div>
            <div class="box-body">
                <p>This dashboard provides a high-level overview of all risk metrics, trends, and key indicators for executive decision-making.</p>

                <!-- Key Risk Metrics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>K 2,450,000</h3>
                                <p>Total Losses This Month</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>78%</h3>
                                <p>Recovery Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>23</h3>
                                <p>Active Escalations</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>1,450%</h3>
                                <p>Risk ROI</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-chart-line"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Trends Chart Placeholder -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Risk Trends Overview</h4>
                            </div>
                            <div class="box-body">
                                <div style="height: 400px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Risk Trends Chart (Losses, Recovery, Escalations)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Risk Areas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Top Risk Areas</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Fraud Prevention <span class="badge bg-red">High Priority</span></li>
                                    <li class="list-group-item">Operational Controls <span class="badge bg-yellow">Medium Priority</span></li>
                                    <li class="list-group-item">Compliance Monitoring <span class="badge bg-orange">Medium Priority</span></li>
                                    <li class="list-group-item">Staff Risk Profiling <span class="badge bg-red">High Priority</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Recent Achievements</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Reduced fraud losses by 35% <span class="badge bg-green">Success</span></li>
                                    <li class="list-group-item">Improved recovery rate to 78% <span class="badge bg-green">Success</span></li>
                                    <li class="list-group-item">Implemented new monitoring tools <span class="badge bg-blue">Ongoing</span></li>
                                    <li class="list-group-item">Enhanced staff training programs <span class="badge bg-blue">Ongoing</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Executive Actions Required -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Executive Actions Required</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Action Item</th>
                                            <th>Priority</th>
                                            <th>Deadline</th>
                                            <th>Responsible</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Review high-risk staff profiles</td>
                                            <td><span class="label label-danger">High</span></td>
                                            <td>2023-10-05</td>
                                            <td>CEO</td>
                                            <td><span class="label label-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Approve additional risk monitoring budget</td>
                                            <td><span class="label label-warning">Medium</span></td>
                                            <td>2023-10-15</td>
                                            <td>CFO</td>
                                            <td><span class="label label-info">In Progress</span></td>
                                        </tr>
                                        <tr>
                                            <td>Address compliance gaps in Southern region</td>
                                            <td><span class="label label-danger">High</span></td>
                                            <td>2023-10-10</td>
                                            <td>COO</td>
                                            <td><span class="label label-warning">Pending</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection