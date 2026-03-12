@extends('layouts.master')

@section('title')
    Recovery Specialists
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-users"></i> Recovery Specialists</h3>
    </div>
    <div class="box-body no-padding">
        <table class="table table-hover table-striped" style="margin-bottom:0">
            <thead>
                <tr>
                    <th>Specialist</th>
                    <th>Recovered</th>
                    <th>Active</th>
                    <th>Resolved</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($specialists as $row)
            <tr>
                <td>
                    <strong>{{ trim(($row['specialist']->first_name ?? '') . ' ' . ($row['specialist']->last_name ?? '')) ?: $row['specialist']->email }}</strong>
                </td>
                <td>K {{ number_format($row['total_recovered'], 0) }}</td>
                <td>{{ $row['active_cases'] }}</td>
                <td>{{ $row['resolved_cases'] }}</td>
                <td>
                    <a href="{{ url('recovery/specialist/' . $row['specialist']->id . '/show') }}"
                       class="btn btn-xs btn-default">
                        <i class="fa fa-eye"></i> View
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center text-muted" style="padding:48px">
                    No specialist data.
                </td>
            </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
