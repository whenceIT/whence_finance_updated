@extends('layouts.master')

@section('title')
    Enterprise Risk Heat Map
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Enterprise Risk Heat Map</h3>
                <div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                </div>
            </div>
            <div class="box-body">
                <p>This dashboard displays the institutional risk heat map showing risk exposure levels across Fraud Risk, Delinquency Risk, Operational Risk, Compliance Risk, and Technology/System Risk.</p>

                <!-- Filters -->
                <div class="row">
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Branches</option>
                            <option>Branch A</option>
                            <option>Branch B</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Provinces</option>
                            <option>Province 1</option>
                            <option>Province 2</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Products</option>
                            <option>Product A</option>
                            <option>Product B</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control">
                            <option>All Departments</option>
                            <option>Department A</option>
                            <option>Department B</option>
                        </select>
                    </div>
                </div>

                <!-- Heat Map Placeholder -->
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-body">
                                <p>Interactive heat map visualization will be displayed here.</p>
                                <!-- Placeholder for heat map -->
                                <div style="height: 400px; background-color: #f5f5f5; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center;">
                                    <span>Heat Map Visualization</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Levels Legend -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h4>Risk Grading Legend</h4>
                            </div>
                            <div class="box-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div style="background-color: #d9534f; color: white; padding: 10px; text-align: center;">Critical</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="background-color: #f0ad4e; color: white; padding: 10px; text-align: center;">High</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="background-color: #5bc0de; color: white; padding: 10px; text-align: center;">Moderate</div>
                                    </div>
                                    <div class="col-md-3">
                                        <div style="background-color: #5cb85c; color: white; padding: 10px; text-align: center;">Low</div>
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