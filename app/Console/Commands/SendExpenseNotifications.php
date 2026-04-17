<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Notifix;
use App\Services\NotifixService;
use App\User;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendExpenseNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-expenses {--dry-run : Show what would be done without actually creating notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send expense creation notifications for expenses created after 19:00 to admin users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $notifixService = app(NotifixService::class);
        $isDryRun = $this->option('dry-run');

        // Get expenses created after 19:00 today
        $today = Carbon::today();
        $after1900 = $today->copy()->setTime(19, 0, 0);

        $expenses = Expense::where('created_at', '>=', $after1900)->get();

        $expenseCount = $expenses->count();

        if ($expenseCount === 0) {
            $this->info('No expenses created after 19:00 today.');
            return Command::SUCCESS;
        }

        $this->info("Found {$expenseCount} expenses created after 19:00 today");

        // Get all admin users (role 1)
        $adminUsers = User::whereHas('roles', function($query) {
            $query->where('id', 1);
        })->get();

        if ($adminUsers->isEmpty()) {
            $this->warn('No admin users found.');
            return Command::SUCCESS;
        }

        $notificationsSent = 0;

        foreach ($adminUsers as $admin) {
            if ($isDryRun) {
                $this->line("Would send expense notification to admin: {$admin->first_name} {$admin->last_name}");
            } else {
                $this->createExpenseNotification($notifixService, $admin, $expenseCount);
                $notificationsSent++;
                $this->line("Sent expense notification to admin: {$admin->first_name} {$admin->last_name}");
            }
        }

        $this->info("Expense notifications sent to {$notificationsSent} admin users");

        return Command::SUCCESS;
    }

    /**
     * Create an expense notification for an admin user
     *
     * @param NotifixService $notifixService
     * @param User $admin
     * @param int $expenseCount
     */
    private function createExpenseNotification(NotifixService $notifixService, User $admin, int $expenseCount)
    {
        $notifixService->create($admin->id, [$admin->office_id ?? 0], [
            'id' => uniqid('expense_creation_'),
            'from_id' => 1, // System admin
            'link_from' => null,
            'link_to' => url('/expense'), // Assuming there's a route for expenses
            'type' => 'expense_creation_after_hours',
            'message' => "{$expenseCount} expense(s) were created after 19:00 today and require attention.",
            'positions' => [],
            'office_id' => $admin->office_id,
            'district_id' => $admin->office->district_id ?? null,
            'province_id' => $admin->office->province_id ?? null,
            'to_id' => $admin->id,
            'created_date' => now()->toIso8601String()
        ]);
    }
}