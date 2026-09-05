<?php

use App\Http\Controllers\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:60,1')->group(function () {
    Route::get('/whatsapp/webhook', [WhatsAppWebhookController::class, 'verify']);
    Route::post('/whatsapp/webhook', [WhatsAppWebhookController::class, 'handle']);
});

Route::get('/deploy/trigger', \App\Http\Controllers\DeployController::class)->name('deploy.trigger');

