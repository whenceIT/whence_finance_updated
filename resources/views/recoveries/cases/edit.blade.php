@extends('layouts.master')

@section('title')
    Edit Case — {{ $case->case_number }}
@endsection

@section('content')

@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="row">
    <div class="col-md-10 col-lg-8">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-pencil"></i> Edit Case
                    <span class="label label-default" style="margin-left:8px">{{ $case->case_number }}</span>
                </h3>
            </div>

            <form method="POST" action="{{ url('recovery/case/' . $case->id . '/update') }}">
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
                            <div class="form-group">
                                <label>Category <span class="text-danger">*</span></label>
                                <select name="category" class="form-control" required>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ $case->category === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="escalated_handover" {{ $case->status === 'escalated_handover' ? 'selected' : '' }}>Handover</option>
                                    <option value="escalated_in_review" {{ $case->status === 'escalated_in_review' ? 'selected' : '' }}>In Review</option>
                                    <option value="closed" {{ $case->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Outstanding Amount (K)</label>
                                <input type="number" name="loan_outstanding_amount"
                                       class="form-control" step="0.01" min="0"
                                       value="{{ old('loan_outstanding_amount', $case->loan_outstanding_amount) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Assigned Specialist</label>
                                <select name="assigned_specialist_id" class="form-control">
                                    <option value="">— Unassigned —</option>
                                    @foreach($specialists as $specialist)
                                        <option value="{{ $specialist->id }}" {{ $case->assigned_specialist_id == $specialist->id ? 'selected' : '' }}>
                                            {{ trim(($specialist->first_name ?? '') . ' ' . ($specialist->last_name ?? '')) ?: $specialist->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Target Resolution Date</label>
                                <input type="date" name="target_resolution_date" class="form-control"
                                       value="{{ old('target_resolution_date', $case->target_resolution_date?->format('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:14px">
                        <label>Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $case->notes) }}</textarea>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Save Changes
                    </button>
                    <a href="{{ url('recovery/case/' . $case->id . '/show') }}" class="btn btn-default" style="margin-left:6px">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
