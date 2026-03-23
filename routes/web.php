<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\application\ApplicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\company\ApplicationsCompanyController;
use App\Http\Controllers\company\DashboardCompanyController;
use App\Http\Controllers\company\JobController;
use App\Http\Controllers\company\ProgramsCompanyController;
use App\Http\Controllers\student\StudentController;
use App\Http\Controllers\supervisior\SupervisorApplicationsController;
use App\Http\Controllers\supervisior\SupervisorController;
use App\Http\Controllers\TrelloController;
use App\Http\Controllers\TrainingTaskController;
use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::view('/login', 'auth.login')->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::view('/register', 'auth.register')->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.process');

Route::prefix('trello')->group(function () {
    Route::get('/', [TrelloController::class, 'index']);
    Route::post('/card', [TrelloController::class, 'createCard']);
});

Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::view('/board', 'board')->name('board');

    Route::get('/training-complete/{application}', function (Application $application) {
        $user = Auth::user();
        abort_if(! $user, 403);

        abort_unless(
            $user->id === $application->student_id ||
            ($user->role === 'company' && optional($application->opportunity)->company_user_id === $user->id) ||
            ($user->role === 'supervisor' && optional($application->student)->supervisor_code === $user->supervisor_code),
            403
        );

        return view('training-complete', ['application' => $application]);
    })->name('training.complete');

    Route::get('/admin/dashboard', [AdminController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::get('/admin/users', [AdminController::class, 'adminUsersPage'])->name('admin.users');
    Route::get('/admin/companies', [AdminController::class, 'adminCompaniesPage'])->name('admin.companies');
    Route::post('/admin/supervisor/{id}', [AdminController::class, 'updateSupervisorStatus'])->name('admin.supervisor.update');
    Route::post('/admin/company/{id}', [AdminController::class, 'updateCompanyStatus'])->name('admin.company.update');
    Route::post('/admin/supervisors/{id}/status', [AdminController::class, 'updateSupervisorStatus']);
    Route::post('/admin/companies/{id}/status', [AdminController::class, 'updateCompanyStatus']);

    Route::get('/company/dashboard', [DashboardCompanyController::class, 'dashboard'])->name('company.dashboard');
    Route::get('/company/programs', [ProgramsCompanyController::class, 'programsPage'])->name('company.programs.index');
    Route::get('/company/programs/create', [ProgramsCompanyController::class, 'createProgramPage'])->name('company.programs.create');
    Route::get('/company/programs/{id}/edit', [ProgramsCompanyController::class, 'editProgramPage'])->name('company.programs.edit');
    Route::post('/company/createJob', [JobController::class, 'createJob'])->name('company.jobs.create');
    Route::post('/company/jobs/{id}', [JobController::class, 'updateJob'])->name('company.jobs.update');
    Route::delete('/company/jobs/{id}', [JobController::class, 'deleteJob'])->name('company.jobs.delete');
    Route::post('/company/programs', [JobController::class, 'createJob'])->name('company.programs.store');
    Route::post('/company/programs/{id}', [JobController::class, 'updateJob'])->name('company.programs.update');
    Route::post('/company/programs/{id}/delete', [JobController::class, 'deleteJob'])->name('company.programs.delete');
    Route::get('/company/applicants', [ApplicationsCompanyController::class, 'applicantsPage'])->name('company.applicants.index');
    Route::get('/company/applicants/{id}', [ApplicationsCompanyController::class, 'applicantDetails'])->name('company.applicants.show');
    Route::post('/company/applications/{id}/approve', [ApplicationController::class, 'companyApplicationApprove'])->name('company.applications.approve');
    Route::post('/company/applications/{id}/reject', [ApplicationController::class, 'companyApplicationReject'])->name('company.applications.reject');

    Route::get('/student/dashboard', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/student/programs', [StudentController::class, 'programs'])->name('student.programs.index');
    Route::get('/student/programs/{id}', [StudentController::class, 'programShow'])->name('student.programs.show');
    Route::post('/student/applications/apply', [ApplicationController::class, 'applyApplication'])->name('student.applications.apply');
    Route::get('/student/applications', [StudentController::class, 'applications'])->name('student.applications.index');
    Route::get('/student/workspace', [StudentController::class, 'workspace'])->name('student.workspace.index');

    Route::get('/supervisor/dashboard', [SupervisorController::class, 'dashboard'])->name('supervisor.dashboard');
    Route::get('/supervisor/students', [SupervisorController::class, 'studentsPage'])->name('supervisor.students.index');
    Route::get('/supervisor/students/pending', [SupervisorController::class, 'pendingStudentsPage'])->name('supervisor.students.pending');
    Route::post('/supervisor/students/{id}/approve', [ApplicationController::class, 'supervisorActiveStudentAcouunt'])->name('supervisor.students.approve');
    Route::post('/supervisor/students/{id}/reject', [ApplicationController::class, 'rejectActiveStudentAcouunt'])->name('supervisor.students.reject');
    Route::get('/supervisor/applications', [SupervisorApplicationsController::class, 'applicationsPage'])->name('supervisor.applications.index');
    Route::get('/supervisor/applications/{id}', [SupervisorApplicationsController::class, 'show'])->name('supervisor.applications.show');
    Route::post('/supervisor/applications/{id}/approve', [ApplicationController::class, 'supervisorApplicationApprove'])->name('supervisor.applications.approve');
    Route::post('/supervisor/applications/{id}/reject', [ApplicationController::class, 'supervisorReject'])->name('supervisor.applications.reject');
    Route::get('/supervisor/weekly-tasks', [SupervisorController::class, 'weeklyTasksPage'])->name('supervisor.weekly-tasks');

    // Training Tasks Workflow (Role-based + Trello sync)
    Route::get('/workspace/tasks', [TrainingTaskController::class, 'workspace'])->name('tasks.workspace');
    Route::get('/admin/tasks/workspace', [TrainingTaskController::class, 'adminWorkspace'])->name('tasks.admin.workspace');
    Route::post('/admin/tasks/broadcast', [TrainingTaskController::class, 'adminBroadcastTask'])->name('tasks.admin.broadcast');
    Route::post('/supervisor/tasks/broadcast', [TrainingTaskController::class, 'supervisorBroadcastTask'])->name('tasks.supervisor.broadcast');
    Route::get('/applications/{application}/tasks', [TrainingTaskController::class, 'board'])->name('tasks.board');
    Route::post('/applications/{application}/tasks', [TrainingTaskController::class, 'createTask'])->name('tasks.create');
    Route::post('/applications/{application}/tasks/{task}/submit', [TrainingTaskController::class, 'submitSolution'])->name('tasks.submit');
    Route::post('/applications/{application}/tasks/{task}/comment', [TrainingTaskController::class, 'addComment'])->name('tasks.comment');
    Route::post('/applications/{application}/tasks/{task}/grade', [TrainingTaskController::class, 'gradeTask'])->name('tasks.grade');
});

