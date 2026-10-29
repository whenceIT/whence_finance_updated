<?php

namespace App\Http\Controllers;

use App\Services\BulkSMS;
use App\Models\SmsGateway;
use App\Models\User;
use App\Models\Client;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SmsController extends Controller
{
    protected $bulkSms;

    public function __construct(BulkSMS $bulkSms)
    {
        $this->bulkSms = $bulkSms;
    }

    public function sendSms(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        try {
            $mockClient = (object) ['mobile' => $request->phone, 'phone' => $request->phone];
            $result = $this->bulkSms->sendToClients([$mockClient], $request->message);

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function sendBulkSms(Request $request): JsonResponse
    {
        $request->validate([
            'message_type' => 'required|string',
            'office_id' => 'required_if:message_type,overdue,balances|integer',
        ]);

        try {
            if ($request->message_type === 'overdue') {
                $result = SmsGateway::sendOverdueSms($request->office_id);
                return response()->json([
                    'success' => true,
                    'data' => $result,
                ]);
            } elseif ($request->message_type === 'balances') {
                $result = SmsGateway::sendBalanceReminderSms($request->office_id);
                return response()->json([
                    'success' => true,
                    'data' => $result,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid message type',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOfficeUsers(int $officeId): JsonResponse
    {
        $users = User::where('office_id', $officeId)
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name']);

        return response()->json(
            $users->map(fn($u) => [
                'id'   => $u->id,
                'name' => trim($u->first_name . ' ' . $u->last_name),
            ])
        );
    }

    public function sendToOfficersClients(Request $request): JsonResponse
    {
        $request->validate([
            'office_id'    => 'required|integer',
            'user_id'      => 'nullable|integer',
            'message_type' => 'required|in:overdue,balances',
        ]);

        try {
            if ($request->user_id) {
                $officers = [User::findOrFail($request->user_id)];
            } else {
                $officers = User::where('office_id', $request->office_id)->get();
            }

            $messagesSent = 0;
            $errors       = [];
            $totalLoans   = 0;

            foreach ($officers as $officer) {
                $loans = Loan::loan_remainder($officer->id);
                if (empty($loans)) {
                    continue;
                }
                $totalLoans += count($loans);

                foreach ($loans as $loanData) {
                try {
                    $loan = Loan::with(['client', 'transactions'])->find($loanData->id);

                    if (!$loan || !$loan->client) {
                        continue;
                    }

                    $client      = $loan->client;
                    $phone       = $client->phone ?: $client->mobile;

                    if (!$phone) {
                        continue;
                    }

                    $balanceInfo = $loan->calculateBalance();
                    $balance     = $balanceInfo['balance'] ?? 0;
                    $principal   = $loanData->principal ?? $loan->approved_amount ?? 0;

                    if ($request->message_type === 'overdue') {
                        $message  = 'Dear Customer, this is a reminder that your loan of ZMW ' . number_format($principal, 2);
                        $message .= ' with outstanding balance of ZMW ' . number_format($balance, 2);
                        $message .= ' is overdue. Kindly make your payment to avoid penalties or further legal action. For assistance, contact 0773425477.';
                    } else {
                        // balances
                        $principalWithInterest = $principal + ($principal * 0.4);
                        $message  = 'Dear Customer, your loan of ZMW ' . number_format($principalWithInterest, 2);
                        $message .= ' has an outstanding balance of ZMW ' . number_format($balance, 2);
                        $message .= '. Please make your payment on time to avoid penalties. For assistance, contact 0773425477.';
                    }

                    $mockClient = (object) ['mobile' => $phone, 'phone' => $phone];
                    $this->bulkSms->sendToClients([$mockClient], $message);
                    $messagesSent++;

                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
                }

            }

            return response()->json([
                'success'       => true,
                'messages_sent' => $messagesSent,
                'total_loans'   => $totalLoans,
                'errors'        => $errors,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}