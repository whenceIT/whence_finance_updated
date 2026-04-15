<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\GeneralHelper;
use App\Services\NotifixService;
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
            'message' => 'New loan pending approval for ' . $client->first_name . ' ' . $client->last_name . ' with amount ' . $amount,
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
                'message' => 'Pending transaction approval for client: ' . $client->first_name . ' ' . $client->last_name . ' with amount ' . $amount,
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
                'type' => 'loan_transaction_approval',
                'message' => 'Possible risk identified for ' . $client->first_name . ' ' . $client->last_name . ' with amount ' . $amount . ' Please review the transaction for approval. ' . Sentinel::getUser()->office->name,
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
                'message' => 'Your loan application for ' . $client->first_name . ' ' . $client->last_name . ' with amount ' . $loan->approved_amount . ' has been approved.',
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
                'message' => 'Your loan application for ' . $client->first_name . ' ' . $client->last_name . ' has been declined. Reason: ' . $loan->declined_notes,
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
     * @return void
     */
    public static function notifyLoanOfficerLoanDisbursed($loan, $client)
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
                'message' => 'The loan for your client ' . $client->first_name . ' ' . $client->last_name . ' has been disbursed with amount ' . $loan->principal . '.',
                'positions' => [Sentinel::getUser()->position_id],
                'office_id' => Sentinel::getUser()->office->id,
                'district_id' => Sentinel::getUser()->office->district_id,
                'province_id' => Sentinel::getUser()->office->province_id,
                'to_id' => $loan->loan_officer_id,
                'created_date' => now()->toIso8601String()
            ]);
        }
    }
}