<?php

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\company\ApplicationsCompanyController;
use App\Http\Controllers\company\ProgramsCompanyController;
use App\Http\Controllers\company\DashboardCompanyController;
use App\Http\Controllers\SupervisorController;

/*
|--------------------------------------------------------------------------
| صفحات عامة
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('/login', 'auth.login')->name('login');
Route::view('/register', 'auth.register')->name('register');

/*
|--------------------------------------------------------------------------
| تنفيذ العمليات
|--------------------------------------------------------------------------
*/

Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::post('/register', [RegisterController::class, 'register'])->name('register.process');
Route::post('/admin/supervisors/{id}/status', [AdminController::class, 'updateSupervisorStatus'])->name('admin.supervisor.status');
Route::post('/admin/companies/{id}/status', [AdminController::class, 'updateCompanyStatus'])->name('admin.company.status');
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

/*
|--------------------------------------------------------------------------
| شاشات بعد تسجيل الدخول
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'adminUsersPage'])->name('admin.users');
    Route::get('/admin/companies', [AdminController::class, 'adminCompaniesPage'])->name('admin.companies');

    Route::get('/admin/users-data', [AdminController::class, 'getFilteredUsers'])->name('admin.users.data');

    Route::post('/admin/supervisors/{id}/status', [AdminController::class, 'updateSupervisorStatus'])->name('admin.supervisor.status');
    Route::post('/admin/companies/{id}/status', [AdminController::class, 'updateCompanyStatus'])->name('admin.company.status');

    Route::view('/student/dashboard', 'student.dashboard')->name('student.dashboard');
    Route::view('/supervisor/dashboard', 'supervisor.dashboard')->name('supervisor.dashboard');
    Route::view('/company/dashboard', 'company.dashboard')->name('company.dashboard');


    Route::get('/company/dashboard', [DashboardCompanyController::class, 'dashboard'])->name('company.dashboard');

    Route::get('/company/programs', [ProgramsCompanyController::class, 'programsPage'])->name('company.programs.index');
    Route::get('/company/programs/create', [ProgramsCompanyController::class, 'createProgramPage'])->name('company.programs.create');
    Route::get('/company/programs/{id}/edit', [ProgramsCompanyController::class, 'editProgramPage'])->name('company.programs.edit');

    Route::post('/company/programs', [JobController::class, 'createJob'])->name('company.programs.store');
    Route::post('/company/programs/{id}/update', [JobController::class, 'updateJob'])->name('company.programs.update');
    Route::post('/company/programs/{id}/delete', [JobController::class, 'deleteJob'])->name('company.programs.delete');

    Route::get('/company/applicants', [ApplicationsCompanyController::class, 'applicantsPage'])->name('company.applicants.index');
    Route::get('/company/applicants/{id}', [ApplicationsCompanyController::class, 'applicantDetails'])->name('company.applicants.show');

    Route::post('/company/applications/{id}/approve', [ApplicationController::class, 'companyApplicationApprove'])->name('company.applications.approve');
    Route::post('/company/applications/{id}/reject', [ApplicationController::class, 'companyApplicationReject'])->name('company.applications.reject');


    Route::get('/supervisor/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');

    Route::get('/supervisor/students', [SupervisorController::class, 'studentsPage'])->name('supervisor.students.index');
    Route::get('/supervisor/pending-students', [SupervisorController::class, 'pendingStudentsPage'])->name('supervisor.students.pending');

    Route::post('/supervisor/students/{id}/approve', [ApplicationController::class, 'supervisorActiveStudentAcouunt'])->name('supervisor.students.approve');
    Route::post('/supervisor/students/{id}/reject', [ApplicationController::class, 'rejectActiveStudentAcouunt'])->name('supervisor.students.reject');

    Route::get('/supervisor/weekly-tasks', [SupervisorController::class, 'weeklyTasksPage'])->name('supervisor.weekly-tasks');

    });

Route::view('/board', 'board')->name('board');

Route::get('/training-complete/{application}', function (Application $application) {
    $user = Auth::user();
    abort_if(!$user, 403);

    abort_unless(
        $user->id === $application->student_id ||
            ($user->role === 'company' && optional($application->opportunity)->company_user_id === $user->id) ||
            ($user->role === 'supervisor' && optional($application->student)->supervisor_code === $user->supervisor_code),
        403
    );

    return view('training-complete', ['application' => $application]);
})->name('training.complete');
