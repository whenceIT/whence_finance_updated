<style>
    table { width:100%; border-collapse:collapse; font-family:Arial, Helvetica, sans-serif; font-size:9px; }
    th, td { padding:6px; border-bottom:1px solid #ddd; }
    .style-1 { color:#fff; padding-left:10pt; font-size:14pt; font-weight:bold; background:#339933; }
    .style-2 { font-size:8pt; font-weight:bold; }
    .style-3 { font-size:8pt; text-align:right; }
</style>

<table>
    <tbody>
    <tr style="height:25pt">
        <td colspan="14" class="style-1">
            {{trans_choice('general.age',1)}} {{trans_choice('general.analysis',1)}} {{trans_choice('general.report',1)}}
            by {{trans_choice('general.principal',1)}} {{trans_choice('general.balance',2)}}
        </td>
    </tr>
    <tr style="height:15pt">
        <td class="style-2">{{trans_choice('general.date',1)}} :</td>
        <td class="style-3">{{$end_date}}</td>
        <td colspan="2" class="style-2">{{trans_choice('general.report',1)}} {{trans_choice('general.run',1)}} {{trans_choice('general.date',1)}} :</td>
        <td colspan="3" class="style-3">{{date("Y-m-d H:i:s")}}</td>
        <td colspan="7"></td>
    </tr>

    {{-- HEADERS: SAME AS SCREEN --}}
    <tr>
        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.officer',1)}}</th>
        <th>{{trans_choice('general.office',1)}}</th>
        <th>{{trans_choice('general.client',1)}}</th>
        <th>{{trans_choice('general.phone',1)}}</th>
        <th>Employee ID</th>
        <th>{{trans_choice('general.product',1)}}</th>
        <th>{{trans_choice('general.bank',1)}}ACC</th>
        <th>Current</th>
        <th>{{trans_choice('general.below',1)}} 30 {{trans_choice('general.day',2)}}</th>
        <th>30 - 89 {{trans_choice('general.day',2)}}</th>
        <th>90 - 119 {{trans_choice('general.day',2)}}</th>
        <th>120 - 179 {{trans_choice('general.day',2)}}</th>
        <th>{{trans_choice('general.over',1)}} 179 {{trans_choice('general.day',2)}}</th>
        <th>{{trans_choice('general.total',1)}}</th>
    </tr>

    <?php
    $total_on_time_amount = 0;
    $total_p_30_amount = 0;
    $total_p_60_amount = 0;
    $total_p_90_amount = 0;
    $total_p_180_amount = 0;
    $total_p_180_plus_amount = 0;
    $total_p_amount = 0;
    ?>

  @foreach($data as $key)
    <?php
    $principal = 0;
    $amount_in_arrears = 0;
    $days_in_arrears = 0;

    $on_time_amount = 0;
    $p_30_amount = 0;
    $p_60_amount = 0;
    $p_90_amount = 0;
    $p_180_amount = 0;
    $p_180_plus_amount = 0;

    $late_count = 0;
    $overdue_date = null;

    // EXACTLY LIKE SCREEN VIEW
    foreach ($key->repayment_schedules as $schedule) {

        if (strtotime($schedule->due_date) < strtotime($end_date)) {
            $amount_in_arrears += (
                $schedule->principal
                - $schedule->principal_waived
                - $schedule->principal_written_off
                - $schedule->principal_paid
            );
        }

        $principal += (
            $schedule->principal
            - $schedule->principal_waived
            - $schedule->principal_written_off
            - $schedule->principal_paid
        );

        if ($amount_in_arrears > 0) {
            $late_count++;
            if ($late_count == 1) {
                $overdue_date = $schedule->due_date;
            }
        }
    }

    if ($amount_in_arrears > 0 && $overdue_date) {
        $date1 = new DateTime($overdue_date);
        $date2 = new DateTime($end_date);
        $days_in_arrears = (int) $date2->diff($date1)->format("%a");

        // SAME BUCKETS AS SCREEN (using $principal, not $amount_in_arrears)
        if ($days_in_arrears < 30) {
            $p_30_amount += $principal;
        }
        if ($days_in_arrears > 30 && $days_in_arrears <= 60) {
            $p_60_amount += $principal;
        }
        if ($days_in_arrears > 60 && $days_in_arrears <= 90) {
            $p_90_amount += $principal;
        }
        if ($days_in_arrears > 90 && $days_in_arrears <= 180) {
            $p_180_amount += $principal;
        }
        if ($days_in_arrears > 180) {
            $p_180_plus_amount += $principal;
        }

        $on_time_amount = 0;
    } else {
        // ON TIME (same as screen)
        $on_time_amount = $principal;
    }

    $p_amount = $on_time_amount + $p_30_amount + $p_60_amount + $p_90_amount + $p_180_amount + $p_180_plus_amount;

    $total_on_time_amount += $on_time_amount;
    $total_p_30_amount += $p_30_amount;
    $total_p_60_amount += $p_60_amount;
    $total_p_90_amount += $p_90_amount;
    $total_p_180_amount += $p_180_amount;
    $total_p_180_plus_amount += $p_180_plus_amount;
    $total_p_amount += $p_amount;
    ?>

    @if($days_in_arrears >= 0)
        <tr>
            <td>
                @if(!empty($key->loan_officer))
                    {{$key->loan_officer->first_name}} {{$key->loan_officer->last_name}}
                @endif
            </td>
            <td>@if(!empty($key->office)) {{$key->office->name}} @endif</td>
            <td>
                @if($key->client_type=="client" && !empty($key->client))
                    @if($key->client->client_type=="individual")
                        {{$key->client->first_name}} {{$key->client->middle_name}} {{$key->client->last_name}}
                    @else
                        {{$key->client->full_name}}
                    @endif
                @endif
                @if($key->client_type=="group" && !empty($key->group))
                    {{$key->group->name}}
                @endif
            </td>
            <td>
                @if(!empty($key->client)) {{$key->client->mobile}} @endif
                @if(!empty($key->group)) {{$key->group->mobile}} @endif
            </td>
            <td>{{ $key->client->external_id ?? '' }}</td>
            <td>@if(!empty($key->loan_product)) {{$key->loan_product->name}} @endif</td>

            {{-- KEEP THIS to preserve leading zeros --}}
            <td>{{ '="' . (string) ($key->client->bank_account_number ?? '') . '"' }}</td>

            <td>{{ number_format($on_time_amount,2) }}</td>
            <td>{{ number_format($p_30_amount,2) }}</td>
            <td>{{ number_format($p_60_amount,2) }}</td>
            <td>{{ number_format($p_90_amount,2) }}</td>
            <td>{{ number_format($p_180_amount,2) }}</td>
            <td>{{ number_format($p_180_plus_amount,2) }}</td>
            <td>{{ number_format($p_amount,2) }}</td>
        </tr>
    @endif
@endforeach

    </tbody>

    <tfoot>
    <tr>
        <th colspan="7"></th>
        <th>{{number_format($total_on_time_amount,2)}}</th>
        <th>{{number_format($total_p_30_amount,2)}}</th>
        <th>{{number_format($total_p_60_amount,2)}}</th>
        <th>{{number_format($total_p_90_amount,2)}}</th>
        <th>{{number_format($total_p_180_amount,2)}}</th>
        <th>{{number_format($total_p_180_plus_amount,2)}}</th>
        <th>{{number_format($total_p_amount,2)}}</th>
    </tr>
    </tfoot>
</table>
