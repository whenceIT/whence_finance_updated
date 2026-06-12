@extends('layouts.master')

@section('title')
Expense Management Dashboard
@endsection

@section('content')

<section class="content-header">

    <h1>
        Expense Dashboard
        <small>Institution → Province → Branch → Category → Expense</small>
    </h1>

    <div class="alert alert-info"
         style="margin-top:15px; margin-bottom:0;">

        <i class="fa fa-calendar"></i>

        <strong>
            Expense Summary Period:
        </strong>

        {{ \Carbon\Carbon::parse($start_date)->format('d F Y') }}

        to

        {{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}

    </div>

</section>

<section class="content">
    {{-- FILTER --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">
                <i class="fa fa-filter"></i>
                Filter Expenses
            </h3>
        </div>

        <div class="box-body">
            <form method="GET" class="row">

                <div class="col-md-3">
                    <label>Start Date</label>
                   <input
    type="date"
    class="form-control"
    name="start_date"
    value="{{ $start_date }}"
>
                </div>

                <div class="col-md-3">
                    <label>End Date</label>
                    <input
    type="date"
    class="form-control"
    name="end_date"
    value="{{ $end_date }}"
>
                </div>

                <div class="col-md-3">
                    <button class="btn btn-primary"
                            style="margin-top:25px;">
                        Apply Filter
                    </button>
                </div>

            </form>
        </div>
    </div>

    {{-- KPI CARDS --}}
<div class="row">

    <div class="col-md-4">

        <div class="info-box">

            <span class="info-box-icon bg-red">
                <i class="fa fa-money"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Total Expenses
                </span>

                <span class="info-box-number">
                    K{{ number_format($institution['total_expenses'] ?? 0,2) }}
                </span>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="info-box">

            <span class="info-box-icon bg-blue">
                <i class="fa fa-list"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Transactions
                </span>

                <span class="info-box-number">
                    {{ number_format($institution['transactions'] ?? 0) }}
                </span>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="info-box">

            <span class="info-box-icon bg-green">
                <i class="fa fa-calculator"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Average Expense
                </span>

                <span class="info-box-number">
                    K{{ number_format($institution['average_expense'] ?? 0,2) }}
                </span>

            </div>

        </div>

    </div>

</div>

<div class="row">

    <div class="col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-purple">
                <i class="fa fa-map"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Highest Spending Province
                </span>

                <span class="info-box-number"
                      style="font-size:18px;">

                    {{ $topProvince['province_name'] ?? 'N/A' }}

                </span>

                <span class="text-muted">

                    K{{ number_format($topProvince['total_expenses'] ?? 0,2) }}

                </span>

            </div>

        </div>

    </div>

    <div class="col-md-6">

        <div class="info-box">

            <span class="info-box-icon bg-aqua">
                <i class="fa fa-building"></i>
            </span>

            <div class="info-box-content">

                <span class="info-box-text">
                    Highest Spending Branch
                </span>

                <span class="info-box-number"
                      style="font-size:18px;">

                    {{ $topBranch['office_name'] ?? 'N/A' }}

                </span>

                <span class="text-muted">

                    K{{ number_format($topBranch['total_expenses'] ?? 0,2) }}

                </span>

            </div>

        </div>

    </div>

</div>


<div class="box box-success">

    <div class="box-header with-border">
        <h3 class="box-title">
            National Expense Summary
        </h3>
    </div>

    <div class="box-body table-responsive no-padding">

        <table class="table table-bordered table-hover">

            <thead>
                <tr>
                    <th width="50"></th>
                    <th>Level</th>
                    <th>Total Expenses</th>
                    <th>Transactions</th>
                </tr>
            </thead>

            <tbody>

                <tr class="national-row"
                    data-id="national">

                    <td>
                        <button
                            class="btn btn-xs btn-success toggle-national">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>

                    <td>
                        <strong>National</strong>
                    </td>

                    <td>
                        K{{ number_format($institution['total_expenses'] ?? 0,2) }}
                    </td>

                    <td>
                        {{ number_format($institution['transactions'] ?? 0) }}
                    </td>

                </tr>

                <tr id="national-categories"
                    style="display:none;">

                    <td colspan="4">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th width="50"></th>
                                    <th>Category</th>
                                    <th>Total</th>
                                    <th>Transactions</th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach($categoryBreakdown as $category)

                                <tr class="national-category-row"
                                    data-id="national-{{ $category['expense_type_id'] }}">

                                    <td>
                                        <button
                                            class="btn btn-xs btn-primary toggle-national-category">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>

                                    <td>
                                        {{ $category['category'] }}
                                    </td>

                                    <td>
                                        K{{ number_format($category['total'],2) }}
                                    </td>

                                    <td>
                                        {{ $category['transactions'] }}
                                    </td>

                                </tr>

                                <tr id="national-expenses-{{ $category['expense_type_id'] }}"
                                    style="display:none;">

                                    <td colspan="4">

                                        <table class="table table-striped table-bordered">

                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Province</th>
                                                    <th>Branch</th>
                                                    <th>Name</th>

                                                       @if($category['expense_type_id'] == -999)
            <th>Reason</th>
            <th>Gateway Fee</th>
            <th>Withinhere Fee</th>
        @endif

                                                    <th>Amount</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($expenses as $expense)

                                                @if($expense['expense_type_id'] == $category['expense_type_id'])

                                         <tr>
    <td>{{ date('d-M-Y', strtotime($expense['date'])) }}</td>
    <td>{{ $expense['province_name'] }}</td>
    <td>{{ $expense['office_name'] }}</td>
    <td>{{ $expense['name'] }}</td>

    @if($category['expense_type_id'] == -999)
        <td>{{ $expense['reason'] ?? '-' }}</td>
        <td>K{{ number_format($expense['gateway_fee'] ?? 0, 2) }}</td>
        <td>K{{ number_format($expense['withinhere_fee'] ?? 0, 2) }}</td>
    @endif

    <td>K{{ number_format($expense['amount'],2) }}</td>
</tr>

                                                @endif

                                            @endforeach

                                            </tbody>

                                        </table>

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

        <div class="box box-primary">

        <div class="box-header with-border">
            <h3 class="box-title">
                Provincial Expense Summary
            </h3>
        </div>

        <div class="box-body table-responsive no-padding">

            <table class="table table-bordered table-hover">

                <thead>

                <tr>
                    <th width="50"></th>
                    <th>Province</th>
                    <th>Total Expenses</th>
                    <th>Transactions</th>
                 
                </tr>

                </thead>

       <tbody>

@forelse($provinces as $province)

<tr class="province-row"
    data-id="{{ $province['province_id'] }}">

    <td>
        <button
            class="btn btn-xs btn-primary toggle-province">
            <i class="fa fa-plus"></i>
        </button>
    </td>

    <td>
        <strong>
            {{ $province['province_name'] }}
        </strong>
    </td>

    <td>
        K{{ number_format($province['total_expenses'],2) }}
    </td>

    <td>
        {{ number_format($province['transactions']) }}
    </td>



</tr>

<tr id="branches-{{ $province['province_id'] }}"
    style="display:none;">

    <td colspan="5">

        <table class="table table-bordered">

            <thead>
                <tr>
                    <th width="50"></th>
                    <th>Branch</th>
                    <th>Total</th>
                    <th>Transactions</th>
                 
                </tr>
            </thead>

            <tbody>

            @foreach($branches as $branch)

                @if($branch['province_id'] == $province['province_id'])

                <tr class="branch-row"
                    data-id="{{ $branch['office_id'] }}">

                    <td>
                        <button
                            class="btn btn-xs btn-warning toggle-branch">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>

                    <td>
                        {{ $branch['office_name'] }}
                    </td>

                    <td>
                        K{{ number_format($branch['total_expenses'],2) }}
                    </td>

                    <td>
                        {{ $branch['transactions'] }}
                    </td>

                 

                </tr>

                <tr id="categories-{{ $branch['office_id'] }}"
                    style="display:none;">

                    <td colspan="5">

                        <table class="table table-bordered">

                            <thead>
                                <tr>
                                    <th width="50"></th>
                                    <th>Category</th>
                                    <th>Total</th>
                                    <th>Transactions</th>
                                </tr>
                            </thead>

                            <tbody>

                            @foreach($categories as $category)

                                @if($category['office_id'] == $branch['office_id'])

                                <tr class="category-row"
                                    data-id="{{ $branch['office_id'] }}-{{ $category['expense_type_id'] }}">

                                    <td>
                                        <button
                                            class="btn btn-xs btn-success toggle-category">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>

                                    <td>
                                        {{ $category['expense_type'] }}
                                    </td>

                                    <td>
                                        K{{ number_format($category['total_expenses'],2) }}
                                    </td>

                                    <td>
                                        {{ $category['transactions'] }}
                                    </td>

                                </tr>

                                <tr id="expenses-{{ $branch['office_id'] }}-{{ $category['expense_type_id'] }}"
                                    style="display:none;">

                                    <td colspan="4">

                                        <table class="table table-striped table-bordered">

                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Name</th>
                                                    <th>Category</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>

                                            <tbody>

                                            @foreach($expenses as $expense)

                                                @if(
                                                    $expense['office_id'] == $branch['office_id']
                                                    &&
                                                    $expense['expense_type_id'] == $category['expense_type_id']
                                                )

                                                <tr>

                                                    <td>
                                                        {{ date('d-M-Y', strtotime($expense['date'])) }}
                                                    </td>

                                                    <td>
                                                        {{ $expense['name'] }}
                                                    </td>

                                                    <td>
                                                        {{ $expense['expense_type'] }}
                                                    </td>

                                                    <td>
                                                        K{{ number_format($expense['amount'],2) }}
                                                    </td>

                                                </tr>

                                                @endif

                                            @endforeach

                                            </tbody>

                                        </table>

                                    </td>

                                </tr>

                                @endif

                            @endforeach

                            </tbody>

                        </table>

                    </td>

                </tr>

                @endif

            @endforeach

            </tbody>

        </table>

    </td>

</tr>

@empty

<tr>
    <td colspan="5"
        class="text-center text-muted">
        No expense records found.
    </td>
</tr>

@endforelse

</tbody>

            </table>

        </div>

    </div>


    {{-- LEADERBOARDS --}}
    <div class="row">

    {{-- Province Ranking --}}
    <div class="col-md-6">

        <div class="box box-info collapsed-box">

            <div class="box-header with-border">

                <h3 class="box-title">
                    Province Ranking
                </h3>

                <div class="box-tools pull-right">

                    <button type="button"
                            class="btn btn-box-tool"
                            data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>

                </div>

            </div>

            <div class="box-body table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th>Rank</th>
                            <th>Province</th>
                            <th>Total Expenses</th>
                        </tr>

                    </thead>

                    <tbody>

                    @foreach($provinces as $index => $province)

                        <tr>

                            <td>
                                <span class="badge bg-blue">
                                    #{{ $index + 1 }}
                                </span>
                            </td>

                            <td>
                                {{ $province['province_name'] }}
                            </td>

                            <td>
                                K{{ number_format($province['total_expenses'],2) }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

    {{-- Branch Ranking --}}
    <div class="col-md-6">

        <div class="box box-warning collapsed-box">

            <div class="box-header with-border">

                <h3 class="box-title">
                    Branch Ranking
                </h3>

                <div class="box-tools pull-right">

                    <button type="button"
                            class="btn btn-box-tool"
                            data-widget="collapse">
                        <i class="fa fa-plus"></i>
                    </button>

                </div>

            </div>

            <div class="box-body table-responsive">

                <table class="table table-bordered table-striped">

                    <thead>

                        <tr>
                            <th>Rank</th>
                            <th>Branch</th>
                            <th>Total Expenses</th>
                        </tr>

                    </thead>

                    <tbody>

                    @foreach($branches as $index => $branch)

                        <tr>

                            <td>
                                <span class="badge bg-green">
                                    #{{ $index + 1 }}
                                </span>
                            </td>

                            <td>
                                {{ $branch['office_name'] }}
                            </td>

                            <td>
                                K{{ number_format($branch['total_expenses'],2) }}
                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

    {{-- DRILLDOWN TABLE --}}


    {{-- TOP EXPENSES --}}
    <div class="box box-danger">

        <div class="box-header with-border">
            <h3 class="box-title">
                Top 10 Biggest Expenses
            </h3>
        </div>

        <div class="box-body table-responsive">

            <table class="table table-bordered">

                <thead>
              <tbody>

@foreach(collect($expenses)->sortByDesc('amount')->take(10) as $expense)

<tr>

    <td>
        {{ date('d-M-Y', strtotime($expense['date'])) }}
    </td>

    <td>
        {{ $expense['office_name'] }}
    </td>

    <td>
        {{ $expense['expense_type'] }}
    </td>

    <td>
        {{ $expense['name'] }}
    </td>

    <td>
        <strong>
            K{{ number_format($expense['amount'],2) }}
        </strong>
    </td>

</tr>

@endforeach

</tbody>
                </thead>

            </table>

        </div>

    </div>

    {{-- MONTHLY COMPARISON --}}
    <div class="row">

        <div class="col-md-6">

            <div class="info-box">

                <span class="info-box-icon bg-green">
                    <i class="fa fa-line-chart"></i>
                </span>

                <div class="info-box-content">

                    <span class="info-box-text">
                        Monthly Comparison
                    </span>

                    <span class="info-box-number">
                  {{ number_format($monthlyComparison['percentage'],2) }}%
                    </span>

                    <small>
                        Compared to previous month
                    </small>

                </div>

            </div>

        </div>

        <div class="col-md-6">

     

        </div>

    </div>

</section>
@endsection

@section('footer-scripts')

<script>

document.addEventListener('DOMContentLoaded', function(){

    function setupToggle(buttonClass, targetPrefix){

        document.querySelectorAll(buttonClass).forEach(btn => {

            btn.addEventListener('click', function(){

                let row = this.closest('tr');
                let id = row.dataset.id;

                let target = document.getElementById(
                    targetPrefix + id
                );

                let icon = this.querySelector('i');

                if(target.style.display === 'none'){

                    target.style.display = 'table-row';

                    icon.classList.remove('fa-plus');
                    icon.classList.add('fa-minus');

                }else{

                    target.style.display = 'none';

                    icon.classList.remove('fa-minus');
                    icon.classList.add('fa-plus');

                }

            });

        });

    }

    setupToggle('.toggle-province','branches-');
    setupToggle('.toggle-branch','categories-');
    setupToggle('.toggle-category','expenses-');

});


document.querySelectorAll('.toggle-national').forEach(btn => {

    btn.addEventListener('click', function(){

        let target =
            document.getElementById('national-categories');

        let icon = this.querySelector('i');

        if(target.style.display === 'none'){

            target.style.display = 'table-row';

            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');

        }else{

            target.style.display = 'none';

            icon.classList.remove('fa-minus');
            icon.classList.add('fa-plus');

        }

    });

});

document.querySelectorAll('.toggle-national-category').forEach(btn => {

    btn.addEventListener('click', function(){

        let row = this.closest('tr');

        let id =
            row.dataset.id.replace('national-','');

        let target =
            document.getElementById(
                'national-expenses-' + id
            );

        let icon = this.querySelector('i');

        if(target.style.display === 'none'){

            target.style.display = 'table-row';

            icon.classList.remove('fa-plus');
            icon.classList.add('fa-minus');

        }else{

            target.style.display = 'none';

            icon.classList.remove('fa-minus');
            icon.classList.add('fa-plus');

        }

    });

});

</script>

<style>

.province-row{
    background:#f4f4f4;
    font-weight:600;
}

.branch-row{
    background:#fcfcfc;
}

.table td,
.table th{
    vertical-align:middle !important;
}

.small-box h3{
    font-size:28px;
}

</style>

@endsection