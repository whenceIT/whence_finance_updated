@extends('layouts.master')

@section('title')
    Geographic Risk Intelligence
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Geographic Risk Intelligence</h3>
            </div>
            <div class="box-body">
                <p>The system visually maps fraud hotspots, delinquency hotspots, high-cash-risk areas, weak recovery regions, and rapid growth/high-risk zones.</p>

                <!-- Map Filters -->
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>Risk Type</option>
                            <option>Fraud Hotspots</option>
                            <option>Delinquency Hotspots</option>
                            <option>Cash Risk Areas</option>
                            <option>Recovery Weakness</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>Region</option>
                            <option>Northern Region</option>
                            <option>Southern Region</option>
                            <option>Eastern Region</option>
                            <option>Western Region</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="From Date">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" placeholder="To Date">
                    </div>
                </div>

                <!-- Geographic Map Placeholder -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Risk Geographic Map</h4>
                            </div>
                            <div class="box-body">
                                <div style="height: 500px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Interactive Geographic Risk Map</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Hotspots Summary -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Fraud Hotspots</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Lusaka Central <span class="badge bg-red">High</span></li>
                                    <li class="list-group-item">Kitwe <span class="badge bg-yellow">Medium</span></li>
                                    <li class="list-group-item">Ndola <span class="badge bg-orange">Medium</span></li>
                                    <li class="list-group-item">Livingstone <span class="badge bg-red">High</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Delinquency Hotspots</h4>
                            </div>
                            <div class="box-body">
                                <ul class="list-group">
                                    <li class="list-group-item">Copperbelt Province <span class="badge bg-red">High</span></li>
                                    <li class="list-group-item">Lusaka Province <span class="badge bg-yellow">Medium</span></li>
                                    <li class="list-group-item">Southern Province <span class="badge bg-orange">Medium</span></li>
                                    <li class="list-group-item">Northern Province <span class="badge bg-green">Low</span></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recovery Performance by Region -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Recovery Performance by Region</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Region</th>
                                            <th>Recovery Rate</th>
                                            <th>Avg Recovery Time</th>
                                            <th>Risk Level</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Northern</td>
                                            <td>85%</td>
                                            <td>45 days</td>
                                            <td><span class="label label-success">Low</span></td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>Copperbelt</td>
                                            <td>65%</td>
                                            <td>78 days</td>
                                            <td><span class="label label-warning">Medium</span></td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>Lusaka</td>
                                            <td>72%</td>
                                            <td>62 days</td>
                                            <td><span class="label label-warning">Medium</span></td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
                                        </tr>
                                        <tr>
                                            <td>Southern</td>
                                            <td>58%</td>
                                            <td>95 days</td>
                                            <td><span class="label label-danger">High</span></td>
                                            <td><button class="btn btn-sm btn-info">View Details</button></td>
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