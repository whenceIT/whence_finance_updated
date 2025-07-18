@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10 border rounded p-4 bg-white shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">Company Policies</h2>
                <!-- <a href="{{ route('policies.add_policies') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Policy
                </a> -->
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    @if($policies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col" class="ps-3">Policy Title</th>
                                        <th scope="col">File Type</th>
                                        <th scope="col">File Size</th>
                                        <th scope="col" class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($policies as $policy)
                                        <tr>
                                            <td class="ps-3">{{ $policy->title }}</td>
                                            <td>{{ strtoupper(pathinfo($policy->file_name, PATHINFO_EXTENSION)) }}</td>
                                            <td>{{ round($policy->file_size / 1024, 2) }} KB</td>
                                            <td class="text-center">
                                                
                                                <a href="{{ $policy->file_url }}" download class="btn btn-sm btn-success" title="Download">
                                                    <i></i> Download
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info m-3" role="alert">
                            No policies found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
