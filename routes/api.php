<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DistrictController;
use App\Http\Controllers\DistrictRegionalController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\SearchEngineController;
use App\Http\Controllers\SmsController;

Route::prefix('districts')->group(function () {
    Route::get('/', [DistrictController::class, 'index']);
    Route::post('/', [DistrictController::class, 'store']);
    Route::get('/{id}', [DistrictController::class, 'show']);
    Route::put('/{id}', [DistrictController::class, 'update']);
    Route::delete('/{id}', [DistrictController::class, 'destroy']);
    Route::get('/stats', [DistrictController::class, 'getDistrictsWithStats']);
});

Route::prefix('district-regionals')->group(function () {
    Route::get('/', [DistrictRegionalController::class, 'index']);
    Route::post('/', [DistrictRegionalController::class, 'store']);
    Route::get('/{id}', [DistrictRegionalController::class, 'show']);
    Route::put('/{id}', [DistrictRegionalController::class, 'update']);
    Route::delete('/{id}', [DistrictRegionalController::class, 'destroy']);
    Route::get('/stats', [DistrictRegionalController::class, 'getDistrictRegionalsWithStats']);
});

Route::post('/send-sms', [SmsController::class, 'sendSms']);
Route::post('/send-bulk-sms', [SmsController::class, 'sendBulkSms']);
Route::get('/get-office-users/{officeId}', [SmsController::class, 'getOfficeUsers']);
Route::post('/send-officer-sms', [SmsController::class, 'sendToOfficersClients']);
Route::post('/search/clients', [SearchEngineController::class, 'clientSearch']);
Route::get('/bank-deposits-with-records', [\App\Http\Controllers\BankDepositLogController::class, 'getDepositsWithRecords']);
Route::get('/ledger-summary', [\App\Http\Controllers\BankDepositLogController::class, 'getLedgerSummary'])->name('api.manual.ledger');

Route::prefix('api/provincial-ledger')->group(function () {
    Route::post('/', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'store'])->name('api.provincial-ledger.store');
    Route::get('/', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'index'])->name('api.provincial-ledger.index');
    Route::get('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'show'])->name('api.provincial-ledger.show');
    Route::put('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'update'])->name('api.provincial-ledger.update');
    Route::delete('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'destroy'])->name('api.provincial-ledger.destroy');
});

Route::get('/loan-vetted-by/{loanId}', function ($loanId) {
    $loan = \App\Models\Loan::find($loanId);
    if (!$loan || !$loan->vetted_by) {
        return response()->json([
            'success' => false,
            'data' => null,
            'veted_by' => $loan->vetted_by,
            'message'=> 'first'
        ]);
    }
    $user = \App\Models\User::find($loan->vetted_by);
    if (!$user) {
        return response()->json([
            'success' => false,
            'data' => null,
            'veted_by' => $loan->vetted_by,
            'message'=> 'second'
        ]);
    }
    return response()->json([
        'success' => true,
        'data' => [
            'id'         => $user->id,
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'name'       => $user->first_name . ' ' . $user->last_name,
        ],
    ]);
});

Route::get('/provinces', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Province::orderBy('name')->get()
    ]);
});

Route::get('/offices', function () {
    return response()->json([
        'success' => true,
        'data' => \App\Models\Office::with('province')->orderBy('name')->get()
    ]);
});

Route::get('/search-clients', function () {
    $q = trim(request('q', ''));
    $like = "%{$q}%";
    $clients = \App\Models\Client::whereNull('deleted_at')
        ->where(function ($query) use ($like) {
            $query->where('first_name', 'LIKE', $like)
                ->orWhere('last_name', 'LIKE', $like)
                ->orWhere('full_name', 'LIKE', $like)
                ->orWhere('nrc_number', 'LIKE', $like);
        })
        ->orderBy('full_name')
        ->take(30)
        ->get();
    return response()->json([
        'success' => true,
        'data' => $clients->map(function ($c) {
            $name = $c->full_name ?: ($c->first_name . ' ' . $c->last_name ?: '');
            return [
                'id' => $c->id,
                'label' => $name . ' (' . $c->nrc_number . ')',
            ];
        }),
    ]);
});

Route::get('/search-users', function () {
    $q = trim(request('q', ''));
    $like = "%{$q}%";
    $users = \App\Models\User::whereNull('deleted_at')->where('blocked', 0)
        ->where(function ($query) use ($like) {
            $query->where('first_name', 'LIKE', $like)
                ->orWhere('last_name', 'LIKE', $like)
                ->orWhere('email', 'LIKE', $like);
        })
        ->orderBy('first_name')
        ->take(30)
        ->get();
    return response()->json([
        'success' => true,
        'data' => $users->map(function ($u) {
            return [
                'id' => $u->id,
                'label' => $u->first_name . ' ' . $u->last_name,
            ];
        }),
    ]);
});

Route::get('/loans/{loanId}/kyc-compliance-status', 'MotorVehicleLoanLifecycleController@checkKycAndComplianceStatus');

Route::prefix('mvl')->group(function () {
    Route::get('/loans', [\App\Http\Controllers\MVLAPIController::class, 'getLoans']);
    Route::get('/vehicles', [\App\Http\Controllers\MVLAPIController::class, 'getVehicles']);
    Route::get('/portfolio', [\App\Http\Controllers\MVLAPIController::class, 'getPortfolio']);
    Route::get('/collections', [\App\Http\Controllers\MVLAPIController::class, 'getCollections']);
    Route::get('/defaulted', [\App\Http\Controllers\MVLAPIController::class, 'getDefaulted']);
});

Route::post('/schedule/autolock', [\App\Http\Controllers\API\ScheduleController::class, 'runAutolock']);

// Fund movements for blocked offices
Route::prefix('fund-movements')->group(function () {
    Route::get('/blocked',              [\App\Http\Controllers\API\FundMovementsController::class, 'index']);
    Route::get('/blocked/{blockageId}', [\App\Http\Controllers\API\FundMovementsController::class, 'byBlockage']);
});

// Payroll Loan Dashboard API
Route::prefix('payroll-loans')->group(function () {
    Route::get('/consultants',       [\App\Http\Controllers\PayrollloanController::class, 'apiConsultants']);
    Route::get('/drilldown',         [\App\Http\Controllers\PayrollloanController::class, 'apiDrilldown']);
    Route::get('/consultant-loans',  [\App\Http\Controllers\PayrollloanController::class, 'apiConsultantLoans']);
});
