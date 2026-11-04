@extends('layouts.master')
@section('title')
    Job Applications
@endsection
@section('content')

    <section class="content">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Applications List</h3>
                <div class="box-tools">
                    <a href="{{ route('job-applications.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Register Applicant</a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>NRC Number</th>
                                <th>Full Name</th>
                                <th>Contact</th>
                                <th>Position Applied</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Date Recommended</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($applications as $key => $app)
                                <tr>
                                    <td>{{ $applications->firstItem() + $key }}</td>
                                    <td>{{ $app->nrc_number }}</td>
                                    <td>{{ $app->full_name }}</td>
                                    <td>{{ $app->contact_number }}</td>
                                    <td>{{ $app->position_applied_for }}</td>
                                    <td>{{ $app->recommending_department }}</td>
                                    <td>
                                        <span class="label @if($app->status == 'Approved') label-success @elseif($app->status == 'Rejected') label-danger @elseif($app->status == 'Employed') label-info @elseif($app->status == 'Under Review') label-warning @else label-default @endif">
                                            {{ $app->status }}
                                        </span>
                                    </td>
                                    <td>{{ $app->date_recommended ? date('d-M-Y', strtotime($app->date_recommended)) : date('d-M-Y', strtotime($app->created_at)) }}</td>
                                    <td>
                                        <a href="{{ route('job-applications.show', $app->id) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No applications found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    {{ $applications->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
