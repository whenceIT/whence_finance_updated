<div class="container-fluid">
    <div style="background: linear-gradient(135deg, #000041 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <div class="row">
            <div class="col-md-6" style="font-size:1.8em">
                <strong>Employee:</strong> {{ $resignation->user->first_name }} {{ $resignation->user->last_name }}
            </div>
            <div class="col-md-6" style="font-size:1.5em">
                <strong>Resignation Date:</strong> {{ date('d M Y', strtotime($resignation->resignation_date)) }}
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <strong>Status:</strong>
                @if($resignation->status == 'pending')
                    <span style="background: #ffc107; color: #212529; padding: 4px 8px; border-radius: 4px; font-size: 0.875em;">{{ ucfirst($resignation->status) }}</span>
                @elseif($resignation->status == 'manager_approved')
                    <span style="background: #17a2b8; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.875em;">Manager Approved</span>
                @elseif($resignation->status == 'admin_approved')
                    <span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.875em;">Approved</span>
                @elseif($resignation->status == 'declined')
                    <span style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.875em;">Declined</span>
                @endif
            </div>
            <div class="col-md-6">
                <strong>Submitted Date:</strong> {{ date('d M Y H:i', strtotime($resignation->created_at)) }}
            </div>
        </div>
    </div>
    <div class="row mt-6" style="margin-left:0.5%">
        <div class="col-12">
            <strong>Reason:</strong>
            <p class="mt-1">{{ $resignation->reason }}</p>
        </div>
    </div>
    <hr>
    @if($resignation->letter_path)
    <div class="row mt-3" style="margin-left:0.5%">
        <div class="col-12">
            <strong>Resignation Letter:</strong>
            <a href="{{ Storage::url($resignation->letter_path) }}" target="_blank" class="btn btn-sm btn-primary ml-2"><i class="fa fa-eye"></i> View</a>
        </div>
    </div>
    @endif
    @if($resignation->manager)
    <hr>
    <h5>Manager Review</h5>
    <div class="row">
        <div class="col-md-6">
            <strong>Reviewed by:</strong> {{ $resignation->manager->first_name }} {{ $resignation->manager->last_name }}
        </div>
        <div class="col-md-6">
            <strong>Action:</strong> {{ $resignation->status == 'declined' && $resignation->admin_id == null ? 'Declined' : 'Approved' }}
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6">
            <strong>Reviewed Date:</strong> {{ $resignation->manager_approved_at ? date('d M Y H:i', strtotime($resignation->manager_approved_at)) : 'N/A' }}
        </div>
    </div>
    @if($resignation->manager_comment)
    <div class="row mt-2">
        <div class="col-12">
            <strong>Comment:</strong>
            <p class="mt-1">{{ $resignation->manager_comment }}</p>
        </div>
    </div>
    @endif
    @endif
    @if($resignation->admin)
    <hr>
    <h5>Admin Review</h5>
    <div class="row">
        <div class="col-md-6">
            <strong>Reviewed by:</strong> {{ $resignation->admin->first_name }} {{ $resignation->admin->last_name }}
        </div>
        <div class="col-md-6">
            <strong>Action:</strong> {{ $resignation->status == 'declined' ? 'Declined' : 'Approved' }}
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-md-6">
            <strong>Reviewed Date:</strong> {{ $resignation->admin_approved_at ? date('d M Y H:i', strtotime($resignation->admin_approved_at)) : 'N/A' }}
        </div>
    </div>
    @if($resignation->admin_comment)
    <div class="row mt-2">
        <div class="col-12">
            <strong>Comment:</strong>
            <p class="mt-1">{{ $resignation->admin_comment }}</p>
        </div>
    </div>
    @endif
    @endif
</div>