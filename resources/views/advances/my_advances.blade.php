@extends('layouts.master')

@section('title')
    My Advances
@endsection


@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">My Advances</h3>
    </div>    
    <div class="box-body table-responsive">
        @if ($advances->isEmpty())
            <p>No advances found.</p>
        @else
            <table class="table table-bordered table-hover table-striped" id="data-table">     
                <thead>
                    <tr>
                        <th scope="col">Name</th>
                        <th scope="col">Amount Requested</th>
                        <th scope="col">Installment Amount</th>
                        <th scope="col">Installments</th>
                        <th scope="col">Next Payment Date</th>
                        <th scope="col">Amount Paid</th>
                        <th scope="col">Balance</th>
                        <th scope="col">Approved On</th>
                        <th scope="col">Actions</th> 
                    </tr>
                </thead>
                <tbody>
                    @foreach ($advances as $advance)
                        <tr>
                            <td>{{ $advance->first_name }} {{ $advance->last_name }}</td>
                            <td>{{ $advance->amount }}</td>
                            <td>{{ $advance->installment_amount }}</td>
                            <td>{{ $advance->installments }}</td>
                            <td>{{ $advance->expected_repayment_dates }}</td>
                            <td>{{ $advance->amount_paid }}</td>
                            <td>{{ $advance->remaining_amount}}</td>
                            <td>{{ $advance->date_approved}}</td>
                            <td>
                            
                                <button class="btn btn-primary" onclick="toggleTopUpForm({{ $advance->id }})">Top-up</button>

                                
                                <form id="topUpForm{{ $advance->id }}" style="display:none;" action="{{ route('advances.submitTopUp', $advance->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="top_up_amount">Top-up Amount</label>
                                        <input type="number" name="top_up_amount" class="form-control" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="top_up_date">Top-up Date</label>
                                        <input type="date" name="top_up_date" class="form-control" required>
                                    </div>
                                    
                                    
                                        <div class="form-group">
                                            <label for="installments">Increase Installments (Max: 3)</label>
                                            <select name="installments" class="form-control">
                                                @for($i = $advance->installments; $i <= 3; $i++)
                                                    <option value="{{ $i }}" {{ $i == $advance->installments ? 'selected' : '' }}>
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>

                                        </div>
                                    

                                    <button type="submit" class="btn btn-success">Submit Top-up</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                </tbody>
            </table> 
            
            
            @foreach ($advances as $advance)
                <div class="alert 
                    @if ($advance->status == 'approved') alert-success
                    @elseif ($advance->status == 'pending') alert-warning
                    @elseif ($advance->status == 'declined') alert-danger
                    @endif" role="alert">
                    <strong>Status: </strong>
                    @if ($advance->status == 'approved')
                        Salary Advance Approved
                    @elseif ($advance->status == 'pending')
                        Waiting for Salary Advance Approval
                    @elseif ($advance->status == 'declined')
                        Salary Advance has been declined.
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection

<script>
    function toggleTopUpForm(advanceId) {
        var form = document.getElementById('topUpForm' + advanceId);
        if (form.style.display === 'none') {
            form.style.display = 'block';
        } else {
            form.style.display = 'none';
        }
    }
</script>



