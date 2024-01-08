@extends('layouts.master')
@section('title')
    {{ $expense->name }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">{{ $expense->name }}</h3>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                <div class="box-body">
                    <table class="table table-responsive table-hover">
                        <tr>
                            <td>{{ trans_choice('general.branch',1) }}</td>
                            <td>
                                @if(!empty($expense->office))
                                    {{$expense->office->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.type',1) }}</td>
                            <td>
                                @if(!empty($expense->type))
                                    {{$expense->type->name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td> {{ trans('general.date') }}</td>
                            <td>{{ $expense->date }}</td>
                        </tr>
                        <tr>
                            <td>{{ trans_choice('general.amount',1) }}</td>
                            <td>{{ number_format($expense->amount,2) }}</td>
                        </tr>

                        <tr>
                            <td>{{ trans_choice('general.created_by',1) }}</td>
                            <td>
                                @if(!empty($expense->created_by))
                                    {{$expense->created_by->first_name}} {{$expense->created_by->last_name}}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ trans('general.updated_at') }}</td>
                            <td>{{ $expense->updated_at }}</td>
                        </tr>
                        @foreach(\App\Models\CustomFieldMeta::where('category', 'expenses')->where('parent_id', $expense->id)->get() as $key)
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
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="box box-primary">

                <div class="box-body">
                    {!!   $expense->notes !!}
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
