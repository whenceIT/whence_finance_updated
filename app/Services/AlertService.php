<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Client;
use App\Models\LoanTransaction;
use App\Models\User;
use App\Models\Office;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use OwenIt\Auditing\Models\Audit;

class AlertService
{
    /**
     * Run all fraud rules and persist new alerts.
     * Returns the total number of new alerts created this run.
     */
    public static function runAll(): int
    {
        $created = 0;
        $created += self::detectAfterHoursLogins();
        $created += self::detectDuplicateNrc();
        $created += self::detectNullCriticalFields();
        $created += self::detectBlacklistedClients();
        $created += self::detectSuspiciousReversals();
        $created += self::detectAfterHoursTransactions();
        $created += self::detectRecoveryTransactions();
        $created += self::detectStaffPayments();
        $created += self::detectBlockedOrNo2FAUsers();
        return $created;
    }

    /**
     * Deduplicate: skip if an identical alert was created within the last 5 minutes.
     */
    protected static function maybeCreate(array $data): bool
    {
        $recent = Alert::where('rule', $data['rule'])
            ->where('reference_id', $data['reference_id'] ?? null)
            ->where('created_at', '>=', Carbon::now()->subMinutes(5))
            ->exists();

        if ($recent) {
            return false;
        }

        Alert::create($data);
        Log::info('Fraud alert created', ['rule' => $data['rule'], 'reference_id' => $data['reference_id'] ?? null]);
        return true;
    }

    // ── RULE 1: After-hours LMS / system logins ──────────────────────────────

