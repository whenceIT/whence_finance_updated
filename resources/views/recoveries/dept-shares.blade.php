@extends('layouts.master')

@section('title')
    Department Shares
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-share"></i> Department Shares Summary</h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-blue"><i class="fa fa-share"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Recovery Dept Share</span>
                                <span class="info-box-number">K {{ number_format($totalDeptShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <span class="info-box-icon bg-green"><i class="fa fa-usd"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unit Share</span>
                                <span class="info-box-number">K {{ number_format($totalUnitShare, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header with-border">
                        <h3 class="box-title">Details</h3>
                    </div>
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h4><i class="fa fa-file-text-o"></i> Recoveries Dept Excalated Share</h4>
                                <p class="text-muted">Total amount from recovery cases department share allocation</p>
                                <h2>K {{ number_format($totalDeptShare, 2) }}</h2>
                            </div>
                            <div class="col-md-6">
                                <h4><i class="fa fa-file-text-o"></i> Unit Share</h4>
                                <p class="text-muted">Total amount from unit share allocations</p>
                                <h2>K {{ number_format($totalUnitShare, 2) }}</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection