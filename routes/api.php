<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ZohoIntegrationController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:web');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:web');

Route::get('/items', [ItemsController::class, 'index'])->middleware('auth:web');
Route::get('/items/{id}', [ItemsController::class, 'getItem'])->middleware('auth:web');

Route::get('/customers', [CustomerController::class, 'index'])->middleware('auth:web');
Route::get('/customers/statistics', [CustomerController::class, 'statistics'])->middleware('auth:web');
Route::get('/customers/{id}', [CustomerController::class, 'show'])->middleware('auth:web');
Route::get('/customers/contact/{contactId}', [CustomerController::class, 'showByContactId'])->middleware('auth:web');
Route::post('/customers', [CustomerController::class, 'store'])->middleware('auth:web');
Route::put('/customers/{id}', [CustomerController::class, 'update'])->middleware('auth:web');
Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->middleware('auth:web');

Route::get('/sales-orders', [SalesOrderController::class, 'index'])->middleware('auth:web');
Route::get('/sales-orders/statistics', [SalesOrderController::class, 'statistics'])->middleware('auth:web');
Route::get('/sales-orders/{id}', [SalesOrderController::class, 'show'])->middleware('auth:web');
Route::get('/sales-orders/order/{salesorderId}', [SalesOrderController::class, 'showBySalesorderId'])->middleware('auth:web');
Route::post('/sales-orders', [SalesOrderController::class, 'store'])->middleware('auth:web');
Route::post('/sales-orders/with-purchase-orders', [SalesOrderController::class, 'storeWithPurchaseOrders'])->middleware('auth:web');
Route::put('/sales-orders/{id}', [SalesOrderController::class, 'update'])->middleware('auth:web');
Route::delete('/sales-orders/{id}', [SalesOrderController::class, 'destroy'])->middleware('auth:web');

Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->middleware('auth:web');
Route::get('/purchase-orders/statistics', [PurchaseOrderController::class, 'statistics'])->middleware('auth:web');
Route::get('/purchase-orders/{id}', [PurchaseOrderController::class, 'show'])->middleware('auth:web');
Route::get('/purchase-orders/order/{purchaseorderId}', [PurchaseOrderController::class, 'showByPurchaseorderId'])->middleware('auth:web');
Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->middleware('auth:web');
Route::put('/purchase-orders/{id}', [PurchaseOrderController::class, 'update'])->middleware('auth:web');
Route::delete('/purchase-orders/{id}', [PurchaseOrderController::class, 'destroy'])->middleware('auth:web');

Route::get('/dashboard/statistics', [DashboardController::class, 'getStatistics'])->middleware('auth:web');

Route::get('/zoho/token-status', [ZohoIntegrationController::class, 'getTokenStatus']);

