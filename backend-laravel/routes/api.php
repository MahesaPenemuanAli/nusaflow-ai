<?php

use App\Http\Controllers\Api\DestinationController;
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

Route::prefix('destinations')->group(function () {
    Route::get('/', [DestinationController::class, 'index']);
    Route::get('/{destination}', [DestinationController::class, 'show']);
    Route::get('/{destination}/crowd-status', [DestinationController::class, 'crowdStatus']);
    Route::get('/{destination}/recommendations', [DestinationController::class, 'recommendations']);
});
