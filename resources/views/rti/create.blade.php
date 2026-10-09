@extends('layouts.master')

@section('title')
    Create RTI Branch Loan
@endsection

@section('content')
    @include('rti._partials.flash')

    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <i class="fa fa-plus-circle"></i> Create RTI Branch Loan
                    </h4>
                    <div class="heading-elements">
                        <a href="{{ route('rti.loans.index') }}" class="btn btn-default btn-sm">
                            <i class="fa fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>

                <div class="panel-body">

                    <div class="callout callout-info" style="margin-bottom:20px;">
                        <h5><i class="fa fa-info-circle"></i> RTI Arrangement</h5>
                        <p>
                            Interest is automatically calculated at <strong>20%</strong> of the principal
                            amount per the RTI arrangement. The total payable amount will be shown below.
                        </p>
                    </div>

                    <form method="POST" action="{{ route('rti.loans.store') }}" id="rtiLoanForm">
                        {{ csrf_field() }}

                        {{-- Branch / Office --}}
                        <div class="form-group {{ $errors->has('office_id') ? 'has-error' : '' }}">
                            <label for="office_id" class="control-label">
                                Branch / Office <span class="text-danger">*</span>
                            </label>
                            <select name="office_id" id="office_id" class="form-control" required>
                                <option value="">-- Select Branch --</option>
                                @foreach($offices as $office)
                                    <option value="{{ $office->id }}"
                                        {{ old('office_id') == $office->id ? 'selected' : '' }}>
                                        {{ $office->name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('office_id'))
                                <span class="help-block">{{ $errors->first('office_id') }}</span>
                            @endif
                        </div>

                        {{-- Responsible Staff --}}
                        <div class="form-group {{ $errors->has('staff_id') ? 'has-error' : '' }}">
                            <label for="staff_id" class="control-label">
                                Responsible Staff <span class="text-danger">*</span>
                            </label>
                            <select name="staff_id" id="staff_id" class="form-control" required>
                                <option value="">-- Select Staff Member --</option>
                                @foreach($staff as $member)
                                    <option value="{{ $member->id }}"
                                        {{ old('staff_id') == $member->id ? 'selected' : '' }}
                                        data-office-id="{{ $member->office_id }}">
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @if($errors->has('staff_id'))
                                <span class="help-block">{{ $errors->first('staff_id') }}</span>
                            @endif
                        </div>

                        {{-- Principal --}}
                        <div class="form-group {{ $errors->has('principal') ? 'has-error' : '' }}">
                            <label for="principal" class="control-label">
                                Principal Amount (K) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                   name="principal"
                                   id="principal"
                                   class="form-control"
                                   placeholder="0.00"
                                   step="0.01"
                                   min="1"
                                   value="{{ old('principal') }}"
                                   required>
                            @if($errors->has('principal'))
                                <span class="help-block">{{ $errors->first('principal') }}</span>
                            @endif
                        </div>

                        {{-- Live calculation summary --}}
                        <div id="loanSummary" class="well" style="display:none; background:#f9f9f9; margin-top:10px;">
                            <h5 style="margin-top:0;font-weight:600;">
                                <i class="fa fa-calculator"></i> Loan Summary
                            </h5>
                            <table class="table table-condensed" style="margin-bottom:0;">
                                <tr>
                                    <td style="width:50%;">Principal</td>
                                    <td><strong id="summaryPrincipal">K0.00</strong></td>
                                </tr>
                                <tr>
                                    <td>RTI Interest (20%)</td>
                                    <td><strong id="summaryInterest" class="text-warning">K0.00</strong></td>
                                </tr>
                                <tr style="border-top:2px solid #ddd;">
                                    <td><strong>Total Payable</strong></td>
                                    <td><strong id="summaryTotal" class="text-primary" style="font-size:16px;">K0.00</strong></td>
                                </tr>
                            </table>
                        </div>

                        <hr>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Save RTI Loan (Pending)
                            </button>
                            <a href="{{ route('rti.loans.index') }}" class="btn btn-default">
                                Cancel
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('principal').addEventListener('input', function () {
            var principal = parseFloat(this.value) || 0;
            var rtiRate   = {{ $rtiRate }};
            var interest  = Math.round(principal * rtiRate * 100) / 100;
            var total     = principal + interest;

            if (principal > 0) {
                document.getElementById('loanSummary').style.display = 'block';
            } else {
                document.getElementById('loanSummary').style.display = 'none';
            }

            document.getElementById('summaryPrincipal').textContent = 'K' + principal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('summaryInterest').textContent  = 'K' + interest.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            document.getElementById('summaryTotal').textContent     = 'K' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        });

        function loadStaffByOffice(officeId) {
            var $staff = $('#staff_id');
            $staff.prop('disabled', true);

            if (!officeId) {
                $staff.empty().append('<option value="">-- Select Staff Member --</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: '/api/users-by-office/' + officeId,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    $staff.empty().append('<option value="">-- Select Staff Member --</option>');
                    $.each(data, function (key, value) {
                        $staff.append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    $staff.prop('disabled', false);
                },
                error: function () {
                    $staff.empty().append('<option value="">-- Select Staff Member --</option>').prop('disabled', false);
                }
            });
        }

        $('#office_id').change(function () {
            loadStaffByOffice($(this).val());
        });

        @if(old('office_id'))
            loadStaffByOffice('{{ old('office_id') }}');
        @else
            $('#staff_id').empty().append('<option value="">-- Select Staff Member --</option>');
        @endif
    </script>
@endsection
