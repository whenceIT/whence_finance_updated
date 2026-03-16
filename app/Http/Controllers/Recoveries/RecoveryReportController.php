<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\{RecoveryCase, RecoveryPayment};
use App\Services\RecoveryDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecoveryReportController extends Controller
{
    public function __construct(private RecoveryDashboardService $dashboard) {}

    // ═══════════════════════════════════════════════
    // MONTHLY REPORT
    // ═══════════════════════════════════════════════

    public function monthly(Request $request)
    {
        [$month, $year, $period, $byCategory, $bySpecialist, $branchBreakdown, $totalRecovered]
            = $this->monthlyData($request);

        return view('recoveries.reports.monthly', compact(
            'period', 'month', 'year', 'totalRecovered', 'byCategory', 'bySpecialist', 'branchBreakdown'
        ));
    }

    public function monthlyPdf(Request $request)
    {
        [$month, $year, $period, $byCategory, $bySpecialist, $branchBreakdown, $totalRecovered]
            = $this->monthlyData($request);

        return view('recoveries.reports.monthly_pdf', compact(
            'period', 'month', 'year', 'totalRecovered', 'byCategory', 'bySpecialist', 'branchBreakdown'
        ));
    }

    public function monthlyExcel(Request $request)
    {
        [$month, $year, $period, $byCategory, $bySpecialist, $branchBreakdown, $totalRecovered]
            = $this->monthlyData($request);

        $monthName = \Carbon\Carbon::create()->month($month)->format('F');
        $filename  = "recovery-monthly-{$monthName}-{$year}.csv";
        $rows      = [];

        $rows[] = ['WHENCE FINANCIAL SERVICES'];
        $rows[] = ['MONTHLY RECOVERY REPORT'];
        $rows[] = ["Period: {$monthName} {$year}"];
        $rows[] = ['Generated: ' . now()->format('d M Y H:i')];
        $rows[] = ['Confidential — Internal Use Only'];
        $rows[] = [];

        // Summary
        $rows[] = ['SUMMARY'];
        $rows[] = ['Metric', 'Value'];
        $rows[] = ['Total Recovered',    number_format($totalRecovered, 2)];
        $rows[] = ['Cases Resolved',     $bySpecialist->sum('resolved_cases')];
        $rows[] = ['Cases Active',       $bySpecialist->sum('active_cases')];
        $rows[] = ['Active Specialists', $bySpecialist->count()];
        $rows[] = ['Branches Active',    $branchBreakdown->count()];
        $rows[] = [];

        // By category
        $rows[] = ['RECOVERY BY CATEGORY'];
        $rows[] = ['Category', 'Amount Recovered', '% of Total'];
        foreach (RecoveryCase::CATEGORIES as $key => $label) {
            $amt    = $byCategory[$key]->total ?? 0;
            $pct    = $totalRecovered > 0 ? round(($amt / $totalRecovered) * 100, 1) : 0;
            $rows[] = [$label, number_format($amt, 2), "{$pct}%"];
        }
        $rows[] = ['TOTAL', number_format($totalRecovered, 2), '100%'];
        $rows[] = [];

        // Specialists
        $rows[] = ['SPECIALIST PERFORMANCE'];
        $rows[] = ['Specialist', 'Stream', 'Recovered', '% of Total', 'Active Cases', 'Resolved', 'Target Amount', 'vs Target %', 'Status'];
        foreach ($bySpecialist->sortByDesc('total_recovered') as $row) {
            $share  = $totalRecovered > 0 ? round(($row['total_recovered'] / $totalRecovered) * 100, 1) : 0;
            $rows[] = [
                $row['specialist']->first_name . ' ' . $row['specialist']->last_name,
                RecoveryCase::CATEGORIES[$row['category']] ?? $row['category'],
                number_format($row['total_recovered'], 2),
                "{$share}%",
                $row['active_cases'],
                $row['resolved_cases'],
                $row['target_amount'] > 0 ? number_format($row['target_amount'], 2) : '—',
                $row['target_pct'] . '%',
                ucwords(str_replace('_', ' ', $row['status'])),
            ];
        }
        $rows[] = [
            'TOTAL', '',
            number_format($bySpecialist->sum('total_recovered'), 2),
            '100%',
            $bySpecialist->sum('active_cases'),
            $bySpecialist->sum('resolved_cases'),
            '', '', '',
        ];
        $rows[] = [];

        // Branch breakdown
        $rows[] = ['RECOVERY BY BRANCH'];
        $rows[] = ['Branch', 'Cases', 'Amount Recovered'];
        foreach ($branchBreakdown as $b) {
            $rows[] = [$b->name, $b->case_count, number_format($b->total_recovered, 2)];
        }
        $rows[] = ['TOTAL', $branchBreakdown->sum('case_count'), number_format($branchBreakdown->sum('total_recovered'), 2)];

        return $this->csvDownload($rows, $filename);
    }

    // ═══════════════════════════════════════════════
    // ATTRIBUTION REPORT
    // ═══════════════════════════════════════════════

    public function attribution(Request $request)
    {
        [$period, $attributionData] = $this->attributionData($request);

        return view('recoveries.reports.attribution', compact('attributionData', 'period'));
    }

    public function attributionPdf(Request $request)
    {
        [$period, $attributionData] = $this->attributionData($request);

        return view('recoveries.reports.attribution_pdf', compact('attributionData', 'period'));
    }

    public function attributionExcel(Request $request)
    {
        [$period, $attributionData] = $this->attributionData($request);

        $filename = "recovery-attribution-{$period}-" . now()->format('Y-m-d') . ".csv";
        $rows     = [];

        $rows[] = ['WHENCE FINANCIAL SERVICES'];
        $rows[] = ['RECOVERY ATTRIBUTION REPORT'];
        $rows[] = ['Period: ' . ucfirst($period)];
        $rows[] = ['Generated: ' . now()->format('d M Y H:i')];
        $rows[] = ['Confidential — Internal Use Only'];
        $rows[] = [];

        $grandAll  = $attributionData->sum('grand_total');
        $deptAll   = $attributionData->sum('dept_total');
        $originAll = $attributionData->sum('origin_total');
        $supAll    = $attributionData->sum('supporting_total');

        // Summary
        $rows[] = ['SUMMARY'];
        $rows[] = ['Component',           'Amount',                     '% of Total'];
        $rows[] = ['Grand Total',         number_format($grandAll, 2),  '100%'];
        $rows[] = ['Recoveries Dept',     number_format($deptAll, 2),   ($grandAll > 0 ? round(($deptAll/$grandAll)*100,1) : 0) . '%'];
        $rows[] = ['Origin Branch',       number_format($originAll, 2), ($grandAll > 0 ? round(($originAll/$grandAll)*100,1) : 0) . '%'];
        $rows[] = ['Supporting Branch',   number_format($supAll, 2),    ($grandAll > 0 ? round(($supAll/$grandAll)*100,1) : 0) . '%'];
        $rows[] = [];

        // Detail
        $rows[] = ['ATTRIBUTION BY CATEGORY'];
        $rows[] = ['Category', 'Grand Total', 'Recoveries Dept', 'Dept %', 'Origin Branch', 'Origin %', 'Supporting Branch', 'Supporting %'];
        foreach ($attributionData as $row) {
            $gt     = $row->grand_total ?: 1;
            $catName = RecoveryCase::CATEGORIES[$row->category] ?? ucwords(str_replace('_',' ',$row->category));
            $rows[] = [
                $catName,
                number_format($row->grand_total, 2),
                number_format($row->dept_total, 2),
                round(($row->dept_total / $gt) * 100, 1) . '%',
                number_format($row->origin_total, 2),
                round(($row->origin_total / $gt) * 100, 1) . '%',
                number_format($row->supporting_total, 2),
                round(($row->supporting_total / $gt) * 100, 1) . '%',
            ];
        }
        $rows[] = [
            'TOTAL',
            number_format($grandAll, 2),
            number_format($deptAll, 2),
            ($grandAll > 0 ? round(($deptAll/$grandAll)*100,1) : 0) . '%',
            number_format($originAll, 2),
            ($grandAll > 0 ? round(($originAll/$grandAll)*100,1) : 0) . '%',
            number_format($supAll, 2),
            ($grandAll > 0 ? round(($supAll/$grandAll)*100,1) : 0) . '%',
        ];

        return $this->csvDownload($rows, $filename);
    }

    // ═══════════════════════════════════════════════
    // SPECIALIST REPORT
    // ═══════════════════════════════════════════════

    public function specialists(Request $request)
    {
        $period      = $request->get('period', 'month');
        $specialists = $this->dashboard->getSpecialistPerformance($period);

        return view('recoveries.reports.specialists', compact('specialists', 'period'));
    }

    public function specialistsPdf(Request $request)
    {
        $period      = $request->get('period', 'month');
        $specialists = $this->dashboard->getSpecialistPerformance($period);

        return view('recoveries.reports.specialists_pdf', compact('specialists', 'period'));
    }

    public function specialistsExcel(Request $request)
    {
        $period      = $request->get('period', 'month');
        $specialists = $this->dashboard->getSpecialistPerformance($period);

        $filename        = "recovery-specialists-{$period}-" . now()->format('Y-m-d') . ".csv";
        $totalRecovered  = $specialists->sum('total_recovered');
        $rows            = [];

        $rows[] = ['WHENCE FINANCIAL SERVICES'];
        $rows[] = ['SPECIALIST PERFORMANCE REPORT'];
        $rows[] = ['Period: ' . ucfirst($period)];
        $rows[] = ['Generated: ' . now()->format('d M Y H:i')];
        $rows[] = ['Confidential — Internal Use Only'];
        $rows[] = [];

        $rows[] = ['SUMMARY'];
        $rows[] = ['Metric', 'Value'];
        $rows[] = ['Total Recovered',    number_format($totalRecovered, 2)];
        $rows[] = ['Active Specialists', $specialists->count()];
        $rows[] = ['Active Cases',       $specialists->sum('active_cases')];
        $rows[] = ['Resolved Cases',     $specialists->sum('resolved_cases')];
        $rows[] = ['Exceeding Target',   $specialists->where('status','exceeding')->count()];
        $rows[] = ['On Track',           $specialists->where('status','on_track')->count()];
        $rows[] = ['At Risk',            $specialists->where('status','at_risk')->count()];
        $rows[] = ['Behind Target',      $specialists->where('status','behind')->count()];
        $rows[] = [];

        $rows[] = ['SPECIALIST DETAIL'];
        $rows[] = ['Rank', 'Specialist', 'Stream', 'Recovered', 'Share %', 'Active Cases', 'Resolved', 'Target Amount', 'vs Target %', 'Status'];
        foreach ($specialists->sortByDesc('total_recovered')->values() as $i => $row) {
            $cat   = $row['category'] ?? 'escalated';
            $share = $totalRecovered > 0 ? round(($row['total_recovered'] / $totalRecovered) * 100, 1) : 0;
            $rows[] = [
                $i + 1,
                $row['specialist']->first_name . ' ' . $row['specialist']->last_name,
                RecoveryCase::CATEGORIES[$cat] ?? ucwords(str_replace('_',' ',$cat)),
                number_format($row['total_recovered'], 2),
                "{$share}%",
                $row['active_cases'],
                $row['resolved_cases'],
                $row['target_amount'] > 0 ? number_format($row['target_amount'], 2) : '—',
                $row['target_pct'] . '%',
                ucwords(str_replace('_', ' ', $row['status'])),
            ];
        }
        $rows[] = [
            '', 'TOTAL', '',
            number_format($totalRecovered, 2),
            '100%',
            $specialists->sum('active_cases'),
            $specialists->sum('resolved_cases'),
            '', '', '',
        ];

        return $this->csvDownload($rows, $filename);
    }

    // ═══════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════

    private function monthlyData(Request $request): array
    {
        $period = $request->get('period', 'month');
        $month  = (int) $request->get('month', now()->month);
        $year   = (int) $request->get('year',  now()->year);

        $totalRecovered = RecoveryPayment::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->sum('amount');

        $byCategory = DB::table('recovery_payments')
            ->join('recovery_cases', 'recovery_payments.recovery_case_id', '=', 'recovery_cases.id')
            ->select('recovery_cases.category', DB::raw('SUM(recovery_payments.amount) as total'))
            ->whereMonth('recovery_payments.payment_date', $month)
            ->whereYear('recovery_payments.payment_date', $year)
            ->groupBy('recovery_cases.category')
            ->get()->keyBy('category');

        $bySpecialist    = $this->dashboard->getSpecialistPerformance($period);
        $branchBreakdown = $this->dashboard->getBranchBreakdown($period);

        return [$month, $year, $period, $byCategory, $bySpecialist, $branchBreakdown, $totalRecovered];
    }

    private function attributionData(Request $request): array
    {
        $period = $request->get('period', 'month');

        $attributionData = DB::table('recovery_payments')
            ->join('recovery_cases', 'recovery_payments.recovery_case_id', '=', 'recovery_cases.id')
            ->select(
                'recovery_cases.category',
                DB::raw('SUM(recovery_payments.recoveries_dept_amount)    as dept_total'),
                DB::raw('SUM(recovery_payments.origin_branch_amount)      as origin_total'),
                DB::raw('SUM(recovery_payments.supporting_branch_amount)  as supporting_total'),
                DB::raw('SUM(recovery_payments.amount)                    as grand_total')
            )
            ->groupBy('recovery_cases.category')
            ->get();

        return [$period, $attributionData];
    }

    private function csvDownload(array $rows, string $filename)
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($rows) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}