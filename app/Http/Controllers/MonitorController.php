<?php

namespace App\Http\Controllers;

use App\Services\AlertService;
use Illuminate\Http\JsonResponse;

class MonitorController extends Controller
{
    /**
     * Trigger AlertService::runAll() and return the summary.
     * Only succeeds during monitored windows 06:00–10:30 or 13:00–14:30.
     * Responds with { success, created, message, serverHour, inWindow }.
     */
    public function runAllAlerts(): JsonResponse
    {
        $now   = now();
        $hour  = (int) $now->format('H');
        $mins  = (int) $now->format('i');
        $stamp = $hour * 100 + $mins;          // 0915, 1422, etc.

        $windowActive =
            ($stamp >= 600  && $stamp <= 1030) ||
            ($stamp >= 1300 && $stamp <= 1430);

        if (! $windowActive) {
            return response()->json([
                'success'   => false,
                'message'   => 'Outside supervised monitoring window.',
                'created'   => 0,
                'serverHour'=> sprintf('%02d:%02d', $hour, $mins),
                'inWindow'  => false,
                'timestamp' => $now->toIso8601String(),
            ]);
        }

        $created = AlertService::runAll();

        return response()->json([
            'success'   => true,
            'message'   => 'AlertService::runAll() completed.',
            'created'   => $created,
            'serverHour'=> sprintf('%02d:%02d', $hour, $mins),
            'inWindow'  => true,
            'timestamp' => $now->toIso8601String(),
        ]);
    }
}
