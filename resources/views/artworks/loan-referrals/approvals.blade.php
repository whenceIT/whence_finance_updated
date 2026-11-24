@extends('layouts.master')
@section('title', 'Loan Referral Approvals')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border" style="background: linear-gradient(135deg, #155724, #28a745); color: #fff; padding: 15px 20px;">
            <h3 class="box-title">Pending Loan Referral Approvals - referred by Creative Artworks</h3>
        </div>
        <div class="box-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->has('selected_user_id'))
                <div class="alert alert-danger">{{ $errors->first('selected_user_id') }}</div>
            @endif
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Client</th>
                        <th>Type</th>
                        <th>Gender</th>
                        <th>Address</th>
                        <th>Marital Status</th>
                        <th>NRC</th>
                        <th>Email</th>
                        <th>Referral Date</th>
                        <th>Remarks</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($referrals as $referral)
                        <tr>
                            <td>{{ $referral->referral_reference }}</td>
                            <td>{{ $referral->fn_name }} {{ $referral->lname }}</td>
                            <td>{{ ucfirst($referral->client_type ?: 'N/A') }}</td>
                            <td>{{ $referral->gender ?: 'N/A' }}</td>
                            <td>{{ $referral->address ?: 'N/A' }}</td>
                            <td>{{ $referral->marital_status ?: 'N/A' }}</td>
                            <td>{{ $referral->nrc_number ?: 'N/A' }}</td>
                            <td>{{ $referral->email ?: 'N/A' }}</td>
                            <td>{{ $referral->referral_date }}</td>
                            <td>{{ $referral->notes ?: 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-success btn-xs"
                                    data-toggle="modal"
                                    data-target="#approveReferralModal"
                                    data-action="{{ route('loan-referrals.approvals.decide', [$referral->id, 'approved']) }}"
                                    data-referral-id="{{ $referral->id }}">Approve</button>
                                <form method="POST" action="{{ route('loan-referrals.approvals.decide', [$referral->id, 'declined']) }}" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-xs" onclick="return confirm('Decline this referral?')">Decline</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">No pending loan referrals.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="text-center">{{ $referrals->links() }}</div>
        </div>
    </div>

    <div class="modal fade" id="approveReferralModal" tabindex="-1" role="dialog" aria-labelledby="approveReferralModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" id="approveReferralForm">
                    @csrf
                    <input type="hidden" name="referral_id" id="approveReferralId" value="{{ old('referral_id') }}">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="approveReferralModalLabel">Approve Loan Referral</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="approval_loan_officer_id">Assign to Loan Officer</label>
                            <select name="selected_user_id" id="approval_loan_officer_id" class="form-control" required>
                                <option value=""></option>
                                @foreach($loanOfficers as $loanOfficer)
                                    <option value="{{ $loanOfficer->id }}" {{ (string) old('selected_user_id') === (string) $loanOfficer->id ? 'selected' : '' }}>
                                        {{ $loanOfficer->first_name }} {{ $loanOfficer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve Referral</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<script>
    $(function () {
        var $modal = $('#approveReferralModal');
        var $form = $('#approveReferralForm');
        var $officerSelect = $('#approval_loan_officer_id');

        var select2Initialized = false;

        $modal.on('show.bs.modal', function (event) {
            var $button = $(event.relatedTarget);
            if ($button.length) {
                $form.attr('action', $button.data('action'));
                $('#approveReferralId').val($button.data('referral-id'));
                $officerSelect.val(null).trigger('change');
            }
        });

        $modal.on('shown.bs.modal', function () {
            if (!select2Initialized && $.fn.select2) {
                $officerSelect.select2({
                    dropdownParent: $modal,
                    placeholder: 'Search and select a loan officer',
                    width: '100%'
                });
                select2Initialized = true;
            }
        });

        @if($errors->has('selected_user_id') && old('referral_id'))
            $form.attr('action', '{{ route('loan-referrals.approvals.decide', [old('referral_id'), 'approved']) }}');
            $modal.modal('show');
        @endif
    });
</script>
@endsection
