<?php

namespace App\Services;

use App\Models\RecoveryCase;
use App\Models\RecoveryNudge;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NudgeService
{
    /**
     * Send a nudge (SMS or WhatsApp) to a case's client.
     * Returns ['success' => bool, 'message' => string]
     */
    public function send(RecoveryCase $case, string $channel, string $messageBody): array
    {
        $phone = $this->normalisePhone($case->client->phone ?? '');

        if (!$phone) {
            return ['success' => false, 'message' => 'No phone number on record for this client.'];
        }

        try {
            $result = $this->sendAfricasTalking($phone, $messageBody, $channel);

            RecoveryNudge::create([
                'recovery_case_id' => $case->id,
                'sent_by'          => optional(\Sentinel::getUser())->id,
                'channel'          => $channel,
                'phone_number'     => $phone,
                'message'          => $messageBody,
                'status'           => $result['success'] ? 'sent' : 'failed',
                'gateway_response' => json_encode($result['raw'] ?? []),
            ]);

            \App\Models\RecoveryActivity::create([
                'recovery_case_id' => $case->id,
                'performed_by'     => optional(\Sentinel::getUser())->id,
                'activity_type'    => $channel === 'whatsapp' ? 'whatsapp_sent' : 'sms_sent',
                'description'      => strtoupper($channel) . ' nudge sent to ' . $phone,
                'outcome'          => $result['success'] ? 'delivered' : 'failed',
            ]);

            return $result;

        } catch (\Throwable $e) {
            Log::error('NudgeService error', ['case' => $case->id, 'error' => $e->getMessage()]);
            return ['success' => false, 'message' => 'Gateway error: ' . $e->getMessage()];
        }
    }

    /**
     * Send bulk nudges to multiple cases.
     */
    public function sendBulk(array $caseIds, string $channel, string $messageTemplate): array
    {
        $sent = $failed = 0;
        $errors = [];

        foreach ($caseIds as $id) {
            $case = RecoveryCase::with('client', 'loan')->find($id);
            if (!$case) continue;

            $body   = $this->buildMessage($case, $messageTemplate);
            $result = $this->send($case, $channel, $body);

            $result['success'] ? $sent++ : $failed++;
            if (!$result['success']) {
                $errors[] = ($case->client->first_name ?? '#' . $case->id) . ': ' . $result['message'];
            }
        }

        return compact('sent', 'failed', 'errors');
    }

    /**
     * Build message from template — {name}, {balance}, {case_number} only.
     */
    public function buildMessage(RecoveryCase $case, string $template): string
    {
        $client = $case->client;
        $name   = ($client->client_type ?? '') === 'business'
            ? ($client->full_name ?? '')
            : trim(($client->first_name ?? '') . ' ' . ($client->last_name ?? ''));

        $outstanding = ($case->loan_outstanding_amount ?? 0) - ($case->amount_recovered ?? 0);

        return strtr($template, [
            '{name}'        => $name ?: 'Valued Client',
            '{balance}'     => 'K' . number_format($outstanding, 2),
            '{case_number}' => $case->case_number,
        ]);
    }

    // ── Africa's Talking ────────────────────────────────────────

    private function sendAfricasTalking(string $phone, string $message, string $channel): array
    {
        $apiKey   = config('nudge.at_api_key');
        $username = config('nudge.at_username');

        if ($channel === 'whatsapp') {
            $response = Http::withHeaders(['apiKey' => $apiKey, 'Accept' => 'application/json'])
                ->asForm()
                ->post('https://api.africastalking.com/version1/messaging/whatsapp', [
                    'username' => $username,
                    'to'       => $phone,
                    'message'  => $message,
                ]);
        } else {
            $response = Http::withHeaders(['apiKey' => $apiKey, 'Accept' => 'application/json'])
                ->asForm()
                ->post('https://api.africastalking.com/version1/messaging', [
                    'username' => $username,
                    'to'       => $phone,
                    'message'  => $message,
                    'from'     => config('nudge.at_sender_id'),
                ]);
        }

        $body = $response->json();

        // AT returns statusCode 101 for success on SMS
        $atStatus = $body['SMSMessageData']['Recipients'][0]['statusCode'] ?? null;
        $success  = $response->successful() && ($atStatus === null || $atStatus === 101);

        return [
            'success' => $success,
            'message' => $success ? 'Sent' : ($body['SMSMessageData']['Message'] ?? 'Failed'),
            'raw'     => $body,
        ];
    }

    // ── Helpers ─────────────────────────────────────────────────

    private function normalisePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (!$phone) return '';

        // Zambia: strip leading 0, prepend +260
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            $phone = '260' . substr($phone, 1);
        }
        if (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }
        return $phone;
    }
}
