<?php

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
