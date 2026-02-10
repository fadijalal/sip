<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\JobController;
use App\Http\Controllers\InternshipAssignmentController
;use App\Http\Controllers\InternshipApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;

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
 
           Route::post('/admin/supervisor/{id}', [AdminController::class, 'updateSupervisorStatus']);
    Route::post('/admin/company/{id}', [AdminController::class, 'updateCompanyStatus']);


        Route::post('/company/createJob', [JobController::class, 'createJob']);
    Route::post('/company/jobs/{id}', [JobController::class, 'updateJob']);
Route::delete('/company/jobs/{id}', [JobController::class, 'deleteJob']);
Route::post('/application', [ApplicationController::class, 'applyApplication']);

    });






});


 
 