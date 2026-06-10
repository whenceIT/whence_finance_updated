@extends('layouts.master')
@section('title')
    Administration Expenses
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Expense Management</h3>
        <div class="box-tools pull-right">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#admin-tab" data-toggle="tab">Administration Expense</a></li>
                <li><a href="#bank-tab" data-toggle="tab">Bank Expense</a></li>
            </ul>
        </div>
    </div>

    <div class="box-body tab-content">
        <div class="tab-pane active" id="admin-tab">
            @include('administration_expenses._index_partial')
        </div>
        <div class="tab-pane" id="bank-tab">
            @include('administration_expenses.bank_expenses._index_partial')
        </div>
    </div>
</div>
@endsection