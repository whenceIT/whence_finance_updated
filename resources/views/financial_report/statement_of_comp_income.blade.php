@extends('layouts.master')
@section('title')
    statement of comp. income
@endsection
@section('content')
<style>
    .style-0 {
        empty-cells: show;
        table-layout: fixed;
        width: 1400px;
    }

    table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
<div class="panel panel-white">
    <div class="panel-body table-responsive">
        <table class="style-0 ">
            <tbody>
                <tr>
                <td colspan="3" style="height:20px;">ZMK</td>
                </tr>
          

           </td>

            <tr> 
            <td colspan="1"></td>
                <td colspan="1">NOTES(S)</td>
                @foreach($years as $year)
                <td colspan="1">{{$year}}</td>
                @endforeach
            </tr>

            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
		<td  valign="middle" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
            <td colspan="1" style="font-weight:bold;">REVENUE</td>
            <td colspan="1">3</td>
            @foreach($year_index as $index)
            <td colspan="1">{{$yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))}}</td>
            @endforeach
            </tr>


            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
            <td colspan="1" style="font-weight:bold;">COST OF SALES</td>
            <td colspan="1">4</td>
            @foreach($year_index as $index)
            <td colspan="1">{{$yearly_cost_total[$index]}}</td>
            @endforeach
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>


            <tr>
            <td colspan="1" style="font-weight:bold;">GROSS PROFIT</td>
            <td colspan="1"></td>
            @foreach($year_index as $index)
            <td>{{($yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))) - ($yearly_cost_total[$index])}}</td>
            @endforeach
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>


            <tr>
            <td colspan="1" style="font-weight:bold;">ADMINISTRATIVE COSTS</td>
            <td colspan="1">5</td>
            @foreach($year_index as $index)
            <td>{{$yearly_final_expense_totals[$index]}}</td>
            @endforeach
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>

            <tr>
            <td colspan="1" style="font-weight:bold;">DISTRIBUTION COSTS</td>
            <td colspan="1">6</td>
            @foreach($year_index as $index)
            <td>{{$yearly_final_distribution_total[$index]}}</td>
            @endforeach
            </tr>


            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>

            <tr>
            <td colspan="1" style="font-weight:bold;">PROFIT/LOSS BEFORE INTEREST & TAX (PBIT)</td>
            <td colspan="1"></td>
            @foreach($year_index as $index)
            <td>{{(($yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))) - ($yearly_cost_total[$index])) - $yearly_final_expense_totals[$index] - $yearly_final_distribution_total[$index]}}</td>
            @endforeach
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>


            <td colspan="1" style="font-weight:bold;">INTEREST RECEIVED/PAID</td>
            <td colspan="1"></td>
            @foreach($year_index as $index)
            <td>-</td>
            @endforeach
            </tr>


            
            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>

            <td colspan="1" style="font-weight:bold;">PROFIT/LOSS  BEFORE TAX</td>
            <td colspan="1"></td>
            @foreach($year_index as $index)
            <td>{{(($yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))) - ($yearly_cost_total[$index])) - $yearly_final_expense_totals[$index] - $yearly_final_distribution_total[$index]}}</td>
            @endforeach
            </tr>


            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>


            <td colspan="1" style="font-weight:bold;">CORPORATE INCOME TAX</td>
            <td colspan="1">7</td>
            @foreach($year_index as $index)
            <td>-</td>
            @endforeach
            </tr>
            
    
            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>



            <td colspan="1" style="font-weight:bold;">PROFIT AFTER TAX</td>
            <td colspan="1"></td>
            @foreach($year_index as $index)
            <td>{{number_format((($yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))) - ($yearly_cost_total[$index])) - $yearly_final_expense_totals[$index] - $yearly_final_distribution_total[$index],2)}}</td>
            @endforeach
            </tr>

            <tr>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
		<td colspan="1" style="font-weight:bold; height:20px;"></td>
  <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>
                <td colspan="1" style="font-weight:bold; height:20px;"></td>

            </tr>




            </tbody>
        </table>
    </div>
</div>

@endsection
