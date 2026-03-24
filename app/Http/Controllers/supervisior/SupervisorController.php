<?php

namespace App\Http\Controllers\supervisior;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Application;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SupervisorController extends Controller
{
    private function ensureRole(): void
    {
        abort_unless(Auth::check() && Auth::user()->role === 'supervisor', 403);
    }

    public function supervisorActiveStudentAcouunt(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole();

        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();

        if ($student->supervisor_code !== $request->user()->supervisor_code) {
            return back()->with('error', 'You are not the supervisor of this student.');
        }

        $student->status = 'active';
        $student->save();

        return back()->with('success', 'successfully activated the student account.');
    }

    public function rejectActiveStudentAcouunt(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole();

        $student = User::where('id', $id)->where('role', 'student')->firstOrFail();
        $student->status = 'rejected';
        $student->save();

        return back()->with('success', 'successfully rejected the student account.');
    }

    public function dashboard()
    {
        $this->ensureRole();

        $supervisor = Auth::user();

        $students = User::where('role', 'student')
            ->where('supervisor_code', $supervisor->supervisor_code)
            ->get();

        $totalStudents = $students->count();
        $pendingStudents = $students->where('status', 'pending')->count();
        $activeStudents = $students->where('status', 'active')->count();
        $rejectedStudents = $students->where('status', 'rejected')->count();

        $studentCards = Application::with(['student', 'opportunity'])
            ->whereHas('student', function ($q) use ($supervisor) {
                $q->where('supervisor_code', $supervisor->supervisor_code)
                    ->where('status', 'active');
            })
            ->where('final_status', 'approved')
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($application) {
                $progress = $this->calculateProgress($application);

                return [
                    'application' => $application,
                    'student' => $application->student,
                    'opportunity' => $application->opportunity,
                    'progress' => $progress,
                    'status_label' => $progress >= 75 ? 'On Track' : 'At Risk',
                ];
            });

        return view('supervisor.dashboard', compact(
            'supervisor',
            'totalStudents',
            'pendingStudents',
            'activeStudents',
            'rejectedStudents',
            'studentCards'
        ));
    }

    public function studentsPage()
    {
        $this->ensureRole();

        $supervisor = Auth::user();

        $pendingStudents = User::where('role', 'student')
            ->where('supervisor_code', $supervisor->supervisor_code)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $activeStudentsRaw = User::where('role', 'student')
            ->where('supervisor_code', $supervisor->supervisor_code)
            ->where('status', 'active')
            ->latest()
            ->get();

        $approvedStudents = $activeStudentsRaw->map(function ($student) {
            $application = Application::with('opportunity')
                ->where('student_id', $student->id)
                ->where('final_status', 'approved')
                ->latest()
                ->first();

            $progress = 0;
            $statusLabel = 'On Track';

            if ($application) {
                $progress = $this->calculateProgress($application);
                if ($application->training_completed_at) {
                    $statusLabel = 'Completed';
                } else {
                    $statusLabel = $progress >= 75 ? 'On Track' : 'At Risk';
                }
            }

            return [
                'student'      => $student,
                'application'  => $application,
                'opportunity'  => $application?->opportunity,
                'progress'     => $progress,
                'status_label' => $statusLabel,
            ];
        });

        $totalStudents = User::where('role', 'student')
            ->where('supervisor_code', $supervisor->supervisor_code)
            ->count();

        $totalPending = $pendingStudents->count();
        $totalApproved = $approvedStudents->count();

        return view('supervisor.students.index', compact(
            'supervisor',
            'pendingStudents',
            'approvedStudents',
            'totalStudents',
            'totalPending',
            'totalApproved'
        ));
    }

    public function pendingStudentsPage()
    {
        $this->ensureRole();

        $supervisor = Auth::user();

        $pendingStudents = User::where('role', 'student')
            ->where('supervisor_code', $supervisor->supervisor_code)
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('supervisor.students.pending', compact('pendingStudents', 'supervisor'));
    }

    public function weeklyTasksPage()
    {
        $this->ensureRole();

        $supervisor = Auth::user();

        $approvedApplications = Application::with(['student', 'opportunity'])
            ->where('company_status', 'approved')
            ->where('supervisor_status', 'approved')
            ->where('final_status', 'approved')
            ->whereNull('training_completed_at')
            ->whereHas('student', function ($query) use ($supervisor) {
                $query->where('supervisor_code', $supervisor->supervisor_code);
            })
            ->latest()
            ->get();

        $applicationIds = $approvedApplications->pluck('id');

        $totalTasks = Task::whereIn('application_id', $applicationIds)->count();
        $todoCount = Task::whereIn('application_id', $applicationIds)->where('status', 'todo')->count();
        $progressCount = Task::whereIn('application_id', $applicationIds)->where('status', 'progress')->count();
        $doneCount = Task::whereIn('application_id', $applicationIds)->where('status', 'done')->count();

        return view('supervisor.weekly-tasks', compact(
            'approvedApplications',
            'totalTasks',
            'todoCount',
            'progressCount',
            'doneCount'
        ));
    }

    private function calculateProgress($application)
    {
        if (!$application->approved_at || !$application->opportunity || !$application->opportunity->duration) {
            return 0;
        }

        if ($application->training_completed_at) {
            return 100;
        }

        $start = Carbon::parse($application->approved_at);
        $end = Carbon::parse($application->approved_at)->addMonths((int)$application->opportunity->duration);
        $now = now();

        if ($now->lessThanOrEqualTo($start)) {
            return 0;
        }

        if ($now->greaterThanOrEqualTo($end)) {
            return 100;
        }

        $totalSeconds = $start->diffInSeconds($end);
        $elapsedSeconds = $start->diffInSeconds($now);

        if ($totalSeconds <= 0) {
            return 0;
        }

        return min(100, max(0, round(($elapsedSeconds / $totalSeconds) * 100)));
    }
}
