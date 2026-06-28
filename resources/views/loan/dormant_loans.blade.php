@extends('layouts.master')

@section('title')
    Dormant Loans by Province
@endsection

@section('content')

<section class="content">


<!-- Headline cards here -->


    {{-- INSTITUTIONAL HEADLINE CARDS --}}
    <div class="row">
        <div class="col-md-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3 id="institutionalDormantLoans">-</h3>
                    <p>Total Dormant Loans (Institution-Wide)</p>
                </div>
                <div class="icon">
                    <i class="fa fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3 id="institutionalUnitShare">-</h3>
                    <p>Total Unit Share (Institution-Wide)</p>
                </div>
                <div class="icon">
                    <i class="fa fa-pie-chart"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER TOOLBAR --}}
    <div class="box box-primary">
        <div class="box-body">
            <form id="filterForm" style="display: flex; align-items: center; justify-content: center; gap: 15px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 10px;">
                    <label style="margin: 0; white-space: nowrap; font-weight: 600;">
                        <i class="fa fa-map-marker"></i> Province:
                    </label>
                    <select name="province_id" id="province_id" class="form-control" style="width: 250px;" required>
                        <option value="">Select Province</option>
                        @foreach ($provinces as $province)
                            <option value="{{ $province->id }}">
                                {{ $province->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa fa-filter"></i> Load Dormant Loans
                </button>
            </form>
        </div>
    </div>

    <!-- Stats here -->
    {{-- SUMMARY STATS --}}
    <div id="summary"></div>

    {{-- RESULTS --}}
    <div id="results"></div>

</section>

{{-- JS --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterForm');
    const resultsDiv = document.getElementById('results');
    const summaryDiv = document.getElementById('summary');

    // Fetch institutional-wide stats on page load
    fetchInstitutionalStats();

    async function fetchInstitutionalStats() {
        try {
            const res = await fetch('https://lms2backend.whencefinancesystem.com/dormant-loans-institutional-stats');
            const data = await res.json();

            if (data.total_dormant_loans !== undefined) {
                document.getElementById('institutionalDormantLoans').textContent = data.total_dormant_loans.toLocaleString();
            }

            if (data.total_unit_share !== undefined) {
                document.getElementById('institutionalUnitShare').textContent = data.total_unit_share.toLocaleString();
            }
        } catch (err) {
            console.error('Failed to load institutional stats:', err);
            document.getElementById('institutionalDormantLoans').textContent = 'Error';
            document.getElementById('institutionalUnitShare').textContent = 'Error';
        }
    }

    async function fetchDormantLoans() {
        const provinceId = document.getElementById('province_id').value;

        if (!provinceId) {
            alert('Please select a province');
            return;
        }

        summaryDiv.innerHTML = '';
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
                summaryDiv.innerHTML = '';
                resultsDiv.innerHTML = `
                    <div class="box box-warning">
                        <div class="box-body text-center text-muted">
                            No dormant loans found for this province
                        </div>
                    </div>
                `;
                return;
            }

            // Calculate totals
            let totalOffices = data.offices.length;
            let totalLoans = 0;
            let overallGrandTotal = 0;

            let html = '';

            data.offices.forEach(office => {
                let grandTotal = 0;
                totalLoans += office.loans.length;

                html += `
                    <div class="box box-success">
                        <div class="box-header with-border" style="cursor: pointer;" data-toggle="collapse" data-target="#office-${office.office_id}">
                            <h3 class="box-title">
                                ${office.office_name}
                            </h3>
                            <span class="badge bg-blue pull-right">
                                ${office.loans.length} dormant loans
                            </span>
                            <i class="fa fa-chevron-down pull-right" style="margin-right: 10px; margin-top: 3px;"></i>
                        </div>

                        <div id="office-${office.office_id}" class="collapse box-body table-responsive no-padding">
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
                    overallGrandTotal += dueAmount;

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

            // Display summary stats
            summaryDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box bg-aqua">
                            <span class="info-box-icon"><i class="fa fa-building"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Offices</span>
                                <span class="info-box-number">${totalOffices}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa fa-list"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Total Dormant Loans</span>
                                <span class="info-box-number">${totalLoans}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa fa-money"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Grand Total Due</span>
                                <span class="info-box-number">${overallGrandTotal.toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa fa-pie-chart"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Unit Share</span>
                                <span class="info-box-number">-</span>
                                <span class="progress-description">Coming Soon</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            resultsDiv.innerHTML = html;

            // Add event listeners for chevron toggle
            document.querySelectorAll('[data-toggle="collapse"]').forEach(header => {
                const target = document.querySelector(header.getAttribute('data-target'));
                const chevron = header.querySelector('.fa-chevron-down, .fa-chevron-up');
                
                if (target && chevron) {
                    target.addEventListener('show.bs.collapse', function() {
                        chevron.classList.remove('fa-chevron-down');
                        chevron.classList.add('fa-chevron-up');
                    });
                    
                    target.addEventListener('hide.bs.collapse', function() {
                        chevron.classList.remove('fa-chevron-up');
                        chevron.classList.add('fa-chevron-down');
                    });
                }
            });

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


