@extends('layouts.master')

@section('title', 'My Resignations')

@section('content')
<div class="box box-primary" style="border-radius: 10px;">
    <div class="box-header with-border" style="padding: 20px;">
        <h3 class="box-title" style="font-weight: 600; font-size: 20px;">My Resignations</h3>

        <div class="box-tools pull-right">
            @if($existing)
                <a href="{{ route('resignation.create') }}" class="btn btn-warning btn-sm" style="border-radius: 6px; padding: 6px 14px;">Update Current</a>
            @else
                <a href="{{ route('resignation.create') }}" class="btn btn-primary btn-sm" style="border-radius: 6px; padding: 6px 14px;">Submit New</a>
            @endif
        </div>
    </div>

    <div class="box-body" style="padding: 20px;">
        @forelse($resignations as $resignation)
        <div class="row" style="margin-bottom: 25px;">
            <div class="col-md-12">

                <!-- Card -->
                <div class="card" style="border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.08); border-radius: 12px; overflow: hidden;">

                    <!-- Header -->
                    <div class="card-header"
                         @if($resignation->status == 'pending')
                             style="background: linear-gradient(135deg, #f1c40f, #e67e22); color:white; padding:16px;"
                         @elseif($resignation->status == 'manager_approved')
                             style="background: linear-gradient(135deg, #3498db, #2980b9); color:white; padding:16px;"
                         @elseif($resignation->status == 'admin_approved')
                             style="background: linear-gradient(135deg, #2ecc71, #27ae60); color:white; padding:16px;"
                         @elseif($resignation->status == 'declined')
                             style="background: linear-gradient(135deg, #e74c3c, #c0392b); color:white; padding:16px;"
                         @endif>

                        <div class="row">
                            <div class="col-md-8">
                                <h4 style="margin:0; font-size: 18px; font-weight:600;">
                                    @if($resignation->status == 'pending')
                                        <i class="fa fa-clock-o"></i> Pending Review
                                    @elseif($resignation->status == 'manager_approved')
                                        <i class="fa fa-check-circle"></i> Manager Approved
                                    @elseif($resignation->status == 'admin_approved')
                                        <i class="fa fa-check-circle"></i> Fully Approved
                                    @elseif($resignation->status == 'declined')
                                        <i class="fa fa-times-circle"></i> Declined
                                    @endif
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <small style="font-size: 13px;">Current Status</small>
                            </div>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="card-body" style="padding: 20px;">

                        <div class="row">

                            <!-- Submission Date -->
                            <div class="col-md-6" style="margin-bottom:15px;">
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-calendar fa-2x" style="color:#2980b9;"></i>
                                    </div>
                                    <div class="media-body" style="padding-left:10px;">
                                        <h5 class="media-heading" style="font-weight:600; margin-bottom:4px;">Submission Date</h5>
                                        <p style="margin:0;">{{ date('l, d F Y', strtotime($resignation->created_at)) }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Resignation Effective Date -->
                            <div class="col-md-6" style="margin-bottom:15px;">
                                <div class="media">
                                    <div class="media-left">
                                        <i class="fa fa-sign-out fa-2x" style="color:#c0392b;"></i>
                                    </div>
                                    <div class="media-body" style="padding-left:10px;">
                                        <h5 class="media-heading" style="font-weight:600; margin-bottom:4px;">Resignation Date</h5>
                                        <p style="margin:0;">{{ date('l, d F Y', strtotime($resignation->resignation_date)) }}</p>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <hr style="margin-top:10px; margin-bottom:20px;">

                        <div class="row">
                            <div class="col-md-12 text-center">

                                <!-- View Button -->
                                <button class="btn btn-primary btn-lg view-resignation"
                                        data-id="{{ $resignation->id }}"
                                        style="border-radius: 30px; padding: 10px 35px; font-size: 15px;">
                                    <i class="fa fa-eye"></i> View Full Details
                                </button>

                                <!-- Cancel Button -->
                                @if(in_array($resignation->status, ['pending', 'manager_approved']))
                                    <button type="button" class="btn btn-outline-danger btn-lg cancel-resignation-btn"
                                            data-toggle="modal" data-target="#cancelModal"
                                            data-id="{{ $resignation->id }}"
                                            style="border-radius: 30px; padding: 10px 35px; margin-left:10px; font-size: 15px;">
                                        <i class="fa fa-times"></i> Cancel Resignation
                                    </button>
                                @endif

                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
        @empty

        <!-- Empty State -->
        <div class="row">
            <div class="col-md-12">
                <div class="card text-center" style="border:none; box-shadow:0 4px 15px rgba(0,0,0,0.08); border-radius:12px; padding:35px;">
                    <i class="fa fa-info-circle fa-3x text-info mb-3"></i>
                    <h4 style="font-weight:600; margin-top:10px;">No Resignations Found</h4>
                    <p style="font-size:15px;">You haven’t submitted any resignation requests yet.</p>
                </div>
            </div>
        </div>

        @endforelse
    </div>
</div>

@include('resignation.partials.view_modal')

<!-- Cancel Confirmation Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 10px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #e74c3c, #c0392b); color: white;">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fa fa-exclamation-triangle"></i> Confirm Cancellation
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to cancel this resignation? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No, Keep It</button>
                <a href="#" id="confirmCancelBtn" class="btn btn-danger">Yes, Cancel Resignation</a>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.view-resignation').on('click', function() {
        var id = $(this).data('id');
        $.ajax({
            url: '{{ url("resignation/show") }}/' + id,
            type: 'GET',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                $('#resignationModalBody').html(response.html);
                $('#viewResignationModal').modal('show');
            },
            error: function() {
                alert('Error loading resignation details.');
            }
        });
    });

    $('.cancel-resignation-btn').on('click', function() {
        var id = $(this).data('id');
        var cancelUrl = '{{ url("resignation/cancel") }}/' + id;
        $('#confirmCancelBtn').attr('href', cancelUrl);
    });
});
</script>
@endsection
