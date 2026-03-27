@extends('layouts.master')

@section('title')
    Recovery Specialists
@endsection

@section('content')
@php $categories = \App\Models\RecoveryCase::CATEGORIES; @endphp

<div class="box box-default">
    <div class="box-header with-border">
        <h3 class="box-title"><i class="fa fa-users"></i> Recovery Specialists</h3>
        <div class="box-tools pull-right">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addSpecialistModal">
                <i class="fa fa-plus"></i> Add Specialist
            </button>
        </div>
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

<!-- Add Specialist Modal -->
<div class="modal fade" id="addSpecialistModal" tabindex="-1" role="dialog" aria-labelledby="addSpecialistModalLabel">
    <div class="modal-dialog" role="document">
        <form action="{{ url('recovery/specialist/store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="addSpecialistModalLabel">Assign Specialist</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="user_id">Select User</label>
                        <select name="user_id" id="user_id" class="form-control select2" style="width: 100%;" required>
                            <option value="">Search and select a user...</option>
                            @foreach(App\User::orderBy('first_name')->orderBy('last_name')->get() as $user)
                                <option value="{{ $user->id }}">
                                    {{ trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes (Optional)</label>
                        <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add any notes about this specialist..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Specialist</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection
