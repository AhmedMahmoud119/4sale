<?php

use App\Http\Controllers\TableController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Auth;

Auth::routes();

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('check-availability', [TableController::class, 'checkAvailability']);

Route::post('reserve-table', [ReservationController::class, 'reserveTable']);

Route::get('list-menu-items', [MenuController::class, 'listMenuItems']);


Route::middleware('auth:sanctum')->group(function () {

    Route::post('order', [OrderController::class, 'placeOrder']);
    Route::post('pay', [PaymentController::class, 'pay']);

});
