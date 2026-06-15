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
Route::post('/search/clients', [SearchEngineController::class, 'clientSearch']);
Route::get('/bank-deposits-with-records', [\App\Http\Controllers\BankDepositLogController::class, 'getDepositsWithRecords']);

Route::prefix('api/provincial-ledger')->group(function () {
    Route::post('/', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'store'])->name('api.provincial-ledger.store');
    Route::get('/', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'index'])->name('api.provincial-ledger.index');
    Route::get('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'show'])->name('api.provincial-ledger.show');
    Route::put('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'update'])->name('api.provincial-ledger.update');
    Route::delete('/{id}', [\App\Http\Controllers\ProvincialLedgerApiController::class, 'destroy'])->name('api.provincial-ledger.destroy');
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
