<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoIntegrationController;

Route::prefix('zoho-integration')->name('zoho.integration.')->group(function () {
    Route::get('/', [ZohoIntegrationController::class, 'index'])->name('index');
    Route::get('/callback', [ZohoIntegrationController::class, 'callback'])->name('callback');
    Route::get('/status', [ZohoIntegrationController::class, 'status'])->name('status');
    Route::post('/refresh', [ZohoIntegrationController::class, 'refreshTokens'])->name('refresh');
});

Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');