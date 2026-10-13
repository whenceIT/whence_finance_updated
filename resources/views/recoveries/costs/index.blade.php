@extends('layouts.master')

@section('title')
    Recovery Costs
@endsection

@section('content')

{{-- Summary card --}}
<div class="row" style="margin-bottom:16px">
    <div class="col-md-4">
        <div class="info-box">
            <span class="info-box-icon bg-red"><i class="fa fa-money"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Costs</span>
                <span class="info-box-number">K {{ number_format($totalAmount, 2) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-8 text-right" style="padding-top:10px">
        <a href="{{ url('recovery/costs/create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Record New Cost
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-filter"></i> Filters</h3>
        <div class="box-tools">
            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="box-body">
        <form method="GET" action="{{ url('recovery/costs') }}" style="display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end">
            <div style="flex:2;min-width:180px">
                <label class="control-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       class="form-control" placeholder="Name or description…">
            </div>
            <div style="flex:1;min-width:160px">
                <label class="control-label">Office</label>
                <select name="office_id" class="form-control select2" style="width:100%">
                    <option value="">All Offices</option>
                    @foreach($offices as $office)
                        <option value="{{ $office->id }}" {{ request('office_id') == $office->id ? 'selected' : '' }}>
                            {{ $office->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:160px">
                <label class="control-label">Case</label>
                <select name="case_id" class="form-control select2" style="width:100%">
                    <option value="">All Cases</option>
                    @foreach($cases as $case)
                        <option value="{{ $case->id }}" {{ request('case_id') == $case->id ? 'selected' : '' }}>
                            {{ $case->case_number }} – {{ optional($case->client)->first_name }} {{ optional($case->client)->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1;min-width:130px">
                <label class="control-label">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
            </div>
            <div style="flex:1;min-width:130px">
                <label class="control-label">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
            </div>
            <div style="display:flex;gap:6px;align-items:flex-end">
                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i> Filter</button>
                <a href="{{ url('recovery/costs') }}" class="btn btn-default">Clear</a>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">
            <i class="fa fa-list"></i> Recovery Costs
            <span class="label label-default" style="margin-left:6px">{{ $costs->total() }}</span>
        </h3>
    </div>
    <div class="box-body no-padding">
        @if($costs->isEmpty())
            <div class="callout callout-info" style="margin:16px">
                <p>No recovery costs found. <a href="{{ url('recovery/costs/create') }}">Record the first one.</a></p>
            </div>
        @else
            <table class="table table-hover table-striped table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Office</th>
                        <th>Case</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Recorded By</th>
                        <th style="width:110px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($costs as $cost)
                        <tr>
                            <td>{{ $costs->firstItem() + $loop->index }}</td>
                            <td>
                                <a href="{{ url('recovery/costs/' . $cost->id . '/show') }}">
                                    {{ $cost->name }}
                                </a>
                                @if($cost->description)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($cost->description, 60) }}</small>
                                @endif
                            </td>
                            <td>{{ optional($cost->office)->name ?? '—' }}</td>
                            <td>
                                @if($cost->recoveryCase)
                                    <a href="{{ url('recovery/case/' . $cost->case_id . '/show') }}" target="_blank">
                                        {{ $cost->recoveryCase->case_number }}
                                    </a>
                                    <br><small class="text-muted">
                                        {{ optional($cost->recoveryCase->client)->first_name }}
                                        {{ optional($cost->recoveryCase->client)->last_name }}
                                    </small>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ $cost->cost_date ? $cost->cost_date->format('d M Y') : '—' }}</td>
                            <td><strong>K {{ number_format($cost->amount, 2) }}</strong></td>
                            <td>
                                {{ optional($cost->recordedBy)->first_name }}
                                {{ optional($cost->recordedBy)->last_name }}
                            </td>
                            <td>
                                <a href="{{ url('recovery/costs/' . $cost->id . '/show') }}"
                                   class="btn btn-xs btn-info" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ url('recovery/costs/' . $cost->id . '/edit') }}"
                                   class="btn btn-xs btn-warning" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ url('recovery/costs/' . $cost->id . '/delete') }}"
                                      method="POST" style="display:inline"
                                      onsubmit="return confirm('Delete this cost record?')">
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn btn-xs btn-danger" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    @if($costs->hasPages())
        <div class="box-footer clearfix">
            {{ $costs->links() }}
        </div>
    @endif
</div>

@endsection
