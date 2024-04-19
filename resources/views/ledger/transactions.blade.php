@extends('layouts.master')

@section('title', 'Ledger Summary')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <!----->

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Branch Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ledgerEntriesByOffice as $officeName => $ledgerEntry)
                                <tr>
                                    <td>{{ $officeName }}</td>
                                    <td>
                                        <a href="{{ route('ledger.show', ['officeName' => $officeName]) }}" class="btn btn-primary">View Ledger</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
