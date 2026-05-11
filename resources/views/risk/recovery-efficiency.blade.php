@extends('layouts.master')

@section('title')
    Recovery Efficiency Tracker
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Recovery Efficiency Tracker</h3>
            </div>
            <div class="box-body">
                <p>This dashboard monitors amount detected, amount recovered, recovery percentage, outstanding exposure, recovery ageing, and open investigations.</p>

                <!-- Key Metrics -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3>K 1,250,000</h3>
                                <p>Amount Detected</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3>K 950,000</h3>
                                <p>Amount Recovered</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3>76%</h3>
                                <p>Recovery Percentage</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3>K 300,000</h3>
                                <p>Outstanding Exposure</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h3>45</h3>
                                <p>Open Investigations</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h3>120 Days</h3>
                                <p>Avg Recovery Time</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recovery Ageing Analysis -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Recovery Ageing Analysis</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Age Bracket</th>
                                            <th>Outstanding Amount</th>
                                            <th>Number of Cases</th>
                                            <th>Recovery Rate</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>0-30 Days</td>
                                            <td>K 50,000</td>
                                            <td>5</td>
                                            <td>90%</td>
                                        </tr>
                                        <tr>
                                            <td>31-60 Days</td>
                                            <td>K 80,000</td>
                                            <td>8</td>
                                            <td>75%</td>
                                        </tr>
                                        <tr>
                                            <td>61-90 Days</td>
                                            <td>K 100,000</td>
                                            <td>12</td>
                                            <td>60%</td>
                                        </tr>
                                        <tr>
                                            <td>91+ Days</td>
                                            <td>K 70,000</td>
                                            <td>20</td>
                                            <td>40%</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recovery Trends Chart Placeholder -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Recovery Trends</h4>
                            </div>
                            <div class="box-body">
                                <p>Chart showing recovery trends over time.</p>
                                <div style="height: 300px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Recovery Trends Chart</span>
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