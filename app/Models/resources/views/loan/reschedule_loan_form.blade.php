@extends('layouts.master')

@section('title')
    {{ trans_choice('general.loan', 1) }} Extension
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Extend {{ trans_choice('general.loan', 1) }}</h3>
    </div>

    <form method="post" action="{{ url('loan/'.$loan->id.'/reschedule_loan') }}" class="form-horizontal">
        {{ csrf_field() }}

        <div class="box-body">

            <div class="form-group">
                <label for="rescheduled_from_date" class="col-md-3 control-label">
                    From Which {{ trans_choice('general.installment', 1) }}?
                </label>
                <div class="col-md-6">
                    <input type="text" name="rescheduled_from_date" class="form-control date-picker"
                           value="{{ date('Y-m-d') }}" required id="rescheduled_from_date">
                </div>
            </div>

            <div class="form-group">
                <label for="rescheduled_on_date" class="col-md-3 control-label">
                    {{ trans_choice('general.submitted', 1) }} On
                </label>
                <div class="col-md-6">
                    <input type="text" name="submitte_on_date" class="form-control date-picker"
                           value="{{ date('Y-m-d') }}" required id="rescheduled_on_date">
                </div>
            </div>
            <div class="form-group">
    <label for="balance" class="col-md-3 control-label">
        {{ trans_choice('general.outstanding', 1) }} {{ trans_choice('general.principal', 1) }}
    </label>
    <div class="col-md-6">
        <input type="text" name="balance" id="balance" class="form-control"
               value="{{ number_format($balance, 2, '.', '') }}" readonly required>
    </div>
</div>


            <div class="form-group">
                <label for="interest_rate" class="col-md-3 control-label">
                    {{ trans_choice('general.interest', 1) }} {{ trans_choice('general.rate', 1) }} (%)
                </label>
                <div class="col-md-6">
                    <input type="number" step="0.01" name="interest_rate" id="interest_rate"
                           class="form-control"
                           value="{{ number_format($loan->interest_rate, 4, '.', '') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label for="interest" class="col-md-3 control-label">
                    Adjusted {{ trans_choice('general.interest', 1) }}
                </label>
                <div class="col-md-6">
                    <input type="text" name="interest" id="interest" class="form-control" value="" readonly required>
                </div>
            </div>

            <div class="form-group">
                <label for="next_repayment_date" class="col-md-3 control-label">
                    {{ trans_choice('general.next', 1) }} {{ trans_choice('general.repayment', 1) }}
                </label>
                <div class="col-md-6">
                    <input type="text" name="next_repayment" class="form-control date-picker"
                           value="{{ date('Y-m-d') }}" required id="next_repayment_date">
                </div>
            </div>

            <div class="form-group">
                <label for="rescheduled_notes" class="col-md-3 control-label">
                    {{ trans_choice('general.note', 2) }}
                </label>
                <div class="col-md-6">
                    <textarea name="rescheduled_notes" id="rescheduled_notes" class="form-control" rows="3" required></textarea>
                </div>
            </div>

        </div>

        <div class="box-footer">
            <a href="{{ url()->previous() }}" class="btn btn-default">{{ trans_choice('general.cancel', 1) }}</a>
            <button type="submit" class="btn btn-primary pull-right">{{ trans_choice('general.save', 1) }}</button>
        </div>
    </form>
</div>
@endsection
@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
@section('footer-scripts')
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function () {
        $('.date-picker').daterangepicker({
            singleDatePicker: true,
            locale: { format: 'YYYY-MM-DD' }
        });

        function updateAdjustedInterest() {
            let balance = parseFloat($('#balance').val()) || 0;
            let rate = parseFloat($('#interest_rate').val()) || 0;
            let adjusted = balance * (rate / 100);
            $('#interest').val(adjusted.toFixed(2));
        }

        // Run it on input change
        $('#interest_rate').on('input', updateAdjustedInterest);

        // Run it immediately on page load
        updateAdjustedInterest();
    });
</script>
@endsection

@endsection
