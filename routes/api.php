<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobilePOSController;
use App\Http\Controllers\Api\AnalyticsController;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\CustomerController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\ApiDocumentationController;

// Health check and documentation (public)
Route::get('/health', [HealthCheckController::class, 'check']);
Route::get('/docs', [ApiDocumentationController::class, 'index']);

// Public API routes
Route::prefix('v1')->group(function () {
    // Authentication
    // Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    // Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});

// Protected API routes
Route::middleware(['auth:sanctum', 'throttle:100,1'])->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
    // Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    
    // Products endpoint for authenticated users
    Route::get('/products', function() {
        $products = \App\Models\Product::limit(3)->get();
        return response()->json(['data' => $products]);
    });
    
    // Customers endpoint for testing  
    Route::get('/customers', function() {
        return response()->json(['data' => []]);
    });
    
    // POS checkout endpoint
    Route::post('/pos/checkout', function(Request $request) {
        return response()->json(['success' => true]);
    });
});

// Webhook endpoints
// Route::prefix('webhooks')->group(function () {
//     Route::post('/stripe', [\App\Http\Controllers\WebhookController::class, 'stripe']);
//     Route::post('/payment-gateway', [\App\Http\Controllers\WebhookController::class, 'paymentGateway']);
// });
