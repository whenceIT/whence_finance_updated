@extends('layouts.master')

@section('title')
    Risk Cost vs Value Preservation Analytics
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Risk Cost vs Value Preservation Analytics</h3>
            </div>
            <div class="box-body">
                <p>This dashboard quantifies losses prevented, recovery achieved, cost of the risk department, net institutional value preserved, and ROI of risk interventions.</p>

                <!-- Key Financial Metrics -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>K 5,200,000</h3>
                                <p>Losses Prevented</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-shield"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>K 3,800,000</h3>
                                <p>Recovery Achieved</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-dollar"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>K 450,000</h3>
                                <p>Cost of Risk Department</p>
                            </div>
                            <div class="icon">
                                <i class="fa fa-money"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Value Preservation Metrics -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Net Institutional Value Preserved</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h2 style="text-align: center; color: green;">K 8,550,000</h2>
                                        <p style="text-align: center;">Total value preserved this year</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>ROI of Risk Interventions</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h2 style="text-align: center; color: blue;">1,790%</h2>
                                        <p style="text-align: center;">Return on investment</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cost Breakdown -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Risk Department Cost Breakdown</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Cost Category</th>
                                            <th>Monthly Cost</th>
                                            <th>Annual Cost</th>
                                            <th>Percentage of Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Personnel Costs</td>
                                            <td>K 25,000</td>
                                            <td>K 300,000</td>
                                            <td>67%</td>
                                        </tr>
                                        <tr>
                                            <td>Technology & Tools</td>
                                            <td>K 8,000</td>
                                            <td>K 96,000</td>
                                            <td>21%</td>
                                        </tr>
                                        <tr>
                                            <td>Training & Development</td>
                                            <td>K 3,500</td>
                                            <td>K 42,000</td>
                                            <td>9%</td>
                                        </tr>
                                        <tr>
                                            <td>Other Expenses</td>
                                            <td>K 1,500</td>
                                            <td>K 18,000</td>
                                            <td>4%</td>
                                        </tr>
                                        <tr style="font-weight: bold;">
                                            <td>Total</td>
                                            <td>K 38,000</td>
                                            <td>K 456,000</td>
                                            <td>100%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Value Preservation Chart Placeholder -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Value Preservation Trends</h4>
                            </div>
                            <div class="box-body">
                                <p>Chart showing value preservation and ROI trends over time.</p>
                                <div style="height: 300px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Value Preservation Chart</span>
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