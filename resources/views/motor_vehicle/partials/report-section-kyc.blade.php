<div class="report-section">
    <div class="report-header">
        <h3 class="report-title"><i class="fa fa-user"></i> Client KYC Information</h3>
        <div class="report-meta">Client: {{ $loan->client->first_name ?? '' }} {{ $loan->client->last_name ?? '' }} | NRC: {{ $loan->client->nrc_number ?? 'N/A' }}</div>
    </div>

    <table class="report-table">
        <tr>
            <th class="label-cell">First Name</th>
            <td>{{ $loan->client->first_name ?? 'N/A' }}</td>
            <th class="label-cell">Last Name</th>
            <td>{{ $loan->client->last_name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">NRC Number</th>
            <td>{{ $loan->client->nrc_number ?? 'N/A' }}</td>
            <th class="label-cell">TPIN</th>
            <td>{{ $loan->client->tpin ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Address Line 1</th>
            <td>{{ $loan->client->address_line1 ?? 'N/A' }}</td>
            <th class="label-cell">Address Line 2</th>
            <td>{{ $loan->client->address_line2 ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">City</th>
            <td>{{ $loan->client->city ?? 'N/A' }}</td>
            <th class="label-cell">Email (Primary)</th>
            <td>{{ $loan->client->email_primary ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Phone (Primary)</th>
            <td>{{ $loan->client->phone_primary ?? 'N/A' }}</td>
            <th class="label-cell">Phone (Secondary)</th>
            <td>{{ $loan->client->phone_secondary ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Employer</th>
            <td>{{ $loan->client->employer ?? 'N/A' }}</td>
            <th class="label-cell">Employer Address</th>
            <td>{{ $loan->client->employer_address ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Business Name</th>
            <td>{{ $loan->client->business_name ?? 'N/A' }}</td>
            <th class="label-cell">Business Type</th>
            <td>{{ $loan->client->business_type ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Annual Income</th>
            <td>K{{ number_format($loan->client->annual_income ?? 0, 2) }}</td>
            <th class="label-cell">Office ID</th>
            <td>{{ $loan->loan->office_id ?? $loan->client->office_id ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="report-header" style="margin-top: 20px;">
        <h3 class="report-title" style="font-size: 16px; color: #334159;"><i class="fa fa-users"></i> Next of Kin</h3>
    </div>
    <table class="report-table">
        <tr>
            <th class="label-cell">Name</th>
            <td>{{ $loan->client->next_of_kin_name ?? 'N/A' }}</td>
            <th class="label-cell">Relationship</th>
            <td>{{ $loan->client->next_of_kin_relationship ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Phone</th>
            <td>{{ $loan->client->next_of_kin_phone ?? 'N/A' }}</td>
            <th class="label-cell">Address</th>
            <td>{{ $loan->client->next_of_kin_address ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="report-header" style="margin-top: 20px;">
        <h3 class="report-title" style="font-size: 16px; color: #334159;"><i class="fa fa-user-tie"></i> Guarantor Information</h3>
    </div>
    <table class="report-table">
        <tr>
            <th class="label-cell">Guarantor Name</th>
            <td>{{ $loan->client->guarantor_name ?? 'N/A' }}</td>
            <th class="label-cell">Guarantor NRC</th>
            <td>{{ $loan->client->guarantor_nrc ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Guarantor Phone</th>
            <td>{{ $loan->client->guarantor_phone ?? 'N/A' }}</td>
            <th class="label-cell">Guarantor Address</th>
            <td>{{ $loan->client->guarantor_address ?? 'N/A' }}</td>
        </tr>
        <tr>
            <th class="label-cell">Guarantor Employer</th>
            <td>{{ $loan->client->guarantor_employer ?? 'N/A' }}</td>
            <th class="label-cell">Relationship</th>
            <td>{{ $loan->client->guarantor_relationship ?? 'N/A' }}</td>
        </tr>
    </table>

    <div class="report-header" style="margin-top: 20px;">
        <h3 class="report-title" style="font-size: 16px; color: #334159;"><i class="fa fa-chart-line"></i> Loan Details</h3>
    </div>
    <table class="report-table">
        <tr>
            <th class="label-cell">Principal</th>
            <td>K{{ number_format($loan->principal ?? 0, 2) }}</td>
            <th class="label-cell">Interest Rate</th>
            <td>{{ $loan->interest_rate ?? 0 }}%</td>
        </tr>
        <tr>
            <th class="label-cell">Loan Term</th>
            <td>{{ $loan->loan_term ?? 'N/A' }}</td>
            <th class="label-cell">Repayment Frequency</th>
            <td>{{ $loan->repayment_frequency ?? 'N/A' }}{{ $loan->repayment_frequency_type ?? '' }}</td>
        </tr>
        <tr>
            <th class="label-cell">First Repayment Date</th>
            <td>{{ $loan->first_repayment_date ?? 'N/A' }}</td>
            <th class="label-cell">Expected Maturity Date</th>
            <td>{{ $loan->expected_maturity_date ?? 'N/A' }}</td>
        </tr>
    </table>
</div>
