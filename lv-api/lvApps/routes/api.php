<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\BackupDatabaseController;

use App\Http\Controllers\API\UserauthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\FeatureController;

use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\StockController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\OrderItemController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\CustomerController;
// barcode 
use App\Http\Controllers\API\BarcodeController;
// report 
use App\Http\Controllers\API\ReportController;
// files 
use App\Http\Controllers\API\UploadController;
use App\Http\Controllers\API\IssueController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [UserauthController::class, 'userLogin']);
Route::post('/register', [UserauthController::class, 'userRegister']);
Route::post('/customer-create', [CustomerController::class, 'customerAdd']);
Route::post('/send-verification-code', [UserauthController::class, 'sendVarificationCode']);
Route::post('/verify-code', [UserauthController::class, 'verifyCode']);
Route::post('/change-password', [UserauthController::class, 'changePassword']);

// Route::middleware('auth:api')->group(function () {
    Route::get('user-info', [UserauthController::class, 'getUserDetails']);
    Route::post('logout', [UserauthController::class, 'userLogout']);

    Route::resource('users', UserController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::get('user-by-phone/{phone}', [UserController::class, 'getUserByPhone']);

    Route::resource('feature', FeatureController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('product', ProductController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('stock', StockController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('store', StoreController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('order', OrderController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('order-item', OrderItemController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('payment', PaymentController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::resource('customer', CustomerController::class)->only(['index', 'store', 'update', 'show', 'destroy']);   

    // barcode 
    Route::resource('barcode', BarcodeController::class)->only(['index', 'store', 'update', 'show', 'destroy']);
    Route::get('barcode-regenerate/{id}', [BarcodeController::class, 'reGenerate']);
    Route::get('barcode-by-stock-id/{stock_id}', [BarcodeController::class, 'printBarcodeByStockId']);
    Route::get('barcode-by-code/{barcode}', [BarcodeController::class, 'getByBarcode']);
    // status 
    Route::get('barcode-status-update/{id}/{status}/{customer}', [BarcodeController::class, 'barcodeStatusUpdate']);
    Route::get('barcode-status-update-warranty/{id}', [BarcodeController::class, 'barcodeStatusUpdateWarranty']);

    // issue 
    Route::resource('warranty-issues', IssueController::class)->only(['index', 'store', 'update', 'show', 'destroy']);

    // report 
    Route::get('report-due', [ReportController::class, 'due']);
    Route::get('report-dashboard', [ReportController::class, 'dashboard']);
    //backup 
    Route::get('backup', [BackupDatabaseController::class, 'downloadAllTables']);
    // Route::resource('upload', UploadController::class)->only(['index']);
    Route::get('files', 'App\Http\Controllers\API\UploadController@getAllFile');
    Route::post('file-upload', 'App\Http\Controllers\API\UploadController@uploadFile');
    Route::get('file-remove/{filename}', 'App\Http\Controllers\API\UploadController@deleteFile');
// });
