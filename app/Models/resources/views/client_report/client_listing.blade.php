@extends('layouts.master')
@section('title')
    {{trans_choice('general.client',1)}} {{trans_choice('general.report',1)}} - Listing
@endsection

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">{{trans_choice('general.client',1)}} {{trans_choice('general.report',1)}} - Listing</h3>
        <div class="box-tools pull-right"></div>
    </div>

    <div class="box-body hidden-print">
        <form method="get" action="{{ Request::url() }}" class="form-horizontal">
            <div class="form-group">
                <label class="control-label col-md-2">{{trans_choice('general.office',1)}}</label>
                <div class="col-md-3">
                    <select name="office_id" class="form-control select2">
                        <option value="0" @if(($office_id ?? '0')=='0') selected @endif>{{trans_choice('general.all',1)}}</option>
                        @foreach(\App\Models\Office::all() as $o)
                            <option value="{{$o->id}}" @if(($office_id ?? '0')==$o->id) selected @endif>{{$o->name}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <button type="submit" class="btn btn-success">{{trans_choice('general.search',1)}}!</button>
                    <a href="{{ Request::url() }}" class="btn btn-danger">{{trans_choice('general.reset',1)}}!</a>

                    <a class="btn btn-primary"
                       href="{{ url('report/client_report/client_listing/csv?office_id=' . ($office_id ?? 0)) }}"
                       target="_blank">
                        <i class="icon-file-excel"></i> {{trans_choice('general.download',1)}} {{trans_choice('general.excel',1)}}
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="box-body table-responsive">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>{{trans_choice('general.name',1)}}</th>
                <th>{{trans_choice('general.office',1)}}</th>
                <th>{{trans_choice('general.staff',1)}}</th>
                <th>{{trans_choice('general.phone',1)}}</th>

                <th>ID</th>
                <th>Bank Acc Name</th>

                <th>Next of Kin</th>
                <th>NOK Phone</th>
                <th>NOK Relationship</th>
            </tr>
            </thead>
            <tbody>
            @foreach($clients as $c)
                @php
                    $id = $c->identifications->first(); // latest because ordered desc
                    $nok = $c->next_of_kin->first();    // first because ordered asc
                @endphp
                <tr>
                    <td>
                        @if($c->client_type=="individual")
                            {{$c->first_name}} {{$c->middle_name}} {{$c->last_name}}
                        @else
                            {{$c->full_name}}
                        @endif
                    </td>
                    <td>{{ $c->office->name ?? '' }}</td>
                    <td>{{ ($c->staff->first_name ?? '') . ' ' . ($c->staff->last_name ?? '') }}</td>
                    <td>{{ $c->mobile ?? '' }}</td>

                    <td>{{ $id->name ?? '' }}</td>
                    <td>{{ $c->bank_account_number ?? '' }}</td>

                    <td>{{ ($nok->first_name ?? '') . ' ' . ($nok->last_name ?? '') }}</td>
                    <td>{{ $nok->phone ?? '' }}</td>
                    @php
    $rel = $nok->relationship ?? null;

    // If relationship is JSON string, decode it
    if (is_string($rel)) {
        $decoded = json_decode($rel, true);
        $relName = is_array($decoded) ? ($decoded['name'] ?? $rel) : $rel;
    }
    // If relationship is already an array/object
    elseif (is_array($rel)) {
        $relName = $rel['name'] ?? '';
    } elseif (is_object($rel)) {
        $relName = $rel->name ?? '';
    } else {
        $relName = '';
    }
@endphp

<td>{{ $relName }}</td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
