<?php
namespace App\Models\Artworks;
use Illuminate\Database\Eloquent\Model;
class LoanReferral extends Model
{
    protected $table = 'creative_loan_referrals';
    protected $fillable = ['referral_reference','branch_id','client_name','fn_name','lname','email','dob','gender','client_type','marital_status','address','working_place','working_position','salary','employer','business_name','nrc_number','contact_number','referring_department','referral_date','referring_officer_id','loan_id','approval_status','repayment_status','profit_share','amount_paid','notes'];
    protected $attributes = ['approval_status' => 'pending'];
    protected $dates = ['dob','referral_date','created_at','updated_at'];
}
