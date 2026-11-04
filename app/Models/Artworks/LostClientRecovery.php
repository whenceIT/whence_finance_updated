<?php
namespace App\Models\Artworks;
use Illuminate\Database\Eloquent\Model;
class LostClientRecovery extends Model
{
    protected $table = 'creative_lost_client_recoveries';
    protected $fillable = ['recovery_reference','branch_id','client_name','nrc_number','contact_number','recovery_date','responsible_staff_id','loan_id','repayment_status','profit_share','amount_paid','notes'];
    protected $dates = ['recovery_date','created_at','updated_at'];
}
