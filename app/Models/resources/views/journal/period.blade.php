@extends('layouts.master')
@section('title')
    {{ trans_choice('general.close',2) }} {{ trans_choice('general.period',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"> {{ trans_choice('general.close',2) }} {{ trans_choice('general.period',1) }}</h3>

            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('users.create'))
                    <a href="#" data-toggle="modal" data-target="#close_period" class="btn btn-info btn-sm">
                        {{ trans_choice('general.close',1) }} {{ trans_choice('general.period',1) }}
                    </a>
                @endif
            </div>
        </div>
        <div class="box-body table-responsive">
            <table class="table  table-bordered table-hover table-striped" id="data-table">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.office',1) }}</th>
                    <th>{{ trans('general.date') }} {{ trans('general.closed') }}</th>
                    <th>{{ trans('general.closed') }} {{ trans_choice('general.by',1) }}</th>
                    <th>{{ trans_choice('general.note',2) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
                        <td>{{ $key->closing_date }}</td>
                        <td>
                            @if(!empty($key->created_by))
                                {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                            @endif
                        </td>
                        <td>{{ $key->notes }}</td>
                        <td>
                            <a href="{{ url('accounting/period/'.$key->id.'/delete') }}"
                               class="delete"><i
                                        class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="close_period">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.close',1)}} {{trans_choice('general.period',1)}}</h4>
                </div>
                <form method="post" action="{{url('accounting/period/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="accounting_period">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="office_id"
                                   class="control-label col-md-3">{{trans_choice('general.office',1)}}</label>
                            <div class="col-md-9">
                                <select name="office_id" class="form-control select2" id="office_id" required>
                                    <option></option>
                                    @foreach(\App\Models\Office::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="closing_date"
                                   class="control-label col-md-3">{{trans_choice('general.close',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="closing_date" class="form-control date-picker"
                                       value="{{date("Y-m-d")}}"
                                       required id="closing_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',1)}}</label>
                            <div class="col-md-9">
                        <textarea name="notes" class="form-control"
                                  id="notes" rows="3" required>{{old('notes')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit"
                                class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
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
