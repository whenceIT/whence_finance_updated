@extends('layouts.master')

@section('title')
    Ledger Summary 
@endsection

@section('styles')
    
@endsection

@section('content')
<div class="container">
<div class="table-responsive">
    <table class="table">
        <tbody>
            <tr class="bg-success">
                <td style="font-size: 16px;">Cycle End Date</td>
                <td style="font-size: 16px;">{{ $endDate }}</td>
            </tr>
            @if(Sentinel::hasAccess('groups.create'))
            <tr class="bg-danger">
                <td style="font-size: 16px;">Opening Balance</td>
                <td style="font-size: 16px;">{{ number_format($branchOpeningBalances) }}.00</td>
            </tr>
            <tr class="bg-warning">
                <td style="font-size: 16px;">Total Advances</td>
                <td style="font-size: 16px;">{{ number_format($totalAdvances) }}.00</td>
            </tr>
            <tr class="bg-primary">
                <td style="font-size: 16px;">Total Expenses</td>
                <td style="font-size: 16px;">{{ number_format($totalExpenses) }}.00</td>
            </tr>
            
            <tr class="bg-secondary">
                <td style="font-size: 16px;">Total Full Payments</td>
                <td style="font-size: 16px;">{{ number_format($totalFullPayments) }}.00</td>
            </tr>
            <tr class="bg-danger">
                <td style="font-size: 16px;">Total Reloaned Amount</td>
                <td style="font-size: 16px;">{{ number_format($totalReloanedAmount) }}.00</td>
            </tr>
            <tr class="bg-info">
                <td style="font-size: 16px;">Total Part Payments</td>
                <td style="font-size: 16px;">{{ number_format($totalPartPayment) }}.00</td>
            </tr>
            <tr class="bg-warning">
                <td style="font-size: 16px;">New Loans</td>
                <td style="font-size: 16px;">{{ number_format($totalNewLoans) }}.00</td>
            </tr>
            <tr class="bg-primary">
                <td style="font-size: 16px;">Closing Balance</td>
                <td style="font-size: 16px;">{{ number_format($branchClosingBalances) }}.00</td>
            </tr>
            <tr class="bg-danger">
                <td style="font-size: 16px;">TCC</td>
                <td style="font-size: 16px;">{{ number_format($totalFullPayments + $totalPartPayment + $totalReloanedAmount) }}.00</td>
            </tr>
            <tr class="bg-success">
                <td style="font-size: 16px;">Cycle Opening Uncollected</td>
                <td style="font-size: 16px;">{{ number_format($total_cycle_opening_uncollected_amount) }}.00</td>
            </tr>
            <tr class="bg-info">
                <td style="font-size: 16px;">SUT</td>
                <td style="font-size: 16px;">{{ number_format($total_cycle_opening_uncollected_amount - ($totalFullPayments + $totalPartPayment + $totalReloanedAmount)) }}.00</td>
            </tr>
            @else
            <tr class="bg-danger">
                <td style="font-size: 16px;">Opening Balance</td>
                <td style="font-size: 16px;">{{ number_format($ledgerData->opening_balance) }}.00</td>
            </tr>
            <!---tr class="bg-info">
                <td style="font-size: 16px;">Total Income</td>
                <td style="font-size: 16px;">{{ number_format($ledgerData->total_income) }}.00</td>
            </tr-->
            
            <tr class="bg-warning">
                <td style="font-size: 16px;">Total Advances</td>
                <td style="font-size: 16px;">{{ number_format($advances) }}.00</td>
            </tr>
            <tr class="bg-primary">
                <td style="font-size: 16px;">Total Expenses</td>
                <td style="font-size: 16px;">{{ number_format($expenses) }}.00</td>
            </tr>
            <tr class="bg-secondary">
                <td style="font-size: 16px;">Total Full Payments</td>
                <td style="font-size: 16px;">{{ number_format($fullPayments) }}.00</td>
            </tr>
            <tr class="bg-danger">
                <td style="font-size: 16px;">Total Reloaned Amount</td>
                <td style="font-size: 16px;">{{ number_format($reloanedAmount) }}.00</td>
            </tr>
            <tr class="bg-info">
                <td style="font-size: 16px;">Total Part Payments</td>
                <td style="font-size: 16px;">{{ number_format($partPayment) }}.00</td>
            </tr>
            <tr class="bg-warning">
                <td style="font-size: 16px;">New Loans</td>
                <td style="font-size: 16px;">{{ number_format($newLoans) }}.00</td>
            </tr>
            <tr class="bg-primary">
                <td style="font-size: 16px;">Closing Balance</td>
                <td style="font-size: 16px;">{{ number_format($ledgerData->closing_balance) }}.00</td>
            </tr>
            <tr class="bg-danger">
                <td style="font-size: 16px;">TCC</td>
                <td style="font-size: 16px;">{{ number_format($fullPayments + $partPayment + $reloanedAmount) }}.00</td>
            </tr>
            <tr class="bg-success">
                <td style="font-size: 16px;">Cycle Opening Uncollected</td>
                <td style="font-size: 16px;">{{ number_format($cycle_opening_uncollected_amount) }}.00</td>
            </tr>
            <tr class="bg-info">
                <td style="font-size: 16px;">SUT</td>
                <td style="font-size: 16px;">{{ number_format($cycle_opening_uncollected_amount - ($fullPayments + $partPayment + $reloanedAmount)) }}.00</td>
            </tr>
            @endif

            <!-- Gauge section for admins -->
            @if(Sentinel::hasAccess('groups.create'))
            <tr>
                <td colspan="2">
                    <div style="margin-bottom:30px; margin-top:30px;">
                        <p style="display: flex; align-items: center; justify-content: center; font-size:50px;">PDUA%</p>
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <div class="gauge" style="width: 100%; max-width: 250px; font-size: 50px; color: #004033;">
                                <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                                    @php
                                        $formatted_pdua_percentage = number_format(($totalFullPayments + $totalPartPayment + $totalReloanedAmount) / $total_cycle_opening_uncollected_amount * 100, 2);
                                    @endphp
                                    @php
                                        $gauge_rotation = ($formatted_pdua_percentage / 100) * 180; // Adjusted for 180 degrees
                                    @endphp
                                    @if($formatted_pdua_percentage < 75)
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: red; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @elseif($formatted_pdua_percentage <= 90)
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: green; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @else
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: #d4af37; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @endif
                                    <div class="gauge__cover" style="width: 75%; height: 150%; background: #f7f7f7; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box;">{{ $formatted_pdua_percentage }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:row; justify-content:space-between; align-items: center; justify-content: center;">
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: red;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Poor</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: green;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Good</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: #d4af37;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Very Good</p>
                        </div>
                    </div>
                </td>
            </tr>
            @endif
<!-- End of gauge section for admins -->

<!-- Gauge section for branch managers -->
            @if(!Sentinel::hasAccess('groups.create'))
            <tr>
                <td colspan="2">
                    <div style="margin-bottom:30px; margin-top:30px;">
                        <p style="display: flex; align-items: center; justify-content: center; font-size:50px;">PDUA%</p>
                        <div style="display: flex; align-items: center; justify-content: center;">
                            <div class="gauge" style="width: 100%; max-width: 250px; font-size: 50px; color: #004033;">
                                <div class="gauge__body" style="width: 100%; height: 0; padding-bottom: 50%; background: #b4c0be; position: relative; border-top-left-radius: 100% 200%; border-top-right-radius: 100% 200%; overflow: hidden;">
                                    @php
                                        $formatted_pdua_percentage = number_format(($fullPayments + $partPayment + $reloanedAmount) / $cycle_opening_uncollected_amount * 100, 2);
                                    @endphp
                                    @php
                                        $gauge_rotation = ($formatted_pdua_percentage / 100) * 180; // Adjusted for 180 degrees
                                    @endphp
                                    @if($formatted_pdua_percentage < 75)
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: red; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @elseif($formatted_pdua_percentage <= 90)
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: green; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @else
                                        <div class="gauge__fill" style="position: absolute; top: 100%; left: 0; width: inherit; height: 100%; background: #d4af37; transform-origin: center top; transform: rotate({{ $gauge_rotation }}deg); transition: transform 0.2s ease-out;"></div>
                                    @endif
                                    <div class="gauge__cover" style="width: 75%; height: 150%; background: #f7f7f7; border-radius: 50%; position: absolute; top: 25%; left: 50%; transform: translateX(-50%); display: flex; align-items: center; justify-content: center; padding-bottom: 25%; box-sizing: border-box;">{{ $formatted_pdua_percentage }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:row; justify-content:space-between; align-items: center; justify-content: center;">
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: red;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Poor</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: green;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Good</p>
                        </div>
                        <div style="margin-top: 30px; margin-left: 40px; margin-right: 40px;">
                            <div style="background-color: #d4af37;  height: 10px; width: 20px;"></div>
                            <p style="text-align: center; font-weight:bold;">Very Good</p>
                        </div>
                    </div>
                </td>
            </tr>
            @endif
<!-- End of gauge section for branch managers -->
            </tbody>
        </table>
    </div>
</div>



@endsection
