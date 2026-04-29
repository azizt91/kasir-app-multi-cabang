<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public Routes
Route::post('/login', [AuthController::class, 'login']);

// FCM Debug (temporary - remove after testing)
Route::get('/fcm-debug', [\App\Http\Controllers\Api\FcmDebugController::class, 'diagnose']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/user/fcm-token', [AuthController::class, 'updateFcmToken']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [\App\Http\Controllers\Api\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/mark-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);
    Route::delete('/notifications/clear', [\App\Http\Controllers\Api\NotificationController::class, 'clearAll']);

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Api\DashboardController::class, 'index']);

    // Master Data & Sync
    Route::get('/products/sync', [\App\Http\Controllers\Api\ProductController::class, 'sync']);
    Route::post('/products/{id}/adjust', [\App\Http\Controllers\Api\ProductController::class, 'adjustStock']);

    // POS Transaction (Sync Upstream)
    Route::post('/pos/transaction', [\App\Http\Controllers\Api\PosController::class, 'store']);

    // Transaction History
    Route::get('/transactions', [\App\Http\Controllers\Api\TransactionController::class, 'index']);
    Route::get('/receivables', [\App\Http\Controllers\Api\TransactionController::class, 'receivables']); // New
    Route::get('/transactions/{id}', [\App\Http\Controllers\Api\TransactionController::class, 'show']);
    Route::patch('/transactions/{id}/mark-as-paid', [\App\Http\Controllers\Api\TransactionController::class, 'markAsPaid']); // New
    Route::delete('/transactions/{id}', [\App\Http\Controllers\Api\TransactionController::class, 'destroy']);

    // Expenses
    Route::get('/expenses', [\App\Http\Controllers\Api\ExpenseController::class, 'index']);
    Route::post('/expenses', [\App\Http\Controllers\Api\ExpenseController::class, 'store']);
    Route::put('/expenses/{id}', [\App\Http\Controllers\Api\ExpenseController::class, 'update']);
    Route::delete('/expenses/{id}', [\App\Http\Controllers\Api\ExpenseController::class, 'destroy']);

    // Customers
    Route::get('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'index']);
    Route::post('/customers', [\App\Http\Controllers\Api\CustomerController::class, 'store']);
    Route::put('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [\App\Http\Controllers\Api\CustomerController::class, 'destroy']);
});
