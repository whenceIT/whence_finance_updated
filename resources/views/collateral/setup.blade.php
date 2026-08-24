@extends('layouts.master')
@section('title', 'Collateral Workflow Setup')
@section('content')
<?php
    $userInfo = \App\Helpers\GeneralHelper::get_user_info();
    $role = $userInfo->role;
?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Collateral Workflow Setup</h3>
        <div class="box-tools pull-right">
            <a href="{{ route('collateral.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Back to Collateral
            </a>
        </div>
    </div>
    <div class="box-body">
        <p class="text-muted">Set the users responsible for collateral workflow permissions. Only active staff will appear in the dropdowns.</p>

        <form method="post" action="{{ route('collateral.setup.update') }}" class="form-horizontal">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="supervisor_id" class="col-sm-3 control-label">Collateral Supervisor <span style="color: #c0392b;">*</span></label>
                <div class="col-sm-6">
                    <select name="supervisor_id" id="supervisor_id" class="form-control select2" required>
                        <option value="">Select a staff member</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"{{ ($supervisorId ?? null) == $user->id ? ' selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }} — {{ $user->position->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help-text">User who approves seizures and manages the collateral workflow.</small>
                </div>
            </div>

            <div class="form-group">
                <label for="valuator_id" class="col-sm-3 control-label">Collateral Valuator <span style="color: #c0392b;">*</span></label>
                <div class="col-sm-6">
                    <select name="valuator_id" id="valuator_id" class="form-control select2" required>
                        <option value="">Select a staff member</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}"{{ ($valuatorId ?? null) == $user->id ? ' selected' : '' }}>
                                {{ $user->first_name }} {{ $user->last_name }} — {{ $user->position->name ?? 'N/A' }}
                            </option>
                        @endforeach
                    </select>
                    <small class="form-help-text">User responsible for conducting valuations on seized inventory.</small>
                </div>
            </div>

            <div class="form-group">
                <div class="col-sm-offset-3 col-sm-6">
                    <button type="submit" class="btn btn-primary">Save Setup</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
