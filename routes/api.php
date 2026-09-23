<?php

use App\Http\Controllers\Sirani\RfidController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Endpoint Scan Smart Gate RFID & Barcode
    Route::post('/rfid-scan', [RfidController::class, 'scan'])->middleware('throttle:300,1');
    Route::get('/kiosk-monitor-feed', [RfidController::class, 'monitorFeed']);
});

// Endpoint Webhook Auto-Deploy Server SIRANI
Route::match(['get', 'post'], '/deploy-webhook', [\App\Http\Controllers\Api\DeployController::class, 'handle']);

// Endpoint Push Notification Portal Orang Tua & Siswa
Route::get('/push-vapid-key', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'getPublicKey']);
Route::post('/push-subscribe', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'subscribe']);
Route::post('/push-unsubscribe', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'unsubscribe']);
Route::post('/push-test-background', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'testBackground']);

