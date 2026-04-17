<?php

namespace App\Console\Commands;

use App\Models\GeneralUpload;
use App\Models\Notifix;
use App\Services\NotifixService;
use App\User;
use Illuminate\Console\Command;

class SendTrainingLinksNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-training-links {--dry-run : Show what would be done without actually creating notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send training links notifications to users with position-specific content';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifixService = app(NotifixService::class);
        $isDryRun = $this->option('dry-run');

        // Get all users who have positions
        $users = User::inRandomOrder()->limit(20)->get();

        $this->info("Found {$users->count()} users with positions");

        $notificationsSent = 0;

        foreach ($users as $user) {

            // Find a random general upload that matches the user's position
            $selectedUpload = GeneralUpload::inRandomOrder()->first();

            if (!$selectedUpload) {
                $this->line("No matching uploads for user: {$user->first_name} {$user->last_name}");
                continue;
            }

            if ($isDryRun) {
                $this->line("Would send notification to {$user->first_name} {$user->last_name} for upload: {$selectedUpload->name}");
            } else {
                $this->createTrainingNotification($notifixService, $user, $selectedUpload);
                $notificationsSent++;
                $this->line("Sent notification to {$user->first_name} {$user->last_name} for upload: {$selectedUpload->name}");
            }
        }

        $this->info("Training links notifications sent: {$notificationsSent}");

        return Command::SUCCESS;
    }

    /**
     * Create a training notification for a user
     *
     * @param NotifixService $notifixService
     * @param User $user
     * @param GeneralUpload $upload
     */
    private function createTrainingNotification(NotifixService $notifixService, User $user, GeneralUpload $upload)
    {
        $notifixService->create($user->id, [$user->office_id ?? 0], [
            'id' => uniqid('training_link_'),
            'from_id' => 1, // System admin
            'link_from' => null,
            'link_to' => url('/learning/general-uploads/watch-and-learning?upload=' . $upload->id),
            'type' => 'training_recommendation',
            'message' => "Recommended for you: Check out this training resource - {$upload->name}. Click to watch now!",
            'positions' => [],
            'office_id' => $user->office_id,
            'district_id' => $user->office->district_id ?? null,
            'province_id' => $user->office->province_id ?? null,
            'to_id' => $user->id,
            'created_date' => now()->toIso8601String()
        ]);
    }
}