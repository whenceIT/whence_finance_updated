@extends('layouts.master')
@section('title')
    {{ trans_choice('general.product_configuration',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.product_configuration',2) }}</h3>

            <div class="box-tools pull-right">
                <a href="{{ route('motor-vehicle.product-configurations.create') }}" class="btn btn-success btn-sm">
                    {{ trans_choice('general.add',1) }} {{ trans_choice('general.product_configuration',1) }}
                </a>
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.name',1) }}</th>
                    <th>{{ trans_choice('general.short_name',1) }}</th>
                    <th>{{ trans_choice('general.minimum',1) }} {{ trans_choice('general.amount',1) }}</th>
                    <th>{{ trans_choice('general.maximum',1) }} {{ trans_choice('general.amount',1) }}</th>
                    <th>{{ trans_choice('general.interest',1) }} {{ trans_choice('general.rate',1) }}</th>
                    <th>{{ trans_choice('general.tenure',1) }}</th>
                    <th>{{ trans_choice('general.active',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($configs as $key)
                    <tr>
                        <td>{{ $key->name }}</td>
                        <td>{{ $key->short_name }}</td>
                        <td>{{ number_format($key->minimum_principal,2) }}</td>
                        <td>{{ number_format($key->maximum_principal,2) }}</td>
                        <td>{{ $key->default_interest_rate }}%</td>
                        <td>{{ $key->default_loan_term }} {{ trans_choice('general.month',2) }}</td>
                        <td>
                            @if($key->is_active)
                                <span class="label label-success">{{ trans_choice('general.yes',1) }}</span>
                            @else
                                <span class="label label-danger">{{ trans_choice('general.no',1) }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                        aria-expanded="false"><i
                                            class="fa fa-navicon"></i></button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    <li>
                                        <a href="{{ route('motor-vehicle.product-configurations.edit', $key->id) }}"><i
                                                    class="fa fa-edit"></i>
                                            {{ trans('general.edit') }}</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('motor-vehicle.product-configurations.destroy', $key->id) }}"
                                           class="delete"><i
                                                    class="fa fa-trash"></i>
                                            {{ trans('general.delete') }}</a>
                                    </li>
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

        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[0, "asc"]],
            "columnDefs": [
                {"orderable": false, "targets": []}
            ],
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
    </script>
@endsection
