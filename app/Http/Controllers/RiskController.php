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
        return view('risk.heat-map');
    }

    public function branchRanking()
    {
        return view('risk.branch-ranking');
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
        $data = $request->except(['_token']);

        // Handle audit_scope as array
        if (isset($data['s1_audit_scope']) && is_array($data['s1_audit_scope'])) {
            $data['audit_scope'] = json_encode($data['s1_audit_scope']);
        }

        // Calculate fail_count
        $failCount = 0;
        for ($s = 2; $s <= 9; $s++) {
            for ($i = 1; $i <= 10; $i++) { // max 10 items per section
                $field = "s{$s}_{$i}";
                if (isset($data[$field]) && $data[$field] === 'fail') {
                    $failCount++;
                }
            }
        }

        $riskRating = 'low';
        if ($failCount >= 13) $riskRating = 'critical';
        elseif ($failCount >= 8) $riskRating = 'high';
        elseif ($failCount >= 4) $riskRating = 'medium';

        $data['auditor_id'] = Sentinel::getUser()->id;
        $data['fail_count'] = $failCount;
        $data['risk_rating'] = $riskRating;

        $submission = \App\Models\AuditSubmission::create($data);

        return redirect('/risk/overview')->with('success', 'Audit checklist submitted successfully.');
    }

    public function getAuditSectionDetails($submissionId, $section)
    {
        $submission = \App\Models\AuditSubmission::findOrFail($submissionId);
        $section = (int) $section;

        // Define section items (same as in modal)
        $sectionItems = [
            2 => [
                's2_1' => 'Zero physical cash confirmed at branch',
                's2_2' => 'All client payments received via authorised channels only',
                's2_3' => 'Mobile money payments transferred to Withinhere wallet immediately',
                's2_4' => 'Only Branch Manager initiates mobile-money-to-wallet transfers',
                's2_5' => 'Withinhere wallet balance reconciles with loan system records',
                's2_6' => 'No loans disbursed via mobile money or any channel other than Withinhere',
                's2_7' => 'Client disbursement channel preference documented',
                's2_8' => 'No inter-branch transfers without Withinhere audit compliance and authorisation',
                's2_9' => 'Withinhere wallet audit trail reviewed',
                's2_10' => 'Exception or error transactions investigated and resolved',
            ],
            3 => [
                's3_1' => 'Client files complete & verified',
                's3_2' => 'Loan approvals within authorised limits',
                's3_3' => 'No ghost clients (verify via phone calls)',
                's3_4' => 'Loan disbursements match Withinhere wallet outflows',
                's3_5' => 'Interest rates applied correctly',
                's3_6' => 'No expired or rolled-over loans without re-approval',
                's3_7' => 'Loan purpose verification conducted',
            ],
            4 => [
                's4_1' => 'Collections recorded in Withinhere match total repayments due',
                's4_2' => 'No recycling of collections — all repayments go to Withinhere wallet before any disbursement',
            ],
            5 => [
                's5_1' => 'Fraud indicator 1',
                's5_2' => 'Fraud indicator 2',
                's5_3' => 'Fraud indicator 3',
                's5_4' => 'Fraud indicator 4',
                's5_5' => 'Fraud indicator 5',
                's5_6' => 'Fraud indicator 6',
                's5_7' => 'Fraud indicator 7',
            ],
            6 => [
                's6_1' => 'Staff item 1',
                's6_2' => 'Staff item 2',
                's6_3' => 'Staff item 3',
                's6_4' => 'Staff item 4',
                's6_5' => 'Staff item 5',
                's6_6' => 'Staff item 6',
                's6_7' => 'Staff item 7',
                's6_8' => 'Staff item 8',
            ],
            7 => [
                's7_1' => 'System item 1',
                's7_2' => 'System item 2',
                's7_3' => 'System item 3',
                's7_4' => 'System item 4',
                's7_5' => 'System item 5',
                's7_6' => 'System item 6',
                's7_7' => 'System item 7',
                's7_8' => 'System item 8',
            ],
            8 => [
                's8_1' => 'Reporting item 1',
                's8_2' => 'Reporting item 2',
                's8_3' => 'Reporting item 3',
                's8_4' => 'Reporting item 4',
                's8_5' => 'Reporting item 5',
                's8_6' => 'Reporting item 6',
            ],
            9 => [
                's9_1' => 'Conclusion item 1',
                's9_2' => 'Conclusion item 2',
            ],
        ];

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
}