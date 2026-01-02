<!-- Institution Metrics Modal for Admin -->
@if($role->role_id == '1' || $role->role_id == '10')
    <div class="modal fade" id="institutionMetricsModal" tabindex="-1" role="dialog"
        aria-labelledby="institutionMetricsModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="institutionMetricsModalLabel">Institution Metrics</h4>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning text-center" style="margin-bottom: 15px;">
                        <i class="fa fa-exclamation-triangle"></i> <strong>Note:</strong> This feature is currently under
                        development
                        and testing.
                    </div>
                    <!-- Tabs Nav -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab_branch_efficiency"
                                aria-controls="tab_branch_efficiency" role="tab" data-toggle="tab">Branch Efficiency
                                Rates</a></li>
                        <li role="presentation"><a href="#tab_pdua" aria-controls="tab_pdua" role="tab"
                                data-toggle="tab">Institutional PDUA & Default Rate</a></li>
                    </ul>
                    <div class="tab-content" style="padding-top: 20px;">
                        <div role="tabpanel" class="tab-pane active" id="tab_branch_efficiency">
                            <div class="row">
                                <div class="col-md-12">
                                    <!-- Table 1: Branch Efficiency Rates -->
                                    <h4 style="font-weight: bold; margin-bottom: 15px;">Branch Efficiency Rates</h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped text-center">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">Branch Name</th>
                                                    <th style="text-align: center;">Total Staff</th>
                                                    <th style="text-align: center;">Staff Meeting Targets</th>
                                                    <th style="text-align: center;">Efficiency %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
        // Prepare Branch Efficiency Data (Last 12 Months)
        $offices = \App\Models\Office::get();
        $base_target = $branchtargetDate;
        $base_compare = $branchcompareDate;

        // Pre-calculate date ranges for 12 cycles
        $cycles = [];
        $start_m_offset = (date('d') < 25) ? 1 : 0;
        for ($m = $start_m_offset; $m < $start_m_offset + 12; $m++) {
            $cycles[] = [
                'target' => date('Y-m-d', strtotime($base_target . " - $m months")),
                'compare' => date('Y-m-d', strtotime($base_compare . " - $m months"))
            ];
        }

        $earliest_date = $cycles[11]['compare'];
        $latest_date = $cycles[0]['target'];

        // 1. Fetch ALL relevant transactions for the 12-month period for efficient processing
        $all_disbursements = \App\Models\LoanTransaction::join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
            ->where('loan_transactions.date', '>', $earliest_date)
            ->where('loan_transactions.date', '<=', $latest_date)
            ->where('loan_transactions.transaction_type', 'disbursement')
            ->select('loans.loan_officer_id', 'loan_transactions.debit', 'loan_transactions.date')
            ->get();

        $all_interests = \App\Models\LoanTransaction::join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
            ->where('loan_transactions.date', '>', $earliest_date)
            ->where('loan_transactions.date', '<=', $latest_date)
            ->where('loan_transactions.transaction_type', 'interest')
            ->select('loans.loan_officer_id', 'loan_transactions.debit', 'loan_transactions.date')
            ->get();

        // 2. Map transactions to staff and cycles
        $staff_cycle_data = []; // [staff_id][cycle_index] => ['disbursed' => X, 'interest' => Y]

        foreach ($all_disbursements as $trans) {
            $s_id = $trans->loan_officer_id;
            $d = $trans->date;
            foreach ($cycles as $idx => $c) {
                if ($d > $c['compare'] && $d <= $c['target']) {
                    if (!isset($staff_cycle_data[$s_id][$idx]))
                        $staff_cycle_data[$s_id][$idx] = ['disbursed' => 0, 'interest' => 0];
                    $staff_cycle_data[$s_id][$idx]['disbursed'] += $trans->debit;
                    break;
                }
            }
        }

        foreach ($all_interests as $trans) {
            $s_id = $trans->loan_officer_id;
            $d = $trans->date;
            foreach ($cycles as $idx => $c) {
                if ($d > $c['compare'] && $d <= $c['target']) {
                    if (!isset($staff_cycle_data[$s_id][$idx]))
                        $staff_cycle_data[$s_id][$idx] = ['disbursed' => 0, 'interest' => 0];
                    $staff_cycle_data[$s_id][$idx]['interest'] += $trans->debit;
                    break;
                }
            }
        }

        // Render Table
        foreach ($offices as $office):
            // Get branch staff (Role 3=Consultant, 4=Manager)
            $branch_staff = \App\Models\User::where('office_id', $office->id)
                ->whereHas('role', function ($q) {
                    $q->whereIn('role_id', [3, 4]);
                })->where('status', 'Active')->get();

            $b_total_staff = $branch_staff->count();
            $b_total_hits = 0; // Total times any staff met target in last 12 months

            foreach ($branch_staff as $staff) {
                $s_id = $staff->id;

                for ($m = 0; $m < 12; $m++) {
                    $s_disbursed = isset($staff_cycle_data[$s_id][$m]['disbursed']) ? $staff_cycle_data[$s_id][$m]['disbursed'] : 0;
                    $s_interest = isset($staff_cycle_data[$s_id][$m]['interest']) ? $staff_cycle_data[$s_id][$m]['interest'] : 0;

                    // Calculate Derived Reloan Principal
                    $s_reloans = $s_interest / 0.4;
                    $s_total_given = $s_disbursed + $s_reloans;

                    // Target Logic: >= 40,000
                    if ($s_total_given >= 40000) {
                        $b_total_hits++;
                    }
                }
            }

            // Efficiency Rate: (Total Hits) / (Total Possible Hits)
            // Total Possible Hits = Staff Count * 12 Months
            $total_possible_hits = $b_total_staff * 12;
            $b_efficiency_rate = ($total_possible_hits > 0) ? ($b_total_hits / $total_possible_hits) * 100 : 0;

            if ($b_total_staff > 0): 
                                            ?>
                                                <tr>
                                                    <td>{{ $office->name }}</td>
                                                    <td>{{ $b_total_staff }}</td>
                                                    <td>{{ $b_total_hits }}</td>
                                                    <td>{{ number_format($b_efficiency_rate, 2) }}%</td>
                                                </tr>
                                                <?php        endif;
        endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>




                        <!-- **************************** AVG PDUA % ************************************* -->




                        <div role="tabpanel" class="tab-pane" id="tab_pdua">
                            <?php 
                                                        // PRE-CALCULATE PDUA DATA FOR TABLE AND SUMMARY
        $tbl_data = [];
        $total_default_rate_sum = 0;

        // Loop for 12 months history
        $pdua_start_m = (date('d') < 25) ? 1 : 0;
        for ($m = $pdua_start_m; $m < $pdua_start_m + 12; ++$m) {
            // Calculate dates for this month iteration
            $tbl_target = date('Y-m-d', strtotime($branchtargetDate . ' - ' . $m . 'months'));
            $tbl_compare = date('Y-m-d', strtotime($branchcompareDate . ' - ' . $m . 'months')); // Start of Cycle

            $month_name = date("M Y", strtotime($tbl_target));

            // --- 1. COUA (Cycle Opening Uncollected Amount) ---
            // Logic from lc_information: Sum of (Debit - Credit) for all transactions <= Start Date
            // Note: lc_information aggregates everything.

            // We can use DB queries for efficiency instead of iterating objects in Blade.

            // Money Given Out (Lifetime until start of cycle)
            // lc_information: transaction_type != 'specified_due_date_fee'
            $life_given = \App\Models\LoanTransaction::where('date', '<=', $tbl_compare)
                ->where('transaction_type', '!=', 'specified_due_date_fee')
                ->sum('debit');

            // Money Collected (Lifetime until start of cycle)
            // lc_information: all credits
            $life_collected = \App\Models\LoanTransaction::where('date', '<=', $tbl_compare)
                ->sum('credit');

            $coua = $life_given - $life_collected;
            // Note: lc_information does NOT floor at 0 for the whole user, allowing negative if collected > given?
            // Usually COUA (Opening Balance) should be positive or usually 0. 
            // We will stick to the raw calculation as per "exact logic" unless it produces weird results.
            // However, checking lc_information line 256: number_format($balance), where balance = given - collected.

            // --- 2. Cycle Stats (Between Compare and Target) ---

            // A. Total Loans Given Out This Cycle
            // lc_information: 
            // if disbursement: +debit
            // if interest: +(debit / 0.4)  <-- Principal component of reloan?

            $cycle_disbursements = \App\Models\LoanTransaction::where('date', '>', $tbl_compare)
                ->where('date', '<=', $tbl_target)
                ->where('transaction_type', 'disbursement')
                ->sum('debit');

            $cycle_interest_debits = \App\Models\LoanTransaction::where('date', '>', $tbl_compare)
                ->where('date', '<=', $tbl_target)
                ->where('transaction_type', 'interest')
                ->sum('debit');

            // Logic: $new_reloans_cycle = $principal = $transaction->debit / 0.4;
            $cycle_reloans_derived = $cycle_interest_debits / 0.4;

            $total_given_out = $cycle_disbursements + $cycle_reloans_derived;


            // B. Total Cash Collected This Cycle
            // lc_information LOGIC:
            // full_payment: credit
            // part_payment: credit
            // reloan_payment: balance_bf  <--- CRITICAL

            $cycle_full_part = \App\Models\LoanTransaction::where('date', '>', $tbl_compare)
                ->where('date', '<=', $tbl_target)
                ->whereIn('payment_apply_to', ['full_payment', 'part_payment'])
                ->sum('credit');

            $cycle_reloan_payments = \App\Models\LoanTransaction::where('date', '>', $tbl_compare)
                ->where('date', '<=', $tbl_target)
                ->where('payment_apply_to', 'reloan_payment')
                ->sum('balance_bf');

            $total_collected = $cycle_full_part + $cycle_reloan_payments;

            // --- 3. Derived Metrics ---

            // Total Cash Still Uncollected
            // lc_information: $balance (COUA) - $cycle_collected_total
            $still_uncollected = $coua - $total_collected;

            // PDUA
            // dashboard.blade.php: Collected / COUA
            // Prevent Div/0
            $denom = $coua;
            if ($denom <= 0)
                $denom = 0.0001;

            $pdua_percent = ($total_collected / $denom) * 100;
            if ($pdua_percent > 100)
                $pdua_percent = 100; // Cap at 100? dashboard logic sometimes caps.

            // Default Rate
            $default_rate = 100 - $pdua_percent;
            $total_default_rate_sum += $default_rate;

            $tbl_data[] = [
                'month' => $month_name,
                'coua' => $coua,
                'collected' => $total_collected,
                'given_out' => $total_given_out,
                'still_uncollected' => $still_uncollected,
                'pdua' => $pdua_percent
            ];
        }

        // Average Default Rate
        $avg_default_rate = $total_default_rate_sum / 12;
                                                        ?>

                            <div class="row" style="margin-top: 20px;">
                                <div class="col-md-12">

                                    <!-- Average Summary Box -->
                                    <div class="callout callout-info"
                                        style="background-color: #00c0ef !important; border-color: #0097bc !important; color: #fff !important; margin-bottom: 20px; text-align: center;">
                                        <h4 style="font-weight: bold;" id="pdua_summary_text">Average Institutional Default
                                            Rate (Last 12 Months)
                                        </h4>
                                        <p style="font-size: 32px; font-weight: bold; margin-top: 10px;"
                                            id="pdua_summary_value">
                                            {{ number_format($avg_default_rate, 2) }}%
                                        </p>
                                    </div>

                                    <!-- Table 2: Institutional PDUA & Default Rate -->
                                    <div
                                        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                                        <h4 style="font-weight: bold; margin: 0;">Institutional PDUA & Default Rate
                                            (Monthly)</h4>
                                        <button class="btn btn-success btn-sm" type="button" onclick="exportPDUATable()">
                                            <i class="fa fa-file-excel-o"></i> Export to Excel
                                        </button>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped text-center" id="pdua_table">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;">Month</th>
                                                    <th style="text-align: center;">Cycle Opening Uncollected (ZMW)</th>
                                                    <th style="text-align: center;">Total Cash Collected (ZMW)</th>
                                                    <th style="text-align: center;">Total Cash Still Uncollected (ZMW)</th>
                                                    <th style="text-align: center;">Total loans Given out this cycle (ZMW)
                                                    </th>
                                                    <th style="text-align: center;">Monthly PDUA %</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php    foreach ($tbl_data as $row): ?>
                                                <tr>
                                                    <td>{{ $row['month'] }}</td>
                                                    <td>{{ number_format($row['coua']) }}</td>
                                                    <td>{{ number_format($row['collected']) }}</td>
                                                    <td>{{ number_format($row['still_uncollected']) }}</td>
                                                    <td>{{ number_format($row['given_out']) }}</td>
                                                    <td>{{ number_format($row['pdua'], 2) }}%</td>
                                                </tr>
                                                <?php    endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    function exportPDUATable() {
        // 1. Get the table and summary text
        var table = document.getElementById('pdua_table');
        var summaryText = document.getElementById('pdua_summary_text').innerText;
        var summaryValue = document.getElementById('pdua_summary_value').innerText;
        // Clean up summary text
        summaryText = summaryText.replace(/\s+/g, ' ').trim();
        summaryValue = summaryValue.trim();

        var fullSummary = summaryText + ": " + summaryValue;

        // 2. Clone table to modify for export
        var exportTable = table.cloneNode(true);

        // 3. Insert Summary Row at top
        var thead = exportTable.querySelector('thead');
        var summaryRow = document.createElement('tr');
        var summaryCell = document.createElement('th');
        summaryCell.colSpan = 6;
        summaryCell.style.textAlign = 'center';
        summaryCell.style.fontSize = '16px';
        summaryCell.style.backgroundColor = '#00c0ef';
        summaryCell.style.color = '#ffffff';
        summaryCell.innerText = fullSummary;
        summaryRow.appendChild(summaryCell);

        if (thead) {
            thead.insertBefore(summaryRow, thead.firstChild);
        } else {
            // In case no thead (unlikely but safe)
            var tbody = exportTable.querySelector('tbody');
            tbody.insertBefore(summaryRow, tbody.firstChild);
        }

        // 4. Trigger Download
        var html = exportTable.outerHTML;
        // Use Blob for better browser compatibility
        var blob = new Blob(['\ufeff', html], { // \ufeff for BOM to handle UTF-8 correctly in Excel
            type: 'application/vnd.ms-excel'
        });
        var url = URL.createObjectURL(blob);
        var downloadLink = document.createElement("a");
        downloadLink.href = url;
        downloadLink.download = 'institutional_pdua_metrics.xls';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    }
</script>