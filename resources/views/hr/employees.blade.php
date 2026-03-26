@extends('layouts.master')

@section('title', 'Employee Records')

@section('content')
<section class="content-header">
    <h1>
        Employee Records
        <small>Past and Present Employees</small>
    </h1>
</section>

<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <form method="GET" action="{{ url('hr/employees') }}">
                <div class="row">
                    <div class="col-md-10">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Search by name, office, role, gender, or employment status..."
                            value="{{ request('search') }}"
                        >
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="box-body">
            @if($employees->count())
                <div class="row">
                    @foreach($employees as $employee)
                        <div class="col-md-4">
                            <div class="box box-widget widget-user-2">
                                <div class="widget-user-header bg-light-blue">
                                    <div class="widget-user-image">
                                        @if($employee->image)
                                            <img class="img-circle" src="{{ asset($employee->image) }}" alt="{{ $employee->full_name }}">
                                        @else
                                            <img class="img-circle" src="{{ asset('images/default-employee-icon.jpeg') }}" alt="Default Image">
                                        @endif
                                    </div>

                                    <h3 class="widget-user-username">
                                        {{ $employee->first_name }}
                                         {{ $employee->last_name }}
                                    </h3>
                                    <h5 class="widget-user-desc">
                                        {{ optional($employee->role)->name ?? 'No Role' }}
                                    </h5>
                                </div>

                                <div class="box-footer no-padding">
                                    <ul class="nav nav-stacked">
                                        <li><a href="#">Office <span class="pull-right badge bg-blue">{{ optional($employee->office)->name ?? 'N/A' }}</span></a></li>
                                        <li><a href="#">Gender <span class="pull-right badge bg-green">{{ $employee->gender ?? 'N/A' }}</span></a></li>
                                        <li><a href="#">Status <span class="pull-right badge bg-maroon">{{ $employee->employment_status ?? 'N/A' }}</span></a></li>
                                    </ul>
                                </div>

                                <div class="box-footer text-center">
                                    <a href="{{ url('hr/'.$employee->id.'/employee') }}" class="btn btn-primary btn-sm">
                                        <i class="fa fa-user"></i> Open Dashboard
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-center">
                    {{ $employees->appends(['search' => request('search')])->links() }}
                </div>
            @else
                <div class="alert alert-warning">
                    No employee records found.
                </div>
            @endif
        </div>
    </div>
</section>
@endsection