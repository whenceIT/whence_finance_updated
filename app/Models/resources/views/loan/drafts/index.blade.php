@extends('layouts.master')

@section('title')
    Loan Termination Pending
@endsection

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h6 class="box-title">Loan Termination Pending</h6>
        </div>

        <div class="box-body table-responsive">
        <table id="drafts-table" class="table table-bordered table-hover">
    <thead>
        <tr>
            
            <th>Client</th>
            <th>Branch</th>
            <th>Product</th>
            <th>Outstanding  (ZMW)</th>
            <th>Accrued Interest (ZMW)</th>
            <th>Fees (ZMW)</th>
            <th><strong>Settlement Amount (ZMW)</strong></th>
            <th>Date</th>
            <th>Created By</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
        @foreach($drafts as $draft)
            @php
                // Ensure amounts are numeric and handle null values
                $outstandingAmount = $draft->amount ?? 0;
                $accruedInterest = $draft->accrued_interest ?? 0;
                $fees = $draft->fees ? array_sum(json_decode($draft->fees, true)) : 0;

                // Calculate Total Settlement Amount
                $totalSettlementAmount = $outstandingAmount + $accruedInterest + $fees;
            @endphp

            <tr>
                

                <!-- Client Name -->
                <td>{{ $draft->loan->client->first_name }} {{ $draft->loan->client->last_name }}</td>

                <!-- Branch -->
                <td>{{ $draft->loan->office->name ?? 'N/A' }}</td>

                <!-- Loan Product -->
                <td>{{ $draft->loan->loan_product->name ?? 'N/A' }}</td>

                <!-- Outstanding Amount -->
                <td>{{ number_format($outstandingAmount, 2) }}</td>

                <!-- Accrued Interest -->
                <td>{{ number_format($accruedInterest, 2) }}</td>

                <!-- Fees -->
                <td>{{ number_format($fees, 2) }}</td>

                <!-- Total Settlement Amount -->
                <td><strong>{{ number_format($totalSettlementAmount, 2) }}</strong></td>

                <!-- Termination Date -->
                <td>{{ $draft->termination_date }}</td>

                <!-- Created By -->
                <td>{{ $draft->createdBy->first_name ?? 'N/A' }} {{ $draft->createdBy->last_name ?? '' }}</td>

                <!-- Status -->
                <td><p> {{ $draft->status === 'draft' ? 'Pending' : ucfirst($draft->status) }}</p>
</td>

                <!-- Actions with Button Group -->
                <td>
    <div class="btn-group">
        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Actions <span class="caret"></span>
        </button>

        <ul class="dropdown-menu dropdown-menu-right">
            <!-- View Draft -->
            <li>
                <a href="{{ url('loan/'.$draft->id.'/termination/draft') }}">
                    <i class="fa fa-eye"></i> View
                </a>
            </li>

            @if($draft->status == 'draft')
                <!-- Approve Draft -->
                <li>
                    <form action="{{ url('loan/'.$draft->id.'/withdraw') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-link" onclick="return confirm('Approve this termination?');">
                            <i class="fa fa-check"></i> Approve
                        </button>
                    </form>
                </li>
                <li role="separator" class="divider"></li>
                <!-- Reject Draft -->
                <li>
                    <form action="{{ route('loan.reject', $draft->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-link text-danger" onclick="return confirm('Are you sure you want to reject this draft?');">
                            <i class="fa fa-times"></i> Reject
                        </button>
                    </form>
                </li>
            @else
                <!-- Approved Draft -->
                <li>
                    <button class="btn btn-link" disabled>
                        <i class="fa fa-check"></i> Approved
                    </button>
                </li>
            @endif
        </ul>
    </div>
</td>

            </tr>
        @endforeach
    </tbody>
</table>



        </div>
    </div>
@endsection

@section('footer-scripts')
<script>
    $(document).ready(function() {
        $('#drafts-table').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
        });
    });
</script>
@endsection
