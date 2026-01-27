<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('v1')->group(function () {


    Route::post('/auth/login', [AuthController::class, 'login']);
 Route::post('/auth/register', [AuthController::class, 'register']);


    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('/auth/me', [AuthController::class, 'me']);
          Route::get('/auth/me', function (Request $request) {
        return $request->user();
    });

        Route::post('/auth/logout', [AuthController::class, 'logout']);
           Route::get('/auth/users', [AuthController::class, 'getAllUsers']);
    });

});


 
 