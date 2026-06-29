@extends('layouts.master')

@section('title', 'Internal Fund Movement')

@section('content')

<div class="row">

```
<div class="col-md-10 col-md-offset-1">

    <div style="text-align:center;margin-bottom:40px;">

        <h1 style="
            font-size:40px;
            font-weight:700;
            color:#1f2937;
            margin-bottom:10px;
        ">
            Internal Fund Movement
        </h1>

        <p style="
            font-size:16px;
            color:#6b7280;
            margin:0;
        ">
            Select where the money is being moved.
        </p>

    </div>

    <div class="row">

        {{-- BRANCH TRANSFER --}}
        <div class="col-md-6">

            <a href="{{ url('accounting/add_fund_transfers_and_payments') }}"
               style="text-decoration:none;">

                <div style="
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    min-height:320px;
                    padding:25px;
                    margin-bottom:25px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);
                ">

                    <div style="
                        width:60px;
                        height:60px;
                        line-height:60px;
                        background:#dbeafe;
                        border-radius:18px;
                        text-align:center;
                        margin-bottom:25px;
                    ">
                        <i class="fa fa-exchange"
                           style="
                                color:#2563eb;
                                font-size:24px;
                           ">
                        </i>
                    </div>

                    <h3 style="
                        font-size:24px;
                        font-weight:700;
                        color:#111827;
                        margin-top:0;
                        margin-bottom:12px;
                    ">
                        Money Transfer
                    </h3>

                    <p style="
                        color:#6b7280;
                        line-height:1.8;
                        margin-bottom:25px;
                    ">
                        Move money between branches,
                        provincial offices and head office.
                    </p>




                        <div style="margin-top:30px;">

                        <div style="
                            background:#2563eb;
                            color:white;
                            text-align:center;
                            padding:12px;
                            border-radius:10px;
                            font-weight:600;
                        ">
                            Continue
                        </div>

                    </div>

                </div>

                

            </a>

        </div>

        {{-- BANK DEPOSIT --}}
        <div class="col-md-6">

            <a href="{{ url('user/branch_deposits') }}"
               style="text-decoration:none;">

                <div style="
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    min-height:320px;
                    padding:25px;
                    margin-bottom:25px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);
                ">

                    <div style="
                        width:60px;
                        height:60px;
                        line-height:60px;
                        background:#dcfce7;
                        border-radius:18px;
                        text-align:center;
                        margin-bottom:25px;
                    ">
                        <i class="fa fa-bank"
                           style="
                                color:#16a34a;
                                font-size:24px;
                           ">
                        </i>
                    </div>

                    <h3 style="
                        font-size:24px;
                        font-weight:700;
                        color:#111827;
                        margin-top:0;
                        margin-bottom:12px;
                    ">
                        Deposit
                    </h3>

                    <p style="
                        color:#6b7280;
                        line-height:1.8;
                        margin-bottom:25px;
                    ">
                        Deposit funds into an institutional
                        bank account. E.g Admin Fee, Statutory and Salaries
                    </p>



                        <div style="margin-top:30px;">

                        <div style="
                            background:#dcfce7;
                            color:#15803d;
                            text-align:center;
                            padding:12px;
                            border-radius:10px;
                            font-weight:600;
                        ">
                            Continue
                        </div>

                    </div>

                </div>

            </a>

        </div>

   
        <div class="col-md-6">

            <a href="{{ url('provincial-ledger') }}"
               style="text-decoration:none;">

                <div style="
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    min-height:320px;
                    padding:25px;
                    margin-bottom:25px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);
                ">

                    <div style="
                        width:60px;
                        height:60px;
                        line-height:60px;
                        background:#dcfce7;
                        border-radius:18px;
                        text-align:center;
                        margin-bottom:25px;
                    ">
                        <i class="fa fa-bank"
                           style="
                                color:#16a34a;
                                font-size:24px;
                           ">
                        </i>
                    </div>

                    <h3 style="
                        font-size:24px;
                        font-weight:700;
                        color:#111827;
                        margin-top:0;
                        margin-bottom:12px;
                    ">
                        Contributions
                    </h3>

                    <p style="
                        color:#6b7280;
                        line-height:1.8;
                        margin-bottom:25px;
                    ">
                        Record contributions to Provincial Manager towards Salaries, Petty Cash, Saving, Housing, etc...
                    </p>



                        <div style="margin-top:30px;">

                        <div style="
                            background:#dcfce7;
                            color:#15803d;
                            text-align:center;
                            padding:12px;
                            border-radius:10px;
                            font-weight:600;
                        ">
                            Continue
                        </div>

                    </div>

                </div>

            </a>

        </div>
        {{-- OTHER --}}


    </div>


</div>
```

</div>

@stop
