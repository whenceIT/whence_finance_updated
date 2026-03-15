@extends('layouts.master')
@section('title')
    {{ $asset->name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $asset->name }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{ trans_choice('general.branch',1) }}</td>
                            <td>
                                @if(!empty($asset->office))
                                    {{$asset->office->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.type',1) }}</td>
                            <td>
                                @if(!empty($asset->type))
                                    {{$asset->type->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.purchase',1) }} {{ trans('general.date') }}</td>
                            <td>{{ $asset->purchase_date }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.cost',1) }}</td>
                            <td>{{ number_format($asset->purchase_price,2) }}</td>
                        </tr>

                        <tr>
                            <td>{{ trans_choice('general.life_span',1) }}</td>
                            <td>{{ $asset->life_span }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.updated_at') }}</td>
                            <td>{{ $asset->updated_at }}</td>
                        </tr>
                        @foreach(\App\Models\CustomFieldMeta::where('category', 'assets')->where('parent_id', $asset->id)->get() as $key)
                            <tr>
                                <td>
                                    @if(!empty($key->custom_field))
                                        {{$key->custom_field->name}}
                                    @endif
                                </td>
                                <td>
                                    @if($key->custom_field->field_type=="checkbox")
                                        @foreach(unserialize($key->name) as $v=>$k)
                                            {{$k}}<br>
                                        @endforeach
                                    @else
                                        {{$key->name}}
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td>{{ trans_choice('general.note',2) }}</td>
                            <td>{!!   $asset->notes !!}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ trans_choice('general.depreciation',2) }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table  table-bordered table-hover table-striped" id="">
                        <thead>
                        <tr>
                            <th>{{ trans_choice('general.year',1) }}</th>
                            <th>{{ trans_choice('general.beginning_value',1) }}</th>
                            <th>{{ trans_choice('general.depreciation',1) }}</th>
                            <th>{{ trans_choice('general.ending_value',1) }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($asset->depreciation as $key)
                            <tr>
                                <td>{{ $key->year }}</td>
                                <td>{{ number_format($key->beginning_value,2) }}</td>
                                <td>{{ number_format($key->depreciation_value,2) }}</td>
                                <td>{{ number_format($key->ending_value,2) }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')

    <script>
        $('.data-table').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
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
                },
                "columnDefs": [
                    {"orderable": false, "targets": 0}
                ]
            },
            responsive: true,
        });
    </script>
@endsection
