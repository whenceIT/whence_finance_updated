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
        // Prepare Branch Efficiency Data
        $offices = \App\Models\Office::get();

        // 1. Calculate ACTUAL Collections per Staff (Grouped Sum of Credits in Period)
        // Using 'loans' join to identify the officer
        $start_date_sql = date('Y-m-d', strtotime($branchcompareDate));
        $end_date_sql = date('Y-m-d', strtotime($branchtargetDate));

        $staff_actuals = \App\Models\LoanTransaction::join('loans', 'loan_transactions.loan_id', '=', 'loans.id')
            ->where('loan_transactions.date', '>', $start_date_sql)
            ->where('loan_transactions.date', '<=', $end_date_sql)
            ->whereIn('loan_transactions.payment_apply_to', ['full_payment', 'part_payment', 'reloan_payment'])
            ->selectRaw('loans.loan_officer_id, sum(loan_transactions.credit) as total_collected')
            ->groupBy('loans.loan_officer_id')
            ->pluck('total_collected', 'loans.loan_officer_id')
            ->all();

        // 2. Calculate TARGET (Expected) per Staff
        // Definition: Sum of OPENING BALANCES of all loans that have a 'first_repayment_date' within the cycle.
        // Logic derived from 'new_collection.blade.php' (Loans due) + 'dashboard' (Opening Balance logic).

        $staff_targets = [];

        // Get all loans due in this period
        // Eager load transactions filtered to strictly BEFORE the cycle starts to calc Opening Balance
        $due_loans = \App\Models\Loan::whereBetween('first_repayment_date', [$start_date_sql, $end_date_sql])
            ->with([
                'transactions' => function ($q) use ($start_date_sql) {
                    $q->where('date', '<=', $start_date_sql);
                }
            ])
            ->get();

        foreach ($due_loans as $loan) {
            if (!$loan->loan_officer_id)
                continue;
            $oid = $loan->loan_officer_id;

            // Calculate Opening Balance for this loan
            $l_debit = 0;
            $l_credit = 0;
            foreach ($loan->transactions as $trans) {
                // Consistent with Dashboard COUA logic
                if ($trans->transaction_type != 'specified_due_date_fee') {
                    $l_debit += $trans->debit;
                }
                $l_credit += $trans->credit;
            }

            $l_bal = $l_debit - $l_credit;
            if ($l_bal < 0)
                $l_bal = 0;

            if (!isset($staff_targets[$oid]))
                $staff_targets[$oid] = 0;
            $staff_targets[$oid] += $l_bal;
        }

        // Render Table
        foreach ($offices as $office):
            // Get branch staff (Role 3=Consultant, 4=Manager)
            $branch_staff = \App\Models\User::where('office_id', $office->id)
                ->whereHas('role', function ($q) {
                    $q->whereIn('role_id', [3, 4]);
                })->where('status', 'Active')->get();

            $b_total_staff = $branch_staff->count();
            $b_met_target = 0;

            foreach ($branch_staff as $staff) {
                $s_id = $staff->id;
                $s_target = isset($staff_targets[$s_id]) ? $staff_targets[$s_id] : 0;
                $s_actual = isset($staff_actuals[$s_id]) ? $staff_actuals[$s_id] : 0;

                // Determine if target met
                // Threshold: 100% collection of what was due?
                // Usually 100% is the goal.

                if ($s_target > 0) {
                    $s_eff = ($s_actual / $s_target);
                    if ($s_eff >= 1.0) { // 100%
                        $b_met_target++;
                    }
                } elseif ($s_target == 0 && $s_actual > 0) {
                    // If target 0 (no loans due) but collected money?
                    $b_met_target++;
                }
            }

            // If staff count is 0, avoid div by zero
            $b_efficiency_rate = ($b_total_staff > 0) ? ($b_met_target / $b_total_staff) * 100 : 0;

            if ($b_total_staff > 0): 
                    ?>
                                                <tr>
                                                    <td>{{ $office->name }}</td>
                                                    <td>{{ $b_total_staff }}</td>
                                                    <td>{{ $b_met_target }}</td>
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
                        <div role="tabpanel" class="tab-pane" id="tab_pdua">
                            <?php 
                                // PRE-CALCULATE PDUA DATA FOR TABLE AND SUMMARY
        $tbl_data = [];
        $total_default_rate_sum = 0;

        // Loop for 12 months history
        for ($m = 0; $m < 12; $m++) {
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
                                        <h4 style="font-weight: bold;">Average Institutional Default Rate (Last 12 Months)
                                        </h4>
                                        <p style="font-size: 32px; font-weight: bold; margin-top: 10px;">
                                            {{ number_format($avg_default_rate, 2) }}%
                                        </p>
                                    </div>

                                    <!-- Table 2: Institutional PDUA & Default Rate -->
                                    <h4 style="font-weight: bold; margin-bottom: 15px;">Institutional PDUA & Default Rate
                                        (Monthly)
                                    </h4>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped text-center">
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