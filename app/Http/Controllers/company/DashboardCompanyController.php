<?php

namespace App\Http\Controllers\company;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\InternshipOpportunity;
use Illuminate\Support\Facades\Auth;

class DashboardCompanyController extends Controller
{
    public function dashboard()
    {
        abort_unless(Auth::check() && Auth::user()->role === 'company', 403);

        $companyId = Auth::id();

        $totalPrograms = InternshipOpportunity::where('company_user_id', $companyId)->count();

        $openPrograms = InternshipOpportunity::where('company_user_id', $companyId)
            ->where('status', 'open')
            ->count();

        $pendingApplications = Application::whereHas('opportunity', function ($q) use ($companyId) {
            $q->where('company_user_id', $companyId);
        })->where('company_status', 'pending')->count();

        $recentApplicants = Application::with(['student', 'opportunity'])
            ->whereHas('opportunity', function ($q) use ($companyId) {
                $q->where('company_user_id', $companyId);
            })
            ->latest()
            ->take(5)
            ->get();

        return view('company.dashboard', compact(
            'totalPrograms',
            'openPrograms',
            'pendingApplications',
            'recentApplicants'
        ));
    }
}
