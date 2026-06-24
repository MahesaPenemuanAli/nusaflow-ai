<?php

use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Api\AiServiceController;
use App\Http\Controllers\Api\AuthenticatedUserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Public API endpoints for the NusaFlow AI tourist application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

Route::get('/user', AuthenticatedUserController::class)->middleware('auth:sanctum');

Route::prefix('destinations')->group(function () {
    Route::get('/', [DestinationController::class, 'index']);
    Route::get('/{destination}', [DestinationController::class, 'show']);
    Route::get('/{destination}/crowd-status', [DestinationController::class, 'crowdStatus']);
    Route::get('/{destination}/recommendations', [DestinationController::class, 'recommendations']);
});

Route::prefix('ai-service')->group(function () {
    Route::get('/health', [AiServiceController::class, 'health']);
    Route::get('/model-info', [AiServiceController::class, 'modelInfo']);
});
