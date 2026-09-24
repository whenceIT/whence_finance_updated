<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AuditorService;
use Illuminate\Http\JsonResponse;

class FundMovementsController extends Controller
{
    protected AuditorService $auditorService;

    public function __construct(AuditorService $auditorService)
    {
        $this->auditorService = $auditorService;
    }

    /**
     * Return all fund movements for every currently-blocked office.
     *
     * GET /api/fund-movements/blocked
     */
    public function index(): JsonResponse
    {
        $movements = $this->auditorService->getFundMovementsByBlockedOffice();

        return response()->json([
            'success' => true,
            'data'    => $movements,
        ]);
    }

    /**
     * Return fund movements scoped to the office tied to a specific blockage.
     *
     * GET /api/fund-movements/blocked/{blockageId}
     */
    public function byBlockage(int $blockageId): JsonResponse
    {
        $blockage = \App\Models\Blockage::with('office')->find($blockageId);

        if (! $blockage) {
            return response()->json([
                'success' => false,
                'message' => 'Blockage not found.',
            ], 404);
        }

        $movements = $this->auditorService->getFundMovementsByBlockedOffice($blockageId);

        return response()->json([
            'success'  => true,
            'blockage' => [
                'id'          => $blockage->id,
                'office_id'   => $blockage->office_id,
                'office_name' => $blockage->office?->name ?? 'N/A',
                'reason'      => $blockage->reason,
                'blocked_at'  => $blockage->created_at?->toDateTimeString(),
            ],
            'data'     => $movements,
        ]);
    }
}
