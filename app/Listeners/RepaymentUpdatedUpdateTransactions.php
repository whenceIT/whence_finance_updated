<?php

namespace App\Listeners;

use App\Events\RepaymentUpdated;
use App\Models\GlJournalEntry;
use App\Models\LoanRepaymentSchedule;
use App\Models\LoanTransaction;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RepaymentUpdatedUpdateTransactions
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  RepaymentUpdated $event
     * @return void
     */
    public function handle(RepaymentUpdated $event)
    {
        $loan_transaction = $event->loan_transaction;
        $loan = $loan_transaction->loan;
        $loan_product = $loan_transaction->loan->loan_product;
        $to_update = [];
        $paid_schedules = [];
        //set schedule paid to zero
        foreach (LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->get() as $key) {
            $schedule = LoanRepaymentSchedule::find($key->id);
            $schedule->principal_paid = 0;
            $schedule->interest_paid = 0;
            $schedule->fees_paid = 0;
            $schedule->penalty_paid = 0;
            $schedule->save();
        }
        $principal_paid = 0;
        $interest_paid = 0;
        $fees_paid = 0;
        $penalty_paid = 0;
        foreach (LoanTransaction::where('loan_id', $loan_transaction->loan_id)->where('transaction_type',
            'repayment')->where('reversed', 0)->orderBy('date', 'asc')->orderBy('id', 'asc')->get() as $key) {
            $payments = $key->credit;
            $principal_paid = 0;
            $interest_paid = 0;
            $fees_paid = 0;
            $penalty_paid = 0;
            foreach (LoanRepaymentSchedule::where('loan_id', $loan_transaction->loan_id)->orderBy('due_date', 'asc')->get() as $skey) {
                $principal_due = $skey->principal - $skey->principal_paid - $skey->principal_waived - $skey->principal_written_off;
                $interest_due = $skey->interest - $skey->interest_paid - $skey->interest_waived - $skey->interest_written_off;
                $fees_due = $skey->fees - $skey->fees_paid - $skey->fees_waived - $skey->fees_written_off;
                $penalty_due = $skey->penalty - $skey->penalty_paid - $skey->penalty_waived - $skey->penalty_written_off;
                $schedule_due = $principal_due + $interest_due + $fees_due + $penalty_due;

                if ($schedule_due > 0) {
                    if($key->payment_apply_to=="penalty") {
                        if ($payments > ($penalty_due) && $penalty_due > 0) {
                            $schedule = LoanRepaymentSchedule::find($skey->id);
                            $schedule->penalty_paid = $schedule->penalty_paid + $penalty_due;
                            $schedule->save();
                            $penalty_paid = $penalty_paid + $penalty_due;
                            $payments = $payments - $penalty_due;
                        } else {
                            if ($penalty_due > 0) {
                                $schedule = LoanRepaymentSchedule::find($skey->id);
                                $schedule->penalty_paid = $schedule->penalty_paid + $payments;
                                $schedule->save();
                                $penalty_paid = $penalty_paid + $payments;
                                $payments = 0;
                            }
                        }
                    }
                    if($key->payment_apply_to=="principal") {
                        if ($payments > ($principal_due) && $principal_due > 0) {
                            $schedule = LoanRepaymentSchedule::find($skey->id);
                            $schedule->principal_paid = $schedule->principal_paid + $principal_due;
                            $schedule->save();
                            $principal_paid = $principal_paid + $principal_due;
                            $payments = $payments - $principal_due;
                        } else {
                            if ($principal_due > 0) {
                                $schedule = LoanRepaymentSchedule::find($skey->id);
                                $schedule->principal_paid = $schedule->principal_paid + $payments;
                                $schedule->save();
                                $principal_paid = $principal_paid + $payments;
                                $payments = 0;
                            }
                        }
                    }
                    if($key->payment_apply_to=="interest") {
                        if ($payments > ($interest_due) && $interest_due > 0) {
                            $schedule = LoanRepaymentSchedule::find($skey->id);
                            $schedule->interest_paid = $schedule->interest_paid + $interest_due;
                            $schedule->save();
                            $interest_paid = $interest_paid + $interest_due;
                            $payments = $payments - $interest_due;

                        } else {
                            if ($interest_due > 0) {
                                $schedule = LoanRepaymentSchedule::find($skey->id);
                                $schedule->interest_paid = $schedule->interest_paid + $payments;
                                $schedule->save();
                                $interest_paid = $interest_paid + $payments;
                                $payments = 0;
                            }
                        }
                    }
                    if($key->payment_apply_to=="fees") {
                        if ($payments > ($fees_due) && $fees_due > 0) {
                            $schedule = LoanRepaymentSchedule::find($skey->id);
                            $schedule->fees_paid = $schedule->fees_paid + $fees_due;
                            $schedule->save();
                            $fees_paid = $fees_paid + $fees_due;
                            $payments = $payments - $fees_due;
                        } else {
                            if ($fees_due > 0) {
                                $schedule = LoanRepaymentSchedule::find($skey->id);
                                $schedule->fees_paid = $schedule->fees_paid + $payments;
                                $schedule->save();
                                $fees_paid = $fees_paid + $payments;
                                $payments = 0;
                            }
                        }
                    }
                    if($key->payment_apply_to=="regular") {
                        if ($payments >= $schedule_due) {
                            $schedule = LoanRepaymentSchedule::find($skey->id);
                            $schedule->principal_paid = $schedule->principal_paid + $principal_due;
                            $schedule->interest_paid = $schedule->interest_paid + $interest_due;
                            $schedule->fees_paid = $schedule->fees_paid + $fees_due;
                            $schedule->penalty_paid = $schedule->penalty_paid + $penalty_due;
                            $schedule->from_date = $key->date;
                            if ($key->date < $schedule->due_date) {
                                $schedule->total_paid_advance = $schedule_due;
                            }
                            if ($key->date > $schedule->due_date) {
                                $schedule->total_paid_late = $schedule_due;
                            }
                            $schedule->save();
                            $principal_paid = $principal_paid + $principal_due;
                            $interest_paid = $interest_paid + $interest_due;
                            $fees_paid = $fees_paid + $fees_due;
                            $penalty_paid = $penalty_paid + $penalty_due;
                            $payments = $payments - $schedule_due;

                        } else {
                            if ($loan_product->loan_transaction_strategy == "penalty_fees_interest_principal") {
                                if ($payments > ($penalty_due) && $penalty_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->penalty_paid = $schedule->penalty_paid + $penalty_due;
                                    $schedule->save();
                                    $penalty_paid = $penalty_paid + $penalty_due;
                                    $payments = $payments - $penalty_due;
                                } else {
                                    if ($penalty_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->penalty_paid = $schedule->penalty_paid + $payments;
                                        $schedule->save();
                                        $penalty_paid = $penalty_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($fees_due) && $fees_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->fees_paid = $schedule->fees_paid + $fees_due;
                                    $schedule->save();
                                    $fees_paid = $fees_paid + $fees_due;
                                    $payments = $payments - $fees_due;
                                } else {
                                    if ($fees_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->fees_paid = $schedule->fees_paid + $payments;
                                        $schedule->save();
                                        $fees_paid = $fees_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($interest_due) && $interest_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->interest_paid = $schedule->interest_paid + $interest_due;
                                    $schedule->save();
                                    $interest_paid = $interest_paid + $interest_due;
                                    $payments = $payments - $interest_due;

                                } else {
                                    if ($interest_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->interest_paid = $schedule->interest_paid + $payments;
                                        $schedule->save();
                                        $interest_paid = $interest_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($principal_due) && $principal_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->principal_paid = $schedule->principal_paid + $principal_due;
                                    $schedule->save();
                                    $principal_paid = $principal_paid + $principal_due;
                                    $payments = $payments - $principal_due;
                                } else {
                                    if ($principal_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->principal_paid = $schedule->principal_paid + $payments;
                                        $schedule->save();
                                        $principal_paid = $principal_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                            }
                            if ($loan_product->loan_transaction_strategy == "principal_interest_penalty_fees") {
                                if ($payments > ($principal_due) && $principal_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->principal_paid = $schedule->principal_paid + $principal_due;
                                    $schedule->save();
                                    $principal_paid = $principal_paid + $principal_due;
                                    $payments = $payments - $principal_due;
                                } else {
                                    if ($principal_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->principal_paid = $schedule->principal_paid + $payments;
                                        $schedule->save();
                                        $principal_paid = $principal_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($interest_due) && $interest_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->interest_paid = $schedule->interest_paid + $interest_due;
                                    $schedule->save();
                                    $interest_paid = $interest_paid + $interest_due;
                                    $payments = $payments - $interest_due;

                                } else {
                                    if ($interest_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->interest_paid = $schedule->interest_paid + $payments;
                                        $schedule->save();
                                        $interest_paid = $interest_paid + $payments;
                                        $payments = 0;
                                    }
                                }

                                if ($payments > ($penalty_due) && $penalty_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->penalty_paid = $schedule->penalty_paid + $penalty_due;
                                    $schedule->save();
                                    $penalty_paid = $penalty_paid + $penalty_due;
                                    $payments = $payments - $penalty_due;
                                } else {
                                    if ($penalty_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->penalty_paid = $schedule->penalty_paid + $payments;
                                        $schedule->save();
                                        $penalty_paid = $penalty_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($fees_due) && $fees_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->fees_paid = $schedule->fees_paid + $fees_due;
                                    $schedule->save();
                                    $fees_paid = $fees_paid + $fees_due;
                                    $payments = $payments - $fees_due;
                                } else {
                                    if ($fees_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->fees_paid = $schedule->fees_paid + $payments;
                                        $schedule->save();
                                        $fees_paid = $fees_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                            }
                            if ($loan_product->loan_transaction_strategy == "interest_principal_penalty_fees") {

                                if ($payments > ($interest_due) && $interest_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->interest_paid = $schedule->interest_paid + $interest_due;
                                    $schedule->save();
                                    $interest_paid = $interest_paid + $interest_due;
                                    $payments = $payments - $interest_due;

                                } else {
                                    if ($interest_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->interest_paid = $schedule->interest_paid + $payments;
                                        $schedule->save();
                                        $interest_paid = $interest_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($principal_due) && $principal_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->principal_paid = $schedule->principal_paid + $principal_due;
                                    $schedule->save();
                                    $principal_paid = $principal_paid + $principal_due;
                                    $payments = $payments - $principal_due;
                                } else {
                                    if ($principal_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->principal_paid = $schedule->principal_paid + $payments;
                                        $schedule->save();
                                        $principal_paid = $principal_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($penalty_due) && $penalty_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->penalty_paid = $schedule->penalty_paid + $penalty_due;
                                    $schedule->save();
                                    $penalty_paid = $penalty_paid + $penalty_due;
                                    $payments = $payments - $penalty_due;
                                } else {
                                    if ($penalty_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->penalty_paid = $schedule->penalty_paid + $payments;
                                        $schedule->save();
                                        $penalty_paid = $penalty_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                                if ($payments > ($fees_due) && $fees_due > 0) {
                                    $schedule = LoanRepaymentSchedule::find($skey->id);
                                    $schedule->fees_paid = $schedule->fees_paid + $fees_due;
                                    $schedule->save();
                                    $fees_paid = $fees_paid + $fees_due;
                                    $payments = $payments - $fees_due;
                                } else {
                                    if ($fees_due > 0) {
                                        $schedule = LoanRepaymentSchedule::find($skey->id);
                                        $schedule->fees_paid = $schedule->fees_paid + $payments;
                                        $schedule->save();
                                        $fees_paid = $fees_paid + $payments;
                                        $payments = 0;
                                    }
                                }
                            }
                        }
                    }
                    if ($payments <= 0) {
                        break;
                    }
                }
                if ($payments <= 0) {
                    break;
                }
            }
            if ($key->date >= $loan_transaction->date && $key->id >= $loan_transaction->id) {
                array_push($to_update, $key->id);
            } else {

            }
            $transaction = LoanTransaction::find($key->id);
            $transaction->principal_derived = $principal_paid;
            $transaction->interest_derived = $interest_paid;
            $transaction->fees_derived = $fees_paid;
            $transaction->penalty_derived = $penalty_paid;
            $transaction->unrecognized_income_derived = $payments;
            $transaction->save();
        }
        //update journal transactions
        foreach ($to_update as $key) {

            if ($key != $loan_transaction->id) {
                foreach (GlJournalEntry::where('loan_transaction_id', $key->id)->where('loan_id',
                    $loan_transaction->loan_id)->where('transaction_type', 'repayment')->get() as $ky) {
                    $journal = GlJournalEntry::find($ky->id);
                    if ($ky->debit > $ky->credit) {
                        $journal->credit = $journal->debit;
                    } else {
                        $journal->debit = $journal->credit;
                    }
                    $journal->reversed = 1;
                    $journal->save();
                }
                //reverse existing transaction
                $transaction = LoanTransaction::find($key);
                $transaction->reversible = 0;
                $transaction->reversed = 1;
                $transaction->reversal_type = "system";
                $transaction->debit = $transaction->credit;
                $transaction->save();

                //new transaction
                $new_transaction = new LoanTransaction();
                $new_transaction->created_by_id = $transaction->created_by_id;
                $new_transaction->office_id = $transaction->office_id;
                $new_transaction->loan_id = $transaction->id;
                $new_transaction->reversible = 1;
                $new_transaction->payment_detail_id = $transaction->id;
                $new_transaction->transaction_type = "repayment";
                $new_transaction->receipt = $transaction->receipt;
                $new_transaction->date = $transaction->date;
                $date = explode('-', $transaction->date);
                $new_transaction->year = $date[0];
                $new_transaction->month = $date[1];
                $new_transaction->credit = $transaction->credit;
                $new_transaction->notes = $transaction->notes;
                $new_transaction->principal_derived = $transaction->principal_paid;
                $new_transaction->interest_derived = $transaction->interest_paid;
                $new_transaction->fees_derived = $transaction->fees_paid;
                $new_transaction->penalty_derived = $transaction->penalty_paid;
                $new_transaction->unrecognized_income_derived = $transaction->payments;
                $new_transaction->save();
            } else {
                //new transaction
                $new_transaction = LoanTransaction::find($loan_transaction->id);
            }
            //update journals
            if ($loan_product->accounting_rule != "none") {
                //principal
                if ($new_transaction->principal_derived > 0) {
                    if (!empty($loan->loan_product->gl_account_loan_portfolio)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_loan_portfolio->id;
                        $journal->date = $new_transaction->date;
                        $date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->transaction_sub_type = 'repayment_principal';
                        $journal->name = "Principal Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->credit = $new_transaction->principal_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_fund_source)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_fund_source->id;
                        $journal->date = $new_transaction->date;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->name = "Principal Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->debit = $new_transaction->principal_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                }
                //interest
                if ($new_transaction->interest_derived > 0) {
                    if (!empty($loan->loan_product->gl_account_income_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_interest->id;
                        $journal->date = $new_transaction->date;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->transaction_sub_type = 'repayment_interest';
                        $journal->name = "Interest Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->credit = $new_transaction->interest_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_receivable_interest)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_receivable_interest->id;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->name = "Interest Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->debit = $new_transaction->interest_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                }
                //fees
                if ($new_transaction->fees_derived > 0) {
                    if (!empty($loan->loan_product->gl_account_income_fee)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_fee->id;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->transaction_sub_type = 'repayment_fees';
                        $journal->name = "Fees Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->credit = $new_transaction->fees_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_receivable_fee)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_receivable_fee->id;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->name = "Fees Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->debit = $new_transaction->fees_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                }
                if ($new_transaction->penalty_derived > 0) {
                    if (!empty($loan->loan_product->gl_account_income_penalty)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_income_penalty->id;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->transaction_sub_type = 'repayment_penalty';
                        $journal->name = "Penalty Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->credit = $new_transaction->penalty_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                    if (!empty($loan->loan_product->gl_account_receivable_penalty)) {
                        $journal = new GlJournalEntry();
                        $journal->office_id = $loan->office_id;
                        $journal->currency_id = $loan->currency_id;
                        $journal->gl_account_id = $loan->loan_product->gl_account_receivable_penalty->id;
                        $date = $new_transaction->date;
                        $journal->date = $new_transaction->date;
                        $journal->year = $date[0];
                        $journal->month = $date[1];
                        $journal->transaction_type = 'repayment';
                        $journal->name = "Penalty Repayment";
                        $journal->loan_id = $loan->id;
                        $journal->loan_transaction_id = $new_transaction->id;
                        $journal->debit = $new_transaction->penalty_derived;
                        $journal->reference = $new_transaction->id;
                        $journal->save();
                    }
                }
            }

        }
    }
}
