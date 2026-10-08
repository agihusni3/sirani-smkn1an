<?php

use App\Http\Controllers\Sirani\RfidController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Endpoint Scan Smart Gate RFID & Barcode (Dilindungi rate limiter)
    Route::post('/rfid-scan', [RfidController::class, 'scan'])->middleware('throttle:120,1');
    Route::get('/kiosk-monitor-feed', [RfidController::class, 'monitorFeed'])->middleware('throttle:60,1');
});

// Endpoint Webhook Auto-Deploy Server SIRANI (Rate limit 15 req/menit untuk mencegah brute-force token)
Route::match(['get', 'post'], '/deploy-webhook', [\App\Http\Controllers\Api\DeployController::class, 'handle'])
    ->middleware('throttle:10,1');

// Endpoint Push Notification Portal Orang Tua & Siswa (Dilindungi rate limiter)
Route::get('/push-vapid-key', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'getPublicKey'])->middleware('throttle:60,1');
Route::post('/push-subscribe', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'subscribe'])->middleware('throttle:30,1');
Route::post('/push-unsubscribe', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'unsubscribe'])->middleware('throttle:30,1');
Route::post('/push-test-background', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'testBackground'])->middleware('throttle:6,1');
Route::get('/push-subscribers-count', [\App\Http\Controllers\Api\PushSubscriptionController::class, 'getSubscribersCount'])->middleware('throttle:30,1');
Route::get('/portal-notifikasi-terbaru', [\App\Http\Controllers\Core\PortalOrtuController::class, 'getRecentNotifications'])->middleware('throttle:60,1');
