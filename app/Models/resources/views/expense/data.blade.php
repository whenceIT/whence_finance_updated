@extends('layouts.master')
@section('title')
    {{ trans_choice('general.expense',2) }}
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{ trans_choice('general.expense',2) }}</h3>

        <div class="box-tools pull-right">
            @if(Sentinel::hasAccess('expenses.create'))
                <a href="{{ url('expense/create') }}" class="btn btn-info btn-sm">
                    {{ trans_choice('general.add',1) }} {{ trans_choice('general.expense',1) }}
                </a>
            @endif
        </div>
    </div>

    <div class="box-body">
        {{-- Filters --}}
        <form method="GET" action="{{ url()->current() }}" class="form-horizontal" autocomplete="off" style="margin-bottom:15px">
            <div class="form-group">
                {{-- Start Date --}}
                <label class="control-label col-md-1">{{ trans_choice('general.start',1) }} {{ trans_choice('general.date',1) }}</label>
                <div class="col-md-2">
                    <input type="text" name="start_date" id="start_date" class="form-control date-picker"
                           value="{{ request('start_date') }}" placeholder="YYYY-MM-DD">
                </div>

                {{-- End Date --}}
                <label class="control-label col-md-1">{{ trans_choice('general.end',1) }} {{ trans_choice('general.date',1) }}</label>
                <div class="col-md-2">
                    <input type="text" name="end_date" id="end_date" class="form-control date-picker"
                           value="{{ request('end_date') }}" placeholder="YYYY-MM-DD">
                </div>

                {{-- Branch / Office --}}
                <label class="control-label col-md-1">{{ trans_choice('general.office',1) }}</label>
                <div class="col-md-3">
                    @php $userOfficeId = Sentinel::getUser()->office_id; @endphp
                    <select name="office_id" id="office_id" class="form-control select2">
                        <option value="">{{ trans_choice('general.all',1) }}</option>
                        @if(Sentinel::hasAccess('offices.view'))
                            @foreach(\App\Models\Office::orderBy('name')->get() as $office)
                                <option value="{{ $office->id }}" {{ (string)request('office_id')===(string)$office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        @else
                            @foreach(\App\Models\Office::where('id',$userOfficeId)->get() as $office)
                                <option value="{{ $office->id }}" {{ (string)request('office_id')===(string)$office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                {{-- Actions / Exports --}}
                <div class="col-md-2" style="margin-top:5px">
                    <button type="submit" class="btn btn-success">
                        {{ trans_choice('general.search',1) }}
                    </button>
                    <a href="{{ url()->current() }}" class="btn btn-default">
                        {{ trans_choice('general.reset',1) }}
                    </a>
                </div>

                <div class="col-md-12" style="margin-top:10px">
                    <div class="btn-group">
                        <button type="button" class="btn bg-blue dropdown-toggle" data-toggle="dropdown">
                            {{ trans_choice('general.download',1) }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right">
                            <li>
                                <a href="{{ route('expenses.export.excel', ['start_date'=>request('start_date'), 'end_date'=>request('end_date'), 'office_id'=>request('office_id')]) }}">
                                    <i class="fa fa-file-excel-o"></i> Excel (.xlsx)
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('expenses.export.pdf', ['start_date'=>request('start_date'), 'end_date'=>request('end_date'), 'office_id'=>request('office_id')]) }}" target="_blank" rel="noopener">
                                    <i class="fa fa-file-pdf-o"></i> PDF (.pdf)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </form>

        <div class="table-responsive">
            <table id="data-table" class="table table-bordered table-condensed table-hover">
                <thead>
                <tr>
                    <th>{{ trans_choice('general.expense',1) }} {{ trans_choice('general.type',1) }}</th>
                    <th>{{ trans_choice('general.amount',1) }}</th>
                    <th>{{ trans_choice('general.date',1) }}</th>
                    <th>{{ trans_choice('general.office',1) }}</th>
                    <th>{{ trans_choice('general.description',1) }}</th>
                    <th>{{ trans_choice('general.created_by',1) }}</th>
                    <th>{{ trans_choice('general.action',1) }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $key)
                    <tr>
                        <td>@if(!empty($key->type)) {{ $key->type->name }} @endif</td>
                        <td>{{ number_format($key->amount,2) }}</td>
                        <td>{{ $key->date }}</td>
                        <td>{{ $key->office->name}}</td>
                        <td>{{ $key->name }}</td>
                        <td>
                            @if(!empty($key->created_by))
                                {{ $key->created_by->first_name }} {{ $key->created_by->last_name }}
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-info btn-xs dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                    {{ trans('general.choose') }} <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                    @if(Sentinel::hasAccess('expenses.view'))
                                        <li><a href="{{ url('expense/'.$key->id.'/show') }}"><i class="fa fa-search"></i> {{ trans('general.view') }}</a></li>
                                    @endif
                                    @if(Sentinel::hasAccess('expenses.update'))
                                        <li><a href="{{ url('expense/'.$key->id.'/edit') }}"><i class="fa fa-edit"></i> {{ trans('general.edit') }}</a></li>
                                    @endif
                                    @if(Sentinel::hasAccess('expenses.delete'))
                                        <li><a href="{{ url('expense/'.$key->id.'/delete') }}" class="delete"><i class="fa fa-trash"></i> {{ trans('general.delete') }}</a></li>
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
</div>
@endsection

@section('footer-scripts')
<script>
    $('#data-table').DataTable({
        dom: 'frtip',
        paging: true,
        lengthChange: true,
        displayLength: 15,
        searching: true,
        ordering: true,
        info: true,
        autoWidth: true,
        order: [[2, "desc"]],
        columnDefs: [{ "orderable": false, "targets": [] }],
        language: {
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
