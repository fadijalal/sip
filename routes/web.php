<?php

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

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
