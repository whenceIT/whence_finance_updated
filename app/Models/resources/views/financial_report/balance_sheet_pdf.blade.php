<style>
    table {
        width: 100%;
        border-collapse: collapse;
        font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
    }
    .style-0 {
        empty-cells: show;
        table-layout: fixed;
        width: 575pt
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

    .style-11 {
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

    .style-12 {
        border-top: 1pt solid black
    }

    .style-13 {
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

    .style-14 {
        color: black;
        padding-right: 5pt;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-15 {
        color: black;
        padding-right: 5pt;
        font-size: 9pt;
        font-family: "Arial";
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
        text-align: left;
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
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-5 {
        color: #2f2c35;
        font-size: 10pt;
        font-family: "Arial";
        font-weight: normal;
        font-style: normal;
        text-decoration: none;
        text-align: right;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

    .style-6 {
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
        background-color: #999999
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
        text-align: left;
        word-spacing: 0pt;
        letter-spacing: 0pt;
        background-color: #999999
    }

    .style-8 {
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
        background-color: #999999
    }

    .style-9 {
        color: black;
        font-size: 13pt;
        font-family: "Arial";
        font-weight: bold;
        font-style: normal;
        text-decoration: none;
        text-align: center;
        word-spacing: 0pt;
        letter-spacing: 0pt;

    }

</style>
<div>

    @if(!empty($end_date))
        <table cellspacing="0" cellpadding="0" class="style-0">
            <tbody>
            <tr style="height: 33pt">
                <td colspan="16" valign="middle"
                    class="style-1"> {{trans_choice('general.balance',1)}} {{trans_choice('general.sheet',1)}}</td>
            </tr>
            <tr style="height: 6pt">
                <td colspan="16"></td>
            </tr>
            <tr style="height: 16pt">
                <td colspan="4" valign="middle" class="style-2">{{trans_choice('general.office',1)}}</td>
                <td valign="middle" class="style-3">
                    @if($office_id!=0)
                        {{\App\Models\Office::find($office_id)->name}}
                    @endif
                </td>
                <td>
                </td>
                <td>
                </td>
                <td colspan="9" valign="middle"
                    class="style-4">{{trans_choice('general.as',1)}} {{trans_choice('general.at',1)}} {{$end_date}}</td>
            </tr>
            <tr style="height: 12pt">
                <td colspan="7"></td>
                <td colspan="2" valign="middle" class="style-4">{{trans_choice('general.on',1)}}:</td>
                <td colspan="5" valign="middle" class="style-5">{{date("Y-m-d H:i:s")}}</td>
                <td></td>
                <td></td>
            </tr>
            <tr style="height: 20pt">
                <td colspan="2" valign="middle" class="style-6">{{trans_choice('general.gl_code',1)}}</td>
                <td colspan="3" valign="middle" class="style-7">{{trans_choice('general.account',1)}}</td>
                <td colspan="6" valign="middle" class="style-7">{{trans_choice('general.office',1)}}</td>
                <td colspan="5" valign="middle" class="style-8">{{trans_choice('general.balance',1)}}</td>

            </tr>
            <tr style="height: 20pt">
                <td></td>
                <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.asset',2)}}</td>
            </tr>
            <?php
            $total_assets = 0;
            $total_liabilities = 0;
            $total_equity = 0;
            ?>
            @foreach(\App\Models\GlAccount::where('account_type','asset')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $dr = 0;
                $cr = 0;
                $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->where('date', '<=', $end_date)->get();

                foreach ($journals as $journal) {
                    $cr = $cr + $journal->credit;
                    $dr = $dr + $journal->debit;
                }
                $balance = $dr - $cr;
                $total_assets = $total_assets + $balance;
                ?>
                <tr style="height: 15pt">

                    <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                    <td colspan="3" valign="middle" class="style-3">
                        {{$key->name}}
                    </td>
                    <td colspan="6"></td>
                    <td colspan="5" valign="middle" class="style-4">{{ number_format($balance,2) }}</td>
                </tr>
                <tr style="height: 4pt">
                    <td colspan="16">
                    </td>
                </tr>
                <tr style="height: 1pt">
                    <td class="style-12" colspan="16">
                    </td>
                </tr>
            @endforeach
            <tr style="height: 1pt">

                <td class="style-12" colspan="16"></td>
            </tr>
            <tr style="height: 1pt">

                <td colspan="11" valign="middle" class="style-13">
                    {{trans_choice('general.total',1)}} {{trans_choice('general.asset',2)}}</td>
                <td colspan="5" valign="middle"
                    class="style-14">{{ number_format($total_assets,2) }}</td>
            </tr>

            <tr style="height: 0pt">

                <td class="style-12" colspan="16">
                </td>
            </tr>
            <tr style="height: 20pt">
                <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.liability',2)}}</td>
            </tr>

            @foreach(\App\Models\GlAccount::where('account_type','liability')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $dr = 0;
                $cr = 0;
                $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->where('date', '<=', $end_date)->get();

                foreach ($journals as $journal) {
                    $cr = $cr + $journal->credit;
                    $dr = $dr + $journal->debit;
                }
                $balance = $cr - $dr;
                $total_liabilities = $total_liabilities + $balance;
                ?>
                <tr style="height: 15pt">
                    <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                    <td colspan="3" valign="middle" class="style-3">
                        {{$key->name}}
                    </td>
                    <td colspan="6"></td>
                    <td colspan="5" valign="middle" class="style-4">{{ number_format($balance,2) }}</td>
                </tr>
                <tr style="height: 4pt">
                    <td colspan="16">
                    </td>
                </tr>
                <tr style="height: 1pt">
                    <td class="style-12" colspan="16">
                    </td>
                </tr>
            @endforeach
            <tr style="height: 1pt">

                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 1pt">

                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 20pt">

                <td colspan="11" valign="middle"
                    class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}}
                    :
                </td>
                <td colspan="5" valign="middle" class="style-14">{{ number_format($total_liabilities,2) }}</td>
            </tr>
            <tr style="height: 1pt">
                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 18pt">
                <td colspan="11" valign="middle"
                    class="style-13">{{trans_choice('general.net',1)}} {{trans_choice('general.income',2)}} :
                </td>
                <td colspan="5" valign="middle"
                    class="style-14">{{ number_format($total_liabilities-$total_liabilities,2) }}</td>
            </tr>
            <tr style="height: 0pt">

                <td class="style-12" colspan="16">
                </td>
            </tr>
            <tr style="height: 20pt">
                <td colspan="16" valign="middle" class="style-9">{{trans_choice('general.equity',2)}}</td>
            </tr>

            @foreach(\App\Models\GlAccount::where('account_type','equity')->orderBy('gl_code','asc')->get() as $key)
                <?php
                $balance = 0;
                $dr = 0;
                $cr = 0;
                $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->where('date', '<=', $end_date)->get();

                foreach ($journals as $journal) {
                    $cr = $cr + $journal->credit;
                    $dr = $dr + $journal->debit;
                }
                $balance = $cr - $dr;
                $total_equity = $total_equity + $balance;
                ?>
                <tr style="height: 15pt">
                    <td colspan="2" valign="middle" class="style-3">{{ $key->gl_code }}</td>
                    <td colspan="3" valign="middle" class="style-3">
                        {{$key->name}}
                    </td>
                    <td colspan="6"></td>
                    <td colspan="5" valign="middle" class="style-4">{{ number_format($balance,2) }}</td>
                </tr>
                <tr style="height: 4pt">
                    <td colspan="16">
                    </td>
                </tr>
                <tr style="height: 1pt">
                    <td class="style-12" colspan="16">
                    </td>
                </tr>
            @endforeach
            <tr style="height: 15pt">
                <td colspan="2" valign="middle" class="style-3"></td>
                <td colspan="3" valign="middle" class="style-3">
                    {{trans_choice('general.current',1)}} {{trans_choice('general.year',1)}} {{trans_choice('general.profit',2)}}
                </td>
                <td colspan="6"></td>
                <?php
                $current_year_profits = 0;
                $office = \App\Models\Office::find($office_id);
                $total_expenses = 0;
                $total_income = 0;
                $total_expenses = 0;
                foreach (\App\Models\GlAccount::where('account_type', 'expense')->orderBy('gl_code', 'asc')->get() as $key) {
                    $balance = 0;
                    $dr = 0;
                    $cr = 0;
                    $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                        if ($office_id != 0) {
                            $query->where('office_id', '=', $office_id);
                        }
                    })->whereBetween('date',
                        [$office->opening_date, $end_date])->get();

                    foreach ($journals as $journal) {
                        $cr = $cr + $journal->credit;
                        $dr = $dr + $journal->debit;
                    }
                    $balance = $dr - $cr;
                    $total_expenses = $total_expenses + $balance;
                }
                foreach (\App\Models\GlAccount::where('account_type', 'income')->orderBy('gl_code', 'asc')->get() as $key) {
                    $balance = 0;
                    $dr = 0;
                    $cr = 0;
                    $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                        if ($office_id != 0) {
                            $query->where('office_id', '=', $office_id);
                        }
                    })->whereBetween('date',
                        [$office->opening_date, $end_date])->get();

                    foreach ($journals as $journal) {
                        $cr = $cr + $journal->credit;
                        $dr = $dr + $journal->debit;
                    }
                    $balance = $cr - $dr;
                    $total_income = $total_income + $balance;
                }
                $current_year_profits = $total_income - $total_expenses;
                $total_equity=$total_equity+$current_year_profits;
                ?>
                <td colspan="5" valign="middle" class="style-4">{{ number_format($current_year_profits,2) }}</td>
            </tr>
            <tr style="height: 4pt">
                <td colspan="16">
                </td>
            </tr>
            <tr style="height: 1pt">
                <td class="style-12" colspan="16">
                </td>
            </tr>
            <tr style="height: 1pt">

                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 1pt">

                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 20pt">

                <td colspan="11" valign="middle"
                    class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.equity',2)}} :
                </td>
                <td colspan="5" valign="middle" class="style-14">{{ number_format($total_equity,2) }}</td>
            </tr>
            <tr style="height: 1pt">
                <td class="style-12" colspan="16">
                </td>

            </tr>
            <tr style="height: 18pt">
                <td colspan="11" valign="middle"
                    class="style-13">{{trans_choice('general.total',1)}} {{trans_choice('general.liability',2)}} {{trans_choice('general.and',2)}} {{trans_choice('general.equity',2)}}
                    :
                </td>
                <td colspan="5" valign="middle"
                    class="style-14">{{ number_format($total_equity+$total_liabilities,2) }}</td>
            </tr>
            </tbody>
        </table>
    @endif
</div>