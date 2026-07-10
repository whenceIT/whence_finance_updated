<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LockService;

class DeadlineController extends Controller
{

    public function triggerLock(Request $request)
    {
        try {
            $service = new LockService();
            $service->lock_deposits();

            return response()->json(['success' => true, 'message' => 'Lock fired']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
