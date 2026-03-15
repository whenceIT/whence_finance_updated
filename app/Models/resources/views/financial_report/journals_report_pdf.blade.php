<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
        font-size: 10px !important;
    }

    .style-0 {
        empty-cells: show;
        table-layout: fixed;
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

        background-color: #339933
    }

    .style-10 {
        color: black;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: italic;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

    .style-11 {
        color: black;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 1pt solid black
    }

    .style-12 {
        color: black;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        border-top: 1pt solid black
    }

    .style-13 {
        color: black;
        font-size: 10pt;
        font-family: serif;
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-14 {
        width: 50px;
        height: 50px
    }

    .style-15 {
        color: black;
        padding-right: 5pt;
        font-size: 9pt;
        font-family: serif;
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-16 {
        color: #2f2c35;
        font-size: 9pt;
        font-family: "Arial";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-2 {
        color: black;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-3 {
        color: black;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-4 {
        color: black;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-5 {
        color: white;
        padding-left: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

    .style-6 {
        color: white;
        padding-left: 5pt;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

    .style-7 {
        color: white;
        padding-left: 5pt;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

    .style-8 {
        border-top: 1pt solid black
    }

    .style-9 {
        color: black;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: italic;
        text-decoration: none;
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;

        background-color: #cccccc
    }

</style>
<div>
    @if(!empty($start_date))
        <table cellspacing="0" cellpadding="0" class="style-0">

            <tbody>
            <tr style="height: 25pt">
                <td colspan="8" valign="middle"
                    class="style-1">  {{trans_choice('general.journal',2)}} {{trans_choice('general.report',1)}}</td>
            </tr>
            <tr style="height: 15pt">
                <td valign="middle" class="style-3">{{trans_choice('general.office',1)}} :
                </td>
                <td colspan="3" valign="middle" class="style-3">
                    @if($office_id!=0)
                        {{\App\Models\Office::find($office_id)->name}}
                    @endif
                </td>
                <td colspan="3" valign="middle"
                    class="style-4">{{trans_choice('general.from',1)}} {{$start_date}} {{trans_choice('general.to',1)}} {{$end_date}}</td>
                <td>
            </tr>
            <tr style="height: 20pt">
                <td valign="middle" class="style-5">{{trans_choice('general.id',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.reference',1)}}</td>
                <td valign="middle"
                    class="style-5">{{trans_choice('general.transaction',1)}} {{trans_choice('general.type',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.date',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.gl_code',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.account',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.debit',1)}}</td>
                <td valign="middle" class="style-5">{{trans_choice('general.credit',1)}}</td>
            </tr>
            <?php
            $total_debit_balance = 0;
            $total_credit_balance = 0;
            $total_opening_balance = 0;
            $total_closing_balance = 0;
            $total_dr = 0;
            $total_cr = 0;
            $total_balance = 0;
            ?>
            @foreach($data as $key)
                <?php
                $dr = 0;
                $cr = 0;
                $balance = 0;
                $cr = $cr + $key->credit;
                $dr = $dr + $key->debit;
                $total_dr = $total_dr + $dr;
                $total_cr = $total_cr + $cr;
                if ($key->account_type == "asset" || $key->account_type == "expense") {
                    //debit balance
                    $balance = $dr - $cr;
                }
                if ($key->account_type == "liability" || $key->account_type == "equity" || $key->account_type == "income") {
                    //debit balance
                    $balance = $cr - $dr;
                }
                $total_balance = $total_balance + $balance;
                ?>
                <tr style="height: 15pt">
                    <td valign="middle" class="style-3">{{ $key->id }}</td>
                    <td valign="middle" class="style-3">{{ $key->reference }}</td>
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
                    <td valign="middle" class="style-3">
                        {{$key->date}}
                    </td>
                    @if(!empty($key->gl_account))
                        <td> {{ $key->gl_account->gl_code }}</td>
                        <td> {{ $key->gl_account->name }}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                    <td valign="middle" class="style-4">{{ number_format($dr,2) }}</td>
                    <td valign="middle" class="style-4">{{ number_format($cr,2) }}</td>
                </tr>
            @endforeach
            <tr style="height: 2pt">
                <td class="style-8" colspan="8"></td>
            </tr>
            </tbody>

        </table>
    @endif
</div>