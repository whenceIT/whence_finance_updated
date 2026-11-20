<?php

namespace App\Http\Controllers;

use App\Services\CleanUpService;
use Illuminate\Http\Request;

class CleanUpController extends Controller
{
    public function run(Request $request)
    {
        $service = new CleanUpService();
        $result = $service->closeLoansWithZeroBalance();

        return response()->json($result, $result['success'] ? 200 : 401);
    }
}
