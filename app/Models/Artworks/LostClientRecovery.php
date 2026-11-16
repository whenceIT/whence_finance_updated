<?php
namespace App\Models\Artworks;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LostClientRecovery extends Model
{
    protected $table = 'creative_lost_client_recoveries';
    protected $fillable = ['recovery_reference','branch_id','client_id','client_name','nrc_number','contact_number','recovery_date','responsible_staff_id','loan_id','repayment_status','profit_share','amount_paid','notes'];
    protected $dates = ['recovery_date','created_at','updated_at'];

    public static function store($data): self
    {
        // If a Loan model is passed, extract relevant fields
        if ($data instanceof \App\Models\Loan) {
            $client = $data->client;
            if (!$client || $client->lost_recovery_approval !== 'approved') {
                return null;
            }
            $data = [
                'branch_id' => $data->office_id,
                'client_id' => $data->client_id,
                'client_name' => $client ? ($client->full_name ?: ($client->first_name . ' ' . $client->last_name)) : null,
                'nrc_number' => $client ? $client->nrc_number : null,
                'contact_number' => $client ? $client->phone : null,
                'recovery_date' => Carbon::now(),
                'responsible_staff_id' => Sentinel::getUser() ? Sentinel::getUser()->id : null,
                'loan_id' => $data->id,
                'notes' => null,
            ];
        }

        $ref = 'LCR-' . Carbon::now()->format('Y') . '-' . str_pad(static::count() + 1, 6, '0', STR_PAD_LEFT);

        return static::create([
            'recovery_reference' => $ref,
            'branch_id' => $data['branch_id'] ?? null,
            'client_id' => $data['client_id'] ?? null,
            'client_name' => $data['client_name'] ?? null,
            'nrc_number' => $data['nrc_number'] ?? null,
            'contact_number' => $data['contact_number'] ?? null,
            'recovery_date' => $data['recovery_date'] ?? Carbon::now(),
            'responsible_staff_id' => $data['responsible_staff_id'] ?? null,
            'loan_id' => $data['loan_id'] ?? null,
            'repayment_status' => 'Unpaid',
            'profit_share' => 0,
            'amount_paid' => 0,
            'notes' => $data['notes'] ?? null,
        ]);
    }
}
