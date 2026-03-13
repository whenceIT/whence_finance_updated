@extends('layouts.master')

@section('title')
    Open New Recovery Case
@endsection

@section('content')

<div class="row">
    <div class="col-md-10 col-lg-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-folder-open"></i> New Recovery Case</h3>
            </div>

            <form method="POST" action="{{ url('recovery/case/store') }}">
                @csrf
                <div class="box-body">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin:0;padding-left:18px">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('loan_id') ? 'has-error' : '' }}">
                                <label>Loan <span class="text-danger">*</span></label>
                                <select name="loan_id" class="form-control" required>
                                    <option value="">— Select Loan —</option>
                                    @foreach($loans as $loan)
                                        <option value="{{ $loan->id }}">
                                            {{ $loan->client?->first_name . ' ' . $loan->client?->last_name ?? 'N/A' }} 
                                            - {{ $loan->loan_product?->name ?? 'N/A' }} 
                                            (K{{ number_format($loan->principal, 2) }})
                                            - {{ $loan->office?->name ?? 'N/A' }}
                                            ({{ $loan->first_installment_date ? \Carbon\Carbon::parse($loan->first_installment_date)->format('d M Y') : 'No schedule' }})
                                        </option>
                                    @endforeach
                                </select>
                                @if($errors->has('loan_id'))
                                    <span class="help-block text-danger">{{ $errors->first('loan_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('category') ? 'has-error' : '' }}">
                                <label>Recovery Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" id="category-select" required>
                                    <option value="">— Select Category —</option>
                                    <option value="cross_branch">Cross-Branch</option>
                                    <option value="escalated">Escalated</option>
                                    <option value="dormant">Dormant</option>
                                    <option value="legal">Legal</option>
                                    <option value="skip_trace">Skip Trace</option>
                                </select>
                                @if($errors->has('category'))
                                    <span class="help-block text-danger">{{ $errors->first('category') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('origin_branch_id') ? 'has-error' : '' }}">
                                <label>Origin Branch <span class="text-danger">*</span></label>
                                <select name="origin_branch_id" class="form-control" required>
                                    <option value="">— Select Branch —</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('origin_branch_id'))
                                    <span class="help-block text-danger">{{ $errors->first('origin_branch_id') }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supporting Branch</label>
                                <select name="supporting_branch_id" class="form-control">
                                    <option value="">— None —</option>
                                    @foreach($offices as $office)
                                        <option value="{{ $office->id }}">{{ $office->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group {{ $errors->has('loan_outstanding_amount') ? 'has-error' : '' }}">
                                <label>Loan Outstanding Amount (K) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-addon">K</span>
                                    <input type="number" name="loan_outstanding_amount"
                                           class="form-control" step="0.01" min="0" required
                                           value="{{ old('loan_outstanding_amount') }}">
                                </div>
                                @if($errors->has('loan_outstanding_amount'))
                                    <span class="help-block text-danger">{{ $errors->first('loan_outstanding_amount') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:14px">
                        <label>Notes <small class="text-muted">(optional)</small></label>
                        <textarea name="notes" class="form-control" rows="3"
                                  placeholder="Any additional context...">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-folder-open"></i> Open Case
                    </button>
                    <a href="{{ url('recovery/case/data') }}" class="btn btn-default" style="margin-left:6px">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
