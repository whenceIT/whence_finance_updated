<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\GeneralHelper;
use App\Services\NotifixService;
use App\Models\User;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class Notifix extends Model
{
    protected $table = 'notifix';

    protected $fillable = [
        'user_id',
        'positions',
        'note',
        'office_id',
        'district_id',
        'province_id',
        'to_id',
        'unread',
    ];

    protected $casts = [
        'positions' => 'array',
        'note' => 'array',
        'unread' => 'boolean',
    ];

    /**
     * Get the user that owns the notifix record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the notification count for a specific user.
     *
     * @param int $userId
     * @return int
     */
    public static function getUserNotificationCount($userId)
    {
        return self::where('user_id', $userId)->count();
    }

    /**
     * Notify Branch Manager to approve a new loan application.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @param float $amount The loan principal amount
     * @return void
     */
    public static function notifyBmToApproveNewLoan($loan, $client, $amount)
    {
        $manager = GeneralHelper::get_my_manager();
        $notifixService = app(NotifixService::class);

        $notifixService->create($manager['bm'], [Sentinel::getUser()->office->id], [
            'id' => uniqid(),
            'loan_id' => $loan->id,
            'from_id' => Sentinel::getUser()->id,
            'link_from' => null,
            'link_to' => url('/loan/managers_pending_approval'),
            'type' => 'loan_created',
            'message' => 'Pending approval: ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' has requested a loan in the amount of ' . htmlspecialchars($amount),
            'positions' => [Sentinel::getUser()->position_id],
            'office_id' => Sentinel::getUser()->office->id,
            'district_id' => Sentinel::getUser()->office->district_id,
            'province_id' => Sentinel::getUser()->office->province_id,
            'to_id' => null, // This can be set if there's a specific target user
            'created_date' => now()->toIso8601String()
        ]);
    }

    /**
     * Notify Branch Manager to approve a loan transaction.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @param float $amount The transaction amount
     * @return void
     */
    public static function notifyBmToApproveTransaction($loan, $client, $amount)
    {
        $manager = GeneralHelper::get_my_manager();
        $notifixService = app(NotifixService::class);

        if ($manager['bm']) {
            $notifixService->create($manager['bm'], [Sentinel::getUser()->office->id], [
                'id' => uniqid(),
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/transaction_approvals'),
                'type' => 'loan_transaction_approval',
                'message' => 'Pending transaction approval for client: ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' with amount ' . htmlspecialchars($amount),
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $manager['bm'],
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Risk Manager to review a high-value loan transaction.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @param float $amount The transaction amount
     * @return void
     */
    public static function notifyRiskToReviewLoan($loan, $client, $amount)
    {
        $manager = GeneralHelper::get_my_manager();
        $notifixService = app(NotifixService::class);

        if ($amount >= 3500 && $manager['rk']) {
            $notifixService->create($manager['rk'], [Sentinel::getUser()->office->id], [
                'id' => uniqid(),
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/transaction_approvals'),
                'type' => 'risk_review',
                'message' => 'Flagged: ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . "'s " . htmlspecialchars($amount) . " transaction requires approval review. -. " . htmlspecialchars(Sentinel::getUser()->office->name),
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $manager['rk'],
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Loan Officer that their loan has been approved.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyLoanOfficerLoanApproved($loan, $client)
    {
        $notifixService = app(NotifixService::class);

        if ($loan->loan_officer_id) {
            $notifixService->create($loan->loan_officer_id, [Sentinel::getUser()->office->id], [
                'id' => uniqid(),
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/' . $loan->id . '/show'),
                'type' => 'loan_approved',
                'message' => 'Loan application for ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' with amount ' . htmlspecialchars($loan->approved_amount) . ' has been approved.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Loan Officer that their loan has been declined.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyLoanOfficerLoanDeclined($loan, $client)
    {
        $notifixService = app(NotifixService::class);

        if ($loan->loan_officer_id) {
            $notifixService->create($loan->loan_officer_id, [Sentinel::getUser()->office->id], [
                'id' => uniqid(),
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/' . $loan->id . '/show'),
                'type' => 'loan_declined',
                'message' => 'Loan application for ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' has been declined. Reason: ' . htmlspecialchars($loan->declined_notes),
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Loan Officer that their loan has been disbursed.
     *
     * @param mixed $loan The loan model
     * @param mixed $client The client model
     * @param mixed $disbursed_by The user who disbursed the loan
     * @param string $payment_type_name The name of the payment type used
     * @return void
     */
    public static function notifyLoanOfficerLoanDisbursed($loan, $client, $disbursed_by, $payment_type_name)
    {
        $notifixService = app(NotifixService::class);

        if ($loan->loan_officer_id) {
            $notifixService->create($loan->loan_officer_id, [Sentinel::getUser()->office->id], [
                'id' => uniqid(),
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/' . $loan->id . '/show'),
                'type' => 'loan_disbursed',
                'message' => 'The loan for your client ' . $client->first_name . ' ' . $client->last_name . ' has been disbursed with amount ' . $loan->principal . ' by ' . $disbursed_by->first_name . ' ' . $disbursed_by->last_name . ' via ' . $payment_type_name . '.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Branch Manager for top-up approval.
     *
     * @param mixed $loan The loan model
     * @param mixed $topup The top-up model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyBmForTopUpApproval($loan, $topup, $client)
    {
        $manager = GeneralHelper::get_my_manager();
        $notifixService = app(NotifixService::class);

        if ($manager['bm']) {
            $notifixService->create($manager['bm'], [Sentinel::getUser()->office->id], [
                'id' => 'top_up_' . $topup->id,
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/top_up_approvals'),
                'type' => 'top_up_approval',
                'message' => 'Top-up request pending approval for client: ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' with amount ' . htmlspecialchars($topup->amount). ' by Loan Officer: ' . $topup->created_by,
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $manager['bm'],
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Risk Manager for top-up close to maturity.
     *
     * @param mixed $loan The loan model
     * @param mixed $topup The top-up model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyRkForTopUpCloseToMaturity($loan, $topup, $client)
    {
        $manager = GeneralHelper::get_my_manager();
        $notifixService = app(NotifixService::class);

        if ($manager['rk']) {
            $notifixService->create($manager['rk'], [Sentinel::getUser()->office->id], [
                'id' => 'top_up_risk_' . $topup->id,
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/top_up_approvals'),
                'type' => 'top_up_risk_review',
                'message' => 'Top-up close to maturity: ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' top-up of ' . htmlspecialchars($topup->amount) . ' is close to loan maturity.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $manager['rk'],
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Loan Officer that top-up has been approved.
     *
     * @param mixed $loan The loan model
     * @param mixed $topup The top-up model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyLoanOfficerTopUpApproved($loan, $topup, $client)
    {
        $notifixService = app(NotifixService::class);

        if ($loan->loan_officer_id) {
            $notifixService->create($loan->loan_officer_id, [Sentinel::getUser()->office->id], [
                'id' => 'top_up_approved_' . $topup->id,
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/' . $loan->id . '/show'),
                'type' => 'top_up_approved',
                'message' => 'Manager has approved top up for client ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' with amount ' . htmlspecialchars($topup->amount) . '.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Loan Officer that top-up has been declined.
     *
     * @param mixed $loan The loan model
     * @param mixed $topup The top-up model
     * @param mixed $client The client model
     * @return void
     */
    public static function notifyLoanOfficerTopUpDeclined($loan, $topup, $client)
    {
        $notifixService = app(NotifixService::class);

        if ($loan->loan_officer_id) {
            $notifixService->create($loan->loan_officer_id, [Sentinel::getUser()->office->id], [
                'id' => 'top_up_declined_' . $topup->id,
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => null,
                'link_to' => url('/loan/' . $loan->id . '/show'),
                'type' => 'top_up_declined',
                'message' => 'Declined top up for client ' . htmlspecialchars($client->first_name) . ' ' . htmlspecialchars($client->last_name) . ' with amount ' . htmlspecialchars($topup->amount) . ' by ' . Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name . '.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }

    /**
     * Notify Branch Manager for top-up approval by office.
     *
     * @param mixed $loan The loan model
     * @param mixed $topup The top-up model
     * @return void
     */
    public static function notifyBmForTopUpApprovalByOffice($loan, $topup)
    {

        $manager = GeneralHelper::get_my_manager();
        if ($manager['bm']) {
            $notifixService = app(NotifixService::class);
            $notifixService->create($manager['bm'], [$loan->office_id], [
                'id' => 'top_up_' . $topup->id,
                'loan_id' => $loan->id,
                'from_id' => Sentinel::getUser()->id,
                'link_from' => 'loan/' . $loan->id . '/show',
                'link_to' => 'loan/' . $loan->id . '/show',
                'type' => 'top_up_approval',
                'message' => 'New top-up request pending approval for loan ' . $loan->id,
                'created_date' => now()->toIso8601String(),
                'office_id' => $loan->office_id,
                'to_id' => $manager['bm']
            ]);
        }
    }

    /**
     * Send daily reminder to risk manager at 19:00.
     *
     * @return void
     */
    public static function notifyDailyReminderToRiskManager()
    {
        $currentTime = date('H:i');
        if ($currentTime == '19:00') {
            $manager = GeneralHelper::get_my_manager();
            if ($manager['rk']) {
                $notifixService = app(NotifixService::class);
                $notifixService->create($manager['rk'], [Sentinel::getUser()->office->id], [
                    'id' => uniqid(),
                    'from_id' => Sentinel::getUser()->id,
                    'link_from' => null,
                    'link_to' => url('/dashboard'),
                    'type' => 'daily_reminder',
                    'message' => 'Daily reminder at 7 PM for user: ' . Sentinel::getUser()->first_name . ' ' . Sentinel::getUser()->last_name. ' from office ' . Sentinel::getUser()->office->name.'.  made a loan repayment',
                    'to_id' => $manager['rk'],
                    'created_date' => now()->toIso8601String()
                ]);
            }
        }
    }
}