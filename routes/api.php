<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SalesOrderController;
use App\Http\Controllers\Api\AuthController;

use App\Http\Controllers\Api\WebhookController;

Route::post('/token', [AuthController::class, 'generateToken']);
Route::post('/webhooks/marketplace', [WebhookController::class, 'handleMarketplace']);

Route::middleware('api.auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);

    Route::get('/sales-orders', [SalesOrderController::class, 'index']);
    Route::get('/sales-orders/{id}', [SalesOrderController::class, 'show']);
});
