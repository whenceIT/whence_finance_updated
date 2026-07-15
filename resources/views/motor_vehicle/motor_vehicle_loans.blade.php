@extends('layouts.master')
@section('title')
    Motor Vehicle Loans
@endsection 
@section('content')  
   <div class="row">

        <div class="col-md-12">

            <div class="box box-success">

                <div class="box-header with-border">
                    <h3 class="box-title">
                        Motor Vehicle Loans
                    </h3>
                </div>

                <div class="box-body table-responsive">

                <div class="box-body">

<form method="GET">

<div class="row">

<div class="col-md-3">
    <input type="text"
           class="form-control"
           name="search"
           placeholder="Search Loan ID or Client"
           value="{{ request('search') }}">
</div>

<div class="col-md-2">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>

        <option value="pending"
            {{ request('status')=='pending' ? 'selected' : '' }}>
            Pending
        </option>

        <option value="approved"
            {{ request('status')=='approved' ? 'selected' : '' }}>
            Approved
        </option>

        <option value="disbursed"
            {{ request('status')=='disbursed' ? 'selected' : '' }}>
            Disbursed
        </option>

        <option value="closed"
            {{ request('status')=='closed' ? 'selected' : '' }}>
            Closed
        </option>

    </select>
</div>

<div class="col-md-2">
    <input type="date"
           class="form-control"
           name="date"
           value="{{ request('date') }}">
</div>

<div class="col-md-3">

    <select name="office" class="form-control">

        <option value="">All Branches</option>

        @foreach($offices as $office)

            <option value="{{ $office->id }}"
                {{ request('office')==$office->id ? 'selected' : '' }}>
                {{ $office->name }}
            </option>

        @endforeach

    </select>

</div>

<div class="col-md-2">

    <button class="btn btn-success">
        <i class="fa fa-search"></i> Search
    </button>

    <a href="{{ url()->current() }}"
       class="btn btn-default">
       Reset
    </a>

</div>

</div>

</form>

</div>

                    <table class="table table-bordered table-striped">

                        <thead>

                        <tr>

                            <th>ID</th>
                            <th>Client</th>
                            <th>Office</th>
                            <th>Principal</th>
                            <th>Created Date</th>
                            <th>Status</th>

                        </tr>

                        </thead>

                        <tbody>

                        @forelse($recentLoans as $loan)

                            <tr>

                                <td>
                               <a href="{{ url('loan/'.$loan->id.'/show') }}">
                                               {{$loan->id}}</a>
                                </td>

                                <td>
                                    {{ optional($loan->client)->first_name }}
                                    {{ optional($loan->client)->last_name }}
                                </td>

                                    <td>
                                    {{ optional($loan->office)->name }}
                                </td>


                                <td>
                                    K{{ number_format($loan->approved_amount,2) }}
                                </td>

                                   <td>
                                    {{$loan->created_date}}
                                </td>


                                <td>

                                    @if($loan->status == 'disbursed')
                                        <span class="label label-success">
                                            Disbursed
                                        </span>
                                    @elseif($loan->status == 'closed')
                                        <span class="label label-danger">
                                            Closed
                                        </span>
                                    @else
                                        <span class="label label-warning">
                                            {{ ucfirst($loan->status) }}
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="text-center">
                                    No vehicle loans found.
                                </td>

                            </tr>

                        @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>
@endsection
