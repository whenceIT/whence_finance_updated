@extends('layouts.master')

@section('title', 'Money Movements')

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
            Money Movement
        </h1>

        <p style="
            font-size:16px;
            color:#6b7280;
            margin:0;
        ">
            Select the type of transaction you would like to record.
        </p>

    </div>

    <div class="row">

        {{-- EXPENSE --}}
        <div class="col-md-6">

            <a href="{{ url('expense/create') }}"
               style="text-decoration:none;">

                <div style="
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    min-height:340px;
                    padding:25px;
                    margin-bottom:25px;
                    box-shadow:0 4px 20px rgba(0,0,0,0.06);
                ">

                    <div style="
                        width:60px;
height:60px;
line-height:60px;
                        background:#fee2e2;
                        border-radius:18px;
                        text-align:center;
                  
                        margin-bottom:25px;
                    ">
                        <i class="fa fa-money"
                           style="
                                color:#dc2626;
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
                        Operational Expense
                    </h3>

                    <p style="
                        color:#6b7280;
                        line-height:1.8;
                        margin-bottom:25px;
                    ">
                        Record money spent on operating the business.
                        These funds leave the institution permanently and
                        are treated as expenses.
                    </p>

                    <div style="
                        background:#f9fafb;
                        border-radius:12px;
                        padding:15px;
                        margin-bottom:25px;
                    ">

                        <strong style="display:block;margin-bottom:10px;">
                            Common Examples
                        </strong>

                        <div style="margin-bottom:8px;">
                            ✓ Fuel Purchases
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Office Rent
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Utilities
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Stationery
                        </div>

                     

                    </div>

                    <span style="
                        background:#fee2e2;
                        color:#b91c1c;
                        padding:8px 16px;
                        border-radius:999px;
                        font-size:12px;
                        font-weight:600;
                    ">
                        Funds Leave The Institution
                    </span>

                    <div style="margin-top:30px;">

                        <div style="
                            background:#dc2626;
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

        {{-- INTERNAL MOVEMENT --}}
        <div class="col-md-6">

            <a href="{{ url('accounting/internal_fund_movement') }}"
               style="text-decoration:none;">

                <div style="
                    background:#ffffff;
                    border:1px solid #e5e7eb;
                    border-radius:18px;
                    min-height:340px;
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
                        Internal Fund Movement
                    </h3>

                    <p style="
                        color:#6b7280;
                        line-height:1.8;
                        margin-bottom:25px;
                    ">
                        Transfer money between branches, provinces,
                        head office and bank accounts while keeping
                        funds within the institution.
                    </p>

                    <div style="
                        background:#f9fafb;
                        border-radius:12px;
                        padding:15px;
                        margin-bottom:25px;
                    ">

                        <strong style="display:block;margin-bottom:10px;">
                            Common Examples
                        </strong>

                        <div style="margin-bottom:8px;">
                            ✓ Branch → Branch
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Branch → Provincal Office
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Branch → Head Office
                        </div>

                        <div style="margin-bottom:8px;">
                            ✓ Branch → Bank Account
                        </div>


                    </div>

                    <span style="
                        background:#dbeafe;
                        color:#1d4ed8;
                        padding:8px 16px;
                        border-radius:999px;
                        font-size:12px;
                        font-weight:600;
                    ">
                        Money Stays In The Institution
                    </span>

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

    </div>

</div>
```

</div>

@stop
