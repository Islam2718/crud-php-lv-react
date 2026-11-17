<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\UserauthController;
use App\Http\Controllers\API\UserController;

use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\PaymentController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [UserauthController::class, 'userLogin']);
Route::post('/register', [UserauthController::class, 'userRegister']);
Route::post('/send-verification-code', [UserauthController::class, 'sendVarificationCode']);
Route::post('/verify-code', [UserauthController::class, 'verifyCode']);
Route::post('/change-password', [UserauthController::class, 'changePassword']);

// Route::middleware('auth:api')->group(function () {
    Route::get('user-info', [UserauthController::class, 'getUserDetails']);
    Route::post('logout', [UserauthController::class, 'userLogout']);
    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'show', 'destroy']);    
    Route::resource('product', ProductController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('stock', StockController::class)->only(['index', 'store', 'update', 'show', 'destroy']);    
    Route::resource('order', OrderController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('order-item', OrderItemController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('payment', PaymentController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
// });
