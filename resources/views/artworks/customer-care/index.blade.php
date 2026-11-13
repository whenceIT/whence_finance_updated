@extends('layouts.master')
@section('title')
    Customer Care Cases
@endsection
@section('content')

    <section class="content">
        @if (Session::has('success'))
            <div class="alert alert-success">{{ Session::get('success') }}</div>
        @endif
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Cases List</h3>
                <div class="box-tools">
                    <a href="{{ route('customer-care.create') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus"></i> Register Case</a>
                </div>
            </div>
            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Case Reference</th>
                                <th>Client Name</th>
                                <th>NRC</th>
                                <th>Complaint Type</th>
                                <th>Status</th>
                                <th>Date Reported</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cases as $key => $case)
                                <tr>
                                    <td>{{ $cases->firstItem() + $key }}</td>
                                    <td><strong>{{ $case->case_reference }}</strong></td>
                                    <td>{{ $case->client_name }}</td>
                                    <td>{{ $case->nrc_number }}</td>
                                    <td>{{ $case->complaint_type }}</td>
                                    <td>
                                        <span class="label @if($case->status == 'Resolved') label-success @elseif($case->status == 'Escalated') label-danger @elseif($case->status == 'In Progress') label-warning @else label-info @endif">
                                            {{ $case->status }}
                                        </span>
                                    </td>
                                    <td>{{ date('d-M-Y', strtotime($case->date_reported)) }}</td>
                                    <td>
                                        <a href="{{ route('customer-care.show', $case->id) }}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No cases found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    {{ $cases->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
