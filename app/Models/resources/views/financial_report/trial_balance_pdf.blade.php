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
                <td colspan="4" valign="middle"
                    class="style-1">  {{trans_choice('general.trial_balance',1)}}</td>
            </tr>
            <tr style="height: 15pt">
                <td valign="middle" class="style-2">{{trans_choice('general.office',1)}} :

                </td>
                <td valign="middle" class="style-3">
                    @if($office_id!=0)
                        {{\App\Models\Office::find($office_id)->name}}
                    @endif
                </td>
                <td colspan="2" valign="middle"
                    class="style-4">{{trans_choice('general.from',1)}} {{$start_date}} {{trans_choice('general.to',1)}} {{$end_date}}</td>
                <td>
            </tr>
            <tr style="height: 20pt">
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
            ?>
            @foreach($data as $key)
                <?php
                $dr = 0;
                $cr = 0;
                $journals = \App\Models\GlJournalEntry::where('gl_account_id', $key->id)->where('reversed', 0)->when($office_id, function ($query) use ($office_id) {
                    if ($office_id != 0) {
                        $query->where('office_id', '=', $office_id);
                    }
                })->whereBetween('date',
                    [$start_date, $end_date])->get();

                foreach ($journals as $journal) {
                    $cr = $cr + $journal->credit;
                    $dr = $dr + $journal->debit;
                }
                $total_dr = $total_dr + $dr;
                $total_cr = $total_cr + $cr;
                ?>
                <tr style="height: 15pt">
                    <td valign="middle" class="style-3">{{ $key->gl_code }}</td>
                    <td valign="middle" class="style-3">
                        {{$key->name}}
                    </td>
                    <td valign="middle" class="style-4">{{ number_format($dr,2) }}</td>
                    <td valign="middle" class="style-4">{{ number_format($cr,2) }}</td>
                </tr>
            @endforeach
            <tr style="height: 20pt">
                <td colspan="2" class="style-11"><b>{{trans_choice('general.total',1)}}</b></td>
                <td valign="middle" class="style-12">
                {{number_format($total_dr,2)}}</td>
                <td valign="middle" class="style-12">
                {{number_format($total_cr,2)}}</td>
            </tr>
            <tr style="height: 2pt">
                <td class="style-8" colspan="4"></td>
            </tr>
            </tbody>

        </table>
    @endif
</div>