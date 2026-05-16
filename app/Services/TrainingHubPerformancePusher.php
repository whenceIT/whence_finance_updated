<?php

namespace App\Services;

use App\Models\Client;
use App\Models\GeneralTopic;
use App\Models\GeneralUpload;
use App\Models\Loan;
use App\Models\Office;
use App\Models\ResignationLetter;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrainingHubPerformancePusher
{
    /*
    |--------------------------------------------------------------------------
    | Performance-Linked Learning Triggers
    |--------------------------------------------------------------------------
    |
    | This service analyses performance indicators across the platform and
    | pushes relevant training-hub content (GeneralUpload / GeneralTopic)
    | to users whose metrics suggest they would benefit from additional
    | learning material.
    |
    | Triggers:
    |  1. High Loan Defaults    → vetting & due-diligence content
    |  2. High Staff Turnover   → leadership & management content
    |  3. Declining Client Base → client management content
    |
    */

    /** Performance notification types */
    const TYPE_LOAN_DEFAULT   = 'perf_loan_default';
    const TYPE_STAFF_TURNOVER = 'perf_staff_turnover';
    const TYPE_LOW_CLIENTS    = 'perf_low_clients';

    const ALL_PERF_TYPES = [
        self::TYPE_LOAN_DEFAULT,
        self::TYPE_STAFF_TURNOVER,
        self::TYPE_LOW_CLIENTS,
    ];

    /**
     * Run all performance-linked learning triggers.
     *
     * @return array  Summary of notifications sent per trigger.
     */
    public static function run(): array
    {
        $summary = [
            'high_loan_defaults'    => 0,
            'high_staff_turnover'   => 0,
            'declining_client_base' => 0,
        ];

        $summary['high_loan_defaults']    = static::pushForHighLoanDefaults();
        $summary['high_staff_turnover']   = static::pushForHighStaffTurnover();
        $summary['declining_client_base'] = static::pushForDecliningClientBase();

        Log::info('[TrainingHubPerformancePusher] Run complete', $summary);

        return $summary;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 1. High Loan Defaults → push vetting & due-diligence content
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Identify loan officers with defaulted/written-off loans and push
     * vetting & due-diligence GeneralUpload/GeneralTopic content.
     *
     * @return int  Number of users notified.
     */
    public static function pushForHighLoanDefaults(): int
    {
        $notified = 0;
        $notifixService = app(NotifixService::class);
        $cutoff = now()->subDays(90);

        $officerDefaults = Loan::whereIn('status', ['defaulted', 'written_off'])
            ->where('created_at', '>=', $cutoff)
            ->whereNotNull('loan_officer_id')
            ->select('loan_officer_id', DB::raw('COUNT(*) as default_count'))
            ->groupBy('loan_officer_id')
            ->having('default_count', '>=', 1)
            ->get();

        if ($officerDefaults->isEmpty()) {
            return 0;
        }

        $keywords = [
            'vetting', 'due diligence', 'due-diligence', 'credit assessment',
            'loan appraisal', 'risk assessment', 'underwriting', 'loan analysis',
        ];

        $uploads = static::findUploadsByKeywords($keywords);
        $topics  = static::findTopicsByKeywords($keywords);
        $materialLink = static::buildUploadLink($uploads);
        $materialIds  = static::serializeMaterialIds($uploads, $topics);

        foreach ($officerDefaults as $row) {
            $user = User::find($row->loan_officer_id);
            if (!$user) continue;
            if (static::wasNotifiedToday($user->id, self::TYPE_LOAN_DEFAULT)) continue;

            $officeIds = $user->office_id ? [$user->office_id] : [];

            $notifixService->create($user->id, $officeIds, [
                'id'           => self::TYPE_LOAN_DEFAULT . '_' . $user->id . '_' . now()->format('Ymd'),
                'from_id'      => null,
                'link_from'    => null,
                'link_to'      => $materialLink,
                'type'         => self::TYPE_LOAN_DEFAULT,
                'message'      => 'You have ' . $row->default_count
                    . ' defaulted/written-off loan(s) in the last 90 days. '
                    . 'We recommend reviewing training materials on vetting and due diligence.',
                'perf_uploads' => $materialIds['uploads'],
                'perf_topics'  => $materialIds['topics'],
                'created_date' => now()->toIso8601String(),
            ]);

            $notified++;
        }

        return $notified;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 2. High Staff Turnover → push leadership / management content
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Identify offices with high staff turnover and push leadership &
     * management GeneralUpload/GeneralTopic content to the branch manager.
     *
     * @return int  Number of users notified.
     */
    public static function pushForHighStaffTurnover(): int
    {
        $notified = 0;
        $notifixService = app(NotifixService::class);
        $cutoff = now()->subDays(180);
        $threshold = 2;

        $highTurnoverOffices = ResignationLetter::where('resignation_letters.created_at', '>=', $cutoff)
            ->join('users', 'users.id', '=', 'resignation_letters.user_id')
            ->whereNotNull('users.office_id')
            ->select('users.office_id', DB::raw('COUNT(*) as resignation_count'))
            ->groupBy('users.office_id')
            ->having('resignation_count', '>=', $threshold)
            ->get();

        if ($highTurnoverOffices->isEmpty()) {
            return 0;
        }

        $keywords = [
            'leadership', 'management', 'team building', 'staff retention',
            'people management', 'supervisory', 'coaching', 'mentoring',
        ];

        $uploads = static::findUploadsByKeywords($keywords);
        $topics  = static::findTopicsByKeywords($keywords);
        $materialLink = static::buildUploadLink($uploads);
        $materialIds  = static::serializeMaterialIds($uploads, $topics);

        foreach ($highTurnoverOffices as $row) {
            $office = Office::find($row->office_id);
            if (!$office) continue;

            // Find branch manager
            $targetUser = null;
            if ($office->manager_id) {
                $targetUser = User::find($office->manager_id);
            }
            if (!$targetUser) {
                $targetUser = User::where('office_id', $office->id)
                    ->whereHas('roles', fn($q) => $q->where('roles.id', 4))
                    ->first();
            }
            if (!$targetUser) continue;
            if (static::wasNotifiedToday($targetUser->id, self::TYPE_STAFF_TURNOVER)) continue;

            $notifixService->create($targetUser->id, [$office->id], [
                'id'           => self::TYPE_STAFF_TURNOVER . '_' . $targetUser->id . '_' . now()->format('Ymd'),
                'from_id'      => null,
                'link_from'    => null,
                'link_to'      => $materialLink,
                'type'         => self::TYPE_STAFF_TURNOVER,
                'message'      => 'Your branch (' . htmlspecialchars($office->name)
                    . ') has had ' . $row->resignation_count
                    . ' resignation(s) in the last 6 months. '
                    . 'We recommend reviewing leadership and management training materials.',
                'perf_uploads' => $materialIds['uploads'],
                'perf_topics'  => $materialIds['topics'],
                'created_date' => now()->toIso8601String(),
            ]);

            $notified++;
        }

        return $notified;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // 3. Declining Client Base → push client management content
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Identify staff with a low active client count and push client
     * management GeneralUpload/GeneralTopic content.
     *
     * @return int  Number of users notified.
     */
    public static function pushForDecliningClientBase(): int
    {
        $notified = 0;
        $notifixService = app(NotifixService::class);
        $lowClientThreshold = 5;

        $lowClientUsers = Client::whereNull('deleted_at')
            ->whereNotNull('staff_id')
            ->select('staff_id', DB::raw('COUNT(*) as client_count'))
            ->groupBy('staff_id')
            ->having('client_count', '<=', $lowClientThreshold)
            ->get();

        if ($lowClientUsers->isEmpty()) {
            return 0;
        }

        $keywords = [
            'client management', 'customer service', 'client retention',
            'client acquisition', 'relationship management', 'customer relations',
            'client engagement', 'business development',
        ];

        $uploads = static::findUploadsByKeywords($keywords);
        $topics  = static::findTopicsByKeywords($keywords);
        $materialLink = static::buildUploadLink($uploads);
        $materialIds  = static::serializeMaterialIds($uploads, $topics);

        foreach ($lowClientUsers as $row) {
            $user = User::find($row->staff_id);
            if (!$user) continue;
            if (static::wasNotifiedToday($user->id, self::TYPE_LOW_CLIENTS)) continue;

            $officeIds = $user->office_id ? [$user->office_id] : [];

            $notifixService->create($user->id, $officeIds, [
                'id'           => self::TYPE_LOW_CLIENTS . '_' . $user->id . '_' . now()->format('Ymd'),
                'from_id'      => null,
                'link_from'    => null,
                'link_to'      => $materialLink,
                'type'         => self::TYPE_LOW_CLIENTS,
                'message'      => 'You currently have ' . $row->client_count
                    . ' active client(s), which is below the recommended threshold. '
                    . 'We recommend reviewing client management training materials.',
                'perf_uploads' => $materialIds['uploads'],
                'perf_topics'  => $materialIds['topics'],
                'created_date' => now()->toIso8601String(),
            ]);

            $notified++;
        }

        return $notified;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Read-side: get pushed recommendations for current user (for the UI pop)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Get all active performance-linked recommendations for a given user.
     * Used by the blade pop-up component in master.blade.php.
     *
     * Returns an array of recommendation groups, each with:
     *   - type, label, icon, color, message
     *   - uploads (GeneralUpload collection)
     *   - topics  (GeneralTopic collection)
     *
     * @param  int  $userId
     * @return array
     */
    public static function getRecommendationsForUser(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $meta = [
            self::TYPE_LOAN_DEFAULT => [
                'label' => 'Vetting & Due Diligence',
                'icon'  => 'fa-exclamation-triangle',
                'color' => '#e74c3c',
                'category' => 'loan',
                'message' => 'Based on your loan performance, here are some resources to improve vetting and due diligence.',
            ],
            self::TYPE_STAFF_TURNOVER => [
                'label' => 'Leadership & Management',
                'icon'  => 'fa-users',
                'color' => '#f39c12',
                'category' => 'staff',
                'message' => 'With low staff retention in your office, consider these leadership resources.',
            ],
            self::TYPE_LOW_CLIENTS => [
                'label' => 'Client Management',
                'icon'  => 'fa-user-plus',
                'color' => '#3498db',
                'category' => 'client',
                'message' => 'To grow your client base, check out these client management resources.',
            ],
        ];

        $recommendations = [];

        // Check for loan defaults
        $hasLoanDefaults = Loan::where('borrower_id', $userId)
            ->where(function($q) {
                $q->where('first_repayment_date', '<', now())
                   ->orWhere('status', 'defaulted');
            })->exists();

        if ($hasLoanDefaults) {
            $uploads = GeneralUpload::where('category', 'loan')->limit(5)->get();
            $topics = GeneralTopic::whereHas('uploads', function($q) {
                $q->where('category', 'loan');
            })->limit(3)->get();

            if (!$uploads->isEmpty() || !$topics->isEmpty()) {
                $recommendations[self::TYPE_LOAN_DEFAULT] = [
                    'type'    => self::TYPE_LOAN_DEFAULT,
                    'label'   => $meta[self::TYPE_LOAN_DEFAULT]['label'],
                    'icon'    => $meta[self::TYPE_LOAN_DEFAULT]['icon'],
                    'color'   => $meta[self::TYPE_LOAN_DEFAULT]['color'],
                    'message' => $meta[self::TYPE_LOAN_DEFAULT]['message'],
                    'link'    => url('/learning'),
                    'uploads' => $uploads,
                    'topics'  => $topics,
                ];
            }
        }

        // Check for low clients
        $clientCount = $user->client_users()->count();
        if ($clientCount < 15) {
            $uploads = GeneralUpload::where('category', 'client')->limit(5)->get();
            $topics = GeneralTopic::whereHas('uploads', function($q) {
                $q->where('category', 'client');
            })->limit(3)->get();

            if (!$uploads->isEmpty() || !$topics->isEmpty()) {
                $recommendations[self::TYPE_LOW_CLIENTS] = [
                    'type'    => self::TYPE_LOW_CLIENTS,
                    'label'   => $meta[self::TYPE_LOW_CLIENTS]['label'],
                    'icon'    => $meta[self::TYPE_LOW_CLIENTS]['icon'],
                    'color'   => $meta[self::TYPE_LOW_CLIENTS]['color'],
                    'message' => $meta[self::TYPE_LOW_CLIENTS]['message'],
                    'link'    => url('/learning'),
                    'uploads' => $uploads,
                    'topics'  => $topics,
                ];
            }
        }

        // Check for low staff turnover
        $staffCount = User::where('office_id', $user->office_id)
            ->whereHas('roles', function($q) {
                $q->where('id', 4); // Assuming role_id 4 is staff
            })->count();

        if ($staffCount < 15) {
            $uploads = GeneralUpload::where('category', 'staff')->limit(5)->get();
            $topics = GeneralTopic::whereHas('uploads', function($q) {
                $q->where('category', 'staff');
            })->limit(3)->get();

            if (!$uploads->isEmpty() || !$topics->isEmpty()) {
                $recommendations[self::TYPE_STAFF_TURNOVER] = [
                    'type'    => self::TYPE_STAFF_TURNOVER,
                    'label'   => $meta[self::TYPE_STAFF_TURNOVER]['label'],
                    'icon'    => $meta[self::TYPE_STAFF_TURNOVER]['icon'],
                    'color'   => $meta[self::TYPE_STAFF_TURNOVER]['color'],
                    'message' => $meta[self::TYPE_STAFF_TURNOVER]['message'],
                    'link'    => url('/learning'),
                    'uploads' => $uploads,
                    'topics'  => $topics,
                ];
            }
        }

        return array_values($recommendations);
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Helper Methods
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Search GeneralUpload by name matching any of the given keywords.
     */
    private static function findUploadsByKeywords(array $keywords): \Illuminate\Support\Collection
    {
        return GeneralUpload::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%');
                }
            })
            ->orderBy('views_count', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Search GeneralTopic by name or description matching any of the given keywords.
     */
    private static function findTopicsByKeywords(array $keywords): \Illuminate\Support\Collection
    {
        return GeneralTopic::where(function ($query) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $query->orWhere('name', 'like', '%' . $keyword . '%')
                          ->orWhere('description', 'like', '%' . $keyword . '%');
                }
            })
            ->limit(3)
            ->get();
    }

    /**
     * Build a link to the first matching upload, or fall back to the
     * general learning dashboard.
     */
    private static function buildUploadLink(\Illuminate\Support\Collection $uploads): string
    {
        if ($uploads->isNotEmpty()) {
            return url('/learning/general-uploads/' . $uploads->first()->id);
        }

        return url('/learning');
    }

    /**
     * Serialize upload/topic IDs into the notification payload so the
     * pop-up component can retrieve the actual models later.
     */
    private static function serializeMaterialIds(
        \Illuminate\Support\Collection $uploads,
        \Illuminate\Support\Collection $topics
    ): array {
        return [
            'uploads' => $uploads->pluck('id')->toArray(),
            'topics'  => $topics->pluck('id')->toArray(),
        ];
    }

    /**
     * Check whether a user was already notified for a specific trigger type
     * today to prevent duplicate notifications.
     */
    private static function wasNotifiedToday(int $userId, string $type): bool
    {
        $notifixService = app(NotifixService::class);
        $record = $notifixService->getMyNotifix($userId);

        if (!$record || !$record->note) {
            return false;
        }

        $today = now()->format('Y-m-d');

        foreach ($record->note as $note) {
            if (
                isset($note['type']) && $note['type'] === $type
                && isset($note['created_date'])
                && str_starts_with($note['created_date'], $today)
            ) {
                return true;
            }
        }

        return false;
    }
}
