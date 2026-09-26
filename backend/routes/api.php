<?php

use App\Http\Controllers\Api\V1\WhatsAppWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — /api/v1
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    Route::get('/health', function () {
        return response()->json([
            'status' => 'ok',
            'app' => 'WhatsCRM API v1',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Official Meta WhatsApp Webhook endpoints
    Route::get('/webhook/whatsapp', [WhatsAppWebhookController::class, 'verify'])->name('api.webhook.whatsapp.verify');
    Route::post('/webhook/whatsapp', [WhatsAppWebhookController::class, 'handle'])->name('api.webhook.whatsapp.handle');

    // Internal Baileys microservice webhook endpoint
    Route::post('/webhook/baileys', [\App\Http\Controllers\Api\V1\BaileysWebhookController::class, 'handle'])->name('api.webhook.baileys');

    // Protected Developer REST API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
    });
});
