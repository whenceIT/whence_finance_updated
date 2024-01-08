@extends('layouts.master')
@section('title')
    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.detail',2) }}
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="panel ">
                <div class="panel-heading">
                    <h6 class="panel-title">{{$loan->loan_product->name}}(#{{$loan->id}})
                        @if($loan->defaulted == 'yes')
                        <span style="color: red;">(Defaulted)</span>
                        @endif
                    </h6>

                    <div class="heading-elements">

                    </div>
                </div>
                <div class="panel-body">
                    @if($loan->status=="pending")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('loans.approve'))
                                        <a href="#" data-toggle="modal" data-target="#approve_loan_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-check"></i>&nbsp;{{trans_choice('general.approve',1)}}
                                        </a>
                                        <a href="#" data-toggle="modal" data-target="#decline_loan_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-times"></i>&nbsp;{{trans_choice('general.decline',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.update'))
                                        <a href="{{ url('loan/'.$loan->id.'/edit') }}" class="btn btn-primary"><i
                                                    class="fa fa-edit"></i>&nbsp;{{trans_choice('general.edit',1)}}</a>
                                    @endif
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-expanded="false">More<span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                        @if(Sentinel::hasAccess('loans.update'))
                                            <li>
                                                <a href="#"
                                                   data-toggle="modal" data-target="#change_loan_officer_modal">
                                                    {{ trans_choice('general.change',1) }} {{ trans_choice('general.loan',1) }} {{ trans_choice('general.officer',1) }}</a>
                                            </li>
                                        @endif
                                       
                                        @if(Sentinel::hasAccess('loans.delete'))
                                            <li>
                                                <a href="{{ url('loan/'.$loan->id.'/delete') }}"
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
                                    @if($loan->client_type=="client")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}</a>
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->full_name}}</a>
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($loan->client_type=="group")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.group',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->group))
                                                <a href="{{url('group/'.$loan->group_id.'/show')}}">{{$loan->group->name}}</a>
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.currency',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->currency))
                                                {{$loan->currency->name}} 
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_officer))
                                                {{$loan->loan_officer->first_name}} {{$loan->loan_officer->last_name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.external_id',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ $loan->external_id }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-5 col-md-5">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_purpose))
                                                {{$loan->loan_purpose->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.proposed',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->principal,$loan->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.expected',1)}} {{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $loan->expected_disbursement_date }}</span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                        @if(Sentinel::hasAccess('loans.approve'))
                            <div class="modal fade" id="approve_loan_modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">{{trans_choice('general.approve',1)}} {{trans_choice('general.loan',1)}}</h4>
                                        </div>
                                        <form method="post" action="{{url('loan/'.$loan->id.'/approve')}}"
                                              class="form-horizontal "
                                              enctype="multipart/form-data" id="approve_loan_form">
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
                                                    <label for="approved_amount"
                                                           class="control-label col-md-3">{{trans_choice('general.amount',1)}}</label>
                                                    <div class="col-md-9">
                                                        <input type="number" name="approved_amount" class="form-control"
                                                               value="{{$loan->applied_amount}}"
                                                               required id="approved_amount">
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
                            <div class="modal fade" id="decline_loan_modal">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span></button>
                                            <h4 class="modal-title">{{trans_choice('general.decline',1)}} {{trans_choice('general.loan',1)}}</h4>
                                        </div>
                                        <form method="post" action="{{url('loan/'.$loan->id.'/decline')}}"
                                              class="form-horizontal "
                                              enctype="multipart/form-data" id="decline_loan_form">
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
                        @endif
              

                    @endif
                    @if($loan->status=="approved")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('loans.disburse'))
                                        <a href="#" data-toggle="modal" data-target="#disburse_loan_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-flag"></i>&nbsp;{{trans_choice('general.disburse',1)}}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.undo_approval'))
                                        <a href="{{ url('loan/'.$loan->id.'/unapprove') }}"
                                           class="btn btn-primary confirm"><i
                                                    class="fa fa-undo"></i>&nbsp;{{trans_choice('general.undo',1)}}
                                            &nbsp;{{trans_choice('general.approval',1)}}</a>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.update'))
                                        <a href="#"
                                           data-toggle="modal" data-target="#change_loan_officer_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-user"></i>&nbsp;
                                            {{ trans_choice('general.change',1) }} {{ trans_choice('general.loan',1) }} {{ trans_choice('general.officer',1) }}
                                        </a>
                                    @endif
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-expanded="false">More<span class="caret"></span></button>

                                </div>
                            </div>
                        </div>
                        <div class="row m-t-20" style="">
                            <div class="col-sm-7 col-md-7">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    @if($loan->client_type=="client")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}</a>
                                                    ({{trans_choice('general.individual',1)}})
                                                @else
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->full_name}}</a>
                                                    ({{trans_choice('general.business',1)}})
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($loan->client_type=="group")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.group',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->group))
                                                <a href="{{url('group/'.$loan->group_id.'/show')}}">{{$loan->group->name}}</a>
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.currency',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->currency))
                                                {{$loan->currency->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_officer))
                                                {{$loan->loan_officer->first_name}} {{$loan->loan_officer->last_name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.external_id',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ $loan->external_id }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-sm-5 col-md-5">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_purpose))
                                                {{$loan->loan_purpose->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.proposed',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->applied_amount,$loan->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.approved',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->approved_amount,$loan->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.expected',1)}} {{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $loan->expected_disbursement_date }}</span>
                                        </td>
                                    </tr>

                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div class="modal fade" id="disburse_loan_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.disburse',1)}} {{trans_choice('general.loan',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/disburse')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="disburse_loan_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="disbursement_date"
                                                       class="control-label col-md-4">{{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="disbursement_date"
                                                           class="form-control date-picker"
                                                           value="{{$loan->expected_disbursement_date}}"
                                                           required id="disbursement_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first_repayment_date"
                                                       class="control-label col-md-4">{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="first_repayment_date"
                                                           class="form-control date-picker"
                                                           value="{{$loan->expected_first_repayment_date}}"
                                                           required id="first_repayment_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_type_id"
                                                       class="control-label col-md-4">{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}
                                                </label>
                                                <div class="col-md-8">
                                                    <select name="payment_type_id" class="form-control select2"
                                                            id="payment_type_id" required>
                                                        <option></option>
                                                        @foreach(\App\Models\PaymentType::all() as $key)
                                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="approved_amount"
                                                       class="control-label col-md-4">{{trans_choice('general.show',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</label>
                                                <div class="col-md-8">
                                                    <button type="button" class="btn btn-primary" data-toggle="collapse"
                                                            data-target="#show_payment_details">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="show_payment_details" class="collapse">
                                                <div class="form-group">
                                                    <label for="account_number"
                                                           class="control-label col-md-4">{{trans_choice('general.account',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="account_number"
                                                               class="form-control"
                                                               value=""
                                                               id="account_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="cheque_number"
                                                           class="control-label col-md-4">{{trans_choice('general.cheque',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="cheque_number"
                                                               class="form-control"
                                                               value=""
                                                               id="cheque_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="routing_code"
                                                           class="control-label col-md-4">{{trans_choice('general.routing_code',1)}}</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="routing_code"
                                                               class="form-control"
                                                               value=""
                                                               id="routing_code">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="receipt_number"
                                                           class="control-label col-md-4">{{trans_choice('general.receipt',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="receipt_number"
                                                               class="form-control"
                                                               value=""
                                                               id="receipt_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="bank"
                                                           class="control-label col-md-4">{{trans_choice('general.bank',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="bank"
                                                               class="form-control"
                                                               value=""
                                                               id="bank">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="disbursed_notes"
                                                       class="control-label col-md-4">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-8">
                                                     <textarea name="disbursed_notes" class="form-control"
                                                               id="disbursed_notes"
                                                               rows="3">{{old('disbursed_notes')}}</textarea>
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
                    @if($loan->status=="disbursed")
                        <div class="row">
                            <div class="col-md-12">
                                <div class="pull-right btn-group">
                                    @if(Sentinel::hasAccess('loans.transactions.create'))
                                        <a href="{{ url('loan/'.$loan->id.'/repayment/create') }}"
                                           class="btn btn-primary"><i
                                                    class="fa fa-dollar"></i>&nbsp;{{trans_choice('general.make',1)}} {{trans_choice('general.repayment',1)}}
                                        </a>
                                    @endif

                                    @if(Sentinel::hasAccess('loans.transactions.create'))
                                        <a href="#" data-toggle="modal" data-target="#reschedule_loan_modal"
                                           class="btn btn-primary" onclick="sum()"><i
                                                    class="fa fa-clock-o"></i>&nbsp;{{trans_choice('general.reschedule',1)}} {{trans_choice('general.loan',1)}}
                                        </a>
                                    @endif



                                    @if(Sentinel::hasAccess('loans.charge.create'))
                                        <a href="#"
                                           data-toggle="modal" data-target="#add_charge_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-plus"></i>&nbsp;{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}
                                        </a>
                                    @endif

                                    @if(Sentinel::hasAccess('loans.update'))
                                        <a href="#"
                                           data-toggle="modal" data-target="#add_refund_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-user"></i>
                                           Refund
                                        </a>
                                    @endif








                                    @if(Sentinel::hasAccess('loans.update'))
                                        <a href="#"
                                           data-toggle="modal" data-target="#change_loan_officer_modal"
                                           class="btn btn-primary"><i
                                                    class="fa fa-user"></i>&nbsp;
                                            {{ trans_choice('general.change',1) }} {{ trans_choice('general.loan',1) }} {{ trans_choice('general.officer',1) }}
                                        </a>
                                    @endif
                                    @if(Sentinel::hasAccess('loans.undo_disbursement'))
                                        <a href="{{ url('loan/'.$loan->id.'/undisburse') }}"
                                           class="btn btn-primary confirm"><i
                                                    class="fa fa-undo"></i>&nbsp;{{trans_choice('general.undo',1)}}
                                            &nbsp;{{trans_choice('general.disbursement',1)}}</a>
                                    @endif
                                    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"
                                            aria-expanded="false">More <span class="caret"></span></button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">

                                        @if(Sentinel::hasAccess('loans.waive_interest'))
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#waive_interest_modal">
                                                    {{ trans_choice('general.waive',1) }} {{ trans_choice('general.interest',1) }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.email_schedule'))
                                            <li>
                                                <a href="{{ url('loan/'.$loan->id.'/email_schedule') }}" class="delete">
                                                    {{ trans_choice('general.email',1) }} {{ trans_choice('general.schedule',1) }}</a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.pdf_schedule'))
                                            <li>
                                                <a href="{{ url('loan/'.$loan->id.'/pdf_schedule') }}" target="_blank">
                                                    {{ trans_choice('general.pdf',1) }} {{ trans_choice('general.schedule',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.pdf_schedule'))
                                            <li>
                                                <a href="{{ url('loan/'.$loan->id.'/print_schedule') }}"
                                                   target="_blank">
                                                    {{ trans_choice('general.print',1) }} {{ trans_choice('general.schedule',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.pdf_schedule'))
                                            <li>
                                                <a href="{{ url('loan/'.$loan->id.'/print_statement') }}"
                                                   target="_blank">
                                                    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.statement',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.write_off'))
                                            <li>
                                                <a href="#" data-toggle="modal" data-target="#write_off_loan_modal">
                                                    {{ trans_choice('general.write_off',1) }} </a>
                                            </li>
                                        @endif
                                        @if(Sentinel::hasAccess('loans.close'))
                                            <li class="hidden">
                                                <a href="#" data-toggle="modal" data-target="#close_loan_modal">
                                                    {{ trans_choice('general.close',1) }} </a> 
                                            </li>
                                        @endif

                                        @if(Sentinel::hasAccess('loans.approve'))
                                            <li>
                                                <a href="#"
                                                   data-toggle="modal" data-target="#withdraw_loan_modal">
                                                    Terminate</a>
                                            </li>
                                        @endif

                                        @if(Sentinel::hasAccess('loans.waive_interest'))
                                            <li>
                                               <a href="{{ url('loan/'.$loan->id.'/set_defaulted') }}">
                                                Defaulted
                                               </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </div>













                        </div>









                        <div class="row m-t-20" style="">
                            <div class="col-sm-8 col-md-8">
                                <?php
                                $loan_allocation = \App\Helpers\GeneralHelper::loan_items($loan->id);
                                $decimals = $loan->loan_product->decimals;
                                $timely_repayments = 0;
                                $total_repayments = 0;
                                $days_in_arrears = 0;
                                $amount_in_arrears = 0;
                                $amount_due = 0;
                                $amount_paid = 0;
                                $late_count = 0;
                                foreach (\App\Models\LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', '<', date("Y-m-d"))->orderBy('due_date', 'asc')->get() as $schedule) {
                                    $total_repayments = $total_repayments + 1;
                                    $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                                    if (!empty($schedule->from_date)) {
                                        if (strtotime($schedule->due_date) > strtotime($schedule->from_date)) {
                                            $timely_repayments = $timely_repayments + 1;
                                        }
                                    }
                                    if ($amount_in_arrears > 0) {
                                        $late_count++;
                                        if ($late_count == 1) {
                                            $overdue_date = $schedule->due_date;
                                        }
                                    }

                                }
                                if ($amount_in_arrears > 0) {
                                    $date1 = new DateTime($overdue_date);
                                    $date2 = new DateTime(date("Y-m-d"));
                                    $days_in_arrears = $date2->diff($date1)->format("%a");
                                }
                                ?>
                                <h4 class="">{{ trans_choice('general.current',1) }} {{ trans_choice('general.balance',1) }}
                                    :
                                    <b>{{number_format(($loan_allocation["principal"]-$loan_allocation["principal_paid"]-$loan_allocation["principal_waived"]-$loan_allocation["principal_written_off"])+($loan_allocation["interest"]-$loan_allocation["interest_paid"]-$loan_allocation["interest_waived"]-$loan_allocation["interest_written_off"])+($loan_allocation["fees"]-$loan_allocation["fees_paid"]-$loan_allocation["fees_waived"]-$loan_allocation["fees_written_off"])+($loan_allocation["penalty"]-$loan_allocation["penalty_paid"]-$loan_allocation["penalty_waived"]-$loan_allocation["penalty_written_off"]),$decimals)}}</b>
                                </h4>
                                <h4 class="">
                                    {{ trans_choice('general.timely',1) }} {{ trans_choice('general.repayment',2) }}:
                                    @if($total_repayments>0)
                                        <b>{{$timely_repayments*100/$total_repayments}}%</b>
                                    @else
                                        <b> 0%</b>
                                    @endif
                                </h4>
                                <h4 class="">
                                    {{ trans_choice('general.amount',1) }} {{ trans_choice('general.in',1) }} {{ trans_choice('general.arrears',1) }}
                                    :
                                    <b>{{number_format($amount_in_arrears,$decimals)}}</b>
                                </h4>
                                <h4 class="">
                                    {{ trans_choice('general.day',2) }} {{ trans_choice('general.in',1) }} {{ trans_choice('general.arrears',1) }}
                                    :
                                    <b>{{$days_in_arrears}}</b>
                                </h4>
                                <table class="pretty displayschedule" id="summarytable">
                                    <thead>
                                    <tr>
                                        <th class="empty"></th>
                                        <th>{{ trans_choice('general.contract',1) }}</th>
                                        <th>{{ trans_choice('general.paid',1) }}</th>
                                        <th>{{ trans_choice('general.waived',1) }}</th>
                                        <th>{{ trans_choice('general.written_off',1) }}</th>
                                        <th>{{ trans_choice('general.outstanding',1) }}</th>
                                        <th>{{ trans_choice('general.overdue',1) }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th>{{ trans_choice('general.principal',1) }}</th>
                                        <td>{{number_format($loan_allocation["principal"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["principal_paid"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["principal_waived"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["principal_written_off"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["principal"]-$loan_allocation["principal_paid"]-$loan_allocation["principal_waived"]-$loan_allocation["principal_written_off"],$decimals)}}</td>
                                        <td>{{number_format(0,$decimals)}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans_choice('general.interest',1) }}</th>
                                        <td>{{number_format($loan_allocation["interest"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["interest_paid"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["interest_waived"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["interest_written_off"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["interest"]-$loan_allocation["interest_paid"]-$loan_allocation["interest_waived"]-$loan_allocation["interest_written_off"],$decimals)}}</td>
                                        <td>{{number_format(0,$decimals)}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans_choice('general.fee',2) }}</th>
                                        <td>{{number_format($loan_allocation["fees"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["fees_paid"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["fees_waived"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["fees_written_off"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["fees"]-$loan_allocation["fees_paid"]-$loan_allocation["fees_waived"]-$loan_allocation["fees_written_off"],$decimals)}}</td>
                                        <td>{{number_format(0,$decimals)}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ trans_choice('general.penalty',1) }}</th>
                                        <td>{{number_format($loan_allocation["penalty"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["penalty_paid"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["penalty_waived"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["penalty_written_off"],$decimals)}}</td>
                                        <td>{{number_format($loan_allocation["penalty"]-$loan_allocation["penalty_paid"]-$loan_allocation["penalty_waived"]-$loan_allocation["penalty_written_off"],$decimals)}}</td>
                                        <td>{{number_format(0,$decimals)}}</td>
                                    </tr>
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th>{{ trans_choice('general.total',1) }}</th>
                                        <th>{{number_format($loan_allocation["principal"]+$loan_allocation["interest"]+$loan_allocation["fees"]+$loan_allocation["penalty"],$decimals)}}</th>
                                        <th>{{number_format($loan_allocation["principal_paid"]+$loan_allocation["interest_paid"]+$loan_allocation["fees_paid"]+$loan_allocation["penalty_paid"],$decimals)}}</th>
                                        <th>{{number_format($loan_allocation["principal_waived"]+$loan_allocation["interest_waived"]+$loan_allocation["fees_waived"]+$loan_allocation["penalty_waived"],$decimals)}}</th>
                                        <th>{{number_format($loan_allocation["principal_written_off"]+$loan_allocation["interest_written_off"]+$loan_allocation["fees_written_off"]+$loan_allocation["penalty_written_off"],$decimals)}}</th>
                                        <th>{{number_format(($loan_allocation["principal"]-$loan_allocation["principal_paid"]-$loan_allocation["principal_waived"]-$loan_allocation["principal_written_off"])+($loan_allocation["interest"]-$loan_allocation["interest_paid"]-$loan_allocation["interest_waived"]-$loan_allocation["interest_written_off"])+($loan_allocation["fees"]-$loan_allocation["fees_paid"]-$loan_allocation["fees_waived"]-$loan_allocation["fees_written_off"])+($loan_allocation["penalty"]-$loan_allocation["penalty_paid"]-$loan_allocation["penalty_waived"]-$loan_allocation["penalty_written_off"]),$decimals)}}</th>
                                        <th>{{number_format(0,$decimals)}}</th>
                                    </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="col-sm-4 col-md-4">
                                <table class="table table-striped table-bordered">
                                    <tbody>
                                    @if($loan->client_type=="client")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.client',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->client))
                                                @if($loan->client->client_type=="individual")
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->first_name}} {{$loan->client->middle_name}} {{$loan->client->last_name}}</a>

                                                @else
                                                    <a href="{{url('client/'.$loan->client_id.'/show')}}">{{$loan->client->full_name}}</a>
                                                @endif
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($loan->client_type=="group")
                                        <tr>
                                            <th class="table-bold-loan">{{trans_choice('general.group',1)}}</th>
                                            <td>
                                        <span class="padded-td">
                                             @if(!empty($loan->group))
                                                <a href="{{url('group/'.$loan->group_id.'/show')}}">{{$loan->group->name}}</a>
                                            @endif
                                        </span>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.currency',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->currency))
                                                {{$loan->currency->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_officer))
                                                {{$loan->loan_officer->first_name}} {{$loan->loan_officer->last_name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.external_id',1)}} </th>
                                        <td>
                                            <span class="padded-td">{{ $loan->external_id }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.loan',1)}} {{trans_choice('general.purpose',1)}}</th>
                                        <td>
                                        <span class="padded-td">
                                              @if(!empty($loan->loan_purpose))
                                                {{$loan->loan_purpose->name}}
                                            @endif
                                        </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.proposed',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->applied_amount,$loan->loan_product->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.approved',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->approved_amount,$loan->loan_product->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan"> {{trans_choice('general.disbursed',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ number_format($loan->principal,$loan->loan_product->decimals) }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="table-bold-loan">{{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</th>
                                        <td>
                                            <span class="padded-td">{{ $loan->disbursement_date }}</span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>









                        </div>



                        <div class="modal fade" id="withdraw_loan_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        <h4 class="modal-title">{{trans_choice('general.withdraw',1)}} {{trans_choice('general.loan',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/withdraw')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="withdraw_loan_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                       <div class="form-group">
                                       <label for="amount"
                           class="control-label col-md-3">Outstanding Principal {{trans_choice('general.amount',1)}}</label>
                    <div class="col-md-9">
                        <input type="text" name="amount" class="form-control"
                               value="{{$loan_allocation["principal"]-$loan_allocation["principal_paid"]-$loan_allocation["principal_waived"]-$loan_allocation["principal_written_off"],$decimals}}"
                               required id="amount">
                    </div><BR><br><br>
                


                                       <label for="fund_id"
                           class="control-label col-md-3">Terminate against
                        <i class="fa fa-question-circle " data-toggle="tooltip"
                           data-title="Choose an account to use against this Termination."></i>
                    </label>
                    <div class="col-md-9">
                        <select name="fund_id" class="form-control select2" id="fund_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->get() as $key)
                             <option value="{{$key->id}}">{{$key->name}}</option>
                         @endforeach
                        </select>
                    </div>
                                       </div>
                     
                                       <div class="form-group">
                    <label for="opening_date"
                           class="control-label col-md-3">Termination Date</label>
                    <div class="col-md-9">
                        <input type="text" name="date" class="form-control date-picker"
                               value="{{date("Y-m-d")}}"
                               required id="date">
                    </div>
                </div>



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























                        <div class="modal fade" id="disburse_loan_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.disburse',1)}} {{trans_choice('general.loan',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/disburse_daily')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="disburse_loan_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="disbursement_date"
                                                       class="control-label col-md-4">{{trans_choice('general.disbursement',1)}} {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="disbursement_date"
                                                           class="form-control date-picker"
                                                           value="{{$loan->expected_disbursement_date}}"
                                                           required id="disbursement_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="first_repayment_date"
                                                       class="control-label col-md-4">{{trans_choice('general.first',1)}} {{trans_choice('general.repayment',1)}} {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-8">
                                                    <input type="text" name="first_repayment_date"
                                                           class="form-control date-picker"
                                                           value="{{$loan->expected_first_repayment_date}}"
                                                           required id="first_repayment_date">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="payment_type_id"
                                                       class="control-label col-md-4">{{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}
                                                </label>
                                                <div class="col-md-8">
                                                    <select name="payment_type_id" class="form-control select2"
                                                            id="payment_type_id" required>
                                                        <option></option>
                                                        @foreach(\App\Models\PaymentType::all() as $key)
                                                            <option value="{{$key->id}}">{{$key->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="approved_amount"
                                                       class="control-label col-md-4">{{trans_choice('general.show',1)}} {{trans_choice('general.payment',1)}} {{trans_choice('general.detail',2)}}</label>
                                                <div class="col-md-8">
                                                    <button type="button" class="btn btn-primary" data-toggle="collapse"
                                                            data-target="#show_payment_details">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div id="show_payment_details" class="collapse">
                                                <div class="form-group">
                                                    <label for="account_number"
                                                           class="control-label col-md-4">{{trans_choice('general.account',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="account_number"
                                                               class="form-control"
                                                               value=""
                                                               id="account_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="cheque_number"
                                                           class="control-label col-md-4">{{trans_choice('general.cheque',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="cheque_number"
                                                               class="form-control"
                                                               value=""
                                                               id="cheque_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="routing_code"
                                                           class="control-label col-md-4">{{trans_choice('general.routing_code',1)}}</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="routing_code"
                                                               class="form-control"
                                                               value=""
                                                               id="routing_code">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="receipt_number"
                                                           class="control-label col-md-4">{{trans_choice('general.receipt',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="receipt_number"
                                                               class="form-control"
                                                               value=""
                                                               id="receipt_number">
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="bank"
                                                           class="control-label col-md-4">{{trans_choice('general.bank',1)}}
                                                        #</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="bank"
                                                               class="form-control"
                                                               value=""
                                                               id="bank">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="disbursed_notes"
                                                       class="control-label col-md-4">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-8">
                                                     <textarea name="disbursed_notes" class="form-control"
                                                               id="disbursed_notes"
                                                               rows="3">{{old('disbursed_notes')}}</textarea>
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
                    @if($loan->status=="withdrawn")
                        <div class="row">
                            <div class="col-md-12">
                                <blockquote>
                                    <p>{{$loan->withdrawn_notes}}</p>
                                    <small>{{$loan->withdrawn_date}}</small>
                                </blockquote>
                            </div>
                        </div>
                    @endif
                    @if($loan->status=="declined")
                        <div class="row">
                            <div class="col-md-12">
                                <blockquote>
                                    <p>{{$loan->declined_notes}}</p>
                                    <small>{{$loan->declined_date}}</small>
                                </blockquote>
                            </div>
                        </div>
                    @endif
                    @if($loan->status=="written_off")
                        <div class="row">
                            <div class="col-md-12">
                                <blockquote>
                                    <p>{{$loan->written_off_notes}}</p>
                                    <small>{{$loan->written_off_date}}</small>
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
                    @if(Sentinel::hasAccess('loans.view'))
                        <li class="active"><a href="#account_details" data-toggle="tab"
                                              aria-expanded="false">{{trans_choice('general.account',1)}} {{trans_choice('general.detail',2)}}</a>
                        </li>
                    @endif
                    @if($loan->status=="disbursed" || $loan->status=="closed" || $loan->status=="written_off" || $loan->status=="rescheduled" )
                        @if(Sentinel::hasAccess('loans.view_repayment_schedule'))
                            <li class=""><a href="#repayment_schedule" data-toggle="tab"
                                            aria-expanded="false">{{trans_choice('general.repayment',1)}} {{trans_choice('general.schedule',1)}}</a>
                            </li>
                        @endif
                        @if(Sentinel::hasAccess('loans.transactions.view'))
                            <li class=""><a href="#transactions" data-toggle="tab"
                                            aria-expanded="false">{{trans_choice('general.transaction',2)}}</a>
                            </li>
                        @endif
                    @endif
                    @if(Sentinel::hasAccess('loans.documents.view'))
                        <li class=""><a href="#documents" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.document',2)}}</a>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('loans.documents.view'))
                        <li class=""><a href="#collateral" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.collateral',1)}}</a>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('loans.guarantors.view'))
                        <li class=""><a href="#guarantors" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.guarantor',2)}}</a>
                        </li>
                    @endif
                    @if(Sentinel::hasAccess('loans.notes.view'))
                        <li class=""><a href="#notes" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.note',2)}}</a>
                        </li>
                    @endif
                    @if($loan->client_type=="group")
                        <li class=""><a href="#group_allocation" data-toggle="tab"
                                        aria-expanded="false">{{trans_choice('general.group',1)}} {{trans_choice('general.allocation',1)}}</a>
                        </li>
                    @endif
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="account_details">
                        <table class="table table-striped table-hover">
                            <tr>
                                <td>{{trans_choice('general.repayment',1)}} {{trans_choice('general.strategy',1)}}</td>
                                <td>
                                    @if($loan->loan_product->loan_transaction_strategy=="penalty_fees_interest_principal")
                                        {{trans_choice('general.penalty_fees_interest_principal',1)}}
                                    @endif
                                    @if($loan->loan_product->loan_transaction_strategy=="principal_interest_penalty_fees")
                                        {{trans_choice('general.principal_interest_penalty_fees',1)}}
                                    @endif
                                    @if($loan->loan_product->loan_transaction_strategy=="interest_principal_penalty_fees")
                                        {{trans_choice('general.interest_principal_penalty_fees',1)}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.loan',1)}} {{trans_choice('general.term',1)}}</td>
                                <td>
                                    {{$loan->loan_term}}
                                    @if($loan->loan_term_type=="days")
                                        {{trans_choice('general.day',2)}}
                                    @endif
                                    @if($loan->loan_term_type=="weeks")
                                        {{trans_choice('general.week',2)}}
                                    @endif
                                    @if($loan->loan_term_type=="months")
                                        {{trans_choice('general.month',2)}}
                                    @endif
                                    @if($loan->loan_term_type=="years")
                                        {{trans_choice('general.year',2)}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.repayment',2)}}</td>
                                <td>
                                    {{trans_choice('general.every',1)}} {{$loan->repayment_frequency}}
                                    @if($loan->repayment_frequency_type=="days")
                                        {{trans_choice('general.day',2)}}
                                    @endif
                                    @if($loan->repayment_frequency_type=="weeks")
                                        {{trans_choice('general.week',2)}}
                                    @endif
                                    @if($loan->repayment_frequency_type=="months")
                                        {{trans_choice('general.month',2)}}
                                    @endif
                                    @if($loan->repayment_frequency_type=="years")
                                        {{trans_choice('general.year',2)}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.interest',1)}} {{trans_choice('general.method',1)}}</td>
                                <td>
                                    @if($loan->interest_method=="flat")
                                        {{trans_choice('general.flat',1)}}
                                    @endif
                                    @if($loan->interest_method=="declining_balance")
                                        {{trans_choice('general.declining_balance',1)}}
                                        @if($loan->armotization_method=="equal_installment")
                                            -{{trans_choice('general.equal_installment',1)}}
                                        @endif
                                        @if($loan->armotization_method=="equal_principal")
                                            -{{trans_choice('general.equal_principal',1)}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.interest',1)}}</td>
                                <td>
                                    {{$loan->interest_rate}} %
                                    @if($loan->override_interest==0)
                                        {{trans_choice('general.per',1)}}
                                        @if($loan->interest_rate_type=="day")
                                            {{trans_choice('general.day',1)}}
                                        @endif
                                        @if($loan->interest_rate_type=="week")
                                            {{trans_choice('general.week',1)}}
                                        @endif
                                        @if($loan->interest_rate_type=="month")
                                            {{trans_choice('general.month',1)}}
                                        @endif
                                        @if($loan->interest_rate_type=="year")
                                            {{trans_choice('general.year',1)}}
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.grace_on_principal',1)}}</td>
                                <td>
                                    {{$loan->grace_on_principal}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.grace_on_interest_payment',1)}}</td>
                                <td>
                                    {{$loan->grace_on_interest_payment}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.grace_on_interest_charged',1)}}</td>
                                <td>
                                    {{$loan->grace_on_interest_charged}}
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.fund',1)}}</td>
                                <td>
                                    @if(!empty($loan->fund))
                                        {{$loan->fund->name}}
                                    @endif
                                </td>
                            </tr>
                            @foreach(\App\Models\CustomFieldMeta::where('category', 'loans')->where('parent_id', $loan->id)->get() as $key)
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
                                    {{$loan->created_date}}
                                    @if(!empty($loan->created_by))
                                        by {{$loan->created_by->first_name}} {{$loan->created_by->last_name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.approved',1)}} {{trans_choice('general.on',1)}}</td>
                                <td>
                                    {{$loan->approved_date}}
                                    @if(!empty($loan->approved_by))
                                        by {{$loan->approved_by->first_name}} {{$loan->approved_by->last_name}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>{{trans_choice('general.disbursed',1)}} {{trans_choice('general.on',1)}}</td>
                                <td>
                                    {{$loan->disbursed_date}}
                                    @if(!empty($loan->disbursed_by))
                                        by {{$loan->disbursed_by->first_name}} {{$loan->disbursed_by->last_name}}
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    @if(Sentinel::hasAccess('loans.documents.view'))
                        <div class="tab-pane" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('loans.documents.create'))
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
                                        @foreach(\App\Models\Document::where('record_id',$loan->id)->where('type','loan')->get() as $key)
                                            <tr>
                                                <td>{{ $key->name }}</td>
                                                <td>{!!   $key->notes !!}</td>
                                                <td>
                                                    <a class="" href="{{asset('uploads/'.$key->location)}}"
                                                       target="_blank"><i class="fa fa-download"></i> </a>
                                                    @if(Sentinel::hasAccess('loans.documents.delete'))
                                                        <a class="confirm"
                                                           href="{{url('loan/document/'.$key->id.'/delete')}}"><i
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
                    @if(Sentinel::hasAccess('loans.collateral.view'))
                        <div class="tab-pane" id="collateral">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('loans.collateral.create'))
                                        <a href="#add_collateral_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.collateral',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.type',1) }}</th>
                                            <th>{{ trans('general.value') }}</th>
                                            <th>{{ trans('general.description') }}</th>
                                            <th>{{ trans('general.serial') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($loan->collateral as $key)
                                            <tr>
                                                <td>
                                                    @if(!empty($key->type))
                                                        {{$key->type->name}}
                                                    @endif
                                                </td>
                                                <td>{{$key->value}}</td>
                                                <td>{!!$key->description !!}</td>
                                                <td>{{$key->serial}}</td>
                                                <td>
                                                    <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                       data-target="#view_collateral"><i class="fa fa-eye"></i> </a>
                                                    @if(Sentinel::hasAccess('loans.collateral.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_collateral"><i class="fa fa-edit"></i>
                                                        </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('loans.collateral.delete'))
                                                        <a class="confirm"
                                                           href="{{url('loan/collateral/'.$key->id.'/delete')}}"><i
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
                    @if(Sentinel::hasAccess('loans.guarantors.view'))
                        <div class="tab-pane" id="guarantors">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('loans.guarantors.create'))
                                        <a href="#add_guarantor_modal"
                                           data-toggle="modal" class="btn btn-info pull-right"><i
                                                    class="fa fa-plus"></i> {{trans_choice('general.add',1)}} {{trans_choice('general.guarantor',1)}}
                                        </a>
                                    @endif
                                </div>
                                <div class="col-md-12 table-responsive">
                                    <table id="" class="table table-striped table-condensed table-hover">
                                        <thead>
                                        <tr>
                                            <th>{{trans_choice('general.name',1)}}</th>
                                            <th>{{trans_choice('general.relationship',1)}}</th>
                                            <th>{{trans_choice('general.type',1)}}</th>
                                            <th>{{trans_choice('general.amount',1)}}</th>
                                            <th>{{trans_choice('general.mobile',1)}}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($loan->guarantors as $key)
                                            <tr>
                                                <td>
                                                    @if($key->is_client==1)
                                                        @if(!empty($key->client))
                                                            <a href="{{url('client/'.$key->client_id.'/show')}}">{{ $key->client->first_name }} {{ $key->client->midddle_name }} {{ $key->client->last_name }}</a>
                                                        @endif
                                                    @else
                                                        {{ $key->first_name }} {{ $key->midddle_name }} {{ $key->last_name }}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($key->relationship))
                                                        {{$key->relationship->name}}
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($key->is_client==1)
                                                        {{trans_choice('general.client',1)}}
                                                    @else
                                                        {{trans_choice('general.external',1)}}
                                                    @endif
                                                </td>
                                                <td>{{ $key->amount }}</td>
                                                <td>
                                                    @if($key->is_client==1)
                                                        @if(!empty($key->client))
                                                            {{$key->client->mobile}}
                                                        @endif
                                                    @else
                                                        {{ $key->mobile }}
                                                    @endif


                                                </td>
                                                <td>
                                                    @if($key->is_client==1)
                                                        <a href="{{url('client/'.$key->client_id.'/show')}}"><i
                                                                    class="fa fa-eye"></i> </a>
                                                    @else
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#view_guarantor"><i class="fa fa-eye"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('loans.guarantors.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_guarantor"><i class="fa fa-edit"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('loans.guarantors.delete'))
                                                        <a class="confirm"
                                                           href="{{url('loan/guarantor/'.$key->id.'/delete')}}"><i
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
                    @if(Sentinel::hasAccess('loans.notes.view'))
                        <div class="tab-pane" id="notes">
                            <div class="row">
                                <div class="col-md-12">
                                    @if(Sentinel::hasAccess('loans.notes.create'))
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
                                        @foreach(\App\Models\Note::where('reference_id',$loan->id)->where('type','loan')->get() as $key)
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
                                                    @if(Sentinel::hasAccess('loans.notes.update'))
                                                        <a data-id="{{$key->id}}" href="#" data-toggle="modal"
                                                           data-target="#edit_note"><i class="fa fa-edit"></i> </a>
                                                    @endif
                                                    @if(Sentinel::hasAccess('loans.notes.delete'))
                                                        <a class="confirm"
                                                           href="{{url('loan/note/'.$key->id.'/delete')}}"><i
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
                    @if($loan->status=="disbursed" || $loan->status=="closed" || $loan->status=="written_off" || $loan->status=="rescheduled" )
                        @if(Sentinel::hasAccess('loans.view_repayment_schedule'))
                            <div class="tab-pane" id="repayment_schedule">
                                <div class="row">
                                    <div class="col-md-12">

                                    </div>
                                    <div class="col-md-12 table-responsive">
                                        <table class="pretty displayschedule" id="repaymentschedule"
                                               style="margin-top: 20px;">
                                            <colgroup span="3"></colgroup>
                                            <colgroup span="3">
                                                <col class="lefthighlightcol">
                                                <col>
                                                <col class="righthighlightcol">
                                            </colgroup>
                                            <colgroup span="3">
                                                <col class="lefthighlightcol">
                                                <col>
                                                <col class="righthighlightcol">
                                            </colgroup>
                                            <colgroup span="3"></colgroup>

                                            <thead>
                                            <tr>
                                                <th class="empty" scope="colgroup" colspan="4">&nbsp;</th>
                                                <th class="highlightcol" scope="colgroup"
                                                    colspan="3">{{trans_choice('general.loan',1)}} {{trans_choice('general.amount',1)}} {{trans_choice('general.and',1)}}
                                                    {{trans_choice('general.balance',1)}}
                                                </th>
                                                <th class="highlightcol" scope="colgroup"
                                                    colspan="3">{{trans_choice('general.total',1)}} {{trans_choice('general.cost',1)}} {{trans_choice('general.of',1)}} {{trans_choice('general.loan',1)}}
                                                </th>
                                                <th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
                                            </tr>
                                            <tr>
                                                <th scope="col">#</th>
                                                <th scope="col">{{trans_choice('general.date',1)}}</th>
                                                <th scope="col">{{trans_choice('general.paid',1)}} {{trans_choice('general.by',1)}}</th>
                                                <th scope="col"></th>
                                                <th class="lefthighlightcolheader"
                                                    scope="col">{{trans_choice('general.disbursement',1)}}</th>
                                                <th scope="col">{{trans_choice('general.principal',1)}} {{trans_choice('general.due',1)}}</th>
                                                <th class="righthighlightcolheader"
                                                    scope="col">{{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}}</th>

                                                <th class="lefthighlightcolheader"
                                                    scope="col">{{trans_choice('general.interest',1)}} {{trans_choice('general.due',1)}}</th>
                                                <th scope="col">{{trans_choice('general.fee',2)}}</th>
                                                <th class="righthighlightcolheader"
                                                    scope="col">{{trans_choice('general.penalty',2)}}</th>

                                                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.due',1)}}</th>
                                                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.paid',1)}}</th>
                                                <th scope="col">{{trans_choice('general.total',1)}} {{trans_choice('general.outstanding',1)}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $count = 1;
                                            $principal_balance = $loan->principal;
                                            $total_principal = 0;
                                            $total_interest = 0;
                                            $total_penalties = 0;
                                            $total_fees = 0;
                                            $total_due = 0;
                                            $total_paid = 0;
                                            $total_outstanding = 0;
                                            $disbursement_charges = \App\Models\LoanTransaction::where('loan_id', $loan->id)->where('transaction_type', 'disbursement_fee')->where('reversed', 0)->sum('debit');
                                            ?>
                                            <tr>
                                                <td scope="row"></td>
                                                <td>{{$loan->disbursement_date}}</td>

                                                <td><span style="color: #eb2442;"></span></td>
                                                <td>&nbsp;</td>
                                                <td class="lefthighlightcolheader">{{number_format($loan->principal,$loan->loan_product->decimals)}}</td>
                                                <td></td>
                                                <td class="righthighlightcolheader">{{number_format($loan->principal,$loan->loan_product->decimals)}}</td>

                                                <td class="lefthighlightcolheader">


                                                </td>
                                                <td>{{number_format($disbursement_charges,$loan->loan_product->decimals)}}</td>
                                                <td class="righthighlightcolheader"></td>

                                                <td></td>
                                                <td>{{number_format($disbursement_charges,$loan->loan_product->decimals)}}</td>
                                                <td></td>
                                            </tr>
                                            @foreach($loan->repayment_schedules as $key)
                                                <?php
                                                $principal_balance = $principal_balance - $key->principal - $key->principal_waived - $key->principal_written_off;
                                                $total_principal = $total_principal + $key->principal - $key->principal_waived - $key->principal_written_off;
                                                $total_interest = $total_interest + $key->interest - $key->interest_waived - $key->interest_written_off;
                                                $total_penalties = $total_penalties + $key->penalty - $key->penalty_waived - $key->penalty_written_off;
                                                $total_fees = $total_fees + $key->fees - $key->fees_waived - $key->fees_written_off;
                                                $total_due = $total_due + $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->penalties - $key->penalty_waived - $key->penalty_written_off + $key->fees - $key->fees_waived - $key->fees_written_off;
                                                $total_paid = $total_paid + $key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid;
                                                $total_outstanding = $total_outstanding + $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->penalty - $key->penalty_waived - $key->penalty_written_off + $key->fees - $key->fees_waived - $key->fees_written_off;
                                                ?>
                                                <tr>
                                                    <td scope="row">{{$count}}</td>
                                                    <td>{{$key->due_date}}</td>
                                                    <td>
                                                        @if(!empty($key->from_date))
                                                            @if(strtotime($key->due_date) >strtotime($key->from_date))
                                                                <span style="">{{$key->from_date}}</span>
                                                            @else
                                                                <span style="color: #eb2442;">{{$key->from_date}}</span>
                                                            @endif
                                                        @else

                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if(!empty($key->from_date))
                                                            @if(strtotime($key->due_date) >strtotime($key->from_date))
                                                                <i class="fa fa-check" data-toggle="tooltip"
                                                                   title="{{trans_choice('general.timely',1)}}"></i>
                                                            @else
                                                                <i class="fa fa-question" data-toggle="tooltip"
                                                                   title="{{trans_choice('general.late',1)}}"></i>
                                                            @endif
                                                        @else
                                                            @if(strtotime(date("Y-m-d")) <strtotime($key->due_date))
                                                                &nbsp;
                                                            @else
                                                                <i class="fa fa-question" data-toggle="tooltip"
                                                                   title="{{trans_choice('general.late',1)}}"></i>
                                                            @endif
                                                        @endif
                                                    </td>
                                                    <td class="lefthighlightcolheader"></td>
                                                    <td>
                                                        @if( ($key->principal_waived +$key->principal_written_off)>0)
                                                            <span style="color: #eb2442;"><s>{{number_format($key->principal_waived +$key->principal_written_off,$loan->loan_product->decimals)}}</s></span>
                                                        @endif
                                                        {{number_format($key->principal- $key->principal_waived - $key->principal_written_off,$loan->loan_product->decimals)}}
                                                    </td>
                                                    <td class="righthighlightcolheader">{{number_format($principal_balance,$loan->loan_product->decimals)}}</td>

                                                    <td class="lefthighlightcolheader">
                                                        @if( ($key->interest_waived +$key->interest_written_off)>0)
                                                            <span style="color: #eb2442;"><s>{{number_format($key->interest_waived +$key->interest_written_off,$loan->loan_product->decimals)}}</s></span>
                                                        @endif
                                                        {{number_format($key->interest- $key->interest_waived - $key->interest_written_off,$loan->loan_product->decimals)}}
                                                    </td>
                                                    <td>
                                                        @if( ($key->fees_waived +$key->fees_written_off)>0)
                                                            <span style="color: #eb2442;"><s>{{number_format($key->fees_waived +$key->fees_written_off,$loan->loan_product->decimals)}}</s></span>
                                                        @endif
                                                        {{number_format($key->fees- $key->fees_waived - $key->fees_written_off,$loan->loan_product->decimals)}}
                                                    </td>
                                                    <td class="righthighlightcolheader">
                                                        @if( ($key->penalty_waived +$key->penalty_written_off)>0)
                                                            <span style="color: #eb2442;"><s>{{number_format($key->penalty_waived +$key->penalty_written_off,$loan->loan_product->decimals)}}</s></span>
                                                        @endif
                                                        {{number_format($key->penalty- $key->penalty_waived - $key->penalty_written_off,$loan->loan_product->decimals)}}
                                                    </td>

                                                    <td>{{number_format($key->principal- $key->principal_waived - $key->principal_written_off + $key->interest- $key->interest_waived - $key->interest_written_off + $key->penalty- $key->penalty_waived - $key->penalty_written_off + $key->fees- $key->fees_waived - $key->fees_written_off,$loan->loan_product->decimals)}}</td>
                                                    <td>{{number_format($key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid,$loan->loan_product->decimals)}}</td>
                                                    <td>{{number_format($key->principal- $key->principal_waived - $key->principal_written_off + $key->interest- $key->interest_waived - $key->interest_written_off + $key->penalty- $key->penalty_waived - $key->penalty_written_off + $key->fees- $key->fees_waived - $key->fees_written_off-($key->principal_paid + $key->interest_paid + $key->penalty_paid + $key->fees_paid),$loan->loan_product->decimals)}}</td>
                                                </tr>
                                                <?php
                                                $count++;
                                                ?>
                                            @endforeach
                                            </tbody>
                                            <tfoot class="ui-widget-header">
                                            <tr>
                                                <th colspan="2">{{trans_choice('general.total',1)}}</th>
                                                <th></th>
                                                <th></th>

                                                <th class="lefthighlightcolheader"> {{number_format($loan->principal,$loan->loan_product->decimals)}}</th>
                                                <th> {{number_format($total_principal,$loan->loan_product->decimals)}}</th>
                                                <th class="righthighlightcolheader">&nbsp;</th>

                                                <th class="lefthighlightcolheader">{{number_format($total_interest,$loan->loan_product->decimals)}}</th>
                                                <th>{{number_format($total_fees,$loan->loan_product->decimals)}}</th>
                                                <th class="righthighlightcolheader">{{number_format($total_penalties,$loan->loan_product->decimals)}}</th>

                                                <th>{{number_format($total_due,$loan->loan_product->decimals)}}</th>
                                                <th>{{number_format($total_paid,$loan->loan_product->decimals)}}</th>
                                                <th>{{number_format($total_outstanding-$total_paid,$loan->loan_product->decimals)}}</th>
                                            </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                </div>
                            </div>
                        @endif
                        @if(Sentinel::hasAccess('loans.transactions.view'))
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
                                                @foreach(\App\Models\LoanTransaction::where('loan_id',$loan->id)->whereIn('reversal_type',['user','none'])->orderBy('date','asc')->orderBy('id','asc')->get() as $key)
                                                    <?php
                                                    $balance = $balance + ($key->debit - $key->credit);
                                                    ?>
                                                    <tr>
                                                        <td>{{$key->id}}</td>
                                                        <td>{{$key->date}}</td>
                                                        <td>{{$key->created_at}}</td>
                                                        <td>
                                                            @if($key->transaction_type=='disbursement')
                                                                {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='specified_due_date_fee')
                                                                {{trans_choice('general.specified_due_date',2)}}   {{trans_choice('general.fee',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='installment_fee')
                                                                {{trans_choice('general.installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_installment_fee')
                                                                {{trans_choice('general.overdue_installment_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='loan_rescheduling_fee')
                                                                {{trans_choice('general.loan_rescheduling_fee',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='overdue_maturity')
                                                                {{trans_choice('general.overdue_maturity',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='disbursement_fee')
                                                                {{trans_choice('general.disbursement',1)}} {{trans_choice('general.charge',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.applied',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment')
                                                                {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='adjusted_interest')
                                                                Reloan Interest
                                                            @endif
                                                            @if($key->transaction_type=='penalty')
                                                                {{trans_choice('general.penalty',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='interest_waiver')
                                                                {{trans_choice('general.interest',1)}} {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='waiver')
                                                                {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='charge_waiver')
                                                                {{trans_choice('general.charge',1)}}  {{trans_choice('general.waiver',2)}}
                                                            @endif
                                                            @if($key->transaction_type=='write_off')
                                                                {{trans_choice('general.write_off',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='repayment_disbursement')
                                                                {{trans_choice('general.repayment',1)}} {{trans_choice('general.disbursement',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='write_off_recovery')
                                                                {{trans_choice('general.recovery',1)}} {{trans_choice('general.repayment',1)}}
                                                            @endif
                                                            @if($key->transaction_type=='loan_termination')
                                                                Loan Termination
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
                                                                    @if(Sentinel::hasAccess('loans.transactions.view'))
                                                                        <li>
                                                                            <a href="{{url('loan/transaction/'.$key->id.'/show')}}"><i
                                                                                        class="fa fa-search"></i> {{ trans_choice('general.view',1) }}
                                                                            </a></li>
                                                                        <li>
                                                                    @endif
                                                                    @if($key->transaction_type=='repayment' || $key->payment_apply_to == 'part_payment' || $key->payment_apply_to == 'reloan_payment')
                                                                        <!-- <li>
                                                                            <a href="{{url('loan/transaction/'.$key->id.'/print')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-print"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li> -->
                                                                        <li>
                                                                            <a href="{{url('loan/transaction/'.$key->id.'/pdf')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-file-pdf-o"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li>
                                                                 
                                                                    @endif
                                                                    
                                                                    
                                                                    @if($key->transaction_type=='refund' && $key->reversible==1)
                                                                        <li>
                                                                            <a href="{{url('loan/transaction/'.$key->id.'/print')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-print"></i> {{ trans_choice('general.print',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li>
                                                                        <li>
                                                                            <a href="{{url('loan/transaction/'.$key->id.'/pdf')}}"
                                                                               target="_blank"><i
                                                                                        class="fa fa-file-pdf-o"></i> {{ trans_choice('general.pdf',1) }} {{trans_choice('general.receipt',1)}}
                                                                            </a></li>
                                                                        @if(Sentinel::hasAccess('loans.transactions.update'))
                                                                            <li>
                                                                                <a href="{{url('loan/repayment/'.$key->id.'/edit')}}"><i
                                                                                            class="fa fa-edit"></i> {{ trans('general.edit') }}
                                                                                </a></li>

                                                                            <li>
                                                                                <a href="{{url('loan/repayment/'.$key->id.'/reverse')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.reverse') }}
                                                                                </a></li>
                                                                        @endif
                                                                    @endif
                                                                    
                                                                    
                                                                    @if($key->transaction_type=='penalty' && $key->reversible==1)
                                                                        @if(Sentinel::hasAccess('loans.charges.waive'))
                                                                            <li>
                                                                                <a href="{{url('loan/transaction/'.$key->id.'/waive')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.waive') }}
                                                                                </a></li>
                                                                        @endif
                                                                    @endif
                                                                    @if($key->transaction_type=='installment_fee' && $key->reversible==1)
                                                                        @if(Sentinel::hasAccess('loans.charges.waive'))
                                                                            <li>
                                                                                <a href="{{url('loan/transaction/'.$key->id.'/waive')}}"
                                                                                   class="delete"><i
                                                                                            class="fa fa-minus-circle"></i> {{ trans('general.waive') }}
                                                                                </a></li>
                                                                        @endif
                                                                    @endif
                                                                    @if($key->transaction_type=='specified_due_date_fee' && $key->reversible==1)
                                                                        @if(Sentinel::hasAccess('loans.charges.waive'))
                                                                            <li>
                                                                                <a href="{{url('loan/transaction/'.$key->id.'/waive')}}"
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
                        <div class="modal fade" id="waive_interest_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.waive',1)}} {{trans_choice('general.interest',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/interest/waive')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="waive_interest_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="waived_date"
                                                       class="control-label col-md-3"> {{trans_choice('general.date',1)}}</label>
                                                <div class="col-md-9">
                                                    <input type="text" name="date"
                                                           class="form-control date-picker"
                                                           value="{{date("Y-m-d")}}"
                                                           required id="waived_date">
                                                </div>
                                            </div>
                                            <?php
                                            $waive_amount = DB::table('loan_repayment_schedules')->select(DB::raw('(COALESCE(interest,0)-COALESCE(interest_waived,0)-COALESCE(interest_written_off,0)-COALESCE(interest_paid,0)) as interest_due'))->where('loan_id', $loan->id)->orderBy('due_date', 'asc')->havingRaw("interest_due>0")->first();
                                            if (empty($waive_amount)) {
                                                $waive_amount = 0;
                                            } else {
                                                $waive_amount = $waive_amount->interest_due;
                                            }
                                            ?>
                                            <div class="form-group">
                                                <label for="waived_amount"
                                                       class="control-label col-md-3">{{trans_choice('general.amount',1)}}</label>
                                                <div class="col-md-9">
                                                    <input type="number" name="amount" class="form-control"
                                                           value="{{$waive_amount}}"
                                                           required id="waived_amount">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="waived_notes"
                                                       class="control-label col-md-3">{{trans_choice('general.note',2)}}</label>
                                                <div class="col-md-9">
                                                     <textarea name="notes" class="form-control"
                                                               id="waived_notes"
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

                        <div class="modal fade" id="add_refund_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/refund/store')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="add_charge_form">
                                        {{csrf_field()}}
                                        <div class="modal-body">
                                            <div class="form-group">
                                                <label for="charge_id"
                                                       class="control-label col-md-3">Account</label>
                                                <div class="col-md-9">
                                                <select name="gl_account_fund_source_id" class="form-control select2" id="gl_account_fund_source_id">
                            <option></option>
                            @foreach(\App\Models\GlAccount::where('active',1)->get() as $key)
                             <option value="{{$key->id}}">{{$key->name}}</option>
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

                        <div class="modal fade" id="reschedule_loan_modal">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Roll Over {{ trans_choice('general.loan',1) }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close">
                                                                <span aria-hidden="true">×</span></button>
                                                        </div>
                                                        <form method="post"
                                                              action="{{ url('loan/'.$loan->id.'/reschedule_loan') }}">
                                                            {{csrf_field()}}
                                                            <div class="modal-body">
                                                           
                                                            <div class="form-group">
            
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                      From Which  {{ trans_choice('general.installment',1) }} ?
                                                                    </label>
          
                                                                    <input type="text" name="rescheduled_from_date"
                                                               class="form-control date-picker"
                                                               value="{{date("Y-m-d")}}"
                                                               required id="rescheduled_from_date">

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.submitted',1) }} On
                                                                    </label>
                                                                    <input type="text" name="submitte_on_date"
                                                               class="form-control date-picker"
                                                               value="{{date("Y-m-d")}}"
                                                               required id="rescheduled_on_date">

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.total',1) }} {{ trans_choice('general.outstanding',1) }}  
                                                                    </label>
                                                                    <input type="text" name="balance"
                                                               class="form-control "
                                                               value="{{$balance}}" readonly
                                                               required id="balance"  onkeyup="sum();">

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                         {{ trans_choice('general.interest',1) }}  {{ trans_choice('general.rate',1) }} %
                                                                    </label>
                                                                    <input type="text" name="interest_rate"
                                                               class="form-control "
                                                               value="{{$loan->interest_rate}}"
                                                               required id="interest_rate" onkeyup="sum();">

                                                                </div>

                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        Adjusted {{ trans_choice('general.interest',1) }} 
                                                                    </label>
                                                                    <input type="text" name="interest"
                                                               class="form-control "
                                                               value=""
                                                               required id="interest" readonly>

                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="rescheduled_on_date"
                                                                           class="control-label">
                                                                        {{ trans_choice('general.next',1) }} {{ trans_choice('general.repayment',1) }} 
                                                                    </label>
                                                                    <input type="text" name="next_repayment"
                                                               class="form-control date-picker"
                                                               value="{{date("Y-m-d")}}"
                                                               required id="rescheduled_on_date" >

                                                                </div>

                                                                
                                                               
                                                                <div class="form-group">
                                                                    <label for="rescheduled_notes"
                                                                           class="control-label">{{ trans_choice('general.note',2) }}</label>
                                                                    <textarea name="rescheduled_notes"
                                                                              v-model="rescheduled_notes"
                                                                              class="form-control"
                                                                              id="rescheduled_notes"
                                                                              rows="3" required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button"
                                                                        class="btn btn-default pull-left"
                                                                        data-dismiss="modal">
                                                                    {{ trans_choice('general.close',1) }}
                                                                </button>
                                                                <button type="submit"
                                                                        class="btn btn-primary">{{ trans_choice('general.save',1) }}</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>















                        <div class="modal fade" id="add_charge_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.charge',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/charge/store')}}"
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
                                                        @foreach(\App\Models\Charge::where('active', 1)->get() as $key)
                                <option value="{{$key->name}}">{{$key->name}}</option>
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
                        <div class="modal fade" id="write_off_loan_modal">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">{{trans_choice('general.write_off',1)}} {{trans_choice('general.loan',1)}}</h4>
                                    </div>
                                    <form method="post" action="{{url('loan/'.$loan->id.'/write_off')}}"
                                          class="form-horizontal "
                                          enctype="multipart/form-data" id="write_off_loan_form">
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
                    @endif
                    @if($loan->client_type=="group")
                        <div class="tab-pane" id="group_allocation">
                            <div class="row">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-hover table-striped" id="">
                                        <thead>
                                        <tr>
                                            <th>{{ trans_choice('general.name',1) }}</th>
                                            <th>{{ trans('general.id') }}</th>
                                            <th>{{ trans('general.amount') }}</th>
                                            <th>{{ trans_choice('general.action',1) }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($loan->group_allocation as $key)
                                            <tr>
                                                @if(!empty($key->client))
                                                    <td>
                                                        @if($key->client->client_type=="individual")
                                                            {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                                                        @else
                                                            {{$key->client->full_name}}
                                                        @endif
                                                    </td>
                                                    <td>{{$key->client->account_no}}</td>
                                                    <td>
                                                        {{number_format($key->amount,2)}}
                                                    </td>
                                                    <td>
                                                        <a class="" href="{{url('client/'.$key->client_id.'/show')}}"><i
                                                                    class="fa fa-eye"></i> </a>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="change_loan_officer_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.change',1)}} {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</h4>
                </div>
                <form method="post" action="{{url('loan/'.$loan->id.'/change_loan_officer')}}"
                      class="form-horizontal "
                      enctype="multipart/form-data" id="change_loan_officer_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="loan_officer_id"
                                   class="control-label col-md-3">
                                {{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}
                            </label>
                            <div class="col-md-9">
                                <select name="loan_officer_id" class="form-control select2"
                                        id="loan_officer_id" required>
                                    <option></option>
                                    @foreach(\App\Models\User::all() as $key)
                                        @if(!Sentinel::findUserById($key->id)->inRole('client'))
                                            <option value="{{$key->id}}"
                                                    @if($loan->loan_officer_id==$key->id) selected @endif>{{$key->first_name}} {{$key->last_name}}</option>
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
                <form method="post" action="{{url('loan/'.$loan->id.'/document/store')}}"
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
                <form method="post" action="{{url('loan/'.$loan->id.'/note/store')}}"
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
    <div class="modal fade" id="add_collateral_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.collateral',1)}}</h4>
                </div>
                <form method="post" action="{{url('loan/'.$loan->id.'/collateral/store')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="add_collateral_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="collateral_type_id"
                                   class="control-label col-md-3">{{trans_choice('general.type',1)}}</label>
                            <div class="col-md-9">
                                <select name="collateral_type_id" class="select2 form-control"
                                        id="collateral_type_id" required>
                                    <option></option>
                                    @foreach(\App\Models\CollateralType::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="value"
                                   class="control-label col-md-3">{{trans_choice('general.value',1)}}</label>
                            <div class="col-md-9">
                                <input type="number" name="value" class="form-control"
                                       value="{{old('value')}}"
                                       required id="value">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="description"
                                   class="control-label col-md-3">{{trans_choice('general.description',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="description" class="form-control"
                                       value="{{old('description')}}"
                                       required id="description">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="serial"
                                   class="control-label col-md-3">{{trans_choice('general.serial',1)}}</label>
                            <div class="col-md-9">
                                <input type="text" name="serial" class="form-control"
                                       value="{{old('serial')}}"
                                       required id="serial">
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
    <div class="modal fade" id="view_collateral">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_collateral">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>

    <div class="modal fade" id="add_guarantor_modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans_choice('general.add',1)}} {{trans_choice('general.guarantor',1)}}</h4>
                </div>
                <form method="post" action="{{url('loan/'.$loan->id.'/guarantor/store')}}"
                      class="form-horizontal"
                      enctype="multipart/form-data" id="add_guarantor_form">
                    {{csrf_field()}}
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="is_client"
                                   class="control-label col-md-3">{{trans_choice('general.existing',1)}} {{trans_choice('general.client',1)}}</label>
                            <div class="col-md-9">
                                <select name="is_client" class="select2 form-control"
                                        id="is_client" required>
                                    <option value="0">{{trans_choice('general.no',1)}}</option>
                                    <option value="1" selected>{{trans_choice('general.yes',1)}}</option>
                                </select>
                            </div>
                        </div>
                 
                        <div class="form-group">
                            <label for="client_relationship_id"
                                   class="control-label col-md-3">{{trans_choice('general.relationship',1)}}</label>
                            <div class="col-md-9">
                                <select name="client_relationship_id" class="select2 form-control"
                                        id="client_relationship_id" required>
                                    <option></option>
                                    @foreach(\App\Models\ClientRelationship::all() as $key)
                                        <option value="{{$key->id}}">{{$key->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div id="new_client_div">
                            <div class="form-group">
                                <label for="guarantor_first_name"
                                       class="control-label col-md-3">{{trans_choice('general.first_name',1)}}</label>
                                <div class="col-md-9">
                                    <input type="text" name="first_name" class="form-control"
                                           value="{{old('first_name')}}"
                                           required id="guarantor_first_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guarantor_middle_name"
                                       class="control-label col-md-3">{{trans_choice('general.middle_name',1)}}</label>
                                <div class="col-md-9">
                                    <input type="text" name="middle_name" class="form-control"
                                           value="{{old('middle_name')}}"
                                           id="guarantor_middle_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guarantor_last_name"
                                       class="control-label col-md-3">{{trans_choice('general.last_name',1)}}</label>
                                <div class="col-md-9">
                                    <input type="text" name="last_name" class="form-control"
                                           value="{{old('last_name')}}"
                                           required id="guarantor_last_name">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guarantor_mobile"
                                       class="control-label col-md-3">{{trans_choice('general.mobile',1)}}</label>
                                <div class="col-md-9">
                                    <input type="text" name="mobile" class="form-control"
                                           value="{{old('mobile')}}"
                                           id="guarantor_mobile">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guarantor_gender"
                                       class="control-label col-md-3">{{trans_choice('general.gender',1)}}</label>
                                <div class="col-md-9">
                                    <select name="gender" class="form-control" id="guarantor_gender">
                                        <option value="male">{{trans('general.male')}}</option>
                                        <option value="female">{{trans('general.female')}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="guarantor_address"
                                       class="control-label col-md-3">{{trans_choice('general.address',1)}}</label>
                                <div class="col-md-9">
                                    <input type="text" name="guarantor_address" class="form-control"
                                           value=""
                                           id="guarantor_address">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="guarantor_amount"
                                   class="control-label col-md-3">{{trans_choice('general.amount',1)}}</label>
                            <div class="col-md-9">
                                <input type="number" name="amount" class="form-control"
                                       value="{{old('amount')}}"
                                       required id="guarantor_amount">
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
    <div class="modal fade" id="view_guarantor">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit_guarantor">
        <div class="modal-dialog">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>


@endsection
@section('footer-scripts')
    <script>
        if ($("#is_client").val() == 1) {
            $("#clients_div").show();
            $("#new_client_div").hide();
            $("#guarantor_client_id").attr('required', 'required');
            $("#guarantor_first_name").removeAttr('required');
            $("#guarantor_last_name").removeAttr('required');
            $("#guarantor_mobile").removeAttr('required');
        } else if ($("#is_client").val() == 0) {
            $("#clients_div").hide();
            $("#new_client_div").show();
            $("#guarantor_client_id").removeAttr('required');
            $("#guarantor_first_name").attr('required', 'required');
            $("#guarantor_last_name").attr('required', 'required');
            $("#guarantor_mobile").attr('required', 'required');
        } else {
            $("#clients_div").hide();
            $("#clients_div").hide();
        }
        $("#is_client").change(function (e) {
            if ($("#is_client").val() == 1) {
                $("#clients_div").show();
                $("#new_client_div").hide();
                $("#guarantor_client_id").attr('required', 'required');
                $("#guarantor_first_name").removeAttr('required');
                $("#guarantor_last_name").removeAttr('required');
                $("#guarantor_mobile").removeAttr('required');
            } else if ($("#is_client").val() == 0) {
                $("#clients_div").hide();
                $("#new_client_div").show();
                $("#guarantor_client_id").removeAttr('required');
                $("#guarantor_first_name").attr('required', 'required');
                $("#guarantor_last_name").attr('required', 'required');
                $("#guarantor_mobile").attr('required', 'required');
            } else {
                $("#clients_div").hide();
                $("#clients_div").hide();
            }
        });
        $('#view_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/note') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_note').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/note') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        })
        $('#view_collateral').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/collateral') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_collateral').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/collateral') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#view_guarantor').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/guarantor') !!}/" + id + "/show",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $('#edit_guarantor').on('shown.bs.modal', function (e) {
            var id = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'GET',
                url: "{!!  url('loan/guarantor') !!}/" + id + "/edit",
                success: function (data) {
                    $(e.currentTarget).find(".modal-content").html(data);
                }
            });
        });
        $("#add_document_form").validate();
        $("#add_collateral_form").validate();
        $("#add_guarantor_form").validate();
        $("#add_note_form").validate();
        $("#approve_loan_form").validate();
        $("#decline_loan_form").validate();
        $("#add_charge_form").validate();
        $("#waive_interest_form").validate();
        $("#write_off_loan_form").validate();
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
<script>

function sum() {
    var inputFirstNumberValue = document.getElementById('balance').value;
    var inputSecondNumberValue = document.getElementById('interest_rate').value;
    var outputs = parseInt(inputFirstNumberValue) * parseInt(inputSecondNumberValue) / 100;
    if (!isNaN(outputs)) {
        document.getElementById('interest').value = outputs;
    }
}



</script>

@endsection
