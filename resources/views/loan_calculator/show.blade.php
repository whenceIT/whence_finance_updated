@extends('layouts.master')
@section('title')
    {{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}
@endsection
@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">{{ trans_choice('general.loan',1) }} {{ trans_choice('general.calculator',1) }}</h3>

            <div class="box-tools pull-right">
                <button onclick="window.history.back()" class="btn btn-info btn-sm  hidden-print">
                    {{ trans_choice('general.cancel',1) }}
                </button>
            </div>
        </div>

        <div class="box-body ">
            <table id="" class="table table-bordered table-condensed table-hover">
                <thead>
                <tr style="">
                    <th>{{trans_choice('general.disbursement',1)}}</th>
                    <th>{{trans_choice('general.maturity',1)}}</th>
                    <th>{{trans_choice('general.repayment',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}}%</th>
                    <th>{{trans_choice('general.interest',1)}}</th>
                    <th>{{trans_choice('general.due',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$request->expected_disbursement_date}}</td>
                    <td>{{date_format(date_add(date_create($request->first_repayment_date),
                date_interval_create_from_date_string($request->loan_term . ' ' . $request->loan_term_type)),
                'Y-m-d')}}</td>
                    <td id="repayment">
                        {{trans_choice('general.every',1)}} {{$request->repayment_frequency}}
                        @if($request->repayment_frequency_type=="days")
                            {{trans_choice('general.day',2)}}
                        @endif
                        @if($request->repayment_frequency_type=="weeks")
                            {{trans_choice('general.week',2)}}
                        @endif
                        @if($request->repayment_frequency_type=="months")
                            {{trans_choice('general.month',2)}}
                        @endif
                        @if($request->repayment_frequency_type=="years")
                            {{trans_choice('general.year',2)}}
                        @endif
                    </td>
                    <td>{{number_format($request->principal,2)}}</td>
                    <td>
                        {{$request->interest_rate}}  {{trans_choice('general.per',1)}}
                        @if($request->interest_rate_type=="day")
                            {{trans_choice('general.day',1)}}
                        @endif
                        @if($request->interest_rate_type=="week")
                            {{trans_choice('general.week',1)}}
                        @endif
                        @if($request->interest_rate_type=="month")
                            {{trans_choice('general.month',1)}}
                        @endif
                        @if($request->interest_rate_type=="year")
                            {{trans_choice('general.year',1)}}
                        @endif
                    </td>
                    <td id="interest"></td>
                    <td id="due"></td>
                </tr>
                </tbody>
            </table>
            <table class="table table-bordered table-condensed mt-20  table-hover">
                <thead>
                <tr class="">
                    <th>#</th>
                    <th>{{trans_choice('general.due',1)}} {{trans_choice('general.date',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.interest',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.due',1)}} {{trans_choice('general.amount',1)}}</th>
                    <th>{{trans_choice('general.principal',1)}} {{trans_choice('general.balance',1)}}</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $interest = '';
                if ($request->repayment_frequency_type == 'days') {
                    //return the interest per year
                    if ($loan_product->interest_rate_type == 'month') {
                        $interest = $request->interest_rate / 30;
                    }
                    if ($loan_product->interest_rate_type == 'year') {
                        $interest = $request->interest_rate / 365;
                    }

                }
                if ($request->repayment_frequency_type == 'weeks') {
                    //return the interest per semi annually
                    if ($loan_product->interest_rate_type == 'month') {
                        $interest = $request->interest_rate / 4;
                    }
                    if ($loan_product->interest_rate_type == 'year') {
                        $interest = $request->interest_rate / 52;
                    }
                }
                if ($request->repayment_frequency_type == 'months') {
                    //return the interest per quaterly

                    if ($loan_product->interest_rate_type == 'month') {
                        $interest = $request->interest_rate;
                    }
                    if ($loan_product->interest_rate_type == 'year') {
                        $interest = $request->interest_rate / 12;
                    }
                }
                if ($request->repayment_frequency_type == 'years') {
                    //return the interest per bi-monthly
                    if ($loan_product->interest_rate_type == 'month') {
                        $interest = $request->interest_rate * 12;
                    }
                    if ($loan_product->interest_rate_type == 'year') {
                        $interest = $request->interest_rate;
                    }

                }
                $interest_rate = $interest * $request->repayment_frequency / 100;
                $period = $request->loan_term / $request->repayment_frequency;
                $next_payment = $request->expected_first_repayment_date;
                $balance = $request->principal;
                $decimals = $loan_product->decimals;
                $rounded_interest = 0;
                $grace_on_principal = $loan_product->grace_on_principal;
                $grace_on_interest_charged = $loan_product->grace_on_interest_charged;
                $grace_on_interest_payment = $loan_product->grace_on_interest_payment;
                $total_interest = 0;
                $total_principal = 0;
                $total_due = 0;
                for ($i = 0; $i < $period; $i++) {
                //determine which method to use
                if ($loan_product->interest_method == "declining_balance") {
                    if ($loan_product->armotization_method == "equal_installment") {
                        if ($loan_product->grace_on_principal > 0) {
                            $due = round(\App\Helpers\GeneralHelper::amortized_payment($request->id, $request->principal, $period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $due = round(\App\Helpers\GeneralHelper::amortized_payment($request->id, $request->principal, $period), $decimals);
                        }
                        $interest = ($interest_rate * $balance);


                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {
                                $principal_due = $due - round($interest, $decimals);
                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                                $principal_due = $due;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $principal = $principal_due;
                        $interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                    if ($loan_product->armotization_method == "equal_principal") {
                        $interest = ($interest_rate * $balance);
                        if ($loan_product->grace_on_principal > 0) {
                            $principal_due = round($request->principal / ($period - $loan_product->grace_on_principal), $decimals);
                        } else {
                            $principal_due = round($request->principal / $period, $decimals);
                        }
                        //determine next balance
                        if ($i == $period - 1) {
                            //last record, balance rounded figures
                            $principal_due = $balance;
                            $interest = round($interest + $rounded_interest);
                        } else {
                            if ($grace_on_principal > 0) {
                                $grace_on_principal--;
                                $principal_due = 0;
                            } else {

                            }
                            if ($grace_on_interest_payment > 0) {
                                $interest = 0;
                                $grace_on_interest_payment--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                            if ($grace_on_interest_charged > 0) {
                                $interest = 0;
                                $grace_on_interest_charged--;
                            } else {
                                $interest = round($interest, $decimals);
                            }
                        }
                        $principal = $principal_due;
                        $interest = $interest;
                        $balance = ($balance - $principal_due);
                        $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    }
                }
                if ($loan_product->interest_method == "flat") {
                    $interest = ($interest_rate * $request->principal);

                    if ($loan_product->grace_on_principal > 0) {
                        $principal_due = round($request->principal / ($period - $loan_product->grace_on_principal), $decimals);
                    } else {
                        $principal_due = round($request->principal / $period, $decimals);
                    }
                    //determine next balance

                    if ($i == $period - 1) {
                        //last record, balance rounded figures
                        $principal_due = $balance;
                        $interest = round($interest + $rounded_interest);
                    } else {
                        if ($grace_on_principal > 0) {
                            $grace_on_principal--;
                            $principal_due = 0;
                        } else {

                        }
                        if ($grace_on_interest_payment > 0) {
                            $interest = 0;
                            $grace_on_interest_payment--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        if ($grace_on_interest_charged > 0) {
                            $interest = 0;
                            $grace_on_interest_charged--;
                        } else {
                            $interest = round($interest, $decimals);
                        }
                        $principal = $principal_due;
                        $interest = round($interest, $decimals);
                    }
                    $principal = $principal_due;
                    $interest = $interest;
                    $rounded_interest = $rounded_interest + ($interest - round($interest, $decimals));
                    $balance = ($balance - $principal_due);
                }

                ?>
                <tr>
                    <td>
                        {{$i+1}}
                    </td>
                    <td>
                        {{$next_payment}}
                    </td>
                    <td>
                        {{number_format($principal_due,$loan_product->decimals)}}
                    </td>
                    <td>
                        {{number_format($interest,$loan_product->decimals)}}
                    </td>
                    <td>
                        {{number_format($interest+$principal_due,$loan_product->decimals)}}
                    </td>
                    <td>
                        {{number_format($balance,$loan_product->decimals)}}
                    </td>
                </tr>
                <?php
                $next_payment = date_format(date_add(date_create($next_payment),
                    date_interval_create_from_date_string($request->repayment_frequency . ' ' . $request->repayment_frequency_type)),
                    'Y-m-d');
                $total_interest = $total_interest + $interest;
                $total_principal = $total_principal + $principal;
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="2"></th>
                    <th>
                        {{number_format($total_principal,$loan_product->decimals)}}
                    </th>
                    <th>
                        {{number_format($total_interest,$loan_product->decimals)}}
                    </th>
                    <th>
                        {{number_format($total_interest+$total_principal,$loan_product->decimals)}}
                    </th>
                    <th>
                        {{number_format($balance,$loan_product->decimals)}}
                    </th>
                </tr>
                </tfoot>
            </table>
            <div class="form-group p-20">
                <label for=""
                       class="control-label col-md-3"></label>
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary hidden-print" id="next">{{trans_choice('general.print',1)}}</button>
                </div>
            </div>
        </div>

    </div>
@endsection
@section('footer-scripts')
    <script>
        $("#next").click(function (e) {

            window.print();
        })
    </script>
@endsection