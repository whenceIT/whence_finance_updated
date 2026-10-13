@extends('layouts.master')

@section('title')
    Edit Recovery Cost
@endsection

@section('content')

<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-warning">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-pencil"></i> Edit Recovery Cost</h3>
            </div>

            <form action="{{ url('recovery/costs/' . $cost->id . '/update') }}" method="POST">
                {{ csrf_field() }}

                <div class="box-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Name --}}
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                        <label for="name" class="control-label">Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name"
                               class="form-control" value="{{ old('name', $cost->name) }}">
                        @if($errors->has('name'))
                            <span class="help-block">{{ $errors->first('name') }}</span>
                        @endif
                    </div>

                    {{-- Description --}}
                    <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                        <label for="description" class="control-label">Description</label>
                        <textarea id="description" name="description"
                                  class="form-control" rows="3">{{ old('description', $cost->description) }}</textarea>
                        @if($errors->has('description'))
                            <span class="help-block">{{ $errors->first('description') }}</span>
                        @endif
                    </div>

                    <div class="row">
                        {{-- Amount --}}
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                                <label for="amount" class="control-label">Amount (K) <span class="text-danger">*</span></label>
                                <input type="number" id="amount" name="amount"
                                       class="form-control" value="{{ old('amount', $cost->amount) }}"
                                       step="0.01" min="0.01">
                                @if($errors->has('amount'))
                                    <span class="help-block">{{ $errors->first('amount') }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="col-md-6">
                            <div class="form-group {{ $errors->has('cost_date') ? 'has-error' : '' }}">
                                <label for="cost_date" class="control-label">Cost Date <span class="text-danger">*</span></label>
                                <input type="date" id="cost_date" name="cost_date"
                                       class="form-control"
                                       value="{{ old('cost_date', optional($cost->cost_date)->format('Y-m-d')) }}"
                                       max="{{ date('Y-m-d') }}">
                                @if($errors->has('cost_date'))
                                    <span class="help-block">{{ $errors->first('cost_date') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Case --}}
                    <div class="form-group {{ $errors->has('case_id') ? 'has-error' : '' }}">
                        <label for="case_id" class="control-label">Recovery Case <small class="text-muted">(optional)</small></label>
                        <select id="case_id" name="case_id" class="form-control select2" style="width:100%">
                            <option value="">— No specific case —</option>
                            @foreach($cases as $case)
                                <option value="{{ $case->id }}"
                                    {{ old('case_id', $cost->case_id) == $case->id ? 'selected' : '' }}>
                                    {{ $case->case_number }}
                                    @if($case->client)
                                        – {{ $case->client->first_name }} {{ $case->client->last_name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('case_id'))
                            <span class="help-block">{{ $errors->first('case_id') }}</span>
                        @endif
                    </div>

                    {{-- Office --}}
                    <div class="form-group {{ $errors->has('office_id') ? 'has-error' : '' }}">
                        <label for="office_id" class="control-label">Office / Department <span class="text-danger">*</span></label>
                        <select id="office_id" name="office_id" class="form-control select2" style="width:100%">
                            <option value="">— Select office —</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}"
                                    {{ old('office_id', $cost->office_id) == $office->id ? 'selected' : '' }}>
                                    {{ $office->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('office_id'))
                            <span class="help-block">{{ $errors->first('office_id') }}</span>
                        @endif
                    </div>

                </div>{{-- /.box-body --}}

                <div class="box-footer">
                    <button type="submit" class="btn btn-warning">
                        <i class="fa fa-save"></i> Update Cost
                    </button>
                    <a href="{{ url('recovery/costs/' . $cost->id . '/show') }}"
                       class="btn btn-default" style="margin-left:8px">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
