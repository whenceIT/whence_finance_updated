@extends('layouts.master')
@section('title')
    {{trans_choice('general.journal',1)}} {{trans_choice('general.entry',2)}}
@endsection
@section('content')
    <div class="box box-primary">

        <div class="box-body hidden-print">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">
                    <label for="start_date"
                           class="control-label col-md-2">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="start_date" class="form-control date-picker"
                               value="{{$start_date}}"
                               required id="start_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="end_date"
                           class="control-label col-md-2">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                    <div class="col-md-3">
                        <input type="text" name="end_date" class="form-control date-picker"
                               value="{{$end_date}}"
                               required id="end_date">
                    </div>
                </div>
                <div class="form-group">
                    <label for="office_id"
                           class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                    <div class="col-md-3">
                        <select name="office_id" class="form-control select2" id="office_id">
                            <option value="0"
                                    @if($office_id==0) selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="gl_account_id"
                           class="control-label col-md-2">{{trans_choice('general.account',1)}}</label>
                    <div class="col-md-3">
                        <select name="gl_account_id" class="form-control select2" id="gl_account_id">
                            <option value="0"
                                    @if($office_id==0) selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\GlAccount::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($gl_account_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for=""
                           class="control-label col-md-2"></label>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!
                        </button>

                        <a href="{{Request::url()}}"
                           class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->

    </div>
    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-white">
            <div class="box-body ">
                <div class="table-responsive">
                    <table id="data-table" class="table table-striped table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{trans_choice('general.transaction',1)}} {{trans_choice('general.id',1)}}</th>
                            <th>{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</th>
                            <th>{{trans_choice('general.date',1)}}</th>
                            <th>{{trans_choice('general.office',1)}}</th>
                            <th>{{trans_choice('general.account',1)}}</th>
                            <th>{{trans_choice('general.debit',1)}}</th>
                            <th>{{trans_choice('general.credit',1)}}</th>
                            <th>Reconciliation</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($data as $key)
                            <tr>
                                <td>{{ $key->id }}</td>
                                <td>{{ $key->reference }}</td>
                                <td>
                                    @if($key->transaction_type=='disbursement')
                                        {{trans_choice('general.disbursement',1)}}
                                    @endif
                                    @if($key->transaction_type=='accrual')
                                        {{trans_choice('general.accrual',1)}}
                                    @endif
                                    @if($key->transaction_type=='deposit')
                                        {{trans_choice('general.deposit',1)}}
                                    @endif
                                    @if($key->transaction_type=='withdrawal')
                                        {{trans_choice('general.withdrawal',1)}}
                                    @endif
                                    @if($key->transaction_type=='manual_entry')
                                        {{trans_choice('general.manual_entry',2)}}
                                    @endif
                                    @if($key->transaction_type=='pay_charge')
                                        {{trans_choice('general.pay',1)}}    {{trans_choice('general.charge',1)}}
                                    @endif
                                    @if($key->transaction_type=='transfer_fund')
                                        {{trans_choice('general.transfer_fund',1)}} {{trans_choice('general.charge',2)}}
                                    @endif
                                    @if($key->transaction_type=='expense')
                                        {{trans_choice('general.expense',1)}}
                                    @endif
                                    @if($key->transaction_type=='payroll')
                                        {{trans_choice('general.payroll',1)}}
                                    @endif
                                    @if($key->transaction_type=='income')
                                        {{trans_choice('general.income',1)}}
                                    @endif
                                    @if($key->transaction_type=='penalty')
                                        {{trans_choice('general.penalty',1)}}
                                    @endif
                                    @if($key->transaction_type=='fee')
                                        {{trans_choice('general.fee',1)}}
                                    @endif
                                    @if($key->transaction_type=='close_write_off')
                                        {{trans_choice('general.write',1)}}  {{trans_choice('general.waiver',2)}}
                                    @endif
                                    @if($key->transaction_type=='repayment_recovery')
                                        {{trans_choice('general.repayment',1)}}
                                    @endif
                                    @if($key->transaction_type=='repayment')
                                        {{trans_choice('general.repayment',1)}}
                                    @endif
                                    @if($key->transaction_type=='interest_accrual')
                                        {{trans_choice('general.interest',1)}} {{trans_choice('general.accrual',1)}}
                                    @endif
                                    @if($key->transaction_type=='fee_accrual')
                                        {{trans_choice('general.fee',1)}} {{trans_choice('general.accrual',1)}}
                                    @endif
                                </td>
                                <td>{{ $key->date }}</td>
                                <td>
                                    @if(!empty($key->office))
                                        {{ $key->office->name }}
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($key->gl_account))
                                        {{ $key->gl_account->name }}
                                    @endif
                                </td>
                                <td>{{ number_format($key->debit,2) }}</td>
                                <td>{{ number_format($key->credit,2) }}</td>
                                <td>
                                    @if($key->reconciled==1)
                                        <span class="label label-success">{{trans_choice('general.reconciled',1)}}</span>
                                    @else
                                        <span class="label label-danger">{{trans_choice('general.unreconciled',1)}}</span>
                                    @endif
                                </td>
                                <td>
                                <a href="{{ url('accounting/journal/'.$key->id.'/delete_journal') }}"
                                               class="delete"><i
                                                        class="fa fa-trash"></i>
                                                {{ trans('general.delete') }}</a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- /.box-body -->
        </div>
    @endif
@endsection
@section('footer-scripts')
    <script>
        $('#data-table').DataTable({
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
