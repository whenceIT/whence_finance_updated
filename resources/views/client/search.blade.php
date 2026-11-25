
@extends('layouts.master')

@section('content')

<div class="container-fluid">

    {{-- Page Header --}}
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="font-weight-bold mb-1">
                <i class="fas fa-users text-primary mr-2"></i>
                Client Search
            </h4>
            <p class="text-muted mb-0">
                Search for clients across all branches using their name, NRC or phone number.
            </p>
        </div>
    </div>

    {{-- Search Card --}}
    <div class="card card-outline card-primary shadow-sm">

        <div class="card-body p-4">

            <form method="GET" action="{{ route('clients.search') }}">

                <div class="form-group mb-0">

                    <label class="font-weight-bold">
                        Search client
                    </label>

                    <div class="input-group input-group-lg">

                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Enter client name, NRC number or phone number..."
                            value="{{ $search ?? request('search') }}"
                            required
                            autofocus
                        >

                        <div class="input-group-append">

                            <button type="submit" class="btn btn-primary px-4">
                                Search
                            </button>

                        </div>

                    </div>

                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle mr-1"></i>
                        Search covers clients registered at all branches.
                    </small>

                </div>

            </form>

        </div>

    </div>


    {{-- Search Results --}}
    @if(isset($clients))

        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    <div>
                        <h5 class="mb-1 font-weight-bold">
                            Search Results
                        </h5>

                        <small class="text-muted">
                            Clients matching "{{ $search }}"
                        </small>
                    </div>

                    <div class="mt-2 mt-md-0">

                        <span class="badge badge-primary p-2">
                            {{ number_format($clients->total()) }} 
                            {{ $clients->total() == 1 ? 'Client Found' : 'Clients Found' }}
                        </span>

                        <a href="{{ route('clients.search.page') }}"
                           class="btn btn-sm btn-outline-secondary ml-2">
                            <i class="fas fa-times mr-1"></i>
                            Clear Search
                        </a>

                    </div>

                </div>

            </div>


            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover table-striped mb-0">

                        <thead class="bg-light">
                            <tr>
                                <th class="text-muted" style="width: 55px;">#</th>
                                <th>CLIENT NAME</th>
                                <th>NRC NUMBER</th>
                                <th>PRIMARY PHONE</th>
                                <th>SECONDARY PHONE</th>
                                <th>BRANCH</th>
                                <th>STAFF</th>
                                <th class="text-center">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($clients as $client)

                                <tr>

                                    <td class="text-muted align-middle">
                                        {{ $loop->iteration + ($clients->currentPage() - 1) * $clients->perPage() }}
                                    </td>

                                    <td class="align-middle">

                                        <div class="d-flex align-items-center">

                                            <div class="bg-light rounded-circle text-primary d-flex align-items-center justify-content-center mr-2"
                                                 style="width: 38px; height: 38px; min-width: 38px;">
                                                <i class="fas fa-user"></i>
                                            </div>

                                            <div>
                                                <span class="font-weight-bold text-dark">
                                                    {{ $client->first_name }} {{ $client->last_name }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    Client ID: {{ $client->id }}
                                                </small>
                                            </div>

                                        </div>

                                    </td>

                                    <td class="align-middle">
                                        <span class="text-dark">
                                            {{ $client->nrc_number ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        {{ $client->mobile ?? 'N/A' }}
                                    </td>

                                    <td class="align-middle">
                                        {{ $client->phone ?? 'N/A' }}
                                    </td>

                                    <td class="align-middle">
                                        <span class="badge badge-light border">
                                            {{ optional($client->office)->name ?? 'N/A' }}
                                        </span>
                                    </td>

                                    <td class="align-middle">
                                        {{ optional($client->staff)->name ?? 'N/A' }}
                                    </td>

                                    <td class="align-middle text-center">

                                        <a href="{{ url('/clients/'.$client->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye mr-1"></i>
                                            View
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="8" class="text-center py-5">

                                        <div class="mb-3">
                                            <i class="fas fa-search fa-3x text-muted"
                                               style="opacity: 0.3;"></i>
                                        </div>

                                        <h6 class="font-weight-bold">
                                            No clients found
                                        </h6>

                                        <p class="text-muted mb-0">
                                            Try searching with a different name, NRC or phone number.
                                        </p>

                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>


            {{-- Pagination --}}
            @if($clients->hasPages())

                <div class="card-footer bg-white">

                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <small class="text-muted mb-2 mb-md-0">
                            Showing {{ $clients->firstItem() }} to
                            {{ $clients->lastItem() }} of
                            {{ $clients->total() }} clients
                        </small>

                        <div>
                            {{ $clients->links() }}
                        </div>

                    </div>

                </div>

            @endif

        </div>

    @endif

</div>

@endsection
