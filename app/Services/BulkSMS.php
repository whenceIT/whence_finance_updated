<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkSMS
{
    protected $apiUrl;
    protected $user;
    protected $password;
    protected $sender;
    protected $language;

    public function __construct()
    {
        $this->apiUrl = config('services.bulk_sms.url', 'https://mshastra.com/sendsms_api_json.aspx');
        $this->user = config('services.bulk_sms.user');
        $this->password = config('services.bulk_sms.password');
        $this->sender = config('services.bulk_sms.sender', 'WHENCE');
        $this->language = config('services.bulk_sms.language', 'English');
    }

    /**
     * Send SMS to a list of clients.
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $clients
     * @param string $message
     * @return array
     */
    public function sendToClients($clients, $message)
    {
        $smsData = [];
        foreach ($clients as $client) {
            $phone = $this->getClientPhone($client);
            if ($phone) {
                $smsData[] = $this->prepareSmsData($phone, $message);
            }
        }
        return $this->sendBatch($smsData);
    }

    /**
     * Send SMS to a list of users.
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $users
     * @param string $message
     * @return array
     */
    public function sendToUsers($users, $message)
    {
        $smsData = [];
        foreach ($users as $user) {
            $phone = $this->getUserPhone($user);
            if ($phone) {
                $smsData[] = $this->prepareSmsData($phone, $message);
            }
        }
        return $this->sendBatch($smsData);
    }

    /**
     * Send SMS to a list of loans (via associated clients).
     *
     * @param \Illuminate\Database\Eloquent\Collection|array $loans
     * @param string $message
     * @return array
     */
    public function sendToLoans($loans, $message)
    {
        $smsData = [];
        foreach ($loans as $loan) {
            $phone = $this->getLoanPhone($loan);
            if ($phone) {
                $smsData[] = $this->prepareSmsData($phone, $message);
            }
        }
        return $this->sendBatch($smsData);
    }

    /**
     * Prepare SMS data for batch sending.
     *
     * @param string $number
     * @param string $message
     * @return array
     */
    protected function prepareSmsData($number, $message)
    {
        return [
            'user' => $this->user,
            'pwd' => $this->password,
            'number' => $this->formatNumber($number),
            'msg' => $message,
            'sender' => $this->sender,
            'language' => $this->language,
        ];
    }

    /**
     * Send a batch of SMS.
     *
     * @param array $smsData
     * @return array
     */
    protected function sendBatch($smsData)
    {
        Log::info('Preparing to send bulk SMS', ['count' => count($smsData)]);
        if (empty($smsData)) {
            Log::warning('No valid phone numbers found for bulk SMS');
            return [];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl, $smsData);

            Log::info('Bulk SMS API request sent', ['url' => $this->apiUrl, 'payload' => $smsData]);
            Log::info($response->status(), ['response_body' => $response->body()]);
            if ($response->successful()) {
                $responseData = $response->json();
                Log::info('Bulk SMS sent successfully', ['count' => count($smsData), 'response' => $responseData]);
                return $responseData;
            } else {
                Log::error('Bulk SMS send failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [['error' => 'Failed to send SMS batch', 'details' => $response->body()]];
            }
        } catch (\Exception $e) {
            Log::error('Bulk SMS send exception', ['error' => $e->getMessage()]);
            return [['error' => 'Exception occurred', 'details' => $e->getMessage()]];
        }
    }

    /**
     * Get phone number from client.
     *
     * @param \App\Models\Client $client
     * @return string|null
     */
    protected function getClientPhone($client)
    {
        return $client->mobile ?: $client->phone;
    }

    /**
     * Get phone number from user.
     *
     * @param \App\User $user
     * @return string|null
     */
    protected function getUserPhone($user)
    {
        return $user->phone;
    }

    /**
     * Get phone number from loan (via client).
     *
     * @param \App\Models\Loan $loan
     * @return string|null
     */
    protected function getLoanPhone($loan)
    {
        if ($loan->client) {
            return $this->getClientPhone($loan->client);
        }
        return null;
    }

    /**
     * Format phone number with country code if needed.
     *
     * @param string $number
     * @return string
     */
    protected function formatNumber($number)
    {
        // Assume Zambia country code 260
        if (!str_starts_with($number, '260') && !str_starts_with($number, '+260')) {
            if (str_starts_with($number, '0')) {
                $number = '260' . substr($number, 1);
            } else {
                $number = '260' . $number;
            }
        }
        return str_replace('+', '', $number);
    }
}