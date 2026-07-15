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

                    <table class="table table-bordered table-striped">

                        <thead>

                        <tr>

                            <th>ID</th>
                            <th>Client</th>
                            <th>Office</th>
                            <th>Principal</th>
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

                                    @if($loan->status == 'active')
                                        <span class="label label-success">
                                            Active
                                        </span>
                                    @elseif($loan->status == 'defaulted')
                                        <span class="label label-danger">
                                            Defaulted
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

                       <div class="text-center">
        {{ $recentLoans->links() }}
    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
