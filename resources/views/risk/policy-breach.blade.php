@extends('layouts.master')

@section('title')
    Policy Breach Intelligence Dashboard
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Policy Breach Intelligence Dashboard</h3>
            </div>
            <div class="box-body">
                <p>This system tracks and analyzes policy exceptions and breaches institution-wide, including most violated policies, repeat offenders, branches with chronic violations, product-based policy breaches, escalation delays, and exception regularization timelines.</p>

                <!-- Top Violated Policies -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Most Violated Policies</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Loan Approval Limits <span class="badge">45 violations</span></li>
                                    <li class="list-group-item">Collateral Valuation <span class="badge">32 violations</span></li>
                                    <li class="list-group-item">Client Due Diligence <span class="badge">28 violations</span></li>
                                    <li class="list-group-item">Transaction Reversals <span class="badge">21 violations</span></li>
                                    <li class="list-group-item">Documentation Requirements <span class="badge">18 violations</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Repeat Offenders -->
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Repeat Offenders</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">User A (Branch X) <span class="badge">12 violations</span></li>
                                    <li class="list-group-item">User B (Branch Y) <span class="badge">9 violations</span></li>
                                    <li class="list-group-item">User C (Branch Z) <span class="badge">8 violations</span></li>
                                    <li class="list-group-item">User D (Branch W) <span class="badge">7 violations</span></li>
                                    <li class="list-group-item">User E (Branch V) <span class="badge">6 violations</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branches with Chronic Violations -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Branches with Chronic Violations</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Branch</th>
                                            <th>Total Violations</th>
                                            <th>Most Common Violation</th>
                                            <th>Last Violation Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Branch A</td>
                                            <td>25</td>
                                            <td>Loan Approval Limits</td>
                                            <td>2023-09-28</td>
                                            <td><span class="label label-danger">Under Review</span></td>
                                        </tr>
                                        <tr>
                                            <td>Branch B</td>
                                            <td>18</td>
                                            <td>Collateral Valuation</td>
                                            <td>2023-09-25</td>
                                            <td><span class="label label-warning">Monitoring</span></td>
                                        </tr>
                                        <!-- Add more rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Escalation Delays -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Escalation Delays Analysis</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-red"><i class="fa fa-clock-o"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Avg Escalation Delay</span>
                                                <span class="info-box-number">15 Days</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Pending Escalations</span>
                                                <span class="info-box-number">8</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Resolved This Month</span>
                                                <span class="info-box-number">23</span>
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