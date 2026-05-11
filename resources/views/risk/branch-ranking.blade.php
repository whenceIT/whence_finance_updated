@extends('layouts.master')

@section('title')
    Branch Risk Ranking Index
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Branch Risk Ranking Index</h3>
            </div>
            <div class="box-body">
                <p>This page ranks all branches using weighted institutional risk scoring based on factors such as fraud incidents, delayed deposits, cash discrepancies, portfolio deterioration, ghost client findings, LMS overrides, policy breaches, audit findings, staff turnover, and concentration risks.</p>

                <!-- Filters -->
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-control">
                            <option>All Provinces</option>
                            <option>Province 1</option>
                            <option>Province 2</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Search Branch...">
                    </div>
                    <div class="col-md-4">
                        <button class="btn btn-primary">Filter</button>
                    </div>
                </div>

                <!-- Ranking Table -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Branch Name</th>
                                    <th>Risk Score</th>
                                    <th>Risk Level</th>
                                    <th>Key Risk Factors</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Branch A</td>
                                    <td>85</td>
                                    <td><span class="label label-danger">Critical</span></td>
                                    <td>Fraud Incidents, Cash Discrepancies</td>
                                    <td><button class="btn btn-sm btn-info">View Details</button></td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Branch B</td>
                                    <td>72</td>
                                    <td><span class="label label-warning">High</span></td>
                                    <td>Portfolio Deterioration, Overrides</td>
                                    <td><button class="btn btn-sm btn-info">View Details</button></td>
                                </tr>
                                <!-- Add more rows as needed -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Summary Statistics</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="small-box bg-red">
                                            <div class="inner">
                                                <h3>5</h3>
                                                <p>Critical Branches</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-yellow">
                                            <div class="inner">
                                                <h3>12</h3>
                                                <p>High Risk Branches</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-blue">
                                            <div class="inner">
                                                <h3>25</h3>
                                                <p>Moderate Risk Branches</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="small-box bg-green">
                                            <div class="inner">
                                                <h3>58</h3>
                                                <p>Low Risk Branches</p>
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