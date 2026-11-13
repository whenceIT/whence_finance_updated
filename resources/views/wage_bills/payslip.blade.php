
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Employee Payslip</title>

    <style>
        @page {
            margin: 30px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #333;
            font-size: 11px;
        }

        .header {
            border-bottom: 4px solid #00a04a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .brand-cell {
            vertical-align: middle;
        }

        .company-logo {
            width: 58px;
            height: 58px;
            vertical-align: middle;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #000041;
        }

        .document-title {
            font-size: 19px;
            font-weight: bold;
            text-align: right;
            color: #000041;
            letter-spacing: 1px;
        }

        .document-accent {
            width: 55px;
            border-top: 3px solid #00a04a;
            margin-top: 7px;
            margin-left: auto;
        }

        .muted {
            color: #777;
            font-size: 10px;
        }

        .section-title {
            background: #000041;
            color: #ffffff;
            padding: 8px;
            font-weight: bold;
            margin-top: 18px;
            border-left: 4px solid #00a04a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .details td {
            padding: 7px 5px;
            border-bottom: 1px solid #eee;
        }

        .label {
            color: #666;
            width: 25%;
        }

        .amount-table th,
        .amount-table td {
            padding: 9px;
            border-bottom: 1px solid #ddd;
        }

        .amount-table th {
            background: #eef5f1;
            color: #000041;
            text-align: left;
        }

        .amount {
            text-align: right;
        }

        .net-pay {
            margin-top: 20px;
            background: #eef7f1;
            border: 1px solid #00a04a;
            border-left: 5px solid #00a04a;
            padding: 15px;
        }

        .net-pay-label {
            font-size: 13px;
            font-weight: bold;
        }

        .net-pay-value {
            font-size: 22px;
            font-weight: bold;
            color: #000041;
            text-align: right;
        }

        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            text-align: center;
            color: #777;
            font-size: 9px;
        }
    </style>
</head>

<body>

    @php
        $money = function ($amount) {
            return 'K ' . number_format((float) $amount, 2);
        };
    @endphp

    <table class="header">
        <tr>
            <td width="65%" class="brand-cell">
                <table style="width: auto;">
                    <tr>
                        <td style="padding-right: 10px; vertical-align: middle;">
                            <img class="company-logo"
                                 src="{{ public_path('images/w/logo.jpg') }}"
                                 alt="Whence Finance Logo">
                        </td>
                        <td style="vertical-align: middle;">
                            <div class="company-name">WHENCE</div>
                            <div style="font-size: 12px; font-weight: bold; color: #00a04a;">
                                FINANCIAL SERVICES
                            </div>
                            <div class="muted">Employee Payroll Statement</div>
                        </td>
                    </tr>
                </table>
            </td>

            <td width="35%" align="right" style="vertical-align: middle;">
                <div class="document-title">PAYSLIP</div>
                <div class="document-accent"></div>
                <div class="muted" style="margin-top: 7px;">
                    Generated: {{ $generatedAt->format('d M Y') }}
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">EMPLOYEE INFORMATION</div>

    <table class="details">
        <tr>
            <td class="label">Employee Name</td>
            <td>
                <strong>{{ $employee->first_name ?? '' }}
                    {{ $employee->last_name ?? '' }}</strong>
            </td>
            <td class="label">Employee ID</td>
            <td>{{ $wageBill->user_id }}</td>
        </tr>

        <tr>
            <td class="label">Branch</td>
            <td>{{ $branch->name ?? 'N/A' }}</td>
            <td class="label">Payroll Period</td>
            <td>
                {{ $cycleStart->format('d M Y') }}
                - {{ $cycleEnd->format('d M Y') }}
            </td>
        </tr>
    </table>

    <div class="section-title">EARNINGS</div>

    <table class="amount-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>

        <tbody>
            <tr>
                <td>Performance-Based Salary</td>
                <td class="amount">{{ $money($wageBill->predicted_salary) }}</td>
            </tr>

            <tr>
                <td>Allowances</td>
                <td class="amount">{{ $money($wageBill->allowances) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">STATUTORY DEDUCTIONS</div>

    <table class="amount-table">
        <tbody>
            <tr>
                <td>PAYE</td>
                <td class="amount">{{ $money($wageBill->paye) }}</td>
            </tr>

            <tr>
                <td>NAPSA</td>
                <td class="amount">{{ $money($wageBill->napsa) }}</td>
            </tr>

            <tr>
                <td>NHIMA</td>
                <td class="amount">{{ $money($wageBill->nhima) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">OTHER DEDUCTIONS</div>

    <table class="amount-table">
        <tbody>
            <tr>
                <td>Salary Advance Deductions</td>
                <td class="amount">
                    {{ $money($wageBill->advance_deductions) }}
                </td>
            </tr>
        </tbody>
    </table>

    <div class="net-pay">
        <table>
            <tr>
                <td class="net-pay-label">NET PAY</td>
                <td class="net-pay-value">
                    {{ $money($wageBill->net_pay) }}
                </td>
            </tr>
        </table>
    </div>

    <div class="footer">
        This is a computer-generated payslip from Whence Financial Services.
        <br>
        Please contact your branch manager if you have any queries regarding
        this payslip.
    </div>

</body>
</html>
