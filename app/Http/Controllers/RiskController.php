<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use App\Models\Province;
use Carbon\Carbon;
use App\Services\AlertService;
use Illuminate\Support\Facades\DB;
use App\Models\Deposit;

class RiskController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }
    
    public function overview(Request $request)
    {
        $sectionShorts  = config('risk-audit.section_names', [
            0 => 'Admin', 1 => 'Wallet', 2 => 'Loans', 3 => 'Collections',
            4 => 'Fraud', 5 => 'Staff', 6 => 'Systems', 7 => 'Reporting', 8 => 'Conclusion',
        ]);

        $sectionItemCounts = config('risk-audit.section_item_counts', [
            0 => 0, 1 => 10, 2 => 7, 3 => 6, 4 => 6, 5 => 7, 6 => 8, 7 => 6, 8 => 2,
        ]);

        $ratingConfig = [
            'low'     => ['label' => '🟢 LOW',      'color' => '#27ae60', 'bg' => '#eafaf1', 'badge' => 'success'],
            'medium'  => ['label' => '🟡 MODERATE', 'color' => '#f39c12', 'bg' => '#fef9e7', 'badge' => 'warning'],
            'high'    => ['label' => '🔴 HIGH',     'color' => '#e74c3c', 'bg' => '#fdedec', 'badge' => 'danger' ],
            'critical'=> ['label' => '🚨 CRITICAL', 'color' => '#7b241c', 'bg' => '#f9ebea', 'badge' => 'danger' ],
            'pending' => ['label' => '⚪ PENDING',  'color' => '#7f8c8d', 'bg' => '#f3f3f3', 'badge' => 'default'],
        ];

        $today = Carbon::today();

        $filterStart = $request->filled('filter_start') ? Carbon::createFromFormat('Y-m-d', $request->input('filter_start'))->startOfDay() : null;
        $filterEnd   = $request->filled('filter_end')   ? Carbon::createFromFormat('Y-m-d', $request->input('filter_end'))->endOfDay() : null;

        $submissionsQuery = \App\Models\AuditSubmission::with('office', 'auditor');

        if ($filterStart) {
            $submissionsQuery->where('created_at', '>=', $filterStart);
        }
        if ($filterEnd) {
            $submissionsQuery->where('created_at', '<=', $filterEnd);
        }

        $submissions = $submissionsQuery->latest()->take(50)->get();

        $branches = $submissions->map(function ($sub) use ($sectionShorts, $sectionItemCounts, $today, $ratingConfig) {
            $sections  = [];
            $failCount = 0;

            foreach ($sectionShorts as $i => $short) {
                if ($i === 0) {
                    $sections[] = ['pass' => 0, 'fail' => 0, 'na' => 0];
                    continue;
                }

                $s      = $i + 1;
                $pass   = $fail = $na = 0;
                $itemCount = $sectionItemCounts[$i] ?? 0;

                for ($j = 1; $j <= $itemCount; $j++) {
                    $field = "s{$s}_{$j}";
                    $value = $sub->$field;

                    if ($i === 4) {
                        if ($value === 'not_present') $pass++;
                        elseif ($value === 'present') $fail++;
                    } else {
                        if ($value === 'pass')  $pass++;
                        elseif ($value === 'fail') $fail++;
                        elseif ($value === 'na')   $na++;
                    }
                }

                $sections[]  = ['pass' => $pass, 'fail' => $fail, 'na' => $na];
                $failCount  += $fail;
            }

            $ratingKey = trim((string) ($sub->risk_rating ?? '')) ?: 'pending';
            $scheduled = $sub->audit_date && $sub->audit_date->gt($today);
            $complete  = !$scheduled && $ratingKey !== 'pending';

            return [
                'submission_id'    => $sub->id,
                'name'             => $sub->office->name ?? 'Unknown',
                'code'             => $sub->office->external_id ?? '',
                'audit_date'       => $sub->audit_date ? $sub->audit_date->format('d M Y') : 'Unknown',
                'audit_date_human' => $sub->audit_date ? $sub->audit_date->diffForHumans() : 'Unknown',
                'last_audit'       => $sub->created_at->format('d M Y'),
                'created_at'       => $sub->created_at->format('d M Y'),
                'created_at_human' => $sub->created_at->diffForHumans(),
                'auditor'          => $sub->auditor_name,
                'audit_type'       => $sub->audit_type,
                'opening_remarks'  => $sub->opening_remarks,
                'unannounced'      => $sub->unannounced,
                'fail_count'       => $failCount,
                'rating'           => $ratingKey,
                'is_complete'      => $complete,
                'is_scheduled'     => $scheduled,
                'sections'         => $sections,
            ];
        })->toArray();

        $completeAudits   = array_values(array_filter($branches, fn($b) => $b['is_complete'] && !$b['is_scheduled']));
        $scheduledAudits  = array_values(array_filter($branches, fn($b) => $b['is_scheduled']));
        $incompleteAudits = array_values(array_filter($branches, fn($b) => !$b['is_complete'] && !$b['is_scheduled']));

        // Group complete audits by month-year (newest first)
        $grouped = [];
        foreach ($completeAudits as $branch) {
            $dt  = Carbon::parse($branch['created_at']);
            $key = $dt->format('F Y');
            $grouped[$key][] = $branch;
        }
        krsort($grouped);

        // Offices never audited
        $auditedOfficeIds  = $submissions->pluck('office_id')->unique()->toArray();
        $unauditedOffices  = \App\Models\Office::with(['province', 'district', 'manager'])
            ->whereNotIn('id', $auditedOfficeIds)
            ->where('active', 1)
            ->orderBy('name')
            ->get();

        // Summary counts per rating
        $counts = ['low' => 0, 'medium' => 0, 'high' => 0, 'critical' => 0, 'pending' => 0];
        foreach ($branches as $b) {
            if (isset($counts[$b['rating']])) $counts[$b['rating']]++;
        }

        return view('risk.overview', compact(
            'sectionShorts', 'sectionItemCounts', 'ratingConfig',
            'branches', 'completeAudits', 'grouped', 'scheduledAudits',
            'incompleteAudits', 'unauditedOffices', 'counts',
            'filterStart', 'filterEnd'
        ));
    }

    public function auditTrail()
    {
        return redirect()->route('audits.index');
    }

    public function branchRanking(Request $request)
    {
        $ratingConfig = [
            'low'     => ['label' => 'LOW',      'hex' => '#27ae60'],
            'medium'  => ['label' => 'MODERATE', 'hex' => '#f39c12'],
            'high'    => ['label' => 'HIGH',     'hex' => '#e74c3c'],
            'critical'=> ['label' => 'CRITICAL', 'hex' => '#7b241c'],
            'pending' => ['label' => 'NO DATA',  'hex' => '#95a5a6'],
        ];

        $provinceFilter = trim((string) $request->input('province', ''));
        $searchTerm     = trim((string) $request->input('search', ''));

        // ── 1. Latest completed submission per office (by audit_date DESC, id DESC) ──
        $latestSubs = \App\Models\AuditSubmission::query()
            ->selectRaw('audit_submissions.*')
            ->joinSub(function ($q) {
                $q->selectRaw('MAX(a2.id) as id')
                  ->from('audit_submissions as a2')
                  ->whereNotNull('a2.office_id')
                  ->joinSub(function ($q2) {
                      $q2->selectRaw('office_id, MAX(audit_date) as max_ad')
                        ->from('audit_submissions')
                        ->whereNotNull('office_id')
                        ->groupBy('office_id');
                  }, 'mx2', function ($j) {
                      $j->on('a2.office_id', '=', 'mx2.office_id')
                        ->on('a2.audit_date', '=', 'mx2.max_ad');
                  })
                  ->groupBy('a2.office_id');
            }, 'mx', function ($j) {
                $j->on('audit_submissions.id', '=', 'mx.id');
            })
            ->get();

        $subMap = [];
        foreach ($latestSubs as $sub) {
            $subMap[$sub->office_id] = $sub;
        }

        // ── 2. All active offices with province/district ──────────────────────
        $officesQuery = \App\Models\Office::with(['province', 'district', 'manager'])
            ->where('active', 1)
            ->orderBy('name');

        if ($provinceFilter !== '') {
            $officesQuery->whereHas('province', function ($q) use ($provinceFilter) {
                $q->where('name', $provinceFilter);
            });
        }

        if ($searchTerm !== '') {
            $officesQuery->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('external_id', 'like', "%{$searchTerm}%");
            });
        }

        $allOffices = $officesQuery->get();

        // ── 3. Weighted scoring per office ────────────────────────────────────
        // Category weights based on actual audit_submissions table columns
        $weights = [
            // Section 2 – Wallet / Digital Payment Controls (10 items × 5 = 50)
            's2' => ['weight' => 5, 'count' => 10],
            // Section 3 – Loan Portfolio (7 items × 8 = 56)
            's3' => ['weight' => 8, 'count' => 7],
            // Section 4 – Collections (6 items × 6 = 36)
            's4' => ['weight' => 6, 'count' => 6],
            // Section 5 – Fraud Indicators (6 items × 10 = 60)
            's5' => ['weight' => 10, 'count' => 6],
            // Section 6 – Staff & Process (8 items × 3 = 24)
            's6' => ['weight' => 3, 'count' => 8],
            // Section 7 – System & Control (6 items × 4 = 24)
            's7' => ['weight' => 4, 'count' => 6],
            // Section 8 – Reporting & Governance (6 items × 5 = 30)
            's8' => ['weight' => 5, 'count' => 6],
        ];

        $rows = [];
        foreach ($allOffices as $office) {
            $sub     = $subMap[$office->id] ?? null;
            $score   = 0;
            $factors = [];
            $fails   = 0;

            if ($sub) {
                foreach ($weights as $sec => $cfg) {
                    $secFails = 0;
                    for ($i = 1; $i <= $cfg['count']; $i++) {
                        $field = "{$sec}_{$i}";
                        if (($sub->$field ?? null) === 'fail') {
                            $secFails++;
                        }
                    }
                    $score += $secFails * $cfg['weight'];
                    if ($secFails > 0) {
                        $factors[] = strtoupper($sec) . "({$secFails})";
                    }
                }

                // Conclude from overall fail_count
                $fails  = (int) ($sub->fail_count ?? 0);
                $score += $fails;

                if ($fails >= 13) {
                    $rating = 'critical';
                } elseif ($fails >= 8) {
                    $rating = 'high';
                } elseif ($fails >= 4) {
                    $rating = 'medium';
                } else {
                    $rating = 'low';
                }
            } else {
                $rating = 'pending';
            }

            $rows[] = [
                'office'   => $office,
                'province' => $office->province->name ?? '—',
                'district' => $office->district->name ?? '—',
                'score'    => $score,
                'rating'   => $rating,
                'fails'    => $fails,
                'factors'  => $factors,
                'sub'      => $sub,
            ];
        }

        // Sort lowest-risk first (pending -> low -> medium -> high -> critical)
        $gradeOrder = ['pending'=>0, 'low'=>1, 'medium'=>2, 'high'=>3, 'critical'=>4];
        usort($rows, function ($a, $b) use ($gradeOrder) {
            $ra = $gradeOrder[$a['rating']] ?? 0;
            $rb = $gradeOrder[$b['rating']] ?? 0;
            if ($ra !== $rb) return $ra <=> $rb;
            if ($b['score'] === $a['score']) return $a['office']->name <=> $b['office']->name;
            return $a['score'] <=> $b['score'];
        });

        // Attach rank
        foreach ($rows as $idx => &$row) { $row['rank'] = $idx + 1; }
        unset($row);

        // ── 4. Summary stats ──────────────────────────────────────────────────
        $summary = ['critical' => 0, 'high' => 0, 'medium' => 0, 'low' => 0];
        foreach ($rows as $row) {
            if (isset($summary[$row['rating']])) { $summary[$row['rating']]++; }
        }

        return view('risk.branch-ranking', [
            'rows'           => $rows,
            'ratingConfig'   => $ratingConfig,
            'summary'        => $summary,
            'totalBranches'  => count($rows),
            'provinces'      => \App\Models\Province::orderBy('name')->pluck('name')->all(),
            'filters'        => [
                'province' => $provinceFilter,
                'search'   => $searchTerm,
            ],
        ]);
    }

    public function heatMap()
    {
        $ratingConfig = [
            'low'     => ['label' => 'LOW',      'hex' => '#27ae60', 'scale' => '#a8e6cf'],
            'medium'  => ['label' => 'MODERATE', 'hex' => '#f39c12', 'scale' => '#ffd27a'],
            'high'    => ['label' => 'HIGH',     'hex' => '#e74c3c', 'scale' => '#f5abab'],
            'critical'=> ['label' => 'CRITICAL', 'hex' => '#7b241c', 'scale' => '#d97c7c'],
            'pending' => ['label' => 'NO DATA',  'hex' => '#95a5a6', 'scale' => '#d5dbdb'],
        ];

        // Latest audit submission per office (by audit_date DESC, id DESC)
        $latestSubs = \App\Models\AuditSubmission::query()
            ->selectRaw('audit_submissions.*')
            ->joinSub(function ($q) {
                $q->selectRaw('MAX(a2.id) as id')
                  ->from('audit_submissions as a2')
                  ->whereNotNull('a2.office_id')
                  ->joinSub(function ($q2) {
                      $q2->selectRaw('office_id, MAX(audit_date) as max_ad')
                        ->from('audit_submissions')
                        ->whereNotNull('office_id')
                        ->groupBy('office_id');
                  }, 'mx2', function ($j) {
                      $j->on('a2.office_id', '=', 'mx2.office_id')
                        ->on('a2.audit_date', '=', 'mx2.max_ad');
                  })
                  ->groupBy('a2.office_id');
            }, 'mx', function ($j) {
                $j->on('audit_submissions.id', '=', 'mx.id');
            })
            ->get();
        $officeRatings = [];
        foreach ($latestSubs as $sub) {
            $key = strtolower(trim((string) $sub->risk_rating)) ?: 'pending';
            $officeRatings[$sub->office_id] = [
                'rating'     => $key,
                'fail_count' => (int) $sub->fail_count,
                'audit_label'=> trim($sub->audit_date . ' ' . str_replace('_',' ', $sub->audit_type ?? '')),
                'label'      => ($ratingConfig[$key]['label'] ?? 'NO DATA'),
                'hex'        => ($ratingConfig[$key]['hex']   ?? '#95a5a6'),
            ];
        }

        // All active offices with province + district, preloading latest rating
        $allOffices = \App\Models\Office::with(['province', 'district', 'manager'])
            ->where('active', 1)
            ->orderBy('name')
            ->get()
            ->groupBy('province.name');

        // Provincial summaries
        $provincial = [];
        foreach ($allOffices as $provName => $offices) {
            $ratings = [];
            foreach ($offices as $o) {
                $r = $officeRatings[$o->id]['rating'] ?? 'pending';
                $ratings[$r] = ($ratings[$r] ?? 0) + 1;
            }
            // Worst-case rating for province tint
            $worst = 'pending';
            foreach (['critical', 'high', 'medium', 'low', 'pending'] as $candidate) {
                if (!empty($ratings[$candidate])) { $worst = $candidate; break; }
            }
            $provincial[$provName ?? 'Unknown'] = [
                'offices'  => $offices,
                'ratings'  => $ratings,
                'worst'    => $worst,
                'hex'      => $ratingConfig[$worst]['hex'] ?? '#95a5a6',
                'label'    => $ratingConfig[$worst]['label'] ?? 'NO DATA',
                'bg_light' => ($ratingConfig[$worst]['scale'] ?? '#d5dbdb') . '22',
                'total'    => $offices->count(),
            ];
        }
        ksort($provincial);

        // National totals
        $totals = ['low'=>0,'medium'=>0,'high'=>0,'critical'=>0,'pending'=>0];
        foreach ($provincial as $pd) {
            foreach ($pd['ratings'] as $r => $cnt) { $totals[$r] = ($totals[$r] ?? 0) + $cnt; }
        }

        return view('risk.heat-map', [
            'provincial'    => $provincial,
            'totals'        => $totals,
            'ratingConfig'  => $ratingConfig,
            'officeRatings' => $officeRatings,
            'totalOffices'  => $allOffices->flatten()->count(),
        ]);
    }

    public function fraudFeed(Request $request)
    {
        // Run fraud rules once per page view so fresh alerts are always populated
        AlertService::runAll();
        
        if ($request->wantsJson() || $request->format === 'json') {
            $severity = (string) ($request->input('severity') ?? '');
            $unread   = (bool) $request->boolean('unread');
            $hours    = (int) ($request->input('hours') ?? 168);

            return $this->buildFeedQuery($severity, $unread, $hours);
        }

        return view('risk.fraud-feed');
    }

    /**
     * JSON API for the fraud-feed JS supervisor.
     * Accepts: severity (critical|warning|info), unread (0/1), hours (integer)
     */
    public function getFraudAlerts(Request $request)
    {
        $severity = (string) ($request->input('severity') ?? '');
        $unread   = (bool) $request->boolean('unread');
        $hours    = (int) ($request->input('hours') ?? 168);

        return $this->buildFeedQuery($severity, $unread, $hours);
    }

    /**
     * DELETE risk/fraud-alert/{id}
     * Soft-dismiss an alert (is_read = true so it stays for audit trail)
     */
    public function destroyAlert($id)
    {
        $alert = \App\Models\Alert::findOrFail($id);
        $alert->delete();   // hard delete — plain "completed/dismissed" action
        return response()->json(['success' => true]);
    }

    protected function buildFeedQuery(string $severity, bool $unread, int $hours)
    {
        $base = \App\Models\Alert::query()
            ->when($severity, fn($q) => $q->where('severity', $severity))
            ->when($unread,  fn($q) => $q->where('is_read', false))
            ->where('created_at', '>=', now()->subHours($hours));

        $alerts = (clone $base)
            ->with('creator')
            ->orderByDesc('created_at')
            ->take(200)
            ->get();

        $stats = (clone $base)
            ->selectRaw('severity, is_read, COUNT(*) as cnt')
            ->groupBy('severity', 'is_read')
            ->get()
            ->reduce(function ($acc, $row) {
                $sev = $row->severity;
                $acc[$sev] = ($acc[$sev] ?? 0) + (int) $row->cnt;
                if ($row->is_read) {
                    $acc['read'] = ($acc['read'] ?? 0) + (int) $row->cnt;
                }
                return $acc;
            }, []);

        $total = $alerts->count();

        return response()->json([
            'total'   => $total,
            'stats'   => $stats,
            'alerts'  => $alerts->map(function ($a) {
                return [
                    'id'          => $a->id,
                    'rule'        => $a->rule,
                    'severity'    => $a->severity,
                    'title'       => $a->title,
                    'description' => $a->description,
                    'reference_id'=> $a->reference_id,
                    'meta'        => $a->meta,
                    'is_read'     => (bool) $a->is_read,
                    'created_at'  => $a->created_at ? $a->created_at->format('d M Y H:i') : '',
                    'created_by'  => $a->creator ? $a->creator->full_name ?? ($a->creator->first_name . ' ' . $a->creator->last_name) : null,
                ];
            })->all(),
        ]);
    }

    public function recoveryEfficiency()
    {
        return view('risk.recovery-efficiency');
    }

    public function policyBreach()
    {
        return view('risk.policy-breach');
    }

    public function costValue()
    {
        return view('risk.cost-value');
    }

    public function geographicIntelligence()
    {
        return view('risk.geographic-intelligence');
    }

    public function escalationTracking()
    {
        return view('risk.escalation-tracking');
    }

    public function staffProfiles()
    {
        return view('risk.staff-profiles');
    }

    public function executiveSummary()
    {
        return view('risk.executive-summary');
    }

    public function decisionSla()
    {
        return view('risk.decision-sla');
    }

    public function getOfficeAuditData($officeId)
    {
        $officeId = (int) $officeId;
        $office = \App\Models\Office::findOrFail($officeId);

        // s3_total_active: office's total disbursed loans
        $totalActiveLoans = \App\Models\Loan::where('office_id', $officeId)
            ->where('status', 'disbursed')
            ->count();

        // s3_incomplete_files: Clients with empty required fields
        $incompleteFiles = \App\Models\Client::where('office_id', $officeId)
            ->where(function($q) {
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
            ->count();

        // s4_system_collections: total loan transactions for office (sum of credit for repayments)
        $systemCollections = \App\Models\LoanTransaction::where('office_id', $officeId)
            ->where('transaction_type', 'repayment')
            ->where('reversed', 0)
            ->sum('credit');

        // s4_wallet_collections: fetch from external API, default 0
        // TODO: implement external API call
        $walletCollections = 0;

        // s6_total_staff: total active users at office
        $totalStaff = \App\Models\User::where('office_id', $officeId)
            ->where('status', 'active')
            ->count();

        return response()->json([
            's3_total_active' => $totalActiveLoans,
            's3_incomplete_files' => $incompleteFiles,
            's4_system_collections' => $systemCollections,
            's4_wallet_collections' => $walletCollections,
            's6_total_staff' => $totalStaff,
        ]);
    }

    public function queryDeposits(Request $request)
    {
        $officeId = $request->query('office_id');
        $depositType = $request->query('deposit_type');
        $year = $request->query('year');

        $query = \App\Models\Deposit::query();

        if ($officeId) {
            $query->where('office', (int) $officeId);
        }
        if ($depositType) {
            $query->where('deposit_type', (int) $depositType);
        }
        if ($year) {
            $query->whereYear('date', (int) $year);
        }

        $deposits = $query->orderBy('date', 'desc')
            ->limit(500)
            ->get();

        $officeIds = $deposits->pluck('office')->unique()->values()->all();
        $depositTypeIds = $deposits->pluck('deposit_type')->unique()->values()->all();

        $offices = \App\Models\Office::whereIn('id', $officeIds)->pluck('name', 'id');
        $depositTypes = \App\Models\DepositType::whereIn('id', $depositTypeIds)->pluck('name', 'id');

        $bankLogs = \App\Models\BankDepositLog::query()
            ->with(['user'])
            ->whereIn('deposit_type', $depositTypeIds)
            ->whereIn('office_id', $officeIds)
            ->get()
            ->keyBy(fn($log) => $log->deposit_type . '_' . $log->office_id . '_' . substr($log->created_date, 0, 7));

        $logs = $deposits->map(function ($dep) use ($bankLogs, $offices, $depositTypes) {
            $monthYear = substr($dep->date, 0, 7);
            $key = $dep->deposit_type . '_' . $dep->office . '_' . $monthYear;
            $log = $bankLogs->get($key);

            return [
                'id' => $log->id ?? $dep->id,
                'deposit_type_name' => $depositTypes->get($dep->deposit_type, 'Unknown'),
                'user_name' => $log && isset($log->user) && is_object($log->user) && isset($log->user->first_name)
                    ? ($log->user->first_name . ' ' . $log->user->last_name)
                    : ($dep->user_id ? 'Unknown' : 'Unknown'),
                'office_name' => $offices->get($dep->office, 'Unknown'),
                'amount' => (float) $dep->amount,
                'deposit_method' => $log->deposit_method ?? 'Cash',
                'reference_number' => $log->reference_number ?? 'N/A',
                'created_date' => $log->created_date ?? $dep->date,
            ];
        });

        return response()->json([
            'deposits' => $logs,
            'total' => $logs->sum('amount'),
        ]);
    }

    public function queryFailedDeposits(Request $request)
    {
        $deposits = \App\Models\Deposit::query()
            ->where('amount', '<', 1)
            ->orderBy('date', 'desc')
            ->limit(500)
            ->get();

        $officeIds = $deposits->pluck('office')->unique()->values()->all();
        $depositTypeIds = $deposits->pluck('deposit_type')->unique()->values()->all();

        $offices = \App\Models\Office::whereIn('id', $officeIds)->pluck('name', 'id');
        $depositTypes = \App\Models\DepositType::whereIn('id', $depositTypeIds)->pluck('name', 'id');

        $bankLogs = \App\Models\BankDepositLog::query()
            ->with(['user'])
            ->whereIn('deposit_type', $depositTypeIds)
            ->whereIn('office_id', $officeIds)
            ->get()
            ->keyBy(fn($log) => $log->deposit_type . '_' . $log->office_id . '_' . substr($log->created_date, 0, 7));

        $logs = $deposits->map(function ($dep) use ($bankLogs, $offices, $depositTypes) {
            $monthYear = substr($dep->date, 0, 7);
            $key = $dep->deposit_type . '_' . $dep->office . '_' . $monthYear;
            $log = $bankLogs->get($key);

            return [
                'id' => $log->id ?? $dep->id,
                'deposit_type_name' => $depositTypes->get($dep->deposit_type, 'Unknown'),
                'user_name' => $log && isset($log->user) && is_object($log->user) && isset($log->user->first_name)
                    ? ($log->user->first_name . ' ' . $log->user->last_name)
                    : 'Unknown',
                'office_name' => $offices->get($dep->office, 'Unknown'),
                'amount' => (float) $dep->amount,
                'deposit_method' => $log->deposit_method ?? 'Cash',
                'reference_number' => $log->reference_number ?? 'N/A',
                'created_date' => $log->created_date ?? $dep->date,
            ];
        });

        return response()->json([
            'deposits' => $logs,
            'total' => $logs->sum('amount'),
        ]);
    }

    public function storeAuditSubmission(Request $request)
    {
        $step = (int) $request->input('save_step', 0);
        $isFinalSubmit = $request->input('final_submit') === '1';
        $submission = null;

        if ($request->filled('audit_submission_id')) {
            $submission = \App\Models\AuditSubmission::find($request->input('audit_submission_id'));
        }

        try {
            if ($step > 0) {
                $submission = $this->saveAuditStep($request, $step, $submission);
                return response()->json([
                    'success' => true,
                    'submission_id' => $submission->id,
                    'message' => 'Step ' . $step . ' saved successfully.'
                ]);
            }

            // Final submission: save admin section, then conclusion/sign-off, then risk calc
            $submission = $this->saveAuditStep($request, 2, $submission);
            $submission = $this->saveAuditConclusionStep($request, $submission);
            $this->saveFinalSubmission($request, $submission);

            // If it's an AJAX request (final_submit), return JSON
            if ($isFinalSubmit || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'submission_id' => $submission->id,
                    'message' => 'Audit checklist submitted successfully.'
                ]);
            }

            return redirect('/risk/overview')->with('success', 'Audit checklist submitted successfully.');
        } catch (\Exception $e) {
            if ($step > 0 || $isFinalSubmit || $request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            throw $e;
        }
    }

    private function saveAuditStep(Request $request, int $step, ?\App\Models\AuditSubmission $submission = null)
    {
        switch ($step) {
            case 2:
                return $this->saveAuditAdministrationStep($request, $submission);
            case 3:
                return $this->saveDigitalPaymentControlsStep($request, $submission);
            case 4:
                return $this->saveLoanPortfolioStep($request, $submission);
            case 5:
                return $this->saveCollectionsStep($request, $submission);
            case 6:
                return $this->saveFraudIndicatorsStep($request, $submission);
            case 7:
                return $this->saveStaffProcessStep($request, $submission);
            case 8:
                return $this->saveSystemControlStep($request, $submission);
            case 9:
                return $this->saveReportingGovernanceStep($request, $submission);
            case 10:
                return $this->saveAuditConclusionStep($request, $submission);
            default:
                throw new \InvalidArgumentException('Unsupported audit step: ' . $step);
        }
    }

    private function createOrUpdateSubmission(array $data, ?\App\Models\AuditSubmission $submission = null)
    {
        if ($submission) {
            $submission->update($data);
            return $submission;
        }

        // Set default risk_rating for new submissions
        if (!isset($data['risk_rating'])) {
            $data['risk_rating'] = 'pending';
        }

        return \App\Models\AuditSubmission::create($data);
    }

    private function saveAuditAdministrationStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        $data = [
            'office_id' => $request->input('s1_office_id'),
            'audit_date' => $request->input('s1_audit_date'),
            'auditor_name' => $request->input('s1_auditor_name'),
            'audit_type' => $request->input('s1_audit_type'),
            'unannounced' => $request->input('s1_unannounced'),
            'manager_present' => $request->input('s1_manager_present'),
            'manager_name' => $request->input('s1_manager_name'),
            'opening_remarks' => $request->input('s1_opening_remarks'),
            'period_start' => $request->input('s1_period_start'),
            'period_end' => $request->input('s1_period_end'),
            'auditor_id' => Sentinel::getUser()->id,
        ];

        if ($request->has('s1_audit_scope')) {
            $scope = $request->input('s1_audit_scope');
            $data['audit_scope'] = is_array($scope) ? json_encode($scope) : $scope;
        }

        if (empty($data['office_id'])) {
            throw new \InvalidArgumentException('Branch selection is required before saving Audit Administration.');
        }

        // If no submission exists, check if one already exists for this office + period combination
        if (!$submission) {
            $submission = \App\Models\AuditSubmission::where('office_id', $data['office_id'])
                ->where('audit_date', $data['audit_date'])
                ->where('period_start', $data['period_start'])
                ->where('period_end', $data['period_end'])
                ->first();
        }

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveDigitalPaymentControlsStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Wallet & Digital Payment Controls data.');
        }

        $data = [];
        for ($i = 1; $i <= 10; $i++) {
            $data["s2_{$i}"] = $request->input("s2_{$i}");
            $data["s2_{$i}_notes"] = $request->input("s2_{$i}_notes");
        }

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveLoanPortfolioStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Loan Portfolio data.');
        }

        $data = [];
        for ($i = 1; $i <= 7; $i++) {
            $data["s3_{$i}"] = $request->input("s3_{$i}");
            $data["s3_{$i}_notes"] = $request->input("s3_{$i}_notes");
        }
        $data['s3_total_active'] = $request->input('s3_total_active');
        $data['s3_incomplete_files'] = $request->input('s3_incomplete_files');
        $data['s3_notes'] = $request->input('s3_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveCollectionsStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Collections data.');
        }

        $data = [];
        for ($i = 1; $i <= 6; $i++) {
            $data["s4_{$i}"] = $request->input("s4_{$i}");
            $data["s4_{$i}_notes"] = $request->input("s4_{$i}_notes");
        }
        $data['s4_system_collections'] = $request->input('s4_system_collections');
        $data['s4_wallet_collections'] = $request->input('s4_wallet_collections');
        $data['s4_notes'] = $request->input('s4_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveFraudIndicatorsStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Fraud Indicators data.');
        }

        $data = [];
        for ($i = 1; $i <= 7; $i++) {
            $data["s5_{$i}"] = $request->input("s5_{$i}");
            $data["s5_{$i}_notes"] = $request->input("s5_{$i}_notes");
        }
        $data['s5_notes'] = $request->input('s5_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveStaffProcessStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Staff & Process data.');
        }

        $data = [];
        for ($i = 1; $i <= 8; $i++) {
            $data["s6_{$i}"] = $request->input("s6_{$i}");
            $data["s6_{$i}_notes"] = $request->input("s6_{$i}_notes");
        }
        $data['s6_total_staff'] = $request->input('s6_total_staff');
        $data['s6_notes'] = $request->input('s6_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveSystemControlStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving System & Control data.');
        }

        $data = [];
        for ($i = 1; $i <= 6; $i++) {
            $data["s7_{$i}"] = $request->input("s7_{$i}");
            $data["s7_{$i}_notes"] = $request->input("s7_{$i}_notes");
        }
        $data['s7_notes'] = $request->input('s7_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveReportingGovernanceStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Reporting & Governance data.');
        }

        $data = [];
        for ($i = 1; $i <= 6; $i++) {
            $data["s8_{$i}"] = $request->input("s8_{$i}");
            $data["s8_{$i}_notes"] = $request->input("s8_{$i}_notes");
        }
        $data['s8_notes'] = $request->input('s8_notes');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveAuditConclusionStep(Request $request, ?\App\Models\AuditSubmission $submission = null)
    {
        if (!$submission) {
            throw new \InvalidArgumentException('Audit submission must exist before saving Audit Conclusion data.');
        }

        $data = [];
        
        // Save all 5 section 9 checklist items and their notes (s9_1 through s9_5)
        for ($i = 1; $i <= 5; $i++) {
            $data["s9_{$i}"] = $request->input("s9_{$i}");
            $data["s9_{$i}_notes"] = $request->input("s9_{$i}_notes");
        }
        
        // Save section 9 overall notes
        $data['s9_notes'] = $request->input('s9_notes');
        
        // Save additional conclusion / sign-off fields
        $data['key_findings']        = $request->input('key_findings');
        $data['immediate_actions']   = $request->input('immediate_actions');
        $data['recommendations']     = $request->input('recommendations');
        $data['followup_date']       = $request->input('followup_date');
        $data['escalation_required'] = $request->input('escalation_required');
        $data['auditor_signature']   = $request->input('auditor_signature');
        $data['signoff_datetime']    = $request->input('signoff_datetime');
        $data['manager_acknowledgement'] = $request->input('manager_acknowledgement');
        $data['manager_comments']    = $request->input('manager_comments');

        return $this->createOrUpdateSubmission($data, $submission);
    }

    private function saveFinalSubmission(Request $request, \App\Models\AuditSubmission $submission)
    {
        $submission->fail_count = 0;
        for ($s = 2; $s <= 9; $s++) {
            $max = ($s === 3) ? 7 : (($s === 4) ? 6 : (($s === 5) ? 7 : (($s === 6) ? 8 : (($s === 7) ? 6 : (($s === 8) ? 6 : 2)))));
            for ($i = 1; $i <= $max; $i++) {
                if ($request->input("s{$s}_{$i}") === 'fail') {
                    $submission->fail_count++;
                }
            }
        }

        $riskRating = 'low';
        if ($submission->fail_count >= 13) {
            $riskRating = 'critical';
        } elseif ($submission->fail_count >= 8) {
            $riskRating = 'high';
        } elseif ($submission->fail_count >= 4) {
            $riskRating = 'medium';
        }

        $submission->risk_rating = $riskRating;
        $submission->save();
    }

    public function getAuditSectionDetails($submissionId, $section)
    {
        $submission = \App\Models\AuditSubmission::findOrFail($submissionId);
        $section = (int) $section;

        $sectionItems = config('risk-audit.section_items', []);

        $items = [];
        if (isset($sectionItems[$section])) {
            foreach ($sectionItems[$section] as $field => $label) {
                $status = $submission->$field;
                $notes = $submission->{$field . '_notes'};
                $items[] = [
                    'label' => $label,
                    'status' => $status,
                    'notes' => $notes,
                ];
            }
        }

        return response()->json($items);
    }

    public function deleteAuditSubmission($submissionId)
    {
        try {
            $submission = \App\Models\AuditSubmission::findOrFail($submissionId);
            
            $submission->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Audit submission deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function printAuditReport($submissionId)
    {
        $submission = \App\Models\AuditSubmission::with(['office', 'auditor'])
            ->findOrFail($submissionId);

        $sectionShorts = config('risk-audit.section_names', []);
        $sectionItems  = config('risk-audit.section_items', []);
        $ratingConfig  = config('risk-audit.rating_config', config('risk-audit.fail_rating', []));
        $rc            = ($ratingConfig && is_array($ratingConfig))
            ? ($ratingConfig[$submission->risk_rating] ?? $ratingConfig['pending'] ?? ['label' => ucfirst($submission->risk_rating), 'color' => '#333'])
            : null;

        return view('risk.print-audit-report', [
            'submission'     => $submission,
            'sectionShorts'  => $sectionShorts,
            'sectionItems'   => $sectionItems,
            'ratingConfig'   => $ratingConfig,
            'rc'             => $rc,
            'itemCounts'     => config('risk-audit.section_item_counts', []),
        ]);
    }

    public function getFullAuditReport($submissionId)
    {
        $submission = \App\Models\AuditSubmission::with(['office', 'auditor'])
            ->findOrFail($submissionId);

        $sectionShorts = config('risk-audit.section_names', []);
        $sectionItems  = config('risk-audit.section_items', []);
        $ratingConfig  = config('risk-audit.rating_config', config('risk-audit.fail_rating', []));
        $rc = ($ratingConfig && is_array($ratingConfig))
            ? ($ratingConfig[$submission->risk_rating] ?? $ratingConfig['pending'] ?? ['label' => ucfirst($submission->risk_rating), 'color' => '#333'])
            : null;

        return response()->json([
            'office'      => $submission->office ? $submission->office->name : null,
            'audit_date'  => $submission->audit_date ? $submission->audit_date->format('d M Y') : '',
            'auditor'     => $submission->auditor ? $submission->auditor->name : 'N/A',
            'fail_count'  => (int) $submission->fail_count,
            'risk_rating' => $submission->risk_rating,
            'risk_label'  => $rc ? ($rc['label'] ?? ucfirst($submission->risk_rating)) : ucfirst($submission->risk_rating),
            'risk_color'  => $rc ? ($rc['color'] ?? '#333') : '#333',
            'opening_remarks' => $submission->opening_remarks,
            'key_findings'    => $submission->key_findings,
            'immediate_actions'   => $submission->immediate_actions,
            'recommendations'     => $submission->recommendations,
            'sections'    => $submission->sections ?? [],
            'section_shorts' => $sectionShorts,
            'section_items'  => $sectionItems,
        ]);
    }

    public function getOfficeAuditHistory($officeId)
    {
        $officeId = (int) $officeId;
        $office   = \App\Models\Office::findOrFail($officeId);

        $ratingConfig   = config('risk-audit.rating_config', config('risk-audit.fail_rating', []));
        $sectionShorts  = config('risk-audit.section_names', []);
        $sectionIndexes = config('risk-audit.section_item_counts', []);

        $submissions = \App\Models\AuditSubmission::with(['auditor'])
            ->where('office_id', $officeId)
            ->orderByDesc('audit_date')
            ->orderByDesc('created_at')
            ->get();

        $list = [];
        foreach ($submissions as $sub) {
            $rc = ($ratingConfig && is_array($ratingConfig))
                ? ($ratingConfig[$sub->risk_rating] ?? $ratingConfig['pending'] ?? null)
                : null;

            // ── Compute section pass/fail/na counts ─────────────────────────
            $sections = [];
            foreach ($sectionShorts as $si => $sname) {
                if ($si === 0) {
                    $sections[] = ['pass' => 0, 'fail' => 0, 'na' => 0, 'name' => $sname];
                    continue;
                }
                $secPrefix  = $si + 1; // s2=1, s3=2, …  s9=8
                $itemCount  = $sectionIndexes[$si] ?? 0;
                $pass = $fail = $na = 0;
                for ($j = 1; $j <= $itemCount; $j++) {
                    $field  = "s{$secPrefix}_{$j}";
                    $value  = $sub->$field ?? null;
                    // Fraud (sec index 4) uses present/not_present
                    if ($si === 4) {
                        if ($value === 'not_present') $pass++;
                        elseif ($value === 'present') $fail++;
                    } else {
                        if ($value === 'pass')    $pass++;
                        elseif ($value === 'fail') $fail++;
                        elseif ($value === 'na')   $na++;
                    }
                }
                $sections[] = ['pass' => $pass, 'fail' => $fail, 'na' => $na, 'name' => $sname];
            }

            $list[] = [
                'submission_id'  => (int) $sub->id,
                'audit_date'     => $sub->audit_date ? $sub->audit_date->format('d M Y') : '—',
                'audit_type'     => $sub->audit_type ?? '',
                'auditor'        => ($sub->auditor ? $sub->auditor->name : ($sub->auditor_name ?? '—')),
                'fail_count'     => (int) $sub->fail_count,
                'risk_rating'    => $sub->risk_rating,
                'risk_label'     => $rc['label'] ?? ucfirst($sub->risk_rating),
                'risk_color'     => $rc['color'] ?? '#333',
                'sections'       => $sections,
                'created_at'     => $sub->created_at ? $sub->created_at->format('d M Y H:i') : '',
                'opening_remarks'=> $sub->opening_remarks ?? '',
                'key_findings'   => $sub->key_findings ?? '',
                'immediate_actions' => $sub->immediate_actions ?? '',
                'recommendations'   => $sub->recommendations ?? '',
            ];
        }

        return response()->json([
            'office'    => $office->name,
            'code'      => $office->external_id ?? ('#' . $office->id),
            'province'  => $office->province?->name ?? '—',
            'district'  => $office->district?->name ?? '—',
            'total'     => count($list),
            'audits'    => $list,
        ]);
    }

    /**
     * Run all fraud-detection rules via AlertService and return the count.
     * Thin relay so the risk UI can call /risk/run-all directly.
     */
    public function runAll(): int
    {
        return \App\Services\AlertService::runAll();
    }

    // ── OfficeDebt management ──────────────────────────────────────────────────

    /**
     * GET /risk/office-debts
     * Return all office debt records as JSON.
     */
    public function listOfficeDebts()
    {
        $rows = \App\Models\OfficeDebt::with(['office', 'depositType'])->where('office_id', '!=', 67)->orderByDesc('id')->get();

        return response()->json($rows->map(function ($row) {
            return [
                'id'                => $row->id,
                'office_id'         => $row->office_id,
                'office_name'       => $row->office->name ?? '—',
                'deposit_type_id'   => $row->deposit_type_id,
                'deposit_type_name' => $row->depositType ? $row->depositType->name : '—',
                'debt_status'       => $row->debt_status,
                'debt_month'        => $row->debt_month,
                'debt_year'         => $row->debt_year,
                'original_amount'   => (int) $row->original_amount,
                'outstanding_amount'=> (int) $row->outstanding_amount,
                'notes'             => (string) ($row->notes ?? ''),
                'is_setup_debt'     => $row->is_setup_debt,
                'created_at'        => $row->created_at ? $row->created_at->toDateTimeString() : null,
            ];
        })->all());
    }

    /**
     * POST /risk/office-debts
     * Create an OfficeDebt record.
     *
     * Special case: if deposit_type_id === "setup_debt" (from the UI),
     * we store with deposit_type_id = null and is_setup_debt = 'true'.
     */
    public function storeOfficeDebt(Request $request)
    {
        $data = $request->validate([
            'office_id'          => 'required|integer|exists:offices,id',
            'deposit_type_id'    => 'sometimes|nullable',
            'debt_month'         => 'sometimes|nullable|integer|min:1|max:12',
            'debt_year'          => 'sometimes|nullable|integer|min:2020|max:2100',
            'debt_status'        => 'sometimes|string|max:30',
            'original_amount'    => 'required|integer|min:0',
            'outstanding_amount' => 'required|integer|min:0',
            'notes'              => 'sometimes|nullable|string',
            'is_setup_debt'      => 'sometimes|in:true,false',
        ]);

        // Handle the special "Setup Debt" option from the UI
        if (($data['deposit_type_id'] ?? null) === 'setup_debt') {
            $data['deposit_type_id'] = 0;
            $data['is_setup_debt']   = 'true';
        } else {
            if (!array_key_exists('is_setup_debt', $data)) {
                $data['is_setup_debt'] = 'false';
            }
        }

        // Uniqueness is enforced by the database unique index on
        // (office_id, deposit_type_id, debt_month, debt_year).
        // The previous "only one debt per branch" check has been removed.

        try {
            $debt = \App\Models\OfficeDebt::create($data);

            return response()->json([
                'success' => true,
                'id'      => $debt->id,
                'message' => 'Debt record created successfully.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            // Handle duplicate monthly debt (unique constraint violation)
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'A debt record for this office, deposit type, month, and year already exists. Duplicates are not allowed.',
                ], 409); // 409 Conflict
            }

            throw $e; // re-throw other database errors
        }
    }

    /**
     * PUT /risk/office-debts/{id}
     * Update an existing OfficeDebt record.
     * Partial update — only supplied fields are changed.
     */
    public function updateOfficeDebt(Request $request, int $id)
    {
        $debt = \App\Models\OfficeDebt::findOrFail($id);

        $data = $request->validate([
            'debt_status'        => 'sometimes|string|max:30',
            'original_amount'    => 'sometimes|integer|min:0',
            'outstanding_amount' => 'sometimes|integer|min:0',
            'notes'              => 'sometimes|nullable|string',
            'is_setup_debt'      => 'sometimes|in:true,false',
        ]);

        // Handle adjustment of debt principal or partial payment via deposit
        if (array_key_exists('outstanding_amount', $data)) {
            $newOut       = (int) $data['outstanding_amount'];
            $currOriginal = (int) $debt->original_amount;

            if ($newOut > $currOriginal) {
                // Debt increased: bump both original and outstanding to the new higher value
                $data['original_amount']    = $newOut;
                $data['outstanding_amount'] = $newOut;
            } elseif ($newOut < $currOriginal) {
                // Debt reduced: record the difference as a new deposit entry (raw insert)
                $debtAmount = $currOriginal - $newOut;

                $year  = $debt->debt_year  ?? Carbon::now()->year;
                $month = $debt->debt_month ?? Carbon::now()->month;
                $date  = Carbon::create($year, $month, 1)->toDateString();

                DB::table('deposits')->insert([
                    'deposit_type' => $debt->deposit_type_id,
                    'office'       => $debt->office_id,
                    'amount'       => $debtAmount,
                    'debt'         => true,
                    'date'         => $date,
                ]);

                // outstanding_amount in $data will be applied by update(); original stays
            }
            // equal: no special action
        }

        try {
            $debt->update($data);

            return response()->json([
                'success' => true,
                'id'      => $debt->id,
                'message' => 'Debt record updated successfully.',
            ]);

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() === '23000' || str_contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Update would create a duplicate. A debt record for this office, deposit type, month, and year already exists.',
                ], 409);
            }

            throw $e;
        }
    }

    /**
     * DELETE /risk/office-debts/{id}
     * Permanently remove an OfficeDebt record (branch has cleared its obligation).
     * Sets outstanding_amount / original_amount to 0 first so a race-condition
     * re-fetch never reads stale state.
     */
    public function deleteOfficeDebt(int $id)
    {
        $debt = \App\Models\OfficeDebt::findOrFail($id);

        $debt->update([
            'outstanding_amount' => 0,
            'original_amount'    => 0,
        ]);
        $debt->delete();

        return response()->json([
            'success' => true,
            'id'      => $id,
            'message' => 'Debt record has been cleared and removed.',
        ]);
    }

    /**
     * -- Branch Deposit Audit --
     * List deposit_types as collapsible cards; on expand show every office with a
     * full (outer) join so offices with no deposits appear as "Not Deposited".
     * Supports date-period filtering via ?period= query param:
     *   overall | month | quarter | year | this_circle | last_circle |
     *   last_quarter | last_month | last_year | custom
     * Custom also requires custom_month (1-12) and custom_year.
     */
    public function branchDepositAudit(Request $request)
    {
        $period       = $request->query('period', 'year');
        $customMonth  = (int) $request->query('custom_month', date('n'));
        $customYear   = (int) $request->query('custom_year', date('Y'));
        $officeId     = $request->query('office_id') !== null ? (int) $request->query('office_id') : null;
        $selectedOfficeName = $officeId ? optional(\App\Models\Office::find($officeId))->name : null;
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');

        [$dateFrom, $dateTo] = $this->getDepositDateRange($period, $customMonth, $customYear);

        // Support direct start/end date for custom period (new date picker UI)
        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo   = $endDate;
        }

        // 1. All offices (sorted) — full list for the dropdown
        $offices = \App\Helpers\StatsHelper::getActiveOffices();

        // 2. Filter deposit types based on office-specific exemption settings
        $requiredDepositTypeIds = \App\Helpers\StatsHelper::getRequiredDepositTypes($officeId);
        $depositTypes = \App\Models\DepositType::orderBy('sort_order')->orderBy('name')
            ->whereIn('id', $requiredDepositTypeIds)
            ->get();

        // Determine scope for data queries and required calculations
        $officeIdsForData     = $officeId ? [$officeId] : $offices->pluck('id')->all();
        $effectiveOfficeCount = $officeId ? 1 : $offices->count();

        // 3. Offices whose ids appear in the deposits table (scoped to filter if any)
        $officeIds = $officeIdsForData;

        // 4. Pull deposits — apply date filter only when a specific period is chosen
        $depositQuery = \App\Models\Deposit::query()
            ->whereIn('office', $officeIds);

        
         
        if ($dateFrom !== null && $dateTo !== null) {
            $depositQuery->whereBetween('date', [$dateFrom, $dateTo]);
        }

        $validDeposits  = $depositQuery->get();
        $depositsByType = [];
        foreach ($validDeposits as $dep) {
            $typeId = $dep->deposit_type;
            if (!isset($depositsByType[$typeId])) {
                $depositsByType[$typeId] = [];
            }
            $depositsByType[$typeId][] = $dep;
        }

        // dd($depositsByType);
        // 5. Compute stats for every deposit type (plain foreach, no map/fn)
        $types = [];
        foreach ($depositTypes as $type) {
            $depositsForType  = $depositsByType[$type->id] ?? [];
            $totalAmount      = 0;
            $depositCount     = 0;
            $officesWithDep   = [];
            $officesWithTot   = [];

            foreach ($depositsForType as $dep) {
                $totalAmount  += (float) $dep->amount;
                $depositCount += 1;
                $officesWithDep[$dep->office] = true;

                if (!isset($officesWithTot[$dep->office])) {
                    $officesWithTot[$dep->office] = 0;
                }
                $officesWithTot[$dep->office] += (float) $dep->amount;
            }

            $officesWithTotalCount = 0;
            foreach ($officesWithTot as $sum) {
                if ($sum > 0) {
                    $officesWithTotalCount += 1;
                }
            }

            $types[] = [
                'id'                   => $type->id,
                'name'                 => $type->name,
                'bank'                 => $type->bank ?? '–',
                'gl_account'           => $type->gl_account ?? '–',
                'total_amount'         => $totalAmount,
                'deposit_count'        => $depositCount,
                'office_count'         => $effectiveOfficeCount,
                'offices_with_deposits'=> count($officesWithDep),
                'offices_with_total'   => $officesWithTotalCount,
            ];
        }

        // 6. Aggregate Outstanding Branch Debt card (scoped if office filter active)
        // Also filter by required deposit types based on office-specific exemption settings
        $debtQuery = \App\Models\OfficeDebt::query()->where('office_id', '!=', 67);
        if ($officeId !== null) {
            $debtQuery->where('office_id', $officeId);
        }
        $debtQuery->whereIn('deposit_type_id', $requiredDepositTypeIds);
        $debtRecords = $debtQuery->get();

        // 'paid' now comes from actual Deposit records flagged as debt-related (inserted when Outstanding is manually reduced)
        // Also filter by required deposit types
        $paidDebtDeposits = \App\Models\Deposit::query()->where('debt', true);
        if ($officeId !== null) {
            $paidDebtDeposits->where('office', $officeId);
        }
        $paidDebtDeposits->whereIn('deposit_type', $requiredDepositTypeIds);
        $paid = (int) $paidDebtDeposits->sum('amount');

        $debtCards = [
            'accumulated' => (int) $debtRecords->sum('original_amount'),
            'paid'        => $paid,
            'balance'     => (int) $debtRecords->sum('outstanding_amount') - $paid,
        ];

        // 7. Per-type deposit requirement vs received summary cards
        // Required = monthly_amount x offices x months-spanned-by-period.
        // "overall" is bounded: Jan 1 of the current year through the 28th of the
        // current month — the current month is excluded because it's incomplete.
        $officeCount     = $effectiveOfficeCount;
        $depositCardStats  = [];
        $depositCardTotals = null;
        $totReq   = 0;
        $totRecv  = 0;

        if ($dateFrom === null) {
            // overall: full months from Jan 1 this year through 28th of current month
            $overallPeriodMonths = (int) \Carbon\Carbon::now()
                ->startOfYear()
                ->diffInMonths(\Carbon\Carbon::parse($dateTo))
                + 1;
            foreach ($depositTypes as $type) {
                $monthlyRequired = (int) ($type->monthly_amount ?? 0);
                $required = $monthlyRequired * $officeCount * $overallPeriodMonths;
                $received = 0;
                foreach ($validDeposits as $dep) {
                    if ((int) $dep->deposit_type === (int) $type->id) {
                        $received += (float) $dep->amount;
                    }
                }
                
                $balance = $required - $received;

                $totReq  += $required;
                $totRecv += $received;

                $isMandatory = in_array((int) $type->id, [3, 1, 5]);
                $depositCardStats[] = [
                    'label'       => $type->name,
                    'sort_order'  => $type->sort_order ?? 0,
                    'required'    => $required,
                    'received'    => $received,
                    'other'       => !$isMandatory ? (int) $received : 0,
                    'balance'     => $balance,
                    'grand_total' => (int) $received,
                ];
            }

        } else {
            // specific period: months spanned by the date range
            $periodMonths = (int) \Carbon\Carbon::parse($dateFrom)->diffInMonths(\Carbon\Carbon::parse($dateTo)) + 1;
            foreach ($depositTypes as $type) {
                $monthlyRequired = (int) ($type->monthly_amount ?? 0);
                $required = $monthlyRequired * $officeCount * $periodMonths;
                $received = 0;
                foreach ($validDeposits as $dep) {
                    if ((int) $dep->deposit_type === (int) $type->id) {
                        $received += (float) $dep->amount;
                    }
                }

                $balance = $required - $received;

                $totReq  += $required;
                $totRecv += $received;

                $isMandatory = in_array((int) $type->id, [3, 1, 5]);
                $depositCardStats[] = [
                    'label'       => $type->name,
                    'sort_order'  => $type->sort_order ?? 0,
                    'required'    => $required,
                    'received'    => $received,
                    'other'       => !$isMandatory ? (int) $received : 0,
                    'balance'     => $balance,
                    'grand_total' => (int) $received,
                ];
            }
        }

        // Grand total card should only count deposit types that are in the required list
        // (filtered by office-specific exemption settings)
        $totReq  = array_sum(array_column($depositCardStats, 'required'));
        $totRecv = array_sum(array_column($depositCardStats, 'received'));

        $depositCardTotals = [
            'label'       => 'All Types (Total)',
            'required'    => $totReq,
            'received'    => (int) Deposit::mandatoryReceived([3, 1, 5], $officeId ? [$officeId] : null, $dateFrom, $dateTo),
            'other'       => (int) Deposit::otherReceived([4, 6, 2], $officeId ? [$officeId] : null, $dateFrom, $dateTo),
            'balance'     => $totReq - $totRecv,
            'grand_total' => (int) Deposit::mandatoryReceived([3, 1, 5], $officeId ? [$officeId] : null, $dateFrom, $dateTo) + (int) Deposit::otherReceived([4, 6, 2], $officeId ? [$officeId] : null, $dateFrom, $dateTo),
        ];

        return view('risk.branch-deposit-audit', compact('types', 'offices', 'debtCards', 'depositCardStats', 'depositCardTotals'))
            ->with('period', $period)
            ->with('customMonth', $customMonth)
            ->with('customYear', $customYear)
            ->with('selectedOfficeName', $selectedOfficeName)
            ->with('startDate', $startDate)
            ->with('endDate', $endDate);
    }

    /**
     * Compute the [startDate, endDate] string pair for a given period label.
     * "overall" is bounded at the 28th of the current month — the current month
     * is excluded because it is always incomplete.
     *
     * Periods
     * - overall         : all deposits recorded up to 28th of current month
     * - month           : this calendar month
     * - quarter         : this quarter
     * - year            : this calendar year
     * - this_circle     : 24 Nov → 24 Dec (for Dec); 24 last month → 24 this month
     * - last_circle     : 24 Oct → 24 Nov (for Dec); 24 two months ago → 24 last month
     * - last_quarter    : previous quarter
     * - last_month      : previous calendar month
     * - last_year       : previous calendar year
     * - custom          : specific year + month (customMonth/customYear params)
     */
    private function getDepositDateRange(string $period, int $customMonth, int $customYear): array
    {
        if ($period === 'overall') {
            // Overall = Jan 1 this year → 28th of current month
            return [
                null,
                Carbon::now()->day(28)->toDateString(),
            ];
        }

        if ($period === 'custom') {
            return [
                Carbon::create($customYear, $customMonth, 1)->startOfMonth()->toDateString(),
                Carbon::create($customYear, $customMonth, 1)->endOfMonth()->toDateString(),
            ];
        }

        // start-of-today / end-of-today anchored helpers
        $now   = Carbon::now();

        // Hard ceiling: never go past the last day of the final month that has debt in the table.
        // When today's date is ≥ 28 the current month has been processed by the service,
        // so ceiling can extend to the last day of the *current* month. Otherwise it stays
        // at the last day of last month.
        $ceiling = Carbon::now()->day >= 28
            ? Carbon::now()->endOfMonth()
            : Carbon::now()->subMonth()->endOfMonth();

        // Debt-base-query: never ask for beyond the current/processed month
        $fromCeil = $ceiling->copy()->startOfMonth();

        return match ($period) {
            'month' => [
                $fromCeil->toDateString(),
                $ceiling->toDateString(),
            ],
            'quarter' => [
                $now->copy()->startOfQuarter()->toDateString(),
                ($now->copy()->endOfQuarter()->lte($ceiling) ? $now->copy()->endOfQuarter() : $ceiling)->toDateString(),
            ],
            'year' => [
                $now->copy()->startOfYear()->toDateString(),
                ($now->copy()->endOfYear()->lte($ceiling) ? $now->copy()->endOfYear() : $ceiling)->toDateString(),
            ],
            'last_month' => [
                $now->copy()->subMonth()->startOfMonth()->toDateString(),
                $now->copy()->subMonth()->endOfMonth()->toDateString(),
            ],
            'last_quarter' => [
                $now->copy()->subQuarter()->startOfQuarter()->toDateString(),
                $now->copy()->subQuarter()->endOfQuarter()->toDateString(),
            ],
            'last_year' => [
                $now->copy()->subYear()->startOfYear()->toDateString(),
                $now->copy()->subYear()->endOfYear()->toDateString(),
            ],
            // This Circle  = 24th of last month → min(24th this month, ceiling)
            'this_circle' => [
                $now->copy()->subMonth()->day(24)->toDateString(),
                min($now->copy()->day(24), $ceiling)->toDateString(),
            ],
            // Last Circle  = 24th of two months ago → 24th of last month
            'last_circle' => [
                $now->copy()->subMonths(2)->day(24)->toDateString(),
                $now->copy()->subMonth()->day(24)->toDateString(),
            ],
            default => [
                $fromCeil->toDateString(),
                $ceiling->toDateString(),
            ],

        };
    }
    /**
     * JSON: all offices for a single deposit type (full outer-join effect).
     * Each row: { office_id, office_name, total, deposit_count, months }
     * months = [jan, feb, mar, apr, may, jun, jul, aug, sep, oct, nov, dec]
     * Accepts the same ?period= filter as branchDepositAudit().
     */
    public function branchDepositAuditByType(int $depositTypeId, Request $request)
    {
        $period       = $request->query('period', 'month');
        $customMonth  = (int) $request->query('custom_month', date('n'));
        $customYear   = (int) $request->query('custom_year', date('Y'));
        $officeId     = $request->query('office_id') !== null ? (int) $request->query('office_id') : null;
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');

        [$dateFrom, $dateTo] = $this->getDepositDateRange($period, $customMonth, $customYear);

        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo   = $endDate;
        }

        $offices  = \App\Models\Office::orderBy('name')->get();
        if ($officeId !== null) {
            $offices = $offices->filter(fn($o) => $o->id == $officeId)->values();
        }

        $depositQuery = \App\Models\Deposit::where('deposit_type', $depositTypeId);

        if ($officeId !== null) {
            $depositQuery->where('office', $officeId);
        }

        if ($dateFrom !== null && $dateTo !== null) {
            $depositQuery->whereBetween('date', [$dateFrom, $dateTo]);
        }

        $deposits = $depositQuery->get();

        // Group deposits by office id
        $depsByOffice = [];
        foreach ($deposits as $dep) {
            $oid = $dep->office;
            if (!isset($depsByOffice[$oid])) {
                $depsByOffice[$oid] = [];
            }
            $depsByOffice[$oid][] = $dep;
        }

        // Build one row per office
        $rows = [];
        foreach ($offices as $office) {
            $officeDeps = $depsByOffice[$office->id] ?? [];

            $total      = 0;
            $count      = 0;
            $monthCount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            foreach ($officeDeps as $dep) {
                $total      += (float) $dep->amount;
                $count       += 1;
                $monthNum    = (int) date('n', strtotime((string) $dep->date));
                $monthCount[$monthNum - 1] += 1;
            }

            $rows[] = [
                'office_id'     => $office->id,
                'office_name'   => $office->name,
                'total'         => $total,
                'deposit_count' => $count,
                'months'        => $monthCount,
            ];
        }

        $stats = [
            'offices_with_deposits' => 0,
            'offices_with_total'    => 0,
            'total_offices'         => count($rows),
        ];
        foreach ($rows as $row) {
            if ($row['deposit_count'] > 0) {
                $stats['offices_with_deposits'] += 1;
            }
            if ($row['total'] > 0) {
                $stats['offices_with_total'] += 1;
            }
         }

        return response()->json(['rows' => $rows, 'stats' => $stats]);
    }

    /**
     * Map a row's overall standing to a CSS class so callers can style it.
     */
    private function debtRowClass(array $row): string
    {
        if ($row['outstanding_amount'] <= 0)  return ' da-row-zero';
        if ($row['outstanding_amount'] < $row['original_amount']) return ' da-row-warn';
        return ' da-alert';
    }

    /**
     * GET /risk/office-debts/debt
     * Return debt records **grouped by office** so every office that has
     * outstanding debt appears as one consolidated row.
     * Supports the same ?period= filter for debt_month/debt_year matching.
     *
     * Grouped row shape:
     * { id, office_id, office_name, month_count, original_amount, outstanding_amount,
     *   debt_status, month_boxes: [10-bool-array Jan..Oct], months_detail: [...], notes }
     */
    public function officeDebtsByDebtType(Request $request)
    {
        $period       = $request->query('period', 'month');
        $customMonth  = (int) $request->query('custom_month', date('n'));
        $customYear   = (int) $request->query('custom_year', date('Y'));
        $officeId     = $request->query('office_id') !== null ? (int) $request->query('office_id') : null;
        $startDate    = $request->query('start_date');
        $endDate      = $request->query('end_date');

        [$dateFrom, $dateTo] = $this->getDepositDateRange($period, $customMonth, $customYear);

        if ($period === 'custom' && $startDate && $endDate) {
            $dateFrom = $startDate;
            $dateTo   = $endDate;
        }

        $query = \App\Models\OfficeDebt::with(['office', 'depositType'])
            ->where('outstanding_amount', '>', 0)
            ->where('office_id', '!=', 67);

        if ($officeId !== null) {
            $query->where('office_id', $officeId);
        }

        // Apply period filter on debt_year / debt_month when a specific period is chosen
        if ($dateFrom !== null && $dateTo !== null) {
            $fromYear  = (int) \Carbon\Carbon::parse($dateFrom)->format('Y');
            $fromMonth = (int) \Carbon\Carbon::parse($dateFrom)->format('n');
            $toYear    = (int) \Carbon\Carbon::parse($dateTo)->format('Y');
            $toMonth   = (int) \Carbon\Carbon::parse($dateTo)->format('n');

            // Build a composite year*100 + month range for filtering
            $fromVal = $fromYear * 100 + $fromMonth;
            $toVal   = $toYear   * 100 + $toMonth;

            $query->whereRaw('(debt_year * 100 + debt_month) BETWEEN ? AND ?', [$fromVal, $toVal]);
        }

        $records = $query->orderByDesc('id')->get();

        // ── Group by office so every branch appears in a single row ──────────────
        $grouped = [];
        foreach ($records as $rec) {
            $oid = (int) $rec->office_id;
            if (!isset($grouped[$oid])) {
                $grouped[$oid] = [
                    'office_id'          => $oid,
                    'office_name'        => $rec->office->name ?? '—',
                    'original_amount'    => 0,
                    'outstanding_amount' => 0,
                    'months_detail'      => [],
                ];
            }
            $grouped[$oid]['original_amount']    += (int) $rec->original_amount;
            $grouped[$oid]['outstanding_amount'] += (int) $rec->outstanding_amount;
            $grouped[$oid]['months_detail'][] = [
                'month'          => (int) $rec->debt_month,
                'year'           => (int) $rec->debt_year,
                'original'       => (int) $rec->original_amount,
                'outstanding'    => (int) $rec->outstanding_amount,
                'status'         => (string) ($rec->debt_status ?? 'owing'),
                'deposit_type'   => optional($rec->depositType)->name ?? '—',
                'notes'          => (string) ($rec->notes ?? ''),
            ];
        }

        // ── Build month-box array (Jan..Dec) and determine per-office status ──────
        $mNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        $rows = [];
        $sortIdx = 0;
        foreach ($grouped as $g) {
            $monthBoxes = array_fill(0, 12, false);
            $monthHeads = array_fill(0, 12, null);  // accumulate per-month detail for tooltip
            foreach ($g['months_detail'] as $md) {
                $idx = $md['month'] - 1;
                if ($idx >= 0 && $idx < 12) {
                    $monthBoxes[$idx] = true;
                    if (!isset($monthHeads[$idx]) || $monthHeads[$idx] === null) {
                        $monthHeads[$idx] = [];
                    }
                    $monthHeads[$idx][] = $md;
                }
            }

            $status = $this->debtRowClass([
                'original_amount'    => $g['original_amount'],
                'outstanding_amount' => $g['outstanding_amount'],
            ]);

            $rows[] = [
                'id'                  => ++$sortIdx,
                'office_id'           => $g['office_id'],
                'office_name'         => $g['office_name'],
                'month_count'         => count($g['months_detail']),
                'original_amount'     => $g['original_amount'],
                'outstanding_amount'  => $g['outstanding_amount'],
                'debt_status'         => $status === ' da-row-zero' ? 'Cleared'
                                   : ($status === ' da-row-warn' ? 'Partial' : 'Owing'),
                'month_boxes'         => $monthBoxes,
                'months_detail'       => $g['months_detail'],
            ];
        }

        // Sort: highest outstanding first, then highest original
        usort($rows, function ($a, $b) {
            $diff = ($b['outstanding_amount'] ?? 0) - ($a['outstanding_amount'] ?? 0);
            return $diff !== 0 ? $diff : ($b['original_amount'] ?? 0) - ($a['original_amount'] ?? 0);
        });

        return response()->json(['rows' => $rows]);
    }
}