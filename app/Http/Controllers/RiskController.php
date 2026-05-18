<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class RiskController extends Controller
{
    public function __construct()
    {
        $this->middleware('sentinel');
    }
    
    public function overview()
    {
        return view('risk.overview');
    }

    public function auditTrail()
    {
        return redirect()->route('audits.index');
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

        // Latest audit submission per office (safe for all MySQL versions)
        $latestSubs = \App\Models\AuditSubmission::query()
            ->select('office_id', 'risk_rating', 'fail_count', 'audit_date', 'audit_type')
            ->whereNotNull('office_id')
            ->whereIn('id', function ($q) {
                $q->selectRaw('MAX(id)')->from('audit_submissions')->whereNotNull('office_id')->groupBy('office_id');
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

    public function fraudFeed()
    {
        return view('risk.fraud-feed');
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
            
            // Optional: Add permission check here
            // if (!Sentinel::hasAccess('audits.delete')) {
            //     return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            // }
            
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
}