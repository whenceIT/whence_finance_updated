@if(!Sentinel::inRole('client'))
        <div class="row">
            @if(Sentinel::hasAccess('dashboard.loans_disbursed'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"> {{ trans_choice('general.loan',2) }} {{ trans_choice('general.disbursed',1) }} UHDUF</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_disbursed_loans_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif


            @if(Sentinel::hasAccess('dashboard.my_disbursed'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"> {{ trans_choice('general.my',1) }}  {{ trans_choice('general.disbursed',1) }} {{ trans_choice('general.loan',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::officer_total_disbursed_loans_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(Sentinel::hasAccess('dashboard.my_branch_disbursed'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-green"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"> {{ trans_choice('general.branch',1) }}  {{ trans_choice('general.disbursed',1) }} {{ trans_choice('general.loan',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::branch_total_disbursed_loans_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif


            @if(Sentinel::hasAccess('dashboard.total_repayments'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.total',2) }} {{ trans_choice('general.repayment',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_repayments_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(Sentinel::hasAccess('dashboard.my_repayments_loans'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.my',2) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.repayment',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::officer_total_loans_repayments_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(Sentinel::hasAccess('dashboard.my_branch_repayments'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-dollar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.branch',1) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.repayment',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::branch_total_loans_repayments_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif
      
            @if(Sentinel::hasAccess('dashboard.total_outstanding'))
                <div class="clearfix visible-sm-block"></div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.total',2) }} {{ trans_choice('general.outstanding',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_outstanding_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif
          



            @if(Sentinel::hasAccess('dashboard.my_outstanding_loans'))
                <div class="clearfix visible-sm-block"></div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.my',1) }} {{ trans_choice('general.total',2) }} {{ trans_choice('general.outstanding',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::officer_total_loans_outstanding_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if(Sentinel::hasAccess('dashboard.my_branch_outstanding'))
                <div class="clearfix visible-sm-block"></div>

                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-yellow"><i class="fa fa-money"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.branch',1) }} {{ trans_choice('general.total',2) }} {{ trans_choice('general.outstanding',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::branch_total_loans_outstanding_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif




            @if(Sentinel::hasAccess('dashboard.amount_in_arrears'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-minus"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.in',2) }} {{ trans_choice('general.arrears',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::total_loans_overdue_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif
            @if(Sentinel::hasAccess('dashboard.my_loan_arrears'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-minus"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.my',2) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.arrears',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::officer_total_loans_overdue_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif
     

            @if(Sentinel::hasAccess('dashboard.my_branch_arrears'))
                <div class="col-md-3 col-sm-6 col-xs-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-red"><i class="fa fa-minus"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">{{ trans_choice('general.branch',1) }} {{ trans_choice('general.loan',2) }} {{ trans_choice('general.arrears',2) }}</span>
                            <span class="info-box-number">{{number_format(\App\Helpers\GeneralHelper::branch_total_loans_overdue_amount(),2)}}</span>
                        </div>
                    </div>
                </div>
            @endif
    
        </div>
        <div class="row">
            @if(Sentinel::hasAccess('dashboard.loans_status_overview'))
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans_choice('general.loan',2) }} {{ trans_choice('general.status',1) }} {{ trans_choice('general.overview',2) }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <div id="loans_status_graph" style="height: 300px;"></div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            @endif
            @if(Sentinel::hasAccess('dashboard.clients_overview'))
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans_choice('general.client',2) }} {{ trans_choice('general.overview',2) }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <div id="registered_clients_graph" style="height: 300px;"></div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            @endif
            @if(Sentinel::hasAccess('dashboard.savings_balances_overview'))
                <div class="col-md-4">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans_choice('general.savings',2) }} {{ trans_choice('general.balance',2) }} {{ trans_choice('general.overview',2) }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <div id="savings_balance_graph" style="height: 300px;"></div>

                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
            @endif
        </div>
        <div class="row">
            @if(Sentinel::hasAccess('dashboard.collection_statistics'))
                <div class="col-md-8">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">{{ trans_choice('general.collection',1) }} {{ trans_choice('general.statistic',2) }}</h3>

                            <div class="box-tools pull-right">
                                <button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                                            class="fa fa-minus"></i>
                                </button>
                            </div>
                            <!-- /.box-tools -->
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body" id="">
                            <div class="row text-center">
                                <?php
                                $target = 0;
                                foreach (\App\Models\LoanRepaymentSchedule::where('year', date("Y"))->where('month',
                                    date("m"))->get() as $key) {
                                    $target = $target + $key->principal - $key->principal_waived - $key->principal_written_off + $key->interest - $key->interest_waived - $key->interest_written_off + $key->fees - $key->fees_waived - $key->fees_written_off + $key->penalty - $key->penalty_waived - $key->penalty_written_off;
                                }
                                $paid_this_month = \App\Models\LoanTransaction::where('transaction_type',
                                    'repayment')->where('reversed', 0)->where('year', date("Y"))->where('month',
                                    date("m"))->sum('credit');
                                if ($target > 0) {
                                    $percent = round(($paid_this_month / $target) * 100);
                                } else {
                                    $percent = 0;
                                }
                                ?>
                                <div class="col-md-4">
                                    <div class="content-group">

                                        <h5 class="text-semibold no-margin">
                                            {{ number_format(\App\Models\LoanTransaction::where('transaction_type','repayment')->where('reversed', 0)->where('date',date("Y-m-d"))->sum('credit'),2) }}
                                        </h5>
                                        <span class="text-muted text-size-small">{{ trans_choice('general.today',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">

                                        <h5 class="text-semibold no-margin">
                                            {{ number_format(\App\Models\LoanTransaction::where('transaction_type',
                                'repayment')->where('reversed', 0)->whereBetween('date',array('date_sub(now(),INTERVAL 1 WEEK)','now()'))->sum('credit'),2) }}
                                        </h5>
                                        <span class="text-muted text-size-small">{{ trans_choice('general.last',1) }} {{ trans_choice('general.week',1) }}</span>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="content-group">

                                        <h5 class="text-semibold no-margin">{{ number_format($paid_this_month,2) }}</h5>
                                        <span class="text-muted text-size-small">{{ trans_choice('general.this',1) }} {{ trans_choice('general.month',1) }}</span>
                                    </div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <h5 class=" text-semibold">{{ trans_choice('general.monthly',1) }} {{ trans_choice('general.target',1) }}</h5>
                                    </div>
                                    <div class="progress" data-toggle="tooltip"
                                         title="{{ trans_choice('general.target',1) }} : {{number_format($target,2)}}">

                                        <div class="progress-bar progress-bar-success progress-bar-striped active"
                                             style="width: {{$percent}}%">
                                            <span>{{$percent}}% {{ trans_choice('general.complete',1) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3 class="text-center">HERE {{ trans_choice('general.collection',1) }} {{ trans_choice('general.overview',2) }}</h3>
                                    <div id="collection_statistics_graph" style="height: 300px;"></div>
                                </div>
                            </div>

                        </div>
                        <!-- /.box-body -->
                    </div>

                </div>
            @endif
            <div class="col-md-4">
                <div class="row">
                    <?php $fees_penalty = \App\Helpers\GeneralHelper::fees_penalty_earned_paid(); ?>
                    @if(Sentinel::hasAccess('dashboard.fees_earned'))
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-thumbs-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"> {{ trans_choice('general.fee',2) }} {{ trans_choice('general.earned',1) }}</span>
                                    <span class="info-box-number">{{number_format($fees_penalty["fees"],2)}}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Sentinel::hasAccess('dashboard.fees_paid'))
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"> {{ trans_choice('general.fee',2) }} {{ trans_choice('general.paid',1) }}</span>
                                    <span class="info-box-number">{{number_format($fees_penalty["fees_paid"],2)}}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Sentinel::hasAccess('dashboard.penalties_earned'))
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-yellow"><i class="fa fa-thumbs-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"> {{ trans_choice('general.penalty',2) }} {{ trans_choice('general.earned',1) }}</span>
                                    <span class="info-box-number">{{number_format($fees_penalty["penalty"],2)}}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(Sentinel::hasAccess('dashboard.penalties_paid'))
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text"> {{ trans_choice('general.penalty',2) }} {{ trans_choice('general.paid',1) }}</span>
                                    <span class="info-box-number">{{number_format($fees_penalty["penalty_paid"],2)}}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif