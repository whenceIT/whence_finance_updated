@extends('layouts.master')

@section('title')
Workforce Analytics
@endsection

@section('content')

<div class="row">

    {{-- ================= Diversity ================= --}}
    <div class="col-md-12">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Diversity & Inclusion</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr style="background:#1f2d3d; color:white;">
                            <th>Gender</th>
                            <th>Count</th>
                            <th>Percentage (%)</th>
                            <th>Target</th>
                            <th>Gap</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diversity['breakdown'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['gender'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['percentage'] }}%</td>
                            <td>50%</td>
                            <td>{{ $row['difference_from_target'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= Tenure ================= --}}
    <div class="col-md-12">
        <div class="box box-success">
            <div class="box-header with-border">
                <h3 class="box-title">Tenure & Stability</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr style="background:#00a65a; color:white;">
                            <th>Band</th>
                            <th>Count</th>
                            <th>Percentage (%)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenure['breakdown'] ?? [] as $row)
                        <tr>
                            <td>{{ $row['band'] }}</td>
                            <td>{{ $row['count'] }}</td>
                            <td>{{ $row['percentage'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= Office Insights ================= --}}
    <div class="col-md-12">
        <div class="box box-info">
            <div class="box-header with-border">
                <h3 class="box-title">Office Workforce Insights</h3>
            </div>
            <div class="box-body table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr style="background:#00c0ef; color:white;">
                            <th>Office</th>
                            <th>Total Users</th>
                            <th>Avg Tenure</th>
                            <th>Male %</th>
                            <th>Female %</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($offices ?? [] as $row)
                        <tr>
                            <td>{{ $row['office_name'] }}</td>
                            <td>{{ $row['total_users'] }}</td>
                            <td>{{ $row['avg_tenure_years'] ?? 0 }}</td>
                            <td>{{ $row['male_percentage'] ?? 0 }}%</td>
                            <td>{{ $row['female_percentage'] ?? 0 }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@endsection