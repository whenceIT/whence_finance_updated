@extends('layouts.master')
@section('title')
    {{ trans_choice('general.active', 1) }} {{ trans_choice('general.loan', 2) }}
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
                @if(isset($loans) && count($loans) > 0)
                    <a href="{{ route('loan.data.excel', ['query' => $query ?? '']) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="icon-file-excel"></i> {{ trans_choice('general.download', 1) }} {{ trans_choice('general.to', 1) }} {{ trans_choice('general.excel', 1) }}
                    </a>
                    <a href="{{ route('loan.data.pdf', ['query' => $query ?? '']) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="icon-file-pdf"></i> {{ trans_choice('general.download', 1) }} {{ trans_choice('general.to', 1) }} {{ trans_choice('general.pdf', 1) }}
                    </a>
                @endif
            </div>
            <form id="search-form" action="{{ route('loan.data') }}" method="GET"
                style="display: flex; justify-content: center;">
                <div class="input-group" style="width: 400px; margin-top:15px;">
                    <input id="search-input" type="text" name="query" class="form-control"
                        placeholder="Search by First Name, Last Name or Loan ID" value="{{ $query ?? '' }}">
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
                            <th>{{ trans_choice('general.account', 1) }}#</th>
                            <th>{{ trans_choice('general.branch', 1) }}</th>
                            <th>{{ trans_choice('general.client', 1) }}</th>
                            <th>Loan Consultant</th>
                            <th>vetted_by</th>
                            <th>verified_by</th>
                            <th>{{ trans_choice('general.product', 1) }}</th>
                            <th>{{ trans_choice('general.balance', 1) }}</th>
                            <th>{{ trans_choice('general.action', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $key)
                                    <?php
                            $balance = 0;
                            $debit = 0;
                            $credit = 0;
                                    ?>
                                    @foreach($key->transactions as $transaction)
                                            <?php
                                        if ($transaction->transaction_type != 'specified_due_date_fee') {
                                            $debit = $debit + $transaction->debit;
                                        }

                                        $credit = $credit + $transaction->credit;
                                            ?>
                                    @endforeach
                                    <?php
                            $balance = $debit - $credit;
                                    ?>
                                    <tr>
                                        <td><a href="{{ url('loan/' . $key->id . '/show') }}" data-toggle="tooltip"
                                                title="Click to view">{{ $key->id }}</a></td>
                                        <td>
                                            @if(!empty($key->office))
                                                {{$key->office->name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($key->client_type == "client")
                                                @if(!empty($key->client))
                                                    @if($key->client->client_type == "individual")
                                                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                                    @else
                                                        {{$key->client->full_name}}
                                                    @endif
                                                @endif
                                            @endif
                                            @if($key->client_type == "group")
                                                {{$key->group->name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->loan_officer))
                                                {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->vetted_by_field))
                                                {{$key->vetted_by_field->first_name}} {{$key->vetted_by_field->last_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->verified_by_field))
                                                {{$key->verified_by_field->first_name}} {{$key->verified_by_field->last_name}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($key->loan_product))
                                                {{$key->loan_product->name}}
                                            @endif
                                        </td>

                                        <td>{{ number_format($balance, $key->decimals) }}</td>
                                        <td>{{ $key->disbursement_date }}</td>
                                        <td>
                                            @php
                                                $currentUser = Sentinel::getUser();
                                                $role_id = $currentUser->roles->first()->id ?? 0;
                                                $has_access = false;

                                                if ($role_id == 1) {
                                                    $has_access = true;
                                                } elseif ($role_id == 3 || $role_id == 4) {
                                                    if ($key->office_id == $currentUser->office_id) {
                                                        $has_access = true;
                                                    }
                                                } elseif ($role_id == 12) {
                                                    if (!empty($key->office) && $key->office->district_id == $currentUser->district_id) {
                                                        $has_access = true;
                                                    }
                                                } elseif ($role_id == 6) {
                                                    if (!empty($key->office) && $key->office->province_id == $currentUser->province_id) {
                                                        $has_access = true;
                                                    }
                                                } else {
                                                    $has_access = true;
                                                }
                                            @endphp
                                            @if($has_access)
                                                <div class="btn-group">
                                                    <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                                        aria-expanded="false"><i class="fa fa-navicon"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        @if(Sentinel::hasAccess('loans.view'))
                                                            <li>
                                                                <a href="{{ url('loan/' . $key->id . '/show') }}"><i class="fa fa-search"></i>
                                                                    {{ trans_choice('general.detail', 2) }}</a>
                                                            </li>
                                                        @endif
                                                        @if($key->status == "pending")
                                                            @if(Sentinel::hasAccess('loans.update'))
                                                                <li>
                                                                    <a href="{{ url('loan/' . $key->id . '/edit') }}"><i class="fa fa-edit"></i>
                                                                        {{ trans('general.edit') }}</a>
                                                                </li>
                                                            @endif
                                                            @if(Sentinel::hasAccess('loans.delete'))
                                                                <li>
                                                                    <a href="{{ url('loan/' . $key->id . '/delete') }}" class="delete"><i
                                                                            class="fa fa-trash"></i>
                                                                        {{ trans('general.delete') }}</a>
                                                                </li>
                                                            @endif
                                                        @endif
                                                    </ul>
                                                </div>
                                            @endif
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
        $('#clear-search').click(function () {
            $('#search-input').val('');
            $('#search-form').submit();
        });
    </script>
@endsection