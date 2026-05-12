@extends('layouts.master')

@section('title')
    Risk Management Overview
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Risk Management Dashboard Overview</h3>
            </div>
            <div class="box-body">
                <p>Welcome to the Risk Management Overview. This page provides a high-level summary of all risk-related activities and metrics.</p>

                <!-- Placeholder for key metrics -->
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-red"><i class="fa fa-warning"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Critical Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-yellow"><i class="fa fa-exclamation-triangle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">High Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-info-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Moderate Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-check-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Low Risks</span>
                                <span class="info-box-number">0</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder for charts or additional content -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-solid">
                            <div class="box-header">
                                <h3 class="box-title">Risk Trends</h3>
                            </div>
                            <div class="box-body">
                                <p>Chart placeholder for risk trends over time.</p>
                                <!-- Insert chart here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection