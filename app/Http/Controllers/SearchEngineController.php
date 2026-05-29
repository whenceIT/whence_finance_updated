<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Client;
use DB;

class SearchEngineController extends Controller
{


    public function clientSearch(Request $request)
    {
        $validated = $request->validate([
            'mobile' => 'nullable|string|max:255',
        ]);

        $mobile = $request->mobile;

        $clients = DB::select("
            SELECT 
                c.id,
                c.first_name,
                c.last_name,
                c.email,
                c.mobile,
                c.phone,
                o.name as office_name,
                u.first_name as staff_first_name,
                u.last_name as staff_last_name,
                u.phone as staff_phone,
                l.account_number,
                l.principal,
                l.disbursement_date
            FROM clients c
            LEFT JOIN offices o ON c.office_id = o.id
            LEFT JOIN loans l ON c.id = l.client_id AND l.status = 'disbursed'
            LEFT JOIN users u ON l.loan_officer_id = u.id
            WHERE c.mobile = ?
        ", [$mobile]);

        $data = array_map(function ($client) {
            return [
                'id' => $client->id,
                'first_name' => $client->first_name,
                'last_name' => $client->last_name,
                'email' => $client->email,
                'mobile' => $client->mobile,
                'phone' => $client->phone,
                'office' => $client->office_name,
                'staff' => $client->staff_first_name && $client->staff_last_name 
                    ? $client->staff_first_name . ' ' . $client->staff_last_name 
                    : null,
                'staff_phone' => $client->staff_phone,
                'loans_count' => $client->account_number ? 1 : 0,
                'account_number' => $client->account_number,
                'principal' => $client->principal,
                'disbursement_date' => $client->disbursement_date,
            ];
        }, $clients);

        return response()->json([
            'data' => $data,
            'total' => count($data),
            'current_page' => 1,
            'last_page' => 1,
            'prev_page_url' => null,
            'next_page_url' => null,
        ]);
    }
}