<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ImportProductsController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {

    $user = $request->user();

    $user_abilities = Usermeta::where(['user_id' => $user->id, 'key' => 'abilities'])->first('value');


    return [$user, 'abilities' => $user->currentAccessToken()->abilities];
});

Route::post('/auth', [AuthController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResources([
        'locations' => LocationController::class,
        'suppliers' => SupplierController::class,
        'categories' => CategoryController::class,
        'products' => ProductController::class,
        'files' => FileController::class,
        'orders' => OrderController::class,
        'purchases' => PurchaseController::class,
        'import-products' => ImportProductsController::class,
        'users' => UserController::class
    ]);
});

