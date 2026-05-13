@extends('layouts.master')

@section('title')
    Decision SLA Tracking
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Decision SLA Tracking</h3>
            </div>
            <div class="box-body">
                <p>This dashboard tracks decision-making SLAs, including time to decision, decision quality metrics, escalation rates, and SLA compliance rates.</p>

                <!-- SLA Metrics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>92%</h3>
                                <p>SLA Compliance Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>3.2 Days</h3>
                                <p>Avg Decision Time</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>8%</h3>
                                <p>Escalation Rate</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-arrow-up"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>15</h3>
                                <p>Overdue Decisions</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLA Tracking Table -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Decision SLA Tracking</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Decision ID</th>
                                            <th>Risk Type</th>
                                            <th>Decision Level</th>
                                            <th>Time to Decision</th>
                                            <th>SLA Status</th>
                                            <th>Quality Score</th>
                                            <th>Outcome</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>DEC-2023-001</td>
                                            <td>Fraud Investigation</td>
                                            <td>Manager</td>
                                            <td>2 days</td>
                                            <td><span class="label label-success">Within SLA</span></td>
                                            <td>9/10</td>
                                            <td>Approved</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>DEC-2023-002</td>
                                            <td>Compliance Breach</td>
                                            <td>Director</td>
                                            <td>5 days</td>
                                            <td><span class="label label-warning">Near SLA</span></td>
                                            <td>8/10</td>
                                            <td>Escalated</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>DEC-2023-003</td>
                                            <td>Cash Discrepancy</td>
                                            <td>Executive</td>
                                            <td>12 days</td>
                                            <td><span class="label label-danger">Over SLA</span></td>
                                            <td>7/10</td>
                                            <td>Rejected</td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <!-- Add more rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SLA Performance by Decision Level -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>SLA Performance by Decision Level</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Decision Level</th>
                                            <th>Total Decisions</th>
                                            <th>Within SLA</th>
                                            <th>SLA Compliance %</th>
                                            <th>Avg Time</th>
                                            <th>Escalation Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Manager</td>
                                            <td>145</td>
                                            <td>138</td>
                                            <td>95%</td>
                                            <td>2.1 days</td>
                                            <td>3%</td>
                                        </tr>
                                        <tr>
                                            <td>Director</td>
                                            <td>67</td>
                                            <td>62</td>
                                            <td>93%</td>
                                            <td>4.5 days</td>
                                            <td>8%</td>
                                        </tr>
                                        <tr>
                                            <td>Executive</td>
                                            <td>23</td>
                                            <td>19</td>
                                            <td>83%</td>
                                            <td>8.2 days</td>
                                            <td>17%</td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td>Total</td>
                                            <td>235</td>
                                            <td>219</td>
                                            <td>93%</td>
                                            <td>3.2 days</td>
                                            <td>8%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Decision Quality Trends Chart Placeholder -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Decision Quality Trends</h4>
                            </div>
                            <div class="box-body">
                                <p>Chart showing decision quality scores and SLA compliance over time.</p>
                                <div style="height: 300px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Decision Quality Trends Chart</span>
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