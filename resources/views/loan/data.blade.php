@extends('layouts.master')
@section('title')
    {{ trans_choice('general.active',1) }} {{ trans_choice('general.loan',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border" style="margin-bottom: 10px;">
            <h3 class="box-title">{{ trans_choice('general.active', 1) }} {{ trans_choice('general.loan', 2) }}</h3>
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('loans.create'))
                    <a href="{{ url('loan/create') }}" class="btn btn-info btn-sm">
                        {{ trans_choice('general.add', 1) }} {{ trans_choice('general.loan', 1) }}
                    </a>
                @endif
            </div>
            <form id="search-form" action="{{ route('loan.data') }}" method="GET" style="display: flex; justify-content: center;">
                <div class="input-group" style="width: 400px; margin-top:15px;">
                    <input id="search-input" type="text" name="query" class="form-control" placeholder="Search by First Name, Last Name or Loan ID" value="{{ $query ?? '' }}">
                    <span class="input-group-btn">
                        <button type="submit" class="btn btn-primary">Search</button>
                        @if(isset($query))
                            <button type="button" class="btn btn-default" id="clear-search">Clear</button>
                        @endif
                    </span>
                </div>
            </form>
        </div>
        <div class="box-body table-responsive">
            @if(isset($query))
                <p>Showing results for: {{ $query }}</p>
            @endif
            @if(isset($loans))
                <table class="table table-bordered table-hover" id="data-table">
                    <thead>
                    <tr>
                        <th>{{ trans_choice('general.account',1) }}#</th>
                        <th>{{ trans_choice('general.branch',1) }}</th>
                        <th>{{ trans_choice('general.client',1) }}</th>
                        <th>{{ trans_choice('general.product',1) }}</th>
                        <th>{{ trans_choice('general.principal',1) }}</th>
                        <th>{{ trans_choice('general.paid',1) }}</th>
                        <th>{{ trans_choice('general.balance',1) }}</th>
                        <th>{{ trans_choice('general.disbursed',1) }} {{ trans_choice('general.on',1) }}</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                @foreach($loans as $key)
                    <?php
                    $principal = 0;
                    $principal_paid = 0;
                    $principal_written_off = 0;
                    $fees = 0;
                    $fees_paid = 0;
                    $penalty = 0;
                    $penalty_paid = 0;
                    $penalty_written_off = 0;
                    $interest_waived = 0;
                    $penalty_waived = 0;
                    $fees_waived = 0;
                    $fees_written_off = 0;
                    $principal_waived = 0;
                    $interest = 0;
                    $interest_paid = 0;
                    $interest_written_off = 0;
                    foreach ($key->repayment_schedules as $schedule) {
                        $principal = $principal + $schedule->principal;
                        $interest = $interest + $schedule->interest;
                        $penalty = $penalty + $schedule->penalty;
                        $fees = $fees + $schedule->fees;
                        $principal_paid = $principal_paid + $schedule->principal_paid;
                        $interest_paid = $interest_paid + $schedule->interest_paid;
                        $penalty_paid = $penalty_paid + $schedule->penalty_paid;
                        $fees_paid = $fees_paid + $schedule->fees_paid;
                        $principal_waived = $principal_waived + $schedule->principal_waived;
                        $interest_waived = $interest_waived + $schedule->interest_waived;
                        $penalty_waived = $penalty_waived + $schedule->penalty_waived;
                        $fees_waived = $fees_waived + $schedule->fees_waived;

                        $principal_written_off = $principal_written_off + $schedule->principal_written_off;
                        $interest_written_off = $interest_written_off + $schedule->interest_written_off;
                        $penalty_written_off = $penalty_written_off + $schedule->penalty_written_off;
                        $fees_written_off = $fees_written_off + $schedule->fees_written_off;

                    }
                    $balance = ($principal - $principal_paid - $principal_waived - $principal_written_off) + ($interest - $interest_paid - $interest_waived - $interest_written_off) + ($fees - $fees_paid - $fees_waived - $fees_written_off) + ($penalty - $penalty_paid - $penalty_waived - $penalty_written_off);
                    $payments = $principal_paid + $interest_paid + $fees_paid + $penalty_paid;
                    ?>
                    <tr>
                        <td><a href="{{ url('loan/'.$key->id.'/show') }}" data-toggle="tooltip" title="Click to view">{{ $key->id }}</a></td>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>
                            @if($key->client_type=="client")
                                @if(!empty($key->client))
                                    @if($key->client->client_type=="individual")
                                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                    @else
                                        {{$key->client->full_name}}
                                    @endif
                                @endif
                            @endif
                            @if($key->client_type=="group")
                                {{$key->group->name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->loan_product))
                                {{$key->loan_product->name}}
                            @endif
                        </td>
                        <td>{{ number_format($key->principal,$key->decimals) }}</td>
                        <td>{{ number_format($payments,$key->decimals) }}</td>
                        <td>{{ number_format($balance,$key->decimals) }}</td>
                        <td>{{ $key->disbursement_date }}</td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('loans.view'))
                                        <li>
                                            <a href="{{ url('loan/'.$key->id.'/show') }}"><i
                                                        class="fa fa-search"></i>
                                                {{ trans_choice('general.detail',2) }}</a>
                                        </li>
                                    @endif
                                    @if($key->status=="pending")
                                        @if(Sentinel::hasAccess('loans.update'))
                                            <li>
                                                <a href="{{ url('loan/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i>
                                                    {{ trans('general.edit') }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.delete'))
                                            <li>
                                                <a href="{{ url('loan/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i>
                                                    {{ trans('general.delete') }}</a>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            @else
                <p>No loans found for the search query.</p>
            @endif
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>
        $('#data-tableM').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "desc"]],
            "language": {
                "lengthMenu": "{{ trans('general.lengthMenu') }}",
                "zeroRecords": "{{ trans('general.zeroRecords') }}",
                "info": "{{ trans('general.info') }}",
                "infoEmpty": "{{ trans('general.infoEmpty') }}",
                "search": "{{ trans('general.search') }}",
                "infoFiltered": "{{ trans('general.infoFiltered') }}",
                "paginate": {
                    "first": "{{ trans('general.first') }}",
                    "last": "{{ trans('general.last') }}",
                    "next": "{{ trans('general.next') }}",
                    "previous": "{{ trans('general.previous') }}"
                }
            },
            responsive: false
        });

        // Clear search button functionality
        $('#clear-search').click(function() {
            $('#search-input').val('');
            $('#search-form').submit();
        });
    </script>
@endsection
