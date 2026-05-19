@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $policy->title }}</h4>
                </div>
                <div class="card-body">
                    @if($policy->description)
                        <p class="card-text">{{ $policy->description }}</p>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Category:</strong> 
                        <span class="badge bg-primary">{{ $policy->category->name }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Access Level:</strong>
                        @if($policy->access_level == 'managerial')
                            <span class="badge bg-warning">Managerial</span>
                        @else
                            <span class="badge bg-success">All Staff</span>
                        @endif
                    </div>
                    
                    <div class="mb-3">
                        <strong>File Type:</strong>
                        <span class="badge bg-info">{{ strtoupper(pathinfo($policy->file_name, PATHINFO_EXTENSION)) }}</span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>File Size:</strong>
                        <span class="text-muted">{{ round($policy->file_size / 1024, 2) }} KB</span>
                    </div>
                    
                    <div class="mb-4">
                        <a href="{{ $policy->file_url }}" target="_blank" class="btn btn-primary">
                            <i class="fa fa-download"></i> Download Policy
                        </a>
                        
                         @if($policy->accessibleByUser())
                             <a href="{{ route('policies.respond', ['policy_id' => $policy->id]) }}" class="btn btn-success ms-2">
                                 Respond to Policy
                             </a>
                         @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection