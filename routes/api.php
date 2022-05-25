<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DelegateOrderController;
use App\Http\Controllers\DelegatePurchaseController;
use App\Http\Controllers\DeliveryMethodController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderProductsController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\StatisticsInsightController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SuggestedProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::post('/login', [AuthController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::get('/account', [AccountController::class, 'index']);
    Route::post('/account', [AccountController::class, 'update']);

    Route::put('/orders/assign-delegate', [OrderController::class, 'assignDelegate']);
    Route::put('/purchases/{purchase}/status', [PurchaseController::class, 'status']);
    
    Route::post('/suggested-products/{id}/accept', [SuggestedProductController::class, 'accept']);
    
    //Route::put('/purchases/{purchase}/update-return-invoice-id', [OrderController::class, 'assignDelegate']);
    //Route::get('/orders/{id}/products', [OrderController::clas])

    Route::apiResources([
        'stores' => StoreController::class,
        'locations' => LocationController::class,
        'suppliers' => SupplierController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'files' => FileController::class,
        'orders' => OrderController::class,
        'orders.tracking' => OrderTrackingController::class,
        'orders/{order}/products' =>  OrderProductsController::class,
        'purchases' => PurchaseController::class,
        'import-products' => ImportProductsController::class,
        'users' => UserController::class,
        'delivery-methods' => DeliveryMethodController::class,
        'suggested-products' => SuggestedProductController::class,
        'delegate/orders' => DelegateOrderController::class,
        'delegate/purchases' => DelegatePurchaseController::class
    ]);

    Route::get('/statistics/insights', [StatisticsInsightController::class, 'index']);

    Route::get('/verify-product', VerifyProduct::class);

    Route::post('/logout', function(Request $request) {
        return $request->user()->currentAccessToken()->delete();
    });
    
});

