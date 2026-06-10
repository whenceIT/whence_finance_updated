@extends('layouts.master')

@section('title', 'Expenses')

@section('content')

<div class="row">

<div class="col-md-10 col-md-offset-1">

    <div style="text-align:center;margin-bottom:40px;">

        <h1 style="
            font-size:40px;
            font-weight:700;
            color:#1f2937;
            margin-bottom:10px;
        ">
           Expenses
        </h1>


        

    </div>

    <div class="row">

        {{-- Add Expense --}}
        <div class="col-md-6">

            <a href="{{ url('expense/create') }}"
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
                        Add Expenses
                    </h3>

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


        

        {{-- View Expenses --}}
        <div class="col-md-6">

            <a href="{{ url('expense/data') }}"
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
                        View Expenses
                    </h3>

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


</div>

@stop