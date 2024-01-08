<style>

    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 9px;
    }

    th, td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    .style-1 {
        color: white;
        padding-left: 10pt;
        font-size: 14pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
        background-color: #339933;
    }
    .style-2 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
    .style-3 {
        color: black;
        padding-right: 1pt;
        font-size: 8pt;
        font-family: "Arial Narrow";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        white-space: pre-wrap;
    }
</style>
<div>

    <table class="">

            <tbody>
            <tr style="height: 25pt">
                <td colspan="19" valign="middle"
                    class="style-1"> {{trans_choice('general.arrears',1)}}  {{trans_choice('general.report',1)}}</td>
            </tr>
            <tr style="height: 15pt">
                <td valign="middle" class="style-2">{{trans_choice('general.date',1)}} :</td>
                <td valign="middle" class="style-3">{{$end_date}}</td>
                <td colspan="2" valign="middle"
                    class="style-4">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}}
                    :
                </td>
                <td colspan="3" valign="middle" class="style-5"> {{date("Y-m-d H:i:s")}}</td>
                <td colspan="12"></td>
            </tr>

            <tr class="">
                <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
                <th>{{trans_choice('general.office',1)}}</th>
                <th>{{trans_choice('general.client',1)}}</th>
                <th>{{trans_choice('general.phone',1)}}</th>
                <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.id',1)}}</th>
                <th>{{trans_choice('general.product',1)}}</th>
                <th>{{trans_choice('general.amount',1)}}</th>
                <th>{{trans_choice('general.disbursed',1)}}</th>
                <th>{{trans_choice('general.maturity',1)}} {{trans_choice('general.date',1)}}</th>
                <th>{{trans_choice('general.remaining',1)}}</th>
                <th>{{trans_choice('general.principal',1)}}</th>
                <th>{{trans_choice('general.interest',1)}}</th>
                <th>{{trans_choice('general.fee',2)}}</th>
                <th>{{trans_choice('general.penalty',1)}}</th>
                <th>{{trans_choice('general.outstanding',1)}}</th>
                <th>{{trans_choice('general.due',1)}}</th>
                <th>%</th>
                <th>{{trans_choice('general.day',2)}} {{trans_choice('general.in',2)}} {{trans_choice('general.arrears',2)}}</th>
                <th>{{trans_choice('general.day',2)}} {{trans_choice('general.since',1)}} {{trans_choice('general.payment',1)}}</th>
            </tr>

            <?php
            $total_outstanding = 0;
            $total_due = 0;
            $total_principal = 0;
            $total_interest = 0;
            $total_fees = 0;
            $total_penalty = 0;
            $total_amount = 0;
            $loans_in_arrears = 0;
            ?>
            @foreach($data as $key)
                <?php
                $due = 0;
                $amount = 0;
                $outstanding = 0;
                $principal = 0;
                $interest = 0;
                $fees = 0;
                $penalty = 0;
                $amount_in_arrears = 0;
                $days_in_arrears = 0;
                if (strtotime($key->expected_maturity_date) < strtotime($end_date)) {
                    $remaining_days = 0;
                } else {
                    $date1 = new DateTime($key->expected_maturity_date);
                    $date2 = new DateTime($end_date);
                    $remaining_days = $date2->diff($date1)->format("%a");
                }

                $days_since_last_payment = 0;
                $balance = 0;
                $percentage = 0;
                $late_count = 0;
                foreach ($key->repayment_schedules as $schedule) {
                    if (strtotime($schedule->due_date) < strtotime($end_date)) {
                        $amount_in_arrears = $amount_in_arrears + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                    }
                    $principal = $principal + $schedule->principal - $schedule->principal_waived - $schedule->principal_written_off;
                    $interest = $interest + $schedule->interest - $schedule->interest_waived - $schedule->interest_written_off;
                    $fees = $fees + $schedule->fees - $schedule->fees_waived - $schedule->fees_written_off;
                    $penalty = $penalty + $schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off;
                    $balance = $balance + (($schedule->principal - $schedule->principal_waived - $schedule->principal_written_off - $schedule->principal_paid) + ($schedule->interest - $schedule->interest_waived - $schedule->interest_written_off - $schedule->interest_paid) + ($schedule->fees - $schedule->fees_waived - $schedule->fees_written_off - $schedule->fees_paid) + ($schedule->penalty - $schedule->penalty_waived - $schedule->penalty_written_off - $schedule->penalty_paid));
                    if ($amount_in_arrears > 0) {
                        $late_count++;
                        if ($late_count == 1) {
                            $overdue_date = $schedule->due_date;
                        }
                    }
                }

                if ($amount_in_arrears > 0) {
                    $total_outstanding = $total_outstanding + $balance;
                    $total_due = $total_due + $amount_in_arrears;
                    $total_principal = $total_principal + $principal;
                    $total_interest = $total_interest + $interest;
                    $total_fees = $total_fees + $fees;
                    $total_penalty = $total_penalty + $penalty;
                    $total_amount = $total_amount + $key->principal;
                    $date1 = new DateTime($overdue_date);
                    $date2 = new DateTime($end_date);
                    $days_in_arrears = $date2->diff($date1)->format("%a");
                    $transaction = \App\Models\LoanTransaction::where('loan_id',
                        $key->id)->where('transaction_type',
                        'repayment')->where('reversed', 0)->orderBy('date', 'desc')->first();
                    if (!empty($transaction)) {
                        $date2 = new DateTime($transaction->date);
                        $date1 = new DateTime($end_date);
                        $days_since_last_payment = $date2->diff($date1)->format("%r%a");
                    } else {
                        $days_since_last_payment = 0;
                    }
                }

                //select appropriate schedules


                ?>
                @if($amount_in_arrears >0)
                    <tr>
                        <td>
                            @if(!empty($key->loan_officer))
                                {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                            @endif
                        </td>
                        <td>
                            @if(!empty($key->office))
                                {{$key->office->name}}
                            @endif
                        </td>
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
                            @if(!empty($key->client))
                                {{$key->client->mobile}}
                            @endif
                            @if(!empty($key->group))
                                {{$key->group->mobile}}
                            @endif
                        </td>
                        <td>{{$key->id}}</td>
                        <td>
                            @if(!empty($key->loan_product))
                                {{$key->loan_product->name}}
                            @endif
                        </td>
                        <td>{{number_format($key->principal,2)}}</td>
                        <td>{{$key->disbursement_date}}</td>
                        <td>{{$key->expected_maturity_date}}</td>
                        <td>{{$remaining_days}}</td>
                        <td>{{number_format($principal,$key->decimals)}}</td>
                        <td>{{number_format($interest,$key->decimals)}}</td>
                        <td>{{number_format($fees,$key->decimals)}}</td>
                        <td>{{number_format($penalty,$key->decimals)}}</td>
                        <td>{{number_format($balance,$key->decimals)}}</td>
                        <td>{{number_format($amount_in_arrears,$key->decimals)}}</td>
                        <td>{{round($amount_in_arrears*100/$balance,2)}}</td>
                        <td>{{$days_in_arrears}}</td>
                        <td>{{$days_since_last_payment}}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <th colspan="6"></th>
                <th>{{number_format($total_amount,2)}}</th>
                <th colspan="3"></th>
                <th>{{number_format($total_principal,2)}}</th>
                <th>{{number_format($total_interest,2)}}</th>
                <th>{{number_format($total_fees,2)}}</th>
                <th>{{number_format($total_penalty,2)}}</th>
                <th>{{number_format($total_outstanding,2)}}</th>
                <th>{{number_format($total_due,2)}}</th>
                <th colspan="3"></th>
            </tr>
            </tfoot>

        </table>
</div>