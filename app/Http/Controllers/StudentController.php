<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\InternshipOpportunity;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'student', 403);

        $student = Auth::user();

        $applications = Application::with(['opportunity'])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('final_status', 'pending')->count();
        $approvedApplications = $applications->where('final_status', 'approved')->count();
        $rejectedApplications = $applications->where('final_status', 'rejected')->count();

        $activeTraining = $applications->firstWhere('final_status', 'approved');

        return view('student.dashboard', compact(
            'student',
            'applications',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'activeTraining'
        ));
    }

    public function programs()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'student', 403);

        $student = Auth::user();

        $programs = InternshipOpportunity::with('companyUser')
            ->where('status', 'open')
            ->latest()
            ->get();

        return view('student.programs.index', compact('student', 'programs'));
    }

    public function programShow($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'student', 403);

        $student = Auth::user();

        $program = InternshipOpportunity::with('companyUser')->findOrFail($id);

        $existingApplication = Application::where('student_id', $student->id)
            ->where('opportunity_id', $program->id)
            ->first();

        return view('student.programs.show', compact('student', 'program', 'existingApplication'));
    }

    public function applications()
    {
        abort_unless(auth()->check() && auth()->user()->role === 'student', 403);

        $student = auth()->user();

        $applications = \App\Models\Application::with([
            'opportunity.companyUser'
        ])
            ->where('student_id', $student->id)
            ->latest()
            ->get();

        $activeApplication = $applications->first(function ($application) {
            return $application->final_status === 'approved';
        });

        return view('student.applications.index', compact(
            'student',
            'applications',
            'activeApplication'
        ));
    }

    public function workspace()
    {
        abort_unless(auth()->check() && auth()->user()->role === 'student', 403);

        $student = auth()->user();

        $activeApplication = \App\Models\Application::with([
            'student',
            'opportunity'
        ])
            ->where('student_id', $student->id)
            ->where('final_status', 'approved')
            ->latest()
            ->first();

        return view('student.workspace.index', compact(
            'student',
            'activeApplication'
        ));
    }
}
