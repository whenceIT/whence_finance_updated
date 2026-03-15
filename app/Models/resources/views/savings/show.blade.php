@extends('layouts.master')
@section('title')
    {{ trans_choice('general.savings',1) }} {{ trans_choice('general.detail',2) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel border-left-lg border-left-primary">
                <div class="panel-heading">
                    <h6 class="panel-title">{{$savings->savings_product->name}}(#{{$savings->id}})</h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    @if($savings->status=="pending")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('savings.approve'))
                                        <a href="#" data-toggle="modal" data-target="#approve_savings_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-check"></i>&nbsp;{{trans_choice('general.approve',1)}}
                                        </a>

                                        <a href="#" data-toggle="modal" data-target="#decline_savings_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-times"></i>&nbsp;{{trans_choice('general.decline',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('savings.update'))
                                        <a href="{{ url('savings/'.$savings->id.'/edit') }}" class="btn btn-primary"><i
                                                    class="fa fa-edit"></i>&nbsp;{{trans_choice('general.edit',1)}}</a>
                                    @endif
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-expanded="false">More<span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('savings.update'))
                                            <li>
                                                <a href="#"
                                                   data-toggle="modal" data-target="#change_savings_officer_modal">
                                                    {{ trans_choice('general.change',1) }} {{ trans_choice('general.field',1) }} {{ trans_choice('general.officer',1) }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.approve'))
                                            <li>
                                                <a href="#"
                                                   data-toggle="modal" data-target="#withdraw_savings_modal">
                                                    {{ trans('general.withdraw') }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.delete'))
                                            <li>
                                                <a href="{{ url('savings/'.$savings->id.'/delete') }}"
                                                   class="delete">
                                                    {{ trans('general.delete') }}</a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="row m-t-20" style="">
                            <div class="col-sm-7 col-md-7">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    @if($savings->client_type=="client")
                                        <tr>
                                            <th class="table-bold-savings">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($savings->client))
                                                @if($savings->client->client_type=="individual")
                                                    <a href="{{url('client/'.$savings->client_id.'/show')}}">{{$savings->client->first_name}} {{$savings->client->middle_name}} {{$savings->client->last_name}}</a>
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    <a href="{{url('client/'.$savings->client_id.'/show')}}">{{$savings->client->full_name}}</a>
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($savings->client_type=="group")
                                        <tr>
                                            <th class="table-bold-savings">{{trans_choice('general.group',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($savings->group))
                                                <a href="{{url('group/'.$savings->group_id.'/show')}}">{{$savings->group->name}}</a>
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.currency',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($savings->currency))
                                                {{$savings->currency->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($savings->field_officer))
                                                {{$savings->field_officer->first_name}} {{$savings->field_officer->last_name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.external_id',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ $savings->external_id }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-5 col-md-5">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.created',1)}}  {{trans_choice('general.date',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $savings->created_date }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $savings->interest_rate }}</span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="modal fade" id="approve_savings_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.approve',1)}} {{trans_choice('general.savings',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('savings/'.$savings->id.'/approve')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="approve_savings_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="approved_date"
                                                       class="control-label col-md-3">{{trans_choice('general.approved',1)}} {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="approved_date"
                                                           class="form-control date-picker"
                                                           value="{{date("Y-m-d")}}"
                                                           required id="approved_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="approved_notes"
                                                       class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-9">
                                                     <textarea name="approved_notes" class="form-control"
                                                               id="approved_notes"
                                                               rows="3">{{old('approved_notes')}}</textarea>
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
                        <div class="modal fade" id="decline_savings_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.decline',1)}} {{trans_choice('general.savings',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('savings/'.$savings->id.'/decline')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="decline_savings_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="declined_notes"
                                                       class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-9">
                                                     <textarea name="declined_notes" class="form-control"
                                                               id="declined_notes" rows="3"
                                                               required>{{old('declined_notes')}}</textarea>
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
                        <div class="modal fade" id="withdraw_savings_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.withdraw',1)}} {{trans_choice('general.savings',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('savings/'.$savings->id.'/withdraw')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="withdraw_savings_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="withdrawn_notes"
                                                       class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-9">
                                                     <textarea name="withdrawn_notes" class="form-control"
                                                               id="declined_notes" rows="3"
                                                               required>{{old('withdrawn_notes')}}</textarea>
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

                    @endif
                    @if($savings->status=="approved")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('savings.transactions.deposit'))
                                        <a href="{{ url('savings/'.$savings->id.'/deposit/create') }}"
                                           class="btn btn-primary"><i
                                                    class="fa fa-arrow-up"></i>&nbsp;{{trans_choice('general.deposit',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('savings.transactions.withdrawal'))
                                        <a href="{{ url('savings/'.$savings->id.'/withdrawal/create') }}"
                                           class="btn btn-primary"><i
                                                    class="fa fa-arrow-down"></i>&nbsp;{{trans_choice('general.withdraw',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('savings.charge.create'))
                                        <a href="#" data-toggle="modal" data-target="#add_charge_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-plus"></i>&nbsp;{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('savings.update'))
                                        <a href="#"
                                           data-toggle="modal" data-target="#change_savings_officer_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-user"></i>&nbsp;
                                            {{ trans_choice('general.change',1) }} {{ trans_choice('general.field',1) }} {{ trans_choice('general.officer',1) }}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('savings.undo_approval'))
                                        <a href="{{ url('savings/'.$savings->id.'/unapprove') }}"
                                           class="btn btn-primary confirm"><i
                                                    class="fa fa-undo"></i>&nbsp;{{trans_choice('general.undo',1)}}
                                            &nbsp;{{trans_choice('general.approval',1)}}</a>
                                    @endif
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-expanded="false">More<span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('savings.post_interest'))
                                            <li class="hidden">
                                                <a href="{{ url('savings/'.$savings->id.'/post_interest') }}"
                                                   class="delete">
                                                    {{ trans_choice('general.post',1) }} {{ trans_choice('general.interest',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.email_statement'))
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#email_statement_modal">
                                                    {{ trans_choice('general.email',1) }} {{ trans_choice('general.statement',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.pdf_statement'))
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#pdf_statement_modal">
                                                    {{ trans_choice('general.pdf',1) }} {{ trans_choice('general.statement',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.pdf_statement'))
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#print_statement_modal">
                                                    {{ trans_choice('general.print',1) }} {{ trans_choice('general.statement',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('savings.close'))
                                            <li class="hidden">
                                                <a href="{{ url('savings/'.$savings->id.'/close') }}">
                                                    {{ trans_choice('general.close',1) }} </a>
                                            </li>
                                        @endif
                                    </ul>

                                </div>
                            </div>
                        </div>
                        <div class="row m-t-20" style="">
                            <div class="col-sm-7 col-md-7">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <?php
                                    $deposits = \App\Models\SavingsTransaction::where('savings_id', $savings->id)->where('transaction_type', 'deposit')->where('reversed', 0)->sum('credit');
                                    $withdrawals = \App\Models\SavingsTransaction::where('savings_id', $savings->id)->where('transaction_type', 'withdrawal')->where('reversed', 0)->sum('debit');
                                    $fees = \App\Models\SavingsTransaction::where('savings_id', $savings->id)->where('transaction_type', 'bank_fees')->where('reversed', 0)->sum('debit');
                                    $interest = \App\Models\SavingsTransaction::where('savings_id', $savings->id)->where('transaction_type', 'interest')->where('reversed', 0)->sum('credit');

                                    ?>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.balance',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ number_format(\App\Helpers\GeneralHelper::savings_account_balance($savings->id),$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.total',1)}} {{trans_choice('general.deposit',2)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($deposits,$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.total',1)}} {{trans_choice('general.withdrawal',2)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($withdrawals,$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.total',1)}} {{trans_choice('general.fee',2)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($fees,$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.total',1)}} {{trans_choice('general.interest',2)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($interest,$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-5 col-md-5">
                                <table class="table table-striped table-bordered">
                                    <tbody>

                                    @if($savings->client_type=="client")
                                        <tr>
                                            <th class="table-bold-savings">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($savings->client))
                                                @if($savings->client->client_type=="individual")
                                                    <a href="{{url('client/'.$savings->client_id.'/show')}}">{{$savings->client->first_name}} {{$savings->client->middle_name}} {{$savings->client->last_name}}</a>
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    <a href="{{url('client/'.$savings->client_id.'/show')}}">{{$savings->client->full_name}}</a>
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($savings->client_type=="group")
                                        <tr>
                                            <th class="table-bold-savings">{{trans_choice('general.group',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($savings->group))
                                                <a href="{{url('group/'.$savings->group_id.'/show')}}">{{$savings->group->name}}</a>
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.currency',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($savings->currency))
                                                {{$savings->currency->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($savings->field_officer))
                                                {{$savings->field_officer->first_name}} {{$savings->field_officer->last_name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.external_id',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ $savings->external_id }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.activation',1)}}  {{trans_choice('general.date',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $savings->approved_date }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-savings">{{trans_choice('general.interest',1)}} {{trans_choice('general.rate',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($savings->interest_rate,$savings->decimals) }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>

                    @endif
                    @if($savings->status=="withdrawn")
                        <div class="row">
                            <div class="col-md-12">
                                <blockquote>
                                    <p>{{$savings->withdrawn_notes}}</p>
                                    <small>{{$savings->withdrawn_date}}</small>
                                </blockquote>
                            </div>
                        </div>
                    @endif
                    @if($savings->status=="declined")
                        <div class="row">
                            <div class="col-md-12">
                                <blockquote>
                                    <p>{{$savings->declined_notes}}</p>
                                    <small>{{$savings->declined_date}}</small>
                                </blockquote>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#account_details" data-toggle="tab"
                                          aria-expanded="false">{{trans_choice('general.account',1)}} {{trans_choice('general.detail',2)}}</a>
                    </li>
                    @if($savings->status=="approved" || $savings->status=="closed"  )
                        @if(Sentinel::hasAccess('savings.transactions.view'))
                            <li class=""><a href="#transactions" data-toggle="tab"
                                            aria-expanded="false">{{trans_choice('general.transaction',2)}}</a>
                            </li>
                        @endif
                    @endif
                    @if(Sentinel::hasAccess('savings.documents.view'))
                        <li class=""><a href="#documents" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.document',2)}}</a>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('savings.notes.view'))
                        <li class=""><a href="#notes" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.note',2)}}</a>
                        </li>
                    @endif

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="account_details">
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>{{trans_choice('general.interest',1)}}
                                    {{trans_choice('general.posting',1)}}</td>
                                <td>
                                    @if($savings->savings_product->interest_posting_period=="monthly")
                                        {{trans_choice('general.monthly',1)}}
                                    @endif
                                    @if($savings->savings_product->interest_posting_period=="quarterly")
                                        {{trans_choice('general.quarterly',1)}}
                                    @endif
                                    @if($savings->savings_product->interest_posting_period=="biannual")
                                        {{trans_choice('general.biannual',1)}}
                                    @endif
                                    @if($savings->savings_product->interest_posting_period=="annually")
                                        {{trans_choice('general.annually',1)}}
                                    @endif
                                </td>
                            </tr>

                            <tr>
                                <td>{{trans_choice('general.interest',1)}}</td>
                                <td>
                                    {{$savings->interest_rate}}  {{trans_choice('general.per',1)}} {{trans_choice('general.year',1)}}

                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.allow',1)}} {{trans_choice('general.overdraft',1)}}</td>
                                <td>
                                    @if($savings->allow_overdraft=="1")
                                        {{trans_choice('general.yes',1)}}
                                    @endif
                                    @if($savings->allow_overdraft=="0")
                                        {{trans_choice('general.no',1)}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.minimum',1)}} {{trans_choice('general.balance',1)}}</td>
                                <td>
                                    {{$savings->savings_product->minimum_balance}}
                                </td>
                            </tr>
                            @foreach(\App\Models\CustomFieldMeta::where('category', 'savings')->where('parent_id', $savings->id)->get() as $key)
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
                                <td>{{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}</td>
                                <td>
                                    {{$savings->created_date}}
                                    @if(!empty($savings->created_by))
                                        by {{$savings->created_by->first_name}} {{$savings->created_by->last_name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.approved',1)}} {{trans_choice('general.on',1)}}</td>
                                <td>
                                    {{$savings->approved_date}}
                                    @if(!empty($savings->approved_by))
                                        by {{$savings->approved_by->first_name}} {{$savings->approved_by->last_name}}
                                    @endif
                                </td>
                            </tr>

                        </table>
                    </div>
                    @if(Sentinel::hasAccess('savings.documents.view'))
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('savings.documents.create'))
                                        <a href="#add_document_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.document',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.name',1) }}</th>
                                            <th>{{ trans('general.description') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\Document::where('record_id',$savings->id)->where('type','savings')->get() as $key)
                                            <tr>
                                                <td>{{ $key->name }}</td>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>
                                                    <a class="" href="{{asset('uploads/'.$key->location)}}"
                                                       target="_blank"><i class="fa fa-download"></i> </a>
                                                    @if(Sentinel::hasAccess('savings.documents.delete'))
                                                        <a class="confirm"
                                                           href="{{url('savings/document/'.$key->id.'/delete')}}"><i
                                                                    class="fa fa-trash"></i> </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Sentinel::hasAccess('savings.notes.view'))

                        <div class="tab-pane" id="notes">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('savings.notes.create'))
                                        <a href="#add_note_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.note',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.note',1) }}</th>
                                            <th>{{ trans('general.date') }}</th>
                                            <th>{{ trans('general.created_by') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(\App\Models\Note::where('reference_id',$savings->id)->where('type','savings')->get() as $key)
                                            <tr>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>{!!   $key->created_at !!}</td>
                                                <td>
                                                    @if(!empty($key->created_by))
                                                        {{$key->created_by->first_name}} {{$key->created_by->last_name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                       data-target="#view_note"><i class="fa fa-eye"></i> </a>
                                                    @if(Sentinel::hasAccess('savings.notes.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_note"><i class="fa fa-edit"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('savings.notes.delete'))
                                                        <a class="confirm"
                                                           href="{{url('savings/note/'.$key->id.'/delete')}}"><i
                                                                    class="fa fa-trash"></i> </a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($savings->status=="approved" || $savings->status=="closed"  )
                        @if(Sentinel::hasAccess('savings.transactions.view'))
                            <div class="tab-pane" id="transactions">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="table-responsive">
                                            <table id="repayments-data-table"
                                                   class="table  table-condensed table-hover">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        {{trans_choice('general.id',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.date',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.submitted',1)}} {{trans_choice('general.on',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.type',1)}}
                                                    </th>

                                                    <th>
                                                        {{trans_choice('general.debit',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.credit',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.balance',1)}}
                                                    </th>
                                                    <th>
                                                        {{trans_choice('general.detail',2)}}
                                                    </th>
                                                    <th class="text-center">
                                                        {{trans_choice('general.action',1)}}
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                $balance = 0;
                                                ?>
                                                @foreach(\App\Models\SavingsTransaction::where('savings_id',$savings->id)->whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->get() as $key)
                                                    <?php
                                                    $balance = $balance + ($key->credit - $key->debit);
                                                    ?>
                                                    <tr>
                                                        <td>{{$key->id}}</td>
                                                        <td>{{$key->date}}</td>
                                                        <td>{{$key->created_at}}</td>
                                                        <td>
                                                            @if($key->transaction_type=='deposit')
                                                                {{trans_choice('general.deposit',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='withdrawal')
                                                                {{trans_choice('general.withdrawal',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='bank_fees')
                                                                {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='specified_due_date_fee')
                                                                {{trans_choice('general.bank',1)}}  {{trans_choice('general.fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest')
                                                                {{trans_choice('general.interest',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='dividend')
                                                                {{trans_choice('general.dividend',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='guarantee_restored')
                                                                {{trans_choice('general.guarantee_restored',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='fees_payment')
                                                                {{trans_choice('general.fee',2)}} {{trans_choice('general.payment',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='transfer_loan')
                                                                {{trans_choice('general.transfer',1)}} {{trans_choice('general.loan',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='transfer_savings')
                                                                {{trans_choice('general.transfer',1)}} {{trans_choice('general.savings',2)}}
                                                            @endif
                                                            @if($key->reversed==1)
                                                                @if($key->reversal_type=="user")
                                                                    <span class="text-danger"><b>({{trans_choice('general.user',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif
                                                                @if($key->reversal_type=="system")
                                                                    <span class="text-danger"><b>({{trans_choice('general.system',1)}} {{trans_choice('general.reversed',1)}}
                                                                            )</b></span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>{{number_format($key->debit,2)}}</td>
                                                        <td>{{number_format($key->credit,2)}}</td>
                                                        <td>{{number_format($balance,2)}}</td>
                                                        <td>{{$key->receipt}}</td>
                                                        <td class="">
                                                            <div class="btn-group">
                                                                <button class="btn btn-info btn-sm dropdown-toggle"
                                                                        type="button" data-toggle="dropdown"
                                                                        aria-expanded="false"><i
                                                                            class="fa fa-navicon"></i></button>
                                                                <ul class="dropdown-menu dropdown-menu-right"
                                                                    role="menu">
                                                                    @if(Sentinel::hasAccess('savings.transactions.view'))
                                                                        <li>
                                                                            <a href="{{url('savings/transaction/'.$key->id.'/show')}}"><i
                                                                                        class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                                            </a></li>
                                                                        <li>
                                                                    @endif
                                                                    @if($key->transaction_type=='deposit' && $key->reversible==1)
                                                                        <li>
                                                                            <a href="{{url('savings/transaction/'.$key->id.'/print')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-print"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li>
                                                                        <li>
                                                                            <a href="{{url('savings/transaction/'.$key->id.'/pdf')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-file-pdf-o"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li>
                                                                        @if(Sentinel::hasAccess('savings.transactions.update'))
                                                                            <li>
                                                                                <a href="{{url('savings/deposit/'.$key->id.'/edit')}}"><i
                                                                                            class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                                </a></li>
                                                                            <li>
                                                                                <a href="{{url('savings/deposit/'.$key->id.'/reverse')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.reverse') }}
                                                                                </a></li>
                                                                        @endif
                                                                    @endif
                                                                    @if($key->transaction_type=='bank_fees' && $key->reversible==1)
                                                                        @if(Sentinel::hasAccess('savings.transactions.update'))
                                                                            <li>
                                                                                <a href="{{url('savings/transaction/'.$key->id.'/waive')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.waive') }}
                                                                                </a></li>
                                                                        @endif
                                                                    @endif

                                                                    @if($key->transaction_type=='specified_due_date_fee' && $key->reversible==1)
                                                                        @if(Sentinel::hasAccess('savings.transactions.update'))
                                                                            <li>
                                                                                <a href="{{url('savings/transaction/'.$key->id.'/waive')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.waive') }}
                                                                                </a></li>
                                                                        @endif
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
                            </div>
                        @endif
                    @endif

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="change_savings_officer_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.change',1)}} {{trans_choice('general.savings',1)}} {{trans_choice('general.officer',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/change_savings_officer')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="change_savings_officer_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="field_officer_id"
                                   class="control-label col-md-3">
                                {{trans_choice('general.field',1)}} {{trans_choice('general.officer',1)}}
                            </label>
                            <div class="col-md-9">
                                <select name="field_officer_id" class="form-control select2"
                                        id="field_officer_id" required>
                                    <option></option>
                                    @foreach(\App\Models\User::all() as $key)
                                        @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                            <option value="{{$key->id}}"
                                                    @if($savings->field_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
                                        @endif
                                    @endforeach
                                </select>
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
    <div class="modal fade" id="add_document_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.document',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/document/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_document_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="document_name"
                                   class="control-label col-md-3">{{trans_choice('general.name',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="name" class="form-control"
                                       value="{{old('name')}}"
                                       required id="document_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="document_notes"
                                   class="control-label col-md-3">{{trans_choice('general.description',2)}}</label>
                            <div class="col-md-9">
       <textarea name="notes" class="form-control"
                 id="document_notes" rows="3">{{old('notes')}}</textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="document_attachment"
                                   class="control-label col-md-3">{{trans_choice('general.attachment',1)}}</label>
                            <div class="col-md-9">
                                <input type="file" name="attachment" class="form-control" required
                                       id="document_attachment">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add_note_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.note',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/note/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_note_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="note_notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',1)}}</label>
                            <div class="col-md-9">
       <textarea name="notes" class="form-control"
                 id="note_notes" rows="3" required>{{old('notes')}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="view_note">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_note">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


    <div class="modal fade" id="add_charge_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/charge/store')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="add_charge_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="charge_id"
                                   class="control-label col-md-3">{{trans_choice('general.charge',1)}}</label>
                            <div class="col-md-9">
                                <select name="charge_id" class="select2 form-control"
                                        id="charge_id" required>
                                    <option></option>
                                    @foreach(\App\Models\SavingsProductCharge::where('savings_product_id',$savings->savings_product->id)->get() as $key)
                                        @if(!empty($key->charge))
                                            @if($key->charge->charge_type=="specified_due_date")
                                                <option value="{{$key->charge_id}}">{{$key->charge->name}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="charge_date"
                                   class="control-label col-md-3"> {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="date"
                                       class="form-control date-picker"
                                       value="{{date("Y-m-d")}}"
                                       required id="charge_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="charge_amount"
                                   class="control-label col-md-3">{{trans_choice('general.amount',1)}}</label>
                            <div class="col-md-9">
                                <input type="number" name="amount" class="form-control"
                                       value=""
                                       required id="charge_amount">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="charge_notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                            <div class="col-md-9">
                                                     <textarea name="notes" class="form-control"
                                                               id="charge_notes"
                                                               rows="3">{{old('notes')}}</textarea>
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
    <div class="modal fade" id="write_off_savings_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.write_off',1)}} {{trans_choice('general.savings',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/write_off')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="write_off_savings_form">
                    {{csrf_field()}}
                    <div class="form-group">
                        <label for="written_off_date"
                               class="control-label col-md-3"> {{trans_choice('general.date',1)}}</label>
                        <div class="col-md-9">
                            <input type="text" name="written_off_date"
                                   class="form-control date-picker"
                                   value="{{date("Y-m-d")}}"
                                   required id="written_off_date">
                        </div>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="written_off_notes"
                                   class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                            <div class="col-md-9">
                                                     <textarea name="written_off_notes" class="form-control"
                                                               id="written_off_notes" rows="3"
                                                               required>{{old('written_off_notes')}}</textarea>
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
    <div class="modal fade" id="pdf_statement_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.pdf',1)}} {{trans_choice('general.statement',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/pdf_statement')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" target="_blank" id="pdf_statement_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="start_date"
                                   class="control-label col-md-3">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="start_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="start_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end_date"
                                   class="control-label col-md-3">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="end_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="email_statement_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.email',1)}} {{trans_choice('general.statement',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/email_statement')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="email_statement_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="start_date"
                                   class="control-label col-md-3">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="start_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="start_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end_date"
                                   class="control-label col-md-3">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="end_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="print_statement_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.print',1)}} {{trans_choice('general.statement',1)}}</h4>
                </div>
                <form method="post" action="{{url('savings/'.$savings->id.'/print_statement')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" target="_blank" id="print_statement_form">
                    {{csrf_field()}}
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="start_date"
                                   class="control-label col-md-3">{{trans_choice('general.start',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="start_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="start_date">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="end_date"
                                   class="control-label col-md-3">{{trans_choice('general.end',1)}} {{trans_choice('general.date',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="end_date" class="form-control"
                                       value="{{date("Y-m-d")}}"
                                       required id="end_date">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-left"
                                data-dismiss="modal">{{trans_choice('general.close',1)}}</button>
                        <button type="submit" class="btn btn-primary">{{trans_choice('general.save',1)}}</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@section('footer-scripts')
    <script>

        $('#view_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('savings/note') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('savings/note') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#charge_id').change(function (e) {
            var id = $('#charge_id').val();
            var url = "{!!  url('savings/product')  !!}/" + id + "/get_charge_detail";
            $.ajax({
                type: 'GET',
                url: url,
                dataType: "json",
                success: function (data) {
                    $('#charge_amount').val(data.amount);
                },
                error: function (data) {
                    swal({
                        title: 'Error',
                        text: 'An Error occurred while fetching charge details, please try again',
                        type: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ok',
                        timer: 2000
                    })
                }
            });
            $.getJSON(url, function (data) {
                $.each(data, function (index, item) {
                    items += "<option value='" + item.id + "'>" + item.name + "</option>";
                });
                $("#charges_dropdown").html(items);
            });
        });

        $("#add_document_form").validate();
        $("#add_collateral_form").validate();
        $("#add_guarantor_form").validate();
        $("#add_note_form").validate();
        $("#approve_savings_form").validate();
        $("#decline_savings_form").validate();
        $("#add_charge_form").validate();
        $("#waive_interest_form").validate();
        $("#write_off_savings_form").validate();
        $('#data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[4, "desc"]],
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
        $('#repayments-data-table').DataTable({
            dom: 'frtip',
            "paging": true,
            "lengthChange": true,
            "displayLength": 15,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": true,
            "order": [[1, "asc"]],
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
