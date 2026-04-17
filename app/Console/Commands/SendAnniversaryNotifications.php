<?php

namespace App\Console\Commands;

use App\Helpers\GeneralHelper;
use App\Models\Notifix;
use App\Services\NotifixService;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAnniversaryNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-anniversaries {--dry-run : Show what would be done without actually creating notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send anniversary notifications to users who are 3 and 6 months old';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifixService = app(NotifixService::class);

        // Find users who are exactly 3 months old (within 1 day tolerance)
        $threeMonthsAgo = Carbon::now()->subMonths(3);
        $threeMonthUsers = User::whereBetween('created_at', [
            $threeMonthsAgo->copy()->subDay(),
            $threeMonthsAgo->copy()->addDay()
        ])->get();

        // Find users who are exactly 6 months old (within 1 day tolerance)
        $sixMonthsAgo = Carbon::now()->subMonths(6);
        $sixMonthUsers = User::whereBetween('created_at', [
            $sixMonthsAgo->copy()->subDay(),
            $sixMonthsAgo->copy()->addDay()
        ])->get();

        // Process 3-month anniversary users
        foreach ($threeMonthUsers as $user) {
            $this->createAnniversaryNotification($notifixService, $user, 3);
            $this->line("Created 3-month notification for: {$user->first_name} {$user->last_name}");
        }

        // Process 6-month anniversary users
        foreach ($sixMonthUsers as $user) {
            $this->createAnniversaryNotification($notifixService, $user, 6);
            $this->line("Created 6-month notification for: {$user->first_name} {$user->last_name}");
        }

        //send the list of users who are celebrating their anniversaries to the admin
        // Find the first admin user (role 1) to send the summary notification
        $manager = GeneralHelper::get_my_manager(); // For now, send to first user as admin
        $message = $this->buildAnniversarySummaryMessage($threeMonthUsers, $sixMonthUsers);

        if ($threeMonthUsers->count() > 0 || $sixMonthUsers->count() > 0) {
            $notifixService->create($manager['poa'], [], [
                'id' => uniqid('anniversary_summary_'),
                'from_id' => 1, // System admin ID
                'link_from' => null,
                'link_to' => null,
                'type' => 'anniversary_summary',
                'message' => $message,
                'positions' => [],
                'office_id' => null,
                'district_id' => null,
                'province_id' => null,
                'to_id' => $manager['poa'],
                'created_date' => now()->toIso8601String()
            ]);
        }
          
        $totalProcessed = $threeMonthUsers->count() + $sixMonthUsers->count();
        return Command::SUCCESS;
    }

    /**
     * Create an anniversary notification for a user
     *
     * @param NotifixService $notifixService
     * @param User $user
     * @param int $months
     */
    private function createAnniversaryNotification(NotifixService $notifixService, User $user, int $months)
    {
        $messages = [
            3 => "Congratulations on completing 3 months with our organization! 🎉",
            6 => "Congratulations on reaching 6 months with our organization! 🎊"
        ];

        $types = [
            3 => 'user_anniversary_3_months',
            6 => 'user_anniversary_6_months'
        ];

        $notifixService->create($user->id, [$user->office_id ?? 0], [
            'id' => uniqid('anniversary_'),
            'from_id' => 1, // System admin
            'link_from' => null,
            'link_to' => null,
            'type' => $types[$months],
            'message' => $messages[$months],
            'positions' => [],
            'office_id' => $user->office_id,
            'district_id' => $user->office->district_id ?? null,
            'province_id' => $user->office->province_id ?? null,
            'to_id' => $user->id,
            'created_date' => now()->toIso8601String()
        ]);
    }

    /**
     * Build a detailed anniversary summary message with user names and office names
     *
     * @param \Illuminate\Database\Eloquent\Collection $threeMonthUsers
     * @param \Illuminate\Database\Eloquent\Collection $sixMonthUsers
     * @return string
     */
    private function buildAnniversarySummaryMessage($threeMonthUsers, $sixMonthUsers)
    {
        $message = "🎉 Provision and Probation Anniversary Summary:\n\n";

        if ($threeMonthUsers->count() > 0) {
            $message .= "3-Month Celebrants:\n";
            foreach ($threeMonthUsers as $user) {
                $officeName = $user->office ? $user->office->name : 'No Office';
                $message .= "• {$user->first_name} {$user->last_name} ({$officeName})\n";
            }
            $message .= "\n";
        }

        if ($sixMonthUsers->count() > 0) {
            $message .= "6-Month Celebrants:\n";
            foreach ($sixMonthUsers as $user) {
                $officeName = $user->office ? $user->office->name : 'No Office';
                $message .= "• {$user->first_name} {$user->last_name} ({$officeName})\n";
            }
            $message .= "\n";
        }

        if ($threeMonthUsers->count() === 0 && $sixMonthUsers->count() === 0) {
            $message .= "No anniversaries to celebrate today.\n";
        }

        $message .= "Total: {$threeMonthUsers->count()} × 3-month, {$sixMonthUsers->count()} × 6-month";

        return $message;
    }
}
