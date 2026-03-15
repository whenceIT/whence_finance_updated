@extends('layouts.master')
@section('title')
    Bulk Repayments Upload
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Bulk Repayments (CSV Upload)</h3>

        <div class="box-tools pull-right">
            <a class="btn btn-success btn-sm" href="{{ url('loan/repayment/bulk/template') }}">
                Download CSV Template
            </a>
            <button onclick="window.history.back()" class="btn btn-info btn-sm">
                {{ trans_choice('general.cancel',1) }}
            </button>
        </div>
    </div>
{{-- TXT Conversion Wizard --}}
<div class="box-body" style="border-bottom:1px solid #f4f4f4; margin-bottom:15px;">
    <h4 style="margin-top:0;">TXT Conversion Wizard (Payroll TXT → CSV)</h4>
    <p class="text-muted">
        Upload the payroll TXT report and download a CSV template that you can review/edit, then upload for reconciliation.
    </p>

    <form id="txtConvertForm" method="post" action="{{ url('loan/repayment/bulk/convert-txt') }}"
      class="form-horizontal" enctype="multipart/form-data">
        {{ csrf_field() }}

        <div class="form-group">
            <label class="control-label col-md-2">TXT File</label>
            <div class="col-md-6">
                <input type="file" name="txt" accept=".txt" class="form-control" required>
                <p class="text-muted" style="margin-top:8px;">
                    Converts payroll report lines into:
                    <b>first_name,last_name,identification,external_id,loan_id,amount</b>
                </p>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-success">
                    Convert & Download CSV
                </button>
            </div>
        </div>
    </form>
</div>

   <form id="bulkUploadForm" method="post" action="{{ url('loan/repayment/bulk/preview') }}"
      class="form-horizontal" enctype="multipart/form-data">

        {{ csrf_field() }}

        <div class="box-body">

            {{-- CSV Upload --}}
            <div class="form-group">
                <label class="control-label col-md-2">CSV File</label>
                <div class="col-md-6">
                   <input type="file" name="csv" accept=".csv,.txt" class="form-control" required>

                    <p class="text-muted" style="margin-top:8px;">
                        CSV headers must be:
                        <b>first_name,last_name,identification,external_id,loan_id,amount</b>
                    </p>
                </div>
            </div>

            {{-- Reference Fields (apply to ALL rows in the CSV) --}}
            <div class="form-group">
                <label for="date" class="control-label col-md-2">
                    {{trans_choice('general.transaction',1)}} {{trans_choice('general.date',1)}}
                </label>
                <div class="col-md-3">
                    <input type="text" name="date" class="form-control date-picker" value="{{ date('Y-m-d') }}" required id="date">
                </div>
            </div>

            <div class="form-group">
                <label for="gl_account_fund_source_id" class="control-label col-md-2">
                    Account to Credit
                    <i class="fa fa-question-circle" data-toggle="tooltip"
                       data-title="an Asset account(typically Bank or cash) that is debited during repayments/payments an credited using disbursals.."></i>
                </label>
                <div class="col-md-3">
                    <select name="gl_account_fund_source_id" class="form-control select2" id="gl_account_fund_source_id" required>
                        <option></option>
                        @foreach(\App\Models\GlAccount::where('active',1)->get() as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>

                <label for="payment_apply_to" class="control-label col-md-2">
                    {{trans_choice('general.payment',1)}} {{trans_choice('general.apply',1)}} {{trans_choice('general.to',1)}}
                </label>
                <div class="col-md-3">
                    <select name="payment_apply_to" class="form-control select2" id="payment_apply_to" required>
                        <option value="regular">{{trans_choice('general.regular',1)}}</option>
                        <option value="interest">{{trans_choice('general.interest',1)}}</option>
                        <option value="fees">{{trans_choice('general.fee',2)}}</option>
                        <option value="penalty">{{trans_choice('general.penalty',1)}}</option>
                        <option value="principal">{{trans_choice('general.principal',1)}}</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="payment_type_id" class="control-label col-md-2">
                    {{trans_choice('general.payment',1)}} {{trans_choice('general.type',1)}}
                </label>
                <div class="col-md-3">
                    <select name="payment_type_id" class="form-control select2" id="payment_type_id" required>
                        <option></option>
                        @foreach(\App\Models\PaymentType::all() as $key)
                            <option value="{{$key->id}}">{{$key->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Payment Detail fields (optional; applied to all rows) --}}
            <div class="form-group">
                <label for="account_number" class="control-label col-md-2">Account #</label>
                <div class="col-md-3">
                    <input type="text" name="account_number" class="form-control" id="account_number">
                </div>

                <label for="cheque_number" class="control-label col-md-2">Cheque #</label>
                <div class="col-md-3">
                    <input type="text" name="cheque_number" class="form-control" id="cheque_number">
                </div>
            </div>

            <div class="form-group">
                <label for="routing_code" class="control-label col-md-2">Routing Code</label>
                <div class="col-md-3">
                    <input type="text" name="routing_code" class="form-control" id="routing_code">
                </div>

                <label for="bank" class="control-label col-md-2">Bank</label>
                <div class="col-md-3">
                    <input type="text" name="bank" class="form-control" id="bank">
                </div>
            </div>

            <div class="form-group">
                <label for="notes" class="control-label col-md-2">{{trans_choice('general.note',2)}}</label>
                <div class="col-md-6">
                    <textarea name="notes" class="form-control" id="notes" rows="3"></textarea>
                </div>
            </div>

        </div>

        <div class="box-footer">
            <button type="submit" class="btn btn-primary pull-right">Upload & Reconcile</button>
        </div>
    </form>
</div>
@endsection

@section('footer-scripts')
<script>
    $("#txtConvertForm").validate();
    $("#bulkUploadForm").validate();
</script>

@endsection
