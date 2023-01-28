<?php

use App\Http\Controllers\api\OrdersController;
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
    return $request->user();
});

Route::resource('orders', OrdersController::class);
Route::post('orders/{id}/add', [OrdersController::class, 'add_product_to_order']);
Route::post('orders/{id}/pay', [OrdersController::class, 'pay_order']);
