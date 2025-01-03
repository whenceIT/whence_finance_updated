@extends('layouts.master')
@section('title')
    Statement of financial position
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
            <tr>
                <td colspan="1" style="height:20px;">ZMK</td>
            </tr>
<!-- CONTENT -->
            <tr> 
            <td colspan="1"></td>
            <td colspan="1"></td>
                <td colspan="1">NOTES(S)</td>
               
                @foreach($years as $year)
                <td colspan="1">{{$year}}</td>
                @endforeach
            </tr>
<!-- CONTENT -->
            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px; font-size:20px;">ASSETS</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>

<!-- SPACE -->
            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>
<!-- CONTENT -->
            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">NON-CURRENT ASSETS</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>
<!-- SPACE -->
            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>
<!-- CONTENT -->
<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">INTANGIBLE ASSETS</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
</tr>

            @foreach($asset_type_intangible_index as $asset_type_i)
            <tr>
                <td  valign="middle" style="height:20px;">{{$asset_types_intangible[$asset_type_i]->name}}</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($year_index as $year)
                <td colspan="1">{{$yearly_intangible_assets_totals[$year]}}</td>
                @endforeach
            </tr>

            @endforeach

<!-- SPACE -->
<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($year_index as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>

<!-- CONTENT -->
<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">TANGIBLE</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            
</tr>


@foreach($asset_type_index as $asset_type_i)
<tr>

    <td>{{$asset_types_tangible[$asset_type_i]->name}}</td>
    <td>{{$asset_totals[$asset_type_i]}}</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
 
</tr>
@endforeach

<tr>
    <td></td>
    <td></td>
    <td></td>
@foreach($year_index as $year)
<td colspan="1">{{$yearly_asset_totals[$year]}}</td>
@endforeach
</tr>

<!-- SPACE -->
<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($year_index as $year)
                <td colspan="1"></td>
                @endforeach
            </tr>


            <tr>
    <td>DEFERRED TAX</td>
    <td></td>
    <td>14</td>
@foreach($year_index as $index)
<td colspan="1">{{$deferred_tax_allowance - ((((  ($yearly_revenue_totals[$index] + (($yearly_expenses_totals[$index][7]*1.1095) - ($yearly_expenses_totals[$index][7]))) - ($yearly_cost_total[$index])) - $yearly_final_expense_totals[$index] - $yearly_final_distribution_total[$index]) - $add_depreciation - $disallowed_expenses) * 0.35)}}</td>
@endforeach
</tr>


<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">CURRENT ASSETS</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($years as $year)
                <td colspan="1"></td>
                @endforeach
            
</tr>

<tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">RECEIVABLES</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($year_index as $year)
                <td colspan="1">{{number_format($yearly_unrecovered_amounts[$year] * 1.228,2)}}</td>
                @endforeach
	    </tr>


            <tr>
                <td  valign="middle" style="font-weight:bold; height:20px;">CASH AND CASH EQUIVALENT</td>
                <td  valign="middle">{{number_format($yearly_cash_and_cash_equivalent_totals[0])}}</td>
                <td  valign="middle" style="font-weight:bold; height:20px;"></td>
                @foreach($year_index as $year)
                <td colspan="1">{{number_format($yearly_cash_and_cash_equivalent_totals[$year])}}</td>
                @endforeach
            </tr>




        </table>
    </div>
</div>
@endsection
