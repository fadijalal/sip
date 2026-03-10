<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\KanbanTaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);
    Route::post('/auth/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', function (Request $request) {
            return $request->user();
        });

        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/users', [AuthController::class, 'getAllUsers']);

        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::post('/admin/supervisor/{id}', [AdminController::class, 'updateSupervisorStatus']);
            Route::post('/admin/company/{id}', [AdminController::class, 'updateCompanyStatus']);
        });

        Route::get('/supervisor/students', [ApplicationController::class, 'supervisorStudents']);
        Route::post('/supervisor/approve-student/{id}', [ApplicationController::class, 'supervisorActiveStudentAcouunt']);
        Route::post('/supervisor/reject-student/{id}', [ApplicationController::class, 'rejectActiveStudentAcouunt']);

        Route::post('/company/createJob', [JobController::class, 'createJob']);
        Route::post('/company/jobs/{id}', [JobController::class, 'updateJob']);
        Route::delete('/company/jobs/{id}', [JobController::class, 'deleteJob']);

        Route::post('/application', [ApplicationController::class, 'applyApplication']);
        Route::get('/my-applications', [ApplicationController::class, 'myApplications']);

        Route::get('/company/applications', [ApplicationController::class, 'companyApplications']);
        Route::post('/company/approve/{id}', [ApplicationController::class, 'companyApplicationApprove']);
        Route::post('/company/reject/{id}', [ApplicationController::class, 'companyApplicationReject']);

        Route::get('/supervisor/applications', [ApplicationController::class, 'supervisorApplications']);
        Route::post('/supervisor/approve/{id}', [ApplicationController::class, 'supervisorApplicationApprove']);
        Route::post('/supervisor/reject/{id}', [ApplicationController::class, 'supervisorReject']);

        Route::prefix('kanban')->group(function () {
            Route::get('/applications', [KanbanTaskController::class, 'applications']);
            Route::get('/tasks', [KanbanTaskController::class, 'tasks']);
            Route::post('/tasks', [KanbanTaskController::class, 'storeTask']);
            Route::put('/tasks/{task}', [KanbanTaskController::class, 'updateTask']);
            Route::delete('/tasks/{task}', [KanbanTaskController::class, 'deleteTask']);
            Route::post('/tasks/{task}/move', [KanbanTaskController::class, 'moveTask']);
            Route::post('/tasks/{task}/comments', [KanbanTaskController::class, 'addComment']);
            Route::post('/tasks/{task}/attachments', [KanbanTaskController::class, 'uploadAttachment']);
            Route::post('/applications/{application}/final-score', [KanbanTaskController::class, 'submitFinalScore']);
            Route::post('/applications/{application}/finish', [KanbanTaskController::class, 'finishTraining']);
        });
    });
});
