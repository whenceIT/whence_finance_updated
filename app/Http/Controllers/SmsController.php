<?php

namespace App\Http\Controllers;

use App\Services\BulkSMS;
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
            // Since it's a single SMS, we can create a mock collection or modify the service to handle single sends
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
}