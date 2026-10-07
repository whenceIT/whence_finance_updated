@extends('layouts.master')

@section('title')
    Record Repayment &mdash; RTI Loan #{{ $loan->id }}
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-7 col-md-offset-2">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-credit-card"></i>
                        Record Repayment &mdash; RTI Loan #{{ $loan->id }}
                    </h4>
                    <div class="heading-elements">
                        <a href="{{ route('rti.loans.show', $loan->id) }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back to Loan
                        </a>
                    </div>
                </div>

                <div class="panel-body">

                    {{-- Loan summary strip --}}
                    <div class="well" style="background:#f9f9f9; margin-bottom:20px;">
                        <div class="row text-center">
                            <div class="col-xs-4">
                                <div style="font-size:11px;color:#888;text-transform:uppercase;">Total Payable</div>
                                <div style="font-size:18px;font-weight:700;">K{{ number_format($loan->total_payable, 2) }}</div>
                            </div>
                            <div class="col-xs-4">
                                <div style="font-size:11px;color:#888;text-transform:uppercase;">Total Paid</div>
                                <div style="font-size:18px;font-weight:700;color:#5cb85c;">K{{ number_format($loan->total_paid, 2) }}</div>
                            </div>
                            <div class="col-xs-4">
                                <div style="font-size:11px;color:#888;text-transform:uppercase;">Outstanding</div>
                                <div style="font-size:18px;font-weight:700;color:#d9534f;">K{{ number_format($loan->outstanding_balance, 2) }}</div>
                            </div>
                        </div>
                        <hr style="margin:10px 0;">
                        <div class="row text-center">
                            <div class="col-xs-6">
                                <div style="font-size:11px;color:#888;">Branch</div>
                                <div style="font-weight:600;">{{ optional($loan->office)->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col-xs-6">
                                <div style="font-size:11px;color:#888;">Status</div>
                                <span class="label {{ $loan->status_badge_class }}">{{ $loan->status_label }}</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('rti.repayment.store', $loan->id) }}">
                        {{ csrf_field() }}

                        {{-- Amount --}}
                        <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                            <label for="amount" class="control-label">
                                Repayment Amount (K) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="amount"
                                   id="amount"
                                   class="form-control"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0.01"
                                   max="{{ $loan->outstanding_balance }}"
                                   value="{{ old('amount') }}"
                                   required>
                            <span class="help-block">
                                Maximum: K{{ number_format($loan->outstanding_balance, 2) }}
                                (outstanding balance)
                            </span>
                            @if($errors->has('amount'))
                                <span class="help-block text-danger">{{ $errors->first('amount') }}</span>
                            @endif
                        </div>

                        {{-- Quick-fill buttons --}}
                        <div class="form-group">
                            <label class="control-label">Quick Fill</label><br>
                            <button type="button" class="btn btn-xs btn-default"
                                    onclick="document.getElementById('amount').value='{{ $loan->outstanding_balance }}'">
                                Full Balance (K{{ number_format($loan->outstanding_balance, 2) }})
                            </button>
                            <button type="button" class="btn btn-xs btn-default"
                                    onclick="document.getElementById('amount').value='{{ round($loan->outstanding_balance / 2, 2) }}'">
                                50% (K{{ number_format($loan->outstanding_balance / 2, 2) }})
                            </button>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group {{ $errors->has('notes') ? 'has-error' : '' }}">
                            <label for="notes" class="control-label">Notes (optional)</label>
                            <textarea name="notes"
                                      id="notes"
                                      class="form-control"
                                      rows="3"
                                      placeholder="e.g. Bank deposit ref, cheque number…">{{ old('notes') }}</textarea>
                            @if($errors->has('notes'))
                                <span class="help-block">{{ $errors->first('notes') }}</span>
                            @endif
                        </div>

                        <div class="callout callout-warning" style="margin-bottom:15px;">
                            <i class="fa fa-info-circle"></i>
                            This repayment will be saved as <strong>Pending</strong> until
                            an authorised user approves it.
                            Only approved repayments reduce the outstanding balance.
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary"
                                    onclick="return confirm('Submit repayment for approval?')">
                                <i class="fa fa-save"></i> Submit Repayment
                            </button>
                            <a href="{{ route('rti.loans.show', $loan->id) }}" class="btn btn-default">
                                Cancel
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
