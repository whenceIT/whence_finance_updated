@extends('layouts.master')

@section('title')
    Dormant Loans by Province
@endsection

@section('content')

<section class="content">

    {{-- FILTER BOX --}}
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Dormant Loans Filter</h3>
        </div>

        <form id="filterForm" class="form-horizontal">
            <div class="box-body">

                {{-- Province --}}
                <div class="form-group">
                    <label class="col-md-2 control-label">Province</label>
                    <div class="col-md-4">
                        <select name="province_id" id="province_id" class="form-control" required>
                            <option value="">Select Province</option>
                            @foreach ($provinces as $province)
                                <option value="{{ $province->id }}">
                                    {{ $province->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="box-footer">
                <div class="col-md-offset-2 col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Load Dormant Loans
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- RESULTS --}}
    <div id="results"></div>

</section>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const resultsDiv = document.getElementById('results');

    async function fetchDormantLoans() {
        const provinceId = document.getElementById('province_id').value;

        if (!provinceId) {
            alert('Please select a province');
            return;
        }

        resultsDiv.innerHTML = `
            <div class="box box-success">
                <div class="box-body text-center">
                    <i class="fa fa-spinner fa-spin"></i> Loading dormant loans...
                </div>
            </div>
        `;

        try {
            const res = await fetch(
                `https://lms2backend.whencefinancesystem.com/dormant-loans-by-province/${provinceId}`
            );

            const data = await res.json();

            if (!data.offices || !data.offices.length) {
                resultsDiv.innerHTML = `
                    <div class="box box-warning">
                        <div class="box-body text-center text-muted">
                            No dormant loans found for this province
                        </div>
                    </div>
                `;
                return;
            }

            let html = '';

            data.offices.forEach(office => {
                let grandTotal = 0;

                html += `
                    <div class="box box-success">
                        <div class="box-header with-border">
                            <h3 class="box-title">
                                ${office.office_name}
                            </h3>
                            <span class="badge bg-blue pull-right">
                                ${office.loans.length} dormant loans
                            </span>
                        </div>

                        <div class="box-body table-responsive no-padding">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Loan ID</th>
                                        <th>Client</th>
                                        <th>Loan Consultant</th>
                                        <th class="text-right">Due Amount</th>
                                        <th>Disbursement Date</th>
                                        <th>First Repayment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;

                office.loans.forEach(loan => {
                    // Calculate due amount: principal + 40%
                    const dueAmount = Number(loan.principal) * 1.4;
                    grandTotal += dueAmount;

                    // Format dates to YYYY-MM-DD
                    const disbDate = loan.disbursement_date
                        ? new Date(loan.disbursement_date).toISOString().split('T')[0]
                        : '-';
                    const repaymentDate = loan.first_repayment_date
                        ? new Date(loan.first_repayment_date).toISOString().split('T')[0]
                        : '-';

                    html += `
                        <tr>
                            <td>${loan.id}</td>
                            <td>${loan.client_name ?? '-'}</td>
                            <td>${loan.loan_officer_name ?? '-'}</td>
                            <td class="text-right">${dueAmount.toLocaleString()}</td>
                            <td>${disbDate}</td>
                            <td>${repaymentDate}</td>
                            <td>
                                <span class="label label-warning">
                                    ${loan.status}
                                </span>
                            </td>
                        </tr>
                    `;
                });

                // Grand total row per office
                html += `
                        <tr>
                            <td colspan="3" class="text-right"><strong>Grand Total</strong></td>
                            <td class="text-right"><strong>${grandTotal.toLocaleString()}</strong></td>
                            <td colspan="3"></td>
                        </tr>
                `;

                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            });

            resultsDiv.innerHTML = html;

        } catch (err) {
            console.error(err);
            resultsDiv.innerHTML = `
                <div class="box box-danger">
                    <div class="box-body text-center">
                        Failed to load dormant loans
                    </div>
                </div>
            `;
        }
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchDormantLoans();
    });
});
</script>

@endsection


