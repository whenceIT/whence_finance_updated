<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\Notifix;
use App\Services\NotifixService;
use App\User;
use Illuminate\Console\Command;

class SendOverdueClientsNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-overdue-clients {--dry-run : Show what would be done without actually creating notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send overdue clients notifications to admin users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userInfo = \App\Helpers\GeneralHelper::get_user_info();
        $user = $userInfo->user;
        $role = $userInfo->role; //admin

        $notifixService = app(NotifixService::class);
        $isDryRun = $this->option('dry-run');

        // Get all overdue loans
        $loanModel = new Loan();
        $overdueLoans = $loanModel->overdue_loans();

        $overdueCount = count($overdueLoans);

        if ($overdueCount === 0) {
            $this->info('No overdue loans found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$overdueCount} overdue loans");

        // Get all admin users (role 1)
        $adminUserIds = \App\Models\UserRole::where('role_id', 1)->pluck('user_id');
        $adminUsers = User::whereIn('id', $adminUserIds)->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('No admin users found.');
            return Command::SUCCESS;
        }

        $notificationsSent = 0;

        foreach ($adminUsers as $admin) {
            if ($isDryRun) {
                $this->line("Would send overdue notification to admin: {$admin->first_name} {$admin->last_name}");
            } else {
                $this->createOverdueNotification($notifixService, $admin, $overdueCount);
                $notificationsSent++;
                $this->line("Sent overdue notification to admin: {$admin->first_name} {$admin->last_name}");
            }
        }

        $this->info("Overdue notifications sent to {$notificationsSent} admin users");

        return Command::SUCCESS;
    }

    /**
     * Create an overdue notification for an admin user
     *
     * @param NotifixService $notifixService
     * @param User $admin
     * @param int $overdueCount
     */
    private function createOverdueNotification(NotifixService $notifixService, User $admin, int $overdueCount)
    {
        $notifixService->create($admin->id, [$admin->office_id ?? 0], [
            'id' => uniqid('overdue_clients_'),
            'from_id' => 1, // System admin
            'link_from' => null,
            'link_to' => url('/my-notifications'), // Assuming there's a route for overdue loans
            'type' => 'overdue_clients',
            'message' => "There are {$overdueCount} overdue loans that require attention.",
            'positions' => [],
            'office_id' => $admin->office_id,
            'district_id' => $admin->office->district_id ?? null,
            'province_id' => $admin->office->province_id ?? null,
            'to_id' => $admin->id,
            'created_date' => now()->toIso8601String()
        ]);
    }
}