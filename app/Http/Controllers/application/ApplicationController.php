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
            if (! $application->approved_at) {
                $application->approved_at = now();
            }
            return;
        }

        if ($application->company_status === 'rejected' || $application->supervisor_status === 'rejected') {
            $application->final_status = 'rejected';
            return;
        }

        $application->final_status = 'pending';
    }

    private function assertCanCompleteTraining(Application $application): void
    {
        abort_unless($application->final_status === 'approved', 422, 'Training completion is available only for fully approved applications.');
        abort_if($application->training_completed_at, 422, 'Training already completed.');

        $durationMonths = (int) ($application->opportunity?->duration ?? 0);
        abort_unless($application->approved_at && $durationMonths > 0, 422, 'Training timeline is not ready yet.');

        $endDate = $application->approved_at->copy()->addMonths($durationMonths)->startOfDay();
        abort_unless(now()->startOfDay()->greaterThanOrEqualTo($endDate), 422, 'Training period is not finished yet.');
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

    public function companyCompleteTraining(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'company');

        $application = Application::with(['opportunity'])->findOrFail($id);
        abort_unless((int) optional($application->opportunity)->company_user_id === (int) $request->user()->id, 403);
        $this->assertCanCompleteTraining($application);

        $validated = $request->validate([
            'company_final_score' => ['required', 'integer', 'min:0', 'max:100'],
            'company_final_note' => ['required', 'string', 'max:2000'],
        ]);

        $application->company_final_score = $validated['company_final_score'];
        $application->company_final_note = trim($validated['company_final_note']);

        if ($application->company_final_score !== null && $application->supervisor_final_score !== null) {
            $application->final_score = (int) round(($application->company_final_score + $application->supervisor_final_score) / 2);
            $application->training_completed_at = now();
        }

        $application->save();

        return back()->with('success', 'Company final evaluation saved successfully.');
    }

    public function supervisorCompleteTraining(Request $request, int $id): RedirectResponse
    {
        $this->ensureRole($request, 'supervisor');

        $application = Application::with(['student', 'opportunity'])->findOrFail($id);
        abort_unless(optional($application->student)->supervisor_code === $request->user()->supervisor_code, 403);
        $this->assertCanCompleteTraining($application);

        $validated = $request->validate([
            'supervisor_final_score' => ['required', 'integer', 'min:0', 'max:100'],
            'supervisor_final_note' => ['required', 'string', 'max:2000'],
        ]);

        $application->supervisor_final_score = $validated['supervisor_final_score'];
        $application->supervisor_final_note = trim($validated['supervisor_final_note']);

        if ($application->company_final_score !== null && $application->supervisor_final_score !== null) {
            $application->final_score = (int) round(($application->company_final_score + $application->supervisor_final_score) / 2);
            $application->training_completed_at = now();
        }

        $application->save();

        return back()->with('success', 'Supervisor final evaluation saved successfully.');
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
