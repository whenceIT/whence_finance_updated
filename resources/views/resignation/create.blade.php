@extends('layouts.master')

@section('title', 'Resignation Letter')

@section('content')
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Submit Resignation Letter</h3>
    </div>
    <div class="box-body">
        @if($existing)
        <div class="alert alert-warning">
            <strong>Notice:</strong> You already have a pending resignation letter submitted on {{ date('d M Y', strtotime($existing->created_at)) }}.
            <br>
            <strong>Options:</strong>
            <br>
            <a href="#" onclick="document.getElementById('resignationForm').style.display='block';" class="btn btn-primary btn-sm">Resubmit (Update Current)</a>
            <a href="{{ route('resignation.cancel', $existing->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel your pending resignation?')">Cancel Pending Letter</a>
        </div>
        <div id="resignationForm" style="display: none;">
        @endif
        <form method="post" action="{{ route('resignation.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="resignation_date">Resignation Date</label>
                        <input type="date" class="form-control" id="resignation_date" name="resignation_date" required min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <textarea class="form-control" id="reason" name="reason" rows="5" required placeholder="Please provide the reason for your resignation"></textarea>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="letter">Upload Resignation Letter</label>
                        <input type="file" class="form-control" id="letter" name="letter" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                        <small class="form-text text-muted">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG. Max size: 2MB</small>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" id="confirm">
                                I confirm that I want to submit this resignation letter and understand that this action cannot be undone.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Submit</button>
                    <a href="{{ url('dashboard') }}" class="btn btn-default">Cancel</a>
                </div>
            </div>
        </form>
        @if($existing)
        </div>
        @endif
    </div>
</div>

<!-- <script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmCheckbox = document.getElementById('confirm');
    const submitBtn = document.getElementById('submit-btn');

    confirmCheckbox.addEventListener('change', function() {
        submitBtn.disabled = !this.checked;
    });
});
</script> -->
@endsection