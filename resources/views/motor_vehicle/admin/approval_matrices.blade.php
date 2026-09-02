@extends('layouts.master')

@section('title', 'Approval Matrices')

@section('content')

<section class="content-header">
    <h1>Approval Matrices</h1>
</section>

<section class="content">

    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Create New Approval Matrix</h3>
        </div>
        <form method="POST" action="{{ route('motor-vehicle.approval-matrices.store') }}">
            @csrf
            <div class="box-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Min Amount</label>
                            <input type="number" step="0.01" name="min_amount" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Max Amount</label>
                            <input type="number" step="0.01" name="max_amount" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Level</label>
                            <select name="level" class="form-control" required>
                                <option value="">Select Level</option>
                                <option value="1">Level 1</option>
                                <option value="2">Level 2</option>
                                <option value="3">Level 3</option>
                                <option value="4">Level 4</option>
                                <option value="5">Level 5</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Approver</label>
                            <select name="required_approver_id" class="form-control" required>
                                <option value="">Select Approver</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Branch</label>
                            <select name="branch_id" class="form-control">
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>District</label>
                            <select name="district_id" class="form-control">
                                <option value="">Select District</option>
                                @foreach($districts as $district)
                                    <option value="{{ $district->id }}">{{ $district->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Province</label>
                            <select name="province_id" class="form-control">
                                <option value="">Select Province</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province->id }}">{{ $province->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>

    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">Approval Matrices</h3>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Min Amount</th>
                        <th>Max Amount</th>
                        <th>Level</th>
                        <th>Approver</th>
                        <th>Branch</th>
                        <th>District</th>
                        <th>Province</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($matrices as $matrix)
                        <tr>
                            <td>{{ $matrix->name }}</td>
                            <td>{{ number_format($matrix->min_amount, 2) }}</td>
                            <td>{{ number_format($matrix->max_amount, 2) }}</td>
                            <td>{{ $matrix->level }}</td>
                            <td>{{ $matrix->approver ? $matrix->approver->first_name . ' ' . $matrix->approver->last_name : '-' }}</td>
                            <td>{{ $matrix->branch ? $matrix->branch->name : '-' }}</td>
                            <td>{{ $matrix->district ? $matrix->district->name : '-' }}</td>
                            <td>{{ $matrix->province ? $matrix->province->name : '-' }}</td>
                            <td>
                                @if($matrix->active)
                                    <span class="label label-success">Yes</span>
                                @else
                                    <span class="label label-danger">No</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No approval matrices found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>

@endsection
