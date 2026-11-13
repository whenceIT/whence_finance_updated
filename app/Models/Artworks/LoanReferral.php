<?php
namespace App\Models\Artworks;
use Illuminate\Database\Eloquent\Model;
class LoanReferral extends Model
{
    protected $table = 'creative_loan_referrals';
    protected $fillable = ['referral_reference','branch_id','client_name','nrc_number','contact_number','referring_department','referral_date','referring_officer_id','loan_id','loan_status','repayment_status','profit_share','amount_paid','notes'];
    protected $dates = ['referral_date','created_at','updated_at'];
}
