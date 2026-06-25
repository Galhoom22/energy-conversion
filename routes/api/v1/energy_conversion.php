<?php

declare(strict_types=1);

use App\Http\Controllers\Api\V1\RegisterMeterController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/energy_conversion')->name('api.v1.energy_conversion.')->group(function () {
    Route::get('/ping', fn () => response()->json(['status' => 'ok']))->name('ping');
    Route::post('/register-meter', RegisterMeterController::class)->middleware(['auth:sanctum', 'idempotent'])->name('register-meter');
});
