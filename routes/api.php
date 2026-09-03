<?php

use App\Http\Controllers\RfidController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Endpoint Scan Smart Gate RFID & Barcode
    Route::post('/rfid-scan', [RfidController::class, 'scan'])->middleware('throttle:300,1');
});
