@php
                            $tenure = $loan->loan_term;

                            if ($loan->schedule_type === 'old') {
                                // Old schedule: lookup from payroll_loan_old_schedule
                                $oldRow = \App\Models\PayrollLoanOldSchedule::where('disbursement_amount', $loan->principal)->first();
                                $colMap = [9 => 'repayment_9_months', 12 => 'repayment_12_months', 18 => 'repayment_18_months', 24 => 'repayment_24_months'];
                                $col = $colMap[$tenure] ?? null;
                                $monthlyAmount = ($oldRow && $col) ? $oldRow->$col : null;
                            } else {
                                // New schedule: lookup from payroll_loan_schedules
                                $schedule = DB::table('payroll_loan_schedules')
                                    ->where('loan_amount', $loan->principal)
                                    ->first();
                                $monthlyAmount = $schedule ? ($schedule->{"months_$tenure"} ?? null) : null;
                            }

                            $totalRepayment = $monthlyAmount ? ($monthlyAmount * $tenure) : null;
                            $totalPaid = DB::table('loan_transactions')
                                ->where('loan_id', $loan->id)
                                ->where('transaction_type', 'repayment')
                                ->sum('credit');
                            $paidPercentage = $totalRepayment ? min(100, round(($totalPaid / $totalRepayment) * 100)) : 0;
                            $scheduleLabel  = ($loan->schedule_type === 'old') ? 'Old Schedule' : 'New Schedule';
                        @endphp

                        @if($monthlyAmount)
                            <p class="text-muted" style="margin-bottom:8px;">
                                <i class="fa fa-table"></i>&nbsp;
                                Using <strong>{{ $scheduleLabel }}</strong>
                            </p>
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>{{trans_choice('general.loan',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <th>Tenure</th>
                                        <th>{{trans_choice('general.monthly',1)}} {{trans_choice('general.amount',1)}}</th>
                                        <th>{{trans_choice('general.total',1)}} {{trans_choice('general.repayment',1)}}</th>
                                        <th>{{trans_choice('general.paid',1)}} {{trans_choice('general.amount',1)}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>K{{ number_format($loan->principal, 2) }}</td>
                                        <td>{{ $tenure }} Months</td>
                                        <td>K{{ number_format($monthlyAmount, 2) }}</td>
                                        <td>K{{ number_format($totalRepayment, 2) }}</td>
                                        <td>K{{ number_format($totalPaid, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="progress" style="height: 20px; margin-top: 15px;">
                                <div class="progress-bar" role="progressbar" style="width: {{ $paidPercentage }}%;" aria-valuenow="{{ $paidPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $paidPercentage }}%
                                </div>
                            </div>
                            <p style="margin-top: 10px;">
                                {{ trans_choice('general.paid',1) }}: K{{ number_format($totalPaid, 2) }} / {{ trans_choice('general.total',1) }}: K{{ number_format($totalRepayment, 2) }}
                            </p>
                        @else
                            <p>No schedule found for this loan amount and tenure.</p>
                        @endif