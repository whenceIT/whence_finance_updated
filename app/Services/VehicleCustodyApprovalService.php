<?php

namespace App\Services;

use App\Models\VehicleCustody;
use Illuminate\Support\Facades\Auth;

class VehicleCustodyApprovalService
{
    public function getPendingApprovalsForUser($userId)
    {
        return VehicleCustody::with('vehicle')
            ->where('received_by', $userId)
            ->where('custody_approved', 0)
            ->orderBy('received_at', 'desc')
            ->get()
            ->map(function ($custody) {
                return [
                    'id' => $custody->id,
                    'vehicle_registration' => optional($custody->vehicle)->registration_number,
                    'vehicle_make' => optional($custody->vehicle)->make,
                    'vehicle_model' => optional($custody->vehicle)->model,
                    'received_at' => $custody->received_at ? $custody->received_at->format('Y-m-d H:i') : null,
                    'garage_name' => $custody->garage_name,
                    'garage_location' => $custody->garage_location,
                    'garage_contact_person' => $custody->garage_contact_person,
                    'garage_contact_phone' => $custody->garage_contact_phone,
                ];
            });
    }
}
