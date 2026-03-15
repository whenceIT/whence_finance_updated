@extends('layouts.master')
@section('title')
    {{trans_choice('general.savings',2)}} {{trans_choice('general.account',2)}}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                {{trans_choice('general.repayment',2)}} {{trans_choice('general.report',1)}}
                @if(!empty($start_date))
                    for period: <b>{{$start_date}} to {{$end_date}}</b>
                @endif
            </h3>

            <div class="box-tools pull-right">

            </div>
        </div>
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
                        <select name="office_id" class="form-control select2" id="office_id" required>
                            <option value="0"
                                    @if($office_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\Office::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($office_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="savings_product_id"
                           class="control-label col-md-2">{{trans_choice('general.product',1)}}</label>
                    <div class="col-md-3">
                        <select name="savings_product_id" class="form-control select2" id="savings_product_id" required>
                            <option value="0"
                                    @if($savings_product_id=="0") selected @endif>{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\SavingsProduct::all() as $key)
                                <option value="{{$key->id}}"
                                        @if($savings_product_id==$key->id) selected @endif>{{$key->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="field_officer_id"
                           class="control-label col-md-2">
                        {{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}}
                    </label>
                    <div class="col-md-3">
                        <select name="field_officer_id" class="form-control select2" id="field_officer_id" required>
                            <option value="0">{{trans_choice('general.all',1)}}</option>
                            @foreach(\App\Models\User::all() as $key)
                                @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                    <option value="{{$key->id}}"
                                            @if($field_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                @endif
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
                        <div class="btn-group">
                            <button type="button" class="btn bg-blue dropdown-toggle legitRipple"
                                    data-toggle="dropdown">{{trans_choice('general.download',1)}} {{trans_choice('general.report',1)}}
                                <span class="caret"></span></button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li>
                                    <a href="{{url('report/savings_report/savings_accounts/pdf?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&savings_product_id='.$savings_product_id.'&field_officer_id='.$field_officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-pdf"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.pdf',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_accounts/excel?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&savings_product_id='.$savings_product_id.'&field_officer_id='.$field_officer_id)}}"
                                       target="_blank"><i
                                                class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.excel',1)}}
                                    </a></li>
                                <li>
                                    <a href="{{url('report/savings_report/savings_accounts/csv?start_date='.$start_date.'&end_date='.$end_date.'&office_id='.$office_id.'&savings_product_id='.$savings_product_id.'&field_officer_id='.$field_officer_id)}}"
                                       target="_blank"><i
                                                class="icon-download"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.to',1)}} {{trans_choice('general.csv',1)}}
                                    </a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <!-- /.box-body -->

    </div>

    <!-- /.box -->
    @if(!empty($start_date))
        <div class="box box-primary">
            <div class="box-body table-responsive ">
                <table class="table  table-condensed table-hover">
                    <tbody>
                    <tr style="height: 25pt">
                        <td colspan="6" valign="middle"
                            class="style-1"> {{trans_choice('general.savings',1)}}  {{trans_choice('general.account',2)}}</td>
                    </tr>
                    <tr style="height: 15pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.from',1)}} :</td>
                        <td valign="middle" class="style-3">{{$start_date}}</td>
                        <td colspan="2" valign="middle"
                            class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                            :
                        </td>
                        <td colspan="2" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
                    </tr>
                    <tr style="height: 45pt">
                        <td valign="middle" class="style-2">{{trans_choice('general.to',1)}} :</td>
                        <td valign="middle" class="style-3">{{$end_date}}</td>
                        <td colspan="7"></td>
                    </tr>
                    <tr class="">
                        <td>{{trans_choice('general.id',1)}}</td>
                        <td>{{trans_choice('general.client',1)}}</td>
                        <td>{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}} </td>
                        <td>{{trans_choice('general.product',1)}}</td>
                        <td>{{trans_choice('general.created',1)}}</td>
                        <td>{{trans_choice('general.balance',1)}}</td>
                    </tr>
                    <?php
                    $total_balance = 0;

                    ?>
                    @foreach($data as $key)
                        <?php

                        $decimals = $key->decimals;

                        $balance = \App\Helpers\GeneralHelper::savings_account_balance($key->id);
                        $total_balance = $total_balance + $balance;


                        ?>
                        <tr>
                            <td>{{$key->id}}</td>
                            <td>
                                @if($key->client_type=="client")
                                    @if(!empty($key->client))
                                        @if($key->client->client_type=="individual")
                                            {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                        @endif
                                        @if($key->client->client_type=="business")
                                            {{$key->client->full_name}}
                                        @endif
                                    @endif
                                @endif
                                @if($key->client_type=="group")
                                    @if(!empty($key->group))
                                        {{$key->group->name}}
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->field_officer))
                                    {{$key->field_officer->first_name}}  {{$key->field_officer->last_name}}
                                @endif
                            </td>
                            <td>
                                @if(!empty($key->savings_product))
                                    {{$key->savings_product->name}}
                                @endif
                            </td>
                            <td>{{$key->created_at}}</td>
                            <td>{{number_format($balance,$decimals)}}</td>

                        </tr>
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5"></td>
                        <td><b>{{number_format($total_balance,2)}}</b></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@endsection
@section('footer-scripts')

@endsection
