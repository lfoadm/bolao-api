<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BetController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PoolController;
use App\Http\Controllers\Api\SellerController;
use App\Http\Controllers\Payments\PaymentWebhookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// Rotas públicas - 
# CUSTOMER 
Route::post('/auth/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/auth/login/send-otp', [AuthController::class, 'loginSendOtp']);

# HOME
Route::get('/pools/home', [HomeController::class, 'index']);

// webhook público
Route::post('/payments/webhook', [PaymentWebhookController::class, 'updatePaymentStatus']);

// Rotas protegidas (apenas usuários logados via Sanctum)
Route::middleware('auth:sanctum')->group(function () {

    // GAMES
    Route::apiResource('/games', GameController::class);
    Route::get('/get-games', [GameController::class, 'getGames']);
    Route::post('/games/{game}/finalize', [GameController::class, 'finalize']);

    // SELLER
    Route::get('/sellers', [SellerController::class, 'index']);
    Route::post('/sellers', [SellerController::class, 'store']);
    Route::get('/sellers/{seller}', [SellerController::class, 'show']);

    // POOLS
    Route::apiResource('pools', PoolController::class);
    

    // BETS
    Route::post('/pools/{pool}/bets', [BetController::class, 'store']);
    Route::get("/bets/{id}", [BetController::class, "show"]);
    Route::get('/my-bets', [BetController::class, 'myBets']);
});