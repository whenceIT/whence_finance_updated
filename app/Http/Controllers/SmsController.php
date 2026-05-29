<?php

namespace App\Http\Controllers;

use App\Services\BulkSMS;
use App\Models\SmsGateway;
use App\Models\User;
use App\Models\Client;
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
            'office_id' => 'required_if:message_type,overdue|integer',
        ]);

        try {
            if ($request->message_type === 'overdue') {
                $result = SmsGateway::sendOverdueSms($request->office_id);
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

    public function sendToOfficersClients(Request $request): JsonResponse
    {
        $request->validate([
            'office_id' => 'required|integer',
            'user_id' => 'required|integer',
        ]);

        try {
            $officer = User::findOrFail($request->user_id);
            
            // Get loans for this officer with remainder details
            $loans = Loan::loan_remainder($request->user_id);
            
            if (empty($loans)) {
                return response()->json([
                    'success' => false,
                    'error' => 'No active loans found for this officer'
                ], 400);
            }

            $messagesSent = 0;
            $errors = [];

            // Process each loan and send individual SMS
            foreach ($loans as $loanData) {
                try {
                    // Get the loan model to use calculateBalance
                    $loan = Loan::with(['client', 'transactions'])->find($loanData->id);
                    
                    if (!$loan || !$loan->client) {
                        continue;
                    }

                    // Calculate balance
                    $balanceInfo = $loan->calculateBalance();
                    $balance = $balanceInfo['balance'] ?? 0;
                    
                    // Format message with loan details
                    $formattedMessage = sprintf(
                        'Loan Balance: Principal %s, Balance %s',
                        number_format($loanData->principal, 2),
                        number_format($balance, 2)
                    );

                    // Get client phone
                    $client = $loan->client;
                    $phone = $client->phone ?: $client->mobile;
                    
                    if ($phone) {
                        $mockClient = (object) ['mobile' => $phone, 'phone' => $phone];
                        $this->bulkSms->sendToClients([$mockClient], $formattedMessage);
                        $messagesSent++;
                    }
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                    continue;
                }
            }

            return response()->json([
                'success' => true,
                'messages_sent' => $messagesSent,
                'total_loans' => count($loans),
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}