<?php

use App\Http\Controllers\Api\FaceScanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Endpoint Publik Pindai Smart Gate & Unduh Descriptors (Dilindungi Rate Limiting)
    Route::get('/face-descriptors', [FaceScanController::class, 'getDescriptors'])->middleware('throttle:300,1');
    Route::post('/face-scan', [FaceScanController::class, 'handleFaceScan'])->middleware('throttle:300,1');
    Route::post('/scan', [FaceScanController::class, 'handleFaceScan'])->middleware('throttle:300,1');
    Route::get('/face-service-status', [FaceScanController::class, 'serviceStatus'])->middleware('throttle:300,1');
});
