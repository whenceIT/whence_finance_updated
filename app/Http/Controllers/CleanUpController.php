<?php

namespace App\Http\Controllers;

use App\Services\CleanUpService;
use Illuminate\Http\Request;

class CleanUpController extends Controller
{
    public function run(Request $request)
    {
        $service = new CleanUpService();
        $service->closeLoansWithZeroBalance();

        return response()->json(['success' => true]);
    }
}
