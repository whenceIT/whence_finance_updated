<?php

namespace App\Http\Controllers\Recoveries;

use App\Http\Controllers\Controller;
use App\Models\RecoveryCase;
use App\Services\NudgeService;
use Illuminate\Http\Request;

class RecoveryNudgeController extends Controller
{
    public function __construct(private NudgeService $nudge) {}

    /**
     * Show the bulk nudge compose page.
     */
    public function compose(Request $request)
    {
        $filter   = $request->only(['category', 'status', 'assigned_specialist_id']);
        $query    = RecoveryCase::with('client', 'assignedSpecialist')->active();

        if (!empty($filter['category'])) {
            $query->where('category', $filter['category']);
        }
        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }
        if (!empty($filter['assigned_specialist_id'])) {
            $query->where('assigned_specialist_id', $filter['assigned_specialist_id']);
        }

        $cases      = $query->orderBy('loan_outstanding_amount', 'desc')->get();
        $categories = RecoveryCase::CATEGORIES;
        $specialists = \App\Models\User::orderBy('first_name')->get();

        $defaultSms = "Dear {name}, this is a reminder that your loan account has an outstanding balance of {balance}. Please make payment arrangements at your earliest convenience. Case Ref: {case_number}.";
        $defaultWa  = "Hello {name} 👋\n\nWe'd like to remind you about your outstanding loan balance of *{balance}*.\n\nPlease reach out to us at your earliest convenience to discuss payment options.\n\nRef: {case_number}";

        return view('recoveries.nudges.compose', compact(
            'cases', 'categories', 'specialists', 'filter',
            'defaultSms', 'defaultWa'
        ));
    }

    /**
     * Send bulk nudge to selected cases.
     */
    public function sendBulk(Request $request)
    {
        $request->validate([
            'case_ids'   => 'required|array|min:1',
            'case_ids.*' => 'exists:recovery_cases,id',
            'channel'    => 'required|in:sms,whatsapp',
            'message'    => 'required|string|max:1000',
        ]);

        $result = $this->nudge->sendBulk(
            $request->case_ids,
            $request->channel,
            $request->message
        );

        $msg = "Sent {$result['sent']} nudge(s) successfully.";
        if ($result['failed'] > 0) {
            $msg .= " {$result['failed']} failed.";
        }

        return redirect()->back()->with('success', $msg)
            ->with('nudge_errors', $result['errors']);
    }

    /**
     * Send a single nudge from the case show page.
     */
    public function sendSingle(Request $request, $id)
    {
        $request->validate([
            'channel' => 'required|in:sms,whatsapp',
            'message' => 'required|string|max:1000',
        ]);

        $case   = RecoveryCase::with('client', 'loan')->findOrFail($id);
        $result = $this->nudge->send($case, $request->channel, $request->message);

        if ($result['success']) {
            return redirect()->back()->with('success',
                strtoupper($request->channel) . ' nudge sent to ' .
                ($case->client->phone ?? 'client') . '.'
            );
        }

        return redirect()->back()->with('error', 'Nudge failed: ' . $result['message']);
    }
}