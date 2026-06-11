@extends('layouts.master')
@section('title', 'Expense Approvals')
@include('components.kilo-alert')

@section('content')

<div class="box box-primary">

    <div class="box-header with-border">
        <h3 class="box-title">Expense Approvals</h3>
    </div>

    <div class="box-body">

        <div class="row" style="margin-bottom:15px;">

            <!-- <div class="col-md-6">

                <button type="button"
                        class="btn btn-success btn-sm"
                        id="bulk-approve-btn">
                    <i class="fa fa-check"></i>
                    Approve Selected
                </button>

                <button type="button"
                        class="btn btn-success btn-sm"
                        id="approve-all-btn">
                    <i class="fa fa-check-circle"></i>
                    Approve All
                </button>

            </div> -->

            <div class="col-md-6 text-right">
                <input type="text"
                       id="search-input"
                       class="form-control input-sm"
                       placeholder="Search..."
                       style="width:220px;display:inline-block;">
            </div>

        </div>

    </div>

    <div class="box-body table-responsive">

        <table class="table table-bordered table-striped">

            <thead>
                <tr>
                    <th width="40">
                        <input type="checkbox" id="select-all">
                    </th>
                    <th>Date</th>
                    <th>Office</th>
                    <th>Expense Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Reference Type</th>
                    <th>Reference No.</th>
                    <th>Created By</th>
                    <th>Proof</th>
                    <th>Narration</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($expenses as $expense)

                    <tr>

                        <td>
                            <input type="checkbox"
                                   class="row-select"
                                   value="{{ $expense->id }}">
                        </td>

                        <td>{{ $expense->date }}</td>

                        <td>
                            {{ $expense->office->name ?? 'N/A' }}
                        </td>

                        <td>
                           {{ $expense->type->name ?? 'N/A' }}
                        </td>

                        <td>
                            {{ $expense->name }}
                        </td>

                        <td>
                            {{ number_format($expense->amount,2) }}
                        </td>

                        <td>
                            {{ $expense->deposit_method }}
                        </td>

                        <td>
                            {{ $expense->reference_number }}
                        </td>

                        <td>
                          {{ $expense->createdBy->first_name ?? '' }}
{{ $expense->createdBy->last_name ?? '' }}
                        </td>

                        <td>
                            @if($expense->proof_of_payment)
                                <a href="{{ $expense->proof_of_payment }}"
                                   target="_blank"
                                   class="btn btn-info btn-xs">
                                    View
                                </a>
                            @else
                                N/A
                            @endif
                        </td>

                        <td>

                            @if($expense->status == 'approved')
                                <span class="label label-success">
                                    Approved
                                </span>
                            @elseif($expense->status == 'declined')
                                <span class="label label-danger">
                                    Declined
                                </span>
                            @else
                                <span class="label label-warning">
                                    Pending
                                </span>
                            @endif

                        </td>

                        <td>

                            @if($expense->status != 'approved')

                                <button
                                    type="button"
                                    class="btn btn-success btn-xs approve-btn"
                                    data-url="{{ url('expense/approvals/expense-approvals/'.$expense->id.'/approved') }}"
                                    data-message="Approve this expense?">

                                    <i class="fa fa-check"></i>
                                    Approve

                                </button>

                            @endif

                            @if($expense->status != 'declined')

                                <button
                                    type="button"
                                    class="btn btn-danger btn-xs decline-btn"
                                    data-url="{{ url('approvals/expense-approvals/'.$expense->id.'/declined') }}"
                                    data-message="Decline this expense?">

                                    <i class="fa fa-times"></i>
                                    Decline

                                </button>

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="12" class="text-center">
                            No expenses found.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="text-center">
            {{ $expenses->links() }}
        </div>

    </div>

</div>

<script>

$(document).ready(function(){

    $('#select-all').click(function(){
        $('.row-select').prop('checked', this.checked);
    });

    $('#bulk-approve-btn').click(function(){

        let selected = $('.row-select:checked').map(function(){
            return $(this).val();
        }).get();

        if(selected.length === 0){
            window.KiloAlert.warning('Select at least one expense.');
            return;
        }

        if(confirm('Approve selected expenses?')){

            $.post(
                '{{ url("approvals/expense-approvals/bulk-approve") }}',
                {
                    _token:'{{ csrf_token() }}',
                    ids:selected
                },
                function(response){

                    if(response.success){
                        window.KiloAlert.success(response.message);
                    }else{
                        window.KiloAlert.error(response.message);
                    }

                    setTimeout(function(){
                        location.reload();
                    },1500);

                }
            );

        }

    });

    $('#approve-all-btn').click(function(){

        if(confirm('Approve all pending expenses?')){

            $.post(
                '{{ url("approvals/expense-approvals/approve-all") }}',
                {
                    _token:'{{ csrf_token() }}'
                },
                function(response){

                    if(response.success){
                        window.KiloAlert.success(response.message);
                    }else{
                        window.KiloAlert.error(response.message);
                    }

                    setTimeout(function(){
                        location.reload();
                    },1500);

                }
            );

        }

    });

    $('#search-input').keyup(function(){

        let value = $(this).val().toLowerCase();

        $('tbody tr').filter(function(){

            $(this).toggle(
                $(this).text().toLowerCase().indexOf(value) > -1
            );

        });

    });

    $(document).on('click','.approve-btn,.decline-btn',function(){

        let url = $(this).data('url');
        let message = $(this).data('message');

        if(confirm(message)){

            $.post(url,{
                _token:'{{ csrf_token() }}'
            },function(response){

                if(response.success){
                    window.KiloAlert.success(response.message);
                }else{
                    window.KiloAlert.error(response.message);
                }

                setTimeout(function(){
                    location.reload();
                },1500);

            });

        }

    });

});

</script>

@endsection