    protected static function detectAfterHoursLogins(): int
    {
        $created = 0;
        $made = 0;
        // Business hours: 06:00 – 19:00
        $cutoffStart = '06:00:00';
        $cutoffEnd   = '19:00:00';

        $logs = Audit::with('user')
            ->where(function ($q) use ($cutoffStart, $cutoffEnd) {
                $q->whereRaw('TIME(created_at) < ?', [$cutoffStart])
                  ->orWhereRaw('TIME(created_at) >= ?', [$cutoffEnd]);
            })
            ->get();

        foreach ($logs as $log) {
            if ($made >= 5) break;
            if ((int) self::maybeCreate([
                'rule'         => 'after_hours_login',
                'severity'     => 'critical',
                'title'        => 'After-hours system login detected',
                'description'  => sprintf(
                    'User "%s" logged in outside business hours (06:00–19:00) at %s.',
                    $log->user->name ?? ('User #' . $log->user_id),
                    $log->created_at
                ),
                'reference_id' => $log->user_id,
                'meta'         => ['user_id' => $log->user_id, 'login_time' => $log->created_at],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 2: Duplicate NRC numbers among active clients ───────────────────

    protected static function detectDuplicateNrc(): int
    {
        $created = 0;
        $made = 0;

        $dupes = Client::select('nrc_number')
            ->where('nrc_number', '<>', '')
            ->whereNotNull('nrc_number')
            ->where('status', 'active')
            ->groupBy('nrc_number')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($dupes as $row) {
            if ($made >= 5) break;
            $clients = Client::where('nrc_number', $row->nrc_number)
                ->with('office')
                ->get();

            $names = $clients->map(function ($c) {
                return ($c->office ? $c->office->name : 'Unknown') . ' / ' . trim(($c->first_name ?? '') . ' ' . ($c->last_name ?? ''));
            })->implode('; ');

            if ((int) self::maybeCreate([
                'rule'         => 'duplicate_nrc',
                'severity'     => 'critical',
                'title'        => 'Duplicate NRC number detected',
                'description'  => sprintf(
                    'NRC "%s" is shared by %d active client(s): %s. Possible identity fraud.',
                    $row->nrc_number,
                    $clients->count(),
                    $names
                ),
                'reference_id' => $clients->first()->id ?? null,
                'meta'         => ['nrc_number' => $row->nrc_number, 'count' => $clients->count()],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 3: Null / empty critical client fields ──────────────────────────

    protected static function detectNullCriticalFields(): int
    {
        $created = 0;
        $made = 0;

        // Staff-linked clients: null staff_id means unassigned
        $orphanClients = Client::where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('first_name')
                  ->orWhere('first_name', '')
                  ->orWhereNull('last_name')
                  ->orWhere('last_name', '')
                  ->orWhereNull('nrc_number')
                  ->orWhere('nrc_number', '')
                  ->orWhereNull('phone')
                  ->orWhere('phone', '')
                  ->orWhereNull('mobile')
                  ->orWhere('mobile', '');
            })
            ->with('office')
            ->get();

        foreach ($orphanClients as $client) {
            if ($made >= 5) break;
            $missing = [];
            foreach (['first_name', 'last_name', 'nrc_number', 'phone', 'mobile'] as $field) {
                if (empty($client->$field)) {
                    $missing[] = $field;
                }
            }

            if ((int) self::maybeCreate([
                'rule'         => 'null_critical_fields',
                'severity'     => 'warning',
                'title'        => 'Active client with incomplete mandatory fields',
                'description'  => sprintf(
                    'Client "%s" (ID:%d, %s) is missing: %s. File is incomplete and cannot be verified.',
                    trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                    $client->id,
                    $client->office->name ?? 'Unknown',
                    implode(', ', $missing)
                ),
                'reference_id' => $client->id,
                'meta'         => ['client_id' => $client->id, 'missing' => $missing],
            ])) {
                $created++;
                $made++;
            }
        }

        // Soft-deleted clients still linked to active loans
        $deletedClients = Client::onlyTrashed()
            ->whereHas('loans', function ($q) {
                $q->whereIn('status', ['disbursed', 'pending', 'approved']);
            })
            ->with(['office', 'loans'])
            ->get();

        foreach ($deletedClients as $client) {
            if ($made >= 5) break;
            if ((int) self::maybeCreate([
                'rule'         => 'deleted_client_active_loan',
                'severity'     => 'critical',
                'title'        => 'Soft-deleted client still has active loan',
                'description'  => sprintf(
                    'Client "%s" (ID:%d, %s) was deleted but has an active %s loan (ID:%d). Possible data tampering.',
                    trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                    $client->id,
                    $client->office->name ?? 'Unknown',
                    $client->loans->first()->status ?? 'unknown',
                    $client->loans->first()->id ?? '?'
                ),
                'reference_id' => $client->id,
                'meta'         => ['client_id' => $client->id, 'loan_id' => $client->loans->first()->id ?? null],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 4: Blacklisted clients still active in the system ───────────────

    protected static function detectBlacklistedClients(): int
    {
        $created = 0;
        $made = 0;

        $blacklisted = Client::where('blacklisted', 1)
            ->with(['office'])
            ->get();

        foreach ($blacklisted as $client) {
            if ($made >= 5) break;
            if ((int) self::maybeCreate([
                'rule'         => 'blacklisted_client',
                'severity'     => 'warning',
                'title'        => 'Blacklisted client in system',
                'description'  => sprintf(
                    'Client "%s" (ID:%d, %s — %s) is blacklisted (since %s). Verify no new activity.',
                    trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? '')),
                    $client->id,
                    $client->office->name ?? 'Unknown',
                    $client->nrc_number ?? 'N/A',
                    $client->date_blacklisted ?? 'unknown date'
                ),
                'reference_id' => $client->id,
                'meta'         => ['client_id' => $client->id, 'blacklisted_date' => $client->date_blacklisted],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 5: Suspiciously reversed transactions ───────────────────────────

    protected static function detectSuspiciousReversals(): int
    {
        $created = 0;
        $made = 0;

        $reversed = LoanTransaction::with(['loan.client', 'office', 'created_by'])
            ->where('reversed', 1)
            ->where('reversal_type', 'user') // user-initiated reversals = higher risk
            ->get();

        foreach ($reversed as $tx) {
            if ($made >= 5) break;
            $clientName = $tx->loan->client
                ? trim(($tx->loan->client->first_name ?? '') . ' ' . ($tx->loan->client->last_name ?? ''))
                : 'Unknown';

            if ((int) self::maybeCreate([
                'rule'         => 'suspicious_reversal',
                'severity'     => 'critical',
                'title'        => 'User-initiated transaction reversal',
                'description'  => sprintf(
                    'Transaction #%d (amount: %.2f, type: %s) for client "%s" at %s was reversed by user "%s".',
                    $tx->id,
                    $tx->amount ?? 0,
                    $tx->transaction_type ?? 'unknown',
                    $clientName,
                    $tx->office->name ?? 'Unknown',
                    $tx->created_by->full_name ?? ('User #' . $tx->created_by_id)
                ),
                'reference_id' => $tx->loan_id,
                'meta'         => [
                    'transaction_id'  => $tx->id,
                    'loan_id'         => $tx->loan_id,
                    'amount'          => $tx->amount ?? 0,
                    'type'            => $tx->transaction_type,
                    'reversed_by'     => $tx->created_by_id,
                ],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 6: After-hours loan transactions ───────────────────────────────

    protected static function detectAfterHoursTransactions(): int
    {
        $created = 0;
        $made = 0;
        $cutoffStart = '06:00:00';
        $cutoffEnd   = '19:00:00';

        // Look at recent transactions in the past 7 days
        $txs = LoanTransaction::with(['loan.client', 'office', 'created_by'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        foreach ($txs as $tx) {
            if ($made >= 5) break;
            $time = $tx->created_at ? $tx->created_at->format('H:i:s') : '';
            if ($time === '' || !self::isAfterHours($time, $cutoffStart, $cutoffEnd)) {
                continue;
            }

            $clientName = $tx->loan->client
                ? trim(($tx->loan->client->first_name ?? '') . ' ' . ($tx->loan->client->last_name ?? ''))
                : 'Unknown';

            if ((int) self::maybeCreate([
                'rule'         => 'after_hours_transaction',
                'severity'     => 'warning',
                'title'        => 'After-hours loan transaction',
                'description'  => sprintf(
                    'Transaction #%d (amount: %.2f, type: %s) by "%s" for client "%s" at %s occurred outside business hours.',
                    $tx->id,
                    $tx->amount ?? 0,
                    $tx->transaction_type ?? 'unknown',
                    $tx->created_by->full_name ?? ('User #' . $tx->created_by_id),
                    $clientName,
                    $tx->office->name ?? 'Unknown'
                ),
                'reference_id' => $tx->loan_id,
                'meta'         => ['transaction_id' => $tx->id, 'time' => $time],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 7: Recovery transactions flagged in loan_transactions ──────────

    protected static function detectRecoveryTransactions(): int
    {
        $created = 0;
        $made = 0;

        $recoveries = LoanTransaction::with(['loan.client', 'office'])
            ->where('is_recovery', 1)
            ->orWhere('recovery', 1)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        foreach ($recoveries as $tx) {
            if ($made >= 5) break;
            $clientName = $tx->loan->client
                ? trim(($tx->loan->client->first_name ?? '') . ' ' . ($tx->loan->client->last_name ?? ''))
                : 'Unknown';

            if ((int) self::maybeCreate([
                'rule'         => 'recovery_transaction',
                'severity'     => 'info',
                'title'        => 'Recovery payment recorded',
                'description'  => sprintf(
                    'Recovery transaction #%d (amount: %.2f, type: %s) for client "%s" at %s.',
                    $tx->id,
                    $tx->amount ?? 0,
                    $tx->transaction_type ?? 'unknown',
                    $clientName,
                    $tx->office->name ?? 'Unknown'
                ),
                'reference_id' => $tx->loan_id,
                'meta'         => ['transaction_id' => $tx->id, 'amount' => $tx->amount ?? 0],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 8: Transactions linked to staff mobile numbers ─────────────────

    protected static function detectStaffPayments(): int
    {
        $created = 0;
        $made = 0;

        // Build a lowercase set of all staff phone/mobile numbers
        $staffPhones = User::where('status', 'active')
            ->whereNotNull('phone')
            ->where('phone', '<>', '')
            ->pluck('phone')
            ->map(fn($p) => strtolower(trim($p)))
            ->unique()
            ->all();

        $staffMobiles = User::where('status', 'active')
            ->whereNotNull('mobile_number')
            ->where('mobile_number', '<>', '')
            ->pluck('mobile_number')
            ->map(fn($p) => strtolower(trim($p)))
            ->unique()
            ->all();

        $staffNumbers = array_unique(array_merge($staffPhones, $staffMobiles));
        if (empty($staffNumbers)) {
            return 0;
        }

        $txs = LoanTransaction::with(['loan.client', 'office', 'payment_detail'])
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get();

        foreach ($txs as $tx) {
            if ($made >= 5) break;
            $phoneField = strtolower($tx->payment_detail->phone ?? '');
            if ($phoneField === '' || !in_array($phoneField, $staffNumbers, true)) {
                continue;
            }

            $staffName = User::where(function ($q) use ($phoneField) {
                $q->whereRaw('LOWER(phone) = ?', [$phoneField])
                  ->orWhereRaw('LOWER(mobile_number) = ?', [$phoneField]);
            })->first();

            if ((int) self::maybeCreate([
                'rule'         => 'staff_linked_payment',
                'severity'     => 'critical',
                'title'        => 'Payment linked to staff phone number',
                'description'  => sprintf(
                    'Transaction #%d (amount: %.2f, type: %s) references a client phone number that also matches staff member "%s (%s)". Possible fraudulent payment diversion.',
                    $tx->id,
                    $tx->amount ?? 0,
                    $tx->transaction_type ?? 'unknown',
                    $staffName->full_name ?? 'Unknown staff',
                    $phoneField
                ),
                'reference_id' => $tx->loan_id,
                'meta'         => [
                    'transaction_id' => $tx->id,
                    'matched_number' => $phoneField,
                    'staff_id'       => $staffName->id ?? null,
                ],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── RULE 9: Users with blocked accounts or disabled 2FA ─────────────────

    protected static function detectBlockedOrNo2FAUsers(): int
    {
        $created = 0;
        $made = 0;

        $blocked = User::where('blocked', 1)
            ->where('status', 'active')
            ->get();

        foreach ($blocked as $user) {
            if ($made >= 5) break;
            if ((int) self::maybeCreate([
                'rule'         => 'blocked_active_user',
                'severity'     => 'warning',
                'title'        => 'Blocked user account still active',
                'description'  => sprintf(
                    'User "%s" (ID:%d, %s) is blocked but account status is still active.',
                    trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    $user->id,
                    $user->office ? $user->office->name : 'Unknown'
                ),
                'reference_id' => $user->id,
                'meta'         => ['user_id' => $user->id, 'blocked' => true],
            ])) {
                $created++;
                $made++;
            }
        }

        // Users with high-risk roles who have 2FA disabled
        $no2fa = User::where('enable_google2fa', 0)
            ->where('status', 'active')
            ->get();

        foreach ($no2fa as $user) {
            if ($made >= 5) break;
            // Only flag senior roles
            $roleIds = \App\Helpers\GeneralHelper::mergedRoleIds(
                'role.exec', 'role.risk', 'role.poa', 'role.ma', 'role.chair'
            );
            if (!in_array((string) $user->id, $roleIds, true)) {
                continue;
            }

            if ((int) self::maybeCreate([
                'rule'         => 'senior_user_no_2fa',
                'severity'     => 'warning',
                'title'        => 'Senior user without two-factor authentication',
                'description'  => sprintf(
                    'User "%s" (ID:%d, %s) holds a senior role but has 2FA disabled.',
                    trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
                    $user->id,
                    $user->office ? $user->office->name : 'Unknown'
                ),
                'reference_id' => $user->id,
                'meta'         => ['user_id' => $user->id, 'two_factor_enabled' => false],
            ])) {
                $created++;
                $made++;
            }
        }

        return $created;
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    /**
     * Return true if $time (H:i:s string) falls outside [$start, $end].
     */
    protected static function isAfterHours(string $time, string $start, string $end): bool
    {
        $seconds = strtotime($time);
        if ($seconds === false) {
            return false;
        }
        $s = strtotime($start);
        $e = strtotime($end);
        return $seconds < $s || $seconds >= $e;
    }
}
