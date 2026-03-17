<?php

namespace App\Http\Controllers\supervisior;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\Controller;

class SupervisorApplicationsController extends Controller
{
    public function index()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'supervisor', 403);

        $supervisor = Auth::user();

        $applications = Application::with([
            'student',
            'opportunity.companyUser'
        ])
            ->whereHas('student', function ($q) use ($supervisor) {
                $q->where('supervisor_code', $supervisor->supervisor_code);
            })
            ->latest()
            ->get();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('supervisor_status', 'pending')->count();
        $approvedApplications = $applications->where('supervisor_status', 'approved')->count();
        $rejectedApplications = $applications->where('supervisor_status', 'rejected')->count();

        return view('supervisor.applications.index', compact(
            'applications',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications',
            'supervisor'
        ));
    }

    public function show($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'supervisor', 403);

        $supervisor = Auth::user();

        $application = Application::with([
            'student',
            'opportunity.companyUser'
        ])
            ->whereHas('student', function ($q) use ($supervisor) {
                $q->where('supervisor_code', $supervisor->supervisor_code);
            })
            ->findOrFail($id);

        return view('supervisor.applications.show', compact('application', 'supervisor'));
    }

    public function applicationsPage()
    {
        abort_unless(auth()->check() && auth()->user()->role === 'supervisor', 403);

        $supervisor = auth()->user();

        $applications = \App\Models\Application::with(['student', 'opportunity'])
            ->whereHas('student', function ($q) use ($supervisor) {
                $q->where('supervisor_code', $supervisor->supervisor_code);
            })
            ->latest()
            ->get();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('supervisor_status', 'pending')->count();
        $approvedApplications = $applications->where('supervisor_status', 'approved')->count();
        $rejectedApplications = $applications->where('supervisor_status', 'rejected')->count();

        return view('supervisor.applications.index', compact(
            'supervisor',
            'applications',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications'
        ));
    }
}
