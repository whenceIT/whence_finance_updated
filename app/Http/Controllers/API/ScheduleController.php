<?php

namespace App\Http\Controllers\API;

use App\Helpers\BlockerHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function runAutolock(Request $request)
    {
        $result = BlockerHelper::autolock();

        return response()->json([
            'success' => $result['status'],
            'data' => $result,
        ]);
    }
}
