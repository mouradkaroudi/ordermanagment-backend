<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DelegateOrdersController;
use App\Http\Controllers\DelegatePurchaseOrdersController;
use App\Http\Controllers\DeliveryMethodController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\StatisticsInsightController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\SuggestedProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerifyProduct;
use App\Models\PurchaseOrder;
use App\Models\Usermeta;
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

Route::post('/auth', [AuthController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResources([
        'stores' => StoreController::class,
        'locations' => LocationController::class,
        'suppliers' => SupplierController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'files' => FileController::class,
        'orders' => OrderController::class,
        'purchases' => PurchaseController::class,
        'import-products' => ImportProductsController::class,
        'users' => UserController::class,
        'delivery-methods' => DeliveryMethodController::class,
        'suggested-products' => SuggestedProductController::class,
        'delegate/orders' => DelegatePurchaseOrdersController::class,
        //'purchases/{id}/orders/1' => PurchaseOrderController::class
    ]);

    Route::get('/statistics/insights', [StatisticsInsightController::class, 'index']);
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        $user_abilities = Usermeta::where(['user_id' => $user->id, 'key' => 'abilities'])->first('value');
        $user['abilities'] = $user->currentAccessToken()->abilities;
        return $user;
    });

    Route::get('/verify-product', VerifyProduct::class);

    Route::post('/logout', function(Request $request) {
        return $request->user()->currentAccessToken()->delete();
    });
    
});

