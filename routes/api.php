<?php

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Magento Monitor API Routes
Route::get('/validate', [ApiController::class, 'validateApiKey']);
Route::post('/stores', [ApiController::class, 'registerStore']);
Route::post('/stores/{storeId}/modules', [ApiController::class, 'updateModules']);
Route::post('/stores/{storeId}/server-info', [ApiController::class, 'updateServerInfo']);
Route::post('/stores/{storeId}/stats', [ApiController::class, 'updateStoreStats']);
