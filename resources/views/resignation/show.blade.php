@extends('layouts.master')

@section('title', 'Resignation Details')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Resignation Details</h3>
        <div class="box-tools pull-right">
            <a href="{{ url()->previous() }}" class="btn btn-default btn-sm">Back</a>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-6">
                <strong>Employee:</strong> {{ $resignation->user->first_name }} {{ $resignation->user->last_name }}
            </div>
            <div class="col-md-6">
                <strong>Resignation Date:</strong> {{ date('d M Y', strtotime($resignation->resignation_date)) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <strong>Status:</strong>
                @if($resignation->status == 'pending')
                    <span class="label label-warning">{{ ucfirst($resignation->status) }}</span>
                @elseif($resignation->status == 'manager_approved')
                    <span class="label label-info">Manager Approved</span>
                @elseif($resignation->status == 'admin_approved')
                    <span class="label label-success">Approved</span>
                @elseif($resignation->status == 'declined')
                    <span class="label label-danger">Declined</span>
                @endif
            </div>
            <div class="col-md-6">
                <strong>Submitted Date:</strong> {{ date('d M Y H:i', strtotime($resignation->created_at)) }}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <strong>Reason:</strong>
                <p>{{ $resignation->reason }}</p>
            </div>
        </div>
        @if($resignation->letter_path)
        <div class="row">
            <div class="col-md-12">
                <strong>Resignation Letter:</strong>
                <a href="{{ Storage::url($resignation->letter_path) }}" target="_blank" class="btn btn-xs btn-primary">View Letter</a>
            </div>
        </div>
        @endif

        @if($resignation->manager)
        <hr>
        <h4>Manager Approval</h4>
        <div class="row">
            <div class="col-md-6">
                <strong>Manager:</strong> {{ $resignation->manager->first_name }} {{ $resignation->manager->last_name }}
            </div>
            <div class="col-md-6">
                <strong>Approved Date:</strong> {{ $resignation->manager_approved_at ? date('d M Y H:i', strtotime($resignation->manager_approved_at)) : 'N/A' }}
            </div>
        </div>
        @if($resignation->manager_comment)
        <div class="row">
            <div class="col-md-12">
                <strong>Comment:</strong>
                <p>{{ $resignation->manager_comment }}</p>
            </div>
        </div>
        @endif
        @endif

        @if($resignation->admin)
        <hr>
        <h4>Admin Approval</h4>
        <div class="row">
            <div class="col-md-6">
                <strong>Admin:</strong> {{ $resignation->admin->first_name }} {{ $resignation->admin->last_name }}
            </div>
            <div class="col-md-6">
                <strong>Approved Date:</strong> {{ $resignation->admin_approved_at ? date('d M Y H:i', strtotime($resignation->admin_approved_at)) : 'N/A' }}
            </div>
        </div>
        @if($resignation->admin_comment)
        <div class="row">
            <div class="col-md-12">
                <strong>Comment:</strong>
                <p>{{ $resignation->admin_comment }}</p>
            </div>
        </div>
        @endif
        @endif
    </div>
</div>
@endsection