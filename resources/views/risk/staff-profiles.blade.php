@extends('layouts.master')

@section('title')
    Staff Risk Profiling Engine
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Staff Risk Profiling Engine</h3>
            </div>
            <div class="box-body">
                <p>The system progressively builds staff risk profiles using indicators such as excessive overrides, repeated delayed banking, high client complaint frequency, irregular transaction patterns, repeated disciplinary issues, and unusual operational behaviour.</p>

                <!-- Risk Profile Filters -->
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>Risk Level</option>
                            <option>High Risk</option>
                            <option>Medium Risk</option>
                            <option>Low Risk</option>
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
                        <select class="form-control">
                            <option>All Departments</option>
                            <option>Loans</option>
                            <option>Operations</option>
                            <option>Recovery</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" placeholder="Search Staff Name...">
                    </div>
                </div>

                <!-- High Risk Staff Table -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>High Risk Staff Profiles</h4>
                            </div>
                            <div class="box-body">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Staff ID</th>
                                            <th>Name</th>
                                            <th>Branch</th>
                                            <th>Risk Score</th>
                                            <th>Risk Factors</th>
                                            <th>Last Activity</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>EMP001</td>
                                            <td>John Doe</td>
                                            <td>Branch A</td>
                                            <td><span class="label label-danger">95</span></td>
                                            <td>Excessive Overrides, Delayed Banking</td>
                                            <td>2023-09-28</td>
                                            <td><button class="btn btn-sm btn-warning">Review Profile</button></td>
                                        </tr>
                                        <tr>
                                            <td>EMP002</td>
                                            <td>Jane Smith</td>
                                            <td>Branch B</td>
                                            <td><span class="label label-danger">88</span></td>
                                            <td>Irregular Transactions, Complaints</td>
                                            <td>2023-09-27</td>
                                            <td><button class="btn btn-sm btn-warning">Review Profile</button></td>
                                        </tr>
                                        <tr>
                                            <td>EMP003</td>
                                            <td>Bob Johnson</td>
                                            <td>Branch C</td>
                                            <td><span class="label label-warning">72</span></td>
                                            <td>Repeated Reversals, Disciplinary</td>
                                            <td>2023-09-25</td>
                                            <td><button class="btn btn-sm btn-warning">Review Profile</button></td>
                                        </tr>
                                        <!-- Add more rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Indicators Summary -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Risk Indicators Summary</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-red"><i class="fa fa-user-times"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">High Risk Staff</span>
                                                <span class="info-box-number">12</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-yellow"><i class="fa fa-user"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Medium Risk Staff</span>
                                                <span class="info-box-number">28</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-blue"><i class="fa fa-users"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Low Risk Staff</span>
                                                <span class="info-box-number">145</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="info-box">
                                            <span class="info-box-icon bg-green"><i class="fa fa-shield"></i></span>
                                            <div class="info-box-content">
                                                <span class="info-box-text">Monitored This Month</span>
                                                <span class="info-box-number">8</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Profile Details Modal Placeholder -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Staff Risk Profile Details</h4>
                            </div>
                            <div class="box-body">
                                <p>Click "Review Profile" to view detailed risk indicators for individual staff members.</p>
                                <!-- Profile details would be shown in a modal or separate view -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection