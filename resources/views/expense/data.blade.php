@extends('layouts.master')
@section('title')
    {{trans_choice('general.expense',2)}}
@endsection

<style>
    .thumbnail {
        width: 100px; 
        height: auto; 
        cursor: pointer; 
    }
</style>

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{trans_choice('general.expense',2)}}</h3>
            <!---------expense redirect-------------------->
            <div class="box-tools pull-right">
                @if(Sentinel::hasAccess('expenses.create'))
                    <a href="{{ url('expense/create') }}"
                       class="btn btn-info btn-sm">{{trans_choice('general.add',1)}} {{trans_choice('general.expense',1)}}</a>
                @endif
            </div>
        </div>

        <div class="box-body">
            <form method="get" action="{{ url()->current() }}" class="form-horizontal" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="form-group">
                    <label for="start_date" class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="start_date" class="form-control date-picker" value="{{ $start_date }}" required id="start_date">
                    </div>
                </div>

                <div class="form-group">
                    <label for="end_date" class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="end_date" class="form-control date-picker" value="{{ $end_date }}" required id="end_date">
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_id" class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0" @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}" @if($office_id==$office->id) selected @endif>{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-offset-2 col-md-3 text-center">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!</button>
                        <a href="{{ url()->current() }}" class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>
                    </div>
                </div>
            </form>
        </div>


        <div class="box-body">
            <div class="table-responsive">
                <table id="data-table" class="table table-bordered table-condensed table-hover">
                    <thead>
                    <tr>
                        <th>{{trans_choice('general.expense',1)}} {{trans_choice('general.type',1)}}</th>
                        <th>{{trans_choice('general.amount',1)}}</th>
                        <th>{{trans_choice('general.date',1)}}</th>
                        <th>{{trans_choice('general.recurring',1)}}</th>
                        <th>{{trans_choice('general.description',1)}}</th>
                        <th>Proof of Payment</th>
                        <th>{{trans_choice('general.created_by',1)}}</th>
                        <th>Branch</th>
                        <th>{{ trans_choice('general.action',1) }}</th>
                    </tr>
                    </thead>
                    <tbody>
                        <!---this was $data-->
                    @foreach($data as $key)
                        <tr>
                            <td>
                                @if(!empty($key->type))
                                    {{$key->type->name}}
                                @endif
                            </td>
                            <td>{{ number_format($key->amount,2) }}</td>
                            <td>{{ $key->date }}</td>

                            <td>
                                @if($key->recurring==1)
                                    {{trans_choice('general.yes',1)}}
                                @else
                                    {{trans_choice('general.no',1)}}
                                @endif
                            </td>
                            <td>{{ $key->name }}</td>
                            <!-----POP---->
                            <td>
                            @if(!empty($key->proof_of_payment))
                                <a href="{{ asset('proof_of_payment/' . $key->proof_of_payment) }}" target="_blank">
                                    <img src="{{ asset('proof_of_payment/' . $key->proof_of_payment) }}" class="thumbnail" alt="Proof of Payment">
                                </a>
                            @else
                                No proof of payment attached
                            @endif
                            </td>
                            <td>
                                @if(!empty($key->created_by))
                                    {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                @endif
                            </td>
                            <td>{{ $key->office->name }}</td> 
                            <td>
                                <div class="btn-group">
                                    <!------------------------choose option----------------------------------------->
                                    <button type="button" class="btn btn-info btn-xs dropdown-toggle"
                                            data-toggle="dropdown" aria-expanded="false">
                                        {{ trans('general.choose') }} <span class="caret"></span>
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('expenses.view'))
                                            <li><a href="{{ url('expense/'.$key->id.'/show') }}"><i
                                                            class="fa fa-search"></i> {{ trans('general.view') }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('expenses.update'))
                                            <li><a href="{{ url('expense/'.$key->id.'/edit') }}"><i
                                                            class="fa fa-edit"></i> {{ trans('general.edit') }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('expenses.delete'))
                                            <li><a href="{{ url('expense/'.$key->id.'/delete') }}"
                                                   class="delete"><i
                                                            class="fa fa-trash"></i> {{ trans('general.delete') }} </a>
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
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
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
            "order": [[2, "desc"]],
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
    <script>
        $(document).ready(function() {
            
            $('#search-button').click(function() {
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                var officeId = $('#office_id').val();
        
                $.ajax({
                    url: '{{ url()->current() }}',
                    method: 'GET',
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        office_id: officeId
                    },
                    success: function(response) {
                        $('#data-table tbody').html(response);
                    }
                });
            });

            $('#reset-button').click(function() {
                $('form')[0].reset();
                $.ajax({
                    url: '{{ url()->current() }}',
                    method: 'GET',
                    success: function(response) {
                        $('#data-table tbody').html(response);
                    }
                });
            });
        });
    </script>
@endsection

