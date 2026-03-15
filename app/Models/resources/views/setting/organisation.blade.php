@extends('layouts.master')
@section('title')
    {{ trans_choice('general.organisation',2) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.organisation',2) }}</h3>

            <div class="box-tools pull-right">

            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="list-group">
                        @if(Sentinel::hasAccess('offices.view'))
                            <a class="list-group-item" href="{{url('office/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i
                                            class="fa fa-building fa fa-large"></i>&nbsp;&nbsp;{{ trans_choice('general.manage',1) }}
                                    {{ trans_choice('general.office',2) }}</h5>
                                <p class="list-group-item-text ng-binding">Add new office or modify or deactivate office
                                    or
                                    modify office hierarchy</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.loan_provisioning_criteria.update'))
                            <a class="list-group-item" href="{{url('loan_provisioning/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i
                                            class="fa fa-building fa fa-large"></i>&nbsp;&nbsp;{{ trans_choice('general.loan',1) }}
                                    Provisioning Criteria</h5>
                                <p class="list-group-item-text ng-binding">Define Loan Provisioning Criteria for
                                    Organization</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.currencies.view'))
                            <a class="list-group-item" href="{{url('currency/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-cogs fa fa-large"></i>&nbsp;&nbsp;{{ trans_choice('general.currency',1) }}
                                    Configuration</h5>
                                <p class="list-group-item-text ng-binding">Currencies available across organization for
                                    different products</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.funds.view'))
                            <a class="list-group-item" href="{{url('fund/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-money fa fa-large"></i>&nbsp;&nbsp;
                                    Manage {{ trans_choice('general.fund',2) }}</h5>
                                <p class="list-group-item-text ng-binding">Funds are associated with loans</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.loan_purposes.view'))
                            <a class="list-group-item" href="{{url('loan_purpose/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-money fa fa-large"></i>&nbsp;&nbsp;
                                    Manage {{ trans_choice('general.loan',1) }} {{ trans_choice('general.purpose',1) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Loan Purpose are associated with loans</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.payment_types.view'))
                            <a class="list-group-item" href="{{url('payment_type/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-dollar fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.payment',1) }} {{ trans_choice('general.type',1) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage payment types</p>
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="list-group">
                        @if(Sentinel::hasAccess('products.client_relationships.view'))
                            <a class="list-group-item" href="{{url('client_relationship/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-user fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.client',1) }} {{ trans_choice('general.relationship',1) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage client relationships</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.client_relationships.view'))
                            <a class="list-group-item" href="{{url('blacklist_reason/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-user fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.blacklist_reason',2) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage Blacklist Reasons</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.client_identification_types.view'))
                            <a class="list-group-item" href="{{url('client_identification_type/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-user fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.client',1) }} {{ trans_choice('general.identification',1) }} {{ trans_choice('general.type',1) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage client identification types</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.charges.view'))
                            <a class="list-group-item" href="{{url('charge/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-money fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.charge',1) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage charges</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('products.collateral_types.view'))
                            <a class="list-group-item" href="{{url('collateral_type/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i class="fa fa-cogs fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.collateral',1) }} {{ trans_choice('general.type',2) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Manage Collateral types</p>
                            </a>
                        @endif
                        @if(Sentinel::hasAccess('settings'))
                            <a class="list-group-item" href="{{url('sms_gateway/data')}}">
                                <h5 class="list-group-item-heading ng-binding"><i
                                            class="fa fa-envelope fa fa-large"></i>&nbsp;&nbsp; {{ trans_choice('general.sms',1) }} {{ trans_choice('general.gateway',2) }}
                                </h5>
                                <p class="list-group-item-text ng-binding">Setup SMS Gateways</p>
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-scripts')
    <script>


    </script>
@endsection
