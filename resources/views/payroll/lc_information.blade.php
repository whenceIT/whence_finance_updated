@extends('layouts.master')
@section('title')
    LC Information
@endsection
@section('content')
    <?php
    $dates = [];
    $target_dates = [];
    $compare_dates = [];
    $todaysDate = date('Y-m-d');
    $use = date('Y-m-');
    $num = 24;
    $endDate = $use . $num;
    $endDate = date('Y-m-d', strtotime($endDate));
    if ($todaysDate > $endDate) {
        $endDate = date('Y-m-d', strtotime($endDate . ' + 1 months'));
    }
    $startDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
    for ($x = 0; $x < 24; $x++) {
        if ($x != 0) {
            $endDate = date('Y-m-d', strtotime($endDate . ' - 1 months'));
            $startDate = date('Y-m-d', strtotime($startDate . ' - 1 months'));
        }
        array_push($dates, $x);
        array_push($target_dates, $endDate);
        array_push($compare_dates, $startDate);
    }

    ?>
    <div class="box box-primary">
        <div class="box-body">
            <form method="post" action="{{Request::url()}}" class="form-horizontal" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="form-group">

                    <label for="office_id" class="control-label col-md-2">{{trans_choice('general.branch', 1)}}</label>


                    @if($user_role == 1)
                        <div class="col-md-3">
                            <select name="office_id" class="form-control select2" id="office_id" required>
                                <option></option>
                                @foreach(\App\Models\Office::all() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($user_role == 4)
                        <div class="col-md-3">
                            <select name="office_id" class="form-control select2" id="office_id" required>
                                <option></option>
                                @foreach(\App\Models\Office::where('id', $user_branch)->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @elseif($user_role == 6)
                        <div class="col-md-3">
                            <select name="office_id" class="form-control select2" id="office_id" required>
                                <option></option>
                                @foreach(\App\Models\Office::where('province_id', $user_province)->get() as $key)
                                    <option value="{{$key->id}}">{{$key->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <label for="office_id" class="control-label col-md-2">Cycle Dates</label>
                    <div class="col-md-3">
                        <select name="cycle" class="form-control select2" id="cycle" required>
                            <option></option>
                            @foreach($dates as $date)
                                <option value="{{$target_dates[$date]}}">{{date("jS M Y", strtotime($compare_dates[$date]))}} -
                                    {{date("jS M Y", strtotime($target_dates[$date]))}}</option>
                            @endforeach
                        </select>
                    </div>


                    <button type="submit" class="btn btn-success">Go!
                    </button>
                </div>
            </form>

            <?php

    $new_loans_cycle = 0;
    $new_reloans_cycle = 0;
    $target_total_cycle = 0;
    $cycle_full_payments = 0;
    $cycle_part_payments = 0;
    $cycle_interest = 0;
    $cycle_reloan_amount = 0;
    $cycle_reloan_payments = 0;
    $cycle_opening_uncollected_amount = 0;
    $MoneyCollected = 0;
    $MoneyGivenOut = 0;
    $charges = 0;
    $balance = 0;
    $target_amount = 0;
    $new_loans = 0;
    $reloans = 0;
    $target_count = 0;
    $month_count = 0;
                ?>
            @if(!empty($branch))
                <div>
                    <p style="font-weight: bold;">{{\App\Models\Office::where('id', $branch)->first()->name}} |
                        {{date("jS M", strtotime($compareDate))}} - {{date("jS M", strtotime($targetDate))}}</p>
                </div>

            @endif

            <div class="table-responsive">
                <table id="view-repayments"
                    class="table table-bordered table-condensed table-striped table-hover no-footer">
                    <thead>
                        <tr style="" role="row">
                            <th>
                                Name
                            </th>
                            <th>
                                Cycle Opening Uncollected Amount

                            </th>
                            <th>
                                Total Cash Collected
                            </th>
                            <th>
                                Total Cash Still Uncollected
                            </th>
                            <th>
                                Total loans Given out this cycle
                            </th>

                            <th>
                                Projected Salary
                            </th>
                            <th>{{ trans_choice('general.action', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loanConsultants as $user)
                        <?php
                            $new_loans_cycle = 0;
                            $new_reloans_cycle = 0;
                            $target_total_cycle = 0;
                            $cycle_reloan_payments = 0;
                            $cycle_full_payments = 0;
                            $cycle_part_payments = 0;
                            $cycle_collected_total = 0;
                            $MoneyCollected = 0;
                            $MoneyGivenOut = 0;
                            $charges = 0;
                            $balance = 0;
                            $target_count = 0;
                            $projected_salary = 4000;


                            foreach ($userLoans as $object) {

                                if ($user->id == $object->loan_officer_id) {
                                    foreach ($object->transactions as $transaction) {
                                        if ($transaction->transaction_type == 'disbursement' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
                                            $new_loans_cycle = $new_loans_cycle + $transaction->debit;
                                        }

                                        if ($transaction->transaction_type == 'interest' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
                                            $principal = $transaction->debit / 0.4;
                                            $new_reloans_cycle = $new_reloans_cycle + $principal;
                                        }
                                    }



                                    foreach ($object->transactions as $transaction) {
                                        if ($transaction->transaction_type == 'repayment' && $transaction->payment_apply_to == 'full_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
                                            $cycle_full_payments = $cycle_full_payments + $transaction->credit;
                                        }

                                        if ($transaction->payment_apply_to == 'part_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {
                                            $cycle_part_payments = $cycle_part_payments + $transaction->credit;
                                        }

                                        if ($transaction->payment_apply_to == 'reloan_payment' && $transaction->date > $compareDate && $transaction->date <= $targetDate) {

                                            $cycle_reloan_amount = $transaction->balance_bf; //+ ($transaction->credit/0.4);
                                            $cycle_interest = $transaction->credit / 0.4;
                                            $cycle_reloan_payments = $cycle_reloan_payments + $cycle_reloan_amount;
                                        }
                                    }


                                    foreach ($object->transactions as $transaction) {
                                        if ($transaction->date <= $compareDate && $transaction->transaction_type != 'specified_due_date_fee') {
                                            $MoneyGivenOut = $MoneyGivenOut + $transaction->debit;
                                        }

                                        if ($transaction->date <= $compareDate) {
                                            $MoneyCollected = $MoneyCollected + $transaction->credit;
                                        }


                                    }


                                }

                            }

                            for ($x = 0; $x < 24; $x++) {
                                $target = date('Y-m-d', strtotime($targetDate . ' - ' . $x . 'months'));
                                $compare = date('Y-m-d', strtotime($compareDate . ' - ' . $x . 'months'));
                                foreach ($userLoans as $object) {
                                    if ($user->id == $object->loan_officer_id) {
                                        foreach ($object->transactions as $transaction) {
                                            if ($transaction->transaction_type == 'disbursement' && $transaction->date > $compare && $transaction->date <= $target) {
                                                $new_loans = $new_loans + $transaction->debit;
                                            }

                                            if ($transaction->transaction_type == 'interest' && $transaction->date > $compare && $transaction->date <= $target) {
                                                $principal = $transaction->debit / 0.4;
                                                $reloans = $reloans + $principal;
                                            }
                                        }

                                    }
                                }

                                $target_amount = $new_loans + $reloans;
                                if ($target_amount > 40000) {
                                    $target_count = $target_count + 1;
                                }
                                // $target_count = $target_count + 1;
                                $new_loans = 0;
                                $reloans = 0;
                                $target_amount = 0;
                            }


                            $balance = $MoneyGivenOut - $MoneyCollected;
                            $target_total_cycle = $new_loans_cycle + $new_reloans_cycle;
                            $cycle_collected_total = $cycle_full_payments + $cycle_part_payments + $cycle_reloan_payments;
                            $uncollected = $balance - $cycle_collected_total;
                            if ($target_count >= 3) {
                                $projected_salary = 5000;
                                if ($target_total_cycle >= 40000 && $uncollected < 5000) {
                                    $projected_salary = 7000;
                                }
                            }

                                        ?>
                                            <tr>

                                                <td>{{$user->first_name}} {{$user->last_name}}</td>
                                                <td>{{number_format($balance)}}</td>
                                                <td>{{number_format($cycle_collected_total)}}</td>
                                                <td>{{number_format($balance - $cycle_collected_total)}}</td>
                                                <td>{{number_format($target_total_cycle)}}</td>
                                                <td>{{number_format($projected_salary)}}</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <button class="btn btn-info btn-sm dropdown-toggle" type="button" data-toggle="dropdown"
                                                            aria-expanded="false"><i class="fa fa-navicon"></i></button>
                                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                            <li>
                                                                <a href="{{url('user/' . $user->id . '/staff_info')}}"><i class="fa fa-search"></i>
                                                                    {{ trans_choice('general.detail', 2) }}</a>
                                                            </li>
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
@endsection