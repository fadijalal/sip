<?php

namespace App\Http\Controllers\company;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipOpportunity;
use Illuminate\Support\Facades\Auth;

class ApplicationsCompanyController extends Controller
{

    public function applicantsPage()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        $companyId = Auth::id();

        $applications = Application::with(['student', 'opportunity'])
            ->whereHas('opportunity', function ($q) use ($companyId) {
                $q->where('company_user_id', $companyId);
            })
            ->latest()
            ->get();

        $totalApplications = $applications->count();
        $pendingApplications = $applications->where('company_status', 'pending')->count();
        $approvedApplications = $applications->where('company_status', 'approved')->count();
        $rejectedApplications = $applications->where('company_status', 'rejected')->count();

        return view('company.applicants.index', compact(
            'applications',
            'totalApplications',
            'pendingApplications',
            'approvedApplications',
            'rejectedApplications'
        ));
    }

    public function applicantDetails($id)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        $companyId = Auth::id();

        $application = Application::with(['student', 'opportunity'])
            ->whereHas('opportunity', function ($q) use ($companyId) {
                $q->where('company_user_id', $companyId);
            })
            ->findOrFail($id);

        return view('company.applicants.show', compact('application'));
    }
}
