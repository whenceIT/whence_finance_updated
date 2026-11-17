
@extends('layouts.master')

@section('content')

<section class="content-header">
    <h1>
        Administration Payroll Management
        <small>Manage employees paid under Administration</small>
    </h1>
    <ol class="breadcrumb">
        <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Administration Payroll</li>
    </ol>
</section>

<section class="content">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fa fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('info') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            {{ session('error') }}
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="row">

        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>{{ $totalAdmins }}</h3>
                    <p>Administration Payroll Employees</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total System Employees</p>
                </div>
                <div class="icon">
                    <i class="fa fa-user"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4 col-sm-6">
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>{{ $totalUsers - $totalAdmins }}</h3>
                    <p>Other Employees</p>
                </div>
                <div class="icon">
                    <i class="fa fa-building"></i>
                </div>
            </div>
        </div>

    </div>

    {{-- SEARCH --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-search"></i> Search Employees
            </h3>
        </div>

        <div class="box-body">
            <form method="GET" action="{{ route('admin-users.index') }}">
                <div class="input-group">
                    <input type="text"
                           name="search"
                           class="form-control"
                           placeholder="Search by employee name or email..."
                           value="{{ $search }}">

                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search"></i> Search
                        </button>
                        <a href="{{ route('admin-users.index') }}"
                           class="btn btn-default">
                            Reset
                        </a>
                    </span>
                </div>
            </form>
        </div>
    </div>

    {{-- ADMINISTRATION PAYROLL EMPLOYEES --}}
    <div class="box box-success">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-users"></i> Administration Payroll Employees
            </h3>

            <div class="box-tools pull-right">
                <span class="label label-success">
                    {{ $totalAdmins }} Employees
                </span>
            </div>
        </div>

        <div class="box-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th width="150">Payroll Category</th>
                        <th width="130">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($adminUsers as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $user->full_name }}</strong>
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->phone }}</td>

                            <td>{{ $user->office->name ?? 'N/A' }}</td>

                            <td>
                                <span class="label label-success">
                                    Administration
                                </span>
                            </td>

                            <td>
                                <form method="POST"
                                      action="{{ route('admin-users.remove', $user->id) }}"
                                      onsubmit="return confirm('Are you sure you want to remove {{ addslashes($user->full_name) }} from Administration Payroll?')">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-danger btn-xs">
                                        <i class="fa fa-times"></i>
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">
                                No employees currently assigned to Administration Payroll.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $adminUsers->appends(request()->except('admins_page'))->links() }}

        </div>
    </div>

    {{-- AVAILABLE EMPLOYEES --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-user-plus"></i> Available Employees
            </h3>

            <div class="box-tools pull-right">
                <span class="label label-primary">
                    {{ $totalUsers - $totalAdmins }} Employees
                </span>
            </div>
        </div>

        <div class="box-body table-responsive">

            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Branch</th>
                        <th width="150">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($availableUsers as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>
                                <strong>{{ $user->full_name }}</strong>
                            </td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->phone }}</td>

                            <td>{{ $user->office->name ?? 'N/A' }}</td>

                            <td>
                                <form method="POST"
                                      action="{{ route('admin-users.add', $user->id) }}"
                                      onsubmit="return confirm('Are you sure you want to add {{ addslashes($user->full_name) }} to Administration Payroll?')">

                                    @csrf

                                    <button type="submit"
                                            class="btn btn-success btn-xs">
                                        <i class="fa fa-plus"></i>
                                        Add to Administration
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No other employees available.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $availableUsers->appends(request()->except('users_page'))->links() }}

        </div>
    </div>

</section>

@endsection
