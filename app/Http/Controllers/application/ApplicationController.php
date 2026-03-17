<?php

namespace App\Http\Controllers\application;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    private function ensureRole(Request $request, string $role): void
    {
        abort_unless($request->user() && $request->user()->role === $role, 403);
    }

    private function updateFinalStatus(Application $application): void
    {
        if ($application->company_status === 'approved' && $application->supervisor_status === 'approved') {
            $application->final_status = 'approved';
            $application->approved_at = now();
            return;
        }

        if ($application->company_status === 'rejected' || $application->supervisor_status === 'rejected') {
            $application->final_status = 'rejected';
            return;
        }

        $application->final_status = 'pending';
    }

    public function applyApplication(Request $request): RedirectResponse
    {
        $this->ensureRole($request, 'student');

        $validated = $request->validate([
            'opportunity_id' => ['required', 'exists:internship_opportunities,id'],
            'skills' => ['nullable', 'string'],
            'motivation' => ['nullable', 'string', 'max:500'],
            'cv' => ['required', 'file', 'mimes:pdf'],
        ]);

        $cvPath = $request->file('cv')->store('cvs', 'public');

        Application::create([
            'student_id' => $request->user()->id,
            'opportunity_id' => $validated['opportunity_id'],
            'skills' => $validated['skills'] ?? null,
            'motivation' => $validated['motivation'] ?? null,
            'cv' => $cvPath,
            'company_status' => 'pending',
            'supervisor_status' => 'pending',
            'final_status' => 'pending',
        ]);

        return back()->with('success', 'successfully applied for the internship opportunity. We will notify you about the status of your application soon.');
    }

    public function companyApplicationApprove(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'company');

        $application = Application::findOrFail($id);
        $application->company_status = 'approved';
        $this->updateFinalStatus($application);
        $application->save();

        return back()->with('success', 'successfully approved the application.');
    }

    public function companyApplicationReject(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'company');

        $application = Application::findOrFail($id);
        $application->company_status = 'rejected';
        $application->final_status = 'rejected';
        $application->save();

        return back()->with('success', 'successfully rejected the application.');
    }

    public function supervisorApplicationApprove(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'supervisor');

        $application = Application::findOrFail($id);
        $application->supervisor_status = 'approved';
        $this->updateFinalStatus($application);
        $application->save();

        return back()->with('success', 'successfully approved the application.');
    }

    public function supervisorReject(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'supervisor');

        $application = Application::findOrFail($id);
        $application->supervisor_status = 'rejected';
        $application->final_status = 'rejected';
        $application->save();

        return back()->with('success', 'successfully rejected the application.');
    }

   

    // Redirect helpers for web-only flow
    public function myApplications(Request $request): RedirectResponse
    {
        $this->ensureRole($request, 'student');
        return redirect()->route('student.applications.index');
    }

    public function companyApplications(Request $request): RedirectResponse
    {
        $this->ensureRole($request, 'company');
        return redirect()->route('company.applicants.index');
    }

    public function supervisorApplications(Request $request): RedirectResponse
    {
        $this->ensureRole($request, 'supervisor');
        return redirect()->route('supervisor.applications.index');
    }

    public function supervisorStudents(Request $request): RedirectResponse
    {
        $this->ensureRole($request, 'supervisor');
        return redirect()->route('supervisor.students.index');
    }
}
